<?php
session_start(); // 啟動 Session 機制，用於跨頁面記錄使用者登入狀態

// 檢查 Session 中是否已有學生登入紀錄
if (isset($_SESSION['student_sql_rows'])) { 
    header("Location: student_result.php"); // 若已登入，強制跳轉至成績結果頁
    exit; 
}

// 從 Docker 環境變數獲取資料庫連線資訊，若無則使用預設值
$host = getenv('DB_HOST') ?: 'db'; 
$user = getenv('DB_USER') ?: 'root'; 
$pass = getenv('DB_PASS') ?: 'root'; 
$dbname = getenv('DB_NAME') ?: 'class_db';

// 建立 MySQLi 連線物件
$conn = new mysqli($host, $user, $pass, $dbname); 
$conn->set_charset("utf8mb4"); 

$error = "";

// 判斷是否有 POST 請求（使用者是否點擊了查詢按鈕）
if ($_POST) {
    // 獲取使用者輸入的學號與密碼，若未輸入則給予空字串
    $id = isset($_POST['studentId']) ? $_POST['studentId'] : ''; 
    $pass = isset($_POST['password']) ? $_POST['password'] : '';

    // JOIN : 將帳號表 (userTable) 與成績表 (scoreTable) 合在一起查詢(這樣才能同時驗證帳密與查成績)
    // ON : 資料庫會尋找兩邊學號一致的資料列。例如，當帳號表的 S001 對應到成績表的 S001 時，這兩列就會拼成一條完整的使用者資料。
    // 🔴 漏洞 1 (查詢他人成績)、🔴 漏洞 2 (查詢全班成績)、🔴 漏洞 4 (取得老師帳密)

    $sql = "SELECT * FROM userTable  
            JOIN scoreTable ON userTable.username = scoreTable.studentId  
            WHERE userTable.username = '$id' AND userTable.password = '$pass'";
            

    // 🔴 漏洞 3 (竄改他人成績)
    // 使用 multi_query 執行查詢，此函式允許一次執行多條 SQL 指令（增加堆疊注入風險）
    if ($conn->multi_query($sql)) {
        $result = $conn->store_result();

        if (!$result) {
            $error = "查詢錯誤：" . $conn->error; 
        }
        elseif ($result->num_rows === 0) {
            $error = "登入失敗：帳號或密碼輸入錯誤";
        }
        else {
           // 取得並存下查詢結果（把 DB 撈出的資料存進 Session，結果頁只讀 Session）
            $rows = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $_SESSION['student_sql_rows'] = $rows; // 存成陣列
            $result->free();
            // 清空剩餘的 multi_query 結果（保留原本行為）
            while ($conn->more_results() && $conn->next_result()) {} 

            header("Location: student_result.php");
            exit;
        }
        while ($conn->more_results() && $conn->next_result()) {} 
    } else {
        $error = "SQL 語法錯誤：" . $conn->error;
    }
}
?>





<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8"> <title>(漏洞版)學生成績查詢系統</title> <style>
        body { font-family: "Microsoft JhengHei", sans-serif; padding: 20px; background-color: #e9ecef; }
        .box { 
            border: 1px solid #ccc; 
            padding: 30px; 
            width: 400px; 
            margin: 80px auto; 
            background-color: #fff;
            border-radius: 8px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; } 
        p { margin-bottom: 15px; font-weight: bold; color: #555;  } 
        input[type="text"], input[type="password"] {
            width: 95%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;
        } 
        button {
            width: 100%; padding: 10px; cursor: pointer;
            background: #007bff; color: white; border: none; border-radius: 4px;
            font-size: 16px;
        } 
        button:hover { background: #0069d9; } 
        .back-btn {
            background: none; border: none; color: #666;
            margin-top: 20px; text-decoration: underline; cursor: pointer;
            width: auto; display: block; margin-left: auto; margin-right: auto;
        } 
        .back-btn:hover { background: none; color: #333; } 
    </style>
</head>
<body>

    <div class="box">
        <h2>成績查詢系統(學生)</h2> <form method="POST">
            <p>學號：
                <input type="text" name="studentId" placeholder="例如 S001" required>
            </p>
            <p>密碼：
                <input type="password" name="password" required>
            </p>
            <button type="submit">查詢成績</button> </form>

        <?php if($error): ?>
            <p style="color:red; text-align:center; margin-top:20px; font-weight:bold;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <hr style="margin-top:25px; border-top:1px solid #eee;"> <button class="back-btn" onclick="location.href='teacher_login.php'">→ 切換至教師登入介面</button>
    </div>

</body>
</html>
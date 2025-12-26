<?php
session_start();

// 1. 權限檢查：如果沒有登入 Session，就踢回教師登入頁
if (!isset($_SESSION['teacher_result'])) {
    header("Location: teacher_login.php");
    exit;
}

// 連線資料庫
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'root';
$dbname = getenv('DB_NAME') ?: 'class_db';

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");

// 2. 處理成績修改
$msg = "";
if (isset($_POST['update'])) {
    // 接收輸入
    $s_id = $_POST['studentId'];
    $s_score = $_POST['score'];

    // 執行更新 (這裡為了方便演示，保留傳統寫法，若需要嚴格防禦可改用 prepare)
    // 更新 scoreTable
    $sql = "UPDATE scoreTable SET score = '$s_score' WHERE studentId = '$s_id'";
    
    if ($conn->query($sql) === TRUE) {
        $msg = "成功更新學號 $s_id 的成績為 $s_score 分！";
    } else {
        $msg = "更新失敗：" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>教師管理後台</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #e9ecef; }
        .container { 
            max-width: 800px; margin: 0 auto; background: #fff; 
            padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; margin-top: 0; }
        .admin-info { text-align: right; color: #666; font-size: 0.9em; margin-bottom: 20px; }
        .admin-info span { font-weight: bold; color: #333; margin-right: 10px; }
        .admin-info a { color: #6c757d; text-decoration: none; font-weight: bold; border: 1px solid #6c757d; padding: 5px 10px; border-radius: 4px; transition: 0.3s;}
        .admin-info a:hover { background-color: #6c757d; color: white; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background-color: #f8f9fa; color: #333; }
        tr:nth-child(even) { background-color: #f2f2f2; }

        .form-box {
            background: #f8f9fa; padding: 20px; margin-top: 30px;
            border: 1px solid #ddd; border-radius: 5px;
        }
        .form-box h3 { margin-top: 0; font-size: 1.2em; color: #007bff; }
        input[type="text"], input[type="number"] {
            padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 150px;
        }
        input[type="submit"] {
            padding: 8px 15px; background: #28a745; color: white; border: none;
            border-radius: 4px; cursor: pointer;
        }
        input[type="submit"]:hover { background: #218838; }
        .msg { color: green; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>成績管理後台</h2>
    
    <div class="admin-info">
        管理員：<?php echo htmlspecialchars($_SESSION['teacher_result']); ?> 
        | <a href="teacher_logout.php">登出系統</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>學號 (Student ID)</th>
                <th>姓名 (Name)</th>
                <th>分數 (Score)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 從 scoreTable 撈取所有成績
            $result = $conn->query("SELECT * FROM scoreTable");
            while ($row = $result->fetch_assoc()) {
                $color = $row['score'] < 60 ? 'red' : 'black';
                echo "<tr>";
                echo "<td>" . $row['studentId'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td style='color:$color; font-weight:bold;'>" . $row['score'] . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="form-box">
        <h3>修改學生成績</h3>
        <form method="POST">
            學號：
            <input type="text" name="studentId" placeholder="例如 S001" required>
            &nbsp;
            新成績：
            <input type="number" name="score" placeholder="0-100" min="0" max="100" required>
            &nbsp;
            <input type="submit" name="update" value="儲存修改">
        </form>
        <?php if($msg) echo "<p class='msg'>$msg</p>"; ?>
    </div>
</div>

</body>
</html>
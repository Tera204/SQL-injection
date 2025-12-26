<?php
session_start();

// 防止直接打網址
if (!isset($_SESSION['student_sql_rows'])) {
    header("Location: student_login.php");
    exit;
}

$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'root';
$dbname = getenv('DB_NAME') ?: 'class_db';

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");


?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>查詢結果</title>
    <style>
        body { font-family: "Microsoft JhengHei", sans-serif; padding: 20px; background-color: #e9ecef; }
        .container { 
            max-width: 800px; margin: 0 auto; background: #fff; 
            padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 15px; }

        .top-info { text-align: right; color: #666; font-size: 0.9em; margin-bottom: 20px; }
        .top-info span { font-weight: bold; color: #333; margin-right: 10px; }
        .top-info a { color: #6c757d; text-decoration: none; font-weight: bold; border: 1px solid #6c757d; padding: 5px 10px; border-radius: 4px; transition: 0.3s;}
        .top-info a:hover { background-color: #6c757d; color: white; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background-color: #f8f9fa; color: #333; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

<div class="container">
    <div class="top-info">
        <!-- <span>查詢學號：<?php echo htmlspecialchars($id); ?></span> -->
        <a href="student_logout.php">登出系統</a>
    </div>

    <h2>查詢結果</h2>

    <?php
    // 再次執行漏洞 SQL 以顯示資料
    // 從 session 取出登入時存下的查詢結果，並顯示（不再直接查 DB）
    if (!empty($_SESSION['student_sql_rows'])) {
        $rows = $_SESSION['student_sql_rows'];

        echo "<table>";
        echo "<thead><tr><th>身份 (Role)</th><th>學號 (ID)</th><th>姓名 (Name)</th><th>學期成績 (Score)</th></tr></thead>";
        echo "<tbody>";
        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
            echo "<td>" . htmlspecialchars($row['studentId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            $color = (isset($row['score']) && $row['score'] < 60) ? 'red' : 'black';
            echo "<td style='color:$color; font-weight:bold;'>" . htmlspecialchars($row['score']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p style='color:orange'>找不到查詢結果，請重新登入。</p>";
    }
    ?>
</div>

</body>
</html>
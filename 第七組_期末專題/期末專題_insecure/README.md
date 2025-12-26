# 實驗說明
- 類別: 紅隊
- 名稱: SQL 注入攻擊(SQL Injection)

## 目的
1. 透過 SQL 注入攻擊理解其原理
2. 防範 SQL 注入攻擊

## 專案目錄
- red_01_sql_injection
  - db
    - init.sql (資料庫初始化腳本)
  - web
    - src
      - student_login.php  (學生登入頁面)
      - student_logout.php
      - student_result.php (學生登入後頁面)
      - teacher_login.php  (教師登入頁面)
      - teacher_logout.php
      - teacher_result.php (教師登入後頁面)
      - .htaccess
  - Dockerfile
  - compose.yaml

## 實驗步驟
Step 1. 在**前景**建立並啟動 docker compose 服務(SQL 注入範例)

```shell
docker compose up -d
```

Step 2. 開啟瀏覽器 [localhost:8080](localhost:8080)


Step 3. 完成實驗題目

Step 4. 停止並刪除 docker compose 服務
```shell
docker compose down --rmi all -v
```

## 實驗內容
### 實驗 A. SQL 注入攻擊
進入「學生登入」頁面

  1. 在不知道密碼的情況下查詢他人成績
  2. 在不知道帳號及密碼的情況下查詢全班成績
  3. 取得老師帳號密碼

### 實驗 B. 防禦 SQL 注入攻擊
進入「學生登入」頁面：使用參數化查詢避免 SQL 注入攻擊
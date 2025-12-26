SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS class_db; 
USE class_db;

-- 重置資料表
DROP TABLE IF EXISTS scoreTable;
DROP TABLE IF EXISTS userTable;

-- 1. 使用者帳號表 
-- 包含 id, username (帳號), password (密碼), role (身分: student/teacher)
CREATE TABLE userTable (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(50) NOT NULL,
    role VARCHAR(20) NOT NULL
)CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. 成績表 
-- 包含 student_id (學號), name (姓名), score (分數)
CREATE TABLE scoreTable (
    studentId VARCHAR(10) PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    score INT NOT NULL
)CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 3. 插入使用者帳密表
-- 學生：S001~S009 / p001~p009
INSERT INTO userTable (username, password, role) VALUES 
('S000', 'admin123', 'teacher'),
('S001', 'p001', 'student'),
('S002', 'p002', 'student'),
('S003', 'p003', 'student'),
('S004', 'p004', 'student'),
('S005', 'p005', 'student'),
('S006', 'p006', 'student'),
('S007', 'p007', 'student'),
('S008', 'p008', 'student'),
('S009', 'p009', 'student'),
('S010', 'p009', 'student');

-- 4. 插入成績表
INSERT INTO scoreTable (studentId, name, score) VALUES 
('S001','Alex', 85),
('S002','Brian', 60),
('S003','Catherine', 92),
('S004','Daniel', 78),
('S005','Emily', 88),
('S006','Frank', 59),
('S007','Grace', 45),
('S008','Henry', 95),
('S009','Ivy', 72),
('S010','Jack', 81);


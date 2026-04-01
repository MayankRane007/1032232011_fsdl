-- =============================================
-- FSD Lab 4 - Student Registration System
-- Run this in phpMyAdmin to set up the database
-- =============================================

CREATE DATABASE IF NOT EXISTS fsd_lab4;
USE fsd_lab4;

CREATE TABLE IF NOT EXISTS students (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(50)  NOT NULL,
    last_name   VARCHAR(50)  NOT NULL,
    roll_no     VARCHAR(20)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    contact     VARCHAR(15)  NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

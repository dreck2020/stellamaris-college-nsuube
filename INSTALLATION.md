# Installation Guide - Stella Maris College Management System

## System Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP for local development

## Installation Steps

### 1. Setup Local Server
1. Install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services

### 2. Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create new database: `stella_maris_db`
3. Import the `database.sql` file
4. Default admin credentials:
   - Username: `admin`
   - Password: `Admin@123`

### 3. Application Setup
1. Copy all files to `htdocs/stella-maris-college/` (for XAMPP)
2. Update database configuration in `config/database.php`:
   ```php
   $this->username = "root";
   $this->password = "";
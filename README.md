# Payment-Record-System

💳 Payment Record System

A web-based Payment Record System developed to manage and store payment details efficiently through a dynamic interface.

🛠️ Tech Stack:
Frontend: HTML, CSS, JavaScript

Backend: PHP

Database: MySQL (using XAMPP)

📌 Features:
Add, update, and delete payment records

View detailed payment history

Clean and responsive user interface

PHP-MySQL integration for dynamic data handling

⚙️ How to Run the Project:
Install XAMPP
Download and install XAMPP from https://www.apachefriends.org.

Start Apache and MySQL
Open the XAMPP Control Panel and start Apache and MySQL services.

Move Project to htdocs
Copy the project folder to:
C:\xampp\htdocs\PaymentRecordSystem\

Create Database

Go to: http://localhost/phpmyadmin

Create a new database (e.g., payment_db)

Import the provided .sql file (if available) or manually create the necessary tables.

Configure Database Connection

Open the PHP file handling the DB connection (usually config.php or inside db.php)

Update the database name, username (root), and password ("" by default for XAMPP):

php
Copy
Edit
$conn = mysqli_connect("localhost", "root", "", "payment_db");
Run the Project
Open your browser and navigate to:
http://localhost/PaymentRecordSystem/

🎯 Purpose:
Created as part of a learning project to enhance full-stack web development skills and understand PHP-MySQL integration using XAMPP.

﻿# e-commerce-php

im using xampp for local server and mysql as database,

#first instalation

clone this repo🌀
```
git clone https://github.com/KbDevs12/e-commerce-php.git
```
after that, create database config file
**config/db.php :**
```
<?php
$servername = "servername"; // if you want to install in local server, change to "localhost"
$username = "username";
$password = "password";
$dbname = "database-name";

// create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// checking connection
if ($conn->connect_error) {
    die("connection failed: " . $conn->connect_error);
}
```
and done, you can start apache and mysql in xampp
# NOTE :
create your database before start the website🌐

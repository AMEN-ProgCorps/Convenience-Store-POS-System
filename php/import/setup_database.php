<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";

// Create connection without database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS convenience_store_post";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("convenience_store_post");

// Read and execute SQL file
$sql = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/Convenience-Store-POS-System/SQL/convenience_store_post.sql');

// Split SQL file into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)), 'strlen');

// Execute each statement
foreach ($statements as $statement) {
    if (strpos($statement, 'CREATE DATABASE') === false && 
        strpos($statement, 'USE') === false && 
        !empty(trim($statement))) {
        
        if ($conn->query($statement) === TRUE) {
            echo "Statement executed successfully<br>";
        } else {
            echo "Error executing statement: " . $conn->error . "<br>";
        }
    }
}

echo "Database setup completed!";

$conn->close();
?> 
<?php 
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "convenience_store_post";

    try {
        // Create PDO connection
        $conn = new PDO(
            "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            )
        );
    } catch(PDOException $e) {
        error_log("Connection failed: " . $e->getMessage());
        die(json_encode(array('error' => 'Database connection failed')));
    }
?>
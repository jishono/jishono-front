<?php
session_start();

    // データベースに接続
    $dsn = getenv('DB_HOST')
    $user = getenv('DB_USER')
    $password = getenv('DB_PASSWORD')
    $driver_options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            );

	try{
    	$dbh = new PDO($dsn, $user, $password,$driver_options);
	}catch (PDOException $e){
    	print('Connection failed:'.$e->getMessage());
    	die();
	}
	
?>

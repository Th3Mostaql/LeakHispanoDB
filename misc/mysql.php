<?php
$serverName = "127.0.0.1"; //MySQl Server Addres
$Database = "078db";
$Uid = ""; //Database User
$PWD = ""; // Database Password;

try {
	$conn = new PDO('mysql:host='.$serverName.';dbname='.$Database.'', $Uid, $PWD);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e)
     {
         exit("¡Error!: " . $e->getMessage() . "<br/>"); 
        }

?>
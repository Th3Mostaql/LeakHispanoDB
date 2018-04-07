<?php
//conectamos a la base de datos
$serverName = "127.0.0.1"; //MySQl Server Addres
$Database = "078db";
$Uid = ""; //Database User
$PWD = ""; // Database Password;

try {
	$conn = new PDO('mysql:host='.$serverName.';dbname='.$Database.'', $Uid, $PWD);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch (PDOException $e) {print "¡Error!: " . $e->getMessage() . "<br/>"; exit(); }

//funciones de seguridad
function cleanthis($data){
	$iclean = filter_var($data, FILTER_SANITIZE_STRING);
	$iclean = thisword($iclean);
	$iclean = htmlentities($iclean, ENT_QUOTES);
	return $iclean;
}
function thisword($word){
	$badword = array("drop", "insert", "update", "delete", "alter", "index", "truncate", "sleep", "'", '"');
	$badreplace = array("***", "***", "****", "***", "****", "***", "*****", "*****", "*", "*");
	$clean = str_replace($badword,$badreplace,$word);
	return $clean;
}
session_start();
$dhash = cleanthis($_GET['dhash']);
$username = $_SESSION['username'];

if(!$username){die('relogea...');}
if(!$dhash){die('Tira pa tu casa anda...');}

$stmt = $conn->prepare("SELECT * FROM `files` WHERE `hash` = '$dhash'");
$stmt->execute();
$row = $stmt->fetch();
if(!$row['hash']){die('Tira pa tu casa pesao');}
$fusername = $row['username'];
$fakeFileName = str_replace(",", "", $row['name']);
$file = $row['filename'];
unset($stmt);
//comprobamos si le quedan coins
$stmt = $conn->prepare("SELECT * FROM `members` WHERE `username` = '$username'");
$stmt->execute();
$row = $stmt->fetch();

if($row['coins'] < 10){die('No tienes suficientes coins!');}
//quitamos coins y +descargas
$stmt = $conn->prepare("UPDATE `members` SET `downloads` = `downloads`+'1', `coins` = `coins`-'10' WHERE `members`.`username` = '$username'");
$stmt->execute();
//damos coins al dueño
$stmt = $conn->prepare("UPDATE `members` SET `coins` = `coins`+'10' WHERE `members`.`username` = '$fusername'");
$stmt->execute();

$fp = fopen($file, 'rb');

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$fakeFileName");
header("Content-Length: " . filesize($file));
fpassthru($fp);

?>
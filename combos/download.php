<?php
require '../misc/mysql.php';
include '../misc/misc.php';
session_start();
if(!(isset($_SESSION['username']) && isset($_GET['dhash'])))
	{die();}
$dhash = cleanthis($_GET['dhash']);
$username = $_SESSION['username'];

if(!$username){die('relogea...');}
if(!$dhash){die('Tira pa tu casa anda...');}

$stmt = $conn->prepare("SELECT * FROM `files` WHERE `hash` = '$dhash'");
$stmt->execute();
$row = $stmt->fetch();
if(!$row['hash']){die('Tira pa tu casa pesao');}
$fusername = $row['username'];
if($fusername == $username){die('You cannot download your combo');}
$downloaders = explode(",", substr($row['downloaders'], 0, -1));
if (in_array($username, $downloaders)) {die('You already downloaded that combo!');}
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
$stmt = $conn->prepare("UPDATE `files` SET `downloads` = `downloads`+'1', `downloaders` = CONCAT(`downloaders`, '$username,') WHERE `files`.`hash` = '".$dhash."'");
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
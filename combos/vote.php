<?php
require '../misc/mysql.php';
include '../misc/misc.php';
session_start();
$fid = cleanthis($_GET['fid']);
$vote = cleanthis($_GET['vote']);
$username = $_SESSION['username'];
if(!$username){die('relogea...');}
if($vote > 3 || ctype_alpha($vote)){die('Tira pa tu casa anda parguelas...');}
if(!$fid || !$vote){die('Tira pa tu casa anda...');}


$stmt = $conn->prepare("SELECT * FROM `files` WHERE `id` = '$fid'");
$stmt->execute();
$row = $stmt->fetch();
if(!$row['id']){die('Tira pa tu casa pesao');}
unset($stmt);

$stmt = $conn->prepare("SELECT * FROM `votes` WHERE `fileid` = '$fid' AND `username` = '$username'");
$stmt->execute();
$row = $stmt->fetch();

if($row['username'] == $username){die('Ya has votado pesao!');}
echo $row['username'];
unset($stmt);

$stmt = $conn->prepare("INSERT INTO `votes` (`id`, `username`, `fileid`, `vote`) VALUES (NULL, '$username', '$fid', '$vote')");
$stmt->execute();

if($vote == 1){
	//upvote
	$stmt = $conn->prepare("UPDATE `files` SET `upvotes` = `upvotes`+'1' WHERE `files`.`id` = '$fid'");
	$stmt->execute();
}elseif($vote == 2){
	//downvote
	$stmt = $conn->prepare("UPDATE `files` SET `downvotes` = `downvotes`+'1' WHERE `files`.`id` = '$fid'");
	$stmt->execute();
}
echo 'Voted!';

?>
<?php
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
$pagetype = 'userpage';
$title = 'File Info';
require '../login/misc/pagehead.php';
$uid = $_SESSION['uid'];
$usr = profileData::pullAllUserInfo($uid);

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
function formatSize ($bytes) {
	$units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);

	$bytes /= pow(1024, $pow);

	return ceil($bytes) . ' ' . $units[$pow];
}

$chash = cleanthis($_GET['chash']);
if(!$chash){header("Location: /"); die();}

$db = new DbConn;
$stmt = $db->conn->prepare("SELECT * FROM `files` WHERE `hash` = '$chash'");
$stmt->execute();
$row = $stmt->fetch();

if(!$row['hash']){die('Tira pa tu casa pesao');}

$file_size = formatSize(filesize("/var/www/078db.cf/combos/".$row['filename']));

if (file_exists("/var/www/078db.cf/user/avatars/".$row['userid'].".jpg")) {
    $urlavatar = "https://078db.cf/user/avatars/".$row['userid'].".jpg";
} else {
    $urlavatar = "https://078db.cf/user/default.jpg";
}

?>
<html>
	<head>
		<link href="/style.css" rel="stylesheet" media="screen">
		<link href="/glitch.css" rel="stylesheet" media="screen">
	</head>
	<body>
		
		<center><span class="glitch"><img src="https://078db.cf/logo.png" height="60px" style="padding-bottom: 1%;">
			file info			<img src="https://078db.cf/logo.png" height="60px" style="padding-bottom: 1%;"></span></center>

		<form>
			<span style="color: #6991f7; text-shadow: 0.5px 0.5px 5px #6991f7; font-size: 20px"><?php echo $row['name']; ?><br>
			Size: <?php echo $file_size; ?><br>
			Date published: <?php echo date('Y-m-d H:i:s', $row['date']); ?></span><br><br>
			<a style="color: #1bce70; font-weight: bold; font-size: 50px" href="vote.php?fid=<?php echo $row['id'];?>&vote=1" target="_blank"><i class='far fa-thumbs-up'></i> <?php echo $row['upvotes']; ?> </a>
			<img src="<?php echo $urlavatar;?>" height="100px" style="padding-left: 5%; padding-right: 5%"><a style="color: #c11139; font-weight: bold; font-size: 50px" href="vote.php?fid=<?php echo $row['id'];?>&vote=2" target="_blank"><i class='far fa-thumbs-down'></i> <?php echo $row['downvotes']; ?></a><br>
			<span style="color: #6991f7; text-shadow: 0.5px 0.5px 5px #6991f7; font-size: 20px"><?php echo $row['username']; ?></span><br><br>

			<button style="color: black;" type="submit" onclick="window.open('download.php?dhash=<?php echo $row['hash'];?>')">DOWNLOAD COST 10 COINS</button>
			
			
		</form>
			</ul>
	</body>
</html>

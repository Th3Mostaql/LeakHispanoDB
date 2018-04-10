<?php

$pagetype = 'userpage';
$title = 'LeakHispano - Combos DB';
require 'login/misc/pagehead.php';
$uid = $_SESSION['uid'];
$usr = profileData::pullAllUserInfo($uid);
$coinsaporte = '20';
	// Load local config file if it exists.
	if (isReadableFile('config.php')) include('config.php');

	// Enabling error reporting
	if ($settings['debug']) {
		error_reporting(E_ALL);
		ini_set('display_startup_errors', 1);
		ini_set('display_errors', 1);
	}

	$settings['title'] = 'Combos';
	// Generated settings file.
	$data = array();

	$data['description'] = '';
	if (strlen($settings['description']) > 0)
		$data['description'] = $settings['description'] . '<br><br>';

	// Adding current script name to ignore list
	$data['ignores'] = $settings['ignores'];
	$data['ignores'][] = basename('index.php');

	// Use canonized path
	$data['uploaddir'] = realpath($settings['base_path'].'combos');

	// Is the directory there?
	if (!is_dir($data['uploaddir'])) {
		// Not found
		die(sprintf('[%s:%d]: Upload path "%s" is not a directory.', pathinfo(__FILE__, PATHINFO_BASENAME), __LINE__, $data['uploaddir']));
	} elseif (!is_readable($data['uploaddir'])) {
		// Not readable
		die(sprintf('[%s:%d]: Upload directory "%s" is not readable.', pathinfo(__FILE__, PATHINFO_BASENAME), __LINE__, $data['uploaddir']));
	} elseif (!is_writable($data['uploaddir'])) {
		// Not writable
		die(sprintf('[%s:%d]: Upload directory "%s" is not writable.', pathinfo(__FILE__, PATHINFO_BASENAME), __LINE__, $data['uploaddir']));
	}

	// Detect maximum upload size, allowed by server
	$data['max_upload_size'] = ini_get('upload_max_filesize');

	// If file deletion or private files are allowed, starting a session.
	// This is required for user authentification
	if ($settings['allow_deletion'] || $settings['allow_private']) {
		//session_start();

		// Genereate random 'user id'
		if (!isset($_SESSION['upload_user_id']))
			$_SESSION['upload_user_id'] = mt_rand(100000, 999999);

		// Store list of files that were uploaded by this user
		if (!isset($_SESSION['upload_user_files']))
			$_SESSION['upload_user_files'] = array();
	}

	//Webhook discord
	function postToDiscord($message){
    $data = array("content" => $message, "username" => "LeakHispanoDB", "avatar_url" => "https://i.imgur.com/OqhibJl.png");
    $curl = curl_init("https://discordapp.com/api/webhooks/431063688376614912/FP_za5l-n_ddCYCYjXpF_89_kqwSYiaZmaRu-L_MRYbdLaVTWgRFzzpL2VJSF5JxkT_R");
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
	}

	// Format file size
	function formatSize ($bytes) {
		$units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return ceil($bytes) . ' ' . $units[$pow];
	}

	// Rotate a two-dimensional array. Used for file uploads
	function diverseArray ($vector) {
		$result = array();
		foreach ($vector as $key1 => $value1)
			foreach ($value1 as $key2 => $value2)
				$result[$key2][$key1] = $value2;
		return $result;
	}

	// Handling file upload
	function uploadFile ($file_data) {
		global $settings, $data;

		$file_data['uploaded_file_name'] = basename($file_data['name']);
		$file_data['target_file_name'] = $file_data['uploaded_file_name'];

		if ( $file_data['type'] == 'text/plain'){
		//obtenemos variables
			echo 'nepe';
		// Generating random file name
		if ($settings['random_name_len'] !== false) {
			do {
				$file_data['target_file_name'] = '';
				while (strlen($file_data['target_file_name']) < $settings['random_name_len']) {
					$file_data['target_file_name'] .= $settings['random_name_alphabet'][mt_rand(0, strlen($settings['random_name_alphabet']) - 1)];
				}

				if ($settings['random_name_keep_type']) {
					$file_data['target_file_name'] .= '.' . pathinfo($file_data['uploaded_file_name'], PATHINFO_EXTENSION);
				}
			} while (isReadableFile($file_data['target_file_name']));
		}

		$file_data['upload_target_file'] = $data['uploaddir'] . DIRECTORY_SEPARATOR . $file_data['target_file_name'];

		// Do now allow to overwriting files
		if (isReadableFile($file_data['upload_target_file'])) {
			echo 'File name already exists' . "\n";
			return false;
		}

		// Moving uploaded file OK
		if (move_uploaded_file($file_data['tmp_name'], $file_data['upload_target_file'])) {
			if ($settings['listfiles'] && ($settings['allow_deletion'] || $settings['allow_private'])) {
				$_SESSION['upload_user_files'][] = $file_data['target_file_name'];
			}
			//preparamos variables
			$narchivo = $file_data['uploaded_file_name'];
			$nrandom = $file_data['target_file_name'];
			$dhash = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
			$nusuario = $_SESSION['username'];
			$idusuario = $_SESSION['uid'];
			$fecha = time();
			//$fecha = '946684800';
			//metemos datos en la db tabla files
			$db2 = new DbConn;
            $stmt = $db2->conn->prepare("INSERT INTO `files` (`id`, `name`, `filename`, `hash`, `coins`, `upvotes`, `downvotes`,`username`, `userid`, `date`) VALUES (NULL, '$narchivo', '$nrandom', '$dhash', '0', '0', '0', '$nusuario', '$idusuario', '$fecha')");
            $stmt->execute();
            unset($stmt);
            //actualizamos shared y coins
            $db3 = new DbConn;
            $stmt = $db3->conn->prepare("UPDATE `members` SET `shared` = `shared`+'1', `coins` = `coins`+'10' WHERE `username` = '$nusuario'");
            $stmt->execute();
            unset($stmt);
            
            //enviamos al discord
            postToDiscord('===> '.$narchivo.' <===
'.$nusuario.'
https://078db.cf/combos/combo.php?chash='.$dhash);

			//mensaje exito
            header("Location: ".$settings['url']."combos/combo.php?chash=".$dhash."");
			die();
			// Return target file name for later handling
			return $file_data['upload_target_file'];
		} else {
			echo 'Error: unable to upload the file.';
			return false;
		}
		}else{
			header("Location: /error.php");
			die();
			return false;
		}
	}

	// Delete file
	function deleteFile ($file) {
		global $data;

		if (in_array(substr($file, 1), $_SESSION['upload_user_files']) || in_array($file, $_SESSION['upload_user_files'])) {
			$fqfn = $data['uploaddir'] . DIRECTORY_SEPARATOR . $file;
			if (!in_array($file, $data['ignores']) && isReadableFile($fqfn)) {
				unlink($fqfn);
				echo 'File has been removed';
				exit;
			}
		}
	}

	// Mark/unmark file as hidden
	function markUnmarkHidden ($file) {
		global $data;

		if (in_array(substr($file, 1), $_SESSION['upload_user_files']) || in_array($file, $_SESSION['upload_user_files'])) {
			$fqfn = $data['uploaddir'] . DIRECTORY_SEPARATOR . $file;
			if (!in_array($file, $data['ignores']) && isReadableFile($fqfn)) {
				if (substr($file, 0, 1) === '.') {
					rename($fqfn, substr($fqfn, 1));
					echo 'File has been made visible';
				} else {
					rename($fqfn, $data['uploaddir'] . DIRECTORY_SEPARATOR . '.' . $file);
					echo 'File has been hidden';
				}
				exit;
			}
		}
	}

	// Checks if the given file is a file and is readable
	function isReadableFile ($file) {
		return (is_file($file) && is_readable($file));
	}

	// Files are being POSEed. Uploading them one by one.
	if (isset($_FILES['file'])) {
		echo 'nepe';
		header('Content-type: text/plain');
		if (is_array($_FILES['file'])) {
			$file_array = diverseArray($_FILES['file']);
			foreach ($file_array as $file_data) {
				$targetFile = uploadFile($file_data);
			} //END - foreach
		} else {
			$targetFile = uploadFile($_FILES['file']);
		}
		exit;
	}

	// Other file functions (delete, private).
	if (isset($_POST)) {
		if ($settings['allow_deletion'] && (isset($_POST['target'])) && isset($_POST['action']) && $_POST['action'] === 'delete') {
			deleteFile($_POST['target']);
		}

		if ($settings['allow_private'] && (isset($_POST['target'])) && isset($_POST['action']) && $_POST['action'] === 'privatetoggle') {
			markUnmarkHidden($_POST['target']);
		}
	}

	// List files in a given directory, excluding certain files
	function createArrayFromPath ($dir) {
		global $data;

		// Empty paths are not accepted
		if (empty($dir)) {
			die(sprintf('[%s:%d]: R.I.P.: Parameter "dir" cannot be empty.', __FUNCTION__, __LINE__));
		} // END - if

		$file_array = array();

		$dh = opendir($dir) or die(sprintf('[%s:%d]: R.I.P.: Cannot read directory "%s".', __FUNCTION__, __LINE__, $dir));

		while ($filename = readdir($dh)) {
			$fqfn = $dir . DIRECTORY_SEPARATOR . $filename;
			if (isReadableFile($fqfn) && !in_array($filename, $data['ignores'])) {
				$file_array[filemtime($fqfn)] = $filename;
			}
		} //END - while

		ksort($file_array);

		$file_array = array_reverse($file_array, true);

		return $file_array;
	}

	// Removes old files
	function removeOldFiles ($dir) {
		global $file_array, $settings;

		foreach ($file_array as $file) {
			$fqfn = $dir . DIRECTORY_SEPARATOR . $file;
			if ($settings['time_limit'] < time() - filemtime($fqfn)) {
				unlink($fqfn);
			}
		} //END - foreach
	}

	// Detects base URL
	function autoDetectBaseUrl () {
		// Detect protocol
		$protocol = 'http';
		if (
			((isset($_SERVER['HTTPS'])) && (strtolower($_SERVER['HTTPS']) == 'on')) ||
			((isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https'))
		) $protocol = 'https';

		// Detect port
		$port = getenv('SERVER_PORT');
		if (
			(($port == 80) && ($protocol == 'http')) ||
			(($port == 443) && ($protocol == 'https'))
		) $port = '';

		// Detect server name
		$server_name = getenv('SERVER_NAME');
		if ($server_name === false) $server_name = 'localhost';

		// Construct base URL
		$base_url = sprintf(
			'%s://%s%s%s',
			$protocol,
			$server_name,
			$port,
			dirname(getenv('SCRIPT_NAME'))
		);

		return $base_url;
	}

	// Only read files if the feature is enabled
	if ($settings['listfiles']) {
		$file_array = createArrayFromPath($data['uploaddir']);

		// Removing old files
		if ($settings['time_limit'] > 0)
			removeOldFiles($data['uploaddir']);

		$file_array = createArrayFromPath($data['uploaddir']);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$settings['lang']?>" lang="<?=$settings['lang']?>" dir="<?=$settings['lang_dir']?>">
	<head>
		<link rel="shortcut icon" type="image/png" href="/favicon.png"/>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
		<meta http-equiv="content-style-type" content="text/css" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="language" content="<?=$settings['lang']?>" />

		<meta name="robots" content="noindex" />
		<meta name="referrer" content="origin-when-crossorigin" />
		<title><?=$settings['title']?></title>
		<link href="/style.css" rel="stylesheet" media="screen">
		<link href="/glitch.css" rel="stylesheet" media="screen">
	</head>
	<body>
		
		<center><span class="glitch"><img src="https://078db.cf/logo.png" height="60px" style="padding-bottom: 1%;">
			<?=$settings['title']?>
			<img src="https://078db.cf/logo.png" height="60px" style="padding-bottom: 1%;"></span></center>

		<form action="<?= $settings['url'] ?>" method="post" enctype="multipart/form-data" class="dropzone" id="simpleupload-form">
			<?=$data['description']?>
			Maximum upload size is 100MB (Cloudflare limitations). You can use <a href="Combo_Splitter.exe" target="_blank" title="DivineGhostMa lo pasó por discord OJO CUIDAO!">ComboSplitter</a><br /><br/>
			<center><input type="file" name="file[]" id="simpleupload-input" /></center>
		</form>
		<?php if (($settings['listfiles']) && (count($file_array) > 0)) { ?>
			<ul id="simpleupload-ul">
				<?php
				//empezamos equisde
				$db = new DbConn;
				$stmt = $db->conn->prepare("SELECT * FROM `files` ORDER BY `date` DESC");
                if($stmt->execute()){
					$lvlcount=1;
					while($row = $stmt->fetch()){

						$class = '';
						$file_owner = 0;
						if ($file_owner)
							$class = 'owned';

						$fqfn = $data['uploaddir'] . DIRECTORY_SEPARATOR . $row['filename'];
						$file_size = formatSize(filesize($fqfn));
						//$row['hash']
						$nepesu = '';
						$fechayhora = date('Y-m-d H:i:s', $row['date']);

                        echo "<li><a class='uploaded_file' href='".$settings['url']."combos/combo.php?chash=".$row['hash']."' style='color: #6991f7;text-shadow: 0.5px 0.5px 5px #6991f7;'>".$row['name']." | ".$row['upvotes']." <i class='far fa-thumbs-up'></i> ".$row['downvotes']." <i class='far fa-thumbs-down'></i><span> ".$row['username']." | ".$row['downloads']." <i class='fas fa-download'></i> | ".$file_size."  | ".$fechayhora."</span></a>";

						if ($file_owner) {
								if ($settings['allow_deletion'])
									echo '<form action="' . $settings['url'] . '" method="post"><input type="hidden" name="target" value="' . $filename . '" /><input type="hidden" name="action" value="delete" /><button type="submit">delete</button></form>';
						}

							echo "</li>";
    				}
                }
                unset($stmt);
				?>
			</ul>
		<?php
		}
		?>

		<script type="text/javascript">
		<!--
			// Init some variables to shorten code
			var target_form        = document.getElementById('simpleupload-form');
			var target_ul          = document.getElementById('simpleupload-ul');
			var target_input       = document.getElementById('simpleupload-input');
			var settings_listfiles = <?=($settings['listfiles'] ? 'true' : 'false')?>;

			/**
			 * Initializes the upload form
			 */
			function init () {
				// Register drag-over event listener
				target_form.addEventListener('dragover', function (event) {
					event.preventDefault();
				}, false);

				// ... and the drop event listener
				target_form.addEventListener('drop', handleFiles, false);

				// Register onchange-event function
				target_input.onchange = function () {
					addFileLi('Uploading...', '');
					target_form.submit();
				};
			}

			/**
			 * Adds given file in a new li-tag to target_ul list
			 *
			 * @param name Name of the file
			 * @param info Some more informations
			 */
			function addFileLi (name, info) {
				if (settings_listfiles == false) {
					return;
				}

				target_form.style.display = 'none';

				var new_li = document.createElement('li');
				new_li.className = 'uploading';

				var new_a = document.createElement('a');
				new_a.innerHTML = name;
				new_li.appendChild(new_a);

				var new_span = document.createElement('span');
				new_span.innerHTML = info;
				new_a.appendChild(new_span);

				target_ul.insertBefore(new_li, target_ul.firstChild);
			}

			/**
			 * Handles given event for file upload
			 *
			 * @param event Event to handle file upload for
			 */
			function handleFiles (event) {
				event.preventDefault();

				var files = event.dataTransfer.files;

				var form = new FormData();

				for (var i = 0; i < files.length; i++) {
					form.append('file[]', files[i]);
					addFileLi(files[i].name, files[i].size + ' bytes');
				}

				var xhr = new XMLHttpRequest();
				xhr.onload = function() {
					window.location.reload();
				};

				xhr.open('post', '<?php echo $settings['url']; ?>', true);
				xhr.send(form);
			}

			// Initialize upload form
			init();

		//-->
		</script>
	</body>
</html>
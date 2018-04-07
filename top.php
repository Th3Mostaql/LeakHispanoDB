<?php

$pagetype = 'userpage';
$title = 'Edit Profile';
require 'login/misc/pagehead.php';
$uid = $_SESSION['uid'];
$usr = profileData::pullAllUserInfo($uid);
if (@get_headers($usr['userimage'])[0] == 'HTTP/1.1 404 Not Found' || $usr['userimage'] == '') {
    $imgpath = "no_user.jpg";
} else {
    $imgpath = $usr['userimage'];
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
		<style type="text/css" media="screen">
			.rainbow {
   /* Chrome, Safari, Opera */
  -webkit-animation: rainbow 1s infinite; 
  
  /* Internet Explorer */
  -ms-animation: rainbow 1s infinite;
  
  /* Standar Syntax */
  animation: rainbow 1s infinite; 
}

/* Chrome, Safari, Opera */
@-webkit-keyframes rainbow{
	20%{color: red;}
	40%{color: yellow;}
	60%{color: green;}
	80%{color: blue;}
	100%{color: orange;}	
}
/* Internet Explorer */
@-ms-keyframes rainbow{
	20%{color: red;}
	40%{color: yellow;}
	60%{color: green;}
	80%{color: blue;}
	100%{color: orange;}	
}

/* Standar Syntax */
@keyframes rainbow{
	20%{color: red;}
	40%{color: yellow;}
	60%{color: green;}
	80%{color: blue;}
	100%{color: orange;}	
}
			body {
				background: #111;
				margin: 0;
				color: #ddd;
				font-family: sans-serif;
			}

			body > h1 {
				display: block;
				background: rgba(255, 255, 255, 0.05);
				padding: 8px 16px;
				text-align: center;
				margin: 0;
			}

			body > form {
				display: block;
				background: rgba(255, 255, 255, 0.075);
				padding: 16px 16px;
				margin: 0;
				text-align: center;
			}

			body > ul {
				display: block;
				padding: 0;
				max-width: 1000px;
				margin: 32px auto;
			}

			body > ul > li {
				display: block;
				margin: 0;
				padding: 0;
			}

			body > ul > li > a.uploaded_file {
				display: block;
				margin: 0 0 1px 0;
				list-style: none;
				background: rgba(255, 255, 255, 0.1);
				padding: 8px 16px;
				text-decoration: none;
				color: inherit;
				opacity: 0.5;
			}

			body > ul > li > a:hover {
				opacity: 1;
			}

			body > ul > li > a:active {
				opacity: 0.5;
			}

			body > ul > li > a > span {
				float: right;
				font-size: 90%;
			}

			body > ul > li > form {
				display: inline-block;
				padding: 0;
				margin: 0;
			}

			body > ul > li.owned {
				margin: 8px;
			}

			body > ul > li > form > button {
				opacity: 0.5;
				display: inline-block;
				padding: 4px 16px;
				margin: 0;
				border: 0;
				background: rgba(255, 255, 255, 0.1);
				color: inherit;
			}

			body > ul > li > form > button:hover {
				opacity: 1;
			}

			body > ul > li > form > button:active {
				opacity: 0.5;
			}

			body > ul > li.uploading {
				animation: upanim 2s linear 0s infinite alternate;
			}

			@keyframes upanim {
				from {
					opacity: 0.3;
				}
				to {
					opacity: 0.8;
				}
			}
		</style>
	</head>
	<body>
		<h1 class="rainbow">TOP CONTRIBUTORS</h1>

			<form action="https://www.078db.cf/" method="post" enctype="multipart/form-data" class="dropzone" id="simpleupload-form">
			Servicio público por ahora, si se cometen abusos será privado!<br><br>
			Maximum upload size is 1,5G. You can use <br><br>
		</form>
		
		<ul id="simpleupload-ul">
			<li><a class="uploaded_file" style="color: #6991f7;
    text-shadow: 0.5px 0.5px 5px #6991f7;">1. Traceback<span> 3 Shares</span></a></li>
    	<li><a class="uploaded_file" style="color: #6991f7;
    text-shadow: 0.5px 0.5px 5px #6991f7;">2. Diegoks<span> 2 Shares</span></a></li>	
		
		</ul>

		
	</body>
</html>

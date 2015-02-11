<?php

/**
 * Form upload main file with php and html5.
 *
 * PHP version >= 5.2
 *
 * @category PHP
 * @package  AyeShare
 * @author   Romnick Bien Pantua (pantuts)
 * @license  MIT License
 * @link     http://pantuts.com
 * @version  1.4
 */

session_start();

// set session id
if (!isset($_SESSION['id'])) $_SESSION['id'] = uniqid();
 
// gzip compression with php
ob_start('ob_gzhandler');

// parse config file
$parseIni = parse_ini_file('./config.ini');
$maxFileSize = htmlentities($parseIni['size']);
$tooltip = htmlentities($parseIni['tip']);

?>

<!DOCTYPE html>   
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--
  ____   _    _   _ _____ _   _ _____ ____  
 |  _ \ / \  | \ | |_   _| | | |_   _/ ___| 
 | |_) / _ \ |  \| | | | | | | | | | \___ \ 
 |  __/ ___ \| |\  | | | | |_| | | |  ___) |
 |_| /_/   \_\_| \_| |_|  \___/  |_| |____/ 
                                            
-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--[if IE]><![endif]-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Minimalist Self Hosted File Sharing">
	<meta property="og:locale" content="en_US"/>
	<meta property="og:type" content="website"/>
	<meta property="og:title" content="AyeShare | Minimalist Self Hosted File Sharing"/>
	<meta property="og:url" content=""/>
	<meta property="og:site_name" content=""/>
	<title>AyeShare | Minimalist Self Hosted File Sharing Service</title>
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/styles.css">
	<link rel="shortcut icon" type="icon" href="images/favicon.png">

	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script src="js/modernizr-2.6.2.min.js"></script>
</head>
<body>

	<div class="container">
		
		<article>
			
			<header>
				<h1>AyeShare</h1>
			</header>
			
			<!--UPLOAD FORM-->
			<form action="./ayeupload.php" id="upload-form" method="post" enctype="multipart/form-data">

				<label class="file">
					<span>Upload File</span>
					<input type="hidden" name="MAX_FILE_SIZE" id="mfs" value="<?php echo $maxFileSize; ?>" />
					<input type="file" name="upload-file" id="upload-file" />
					<!-- <input type="submit" name="upload-submit" id="upload-submit" style="display:none;" /> -->
				</label>

			</form>
			
			<!--tipvalue stores the config tooltip show or hide ;;; tooltip is the tooltip that will hold our texts tip-->
			<span class="tipvalue"><?php echo $tooltip; ?></span>
			<span class="tooltip"></span>

			<ul class="dds">
				<li class="download li-dds"><img src="./images/download.png" alt="download"></li>
				<li class="delete li-dds"><img src="./images/delete.png" alt="delete"></li>
				<li class="send li-dds"><img src="./images/send.png" alt="send"></li>
			</ul>

			<p>
				Max file size: <?php echo $maxFileSize/1000000 . ' MB'; ?>
			</p>
			
			<!--UPLOAD RESPONSE SECTION-->
			<span class="error hidden">Error: File size exceeded on limit.</span>
			<span class="upload-success hidden">File successfully uploaded.</span>
			<span class="progress-bar"></span>

		</article>
		
		<!--SEND TO FRIEND FORM-->
		<div class="mailing">
			
			<form id="mail-form" method="post">
				
				<span class="close">Close</span>
				
				<label for="email">
					<input type="text" id="email" name="email" value="mail@mail.com" onfocus="this.select()" placeholder="Email"  maxlength="100" autofocus required />
				</label>
				
				<label for="subject">
					<input type="text" id="subject" name="subject" value="" placeholder="Subject" maxlength="100" autofocus />
				</label>
				
				<label for="message">
					<textarea id="message" value="" name="message" maxlength="500" placeholder="Message" autofocus required ></textarea>
				</label>
				
				<!-- <input type="submit" name="mail-submit" id="mail-submit" style="display:none;" /> -->
				<span class="send-mail">Send</span>
				<span class="send-response" style="display:none;"></span>
				
			</form>
			
		</div>

	</div>
	
	<!-- jquery, form plugin, and our main javascript files -->
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="js/jquery.form.min.js"></script>
	<script src="js/main.js"></script>

</body>
</html>

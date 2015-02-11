<?php

/**
 * This script is the main upload file of the system,
 * writes upload folder name, and writes htaccess file.
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

// hide errors
error_reporting(0);

session_start(); 
ob_start();

// parsing config.ini setup
$parseIni = parse_ini_file('./config.ini');
$maxFileSize = htmlentities($parseIni['size']);
$uploadFolder = htmlentities($parseIni['upload_folder']);

/**
 * Returns server response code.
 *
 * @param string $reason server reason
 * 
 * @return header(string)
 */
function serverError($reason)
{
    header("HTTP/ 409 Reason " . $reason);
}

/**
 * Echo json encoded errors.
 *
 * @param string $msg error mesage
 * 
 * @return echo json_encoded(json)
 */
function jsonError($msg)
{
    $error['error'] = $msg;
    header('Content-Type: application/json');
    echo json_encode($error);
}

/**
 * Returns error code messages.
 *
 * @param int $code error code
 * 
 * @return string
 */
function uploadErrors($code)
{
    switch ($code){ 
    case 1: 
        $message = "The uploaded file exceeds the upload_max_filesize directive "
        . "in php.ini. Set the size in your .htaccess file."; 
        break; 
    case 2: 
        $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was "
        . "specified in the HTML form"; 
        break; 
    case 3: 
        $message = "The uploaded file was only partially uploaded"; 
        break; 
    case 4: 
        $message = "No file was uploaded"; 
        break; 
    case 5: 
        $message = "Missing a temporary folder"; 
        break; 
    case 6: 
        $message = "Failed to write file to disk"; 
        break; 
    case 7: 
        $message = "File upload stopped by extension"; 
        break; 

    default: 
        $message = "Unknown error encountered."; 
        break; 
    }
    return $message;
}

// create folder if does not exist
if (!file_exists($uploadFolder)) {
    // change permission as required
    // we send error if making the upload directory gives error
    if (!mkdir($uploadFolder, 0755, true)) {
        jsonError("Can not create folder, check permission.");
        serverError("Can Not Create Folder");
        ob_end_flush();
        exit;
    }
}

// htaccess
// we set all script like php, perl as text plain so that
// when you view it, the script will not execute
// rather it will only display its content text
$htaccess = realpath(dirname(__FILE__)) . '/' . $uploadFolder . '/.htaccess';
if (!file_exists($htaccess)) {
    $htContent = "# php off
    <IfModule mod_php5.c>
    \tphp_flag engine off
    </IfModule>
    \n# never list uploaded files on any user
    Options -Indexes
    Order Deny, Allow
    Deny from all
    \n# make all scripts to text only
    <FilesMatch .(php|pl|cgi|sh|bash|asp|phps|py|csh|ksh|ws|wsh)$>
    \tSetHandler text/plain
    </FilesMatch>";

    $fp = fopen($htaccess, 'w');
    fwrite($fp, $htContent);
    fclose($fp);
    // 0644 is the permission recommended for htaccess
    chmod($htaccess, 0644);
}

// using $_SERVER['REQUEST_METHOD'] === 'POST' as required when using ajax
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (is_uploaded_file($_FILES['upload-file']['tmp_name'])) {

        // uniqid for filename to prevent filename conflict
        $fid = htmlentities($_SESSION['id']) . '-';
        // filename needs fullpath
        $filename = realpath(dirname(__FILE__)) . '/' . $uploadFolder
        . '/' . $fid . $_FILES['upload-file']['name'];
        
        $fileSize = $_FILES['upload-file']['size'];

        // for testing only: valid upload files
        // $img = getimagesize($_FILES['upload-file']['tmp_name']);
        // $imgMime = $img['mime'];
        // $valid_upload = array('image/png', 'image/jpeg');
        // if(!in_array($imgMime, $valid_upload)){
        // unlink($_FILES['upload-file']['tmp_name']);
        // jsonError("File not allowed.");
        // serverError("File Not Allowed");
        // ob_end_flush();
        // exit;
        // }

        // double checking the file size to ensure we're not being compromised
        if ($fileSize > $maxFileSize) {
            // if the size is greater than config file then
            // we delete the file and send error message back to ajax
            unlink($_FILES['upload-file']['tmp_name']);
            jsonError("Size exceeded on limit.");
            serverError("Size limit exceeded.");
            ob_end_flush();
            exit;
        } else {

            if (file_exists($filename)) {
                // use this if you want to return an error rather than deleting existing file
                // jsonError("File exists.");
                // serverError("File Exists");
                // ob_end_flush();
                // exit;
                unlink($filename);
            }

            // if the file has been moved successfully
            if (move_uploaded_file($_FILES['upload-file']['tmp_name'], $filename)) {
                // start the time: will be used to determine if
                // the download time is still valid
                $now = microtime(true);
                // &size for $_GET parameters of ayedownload.php
                $size = '&size=' . $fileSize;
                // full path is not used because it will cause an error
                // filepath should be relative to current working directory
                // &file for $_GET parameters of ayedownload.php
                $file = '&file=' . $uploadFolder . '/' . $fid . $_FILES['upload-file']['name'];
                // encode the time, size, and filename to base64
                $partial = base64_encode($now . $size . $file);
                // download link needs only host path not full path
                // on the server but delete needs fullpath
                $link['download'] =  $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'])
                . '/ayedownload.php?id=' . $partial;
                // delete link will have its fullpath
                // sample delete link
                // /home/folder/example.com/public_html/AyeShare/uploaded-files/image.png 
                $link['delete'] = $filename;

                // $link: { 'download': 'theDownloadLink', 'delete': 'theDeleteLink' }, the json
                header('Content-Type: application/json');
                echo json_encode($link);
                ob_end_flush();

            } else {
                jsonError(uploadErrors($_FILES['upload-file']['error']));
                serverError("Upload Error");
                ob_end_flush();
                exit;
            }
        }

    } else {
        exit;
    }
} else {
    exit;
}

?>

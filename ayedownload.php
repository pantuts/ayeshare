<?php

/**
 * This script downloads file with valid download time.
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
 
// hide erros
error_reporting(0);

// get the configured valid download time
$parseIni = parse_ini_file('./config.ini');
$downloadTime = htmlentities($parseIni['download_time']);

// get the var id in url : aWQ9MTM4MTQwNjUwNC42NTg4JmZpbGU9cGFudHV0cy5jb20vZGVtb3M
$ref = htmlentities($_GET['id']);
$refDecode = base64_decode($ref);

// get the decoded time
$dTime = substr($refDecode, 0, strpos($refDecode, '&'));
// get the full filename
// full path is not used because it will cause an error
// the filepath should be relative to parent folder
$filename = substr($refDecode,  strrpos($refDecode, '&') + 6, strlen($refDecode));
// get the size
preg_match('/size+.\d+/', $refDecode, $tmpSize);
$size = substr($tmpSize[0], strpos($tmpSize[0], '=') + 1, strlen($tmpSize[0]));

// if elapsed time is more than configured download time
if ($dTime/3600000000 > $downloadTime) {
    echo 'Download time expired.';
    exit;
} else {

    if (file_exists($filename)) {
        ob_start();
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $size);
        ob_clean();
        flush();
        @readfile($filename);
        exit;
    } else {
        echo '<br/>File does not exist.';
    }
}

?>

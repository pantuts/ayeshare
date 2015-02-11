<?php
    
/**
 * This script deletes uploaded file as for example that
 * the user accidentally uploaded the file.
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
ob_start();
    
// ajax sent data
// fileDelete has a full path
$fileToDelete = htmlentities($_POST['fileDelete']);

/**
 * Returns server responses if OK or errors are encountered.
 *
 * @param int  $code   server response code
 * @param bool $reason false by default
 *
 * @return header(string . int)
 */
function serverResponse($code, $reason = false)
{
    if ($code >= 400) {
         header('HTTP/ ' . $code . ' Reason ' . $reason);
    } else {
         header('HTTP/ 200 OK');
    }
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
    
// process the deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["fileDelete"]) 
    && !empty($_POST["fileDelete"])
) {

    if (file_exists($fileToDelete)) {
    
        if (is_writable($fileToDelete)) {
            // if delete is successful
            if (unlink($fileToDelete)) {
                serverResponse(200);
            } else {
                jsonError("Unable to delete file.");
                print_r(error_get_last());
                serverResponse(405, 'Unable To Delete File');
                ob_end_flush();
                exit;
            }
        } else {
            jsonError("Error deleting file, permission problem.");
            serverResponse(405, 'Unable To Delete File');
            ob_end_flush();
            exit;
        }
    } else {
        jsonError("File does not exist.");
        serverResponse(404, 'File Does Not Exist');
        ob_end_flush();
        exit;
    }

} else {
    echo 'Something is wrong.';
    serverResponse(405, 'Undefined');
    ob_end_flush();
}

?>

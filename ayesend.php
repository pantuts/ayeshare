<?php

/**
 * This script sends an email formatted message containing
 * the download link to any recipient.
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

// email, subject, and message were sent via $.ajax
// htmlspecialchars and htmlentities ensure that
// the parameters are sanitized before accepting: security
$to = htmlspecialchars(htmlentities($_POST['email']), ENT_QUOTES);
$subj = htmlspecialchars(htmlentities($_POST['subject']), ENT_QUOTES);
$message = wordwrap(htmlspecialchars(htmlentities($_POST['message']), ENT_QUOTES));

if ( $_SERVER['REQUEST_METHOD'] === 'POST'
    && ( isset($_POST['email']) && !empty($_POST['email']) )
    && ( isset($_POST['message']) && !empty($_POST['message']) )
) {
    // if $subj is empty then make default to No Subject
    if (empty($subj) && strlen($subj) == 0) {
        $subj = "No Subject";
    }

    // add headers as html so the recipient can directly go to link attached
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'To: ' . $email . "\r\n";
    $headers .= 'X-Mailer: PHP v' . phpversion();
    $headers .= 'X-Originating-IP: ' . $_SERVER['SERVER_ADDR'];
    // $headers .= 'From: ' . $from . "\r\n"; // this causes email as spam
    // $headers .= 'Reply-To: ' . $from . "\r\n"; // this causes email as spam
    // but you can uncomment them on your desire

    // if you only want a text/plain message
    // $headers = 'To: ' . $email . "\r\n";
    // $headers .= 'X-Mailer: PHP v' . phpversion();
    // $headers .= 'X-Originating-IP: ' . $_SERVER['SERVER_ADDR'];

    // validate the email first
    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
        // send via php mail function
        if (mail($to, $subj, $message, $headers)) {
            header('HTTP/ 200 OK');
        } else {
            // send back to ajax
            // only in console.log()
            $msg['error'] = 'Error sending email.';
            header('Content-Type: application/json');
            echo json_encode($msg);
            header('HTTP/ 409 Conflict');
            ob_end_flush();
            exit;
        }
    } else {
        // send back to ajax
        // only in console.log()
        $msg['error'] = 'Email is not allowed.';
        header('Content-Type: application/json');
        echo json_encode($msg);
        header('HTTP/ 409 Conflict');
        ob_end_flush();
        exit;
    }
}

?>

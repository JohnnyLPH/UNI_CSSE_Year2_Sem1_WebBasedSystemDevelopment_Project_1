<?php

//https://www.000webhost.com/forum/t/how-to-use-phpmailer/134686?__cf_chl_jschl_tk__=yJZ1AQgcr9ePT3ZoT5a.gLe4rWRoND5wjAKu67sEE00-1641882745-0-gaNycGzNCRE
//https://github.com/PHPMailer/PHPMailer
//https://netcorecloud.com/tutorials/send-an-email-via-gmail-smtp-server-using-php/#Error1
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* This one cannot used on XAMPP, cuz local file use '\'
    but webhost use '/'
require $_SERVER['DOCUMENT_ROOT'] . '/mail/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] . '/mail/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/mail/SMTP.php';
 */

//if test on XAMPP, use this one
require '.\mail\Exception.php';
require '.\mail\PHPMailer.php';
require '.\mail\SMTP.php';

?>
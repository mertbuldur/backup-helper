<?php
require_once 'src/MTBackup.php';

$BackupService = new \MTBackup\BackupService([
    'host'=>'localhost',
    'database'=>'laravel',
    'user'=>'root',
    'password'=>'123123asdasd'
],[
    'smtp'=>'smtp.mailgun.org',
    'port'=>587,
    'secure'=>'tls',
    'username'=>'postmaster@sandbox65b054327acb4e4aa98c22e3ad75528e.mailgun.org',
    'password'=>'5b3d1bdebbbd40c5c7caa8679f329c8a-dbdfb8ff-a2acb932',
    'backup_mail'=>'buldurmert@gmail.com'
]);
//
//$BackupService->backup()->write()->mail();

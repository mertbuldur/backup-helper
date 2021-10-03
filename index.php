<?php
require_once 'src/BackupService.php';

$BackupService = new \MTBackup\BackupService([
    'host'=>'localhost',
    'database'=>'laravel',
    'user'=>'root',
    'password'=>''
],[
    'smtp'=>'smtp.mailgun.org',
    'port'=>587,
    'secure'=>'tls',
    'username'=>'',
    'password'=>'',
    'backup_mail'=>''
]);
//
//$BackupService->backup()->write()->mail();

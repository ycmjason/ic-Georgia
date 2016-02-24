#!/usr/bin/php
<?php
$secret=file('secret');

$file=$_GET['file'];
$auth=$_GET['auth'];
$req_time=$_GET['req_time'];

$curr_time = time();
$expected_auth = md5($secret.$req_time);

if($curr_time-$req_time > 5 || $auth!=$expected_auth) die("auth is not correct, please try again.");

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($file).'"');
readfile($file);
?> 

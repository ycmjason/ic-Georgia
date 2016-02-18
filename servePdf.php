#!/usr/bin/php
<?php
$secret="this is a nice lovely secret for georgia.";

$file=$_GET['file'].".pdf";
$hash=$_GET['hash'];
$req_time=$_GET['req_time'];

$curr_time = time();
$expected_hash = md5($secret.$req_time);

if(false && ($curr_time-$req_time > 5 || $hash!=$expected_hash)) die("hash not matching, please try again.");

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($file).'"');
readfile($file);
?> 

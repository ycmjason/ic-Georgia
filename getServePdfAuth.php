#!/usr/bin/php
<?php
$secret=file('secret');

$file = $_POST['file'];
$req_time = $_POST['req_time'];

$expected_auth = md5($secret.$req_time);
?> 
https://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/servePdf.php?file=<?=$file?>&auth=<?=$expected_auth?>&req_time=<?=$req_time?>

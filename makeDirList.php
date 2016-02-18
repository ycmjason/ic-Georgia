#!/usr/bin/php
<?php
function extractDir($path){
  if(!is_dir($path)) $path = dirname($path);
  return $path;
}
function removeCurrentAndPrevDir($paths){
  return array_filter($paths, function($f){
    //echo $f.": ".is_dir($f)."<br>";
    $filename = basename($f);
    return $filename!="." && $filename!="..";
  });
}
function combinePathFile($path, $file){
  return $path."/".$file;
}

function getSubdirectories($dirpath){
  $list = array();
  $path  = extractDir($dirpath);
  $files = scandir($path);
  $files = array_map("combinePathFile",
              array_fill(0,count($files),$path),$files);
  $dirs  = removeCurrentAndPrevDir($files);
  $length = 0;
  foreach($dirs as $dir){
    if(is_dir($dir)){
      $list[basename($dir)] = getSubdirectories($dir);
    }else{
      $list[basename($dir)] = $dir;
    }
    $length++;
  }
  $list['length']=$length;
  return $list;
}

function getFileNames($path){
  $path = extractDir($path);
  $files = scandir($path);
  $pdfs  = array_map(function($s){global $path;return $path."/".$s;}, $files);
  $pdfs  = array_filter($pdfs, function($s){return basename($s)!="index.htm" && is_file($s);});
  return array_values($pdfs);
}

$directories = getSubdirectories("./pdf");
$exDirs = getSubdirectories("./exercise");
foreach ($exDirs as $cls=>$o){
  if($cls=="length") continue;
  foreach ($o as $subject=>$a){
    if($subject=="length") continue;
    foreach($a as $file=>$filefullpath){
      if($file=="length") continue;
      $directories[$cls][$subject]['exercise']=$a;
    }
  }
}
file_put_contents("dirList", json_encode($directories));
?>

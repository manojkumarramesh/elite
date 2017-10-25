<?php

//-------------------------------------------------------------------------------------------------------
// File name      : downloader.php
// Description    : file to handle Customer / Vendor Registration Documents download functionality
// Created date   : May-18-2012
// Modified date  : May-18-2012
// ------------------------------------------------------------------------------------------------------

if(!$_SERVER['HTTP_REFERER']) { header('Location: index.php'); die('Access denied'); }

$id = trim($_GET['id']);
$filename = trim($_GET['filename']);

$path = "documents/userfiles_".base64_decode($id)."/".$filename;

if(file_exists($path)) {
  
  ob_start();
  header("Cache-Control: must-revalidate, pre-check=0, post-check=0");
  header("Content-Type: application/binary");
  header("Content-Length: ".filesize($path));
  header("Content-Disposition: attachment; filename=$filename");
  readfile($path);
} else {
  
  header('Location: index.php');
  die('Access denied');
}

?>

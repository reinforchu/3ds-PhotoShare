<?php
if (empty($_SERVER['QUERY_STRING'])) exit('Error: [0]Null or empty.');
if (!preg_match('/^[A-Za-z0-9]+$/i', $_SERVER['QUERY_STRING'], $matches, PREG_OFFSET_CAPTURE)) exit('Error: [1]Invaild query.');
$md5FileName = $_SERVER['QUERY_STRING'];
if (!file_exists('./files/'.$md5FileName.'.JPG')) exit('Error: [2]Not found.');
$baseURI = 'https:/'.dirname($_SERVER['SCRIPT_NAME']);
?><!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@reinforchu">
    <meta name="twitter:title" content="3DS Photo share">
    <meta name="twitter:text:title" content="3DS Photo share">
    <meta name="og:title" content="3DS Photo share">
    <meta property="og:image" content="<?php echo $baseURI.'/'.'files/'.$md5FileName.'.JPG'; ?>">
    <title>3DS Photo share</title>
  </head>
  <body>
    <img src="<?php echo './files/'.$md5FileName.'.JPG'; ?>" alt="image">
  </body>
</html>
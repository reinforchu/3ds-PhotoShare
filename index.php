<?php
// Init particle
$message = array();
$imgResChk = false;
$queryChk = false;
$baseURI = 'https:/'.dirname($_SERVER['SCRIPT_NAME']);
$md5FileName = $_SERVER['QUERY_STRING'];

// UserAgent partical
if (!preg_match('/(Nintendo 3DS)+/', $_SERVER['HTTP_USER_AGENT'], $matches, PREG_OFFSET_CAPTURE)) array_push($message, '[Info]For use Nintendo 3DS only.');

// Card partical
if (!empty($_SERVER['QUERY_STRING'])) {
  if (!preg_match('/^[A-Za-z0-9]+$/i', $_SERVER['QUERY_STRING'], $matches, PREG_OFFSET_CAPTURE)) {
    array_push($message, '[Error]Invaild query.'); } else {
      if (!file_exists('./files/'.$md5FileName.'.JPG')) array_push($message, '[Error]Image not found.'); else $queryChk = true; 
    }
}

// Upload particle
if (!empty($_FILES)) {
  $exif = exif_read_data($_FILES['upload_image']['tmp_name'], 0, true);
  if ($exif === FALSE) array_push($message, '[Error]File broken.');
  if ($exif['FILE']['FileSize'] < 1024 || $exif['FILE']['FileSize'] > 204800) array_push($message, '[Error]File size invild.');
  if ($exif['FILE']['MimeType'] !== 'image/jpeg') array_push($message, '[Error]Unknown format.');
  if ($exif['COMPUTED']['Height'] === 240 && $exif['COMPUTED']['Width'] === 400) $imgResChk = true; // 3D screen
  if ($exif['COMPUTED']['Height'] === 240 && $exif['COMPUTED']['Width'] === 320) $imgResChk = true; // Touch screen
  if ($exif['COMPUTED']['Height'] === 528 && $exif['COMPUTED']['Width'] === 432) $imgResChk = true; // Double screen
  if (!$imgResChk) array_push($message, '[Error]Unsupport file.');
  if ($exif['IFD0']['Make'] !== 'Nintendo' || $exif['IFD0']['Model'] !== 'Nintendo 3DS') array_push($message, '[Error]Is not 3DS image file.');
  $filename = $_FILES['upload_image']['name'];
  $md5Filename = md5_file($_FILES['upload_image']['tmp_name']);
  $uploaded_path = './files/'.$md5Filename.'.JPG';
  $result = move_uploaded_file($_FILES['upload_image']['tmp_name'], $uploaded_path);
  if ($result) {
    array_push($message, '[OK]Share your SNS QR Codes.');
  } else {
    array_push($message, '[Critical]'.$_FILES['upload_image']['error']);
  }
}

// View particle
?><!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="cache-control" content="no-cache, no-store">
    <meta http-equiv="pragma" content="no-cache">
    <meta name="expires" content="0">
    <meta name="author" content="reinforchu">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="3DS Photo share">
    <meta name="keywords" content="3ds, sns, photo, share">
    <meta name="robots" content="index, follow, noarchive">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@reinforchu">
    <meta name="twitter:title" content="3DS Photo share">
    <meta name="twitter:text:title" content="3DS Photo share">
    <meta name="og:title" content="3DS Photo share">
    <meta property="og:image" content="<?php if ($queryChk) echo $baseURI.'/'.'files/'.$md5FileName.'.JPG'; else echo $baseURI.'/default.jpg'; ?>">
    <title>3DS Photo share</title>
  </head>
  <body>
    <h1>3DS Photo share</h1>
    <?php if (!empty($message)) foreach ($message as $log) print '<p>'.$log.'</p>'."\n"; ?>
    <?php if(!empty($uploaded_path)) { ?>
    <?php
    require_once "./phpqrcode/qrlib.php";
    $filepath = './files/'.$md5Filename.'_x.png';
    $contents = "twitter://post?message=\n\n{$baseURI}/?{$md5Filename}";
    QRcode::png($contents, $filepath, QR_ECLEVEL_M, 4); ?>
    <?php echo '<p>X (Twitter)</p>'; ?>
    <img src="<?php echo "{$filepath}"; ?>" alt="qr"><?php } ?>
    <?php if(!empty($uploaded_path)){ ?>
    <?php
    require_once "./phpqrcode/qrlib.php";
    $filepath = './files/'.$md5Filename.'_b.png';
    $contents = "bluesky://intent/compose?text=\n\n{$baseURI}/?{$md5Filename}";
    QRcode::png($contents, $filepath, QR_ECLEVEL_M, 4); ?>
    <?php echo '<p>Bluesky</p>'; ?>
    <img src="<?php echo "{$filepath}"; ?>" alt="qr"><?php } ?>
    <?php if ($queryChk) echo '<img src="'.'./files/'.$md5FileName.'.JPG'.'" alt="image">'; ?>
    <form action="<?php if (!preg_match('/(Nintendo 3DS)+/', $_SERVER['HTTP_USER_AGENT'], $matches, PREG_OFFSET_CAPTURE)) echo 'localhost'; ?>" method="post" enctype="multipart/form-data">
      <input type="file" name="upload_image"<?php if (!preg_match('/(Nintendo 3DS)+/', $_SERVER['HTTP_USER_AGENT'], $matches, PREG_OFFSET_CAPTURE)) echo ' disabled'; ?>>
      <input type="submit" value="POST"<?php if (!preg_match('/(Nintendo 3DS)+/', $_SERVER['HTTP_USER_AGENT'], $matches, PREG_OFFSET_CAPTURE)) echo ' disabled'; ?>>
    </form>
    <hr>
    <p>3ds-ps Version 1.1.5</p>
  </body>
</html>

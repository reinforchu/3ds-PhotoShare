<?php
if (!preg_match('/(Nintendo 3DS)+/', $_SERVER['HTTP_USER_AGENT'], $matches, PREG_OFFSET_CAPTURE)) exit('Error: [0]For use Nintendo 3DS only.');
if(!empty($_FILES)){
  $exif = exif_read_data($_FILES['upload_image']['tmp_name'], 0, true);
  if ($exif === FALSE) exit('Error: [1]File broken.');
  if ($exif['FILE']['FileSize'] < 1024 || $exif['FILE']['FileSize'] > 204800) exit('Error: [2]File size invild.');
  if ($exif['FILE']['MimeType'] !== 'image/jpeg') exit('Error: [3]Unknown format.');
  $imgResChk = false;
  if ($exif['COMPUTED']['Height'] === 240 && $exif['COMPUTED']['Width'] === 400) $imgResChk = true; // 3D screen
  if ($exif['COMPUTED']['Height'] === 240 && $exif['COMPUTED']['Width'] === 320) $imgResChk = true; // Touch screen
  if ($exif['COMPUTED']['Height'] === 528 && $exif['COMPUTED']['Width'] === 432) $imgResChk = true; // Dual screen
  if (!$imgResChk) exit('Error: [4]Unsupport file.');
  if ($exif['IFD0']['Make'] !== 'Nintendo' || $exif['IFD0']['Model'] !== 'Nintendo 3DS') exit('Error: [5]Is not 3DS image file.');
  $filename = $_FILES['upload_image']['name'];
  $md5Filename = md5_file($_FILES['upload_image']['tmp_name']);
  $uploaded_path = './files/'.$md5Filename.'.JPG';
  $result = move_uploaded_file($_FILES['upload_image']['tmp_name'], $uploaded_path);
if($result){
  $message = 'Share SNS QR Codes.';
  $img_path = $uploaded_path;
  $baseURI = 'https:/'.dirname($_SERVER['SCRIPT_NAME']);
}else{
  $message = 'Error: '.$_FILES['upload_image']['error'];
}
}else{
  $message = 'Select your screenshot.';
}
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
    <meta name="keywords" content="3ds, sns, share, photo">
    <meta name="robots" content="index, follow, noarchive">
    <title>3DS Photo share</title>
  </head>
  <body>
  <h1>3DS Photo share</h1>
  <p><?php if(!empty($message)) echo $message;?></p>
  <?php if(!empty($img_path)){ ?>
    <?php
    require_once "./phpqrcode/qrlib.php";
    $filepath = './files/'.$md5Filename.'_x.png';
    $contents = "twitter://post?message=\n\n{$baseURI}/card.php?{$md5Filename}";
    QRcode::png($contents, $filepath, QR_ECLEVEL_M, 4);
    ?>
    <?php echo '<p>X (Twitter)</p>'; ?>
    <img src="<?php echo "{$filepath}"; ?>" alt="qr"><?php } ?>
  <?php if(!empty($img_path)){ ?>
    <?php
    require_once "./phpqrcode/qrlib.php";
    $filepath = './files/'.$md5Filename.'_b.png';
    $contents = "bluesky://intent/compose?text=\n\n{$baseURI}/card.php?{$md5Filename}";
    QRcode::png($contents, $filepath, QR_ECLEVEL_M, 4);
    ?>
    <?php echo '<p>Bluesky</p>'; ?>
    <img src="<?php echo "{$filepath}"; ?>" alt="qr"><?php } ?>
    <form action="" method="post" enctype="multipart/form-data">
      <input type="file" name="upload_image"><br>
      <input type="submit" value="POST">
    </form>
  </body>
</html>
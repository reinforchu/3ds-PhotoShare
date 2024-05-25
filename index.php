<?php
if (!preg_match('/(Nintendo 3DS)+/', $_SERVER['HTTP_USER_AGENT'], $matches, PREG_OFFSET_CAPTURE)) exit('Error: [0]For use Nintendo 3DS only.');
if(!empty($_FILES)){
  $filename = $_FILES['upload_image']['name'];
  $md5Filename = md5_file($_FILES['upload_image']['tmp_name']);
  $uploaded_path = './files/'.$md5Filename.'.JPG';
  $result = move_uploaded_file($_FILES['upload_image']['tmp_name'], $uploaded_path);
if($result){
  $MSG = 'Share SNS QR Codes.';
  $img_path = $uploaded_path;
  $baseURI = 'https:/'.dirname($_SERVER['SCRIPT_NAME']);
}else{
  $MSG = 'Error: '.$_FILES['upload_image']['error'];
}
}else{
  $MSG = 'Select your screenshot.';
}
?><!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>3DS Screenshot Uploader</title>
</head>
<body>

<h1>3DS Photo share</h1>

<p><?php if(!empty($MSG)) echo $MSG;?></p>

<?php if(!empty($img_path)){ ?>
  <?php
  require_once "./phpqrcode/qrlib.php";
  $filepath = './files/'.$md5Filename.'_x.png';
  $contents = "twitter://post?message=\n\n{$baseURI}/card.php?{$md5Filename}";
  QRcode::png($contents, $filepath, QR_ECLEVEL_M, 2);
  ?>
   <?php echo '<p>X (Twitter)</p>'; ?>
  <img src="<?php echo "{$filepath}"; ?>" alt="qr">
<?php } ?>

<?php if(!empty($img_path)){ ?>
  <?php
  require_once "./phpqrcode/qrlib.php";
  $filepath = './files/'.$md5Filename.'_b.png';
  $contents = "bluesky://intent/compose?text=\n\n{$baseURI}/card.php?{$md5Filename}";
  QRcode::png($contents, $filepath, QR_ECLEVEL_M, 2);
  ?>
  <?php echo '<p>Bluesky</p>'; ?>
  <img src="<?php echo "{$filepath}"; ?>" alt="qr">
<?php } ?>

  <form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="upload_image">
    <input type="submit" value="POST">
  </form>

</body>
</html>
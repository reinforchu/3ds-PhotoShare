<?php
if (!preg_match('/(Nintendo 3DS)+/', $_SERVER['HTTP_USER_AGENT'], $matches, PREG_OFFSET_CAPTURE)) exit('Error: [0]For use Nintendo 3DS only.');
if(!empty($_FILES)){
  $filename = $_FILES['upload_image']['name'];
  $md5Filename = md5_file($_FILES['upload_image']['tmp_name']);
  $uploaded_path = './files/'.$md5Filename.'.JPG';
  $result = move_uploaded_file($_FILES['upload_image']['tmp_name'], $uploaded_path);
if($result){
  $MSG = 'Post Bluesky iOS App';
  $img_path = $uploaded_path;
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

<h3>3DS Photo share for Bluesky</h3>

<p><?php if(!empty($MSG)) echo $MSG;?></p>

<?php if(!empty($img_path)){ ?>
  <?php
  require_once "./phpqrcode/qrlib.php";
  $filepath = './files/'.$md5Filename.'.png';
  $contents = "bluesky://intent/compose?text=\n\nhttps://rein.jp/3ds/bsky.php?{$md5Filename}";
  QRcode::png($contents, $filepath, QR_ECLEVEL_M, 2);
  ?>
  <img src="<?php echo "{$filepath}"; ?>" alt="qr">
<?php } ?>

  <form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="upload_image">
    <input type="submit" value="POST">
  </form>

</body>
</html>
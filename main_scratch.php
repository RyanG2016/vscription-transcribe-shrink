<?php
session_start(['cookie_lifetime' => 86400,'cookie_secure' => true,'cookie_httponly' => true]);
 
if(!isset($_SESSION['loggedIn'])){
    header('Location: index.php');
    exit;
} else {
    // Show users the page!
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="data/images/favicon.png" />
  <link href='data/main/main.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet' />
  <link href='data/main/buttons.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet' />
  <link rel="stylesheet" href="data/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;1,300&display=swap" rel="stylesheet">

  <title>vScription Transcribe File Upload</title>
</head>

<body>
  <header>
    <div class="page_header">
      <span class="page_title">
        <h1>vScription Transcribe File Uploader</h1>
      </span>
      <span class="logo_pro"><img class="logo_pro" src="data/images/Logo_vScription_Transcribe.png" alt="vScription Transcribe Logo"></span>
    </div>
  </header>
  <hr>
  <section class="upload_form">
    <form class="upload" method="post" enctype="multipart/form-data">
      <input class="upload_btn btn" type="file" name="files[]" multiple />
      <input class="upload_btn btn" type="submit" value="Upload File(s)" name="Upload" />
    </form>
  </section>
  <a class="button-blue" onclick="location.href = 'logout.php'">
    <i class="fas fa-sign-out-alt"></i>
    Logout
  </a>
  <script src="script.js"></script>
  <!--<script src="upload.js"></script>-->
</body>

</html>

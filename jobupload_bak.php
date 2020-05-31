<?php 
include("data/parts/config.php");
include('data/parts/head.php');
include ('data/parts/constants.php');

	if(isset($_SESSION['fname']) && isset($_SESSION['lname']))
	{
		$popName = $_SESSION['fname'] . " " . $_SESSION['lname'];
	}
	else{
		$popName = "";
	}

?>

<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="data/main/job_upload.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;1,300&display=swap" rel="stylesheet">
        <link href='data/fontawesome/css/all.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet' />
        <link rel="stylesheet" href="data/css/vs-style.css">
        <title>vScription Transcribe Pro Dictation Upload</title>
</head>

<body>
        <div class="grid-wrapper">
                <div class="box box1 page_title">
                        <h1>vScription Transcribe Pro Dictation Upload</h1>
                </div>
                <div class="box box2">
                        <div class="logged_in_info">
                                <div class="logged_in_user">

                                        Logged in as: <?php echo $_SESSION['uEmail']?> |</div>
                                <div class="logout_btn"><a href="#">Logout</a></div>
                        </div>
                </div>
                <div class="box box3">
                        <img class="logo" src="data/images/Logo_vScription_Transcribe.png" alt="vScription Transcribe Pro Logo">
                </div>
                <div class="box box4">

                        <h3>Upload Instructions</h3>
                        <ul>
                                <li>1. Click Choose File </li>
                                <li>2. Choose the file(s) to upload</li>
                                <li>3. Enter the file information under the Upload Demographics section</li>
                        </ul>

                </div>
                <div class="box box5">
                        <form class="upload" method="post" enctype="multipart/form-data">
                                <label for="upload_btn">Choose Files to Upload (wav, mp3, dss, ds2, ogg)</label>
                                <input id="upload_btn" type="file" name="upload_btn" accept=".wav, .mp3, .dss, .ds2, .ogg" multiple />
                                <input type="button" class="clear_btn" value="Clear Files" name="Clear" disabled />
                                <input class="submit_btn" type="submit" value="Upload File(s)" name="Upload" disabled />
                        </form>
                </div>
                <div class="box box6">
                        <h3>Selected Files:</h3>
                        <div class="preview">
                                <p>No files currently selected for upload</p>
                        </div>
                </div>
                <div class="box box7">
                        <h3>Upload demographics</h3>
                        <div class="upload_fields">
                                <label for="demo_author">Author Name: </label>
                                <input class="demo_author" type="text">
                                <label for="demo_job_type">Job Type: </label>
                                <select id="demo_job_type">
                                        <option value="interview"> Interview</option>
                                        <option value="focus_group">Focus Group</option>
                                        <option value="notes">Notes</option>
                                        <option value="letter">Letter</option>
                                        <option value="other">Other</option>
                                </select>
                                <label for="demo_dictdate">Dictated Date: </label>
                                <input class="demo_dictdate" type="date">
                                <label for="demo_speaker_type">Speaker Type: </label>
                                <select id="demo_speaker_type">
                                        <option value="single_speaker"> Single Speaker</option>
                                        <option value="multiple_speaker">Multiple Speakers</option>
                                </select>
                                <label for="demo_comments">Comments: </label>
                                <textarea name="demo_comments" id="demo_comments" cols="30" rows="2"></textarea>
                        </div>
                </div>
                <div class="box box8">
                        <h3 class="insert_test">Upload Messages</h3>
                        <div class="upload_success_message">
                                <i class="fas fa-check-circle"></i>
                                <p>Upload(s) Successful!<br> ...Will automatically redirect to Job List in 3 seconds</p>
                        </div>
                        <div class="upload_failed_message">
                                <i class="fas fa-ban"></i>
                                <p>Upload(s) Failed. Please try again.</p>
                        </div>
                </div>
        </div>

</body>

</html>

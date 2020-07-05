<?php

//strtolower

include("config.php");
include("../../mail.php");
include("common_functions.php");

//Should this go into the case statement? It was in the formsave.php file
require_once('../../rtf3/src/HtmlToRtf.php');
require_once('../regex.php');
//////////

include('session_settings.php');
include('constants.php');
	 $lang2 = 'en';

//$_SESSION['lastPing'] = date("Y-m-d H:i:s");
include("ping.php");

if(isset($_REQUEST["reqcode"])){
	$code = $_REQUEST["reqcode"];
	
	$trials = 0;
	// BRUTEFORCE SAFE
	if($code == 30 || $code == 50 || $code == 41 || $code == 31 )
	{
//		echo 'HI BRUTE';
		// CONSTANTS TO BE USED
		$ctime = date("Y-m-d H:i:s");
		$sctime = strtotime($ctime);
		$intervalAllowing5 = 10*60;
		
		$stimePls1 = strtotime(date("Y-m-d H:i:s")) + 60*60; //1 hour
		$timePls1 = date("Y-m-d H:i:s", $stimePls1);		
		
		//PREPARE STATEMENTS
		$sqlX1 = "INSERT INTO protect (first_attempt, ip, last_attempt, trials, src, locked, unlocks_on) values(?,?,?,?,?,?,?)";
		$stX1 = mysqli_prepare($con, $sqlX1);
		
		$sqlU2 = "UPDATE protect SET first_attempt = ?, last_attempt = ?, trials = ?, locked = ?,unlocks_on=? where ip=? and src=?";
		$stU2 = mysqli_prepare($con, $sqlU2);
		
		$ip = getIP();
		$sqlQ = "SELECT first_attempt, ip, last_attempt, trials, locked, unlocks_on FROM protect WHERE ip=? AND src=?";
		$stmtQ = mysqli_prepare($con, $sqlQ);

		switch($code){
			case 30: // reset password  email
			case 50: // verify email
				//SRC = 0;
				$src = 0;				
				
				break;
				
			case 41: //LOGIN
				//SRC = 1;
				$src = 1;
				
				break;
				
				
			case 31: // SIGN UP
				//SRC = 2;
				$src = 2;
				
				break;
			}
		
		// MAIN ALGORITHM
		$stmtQ->bind_param("si", $ip, $src);		// BIND PARAMETERS  IP & SRC
		$ex = mysqli_stmt_execute($stmtQ)  or die( "Error in bind_param: (" .$con->errno . ") " . $con->error);         	// EXCUTE
		$result = mysqli_stmt_get_result($stmtQ);  	// GET RESULTS
		if(mysqli_num_rows($result) > 0)
		{ 	// ENTRY EXISTS
			// START CHECKING LOCK DOWN

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

				// CHECKING STARTS
				// CHECK IF ALREADY LOCKED
				$locked = $row['locked'];
				//CHECK TIME
				$ftime = $row['first_attempt'];
				$ltime = $row['last_attempt'];
				$unlocks_on = $row['unlocks_on'];
				$sunlocks_on = strtotime($unlocks_on);
				$trials = $row['trials'];
				$sftime = strtotime($ftime);
				$sltime = strtotime($ltime);
				$timeDiff = $sltime - $sftime;
				$allowed = $timeDiff > $intervalAllowing5;

				if($locked)
				{ // LOCKED CHECK FOR UNLOCK TIME
					if($sctime > $sunlocks_on) // unlock & pass
					{
						$trial = 1;
						$locked = 0;
//						$unlocks_on = "";
						$unlocks_on = $timePls1;

						//UPDATE protect SET first_attempt = ?, last_attempt = ?, trials = ?, locked = ?,unlocks_on = ? where ip=? and src=?
						$stU2->bind_param("ssiissi", $ctime, $ctime, $trial, $locked, $unlocks_on, $ip, $src); // BIND PARS
						$exU = mysqli_stmt_execute($stU2) or die( "Error in exc: (" .$con->errno . ")");         		// EXECUTE
						$result = mysqli_stmt_get_result($stU2);  	// GET RESULTS

						//PASS
					} 
					else // STILL LOCKED DENY
					{
						BRUTELOCK($unlocks_on, $src);
					}
				}
				else{ //NOT LOCKED
					// check timing
					if($allowed) //TIME IS OK -> RESET TRIALS TO 1 AND TIMING THEN PASS
					{
						// PASS
						echo "time ok pass";

						$trial = 1;
						$locked = 0;
//						$unlocks_on = "";
						$unlocks_on = $timePls1;

						//UPDATE protect SET first_attempt = ?, last_attempt = ?, trials = ?, locked = ?,unlocks_on =? where ip=? and src=?
						$stU2->bind_param("ssiissi", $ctime, $ctime, $trial, $locked, $unlocks_on, $ip, $src); // BIND PARS
						$exU = mysqli_stmt_execute($stU2);        		// EXECUTE
						//$exU = mysqli_stmt_execute($stU2) or die( "Error in exec: (" .$con->errno . ")");        // EXECUTE - TROUBLESHOOTING
						$result = mysqli_stmt_get_result($stU2);  	// GET RESULTS

					}
					else{
						// INCREMENT TRIALS UNTIL IT REACHES 5 LOCK HIM OUT FOR ONE HOUR
						$trial = $trials + 1;
						$locked = 0;
						$unlocks_on = $ctime;

						echo "increment";

						if($trials >= 5)
						{
							echo "lock down";
							$trial = 5;
							$locked = 1;
							$unlocks_on = $timePls1;

						}

						//UPDATE protect SET first_attempt = ?, last_attempt = ?, trials = ?, locked = ?,unlocks_on where ip=? and src=?
						$stU2->bind_param("ssiissi", $ftime, $ctime, $trial, $locked, $unlocks_on,  $ip, $src);// or die( "Error in bind_param: (" .$con->errno . ") " . $con->error); // BIND PARS
						$exU = mysqli_stmt_execute($stU2);// or die( "Error in exc: (" .$con->errno . ") " . $con->error);         		// EXCUTE
						$result = mysqli_stmt_get_result($stU2);// or die( "Error in res: (" .$con->errno . ") " . $con->error);  		// GET RESULTS

						if($trials >= 5)
						{
							BRUTELOCK($unlocks_on,$src);
						}


					}
				}


			}
		} 
		else
		{
			// NO ENTRY CLEAN SHEET
			// ADD ENTRY + 1 TRY
			echo 'clean';
			$trial = 1;
			$locked = 0;
			$unlocks_on = $ctime;
			$stX1->bind_param("sssiiis", $ctime, $ip, $ctime, $trial, $src, $locked, $unlocks_on); // BIND ABOVE PARAMETERS
			$exX = mysqli_stmt_execute($stX1);         	// EXCUTE
			$result = mysqli_stmt_get_result($stX1);  	// GET RESULTS

			//PASS
		}
		
	} // END BRUTEFROCE SAFE PROCEDURE

	
	
	if(isset($_REQUEST['args']))
	{
		
		$args = $_REQUEST['args'];
		$a = json_decode($args,true);
	}
	
	
	switch($code)
	{
		case 0:
			
			$sql = "SELECT *
					FROM cities where country=0";

			if($stmt = mysqli_prepare($con, $sql)){

				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);

					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						echo '<option></option>';
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

							echo '<option>'.$row["city"].'</option>';
	


						}
					} else{
						echo $lang2=='en'?"<p>No matches found</p>":"<p>لا يوجد نتائج</p>";

					}
				} else{
					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
					
				}
			}

			// Close statement
			mysqli_stmt_close($stmt);
			
			break;
			
		case 1:
			
			$sql = "SELECT *
					FROM cities where country=1";

			if($stmt = mysqli_prepare($con, $sql)){

				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);

					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						echo '<option></option>';
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

							echo '<option>'.$row["city"].'</option>';
	


						}
					} else{
						echo $lang2=='en'?"<p>No matches found</p>":"<p>لا يوجد نتائج</p>";

					}
				} else{
					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
					
				}
			}
			
			break;
			
		case 5:

			$sql = "SELECT *
					FROM countries";

			if($stmt = mysqli_prepare($con, $sql)){

				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);

					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						echo '<option></option>';
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

							echo '<option>'.$row["country"].'</option>';
	//						echo '<option value="'.$row["id"].'">'.$row["city"].'</option>';


						}
					} else{
						echo $lang2=='en'?"<p>No matches found</p>":"<p>لا يوجد نتائج</p>";

					}
				} else{
					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
					
				}
			}

			// Close statement
			mysqli_stmt_close($stmt);
			
			break;
			
		// Load Job Details for Player //
		// Also move audio file to working directory

		// this whole case needs adjustments as it is used in three different places some of them doesn't even need the tmp random file to be copied

		// currently known use to me is loading a job into the player in transcribe.js <<AKA LOAD BUTTON>>
		case 7:

		$a = json_decode($args,true);
		$file_id = $a['file_id'];
		$sql = "SELECT *
				FROM files WHERE file_id = ? and acc_id = ? and file_status != 3";

		if($stmt = mysqli_prepare($con, $sql))
			{
				mysqli_stmt_bind_param($stmt, "ii", $file_id, $_SESSION['accID']);

				if(mysqli_stmt_execute($stmt) ){
					$result = mysqli_stmt_get_result($stmt);

					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array

                        // If user has permission and file record exist
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

						    // check if the job record has a file saved on the server
							if ($row['filename'] != "") {
							    // file exists

                                $tmpName = $row['tmp_name'];
                                /** checking first if there's already a tmp file for this job on the server */
                                if($tmpName != null && $tmpName != "")
                                {
                                    // Temp file name already exists in the db .. check if it's still on the server workingTmp directory
                                    if(checkIfTmpFileExists($tmpName)){

                                        // pass the old file and exit this case

                                        $jobDetails = array(
                                            "file_id" => $row['file_id'],
                                            "job_id" => $row['job_id'],
                                            "file_author" => $row['file_author'],
                                            "origFilename" => $row['filename'],
                                            "suspendedText" => htmlentities($row['job_document_html'], ENT_QUOTES),
                                            "tempFilename" => $tmpName,  /** REUSING OLD TMP FILE */
                                            "file_date_dict" => $row['file_date_dict'],
                                            "file_work_type" => $row['file_work_type'],
                                            "last_audio_position" => $row['last_audio_position'],
                                            "job_status" => $row['file_status'],
                                            "file_speaker_type" => $row['file_speaker_type'],
                                            "file_comment" => $row['file_comment']
                                        );

                                        echo json_encode($jobDetails);

                                        if($row['file_status'] == 2 || $row['file_status'] == 0) // if the job was suspended/awaiting update it to being typed status = 1
                                        {
                                            updateJobStatus($con, $file_id, 1 );
                                        }

                                        // TO AUDIT LOG (act_log)
                                        recordJobFileLoaded($con);
                                        break; /** BREAKING FROM THE CASE TO PREVENT FURTHER CODE EXECUTION */
                                    }
                                }

                                /** IF NO TMP FILE AVAILABLE FOR THE JOB CREATE A NEW ONE AND SAVE IT TO DB RECORD */

                                $randFileName = random_filename(".wav");

                                // These paths need to be relative to the PHP file making the call....

								$path = "../../../uploads/". $row['filename'];
								$type = pathinfo($path, PATHINFO_EXTENSION);


                                if(copy('../../../uploads/' . $row['filename'], '../../workingTemp/' . $randFileName )) {

                                    // -> file is copied successfully to tmp -> set tmp value to db

                                    $jobDetails = array(
                                        "file_id" => $row['file_id'],
                                        "job_id" => $row['job_id'],
                                        "file_author" => $row['file_author'],
                                        "origFilename" => $row['filename'],
                                        "suspendedText" => htmlentities($row['job_document_html'], ENT_QUOTES),
                                        "tempFilename" => $randFileName,
                                        "file_date_dict" => $row['file_date_dict'],
                                        "file_work_type" => $row['file_work_type'],
                                        "last_audio_position" => $row['last_audio_position'],
                                        "job_status" => $row['file_status'],
                                        "file_speaker_type" => $row['file_speaker_type'],
                                        "file_comment" => $row['file_comment']
                                    );

                                    // add audit log entry for job file loaded
                                    recordJobFileLoaded($con);

                                    $statusToUpdate = $row['file_status'];
                                    // update status
                                    if($row['file_status'] == 2 || $row['file_status'] == 0) // if the job was suspended/awaiting update it to being typed status = 1
                                    {
//                                        updateJobStatus($con, $file_id, 1 );
                                        $statusToUpdate = 1;
                                    }

                                    saveJobTmpFileNameToDbRecord($con, $row['file_id'], $randFileName, $statusToUpdate);

                                    // return the tmp_name & job details back to transcribe
                                    echo json_encode($jobDetails);

                                }
                                else {
                                    //echo "Error moving file" . $randFileName . " to working directory..";
                                    echo false;

                                    break;
                                }

							} else {
								echo "No filename found in record --- ERROR"; //This should NEVER happen....Just for testing
                                break;
							}

						}
					}else{
					    // todo file doesn't exist or you don't have permission to access this file
                        echo false;
                        break;
                    }
				}else{
                        // echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
                    break;
					}
			} else{
//					echo "ERROR: Could not prepare statement . " . mysqli_error($con);
                    break;
			}
			// Close statement
			mysqli_stmt_close($stmt);

			break;
			
			// Job List Transcribe//
			case 9:


			$sql = "SELECT `file_id`, `job_id`, `file_type`, `audio_length`, `original_audio_type`, `filename`, `fileAudioBlob`, `fileTextBlob`, `file_tag`, `file_author`, `file_work_type`, `file_comment`, `file_speaker_type`, `file_date_dict`, (SELECT j_status_name From file_status_ref WHERE file_status_ref.j_status_id=files.file_status ORDER BY file_status LIMIT 1) as file_status, `last_audio_position`, `job_upload_date`, `job_uploaded_by`, `text_downloaded_date`, `times_text_downloaded_date`, `file_transcribed_date`, `typist_comments`, `isBillable`, `billed` FROM files
			WHERE `file_status` IN (0,1,2) and acc_id = (SELECT account from users WHERE email = '" . $_SESSION['uEmail'] . "')";

			if($stmt = mysqli_prepare($con, $sql)){

				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);

                    $a = Array(
                        'email' => $_SESSION['uEmail'],
                        'activity' => 'Job list requested by user',
                        'actPage' => 'transcribe.php',
                        'actIP' => getIP(),
                        'acc_id' => $_SESSION['accID']
                    );
                    $b = json_encode($a);
                    insertAuditLogEntry($con, $b);

					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
//						echo "<table class='transjobs_tbl' aria-label='Job List' id='translist'>";
//						echo "<thead><tr bgcolor='#1e79be' style='color: white;'><th class='table-sort'>Job Num</th><th class='table-sort'>Author</th><th class='table-sort'>Job Type</th><th class='table-sort'>Comments</th><th class='table-sort'>Date Dictated</th><th class='table-sort'>Date Uploaded</th><th class='table-sort'>Job Status</th></tr></thead>";
                        $data = '<thead>
                                    <tr class="mdc-data-table__header-row">
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job #</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Author</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job Type</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Date Dictated</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Date Uploaded</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job Status</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job Length</th>
                                    </tr>
                                    </thead>';

						$data .= '<tbody class="mdc-data-table__content">';

						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

                            $fetchedCmnt = $row['file_comment'];
                            $fetchedCmnt = encodeStr($fetchedCmnt);
                            if(!empty($fetchedCmnt))
                            {
                                $cmnt = "<i class=\"material-icons mdc-button__icon job-comment cTooltip\" aria-hidden=\"true\" title=\"{$fetchedCmnt}\">speaker_notes</i>";
                            }
                            $len = sprintf('%02d:%02d:%02d', ($row['audio_length']/ 3600),($row['audio_length']/ 60 % 60), $row['audio_length']% 60);
							$data .= "<tr data-row-id=\"{$row['job_id']}\" class=\"mdc-data-table__row\" id=\"{$row['file_id']}\" >";
								$data .=
							   "<td class=\"mdc-data-table__cell\">{$row['job_id']} {$cmnt}</td>
								<td class=\"mdc-data-table__cell\">{$row['file_author']}</td>
								<td class=\"mdc-data-table__cell\">{$row['file_work_type']}</td>
								<td class=\"mdc-data-table__cell\">{$row['file_date_dict']}</td>
								<td class=\"mdc-data-table__cell\">{$row['job_upload_date']}</td>
								<td class=\"mdc-data-table__cell\">{$row['file_status']}</td>
								<td class=\"mdc-data-table__cell\">{$len}</td>";
							$data .= '</tr>';
							}
						$data .= "</tbody>";

                        echo generateResponse($data, false);

					} else{
					    $data = "<p>&nbsp; There are currently no jobs available for your account.</p>";
                        echo generateResponse($data, true);

					}
				} else{
					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

				}
			}

			// Close statement
			mysqli_stmt_close($stmt);

			break;


		case 11:

			$a = json_decode($args,true);
//			$job_id = $a['job_id'];
			$file_id = $a['file_id'];
			$sql = "SELECT *
					FROM files WHERE file_id = ?";

			if($stmt = mysqli_prepare($con, $sql))
			{
				mysqli_stmt_bind_param($stmt, "i", $file_id);

				if(mysqli_stmt_execute($stmt) ){
					$result = mysqli_stmt_get_result($stmt);

					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array

						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

								$jobDetails = array(
									"job_id" => $row['job_id'],
									"file_author" => $row['file_author'],
									"origFilename" => $row['filename'],
									"tempFilename" => $row['tmp_name'],
									"file_date_dict" => $row['file_date_dict'],
									"file_status" => $row['file_status'],
									"file_work_type" => $row['file_work_type'],
									"file_speaker_type" => $row['file_speaker_type'],
									"file_comment" => $row['file_comment']
								);
								echo json_encode($jobDetails);
//							} else {
//								echo "No filename found in record --- ERROR"; //This should NEVER happen....Just for testing
//							}

						}
					}
				}
				else{
//						echo "<p>No matches found</p>";

				}
			} else{
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

			}


			// Close statement
			mysqli_stmt_close($stmt);

			break;


		// update job status only
		case 16:

			$a = json_decode($args,true);
//			$job_id = $a['job_id'];
			$file_id = $a['file_id'];
			$newStatus = $a['new_status'];

			updateJobStatus($con, $file_id, $newStatus);

			break;

		// Download file
		case 17:

			$a = json_decode($args,true);
			$file_id = $a['file_id'];
			$currentAccID = $_SESSION['accID']; // to prevent downloading other files belonging to another account
			$res = downloadJob($con, $file_id, $currentAccID); // true if permission granted and hash is generated (return is the hash val) - false if denied

			echo $res;
			$debug = 1;
			break;

		//---------------------------------------------------\\
		//-------------------Insert Cases 3xx----------------\\
		//---------------------------------------------------\\
		case 30:
			//inserts random token to db and send reset pwd email to user
			//
			
			$a = json_decode($args,true);
			$email = strtolower($a["email"]);
			
			$length = 78;
			$token = bin2hex(random_bytes($length));
			$token_type = 4; //reset password
			
			$sql = "INSERT INTO tokens(email, identifier,token_type) VALUES(?,?,?)";
			
			if($stmt = mysqli_prepare($con, $sql)){

				$stmt->bind_param("ssi", $email, $token,$token_type);
				
				$a = mysqli_stmt_execute($stmt);
				if($a){
					//success db record insertion
					//send email to user
					
					$link = "$cbaselink/reset.php?token=$token";
					include("reset_email_template.php");

					$mail->addAddress("$email"); //recepient
					$mail->Subject = 'Password Reset';
					$mail->Body    = $emHTML;
					$mail->AltBody = $emPlain;
					$_SESSION['src'] = 3; //source of message -> mail (3)
					try{
							$mail->send();
							$_SESSION['error'] = false; //outputs empty error in session
							$_SESSION['msg'] = "Reset Email sent."; //outputs empty error in session
							//    echo 'Message has been sent';
						} catch (Exception $e) {
							$_SESSION['error'] = true;  //error=1 in session
							$_SESSION['msg'] = "Reset Email couldn\'t be sent at this time please try again. {$mail->ErrorInfo}";
//							$_SESSION['msg'] = "{$mail->ErrorInfo}";
							//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
							echo 1;
						}
					
					//
				} else{
					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
					
				}
			}

			// Close statement
			mysqli_stmt_close($stmt);
			
			break;
		

		
		
		case 31://INSERTS SIGNUP DATA/////////
			
			$a = json_decode($args,true);
//			email:vemail, fname:vfname, lname:vlname, password:vpassword, country:vcountry,
//			  state:vstate, city:vcity, industry:vindustry, newsletter:vnewsletter};
			
			$email = strtolower($a["email"]);
			$fname = $a['fname'];
			$lname = $a['lname'];
			$password  = $a['password'];
			$country   = $a['country'];
			$state     = $a['state'];
			$city      = $a['city'];
			$industry  = $a['industry'];
			$newsletter= $a['newsletter']; 
			$ip = getenv('HTTP_CLIENT_IP')?:
				  getenv('HTTP_X_FORWARDED_FOR')?:
				  getenv('HTTP_X_FORWARDED')?:
				  getenv('HTTP_FORWARDED_FOR')?:
				  getenv('HTTP_FORWARDED')?:
				  getenv('REMOTE_ADDR');
			$plan_id = 1;
			$account_status = 1;
			/*cho "fname---->> ".$fname;
			echo var_dump($a);*/
//			echo args;
			
			$password = password_hash($password,PASSWORD_BCRYPT);

			
			$sql = "INSERT INTO users(first_name, last_name, email, password, country, city, `state`, last_ip_address, plan_id, account_status, newsletter) VALUES (?,?,?,?,?,?,?,?,1,5,?)";
			
			if($stmt = mysqli_prepare($con, $sql))
			{
				
				if( !$stmt->bind_param("ssssssssi", $fname, $lname, $email, $password, $country, $city, $state, $ip,$newsletter)   )
				{
					
//							die( "Error in bind_param: (" .$con->errno . ") " . $con->error);

				}
				
//				echo $sql;
				$B = mysqli_stmt_execute($stmt);

				
				if($B){
					$result = mysqli_stmt_get_result($stmt);

					// Check number of rows in the result set
//					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						
						/*while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

//							echo $row['count(*)']; //returns int
						}*/
						echo 'ok';
						
						$token = genToken();
						$sql = "insert into tokens(email,identifier,used,token_type) values('$email','$token',0,5) ";
						$stmt = mysqli_prepare($con, $sql);
						mysqli_stmt_execute($stmt);
						
					
						$_SESSION['src'] = 1;
						$_SESSION['msg'] = "Signed up successfully please follow the link that was sent to your Email.";
						$_SESSION['error'] = false;
						$_SESSION['uEmail'] = $email;
						$_SESSION['remember'] = false;
						sendEmail(5,$a,$token,true);
						

//					}
				}
				else{
//						echo "ERROR: Could not able to execute $sql. " . mysqli_error(1);
//						die( "Error in excute: (" .$con->errno . ") " . $con->error);
						echo 'dup';
						$_SESSION['src'] = 1;
						$_SESSION['msg'] = "User already exists please login.";
						$_SESSION['error'] = true;
					}
			
			}
			else
			{
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error(1);

			}
			

			// Close statement
			mysqli_stmt_close($stmt);
			
			break;

			//CLEAR TEMP AUDIO FILE//
			case 33:

			$a = json_decode($args,true);

			$tempAudioFile = $a['job_id'];
			//Paths need to be relative to the calling PHP file
			if (file_exists('../../workingTemp/' . $tempAudioFile)) {
				if (unlink('../../workingTemp/' . $tempAudioFile)) {
					echo "Temp Audio File Deleted";
				}
				else {
					echo "Error deleting temp audio file";
				};
			};

			break ;

			/** This updates the job/file details with new updates as Completed/Suspended job FROM TRANSCRIBE FULL VIEW**/
			case 32:

				if(isset($_POST))
				{
				//	alert('check');
					if(isset($_POST['jobNo']))
					{
						$initials = strtolower(substr($_SESSION['fname'],0,1)) . strtolower(substr($_SESSION['lname'],0,1));
						$dateTrans = date("Y-m-d H:i:s");

						$plainTinyMCEContent = $_POST['report'];

						$report = '<b>'.'Job Number: ' .'</b>'. $_POST['jobNo'] .'<br/>';
						$report = $report . '<b>'.'Author Name: ' .'</b>'. $_POST['jobAuthorName'].'<br/>';
						$report = $report . '<b>'.'Typist Name: ' .'</b>'. $initials .'<br/>';
						$report = $report . '<b>'.'Job Type: ' .'</b>'.$_POST['jobType'].'<br/>';
						$report = $report . '<b>'.'Job Length: ' .'</b>'.$_POST['jobLengthSecs'].'<br/>';
						$report = $report . '<b>'.'Date Dictated: ' .'</b>'.$_POST['jobDateDic'].'<br/>';
						$report = $report. '<b>'.'Date Transcribed: ' .'</b>' . $dateTrans .'<br/>';
						$report = $report . '<b>'.'Comments: ' .'</b>'.$_POST['jobComments'].'<br/>';
						
						$report = $report.'<br/>';
						$report = $report.'<br/>';
						$report = $report . $plainTinyMCEContent;


				
						$htmlToRtfConverter = new HtmlToRtf\HtmlToRtf($report);
				//        $htmlToRtfConverter->getRTFFile();
						$convertedRTF = trim($htmlToRtfConverter->getRTF());
						//echo($convertedRTF);

						//DB Insert Code

						$job_id = $_POST['jobNo'];
						$file_id = $_POST['file_id'];
						$audio_length = $_POST['jobLengthSecsRaw'];
						$audio_elapsed = $_POST['jobElapsedTimeSecs'];
						$file_status = $_POST['jobStatus'];
                        if($file_status == 3)
                        {
                            $file_transcribe_date = $dateTrans;
                        }else{
                            $file_transcribe_date = null;
                        }
						$transcribed_by = $_SESSION['uEmail'];
						$tmp_name = $_POST['tempFilename'];


						$sql = "UPDATE FILES SET audio_length=?, last_audio_position=?, file_status=?, 
								 file_transcribed_date=?, 
								 job_transcribed_by=?,  
								 job_document_html=?, 
								 job_document_rtf=? 
									WHERE file_id = ?";
						
						if($stmt = mysqli_prepare($con, $sql))
						{
			
							if( !$stmt->bind_param("iiisssss", $audio_length, $audio_elapsed, $file_status, $file_transcribe_date, $transcribed_by, $plainTinyMCEContent, $convertedRTF, $file_id)   )
							{
			
										die( "Error in bind_param: (" .$con->errno . ") " . $con->error);
			
							}
							$B = mysqli_stmt_execute($stmt);
			
			
							if($B){
								$result = mysqli_stmt_get_result($stmt);

								// if status is complete -> delete the tmpFile and update DB to empty tmp_name
                                if($file_status == 3)
                                {
                                    deleteTmpFile($con, $file_id, $tmp_name);
                                }

                                echo "Data Updated Successfully!";
							}
							else{
									echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
									die( "Error in excute: (" .$con->errno . ") " . $con->error);
								}
						}
						else
						{
								echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
			
						}
			
			
						// Close statement
						mysqli_stmt_close($stmt);
			
						break;
					}
				}
				else
				{
					echo "Looks like JobNo is empty";
				
				}

				break;

			/*--------------------------------*/
			case 39://INSERTS FILE UPLOAD DATA/////////
			//We can remove this as it as been included with case 61
			

			// Get next job number. Since this is an interm solution and there will only
			//be one client we are going to simply get the number of rows in the table,
			//Prepend UM- for prefix and row count padded to 7

			$sql1 = "SELECT (SELECT AUTO_INCREMENT FROM information_schema.TABLES 
						WHERE TABLE_SCHEMA = 'vtexvsi_transcribe' AND TABLE_NAME = 'files') AS next_job_id, 
       					(SELECT count(file_id)+1 AS num2 FROM files) AS next_job_num
						FROM DUAL";

			if($stmt = mysqli_prepare($con, $sql1))
			{
				if(mysqli_stmt_execute($stmt) ){
					$result = mysqli_stmt_get_result($stmt);
					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
							$nextNum = strval($row['next_job_num']);
							$nextID = strval($row['next_job_id']);
						}
					}
					else {
						// If there are no records in the DB for this account
						$nextNum = "1";
						$nextID = "1";
					}
				}
				else{
						//If the sql execute statement fails
					}
			} else{
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

			}
			//This is a dirty way to change the job prefix for testing. We will ultimately pull this from
			// the database and a new field has already been added and will be included in the production push
			if ($_SESSION['accID'] === "1") {
				$jobPrefix = "UM-";
			} else if ($_SESSION['accID'] === "2") {
				$jobPrefix = "VT-";
			}
			$nextJobNum = $jobPrefix.str_pad($nextNum, 7, "0", STR_PAD_LEFT);
			$a = json_decode($args,true);

			$jobid = $nextJobNum;
			$author = $a['file_author'];
			$worktype = $a['file_work_type'];
			$dictdate = $a['file_dict_date'];
			$speakertype = $a['file_speaker_type'];
			$comment = $a['file_comment'];
			$uploadedby= $a['job_uploaded_by'];
			$filename = $a['file_name'];


			$sql = "INSERT INTO files (job_id,file_author, file_work_type, file_date_dict, file_speaker_type, file_comment, job_uploaded_by, filename)
			VALUES (?,?,?,?,?,?,?,?)";

			$ip = getIP();

			$a = Array(
				'email' => $_SESSION['uEmail'],
				'activity' => 'Job uploaded to server',
				'actPage' => 'jobupload.php',
				//'actPage' => header('Location: '.$_SERVER['REQUEST_URI']),   //This isn't working. For now am going to hardcode the page into the function call
				'actIP' => $ip,
				'acc_id' => $_SESSION['accID']
			);
			$b = json_encode($a);
			insertAuditLogEntry($con, $b);


			break;
			
			//---------------------------------------------------\\
			//-------------------Select Cases 4xx----------------\\
			//---------------------------------------------------\\
			case 40: //check if user exist. returns 1 if exists, 0 if non (int)

			$a = json_decode($args,true);
			$email = strtolower($a["email"]);
			
			$sql = "SELECT count(*) FROM users WHERE email= ?";
			if($stmt = mysqli_prepare($con, $sql))
			{
				mysqli_stmt_bind_param($stmt, "s", $email);

				if(mysqli_stmt_execute($stmt) ){
					$result = mysqli_stmt_get_result($stmt);

					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

							echo $row['count(*)']; //returns int
						}


					}
				}
				else{
//						echo "<p>No matches found</p>";

					}
			} else{
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

			}


			// Close statement
			mysqli_stmt_close($stmt);
			
			break;
		
		case 41: //Login a user

			$a = json_decode($args,true);
			$email = strtolower($a["email"]);
			$rememberme = strtolower($a["rememberme"]);
			$password = $a["password"];
			$onehour = date("Y-m-d H:i:s");
			
			$timestamp = strtotime(date("Y-m-d H:i:s")) + 60*60;
			$onehourahead = date("Y-m-d H:i:s", $timestamp);
			
			$ip = getenv('HTTP_CLIENT_IP')?:
				  getenv('HTTP_X_FORWARDED_FOR')?:
				  getenv('HTTP_X_FORWARDED')?:
				  getenv('HTTP_FORWARDED_FOR')?:
				  getenv('HTTP_FORWARDED')?:
				  getenv('REMOTE_ADDR');

			
			$sql = "SELECT count(*),unlock_time,account_status,first_name,last_name,plan_id,password,account,id from users where email = ?";
			$sql2 = "Update users set account_status=9,unlock_time='$onehourahead' where email = ?";
			$sql3 = "Update users set account_status=1 where email = ?";
			$stmt2 = mysqli_prepare($con, $sql2);
			$stmt3 = mysqli_prepare($con, $sql3);
			if($stmt = mysqli_prepare($con, $sql))
			{
				mysqli_stmt_bind_param($stmt, "s", $email);
				mysqli_stmt_bind_param($stmt2, "s", $email);
				mysqli_stmt_bind_param($stmt3, "s", $email);
//				mysqli_stmt_bind_param($stmt4, "sss", $email,$ip,$action);
				

				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);

					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

							//echo $row['count(*)']; //returns int
							$res = $row['count(*)'];
							$utime = $row['unlock_time'];
							$status = $row['account_status'];
							if($res == 1)
							{//user exist, check password
								if( password_verify($password, $row['password']) && $status == 1)
								{ //correct password, -> LOGIN
									$_SESSION['loggedIn'] = true;
//									$_SESSION['lastPing'] = date("Y-m-d H:i:s");
									$_SESSION['uEmail'] = $email;
									$_SESSION['accID'] = $row['account'];
									$_SESSION['uid'] = $row['id'];
									$rememberme?$_SESSION['remember']=true:$_SESSION['remember']=false;
									$_SESSION['fname'] = $row['first_name'];
									$_SESSION['lname'] = $row['last_name'];
									$_SESSION['role'] = $row['plan_id'];
									
									echo 1;
									//log login to act_log

                                    $a = Array(
                                        'email' => $email,
                                        'activity' => 'Login',
                                        'actPage' => 'index.php',
                                        'actIP' => $ip,
                                        'acc_id' => 0
                                    );
                                    $b = json_encode($a);
                                    insertAuditLogEntry($con, $b);

								}
								else if( password_verify($password, $row['password']) && $status == 5)//pending verification
								{
									$_SESSION['uEmail'] = $email;
									$_SESSION['msg'] = $msgVerifyAccount;
									$_SESSION['src'] = 5;
									$_SESSION['error'] = true;
								}
								
								else //wrong password or disabled
								{ //incorrect login info
									$_SESSION['src'] = 2;
									$_SESSION['error'] = true;
									$_SESSION['uEmail'] = $email;
									
									echo 2;
									if($status == 1 || $status == 5){ //active

									    // log failed attempt to act_log
											$_SESSION['msg'] = "Login failed, email or password is incorrect.";
                                            $a = Array(
                                                'email' => $email,
                                                'activity' => 'Failed login',
                                                'actPage' => 'index.php',
                                                'actIP' => $ip,
                                                'acc_id' => 0
                                            );
                                            $b = json_encode($a);
                                            insertAuditLogEntry($con, $b);

									}
									else if($status == 0){ //disabled
//										$_SESSION['counter'] = 5-$trials;
										$_SESSION['src'] = 2;
//										$_SESSION['src'] = 0;
										$_SESSION['error'] = true;
										$_SESSION['uEmail'] = $email;
										$_SESSION['msg'] = "Account is disabled.";
										echo 0;

                                        // log failed attempt to act_log
                                        $a = Array(
                                            'email' => $email,
                                            'activity' => 'Failed login to disabled account',
                                            'actPage' => 'index.php',
                                            'actIP' => $ip,
                                            'acc_id' => 0
                                        );
                                        $b = json_encode($a);
                                        insertAuditLogEntry($con, $b);
									}
									
									
									//end inc counter
									
									
								}
							}
							else{//doesn't even exist
									echo 3;
								
									$_SESSION['src'] = 2;
									$_SESSION['error'] = true;
									$_SESSION['uEmail'] = $email;
									$_SESSION['msg'] = "Account doesn\'t exist";
								
							}
							
						}


					}
					else{
						
					}
				}
				else{
//						echo "<p>No matches found</p>";

					}
			} else{
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

			}


			// Close statement
			mysqli_stmt_close($stmt);
			
			break;
		
		case 42: //Reset User Pwd
			
			$ip = getenv('HTTP_CLIENT_IP')?:
				  getenv('HTTP_X_FORWARDED_FOR')?:
				  getenv('HTTP_X_FORWARDED')?:
				  getenv('HTTP_FORWARDED_FOR')?:
				  getenv('HTTP_FORWARDED')?:
				  getenv('REMOTE_ADDR');
			
			$a = json_decode($args,true);
			$email = strtolower($a["email"]);
			$password = $a["password"];
			$password = password_hash($password,PASSWORD_BCRYPT);
			$token = $a["token"];
			$action = "Password Reset";


			//check
			$sql = "SELECT *, DATE_ADD(time, INTERVAL '30:0' MINUTE_SECOND) as expire FROM `tokens` WHERE identifier=? and email=? AND used=0 and token_type=4 and DATE_ADD(time, INTERVAL '30:0' MINUTE_SECOND) > NOW()";
			
			//update user password
			$sql2 = "Update users set password=? where email = ?";
			$sql3 = "Update tokens set used=1 where identifier = ?";
			$stmt2 = mysqli_prepare($con, $sql2);
			$stmt3 = mysqli_prepare($con, $sql3);
			if($stmt = mysqli_prepare($con, $sql) )
			{
				mysqli_stmt_bind_param($stmt, "ss", $token ,$email);
				

				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);

					
					if(mysqli_num_rows($result) > 0){ //exists and valid
						
						//expire the token
						mysqli_stmt_bind_param($stmt3, "s",$token);
						mysqli_stmt_execute($stmt3);
						
						//update new password
						mysqli_stmt_bind_param($stmt2, "ss", $password ,$email);
						mysqli_stmt_execute($stmt2);
						
						//insert log entry
                        $a = Array(
                            'email' => $email,
                            'activity' => 'RESET PASSWORD',
                            'actPage' => 'index.php',
                            'actIP' => $ip,
                            'acc_id' => 0 // no account id while not logged in
                        );
                        $b = json_encode($a);
                        insertAuditLogEntry($con, $b);
						
						
						
						//set message and redirect
						$_SESSION['msg'] = "You can now login with your new password.";
						$_SESSION['error'] = false;
						$_SESSION['src'] = 4;
//						redirect("../../index.php");

					}
					else{ //token doesn't exist
						//				$_SESSION['msg'] = "Token Doesn\'t Exist";
						$_SESSION['msg'] = "Link doesn\'t exist or expired.";
						$_SESSION['error'] = true;
						$_SESSION['src'] = 4;
//						redirect("../../index.php");

					}
				}
				else{
//						echo "<p>No matches found</p>";

					}
			} else{
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

			}
			$ip = getIP();

			$a = Array(
				'email' => $_SESSION['uEmail'],
				'activity' => 'Password reset requested',
				'actPage' => 'index.php',
				//'actPage' => header('Location: '.$_SERVER['REQUEST_URI']),   //This isn't working. For now am going to hardcode the page into the function call
				'actIP' => $ip,
				'acc_id' => $_SESSION['accID']
			);
			$b = json_encode($a);
			insertAuditLogEntry($con, $b);

			// Close statement
			mysqli_stmt_close($stmt);
			mysqli_stmt_close($stmt2);
			
			break;
			
			
		case 50: //send verification email
			$token = genToken();
			$sql = "insert into tokens(email,identifier,used,token_type) values('".$a['email']."','$token',0,5) ";
			$stmt = mysqli_prepare($con, $sql);
			mysqli_stmt_execute($stmt);
			
			sendEmail(5,$a,$token,false);
			break;


		case 60: // Next Job Number Generator/Retriever

			$sql1 = "SELECT (SELECT AUTO_INCREMENT FROM information_schema.TABLES 
						WHERE TABLE_SCHEMA = 'vtexvsi_transcribe' AND TABLE_NAME = 'files') AS next_job_id, 
						(SELECT next_job_tally AS num2 FROM accounts WHERE acc_id = (SELECT account FROM users WHERE email = '".$_SESSION['uEmail']."' )) AS next_job_num
						FROM DUAL";								
		
			if($stmt = mysqli_prepare($con, $sql1))
			{
				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);
					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
							$nextNum = strval($row['next_job_num']);
							$nextID = strval($row['next_job_id']);

							$numbers = array(
								"next_job_id" => $nextID,
								"next_job_num" => $nextNum
							);
							echo json_encode($numbers);
						}
					}
					else {
						// If there are no records in the DB for this account
						$numbers = array(
							"next_job_id" => "1",
							"next_job_num" => "1"
						);
						echo json_encode($numbers);
					}
				}
				else{
					//If the sql execute statement fails
				}
			}
			
			mysqli_stmt_close($stmt);
			
			break;

			// Upload new Job
		case 61:		
			// InsertToDB Function 

			// TODO Add Mime check for files in case someone tries to upload a file with supported extension
			// but it isn't that filetype
			// and another user is uploading at the same time, there could be conflict's since subsequent job numbers during
			// a multiple file upload is just incrementing the nextJobNum count without checking it may already have been used.
			// Is this conditional check even needed? request method should always be post but maybe this is a 
			// security precaution or best practice?
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				if (isset($_FILES)) {
					$uploadResult = [];
					$uploadMsg = [];
					$path = '../../../uploads/';
					$extensions = ['wav', 'dss', 'ds2', 'mp3', 'ogg'];
					$nextFileID = $_POST["nextFileID"];
					$nextJobNum = $_POST["nextJobNum"];
					$authorName = $_POST["authorName"];
					$jobType = $_POST["jobType"];
					$dictDate = $_POST["dictDate"];
					$speakerType = $_POST["speakerType"];
					$comments = $_POST["comments"];
					$all_files = count($_FILES);

					for ($i = 0; $i < $all_files; $i++) {
						$file_name = $_FILES['file' . $i]['name'];
						$file_tmp = $_FILES['file' . $i]['tmp_name'];
						$file_type = $_FILES['file' . $i]['type'];
						$file_size = $_FILES['file' . $i]['size'];
						$file_duration = $_POST['dur' . $i];
						$array = explode('.', $_FILES['file' . $i]['name']);
						$file_ext = strtolower(end($array));
						if (isset($fileDemos)) {
							unset($fileDemos);
						}

						// enumerating file names
						$enumName = "F".$nextFileID."_UM".$nextJobNum."_".str_replace(" ","_", $file_name);
						$orig_filename = $file_name;
						$file_name = $enumName;
						$file = $path . $file_name;
						
						//Building demographic array for DB insert function call
						$fileDemos = array($nextFileID, $nextJobNum, $authorName, $jobType, $dictDate, $speakerType, $comments,$orig_filename, $file_name, $file_duration);

						if (!in_array($file_ext, $extensions)) {
							$uploadMsg[] = "<li>'File: ' $orig_filename . ' - UPLOAD FAILED (Extension not allowed)'</li>";              
							continue;
						}

						//Max file upload size is 128MB. PHP is configured for max size of 128MB
						if ($file_size > 134217728) {
//						if ($file_size > 1048576) {
							$uploadMsg[] = "<li>File: $orig_filename - <span style='color:red;'>UPLOAD FAILED </span>(File size exceeds limit)</li>";              
							continue;
						}

							$uplSuccess = move_uploaded_file($file_tmp, $file);
							if ($uplSuccess) {
								$result = insertToDB($con,$fileDemos);
								if ($result) {
									$uploadMsg[] = "<li>File: $orig_filename - <span style='color:green;'>UPLOAD SUCCESSFUL</span></li>";  						
								} else {
									$uploadMsg[] = "<li>'File: ' $orig_filename . ' - FAILED (File uploaded but error writing to database)'<li>";  								
								}     
							} else {
							  $uploadMsg[] = "<li>'File: ' . $orig_filename . ' - UPLOAD FAILED (An error occurred during upload)'</li>";                           
							}

						$nextFileID++;
						$nextJobNum++;
					}

					header('Content-Type: application/json');
					echo json_encode(array_values($uploadMsg), JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
					//echo json_encode(array_values($tableInsertDemos), JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
					//echo ($result);

				}

				//sendEmail(10,$a,$token,true);
			}; //Closing brace for opening If statement case 61

		break;

		/** Upload Progress Watcher **/
		case 65:

			$suffix = $_REQUEST['suffix'];
			$key = ini_get("session.upload_progress.prefix") . $suffix;
			echo json_encode($_SESSION[$key]);

			break;

			/** Password hashing **/
		case 66:

			$a = json_decode($args,true);

			$password = $a['pwd'];
			echo password_hash($password,PASSWORD_BCRYPT);

			break;

		/* Send Email Notification to user with job updates Generator Code */
		case 80:

			$a = json_decode($args,true);
			$mailtype = $a['mailtype'];	
			$usertype = $a['usertype'];
			//echo "Mail type is: " . $mailtype;
			//echo "User Type is :" . $usertype;
			$sql = "SELECT email FROM users WHERE 
						account = (SELECT account from users WHERE email = '" . $_SESSION['uEmail'] . "') AND 
						email_notification = 1 AND plan_id =" . $usertype; 
			echo "SQL Called is " . $sql;
				
				//    $sql = "SELECT * from users;";
					
			if($stmt = mysqli_prepare($con, $sql))
			{
				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);
					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						//echo "We found some rows";
						// Fetch result rows as an associative array
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
							//echo strval($row['email']);
							$recipients[]=$row['email'];
						}
					}
					else {
						// If there are no records in the DB for this account

						echo "No recipients are configured to received these notifications";
					}
				}
				else{
					echo "The SQL Call failed";
				}
				foreach($recipients as $item) {
					echo $item . "<br />";
					$a = Array (
						"email" => $item
					);
					sendEmail($mailtype, $a,"", true);
				} 
				//mailtest($data);
			}
			else {
				echo "ERROR: Could not execute $sql. " . mysqli_error($con->error) .'<br>';
				die( "Error in execute: (" .$con->errno . ") " . $con->error);
			}
			//$_SESSION['email'];
				$ip = getIP();

				if (empty($recipients)) {
					$activity = 'No receipients configured to receive notifications for ' . $mailtype . ' for this account';
				} else {
					$activity = 'Notification Email Type ' . $mailtype . ' Sent to ' . implode(",",$recipients);
				}

				$a = Array(
					'email' => $_SESSION['uEmail'],
					'activity' => $activity,
					'actPage' => 'jobupload.php',
					//'actPage' => header('Location: '.$_SERVER['REQUEST_URI']),   //This isn't working. For now am going to hardcode the page into the function call
					'actIP' => $ip,
					'acc_id' => $_SESSION['accID']
				);
				$b = json_encode($a);
				insertAuditLogEntry($con, $b);
					break;
	

        // Cases starting from 200 related to reports
		case 200:
        confirmAdminPermission();
			
		$rptStartDate = $a['startDate'];
		$rptEndDate = $a['endDate'];
        $sql="SELECT 
       file_id,
		job_id, 
		file_author, 
		file_work_type, 
		file_date_dict, 
		audio_length, 
		file_transcribed_date,
		file_comment
    FROM 
		files
	WHERE 
		file_status  = '3' AND 
		isBillable = '1' AND
		billed = '0' AND 
        acc_id = '1' AND
		file_transcribed_date BETWEEN ? AND ?";

			//Hardcoded for now. Need to add selector to client billing page screen if role_type=3 or use logged in user if role_type=2
		$acc_id = 1; 
		
		$billRatesObj = getBillRates($con, $acc_id);
		$billRates = json_decode($billRatesObj, true);
		$billRate1 = $billRates['billrate1'];
		$bill_rate1_type = $billRates['bill_rate1_type'];
            if($stmt = mysqli_prepare($con, $sql))
            {
                mysqli_stmt_bind_param($stmt, "ss", $a['startDate'], $a['endDate']);
                if(mysqli_stmt_execute($stmt)){
                    $result = mysqli_stmt_get_result($stmt);
					$secsTotal = 0;
					$minsTotal = 0;
					$html = "";

                    if(mysqli_num_rows($result) > 0){
                        $num_rows = mysqli_num_rows($result);

                        $htmlHeader = "<h3>Billing Report Date: $rptStartDate to $rptEndDate </h3>";

                        $htmlTblHead = "<table class='report'><thead><tr id='header'><th class='jobnum'>Job Number</th><th class='author'>Author</th><th class='jobtype'>Job Type</th><th class='datedict'>Date Dictated</th><th class='audiolength'>Audio Length</th><th class='transdate'>Transcribed Date</th><th class='comments'>Comments</td></th></tr></thead><tbody>";

                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
							$alSeconds = round($row['audio_length']);
							$alMinutes = sprintf('%02d:%02d:%02d', ($alSeconds/ 3600),($alSeconds/ 60 % 60), $alSeconds% 60);
                            $html .=
                                "<td>" . $row['job_id']. "</td>" .
                                "<td class='left'>" . $row['file_author']. "</td>" .
                                "<td class='left'>" . $row['file_work_type']. "</td>" .
                                "<td class='num'>" . $row['file_date_dict']. "</td>" .
								//"<td class='num'>" . $row['audio_length']. "</td>" .
								"<td class='num'>" . $alMinutes. "</td>" .
								"<td class='right'>" . $row['file_transcribed_date'] . "</td>" .
                                "<td class='right'>" . $row['file_comment'] . "</td>" .
                                "</tr>";

                            $secsTotal+=$row['audio_length'];
                        }
                        // And now the totals:
						//$htmlfoot = "</tbody><tfoot><tr>Total Minutes:". $minsTotal . "</tr></tfoot></table>";
						//Convert seconds to minutes for report
						$seconds = round($secsTotal);
						$minsTotal = sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
						$totalInMins = round(($seconds / 60),2);
						$rptGenDate = date("Y-m-d H:i:s");
						$totalBillableAmount = number_format(round(($totalInMins * $billRate1),2),2);
                        $htmltablefoot = "</tbody></table>";
                        $htmlfoot1 =  "<p><b>Report generated on:</b> $rptGenDate <b></br>Total Jobs:</b> $num_rows</br>";
						$htmlfoot2 = "<b>Total Length (hh:mm:ss):</b> $minsTotal ($totalInMins minutes) with a rate of $$billRate1/min</br>";
						$htmlfoot3 = "<b>Total Billable Amount is: $$totalBillableAmount</b></p>";
                        $data = html_entity_decode($htmlHeader . $htmlTblHead . $html . $htmltablefoot . $htmlfoot1 . $htmlfoot2 . $htmlfoot3);
                    }
                    else {
                        $data = "No Results Found";
                    }
                    echo generateResponse($data,false);

				}
				$a = Array(
					'email' => $_SESSION['uEmail'],
					'activity' => 'Client Admin Billing Report Run for period '. $a['startDate'] . ' to ' . $a['endDate'],
					'actPage' => 'billing_report.php',
					'actIP' => getIP(),
					'acc_id' => $_SESSION['accID']
				);
				$b = json_encode($a);
				insertAuditLogEntry($con, $b);
            }
			break;

			// Typist Billing Report
			case 201:
                confirmAdminPermission();
				$rptStartDate = $a['startDate'];
				$rptEndDate = $a['endDate'];
				$typist = $a['typist'];
				// We dont' want to include account here as even if they work for multiple accounts they still get paid the same
				//We can get more granular on reports if necessary
				$sql="SELECT 
			   	file_id,
				job_id, 
				file_author, 
				file_work_type, 
				file_date_dict, 
				audio_length, 
				file_transcribed_date,
				acc_id,
				file_comment
			FROM 
				files
			WHERE 
				file_status  = '3' AND 
				isBillable = '1' AND
				billed = '0' AND 
				job_transcribed_by = ? AND
				file_transcribed_date BETWEEN ? AND ?";

				//Hardcoded for now. Need to refine query to get pay rate for each job based on billtype and bill_rateX_min_pay
				$acc_id = 1; 
				
				$typistBillRatesObj = getTypistBillRates($con, $acc_id);
				$typistBillRates = json_decode($typistBillRatesObj, true);
				$typistBillRate1 = $typistBillRates['bill_rate1_pay'];
			
					if($stmt = mysqli_prepare($con, $sql))
					{
						mysqli_stmt_bind_param($stmt, "sss", $a['typist'],$a['startDate'], $a['endDate']);
						if(mysqli_stmt_execute($stmt)){
							$result = mysqli_stmt_get_result($stmt);
							$secsTotal = 0;
							$minsTotal = 0;
							$html = "";
		
							if(mysqli_num_rows($result) > 0){
								$num_rows = mysqli_num_rows($result);
		
								$htmlHeader = "<h3>Billing Report Date: $rptStartDate to $rptEndDate for $typist</h3>";
		
								$htmlTblHead = "<table class='report'><thead><tr id='header'><th class='jobnum'>Job Number</th><th class='author'>Author</th><th class='jobtype'>Job Type</th><th class='datedict'>Date Dictated</th><th class='audiolength'>Audio Length</th><th class='transdate'>Transcribed Date</th><th class='typ_account'>Account</th><th class='comments'>Comments</td></th></tr></thead><tbody>";
		
								while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
								{
									$alSeconds = round($row['audio_length']);
									$alMinutes = sprintf('%02d:%02d:%02d', ($alSeconds/ 3600),($alSeconds/ 60 % 60), $alSeconds% 60);
									$html .= 
										"<td>" . $row['job_id']. "</td>" .
										"<td class='left'>" . $row['file_author']. "</td>" .
										"<td class='left'>" . $row['file_work_type']. "</td>" .
										"<td class='num'>" . $row['file_date_dict']. "</td>" .
										//"<td class='num'>" . $row['audio_length']. "</td>" .
										"<td class='num'>" . $alMinutes. "</td>" .						
										"<td class='right'>" . $row['file_transcribed_date'] . "</td>" .
										"<td class='right'>" . $row['acc_id'] . "</td>" .										
										"<td class='right'>" . $row['file_comment'] . "</td>" .								
										"</tr>";
		
									$secsTotal+=$row['audio_length'];
								}
								// And now the totals:
								//$htmlfoot = "</tbody><tfoot><tr>Total Minutes:". $minsTotal . "</tr></tfoot></table>";
								//Convert seconds to minutes for report
								$seconds = round($secsTotal);
								$minsTotal = sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
								$totalInMins = round(($seconds / 60),2);
								$totalPayable = number_format(round(($totalInMins * $typistBillRate1),2),2);
								$rptGenDate = date("Y-m-d H:i:s");
								$htmltablefoot = "</tbody></table>";
								$htmlfoot1 =  "<br><p><b>Report generated on:</b> $rptGenDate</br><b>Total Jobs:</b> $num_rows</br>";
								$htmlfoot2 = "<b>Total Length (hh:mm:ss):</b> $minsTotal($totalInMins minutes) at pay rate of $$typistBillRate1/min</br>";
								$htmlfoot3 = "<b>Total Payable for Period: - $$totalPayable</b></p>";
								$data = html_entity_decode($htmlHeader . $htmlTblHead . $html . $htmltablefoot . $htmlfoot1 . $htmlfoot2 . $htmlfoot3);
							}
							else {
								$data = "No Results Found";
							}
							echo generateResponse($data,false);

						}
						$a = Array(
							'email' => $_SESSION['uEmail'],
							'activity' => 'Typist Billing Report Run for '. $a['typist'] . ' from ' . $a['startDate'] . ' to ' . $a['endDate'],
							'actPage' => 'typist_billing.php',
							'actIP' => getIP(),
							'acc_id' => $_SESSION['accID']
						);
						$b = json_encode($a);
						insertAuditLogEntry($con, $b);
					}
					break;

        // get all available typist names for typist_billing selector
        case 202:
             confirmAdminPermission();
            $sql="SELECT 
                   email,
                    first_name,
                    last_name
                FROM 
                    users
                WHERE 
                    plan_id  = 3";


            if($stmt = mysqli_prepare($con, $sql))
            {
                if(mysqli_stmt_execute($stmt)){
                    $result = mysqli_stmt_get_result($stmt);
                    $html = "<label for=\"typist\">Typist</label><select id=\"typist\" class=\"typist-select\">";
                    if(mysqli_num_rows($result) > 0){
                        $num_rows = mysqli_num_rows($result);
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                            $html .=
                                "<option value=\"".$row['email']."\">".
                                $row['first_name'] . " " . $row['last_name'] .
                                "</option>";
                        }
                        $html .= "</select>";
                        echo generateResponse($html,false, false);
                    }
                    else {
                            // "No Results Found"
                        $html .= "<option value=\"0\">".
                        "No Typists Found".
                        "</option>";
                        $html .= "</select>";
                        echo generateResponse($html,false, true);
                    }
                }
            }

            break;

            // set session variable from transcribe prior to opening mini player
        case 203:
            $_SESSION['popFileID'] = $a['fileID'];
            return true;

            break;

            // get session variable and load (popup/transcribe) (2-way)
        case 204:
            if(!isset($_SESSION['popFileID']))
            {
                echo false;
            }else{
                echo $_SESSION['popFileID'];
                unset($_SESSION['popFileID']);
            }

            break;

        /** This updates the job/file details with new updates as Completed/Suspended job **/
        case 205:

            if(isset($_POST))
            {
                if(isset($_POST['jobID']))
                {
//                    $initials = strtolower(substr($_SESSION['fname'],0,1)) . strtolower(substr($_SESSION['lname'],0,1));
                    $dateTrans = date("Y-m-d H:i:s");

//                    $plainTinyMCEContent = $_POST['report'];

//                    $report = '<b>'.'Job Number: ' .'</b>'. $_POST['jobNo'] .'<br/>';
//                    $report = $report . '<b>'.'Author Name: ' .'</b>'. $_POST['jobAuthorName'].'<br/>';
//                    $report = $report . '<b>'.'Typist Name: ' .'</b>'. $initials .'<br/>';
//                    $report = $report . '<b>'.'Job Type: ' .'</b>'.$_POST['jobType'].'<br/>';
//                    $report = $report . '<b>'.'Job Length: ' .'</b>'.$_POST['jobLengthSecs'].'<br/>';
//                    $report = $report . '<b>'.'Date Dictated: ' .'</b>'.$_POST['jobDateDic'].'<br/>';
//                    $report = $report. '<b>'.'Date Transcribed: ' .'</b>' . $dateTrans .'<br/>';
//                    $report = $report . '<b>'.'Comments: ' .'</b>'.$_POST['jobComments'].'<br/>';

//                    $report = $report.'<br/>';
//                    $report = $report.'<br/>';
//                    $report = $report . $plainTinyMCEContent;



//                    $htmlToRtfConverter = new HtmlToRtf\HtmlToRtf($report);
//                    $convertedRTF = trim($htmlToRtfConverter->getRTF());

                    //DB Insert Code

                    $job_id = $_POST['jobID'];
                    $file_id = $_POST['file_id'];
//                    $audio_length = $_POST['jobLengthSecsRaw'];
                    $audio_elapsed = $_POST['jobElapsedTimeSecs'];
                    $file_status = $_POST['jobStatus'];
                    if($file_status == 5)
                    {
                        $file_transcribe_date = $dateTrans;
                    }else{
                        $file_transcribe_date = null;
                    }
                    $transcribed_by = $_SESSION['uEmail'];
                    $tmp_name = $_POST['tempFilename'];


                    $sql = "UPDATE FILES SET 
                                last_audio_position=?, 
                                file_status=?, 
								file_transcribed_date=?, 
								job_transcribed_by=?
									WHERE file_id = ?";

                    if($stmt = mysqli_prepare($con, $sql))
                    {

                        if( !$stmt->bind_param("iissi",  $audio_elapsed, $file_status, $file_transcribe_date, $transcribed_by, $file_id)   )
                        {
                            die( "Error in bind_param: (" .$con->errno . ") " . $con->error);
                        }
                        $B = mysqli_stmt_execute($stmt);


                        if($B){
                            $result = mysqli_stmt_get_result($stmt);

                            // if status is complete -> delete the tmpFile and update DB to empty tmp_name
                            if($file_status == 5)
                            {
                                deleteTmpFile($con, $file_id, $tmp_name);
                            }

                            echo "Data Updated Successfully!";
                        }
                        else{
                            echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
                            die( "Error in excute: (" .$con->errno . ") " . $con->error);
                        }
                    }
                    else
                    {
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

                    }


                    // Close statement
                    mysqli_stmt_close($stmt);

                    break;
                }
            }
            else
            {
                echo "Looks like JobNo is empty";

            }

            break;
	} //switch end

}//if code is set end
else{
//	header('location:../../index.php');
    echo "Bad request"; // DO NOT MODIFY,
    // CRITICAL FOR JOBUPLOAD @job_upload.js TO CHECK FOR FILE UPLOAD EXCEEDING THE LIMIT OF POST_MAX_SIZE
}

 
// close connection

mysqli_close($con);


//////   <------------- Functions ----------------->  ////////

function updateJobStatus($con, $fileID, $newStatus)
{

	$sql = "UPDATE FILES SET file_status=? WHERE file_id=?";

	if($stmt = mysqli_prepare($con, $sql))
	{
		mysqli_stmt_bind_param($stmt, "ii", $newStatus, $fileID);

		if(mysqli_stmt_execute($stmt) ){
//			$result = mysqli_stmt_get_result($stmt);
//			echo true;
		}
		else{
			// couldn't update job status
		}
	} else{
		//	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
	}
	// Close statement
	mysqli_stmt_close($stmt);


}

function downloadJob($con, $fileID, $accID)
{
	/*-----Get existing data for job --------*/

	$sql3 = "SELECT times_text_downloaded_date, text_downloaded_date FROM files WHERE file_id = ?
    AND acc_id = ?";
	if($stmt3 = mysqli_prepare($con, $sql3))
	{
		mysqli_stmt_bind_param($stmt3, "ii", $fileID, $accID);
		if(mysqli_stmt_execute($stmt3) ){
			$result = mysqli_stmt_get_result($stmt3);
			// Check number of rows in the result set
			if(mysqli_num_rows($result) == 1){
				// Fetch result rows as an associative array
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
//				$times_downloaded = $row['times_text_downloaded_date'];
//				$times_downloaded++;
//				$text_downloaded_date = $row['text_downloaded_date'];

			} else {
				// TODO PERMISSION DENIED OR FILE DOESN'T EXIST RETURN ERROR MSG
				return false;
			}
		} else {
			//echo "Error executing " .$sql3;
		}
	}else{
		//echo "ERROR: Could not prepare to execute $sql1. " . mysqli_error($con);
		//die( "Error in excute: (" .$con->errno . ") " . $con->error);
	}

	// generate download hash
	$downloadHash = md5(time() . mt_rand(1,1000000));
	$sql = "INSERT INTO downloads(acc_id, hash, file_id) VALUES(?,?,?)";
	//echo $sql;

	if($stmt = mysqli_prepare($con, $sql)){

		$stmt->bind_param("isi", $accID, $downloadHash, $fileID);

		$a = mysqli_stmt_execute($stmt);
		if($a){
			return $downloadHash;
		} else{
			//echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
			// todo failed to create download link
			return false;
		}
	}
	return false; // couldn't prepare hash statement at all

}

function sendEmail($mailType,$a,$token,$appendmsg)//0:login-default, 1:signup, 4:resetpwd 5:signup verify
{
	include('constants.php');
	include("../../mail.php");
	echo "Mail type is: " .$mailType;
	$email = strtolower($a["email"]);
	$_SESSION['src'] = $mailType;
	

//	$_SESSION['msg'] = $_SESSION['msg']; 
	$link = "$cbaselink/verify.php?token=$token";
	
	switch($mailType)
	{
		case 0:
			include('reset_email_template.php');
			$sbj = "Password Reset";
			$_SESSION['src'] = 2; //TDO
				break;
		case 5:
			include('verify_email_temp.php');
			$sbj = "Email Verification";
			$_SESSION['src'] = 2;
			$mail->addCC("sales@vtexvsi.com");
				break;
		case 10:
			include('document_complete_template.php');
			$sbj = "New Document(s) Ready for Download";
			$_SESSION['src'] = 2; 
			$mail->addCC("sales@vtexvsi.com");
		break;
		case 15:
			include('job_ready_for_typing_template.php');
			$sbj = "New Job(s) Ready for Typing";
			$_SESSION['src'] = 2; 
			$mail->addCC("sales@vtexvsi.com");
		default:
			$sbj = "vScription Transcribe Pro";
				break;
	}
	
	$mail->addAddress("$email"); //recepient
	$mail->Subject = $sbj;
	$mail->Body    = $emHTML;
	$mail->AltBody = $emPlain;
	
	
	try{
			$mail->send();
			$_SESSION['msg'] = $_SESSION['msg'];
			if(!$appendmsg)
			{
				$_SESSION['error'] = false; //outputs empty error in session
				$_SESSION['msg'] = "Email sent."; 
			}
			else{
				$_SESSION['msg'] = $_SESSION['msg'] . "<br/><br/>" . "Email sent."; 
			}
		
		} catch (Exception $e) {
			if(!$appendmsg)
			{
				$_SESSION['error'] = true;  //error=1 in session
				$_SESSION['msg'] = "Email couldn\'t be sent at this time please try again. {$mail->ErrorInfo}";
			}
			else{
				$_SESSION['msg'] = $_SESSION['msg'] . "<br/><br/>" . "Email couldn\'t be sent at this time please try again. {$mail->ErrorInfo}";
			}
		}
} //send Email end

function genToken()
{
	$length = 78;
	return bin2hex(random_bytes($length));	
}

function BRUTELOCK($msg, $src)
{
	$_SESSION['msg'] = "You have been banned from this server for 1 hour unlocks on $msg";
	switch($src)
	{
		case 0: //emailing
				$_SESSION['msg'] = "Try again in one hour.";
			break;
			
		case 1: //login
				$_SESSION['msg'] = "5 Login Attempts Failed, Try again in one hour.";
			break;
			
		case 2: //signup
				$_SESSION['msg'] = "Max number of signup reached, Try again in one hour.";
			break;
			
			
	}
//	$_SESSION['msg'] = "You have been banned from this server for 1 hour unlocks on $msg";
	$_SESSION['error'] = true;
	$_SESSION['src'] = 2;
		

	die(); // !IMPORTANT DO NOT DELETE
}



/* REQUEST CODES */
// INSERT -> 3XX
// Select -> 4XX

//function random_filename($length, $directory = '', $extension = '')
function random_filename($extension = '')
{
    // default to this files directory if empty...
//    $dir = !empty($directory) && is_dir($directory) ? $directory : dirname(__FILE__);

    $dir = "../../workingTemp/";

    $filename = uniqid(time()."_", true) . $extension;

    while (file_exists($dir . $filename))
    {
        $filename = uniqid(time()."_", true) . $extension;
    }
    return $filename;
}

function checkIfTmpFileExists($tmpName)
{
    $dir = "../../workingTemp/"; // working tmp directory
    return file_exists($dir.$tmpName);
}

function recordJobFileLoaded($con)
{
    //Insert audit detail. Note we will need to look at where we place this to ensure that we don't put it in a place where it may not fire
    // like after a return call or something like that
    //Need to figure out best way to get the acc_id. I think it should be added to the session but what if the user has access to multiple accounts?
    $ip = getIP();

    $a = Array(
        'email' => $_SESSION['uEmail'],
        'activity' => 'Loading audio file into player',
        'actPage' => 'transcribe.php',
        'actIP' => $ip,
        'acc_id' => $_SESSION['accID']
    );
    $b = json_encode($a);
    insertAuditLogEntry($con, $b);
}

function saveJobTmpFileNameToDbRecord($con, $fileID, $newTmpName, $newStatus)
{

    $sql = "UPDATE FILES SET file_status=?, tmp_name = ? WHERE file_id=?";

    if($stmt = mysqli_prepare($con, $sql))
    {
        mysqli_stmt_bind_param($stmt, "isi", $newStatus, $newTmpName, $fileID);

        if(mysqli_stmt_execute($stmt) ){
//			$result = mysqli_stmt_get_result($stmt);
//			echo true;
        }
        else{
            // couldn't update job status
        }
    } else{
        //	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    // Close statement
    mysqli_stmt_close($stmt);
}


function deleteTmpFile($con, $fileID, $tmpName)
{

    $sql = "UPDATE FILES SET tmp_name=null WHERE file_id=?";

    if($stmt = mysqli_prepare($con, $sql))
    {
        mysqli_stmt_bind_param($stmt, "i", $fileID);

        if(mysqli_stmt_execute($stmt) ){

            // if removed from db -> delete the file from workingTempDirectory

            $dir = "../../workingTemp/"; // working tmp directory
            unlink($dir.$tmpName);

        }
        else{
            // couldn't update job status
        }
    } else{
        //	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    // Close statement
    mysqli_stmt_close($stmt);
}


function insertToDB($dbcon, $input) {
	$con = $dbcon;
	$nextFileID = $input[0];
	$nextNum = $input[1];
	$authorName = $input[2];
	$jobType = $input[3];
	$dictDate = $input[4];
	$speakerType = $input[5];
	$comments = $input[6];
	$orig_filename = $input[7];
	$file_name = $input[8];
    $file_duration = $input[9];
	$uploadedBy = $_SESSION['uEmail'];
	
		//This is a dirty way to change the job prefix for testing. We will ultimately pull this from
	// the database and a new field has already been added and will be included in the production push
	if ($_SESSION['accID'] == 1) {
		$jobPrefix = "UM-";
	} else if ($_SESSION['accID'] == 2) {
		$jobPrefix = "VT-";
	}
	$nextJobNum = $jobPrefix .str_pad($nextNum, 7, "0", STR_PAD_LEFT);

	$sql = "INSERT INTO files (job_id, file_author, file_work_type, file_date_dict, file_speaker_type, file_comment, job_uploaded_by, filename, orig_filename, acc_id, audio_length) VALUES (?,?,?,?,?,?,?,?,?,(SELECT account from users WHERE email = ?),?)";

	if($stmt = mysqli_prepare($con, $sql))
	{

		if( !$stmt->bind_param("ssssisssssi", $nextJobNum, $authorName, $jobType, $dictDate,
			$speakerType, $comments, $uploadedBy, $file_name, $orig_filename, $uploadedBy, $file_duration) )
		{
			die( "Error in bind_param: (" .$con->errno . ") " . $con->error);
		}
		$B = mysqli_stmt_execute($stmt);
		if($B){
            // $result = mysqli_stmt_get_result($stmt);
            $sql1 = "UPDATE accounts SET next_job_tally=next_job_tally+1 where acc_id = (SELECT account from users WHERE email = '" . $uploadedBy . "')";

            if($stmt = mysqli_prepare($con, $sql1))
            {
                $B = mysqli_stmt_execute($stmt);
                if($B){
                    $result = mysqli_stmt_get_result($stmt);
//                    echo $sql1 . " ran succesfully";
                    return true;
                }
//                else{
//                    "ERROR: Unable to increment next job number $sql1. " . mysqli_error($con);
//                    die( "Execution Error: (" .$con->errno . ") " . $con->error);
//                    echo 'dup';
//                }
            }
            else
            {
                echo "ERROR: Could not prepare to execute $sql1. " . mysqli_error($con);
                die( "Execution Error: (" .$con->errno . ") " . $con->error);
            }
			return true;

		}
		else{
//			"ERROR: Was not able to execute $sql. " . mysqli_error($con);
			die( "Execution Error: (" .$con->errno . ") " . $con->error);
			echo 'dup';
		}
	}
	else
	{
		echo "ERROR: Could not prepare to execute $sql. " . mysqli_error($con);
		die( "Execution Error: (" .$con->errno . ") " . $con->error);

	}



	// Close statement
	//mysqli_stmt_close($stmt); //WE need to reuse it. It will get closed when the function closes
}
function generateEmailNotifications($sqlcon, $mailtype)
{
	$con = $sqlcon;
	$sql = "SELECT email FROM users WHERE 
		account = (SELECT account from users WHERE email = '" . $_SESSION['uEmail'] . "') AND 
        email_notification = 1 AND plan_id = 3";

//    $sql = "SELECT * from users;";

	if ($stmt = mysqli_prepare($con, $sql)) {
		if (mysqli_stmt_execute($stmt)) {
			$result = mysqli_stmt_get_result($stmt);
			// Check number of rows in the result set
			if (mysqli_num_rows($result) > 0) {
				//echo "We found some rows";
				// Fetch result rows as an associative array
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
					//echo strval($row['email']);
					$recipients[] = $row['email'];
				}
			} else {
				// If there are no records in the DB for this account

				echo "No recipients are configured to received these notifications";
			}
		} else {
			echo "The SQL Call failed";
		}
		foreach ($recipients as $item) {
			echo $item . "<br />";
			$a = array(
				"email" => $item
			);
			sendEmail($mailtype, $a, "", true);
		}
		//mailtest($data);
	} else {
		echo "ERROR: Could not execute $sql. " . mysqli_error($con->error) . '<br>';
		die("Error in execute: (" . $con->errno . ") " . $con->error);
	}
	//$_SESSION['email'];
}

function confirmAdminPermission()
{
    if($_SESSION['role'] != 1){
        exit();
    }else{
        return true;
    }
}
 
function getBillRates($con, $acc_id) {

	$sql="SELECT bill_rate1,bill_rate1_type,bill_rate1_desc,
	bill_rate2,bill_rate2_type,bill_rate2_TAT,bill_rate2_desc,
	bill_rate3,bill_rate3_type,bill_rate3_TAT,bill_rate3_desc, 
	bill_rate4,bill_rate4_type,bill_rate4_TAT,bill_rate4_desc,
	bill_rate5,bill_rate5_type,bill_rate5_TAT,bill_rate5_desc
	FROM accounts WHERE acc_id  = 1";
	if($stmt = mysqli_prepare($con, $sql))
	{
 		if(mysqli_stmt_execute($stmt)){
			$result = mysqli_stmt_get_result($stmt);
			$billInfo = "";
			if(mysqli_num_rows($result) > 0){
				$num_rows = mysqli_num_rows($result);
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					//We're going to send all billing rates even though we're only using one now
					$billInfo = Array (
						"billrate1" => $row['bill_rate1'],
						"bill_rate1_type" => $row['bill_rate1_type'],
						"billrate2" => $row['bill_rate2'],
						"bill_rate2_type" => $row['bill_rate2_type'],
						"billrate3" => $row['bill_rate3'],
						"bill_rate3_type" => $row['bill_rate3_type'],
						"billrate4" => $row['bill_rate4'],
						"bill_rate4_type" => $row['bill_rate4_type'],
						"billrate5" => $row['bill_rate5'],
						"bill_rate5_type" => $row['bill_rate5_type'],
					);
				}
				return json_encode($billInfo);
			}
			else {
				// "No Results Found"
				// Note this should NEVER happen as the billtype1 fields are NOT NULL values
				$billInfo = Array (
					"billrate1" => "0",
					"bill_rate1_type" => "1"
				);
				return json_encode($billInfo);
			}
		}
	}
}

function getTypistBillRates($con, $acc_id) {

	$sql="SELECT bill_rate1_min_pay, bill_rate2_min_pay, bill_rate3_min_pay, bill_rate4_min_pay, bill_rate5_min_pay
	FROM accounts WHERE acc_id  = 1";
	if($stmt = mysqli_prepare($con, $sql))
	{
 		if(mysqli_stmt_execute($stmt)){
			$result = mysqli_stmt_get_result($stmt);
			$typistBillInfo = "";
			if(mysqli_num_rows($result) > 0){
				$num_rows = mysqli_num_rows($result);
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					//We're going to send all billing rates even though we're only using one now
					$typistBillInfo = Array (
						"bill_rate1_pay" => $row['bill_rate1_min_pay'],
						"bill_rate2_pay" => $row['bill_rate2_min_pay'],
						"bill_rate3_pay" => $row['bill_rate3_min_pay'],
						"bill_rate4_pay" => $row['bill_rate4_min_pay'],
						"bill_rate5_pay" => $row['bill_rate5_min_pay']
					);
				}
				return json_encode($typistBillInfo);
			}
			else {
				// "No Results Found"
				// Note this should NEVER happen as the billtype1 fields are NOT NULL values
				$typistBillInfo = Array (
					"bill_rate1_pay" => 0
				);
				return json_encode($typistBillInfo);
			}
		}
	}
}

////////////////////////////////////////
//           Index of                 //
//           Req Codes                //
//------------------------------------//
// 200 -> Get Billing Reports
// 201 -> Get Typist  Reports
// 202 -> Get Typists names for 201
// 203 -> set popFileID to pass to mini player (in sess var)
// 204 -> get popFileID if any and clear the session var
// 205 -> update file status from popup
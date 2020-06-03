<?php

//strtolower

include("config.php");
include("../../mail.php");


session_start();
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
				
				
			case 31: // SIGNUP
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
						$unlocks_on = "";

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
						$unlocks_on = "";

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

			case 7:

			$a = json_decode($args,true);
			$job_id = $a['job_id'];
			$sql = "SELECT *
					FROM files WHERE job_id = ?";

		if($stmt = mysqli_prepare($con, $sql))
			{
				mysqli_stmt_bind_param($stmt, "s", $job_id);

				if(mysqli_stmt_execute($stmt) ){
					$result = mysqli_stmt_get_result($stmt);

					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array

						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

							if ($row['filename'] != "") {
									$randFileName = random_filename(15) . '.wav';
									// These paths need to be relative to the PHP file making the call....
									if (copy('../../../uploads/' . $row['filename'], '../../workingTemp/' . $randFileName )) {
									}
									else {
										//echo "Error moving file" . $randFileName . " to working directory..";
									};
									$jobDetails = array(
										"job_id" => $row['job_id'],
										"file_author" => $row['file_author'],
										"origFilename" => $row['filename'],
										"tempFilename" => $randFileName,
										"file_date_dict" => $row['file_date_dict'],
										"file_work_type" => $row['file_work_type'],
										"file_speaker_type" => $row['file_speaker_type'],
										"file_comment" => $row['file_comment']
									);
								echo json_encode($jobDetails);
							} else {
								echo "No filename found in record --- ERROR"; //This should NEVER happen....Just for testing
							}

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

		// Job List Admin//
			
			case 8:

			$sql = "SELECT `file_id`, `job_id`, `file_type`, `original_audio_type`, `filename`, `fileAudioBlob`, `fileTextBlob`, `file_tag`, `file_author`, `file_work_type`, `file_comment`, `file_speaker_type`, `file_date_dict`, (SELECT j_status_name From file_status_ref WHERE file_status_ref.j_status_id=files.file_status ORDER BY file_status LIMIT 1) as file_status, `last_audio_position`, `job_upload_date`, `job_uploaded_by`, `text_downloaded_date`, `times_text_downloaded_date`, `file_transcribed_date`, `typist_comments`, `isBillable`, `billed` FROM files";

			if($stmt = mysqli_prepare($con, $sql)){

				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);



					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
//						echo '<table class="mdc-data-table__table" aria-label="Jobs List">';
//						echo "<thead><tr bgcolor='#1e79be' style='color: white;'><th class='table-sort'>Job Num</th><th class='table-sort'>Author</th><th class='table-sort'>Job Type</th><th class='table-sort'>Comments</th><th class='table-sort'>Date Dictated</th><th class='table-sort'>Date Uploaded</th><th class='table-sort'>Job Status</th><th class='table-sort'>File</th></tr></thead>";
						echo '<thead>
                                    <tr class="mdc-data-table__header-row">
                                       <!-- <th class="mdc-data-table__header-cell mdc-data-table__header-cell&#45;&#45;checkbox" role="columnheader" scope="col">
                                            <div class="mdc-checkbox mdc-data-table__header-row-checkbox mdc-checkbox&#45;&#45;selected">
                                                <input type="checkbox" class="mdc-checkbox__native-control" aria-label="Checkbox for header row selection"/>
                                                <div class="mdc-checkbox__background">
                                                    <svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
                                                        <path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59" />
                                                    </svg>
                                                    <div class="mdc-checkbox__mixedmark"></div>
                                                </div>
                                            </div>
                                        </th>-->
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job #</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Author</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job Type</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Comments</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Date Dictated</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Date Uploaded</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job Status</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">File</th>
                                    </tr>
                                    </thead>';

						echo '<tbody class="mdc-data-table__content">';


						/*<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Date Dictated</th>
                                        <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Date Uploaded</th>*/


						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

							echo "<tr data-row-id=\"{$row['job_id']}\" class=\"mdc-data-table__row\">";
//							echo '<td class="mdc-data-table__cell mdc-data-table__cell--checkbox">
//                                                <div class="mdc-checkbox mdc-data-table__row-checkbox">';
							// todo uncomment these below
//							echo " <input type=\"checkbox\" class=\"mdc-checkbox__native-control\" aria-labelledby=\"{$row['job_id']}\"/>";

				/*			echo '<div class="mdc-checkbox__background">
                                                        <svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
                                                            <path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59" />
                                                        </svg>
                                                        <div class="mdc-checkbox__mixedmark"></div>
                                                    </div>
                                                </div>
                                            </td>';*/

							echo "<td class=\"mdc-data-table__cell\">{$row['job_id']}</td>
                                            <td class=\"mdc-data-table__cell\" id=\"{$row['job_id']}\">{$row['file_author']}</td>
                                            <td class=\"mdc-data-table__cell\">{$row['file_work_type']}</td>
                                            <td class=\"mdc-data-table__cell\">{$row['file_comment']}</td>
                                            <td class=\"mdc-data-table__cell\">{$row['file_date_dict']}</td>
                                            <td class=\"mdc-data-table__cell\">{$row['job_upload_date']}</td>
                                            <td class=\"mdc-data-table__cell\">{$row['file_status']}</td>";

							/*<td class=\"mdc-data-table__cell mdc-data-table__cell--numeric\">{$row['file_date_dict']}</td>
                                            <td class=\"mdc-data-table__cell mdc-data-table__cell--numeric\">{$row['job_upload_date']}</td>*/

								echo "<td class=\"mdc-data-table__cell\">";

								if($row['file_status'] == "Completed") {
									echo "<a class=\"material-icons\" href='#'>cloud_download</a>";
								}

								echo "</td>";



							/* <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job #</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Author</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job Type</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Comments</th>
                                        <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Date Dictated</th>
                                        <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Date Uploaded</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Job Status</th>
                                        <th class="mdc-data-table__header-cell" role="columnheader" scope="col">File</th>*/

							echo '   </tr>';

//							}



						}
						echo "</tbody>";
//						echo "</table>";
					} else{
						echo $lang2=='en'?"<p>No matches found</p>":"<p>لا يوجد نتائج</p>";

					}
				} else{
					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
					
				}
			}
			/*else {
				echo "Something's wrong here: " . mysqli_error($con);
			}*/
			// Close statement
			mysqli_stmt_close($stmt);
			
			break;

			// Job List Transcribe//
			// Not using DataTables

			case 9:

			$sql = "SELECT `file_id`, `job_id`, `file_type`, `original_audio_type`, `filename`, `fileAudioBlob`, `fileTextBlob`, `file_tag`, `file_author`, `file_work_type`, `file_comment`, `file_speaker_type`, `file_date_dict`, (SELECT j_status_name From file_status_ref WHERE file_status_ref.j_status_id=files.file_status ORDER BY file_status LIMIT 1) as file_status, `last_audio_position`, `job_upload_date`, `job_uploaded_by`, `text_downloaded_date`, `times_text_downloaded_date`, `file_transcribed_date`, `typist_comments`, `isBillable`, `billed` FROM files
			WHERE `file_status` IN (0,1,2)";

			if($stmt = mysqli_prepare($con, $sql)){

				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);
					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						echo "<table class='transjobs_tbl' aria-label='Job List' id='translist'>";
						echo "<thead><tr bgcolor='#1e79be' style='color: white;'><th class='table-sort'>Job Num</th><th class='table-sort'>Author</th><th class='table-sort'>Job Type</th><th class='table-sort'>Comments</th><th class='table-sort'>Date Dictated</th><th class='table-sort'>Date Uploaded</th><th class='table-sort'>Job Status</th></tr></thead>";


						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

							echo "<tr><td>{$row['job_id']}</td><td>{$row['file_author']}</td><td>{$row['file_work_type']}</td><td>{$row['file_comment']}</td><td>{$row['file_date_dict']}</td><td>{$row['job_upload_date']}</td><td>{$row['file_status']}</td><td></td></tr>";
							}
						echo "</table>";
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
		
			/*--------------------------------*/
			case 39://INSERTS FILE UPLOAD DATA/////////

			// Get next job number. Since this is an interm solution and there will only
			//be one client we are going to simply get the number of rows in the table,
			//Prepend UM- for prefix and row count padded to 7

			$sql1 = "SELECT count(*) AS num FROM files";
			if($stmt = mysqli_prepare($con, $sql1))
			{
				if(mysqli_stmt_execute($stmt) ){
					$result = mysqli_stmt_get_result($stmt);
					// Check number of rows in the result set
					if(mysqli_num_rows($result) > 0){
						// Fetch result rows as an associative array
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
							$nextNum = strval($row['num']++);
						}
					}
					else {
						// If there are no records in the DB for this account
						$nextNum = "1";
					}
				}
				else{
						//If the sql execute statement fails
					}
			} else{
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

			}
			$nextJobNum = "UM-".str_pad($nextNum, 7, "0", STR_PAD_LEFT);
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


			if($stmt = mysqli_prepare($con, $sql))
			{

				if( !$stmt->bind_param("ssssisss", $jobid, $author, $worktype, $dictdate, $speakertype, $comment, $uploadedby, $filename) )
				{

				die( "Error in bind_param: (" .$con->errno . ") " . $con->error);

				}

				// echo $sql;
				$B = mysqli_stmt_execute($stmt);


				if($B){
				$result = mysqli_stmt_get_result($stmt);
				echo 'ok';
				}
				else{
				"ERROR: Was not able to execute $sql. " . mysqli_error(1);
				die( "Execution Error: (" .$con->errno . ") " . $con->error);
				echo 'dup';
				}

			}
			else
			{
			echo "ERROR: Could not able to execute $sql. " . mysqli_error(1);
			die( "Execution Error: (" .$con->errno . ") " . $con->error);

			}
			// Close statement
			mysqli_stmt_close($stmt);

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
			$role = $a["plan_id"];
			$onehour = date("Y-m-d H:i:s");
			
			$timestamp = strtotime(date("Y-m-d H:i:s")) + 60*60;
			$onehourahead = date("Y-m-d H:i:s", $timestamp);
			
			$ip = getenv('HTTP_CLIENT_IP')?:
				  getenv('HTTP_X_FORWARDED_FOR')?:
				  getenv('HTTP_X_FORWARDED')?:
				  getenv('HTTP_FORWARDED_FOR')?:
				  getenv('HTTP_FORWARDED')?:
				  getenv('REMOTE_ADDR');
			$action = "Login";
		
			
			$sql4 = "INSERT INTO userlog(email, user_ip, action) VALUES (?,?,?)";

			
			$sql = "SELECT count(*),unlock_time,account_status,first_name,last_name,plan_id,password from users where email = ?";
			$sql2 = "Update users set account_status=9,unlock_time='$onehourahead' where email = ?";
			$sql3 = "Update users set account_status=1 where email = ?";
			$stmt2 = mysqli_prepare($con, $sql2);
			$stmt3 = mysqli_prepare($con, $sql3);
			$stmt4 = mysqli_prepare($con, $sql4);
			if($stmt = mysqli_prepare($con, $sql))
			{
				mysqli_stmt_bind_param($stmt, "s", $email);
				mysqli_stmt_bind_param($stmt2, "s", $email);
				mysqli_stmt_bind_param($stmt3, "s", $email);
				mysqli_stmt_bind_param($stmt4, "sss", $email,$ip,$action);
				

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
								{ //correct password, -> login
									$_SESSION['loggedIn'] = true;
//									$_SESSION['lastPing'] = date("Y-m-d H:i:s");
									$_SESSION['uEmail'] = $email;
									$rememberme?$_SESSION['remember']=true:$_SESSION['remember']=false;
									$_SESSION['fname'] = $row['first_name'];
									$_SESSION['lname'] = $row['last_name'];
									$_SESSION['role'] = $row['plan_id'];
									
									echo 1;
									//log login
									mysqli_stmt_execute($stmt4);
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
									
											$_SESSION['msg'] = "Login failed, email or password is incorrect.";
											$action = "Failed Login Attempt";
											mysqli_stmt_execute($stmt4);

									}
									else if($status == 0){ //disabled
//										$_SESSION['counter'] = 5-$trials;
										$_SESSION['src'] = 2;
//										$_SESSION['src'] = 0;
										$_SESSION['error'] = true;
										$_SESSION['uEmail'] = $email;
										$_SESSION['msg'] = "Account is disabled.";
										echo 0;
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
			
			$sql4 = "INSERT INTO userlog(email, user_ip, action) VALUES (?,?,?)";

			//check
			$sql = "SELECT *, DATE_ADD(time, INTERVAL '30:0' MINUTE_SECOND) as expire FROM `tokens` WHERE identifier=? and email=? AND used=0 and token_type=4 and DATE_ADD(time, INTERVAL '30:0' MINUTE_SECOND) > NOW()";
			
			//update user password
			$sql2 = "Update users set password=? where email = ?";
			$sql3 = "Update tokens set used=1 where identifier = ?";
			$stmt2 = mysqli_prepare($con, $sql2);
			$stmt3 = mysqli_prepare($con, $sql3);
			$stmt4 = mysqli_prepare($con, $sql4);
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
						mysqli_stmt_bind_param($stmt4, "sss",$email, $ip, $action);
						mysqli_stmt_execute($stmt4);
						
						
						
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


			// Close statement
			mysqli_stmt_close($stmt);
			mysqli_stmt_close($stmt2);
//			mysqli_stmt_close($stmt3);
			mysqli_stmt_close($stmt4);
			
			break;
			
			
		case 50: //send verification email
			$token = genToken();
			$sql = "insert into tokens(email,identifier,used,token_type) values('".$a['email']."','$token',0,5) ";
			$stmt = mysqli_prepare($con, $sql);
			mysqli_stmt_execute($stmt);
			
			sendEmail(5,$a,$token,false);
			break;

			
			
	} //switch end
	
	
	
	
	
	
	
}//if code is set end
else{
	header('location:../../index.php');
}

 
// close connection

mysqli_close($con);


//////Functions

function sendEmail($mailType,$a,$token,$appendmsg)//0:login-default, 1:signup, 4:resetpwd 5:signup verify
{
	include('constants.php');
	include("../../mail.php");
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
		default:
			$sbj = "vScription Transcribe";
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

function getIP()
{
	return getenv('HTTP_CLIENT_IP')?:
				  getenv('HTTP_X_FORWARDED_FOR')?:
				  getenv('HTTP_X_FORWARDED')?:
				  getenv('HTTP_FORWARDED_FOR')?:
				  getenv('HTTP_FORWARDED')?:
				  getenv('REMOTE_ADDR');
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

function random_filename($length, $directory = '', $extension = '')
{
    // default to this files directory if empty...
    $dir = !empty($directory) && is_dir($directory) ? $directory : dirname(__FILE__);

    do {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
    } while (file_exists($dir . '/' . $key . (!empty($extension) ? '.' . $extension : '')));

    return $key . (!empty($extension) ? '.' . $extension : '');
}


?>

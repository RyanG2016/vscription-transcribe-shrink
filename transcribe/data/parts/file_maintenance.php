<?php

include("config.php");
//include(__DIR__ . "/../../../mail/mail_init.php");
include("common_functions.php");

include('constants.php');
$lang2 = 'en';

$a = Array(
    'maint_table' => 'SYSTEM',
    'maint_recs_affected' => 0,
    'maint_comments' => "--------STARTING MAINTENANCE PROCESSES-------------"
);
$b = json_encode($a);
insertMaintenanceAuditLogEntry($con, $b);

# Maintenance Commands
# Order IS IMPORTANT

# ---------------------------------------------------------------------	  
# payments TBL #
# Delete payment records older than 1 month excluding last user payment
# INPUT: older than x (interval 1 month)
# ---------------------------------------------------------------------
    $sql = "SELECT COUNT(*) as numRowsToDelete from payments where payment_id in (select payment_id from payments l where payment_id NOT IN (select max(payment_id) as latest from payments 
        group by user_id) AND timestamp < DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH))";
    $sql1 = "DELETE from payments where payment_id in (select payment_id from payments l where payment_id NOT IN (select max(payment_id) as latest from payments 
        group by user_id) AND timestamp < DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH))";
    $table_name = 'payments';

    if ($stmt = mysqli_prepare($con, $sql)) {
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            // Check number of rows in the result set
            if (mysqli_num_rows($result) > 0) {
                $maint_recs_affected = 0;
                // Check to see if there are records to delete and store the total
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    $maint_recs_affected = $row['numRowsToDelete'];
                }
                if ($maint_recs_affected > 0) {
                    if ($stmt2 = mysqli_prepare($con, $sql1)) {
                        if (mysqli_stmt_execute($stmt2)) {
                            $result = mysqli_stmt_get_result($stmt2);
                            $a = Array(
                                'maint_table' => $table_name,
                                'maint_recs_affected' => $maint_recs_affected,
                                'maint_comments' => "Maintenance for '$table_name' table succeeded"
                            );
                            $b = json_encode($a);
                            insertMaintenanceAuditLogEntry($con, $b);
                        } else {
                            $a = Array(
                                'maint_table' => $table_name,
                                'maint_recs_affected' => '0',
                                'maint_comments' => "Maintenance for '$table_name' table failed while running the delete command"
                            );
                            $b = json_encode($a);
                            insertMaintenanceAuditLogEntry($con, $b);
                        }
                    }
                } else {
                    $a = Array(
                        'maint_table' => $table_name,
                        'maint_recs_affected' => '0',
                        'maint_comments' => "No records purged from '$table_name' table."
                    );
                    $b = json_encode($a);
                    insertMaintenanceAuditLogEntry($con, $b);
                }  
            } else {
                $a = Array(
                    'maint_table' => $table_name,
                    'maint_recs_affected' => '0',
                    'maint_comments' => "No records purged from '$table_name' table due to possible SQL error"
                );
                $b = json_encode($a);
                insertMaintenanceAuditLogEntry($con, $b);

            }
        } else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
        }
    } else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }

# ---------------------------------------------------------------------	  
# sr_queue TBL #
# Deletes Logs older than 1 month
# NOTE: This has a primnary key and can't be deleted if file exists. Need to decide if I'm going to leave them
# INPUT: older than x
# ---------------------------------------------------------------------

/*
$sql = "SELECT COUNT(*) as numRowsToDelete from sr_queue where srq_id in (select srq_id from sr_queue l where srq_id NOT IN (select max(srq_id) as latest from sr_queue 
group by srq_status) AND srq_timestamp < DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH))";
$sql1 = "DELETE from sr_queue where srq_id in (select srq_id from sr_queue l where srq_id NOT IN (select max(srq_id) as latest from sr_queue 
group by srq_status) AND srq_timestamp < DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH))";

if ($stmt = mysqli_prepare($con, $sql)) {
if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    // Check number of rows in the result set
    if (mysqli_num_rows($result) > 0) {
        $maint_recs_affected = 0;
        // Check to see if there are records to delete and store the total
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $maint_recs_affected = $row['numRowsToDelete'];
        }
        if ($maint_recs_affected > 0) {
            if ($stmt2 = mysqli_prepare($con, $sql1)) {
                if (mysqli_stmt_execute($stmt2)) {
                    $result = mysqli_stmt_get_result($stmt2);
                    $a = Array(
                        'maint_table' => 'payments',
                        'maint_recs_affected' => $maint_recs_affected,
                        'maint_comments' => 'Maintenance for payments table succeeded'
                    );
                    $b = json_encode($a);
                    insertMaintenanceAuditLogEntry($con, $b);
                } else {
                    $a = Array(
                        'maint_table' => 'payments',
                        'maint_recs_affected' => '0',
                        'maint_comments' => 'Maintenance for payments table failed while running the delete command'
                    );
                    $b = json_encode($a);
                    insertMaintenanceAuditLogEntry($con, $b);
                }
            }
        } else {
            $a = Array(
                'maint_table' => 'payments',
                'maint_recs_affected' => '0',
                'maint_comments' => 'No records purged from payment table.'
            );
            $b = json_encode($a);
            insertMaintenanceAuditLogEntry($con, $b);
        }  
    } else {
        $a = Array(
            'maint_table' => 'payments',
            'maint_recs_affected' => '0',
            'maint_comments' => 'No records purged from payment table due to possible SQL error'
        );
        $b = json_encode($a);
        insertMaintenanceAuditLogEntry($con, $b);

    }
} else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}
} else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}
*/

# ---------------------------------------------------------------------	  
# tokens TBL #
# Delete tokens older than 7 days
#
# INPUT: older than x
# ---------------------------------------------------------------------

$sql = "SELECT COUNT(*) as numRowsToDelete from tokens where time < (DATE_SUB(CURRENT_DATE, INTERVAL 1 WEEK))";
$sql1 = "DELETE from tokens where time < (DATE_SUB(CURRENT_DATE, INTERVAL 1 WEEK))";
$table_name = "tokens";

if ($stmt = mysqli_prepare($con, $sql)) {
if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    // Check number of rows in the result set
    if (mysqli_num_rows($result) > 0) {
        $maint_recs_affected = 0;
        // Check to see if there are records to delete and store the total
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $maint_recs_affected = $row['numRowsToDelete'];
        }
        if ($maint_recs_affected > 0) {
            if ($stmt2 = mysqli_prepare($con, $sql1)) {
                if (mysqli_stmt_execute($stmt2)) {
                    $result = mysqli_stmt_get_result($stmt2);
                    $a = Array(
                        'maint_table' => $table_name,
                        'maint_recs_affected' => $maint_recs_affected,
                        'maint_comments' => "Maintenance for '$table_name' table succeeded"
                    );
                    $b = json_encode($a);
                    insertMaintenanceAuditLogEntry($con, $b);
                } else {
                    $a = Array(
                        'maint_table' => $table_name,
                        'maint_recs_affected' => '0',
                        'maint_comments' => "Maintenance for '$table_name' table failed while running the delete command"
                    );
                    $b = json_encode($a);
                    insertMaintenanceAuditLogEntry($con, $b);
                }
            }
        } else {
            $a = Array(
                'maint_table' => $table_name,
                'maint_recs_affected' => '0',
                'maint_comments' => "No records purged from '$table_name table."
            );
            $b = json_encode($a);
            insertMaintenanceAuditLogEntry($con, $b);
        }  
    } else {
        $a = Array(
            'maint_table' => $table_name,
            'maint_recs_affected' => '0',
            'maint_comments' => "No records purged from '$table_name' table due to possible SQL error"
        );
        $b = json_encode($a);
        insertMaintenanceAuditLogEntry($con, $b);

    }
} else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}
} else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}

# ---------------------------------------------------------------------	  
# Files TBL #
# Delete files that have met or exceed retention days set in account table
#
# INPUT: older than x
# ---------------------------------------------------------------------

//Clear the deletedUploads folder

//Check to ensure that the deletedUploads folder exists
if (!file_exists("../../../deletedUploads")){
    mkdir("../../../deletedUploads");
}
$deletedFilesList = glob('../../../deletedUploads/*');
foreach($deletedFilesList as $deletedFileName){
    if(is_file($deletedFileName)){
		if ((time()- filemtime($deletedFileName)) >= 86400) { 
			if (unlink($deletedFileName)) {				
				$a = Array(
					'maint_table' => 'deletedFilesDirectory',
					'maint_recs_affected' => 1,
					'maint_comments' => "Permanently deleted file '$deletedFileName' from deletedUploads folder"
				);
				$b = json_encode($a);
				insertMaintenanceAuditLogEntry($con, $b);
			} else {
					$a = Array(
					'maint_table' => 'deletedFilesDirectory',
					'maint_recs_affected' => 1,
					'maint_comments' => "An error occurred when trying to permanently delete file '$deletedFileName' from deletedUploads folder"
				);
				$b = json_encode($a);
				insertMaintenanceAuditLogEntry($con, $b);
			}
		} else {
			$a = Array(
            'maint_table' => 'deletedFilesDirectory',
            'maint_recs_affected' => 1,
            'maint_comments' => "Not deleting file '$deletedFileName' from deletedUploads folder as it hasn't been 24 hours"
			);
			$b = json_encode($a);
			insertMaintenanceAuditLogEntry($con, $b);
		}			
    }   
}
$sql = "SELECT COUNT(*) as numRowsToDelete
FROM files LEFT JOIN accounts ON accounts.acc_id = files.acc_id
WHERE DATE(text_downloaded_date) < DATE_SUB(CURDATE(), INTERVAL accounts.acc_retention_time DAY)
AND deleted = 0
AND audio_file_deleted_date IS NULL";
$sql1 = "SELECT filename, file_id, has_caption, files.acc_id
FROM files LEFT JOIN accounts ON accounts.acc_id = files.acc_id
WHERE DATE(text_downloaded_date) < DATE_SUB(CURDATE(), INTERVAL accounts.acc_retention_time DAY)
AND deleted = 0
AND audio_file_deleted_date IS NULL";
$sql2 = "UPDATE files SET deleted = 1, deleted_date = CURRENT_TIMESTAMP(), audio_file_deleted_date = CURRENT_TIMESTAMP(), job_document_html = null,  job_document_rtf = null, captions = null
WHERE file_id = ?";
$table_name = "files";

if ($stmt = mysqli_prepare($con, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        // Check number of rows in the result set
        if (mysqli_num_rows($result) > 0) {
            $maint_recs_affected = 0;
            // Check to see if there are records to delete and store the total
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $maint_recs_affected = $row['numRowsToDelete'];
            }
            // Here is where we execute the query and iterate through the rows
            if ($maint_recs_affected > 0) {
                if ($stmt1 = mysqli_prepare($con, $sql1)) {
                    if (mysqli_stmt_execute($stmt1)) {
                        $result1 = mysqli_stmt_get_result($stmt1);
                        while ($drow = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
                            $file_id = $drow['file_id'];
                            $filename = $drow['filename'];
                            $has_caption = $drow['has_caption'];
                            if ($stmt2 = mysqli_prepare($con, $sql2)) {
                                mysqli_stmt_bind_param($stmt2, "s", $file_id);
                                if (mysqli_stmt_execute($stmt2)) {
                                    $result2 = mysqli_stmt_get_result($stmt2);
                                    //echo "SUCCESS purging files table";
                                    $a = Array(
                                        'maint_table' => $table_name,
                                        'maint_recs_affected' => 1,
                                        'maint_comments' => "Maintenance for files '$table_name' succeeded. Updated row for file '$file_id'"
                                    );
                                    $b = json_encode($a);
                                    insertMaintenanceAuditLogEntry($con, $b);
                                    //Let's attempt to delete the actual audio file
                                    if (file_exists('../../../uploads/' . $filename)) {
                                        if (rename('../../../uploads/' . $filename,'../../../deletedUploads/' . $filename)) {
                                            // Update file date modified
                                            if (touch('../../../deletedUploads/' . $filename)) {
                                                //echo "Temp Audio File Deleted";
                                                $a = Array(
                                                    'maint_table' => $table_name,
                                                    'maint_recs_affected' => 1,
                                                    'maint_comments' => "Deleted audio file '$filename' for file '$file_id'"
                                                );
                                                $b = json_encode($a);
                                                insertMaintenanceAuditLogEntry($con, $b);
                                                // Now lets' delete any intermediate files (DSS, DS2 files)
                                                $int_dss_file = substr($filename, 0, strrpos($filename, ".")) . ".dss";
                                                $int_ds2_file = substr($filename, 0, strrpos($filename, ".")) . ".ds2";
                                                if (file_exists('../../../uploads/' . $int_dss_file)) {
                                                    if (rename('../../../uploads/' . $int_dss_file,'../../../deletedUploads/' . $int_dss_file)) {
                                                        // Update file date modified
                                                        if (touch('../../../deletedUploads/' . $int_dss_file)) {
                                                            //echo "Temp Audio File Deleted";
                                                            $a = Array(
                                                                'maint_table' => $table_name,
                                                                'maint_recs_affected' => 1,
                                                                'maint_comments' => "Deleted original audio file '$int_dss_file' for file '$file_id'"
                                                            );
                                                            $b = json_encode($a);
                                                            insertMaintenanceAuditLogEntry($con, $b);
                                                        } else {
                                                            //echo "Error updating timestamp ";
                                                               $a = Array(
                                                                   'maint_table' => $table_name,
                                                                   'maint_recs_affected' => 1,
                                                                   'maint_comments' => "Error updating audio file timestamp '$int_dss_file' for file '$file_id'"
                                                               );
                                                               $b = json_encode($a);
                                                               insertMaintenanceAuditLogEntry($con, $b); 
                                                        }
                                                    }
                                                }
                                                if (file_exists('../../../uploads/' . $int_ds2_file)) {
                                                    if (rename('../../../uploads/' . $int_ds2_file,'../../../deletedUploads/' . $int_ds2_file)) {
                                                        // Update file date modified
                                                        if (touch('../../../deletedUploads/' . $int_ds2_file)) {
                                                            //echo "Temp Audio File Deleted";
                                                            $a = Array(
                                                                'maint_table' => $table_name,
                                                                'maint_recs_affected' => 1,
                                                                'maint_comments' => "Deleted original audio file '$int_ds2_file' for file '$file_id'"
                                                            );
                                                            $b = json_encode($a);
                                                            insertMaintenanceAuditLogEntry($con, $b);
                                                        } else {
                                                            //echo "Error updating timestamp ";
                                                               $a = Array(
                                                                   'maint_table' => $table_name,
                                                                   'maint_recs_affected' => 1,
                                                                   'maint_comments' => "Error updating audio file timestamp '$int_ds2_file' for file '$file_id'"
                                                               );
                                                               $b = json_encode($a);
                                                               insertMaintenanceAuditLogEntry($con, $b); 
                                                        }
                                                    }
                                                }
                                            } else {
                                             //echo "Error updating timestamp ";
                                                $a = Array(
                                                    'maint_table' => $table_name,
                                                    'maint_recs_affected' => 1,
                                                    'maint_comments' => "Error updating audio file timestamp '$filename' for file '$file_id'"
                                                );
                                                $b = json_encode($a);
                                                insertMaintenanceAuditLogEntry($con, $b); 
                                            }
                                        } else {
                                            //echo "Error deleting temp audio file";
                                            $a = Array(
                                                'maint_table' => $table_name,
                                                'maint_recs_affected' => 1,
                                                'maint_comments' => "Error deleting audio file '$filename' for file '$file_id'. Maybe it is in the deletedUploads folder?"
                                            );
                                            $b = json_encode($a);
                                            insertMaintenanceAuditLogEntry($con, $b);
                                        };
                                    } else {
                                        //echo "Audio file doesn't exist";
                                        $a = Array(
                                            'maint_table' => $table_name,
                                            'maint_recs_affected' => 1,
                                            'maint_comments' => "Audio file '$filename' for file '$file_id' doesn't exist. Unable to delete file"
                                        );
                                        $b = json_encode($a);
                                        insertMaintenanceAuditLogEntry($con, $b);
                                    };
                                    if ($has_caption) {
                                         //Now let's delete any caption files that may exist for the file
                                        $file_caption = substr($filename, 0, strrpos($filename, ".")) . ".vtt";
                                        //echo $file_caption;
                                        if (file_exists('../../../uploads/' . $file_caption)) {
                                            if (rename('../../../uploads/' . $file_caption,'../../../deletedUploads/' . $file_caption)) {
                                                //echo "Caption file deleted";
												if (touch('../../../deletedUploads/' . $file_caption)) {
													$a = Array(
														'maint_table' => $table_name,
														'maint_recs_affected' => 1,
														'maint_comments' => "Deleted audio file caption '$file_caption' for file '$file_id'"
													);
													$b = json_encode($a);
													insertMaintenanceAuditLogEntry($con, $b);
												} else {
													$a = Array(
														'maint_table' => $table_name,
														'maint_recs_affected' => 1,
														'maint_comments' => "Unable to update date modified for '$file_caption' for file '$file_id'"
													);
													$b = json_encode($a);
													insertMaintenanceAuditLogEntry($con, $b);
												}
                                            } else {
                                                //echo "Error deleting audio caption file";
                                                $a = Array(
                                                    'maint_table' => $table_name,
                                                    'maint_recs_affected' => 1,
                                                    'maint_comments' => "Error deleting audio file caption '$file_caption' for file '$file_id'"
                                                );
                                                $b = json_encode($a);
                                                insertMaintenanceAuditLogEntry($con, $b);
                                            };
                                        } else {
                                            //echo "Caption file doesn't exist";
                                            $a = Array(
                                                'maint_table' => $table_name,
                                                'maint_recs_affected' => 1,
                                                'maint_comments' => "Caption file '$file_caption' for file '$file_id' doesn't exist. Unable to delete file"
                                            );
                                            $b = json_encode($a);
                                            insertMaintenanceAuditLogEntry($con, $b);
                                        };
                                    };
                                } else {
                                //echo "Error executing row update";
                                }
                            } else {
                                //echo "Error preparing SQL Statement for row update";
                            }

                        }
                    } else {
                        echo "Error selecting data to purge";
                    }
                } else {
                    echo "Error getting records to purge total";
                }
                $a = Array(
                    'maint_table' => $table_name,
                    'maint_recs_affected' => $maint_recs_affected,
                    'maint_comments' => "Maintenance for '$table_name' table succeeded."
                );
                $b = json_encode($a);
                insertMaintenanceAuditLogEntry($con, $b);
            } else {
                $a = Array(
                    'maint_table' => $table_name,
                    'maint_recs_affected' => '0',
                    'maint_comments' => "No records matched purge criteria from '$table_name' table."
                );
                $b = json_encode($a);
                insertMaintenanceAuditLogEntry($con, $b);
            }  
        } else {
            echo "Error getting purge count from SQL";
        }
    } else {
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
} else {
	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}

# ---------------------------------------------------------------------	  
# Purge Organization ActLog TBL #
# Delete records from activity log that have met or exceed retention days set in account table
#
# INPUT: older than x
# ---------------------------------------------------------------------
$sql = "SELECT COUNT(*) as numRowsToDelete FROM act_log WHERE act_log_id IN (SELECT act_log_id FROM act_log log INNER JOIN accounts a ON log.acc_id = a.acc_id WHERE act_log_date < DATE_SUB(CURRENT_DATE, INTERVAL a.act_log_retention_time DAY))";
$sql1 = "DELETE FROM act_log WHERE act_log_id IN (SELECT act_log_id FROM act_log log INNER JOIN accounts a ON log.acc_id = a.acc_id WHERE act_log_date < DATE_SUB(CURRENT_DATE, INTERVAL a.act_log_retention_time DAY))";
$table_name = 'act_log';

if ($stmt = mysqli_prepare($con, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        // Check number of rows in the result set
        if (mysqli_num_rows($result) > 0) {
            $maint_recs_affected = 0;
            // Check to see if there are records to delete and store the total
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $maint_recs_affected = $row['numRowsToDelete'];
            }
            if ($maint_recs_affected > 0) {
                if ($stmt2 = mysqli_prepare($con, $sql1)) {
                    if (mysqli_stmt_execute($stmt2)) {
                        $result = mysqli_stmt_get_result($stmt2);
                        $a = Array(
                            'maint_table' => $table_name,
                            'maint_recs_affected' => $maint_recs_affected,
                            'maint_comments' => "Maintenance for '$table_name' table succeeded"
                        );
                        $b = json_encode($a);
                        insertMaintenanceAuditLogEntry($con, $b);
                    } else {
                        $a = Array(
                            'maint_table' => $table_name,
                            'maint_recs_affected' => '0',
                            'maint_comments' => "Maintenance for '$table_name' table failed while running the delete command"
                        );
                        $b = json_encode($a);
                        insertMaintenanceAuditLogEntry($con, $b);
                    }
                }
            } else {
                $a = Array(
                    'maint_table' => $table_name,
                    'maint_recs_affected' => '0',
                    'maint_comments' => "No records purged from '$table_name' table."
                );
                $b = json_encode($a);
                insertMaintenanceAuditLogEntry($con, $b);
            }  
        } else {
            $a = Array(
                'maint_table' => $table_name,
                'maint_recs_affected' => '0',
                'maint_comments' => "No records purged from '$table_name' table due to possible SQL error"
            );
            $b = json_encode($a);
            insertMaintenanceAuditLogEntry($con, $b);

        }
    } else {
    //					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
} else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}

# ---------------------------------------------------------------------	  
# downloads TBL #
# Delete expired downloads from table
#
# ---------------------------------------------------------------------

$sql = "SELECT COUNT(*) as numRowsToDelete from downloads where expired = 1";
$sql1 = "DELETE from downloads where expired = 1";
$table_name = "downloads";

if ($stmt = mysqli_prepare($con, $sql)) {
if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    // Check number of rows in the result set
    if (mysqli_num_rows($result) > 0) {
        $maint_recs_affected = 0;
        // Check to see if there are records to delete and store the total
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $maint_recs_affected = $row['numRowsToDelete'];
        }
        if ($maint_recs_affected > 0) {
            if ($stmt2 = mysqli_prepare($con, $sql1)) {
                if (mysqli_stmt_execute($stmt2)) {
                    $result = mysqli_stmt_get_result($stmt2);
                    $a = Array(
                        'maint_table' => $table_name,
                        'maint_recs_affected' => $maint_recs_affected,
                        'maint_comments' => "Maintenance for '$table_name' table succeeded"
                    );
                    $b = json_encode($a);
                    insertMaintenanceAuditLogEntry($con, $b);
                } else {
                    $a = Array(
                        'maint_table' => $table_name,
                        'maint_recs_affected' => '0',
                        'maint_comments' => "Maintenance for '$table_name' table failed while running the delete command"
                    );
                    $b = json_encode($a);
                    insertMaintenanceAuditLogEntry($con, $b);
                }
            }
        } else {
            $a = Array(
                'maint_table' => $table_name,
                'maint_recs_affected' => '0',
                'maint_comments' => "No records purged from '$table_name' table."
            );
            $b = json_encode($a);
            insertMaintenanceAuditLogEntry($con, $b);
        }  
    } else {
        $a = Array(
            'maint_table' => $table_name,
            'maint_recs_affected' => '0',
            'maint_comments' => "No records purged from '$table_name' table due to possible SQL error"
        );
        $b = json_encode($a);
        insertMaintenanceAuditLogEntry($con, $b);

    }
} else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}
} else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}
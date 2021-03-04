UPDATE files SET deleted = 1, deleted_date = CURRENT_TIMESTAMP(), audio_file_deleted_date = CURRENT_TIMESTAMP()
WHERE file_id = @file_id;
INSERT INTO act_log (acc_id,username,actPage,act_log_date,activity)
VALUES (@acc_id,"SYSTEM","SYSTEM",CURRENT_TIMESTAMP(),concat('Maintenance script deleted file id ', @file_id, ' with filename ' ,@filename));

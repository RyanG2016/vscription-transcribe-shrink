SELECT filename, file_id, files.acc_id
INTO OUTFILE 'C:/utils/jobs/check_exceeded_retention/csv/deleted_records.csv' 
FIELDS ENCLOSED BY '"' 
TERMINATED BY ',' 
ESCAPED BY '"' 
LINES TERMINATED BY '\r\n'
FROM files LEFT JOIN accounts ON accounts.acc_id = files.acc_id
WHERE
    DATE(text_downloaded_date) < DATE_SUB(CURDATE(), INTERVAL accounts.acc_retention_time DAY)
    AND deleted = 0
    AND audio_file_deleted_date IS NULL


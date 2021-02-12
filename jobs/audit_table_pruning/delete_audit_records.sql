-- To Do: Want to log the amount of records being purged
--Purge audit_logs based on users requirements
DELETE act_log
FROM act_log LEFT JOIN accounts ON accounts.acc_id = act_log.acc_id
WHERE
    DATE(act_log_date) < DATE_SUB(CURDATE(), INTERVAL accounts.act_log_retention_time DAY);

---Purge logins past 180 days which is our retention policy for logins. This also includes manually adding trial minutes to STT accounts
DELETE
FROM act_log
WHERE
    DATE(act_log_date) < DATE_SUB(CURDATE(), INTERVAL 180 DAY)
    AND acc_id = 0;
# ADDING maintenance table
# Not in use now
#Last update: 12FEB2021
# ---------------------------

#create table maintenance_log
#(
#	maint_id int auto_increment,
#	maint_table varchar(250) null,
#	maint_count int default 0 null,
#	timestamp TIMESTAMP default CURRENT_TIMESTAMP null,
#	constraint maintenance_log_pk
#		primary key (maint_id)
#);


# ---------------------------------------------------------------------	  
# conversion TBL #
# Delete completed conversions
#
# INPUT: status = 1
# ---------------------------------------------------------------------

DELETE FROM conversion WHERE status = 1;

# ---------------------------------------------------------------------	  
# downloads TBL #
# Delete expired downloads
#
# INPUT: expired = 1
# ---------------------------------------------------------------------
DELETE FROM downloads WHERE `expired` = 1;


# ---------------------------------------------------------------------	  
# tokens TBL #
# Delete tokens older than 7 days
# Delete tokens with used = 1
#
# INPUT: older than x
# ---------------------------------------------------------------------

DELETE FROM tokens WHERE time <= NOW() - INTERVAL 7 day;
DELETE FROM tokens WHERE used = 1;

# ---------------------------------------------------------------------	  
# act_log TBL #
#
# 1.Delete logs past retention time EXCEPT login and logout logs 
#		as these has no ref for acc_id 
#
# 2.Delete logs with acc_id = 0 (login logout) older than 180 days
#
# DEPENDABILITY: 
#		accounts.act_log_retention_time		 
#
# INPUT: older than x (interval 1 month)
# ---------------------------------------------------------------------

#1
DELETE act_log
FROM act_log LEFT JOIN accounts ON accounts.acc_id = act_log.acc_id
WHERE
    DATE(act_log_date) < DATE_SUB(CURDATE(), INTERVAL accounts.act_log_retention_time DAY);

#2
DELETE
FROM act_log
WHERE
    DATE(act_log_date) < DATE_SUB(CURDATE(), INTERVAL 180 DAY)
    AND acc_id = 0;


# ADDING maintenance table
# ---------------------------

create table maintenance_log
(
	maint_id int auto_increment,
	maint_table varchar(250) null,
	maint_count int default 0 null,
	timestamp TIMESTAMP default CURRENT_TIMESTAMP null,
	constraint maintenance_log_pk
		primary key (maint_id)
);


# maintenance Commands
# Order IS IMPORTANT

# ---------------------------------------------------------------------	  
# payments TBL #
# Delete payment records older than 1 month excluding last user payment
# INPUT: older than x (interval 1 month)
# ---------------------------------------------------------------------

delete from payments
where
      payment_id in (
        select payment_id
        from payments l
        where payment_id NOT IN (select
                                     max(payment_id) as latest
                                 from payments
                                 group by user_id)
          AND
                timestamp < DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)
        );

# ---------------------------------------------------------------------	  
# sr_queue TBL #
# Deletes Logs older than 1 month
#
# INPUT: older than x
# ---------------------------------------------------------------------

<@ryan please insert your maint. statement here>


# ---------------------------------------------------------------------	  
# sr_log TBL #
# Deletes Logs older than 1 month
# DEPENDABILITY: Must match sr_queue delete time
#				 sr_queue must be pruned first
#
# INPUT: older than x (interval 1 month)
# ---------------------------------------------------------------------
DELETE from sr_log where srlog_timestamp < (DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH));


# ---------------------------------------------------------------------	  
# tokens TBL #
# Delete tokens older than 7 days
# Delete tokens with used = 1
#
# INPUT: older than x
# ---------------------------------------------------------------------

DELETE from tokens where time < (DATE_SUB(CURRENT_DATE, INTERVAL 1 WEEK));
delete from tokens where used = 1;


# ---------------------------------------------------------------------	  
# act_log TBL #
#
# 1.Delete logs past retention time EXCEPT login and logout logs 
#		as these has no ref for acc_id 
#
# 2.Delete logs with acc_id = 0 (login logout) older than 2 months
#
# DEPENDABILITY: 
#		accounts.act_log_retention_time		 
#
# INPUT: older than x (interval 1 month)
# ---------------------------------------------------------------------

# 1
delete from act_log
where act_log_id in (select
                         act_log_id
                     from
                         act_log log
                             inner join
                         accounts a on log.acc_id = a.acc_id
                     where act_log_date < DATE_SUB(CURRENT_DATE, INTERVAL a.act_log_retention_time DAY))

# 2
DELETE from
		 act_log
	where
		  acc_id not in (select accounts.acc_id from accounts)
	AND
		  act_log_date < DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)


# Template
# ---------------------------------------------------------------------	  
#  TBL #
# 
# DEPENDABILITY: 
#				 
#
# INPUT: older than x (interval 1 month)
# ---------------------------------------------------------------------
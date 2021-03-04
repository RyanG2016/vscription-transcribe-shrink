@echo off
REM Last update 11FEB2021
REM You should create an SQL user called sys_maint with rights to the tables needed as well as global FILE rights.

set Log=C:\utils\jobs\audit_table_pruning\logs\AuditPruningReport.log
echo *************************** >> %Log%
echo ***Audit Log Maintenance Job started on %date% at %time% by %UserName% *** >> %Log%
echo *************************** >> %Log%


REM First mark the records as deleted in the DB and update the record_deleted date

echo ---------- >> %Log%
echo Running SQL script to check for audit records exceeding retention time and delete them >> %Log%
echo ---------- >> %Log%
REM The path to mysql.exe and root password needs to be changed per server.
C:\"Program Files"\"MariaDB 10.5"\bin\mysql.exe -u sys_maint -pTEST vtexvsi_transcribe < C:\utils\jobs\audit_table_pruning\delete_audit_records.sql  1>>%Log%  2>>&1
echo. >> %Log%

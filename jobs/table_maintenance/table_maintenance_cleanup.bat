@echo off
REM Last update 11FEB2021
REM You should create an SQL user called sys_maint with rights to the tables needed as well as global FILE rights.

set Log=C:\utils\jobs\table_maintenance\logs\SQLMaintenanceReport.log
echo *************************** >> %Log%
echo ***SQL Maintenance Job started on %date% at %time% by %UserName% *** >> %Log%
echo *************************** >> %Log%


REM First mark the records as deleted in the DB and update the record_deleted date

echo ---------- >> %Log%
echo Running SQL script to purge old maintenance files: tokens, conversion queue, downloads, sr queue, lockout tokens >> %Log%
echo ---------- >> %Log%
REM The path to mysql.exe and root password needs to be changed per server.
C:\"Program Files"\"MariaDB 10.5"\bin\mysql.exe -u sys_maint -pTEST vtexvsi_transcribe < C:\utils\jobs\table_maintenance\maintenance_records_purge.sql  1>>%Log%  2>>&1
echo. >> %Log%

echo *************************** >> %Log%
echo ***SQL Maintenance Job ended on %date% at %time% by %UserName% *** >> %Log%
echo *************************** >> %Log%
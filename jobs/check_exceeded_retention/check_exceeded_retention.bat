@echo off

REM Last update 27NOV2020
REM You should create an SQL user called sys_maint with rights to the tables needed as well as global FILE rights.

set Log=C:\utils\jobs\check_exceeded_retention\logs\Report.log
echo *************************** >> %Log%
echo ***Maintenance Job started on %date% at %time% by %UserName% *** >> %Log%
echo *************************** >> %Log%

REM If there an existing deleted_records.csv file, the previous batch failed so let's delete it
echo ---------- >> %Log%
echo Deleting any previous deleted_records.csv files >> %Log%
echo ---------- >> %Log%
IF EXIST C:\utils\jobs\check_exceeded_retention\csv\deleted_records.csv (
	DEL /F C:\utils\jobs\check_exceeded_retention\csv\deleted_records.csv
	echo Deleted existing deleted_records.csv file %date% at %time% by %UserName% >> %Log%
) ELSE (
	echo File deleted_records.csv doesn't exist and doesn't need to be deleted %date% at %time% by %UserName% >> %Log%
)

REM First mark the records as deleted in the DB and update the record_deleted date

echo ---------- >> %Log%
echo Running SQL script to check for records exceeding retention time and mark them as deleted as well as clear out any text data in the record >> %Log%
echo ---------- >> %Log%
REM The path to mysql.exe and root password needs to be changed per server.
C:\MAMP\bin\mysql\bin\mysql.exe -u sys_maint -psys_maint vtexvsi_transcribe < C:\utils\jobs\check_exceeded_retention\check_exceeded_retention.sql  1>%Log  2>&1
echo. >> %Log%

REM Now lets go through the csv and move the audio file to the deletedUploads folder and update the audiofile_deleted datetime

echo ---------- >> %Log%
echo Now we're going to go through and attempt to move audio files marked as deleted if applicable in the previous step >> %Log%
echo ---------- >> %Log%

IF EXIST C:\utils\jobs\check_exceeded_retention\csv\deleted_records.csv (
set inputfile=C:\utils\jobs\check_exceeded_retention\csv\deleted_records.csv

REM The following paths needs to be updated for the production server or as needed
set audio_src_folder=C:\apache24\htdocs\vscription-transcribe-temp\uploads\
set audio_deleted_folder=C:\apache24\htdocs\vscription-transcribe-temp\deletedUploads\

echo Changing to the audio files directory %audio_src_folder% >> %Log%
cd %audio_src_folder%
FOR /F "usebackq tokens=1-4 delims=," %%a IN (%inputfile%) DO (
   echo %audio_src_folder%%%a >> %Log%
   IF EXIST %%a (
	move /Y %%a %audio_deleted_folder%
	echo Audio File %audio_src_folder%%%a for file_id %%b deleted successfully >> %Log% 
	echo set @file_id=%%b; > C:\utils\jobs\check_exceeded_retention\temp.sql
	echo set @filename=%%a; >> C:\utils\jobs\check_exceeded_retention\temp.sql
	echo set @acc_id=%%c; >> C:\utils\jobs\check_exceeded_retention\temp.sql
	type C:\utils\jobs\check_exceeded_retention\check_exceeded_retention_audit.sql >> C:\utils\jobs\check_exceeded_retention\temp.sql
	type C:\utils\jobs\check_exceeded_retention\temp.sql
	C:\MAMP\bin\mysql\bin\mysql.exe -u sys_maint -psys_maint vtexvsi_transcribe < C:\utils\jobs\check_exceeded_retention\temp.sql  1>%Log  2>&1
	echo. >> %Log%
	echo Inserted audit log record >> %Log%
	DEL /F C:\utils\jobs\check_exceeded_retention\temp.sql
	) ELSE (
	echo Something went wrong. The file to delete %audio_src_folder%%a% doesn't exist. %date% at %time% by %UserName% >> %Log%
	)
)
) ELSE (
echo No files found marked for deletion >> %Log%
)

REM Now that we're done, let's delete the deleted_records.csv file

echo ---------- >> %Log%
echo All done. Last thing is we'll delete the deleted_records.csv file if it exists >> %Log%
echo ---------- >> %Log%
IF EXIST C:\utils\jobs\check_exceeded_retention\csv\deleted_records.csv (
	REM DEL /F C:\utils\jobs\check_exceeded_retention\csv\deleted_records.csv
	echo Deleted existing deleted_records.csv file %date% at %time% by %UserName% >> %Log%
)
echo *************************** >> %Log%
echo ***Maintenance Job ended on %date% at %time% by %UserName% *** >> %Log%
echo *************************** >> %Log%
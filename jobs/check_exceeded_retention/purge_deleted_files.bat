@echo off
REM This will delete all files from this folder. The backup should protect us further
set deletedFolderPath=C:\apache24\htdocs\vscription-transcribe-temp\deletedUploads\
cd %deletedFolderPath%
del /F /Q *.*

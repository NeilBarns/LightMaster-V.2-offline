@echo off
:: Set the path to the backup directory
set BACKUP_DIR="C:\Users\Neil Michael Barnedo\projects\Automated lights\autolights\database\backup"

:: Set the date format for the backup file (e.g., 2024-08-23)
set DATE=%date:~-4%-%date:~4,2%-%date:~7,2%

:: Set MySQL login details
set MYSQL_USER=lm
set MYSQL_PASSWORD=L1ghtM@st3r@2024
set MYSQL_DATABASE=LM-1.0-KAP

:: Set the path to mysqldump
set MYSQLDUMP_PATH=C:\xampp\mysql\bin\mysqldump.exe

:: Check if backup directory exists
if not exist %BACKUP_DIR% (
    echo Backup directory does not exist. Creating directory...
    mkdir %BACKUP_DIR%
)

:: Backup database and log any errors
echo Backing up database...
%MYSQLDUMP_PATH% -u %MYSQL_USER% -p%MYSQL_PASSWORD% %MYSQL_DATABASE% > %BACKUP_DIR%\%MYSQL_DATABASE%_backup_%DATE%.sql 2> %BACKUP_DIR%\backup_error.log

:: Check if backup was successful
if exist %BACKUP_DIR%\%MYSQL_DATABASE%_backup_%DATE%.sql (
    echo Backup successful!
) else (
    echo Backup failed! Check backup_error.log for details.
)

:: Optional: Delete backups older than 7 days
forfiles -p %BACKUP_DIR% -s -m *.sql -d -30 -c "cmd /c del @path"

echo Backup process completed!
exit

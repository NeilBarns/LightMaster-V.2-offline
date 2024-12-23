@echo off
::Check Apache service

sc query Apache2.4|find "RUNNING">nul
if not %errorlevel%==0 (
echo Apache is not running, attempting to start...
net start Apache2.4
if %errorlevel%==0 (
echo Apache started successfully
)else (
echo Failed to start Apache
)
)else (
echo Apache is running
)

::Check MySQL service

sc query mysql|find "RUNNING">nul
if not %errorlevel%==0 (
echo MySQL is not running, attempting to start...
net start mysql
if %errorlevel%==0 (
echo MySQL started successfully
)else (
echo Failed to start MySQL
)
)else (
echo MySQL is running
)
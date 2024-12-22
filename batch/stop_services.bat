@echo off
rem Gracefully stop MySQL
echo Stopping MySQL...
mysqladmin -u root -pL1ghtM@st3r@2024 shutdown
timeout /t 5 > nul

rem Gracefully stop Apache
echo Stopping Apache...
sc stop "Apache2.4"
timeout /t 5 > nul

echo Apache and MySQL services stopped.
exit
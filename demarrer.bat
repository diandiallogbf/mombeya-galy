@echo off
rem Lance le site Mombeya Galy en local (port 8010 : le port 8000 est utilise par un autre projet)
cd /d "%~dp0"
echo.
echo  Mombeya Galy demarre sur http://127.0.0.1:8010
echo  Administration : http://127.0.0.1:8010/admin
echo  (fermez cette fenetre pour arreter le site)
echo.
start "" cmd /c "timeout /t 3 >nul & start http://127.0.0.1:8010"
php artisan serve --host=127.0.0.1 --port=8010

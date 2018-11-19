@echo off

REM Variables d'environnement pour le mode développement
set ENV=PROD

REM Démarrage du serveur web de PHP sur le port 8000
start cmd.exe /k "php.exe -S localhost:8000 -t "%~dp0/../web/""
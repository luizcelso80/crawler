@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../vendor/nesbot/carbon/bin/upgrade-carbon
php "%BIN_TARGET%" %*

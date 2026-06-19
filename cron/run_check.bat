@echo off
cd /d "C:\xampp\htdocs\FanAmooz\cron"
"C:\xampp\php\php.exe" check_video_uploads.php >> "C:\xampp\htdocs\FanAmooz\cron\video_check.log" 2>&1
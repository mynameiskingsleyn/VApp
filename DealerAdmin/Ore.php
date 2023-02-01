<?php
$pre_dir = '/var/www/html/releases/';
$post_dir = '/artisan';
$LOGFILE="/var/www/html/OreShellCron.logs";

$current = scandir($pre_dir);
$cpath = end($current);
$cron_path = $pre_dir.$cpath.$post_dir;


exec('/usr/bin/php  '.$cron_path.'  schedule:run >> /var/www/html/OreShellCron.logs 2>&1');


?>

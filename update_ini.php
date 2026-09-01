<?php
$file = 'C:/xampp/mysql/bin/my.ini';
$c = file_get_contents($file);
$c = preg_replace('/^innodb_force_recovery.*?$/m', '', $c);
file_put_contents($file, $c);
echo "Done.";

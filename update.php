<?php
$file = 'C:/xampp/apache/conf/httpd.conf';
$c = file_get_contents($file);
$c = str_replace('DocumentRoot "C:/xampp/htdocs"', 'DocumentRoot "C:/xampp/htdocs/dashboard/public"', $c);
$c = str_replace('<Directory "C:/xampp/htdocs">', '<Directory "C:/xampp/htdocs/dashboard/public">', $c);
file_put_contents($file, $c);
echo "Done.";

<?php

$file_url = 'http://www.susancarrier.com/Readings by Charmaine for Robbie Williams.pdf';

header('Content-Type: application/octet-stream');

header("Content-Transfer-Encoding: Binary"); 

header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 

readfile($file_url);

?>
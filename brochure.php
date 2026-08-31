<?php

$file_name = 'charmaines_psychic.pdf';
$file_path = 'brochure.pdf';

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"" . $file_name . "\""); 

if (file_exists($file_path)) {
    readfile($file_path);
} else {
    readfile('https://www.susancarrier.com/brochure.pdf');
}
?>
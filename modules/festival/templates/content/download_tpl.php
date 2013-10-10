<?php
//Create header to force download
header('Content-Description: File Transfer');
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename=' . basename($oggFile));
readfile($oggFile);
?>
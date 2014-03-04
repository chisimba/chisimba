<?php
// This page does not output text, but a binary file - it calls the BLOB class for this.
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="Chat_log.html"');
echo $templateContent
?>;
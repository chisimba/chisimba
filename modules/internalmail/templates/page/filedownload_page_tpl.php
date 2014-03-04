<?php
// This page does not output text, but a binary file - it calls the BLOB class for this.
$attachId = $this->getParam('attachId');
$this->emailFiles->outputFile($attachId);
?>
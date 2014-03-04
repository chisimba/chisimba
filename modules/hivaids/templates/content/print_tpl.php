<?php
/**
* Template to display the print friendly page
* @access public
*/

$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);

echo '<div style="padding: 10px;">'.$display.'<br /></div>';

?>
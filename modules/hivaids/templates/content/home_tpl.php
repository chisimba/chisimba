<?php
/**
* Template to display the registration page
* @access public
*/

if(isset($suppressLayout) && $suppressLayout){
    $this->setVar('pageSuppressBanner', TRUE);
    $this->setVar('pageSuppressToolbar', TRUE);
    $this->setVar('suppressFooter', TRUE);
}else if(isset($suppressLeft) && $suppressLeft){
    // do nothing - no layout template
    $objLayer = $this->newObject('layer', 'htmlelements');
    $objLayer->str = $display;
    $objLayer->padding = '10px';
    $display = $objLayer->show();
}else{
    $this->setLayoutTemplate('hivaids_layout_tpl.php');
}

echo $display.'<br />';

?>
<?php
$table = & $this->newObject('htmltable', 'htmlelements');
$link = & $this->newObject('link', 'htmlelements');


$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1;
$this->objH->str=$this->objLanguage->languageText('mod_contextadmin_importcontent','contextadmin');

if(isset($error)){
    $table->width = '100%';
    
    $table->startRow();
    $table->addCell($this->objH->show());
    $table->endRow();
    
    $table->startRow();
    $table->addCell('<br>');
    $table->endRow();
    
    $this->objH->type=3;
    $this->objH->str=$this->objLanguage->languageText('mod_contextadmin_importfail','contextadmin');


    $table->startRow();
    $table->addCell($this->objH->show());
    $table->endRow();
    
    /*
    $table->startRow();
    $table->addCell('<br>');
    $table->endRow();
    
    $link->href = $this->uri(array('action' => 'content'), 'context');
    $link->link = $this->objLanguage->languageText('mod_contextadmin_viewcontent');
    
    $table->startRow();
    $table->addCell($link->show().'<br>');
    $table->endRow();
    */
    $table->startRow();
    $table->addCell('<br>');
    $table->endRow();
    
    $link->href = $this->uri(array('action' => 'courseadmin'), 'contextadmin');
    $link->link = $this->objLanguage->languageText('mod_contextadmin_returnadmin','contextadmin');
    
    $table->startRow();
    $table->addCell($link->show().'<br>');
    $table->endRow();
    
    print $table->show();
} else {   
    
    $table->width = '100%';
    
    $table->startRow();
    $table->addCell($this->objH->show());
    $table->endRow();
    
    $table->startRow();
    $table->addCell('<br>');
    $table->endRow();
    
    $this->objH->type=3;
    $this->objH->str=$this->objLanguage->languageText('mod_contextadmin_importsuccess','contextadmin');


    $table->startRow();
    $table->addCell($this->objH->show());
    $table->endRow();
    
    
    $table->startRow();
    $table->addCell('<br>');
    $table->endRow();
    
    $link->href = $this->uri(array('action' => 'content'), 'context');
    $link->link = $this->objLanguage->languageText('mod_contextadmin_viewcontent','contextadmin');
    
    $table->startRow();
    $table->addCell($link->show().'<br>');
    $table->endRow();
    
    $table->startRow();
    $table->addCell('<br>');
    $table->endRow();
    
    $link->href = $this->uri(array('action' => 'courseadmin'), 'contextadmin');
    $link->link = $this->objLanguage->languageText('mod_contextadmin_returnadmin','contextadmin');
    
    $table->startRow();
    $table->addCell($link->show().'<br>');
    $table->endRow();
    
    print $table->show();
    //print 'Import Successful';
}

?>
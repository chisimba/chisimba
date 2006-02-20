<?php
$table=&$this->newObject('htmltable','htmlelements');
$form=&$this->newObject('form','htmlelements');
$form->name='modulelist';
$form->setAction($this->uri(array('action'=>'savemodules'),'contextadmin'));
$form->setDisplayType(3);

$button=&$this->newObject('navbuttons','navigation');

$objIcon=&$this->newObject('geticon','htmlelements');
$objIcon->setModuleIcon("modulelist");
$objIcon->alt=$this->objLanguage->languageText("mod_context_unregisteredfeature");
$objIcon->align = "absmiddle";

$key=$objIcon->show();
if (isset($savedTime))
{
	$saved=' <span class="confirm">'.$this->objLanguage->languageText("mod_context_succsave").'&nbsp;'.$savedTime.'</span>';
}
else
{
	$saved='';
}
if ($this->objDBContext->getContextCode()) {    
    
    $objIcon->setIcon("home");
    $objIcon->alt=$this->objLanguage->languageText("word_course").' '.$this->objLanguage->languageText("word_home");
    $objIcon->align = "absmiddle";
            
    $coursehome='&nbsp;<a href="'.$this->URI(null,'context').'">';
    $coursehome.=$objIcon->show();
    $coursehome.='</a>&nbsp;';
}else{
    $coursehome='&nbsp;';
}


  $heading='<center><h2>'.$this->objLanguage->languageText("mod_context_confplugins").'&nbsp;'.$this->objDBContext->getTitle().'</h2>';
  $heading .= $saved.'</center>';
  
  
$table->addHeader(array(
  $this->objLanguage->languageText("mod_context_Learning"),  
  $this->objLanguage->languageText("mod_context_communicate"),
  $this->objLanguage->languageText("mod_contextadmin_organizors"),
  '&nbsp;'), "heading");
  
  $table->addRow(array($this->makeModuleItem('asstest',$this->objLanguage->languageText("mod_contextadmin_asstest")),      
                             $this->makeModuleItem('chat',$this->objLanguage->languageText("mod_contextadmin_chat")),
                             $this->makeModuleItem('faq',$this->objLanguage->languageText("mod_contextadmin_faq"))), 'odd');
  
  
  $table->addRow(array($this->makeModuleItem('marks',$this->objLanguage->languageText("mod_contextadmin_marks")),  
                             $this->makeModuleItem('forum',$this->objLanguage->languageText("mod_contextadmin_forum")),
                             $this->makeModuleItem('whatsnew',$this->objLanguage->languageText("whatsnew"))), 'even');
 
 $table->addRow(array($this->makeModuleItem('inbasket',$this->objLanguage->languageText("mod_contextadmin_inbasket")), 
                             null,
                             $this->makeModuleItem('calendar',$this->objLanguage->languageText("mod_contextadmin_diary"))), 'odd');
 
 $table->addRow(array($this->makeModuleItem('freemind',$this->objLanguage->languageText("mod_freemind_mindmapping")), 
                             null,
                             '&nbsp;'), 'even');
 $table->addRow(array($this->makeModuleItem('glossary',$this->objLanguage->languageText("mod_glossary_name")),
                             '&nbsp;','&nbsp;'),'odd');
 
 $table->addRow(array($this->makeModuleItem('rubric',$this->objLanguage->languageText("mod_contextadmin_rubrics")),
                         '&nbsp;', '&nbsp;'),'even');
 
 $table->addRow(array('&nbsp;','&nbsp;', '&nbsp;'),'odd');
 
 $table->addRow(array($coursehome, 
                             $button->putSaveButton(),
                             $key.'<span class="warning"> ('.$this->objLanguage->languageText("mod_contextadmin_unregisteredfeature").')</span>'), 'even');
 $form->addToForm($heading);
 $form->addToForm($table);
 
echo $form->show();

?>
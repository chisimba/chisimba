<?php

//add step 3

//add step 1 template
$objH = & $this->newObject('htmlheading','htmlelements');
$objForm = & $this->newObject('form','htmlelements');

$inpContextCode =  & $this->newObject('textinput','htmlelements');
$inpMenuText = & $this->newObject('textinput','htmlelements');

$inpButton =  $this->newObject('button','htmlelements');

$objH->str = $this->_objLanguage->languageText("mod_context_step","context").' 3: '.$this->_objLanguage->code2Txt("mod_contextadmin_selectcontextplugins",'contextadmin',array('context'=>'Course'));
$objH->type = 3;

//setup the form
$objForm->name = 'addfrm';
$objForm->action = $this->uri(array('action' => 'savestep3'));
$objForm->extra = 'class="f-wrap-1"';
$objForm->displayType = 3;

$inpAbout->name = 'about';
$inpAbout->id = 'about';
$inpAbout->value = '';
$inpAbout->cols = 4;
$inpAbout->rows = 3;


$inpButton->setToSubmit();
$inpButton->cssClass = 'f-submit';
$inpButton->value = $this->_objLanguage->languageText("word_next");


//validation
//$objForm->addRule('about','About is a required field!', 'required');


//$objForm->addToForm('<div class="req"><b>*</b> Indicates required field</div>');

$objForm->addToForm('<fieldset>');
$objForm->addToForm($objH->show());
$objForm->addToForm('<div id="resultslist-wrap"><ol>');

$objModuleFile = & $this->newObject('modulefile', 'modulecatalogue');
$objModules = & $this->newObject('modules', 'modulecatalogue');
$arrModules = $objModules->getModules(2);


foreach ($arrModules as $module)
{
    if($objModuleFile->contextPlugin($module['module_id']))
    {
        $checkbox = $this->newObject('checkbox', 'htmlelements');
        $checkbox->value=$module['module_id'];
        $checkbox->cssId = 'mod_'.$module['module_id'];
        $checkbox->name = 'mod_'.$module['module_id'];
        $checkbox->cssClass = 'f-checkbox';
        
        $icon = $this->newObject('geticon', 'htmlelements');
        $icon->setModuleIcon($module['module_id']);
        
        $objForm->addToForm('<ul><dt>'.$checkbox->show().'&nbsp;'.$icon->show().'&nbsp;'.ucwords($this->_objLanguage->code2Txt('mod_'.$module['module_id'].'_name',$module['module_id'],array('context' => 'Course'))).'</dt>');
        $objForm->addToForm('<dd  class="desc">'.$module['description'].'</dd>');
        $objForm->addToForm('</ul>');
    }

}
$objForm->addToForm('</ol></div><div class="f-submit-wrap">'.$inpButton->show().'</div></fieldset>');
print $objForm->show().'<br/>';

?>
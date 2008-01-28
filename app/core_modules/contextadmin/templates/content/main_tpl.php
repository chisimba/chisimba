<?php


// Load Inner classes.
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$this->_objLanguage = $this->getObject('language', 'language');

$tabBox = $this->newObject('tabpane', 'htmlelements');
$featureBox = $this->newObject('featurebox', 'navigation');
$objLink =  new link();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$icon =  $this->newObject('geticon', 'htmlelements');
$objContextGroups = $this->newObject('onlinecount', 'contextgroups');
$objH = new htmlheading();
$objH->str = ucwords($objLanguage->code2Txt('mod_contextadmin_contextadmin', 'contextadmin')).' '.$icon->getAddIcon($this->uri(array('action' => 'addstep1')));
$objH->type = 1;
echo $objH->show();

$str = '';
$other = '';
$lects = '';
$conf = '';
$heading = '';

//registered courses
if (isset($contextList))
{	
    foreach ($contextList as $context)
    {
        
        $lecturers = $this->_objUtils->getContextLecturers($context['contextcode']);
        $lects = '';
        if(is_array($lecturers))
        {
            $c = 0;
            foreach($lecturers as $lecturer)
            {
                $c++;
                $lects .= $this->_objUser->fullname($lecturer['userid']);
                $lects .= ($c < count($lecturers)) ? ', ' : '';
                
                
            }
        } else {
            $lects = 'No Instructor for this course';
        }
        //print_r($objContextGroups->getUserCount($context['contextcode']));
        $userCount = $objContextGroups->getUserCount($context['contextcode'])+1;
        $content = $this->_objLanguage->languageText("mod_context_instructors",'context') .': <span class="highlight">'.$lects.'</span>';
        $content .= '<p>'.$this->_objLanguage->languageText("mod_context_status",'context') .' : <span class="highlight">'.$context['status'].'</span>';
        $content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$this->_objLanguage->languageText("mod_context_access",'context') .' : <span class="highlight">'.$context['access'].'</span>';
        $content .= '<br/>'.$this->_objLanguage->languageText("mod_context_lastupdated",'context') .'  : <span class="highlight">'.$context['updated'].'</span>';
        $content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$this->_objLanguage->languageText("mod_context_lastupdatedby",'context') .' : <span class="highlight">'.$this->_objUser->fullname($context['lastupdatedby']).'</span>';
        $content .= '<br/>'.$this->_objLanguage->languageText("mod_context_noregusers",'context') .': <span class="highlight">'.$userCount.'</span></p>';
        $content .= '<p>'.$this->_objUtils->getPlugins($context['contextcode']).'</p>';

        
        //administer context
        //enter context
        $icon = $this->newObject('geticon', 'htmlelements');
        
        //enter context
        $objLink->href = $this->uri(array('action' => 'joincontext', 'contextcode' => $context['contextcode']), 'context');
        $objLink->link = $context['title'];
        
        //edit context
        //$conf .= '  '.$icon->getEditIcon($this->uri(array('action' => 'addstep1', 'mode' => 'edit', 'contextcode' =>$context['contextcode'] ), 'contextadmin'));
                
        //delete context
        $conf = '  '.$icon->getDeleteIconWithConfirm($context['contextcode'], array('action' => 'delete', 'contextcode' =>$context['contextcode']), 'contextadmin');
        
        
        $title = ($context['title'] == '') ? $context['menutext'] : $context['title'];
        /*if($context['contextcode'] == $this->_objDBContext->getContextCode())
        {
              $heading = preg_replace('/_.* /', '', $context['contextcode']);
            $other .= '&nbsp;'.$featureBox->show($heading .' - '.$title.'   '.$conf, $content ).'<hr />';
        } else {*/
            $str .= '&nbsp;'.$featureBox->show($objLink->show().' ('.$context['contextcode'].')   '.$conf, $content ).'<hr />';
        //}
        
        //$icon = null;
    }
} else {
    $str .= '<div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">'.$this->_objLanguage->languageText("mod_context_noasscontext",'context') .'</div>';
}


// Turned Off by Tohir - Use Control Panel Instead
/*
if ($this->_objDBContext->isInContext()) {
    $other .= $this->_objUtils->getContextAdminToolBox();
    $tabBox->addTab(array('name'=> $this->_objDBContext->getTitle(). ' Admin','content' => $other));	
}

$tabBox->addTab(array('name'=>ucwords($this->_objLanguage->code2Txt('mod_contextadmin_mycontext','contextadmin',array('contexts' => 'Courses'))),'content' => $str));
*/
echo $str;




?>

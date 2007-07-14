<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$tabBox = $this->newObject('tabpane', 'htmlelements');
$featureBox = $this->newObject('featurebox', 'navigation');
$objLink =  new link();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$icon =  $this->newObject('geticon', 'htmlelements');
$objContextGroups = $this->newObject('onlinecount', 'contextgroups');
$objH = new htmlheading();
$objH->str = ucwords($objLanguage->code2Txt('mod_contextadmin_contextadmin', 'contextadmin')).' '.$icon->getAddIcon($this->uri(array('action' => 'addstep1')));
$objH->type = 3;
echo $objH->show();


 	$form = $this->objIEUtils->uploadTemplate();
	$tabBox->addTab(array('name'=> "Import",'content' => $form->show()));
// 	$form = $this->objIEUtils->downloadTemplate();
//	$tabBox->addTab(array('name'=> "Export",'content' => $form->show()));


$str = '';
$other = '';
$lects = '';
$conf = '';

$currentContextCode = $this->_objDBContext->getContextCode();

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
		$objLink->href = $this->uri(array('action' => 'admincontext', 'contextcode' => $context['contextcode']), 'contextadmin');
		$icon->setModuleIcon('contextadmin');
		$icon->alt = 'Administer Course';
		$objLink->link = $icon->show();
		$conf = $objLink->show();
		
		//enter context
		$objLink->href = $this->uri(array('action' => 'joincontext', 'contextcode' => $context['contextcode']), 'context');
		$icon->setIcon('entercourse');
		$icon->alt = 'Enter Course';
		$objLink->link = $icon->show();
		$conf .= $objLink->show();
		
		//edit context
		//$conf .= '  '.$icon->getEditIcon($this->uri(array('action' => 'addstep1', 'mode' => 'edit', 'contextcode' =>$context['contextcode'] ), 'contextadmin'));
				
		//delete context
		$conf .= '  '.$icon->getDeleteIcon($this->uri(array('action' => 'delete', 'contextcode' =>$context['contextcode']), 'contextadmin'));
		
		
		$title = ($context['title'] == '') ? $context['menutext'] : $context['title'];
		if($context['contextcode'] == $this->_objDBContext->getContextCode())
		{
		      
			$other .= '&nbsp;'.$featureBox->show($context['contextcode'] .' - '.$title.'   '.$conf, $content ).'<hr />';
		} else {
			$str .= '&nbsp;'.$featureBox->show($context['contextcode'] .' - '.$title.'   '.$conf, $content ).'<hr />';
		}
		
		$icon = null;
	}
} else {
	$str .= '<div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">'.$this->_objLanguage->languageText("mod_context_noasscontext",'context') .'</div>';
}



if ($this->_objDBContext->isInContext()) {
	$other .= $this->_objUtils->getContextAdminToolBox();
	$tabBox->addTab(array('name'=> $this->_objDBContext->getTitle(). ' Admin','content' => $other));	
}
$tabBox->addTab(array('name'=>ucwords($this->_objLanguage->code2Txt('mod_contextadmin_mycontext','contextadmin',array('contexts' => 'Courses'))),'content' => $str));

//if the user is admin then show him all the other courses as well
if($this->_objUser->isAdmin())
{
	
	$other = $featureBox->show('Browse Courses', $filter);
	
	if(count($otherCourses) > 0)
	{
		
		$table->width = '60%';
		$table->startHeaderRow();
		$table->addHeaderCell($this->_objLanguage->languageText("mod_context_noregusers",'context'));
		$table->addHeaderCell($this->_objLanguage->languageText("word_title"));
		$table->addHeaderCell($this->_objLanguage->languageText("mod_context_details",'context'));
		$table->addHeaderCell('&nbsp;');
		$table->endHeaderRow();
		
		$rowcount = 0;
		
		foreach($otherCourses as $context)
		{
			
			$oddOrEven = ($rowcount == 0) ? "even" : "odd";
			$lecturers = $this->_objUtils->getContextLecturers($context['contextcode']);
			$lects = '';
			if(is_array($lecturers))
			{
				foreach($lecturers as $lecturer)
				{
					$lects .= $lecturer['fullname'].', ';
				}
			} else {
				$lects = 'No Instructor for this course';
			}
			
			$content = '<span class="caption">Instructors : '.$lects.'</span>';
			$content .= '<p>'.$context['about'].'</p>';
			$content .= '<p>'.$this->_objUtils->getPlugins($context['contextcode']).'</p>';
			
			
			
			$objLink->href = $this->uri(array('action' => 'joincontext','contextCode'=>$context['contextcode']), 'context');
			$icon->setIcon('leavecourse');
			$icon->alt = 'Enter Course '.$context['title'];
			$objLink->link = $icon->show();
			
			if($this->_objDBContextUtils->canJoin($context['contextcode']))
			{
				$config = $objLink->show();
			} else {
				$icon->setIcon('failed','png');
				$config = $icon->show();
			}
			
			$icon->setIcon('info');
			$icon->alt = '';
			$mes = '';
			$mes .= ($context['access'] != '') ?  'Access : <span class="highlight">'.$context['access'].'</span>' : '' ; 
			$mes .= ($context['startdate'] != '') ? '<br/>Start Date : <span class="highlight">'.$context['startdate'].'</span>'  : '';
			$mes .= ($context['finishdate'] != '') ? '<br/>Finish Date : <span class="highlight">'.$context['finishdate'].'</span>'  : '';
			$mes .= ($lects != '') ? '<br/>Lecturers : <span class="highlight">'.$lects.'</span>'  : '';
			$noStuds = 0;
			$mes .= '<br />No. Registered Students : <span class="highlight">'.$noStuds.'</span>';
			
			$info = $domtt->show(htmlentities($context['title']),$mes,$icon->show());
			$tableRow = array();
			
			$tableRow[] = $context['contextcode'];
			$tableRow[] = $context['title'];
			$tableRow[] = $info;
			$tableRow[] = $config;
			
			$table->addRow($tableRow, $oddOrEven);
			 $rowcount = ($rowcount == 0) ? 1 : 0;
			//$other .= $featureBox->show($context['contextcode'] .' - '.$context['title'].'   '.$objLink->show(), $content ).'<hr />';
		}
			
			$other .='<hr />'.$featureBox->show('Courses', $table->show() );
		}else {
			
			$other .= '<div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">No Public or Open Courses is available</div>';
		}
	
	    if($this->_objUser->isAdmin())
        {
		    //$tabBox->addTab(array('name'=>'All Other Courses','content' => $other));
        }
	
}

echo $tabBox->show();


/*$fieldset->setLegend('My Courses    '.$icon->getAddIcon($this->uri(array('action' => 'addstep1'),'contextadmin')));
$fieldset->addContent($str);
echo $fieldset->show();*/
?>
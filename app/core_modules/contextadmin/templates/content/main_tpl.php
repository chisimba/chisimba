<?php

$tabBox = & $this->newObject('tabpane', 'htmlelements');
$featureBox = & $this->newObject('featurebox', 'navigation');
$objLink =  & $this->newObject('link', 'htmlelements');
$fieldset = & $this->newObject('fieldset', 'htmlelements');
$icon =  & $this->newObject('geticon', 'htmlelements');
$objContextGroups = & $this->newObject('onlinecount', 'contextgroups');


$str = '';
$other = '';
$lects = '';
$conf = '';


//registered courses
if (isset($contextList))
{	
	foreach ($contextList as $context)
	{
		
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
		
		$userCount = count($objContextGroups->getContextUsers($context['contextcode']));
		$content = 'Instructors : <span class="highlight">'.$lects.'</span>';
		$content .= '<p>Status : <span class="highlight">'.$context['status'].'</span>';
		$content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Access : <span class="highlight">'.$context['access'].'</span>';
		$content .= '<br/>Last Updated  : <span class="highlight">'.$context['updated'].'</span>';
		$content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Last Update By : <span class="highlight">'.$this->_objUser->fullname($context['lastupdatedby']).'</span>';
		$content .= '<br/>No. of Registered Users: <span class="highlight">'.$userCount.'</span></p>';
		$content .= '<p>'.$this->_objUtils->getPlugins($context['contextcode']).'</p>';
		
		
		
		//enter context
		$objLink->href = $this->uri(array('action' => 'joincontext', 'contextcode' => $context['contextcode']), 'context');
		$icon->setIcon('leavecourse');
		$icon->alt = 'Enter Course';
		$objLink->link = $icon->show();
		$conf = $objLink->show();
		
		//edit context
		$conf .= '  '.$icon->getEditIcon($this->uri(array('action' => 'addstep1', 'mode' => 'edit', 'contextcode' =>$context['contextcode'] ), 'contextadmin'));
				
		//delete context
		$conf .= '  '.$icon->getDeleteIcon($this->uri(array('action' => 'delete', 'contextcode' =>$context['contextcode']), 'contextadmin'));
		
		//manage context users
		$objLink->href = $this->uri(array('action' => 'manageusers', 'contextcode' => $context['contextcode']), 'context');
		$icon->setIcon('student');
		$icon->alt = 'Manage Course Users';
		$objLink->link = $icon->show();
		$conf .= $objLink->show();
		
		if($context['contextcode'] == $this->_objDBContext->getContextCode())
		{
			$other .= '&nbsp;'.$featureBox->show($context['contextcode'] .' - '.$context['title'].'   ', $content.$conf ).'<hr />';
		} else {
			$str .= '&nbsp;'.$featureBox->show($context['contextcode'] .' - '.$context['title'].'   ', $content.$conf ).'<hr />';
		}
	}
} else {
	$str .= '<div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">No are associated with any courses</div>';
}

if ($this->_objDBContext->isInContext()) {
	$other .= $this->_objUtils->getContextAdminToolBox();
	$tabBox->addTab(array('name'=> $this->_objDBContext->getTitle(). ' Admin','content' => $other));	
}
$tabBox->addTab(array('name'=>'My Courses','content' => $str));
echo $tabBox->show();


/*$fieldset->setLegend('My Courses    '.$icon->getAddIcon($this->uri(array('action' => 'addstep1'),'contextadmin')));
$fieldset->addContent($str);
echo $fieldset->show();*/
?>
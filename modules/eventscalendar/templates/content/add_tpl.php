<?php

// Load HTML Elements
			//$this->loadClass('form', 'htmlelements');
			$this->loadClass('textinput', 'htmlelements');
			$this->loadClass('button', 'htmlelements');
			$this->loadClass('dropdown', 'htmlelements');
			$this->loadClass('htmlheading', 'htmlelements');
			$this->loadClass('checkbox', 'htmlelements');
			$this->loadClass('link', 'htmlelements');
			$this->loadClass('radio', 'htmlelements');
			
//For this recipe I need a form
$objForm = $this->newObject('form', 'htmlelements');// new form('frmone');
//and a editor
$objEditor = & $this->newObject('htmlarea', 'htmlelements');
//maybe a h1 heading
$objH1 = & $this->newObject('htmlheading', 'htmlelements');
//a  text field
$objTextField = new textinput('title');
//the categories dropdown
//$objCatDropdown = new dropdown('eventtype');
//i need a date 
$objDatePicker =  & $this->newObject('datepicker', 'htmlelements');
//a start time 
//$startTime =  & $this->newObject('textinput', 'htmlelements');
// an end time
//$endTime =  & $this->newObject('textinput', 'htmlelements');
//location
//$location =  new textinput('location');
//a button
$button =  new button();//& $this->newObject('button', 'htmlelements');
$button2 = new button();// & $this->newObject('button', 'htmlelements');
//$objFeatureBox
$objFeatureBox =  $this->getObject('featurebox', 'navigation');
//dropdown
//$objDropDown = new dropdown('eventtype');


$mode = $this->getParam('mode');
//check the mode
if($mode == 'edit')
{
    $eventLine = $this->_objDBEventsCalendar->getRow('id', $this->getParam('id'));
    $objTextField->value = $eventLine['title'];
    $objEditor->value = stripslashes($eventLine['description']);
    $objDatePicker->value = $eventLine['event_date'];
    //$startTime->value = $eventLine['start_time'];
    //$endTime->value = $eventLine['end_time'];
    //$location->value =$eventLine['location'];
    $heading = 'Edit Event';
    
} else {
    if($this->getParam('type') == 'context')
    {
        $heading = 'Add Event to '.$this->_objDBContext->getMenuText();
    } else {
        $heading = ' Add Event to My Calendar';
    }
    $objTextField->value = '';
    $objEditor->value = '';
    $objDatePicker->value = mktime ();
    $startTime->value =  mktime();
    $endTime->value = mktime();
    $location->value = '';
}

//$objForm->name = "addevent";
//$objForm->extra = ' class = "f-wrap-1" ';
//$objForm->displayType = 2 ;
//$objForm->action = $this->uri(array('action' => 'saveevent', 'catid' => $this->getParam('catid')));

//the title field

$objTextField->label = 'Title';
$objTextField->size = 70;


//location
$location->name = 'location';
$location->label = 'Location';


//the date picker
$objDatePicker->name = 'start_date';
$objDatePicker->label = 'Date';

//the editor

$objEditor->name = 'description';
$objEditor->label = 'Details';

//the categories
$objCatDropdown->label = 'Category';
//$objCatDropdown->addFromDB($categories,'title','id');

//start time
$startTime->name = 'start_time';
$startTime->label = "Start Time";


//end time
$endTime->name = 'end_time';
$endTime->label = "end Time";



//the button
$button->setToSubmit();
$button->value = "Save";
$button->label = "&nbsp;";
$button->setCSS("f-submit");


$button2->value = "Back";
$button2->label = "&nbsp;";
$button2->setOnClick('javascript:history.back(-1)');

//check if the user is in a context and if he is a
// lecturer in that context to add events to it.
//add a drop dowon for the user to select the type of event this is
if($this->_objDBContext->isInContext())
{   $objContextGroups = & $this->newObject('managegroups', 'contextgroups');
    $contextCode =  $this->_objDBContext->getContextCode();
    //get a list of lecturers for this context
    $arrLecturers = $objContextGroups->contextUsers('Lecturers', $contextCode);
    //check if this user is a lecturer
    
    if($this->_objUser->isAdmin() || in_array($this->_objUser->userId(), $arrLecturers) )
    {
        $objDropDown->name = 'eventtype';
        $objDropDown->label = 'Event Type';
        $objDropDown->extra = ' style="width:200px" ';
        //$objDropDown->addOption($contextCode,'Context: '.$this->_objDBContext->getMenuText());
        //$objDropDown->addOption($this->_objUser->userId(),'Personal');
        if($this->_objUser->isAdmin())
        {
           // $objDropDown->addOption('site1','Site');
        }
        //$objForm->addToForm($objDropDown);
    }
} else {
	    $catId = $this->_objDBCategories->getCatId('user',$this->_objUser->userId());

}


$objForm->name = "addevent";
//$objForm->extra = ' class = "f-wrap-1" ';
$objForm->displayType = 2 ;
$objForm->action = $this->uri(array('action' => 'saveevent', 'catid' => $this->getParam('catid')));

/*
$str = '<select name="catid">';
foreach ($categories as $cat)
{
   $str .= '<option value="'.$cat['id'].'" style="background:'.$cat['colour'].'">&nbsp;'.$cat['title'].'&nbsp;&nbsp;</option>';
  
}
$str .= '</select>';
*/
//$objCatDropdown->addFromDB($categories, 'title','title','Categories');


//add them all to the form
$objForm->addToForm($objTextField);
//$objForm->addToForm($objCatDropdown);
//$objForm->addToForm('Category</td><td>'.$str.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Location '.$location->show());
//$objForm->addToForm($location);

//$objForm->addToForm($objLocation);
$objForm->addToForm($objDatePicker);
//$objForm->addToForm($startTime);
//$objForm->addToForm($endTime);
//$objForm->addToForm('Start Time</td><td>'.$this->_objUtils->getTimeDropDown('startTime'));
//$objForm->addToForm('End Time</td><td>'.$this->_objUtils->getTimeDropDown('endTime'));
$objForm->addToForm($objEditor);
$objForm->addToForm($button->show().$button2->show());

//print "<h1> ".$heading."</h1>";
print $objFeatureBox->show($heading, $objForm->show());

?>

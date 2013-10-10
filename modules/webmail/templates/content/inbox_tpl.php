<?php
//print_r($data);
//die();
$this->objPop= &$this->getObject('windowpop', 'htmlelements');
$this->objPop=&new windowpop;


$userMenu  = &$this->newObject('usermenu','toolbar');
//user cal
$userCal = &$this->newObject('usercalendar','calendar');
// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);

// Add Post login menu to left column
$leftSideColumn ='';
$leftSideColumn = $userMenu->show();
//$rightSideColumn = "Simple calendar plus menu for other folders like calendars etc";
$middleColumn = NULL;

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
// Add Right Column
//$cssLayout->setRightColumnContent($rightSideColumn);

$this->href = $this->getObject('href', 'htmlelements');
$table = $this->newObject('htmltable', 'htmlelements');
$table->border = 1;

$table->attributes="align=\"center\"";
//Create the array for the table header
$tableRow=array();
$tableHd[]="From";
$tableHd[]="Subject";
$tableHd[]="Date";

function etrimstr($s)
{
  $maxlen = 30;
  $str_to_count = html_entity_decode($s);
  if (strlen($str_to_count) <= $maxlen) {
   return htmlentities($s);
  }
  $s2 = substr($str_to_count, 0, $maxlen - 3);
  $s2 .= "...";
  return htmlentities($s2);
}

function fixdate($date)
{
	return $date;
}

$table->addHeader($tableHd, "heading");
//Loop through and display the records
$rowcount = 0;
if (isset($data))
{
    if (count($data) > 0)
    {
        foreach ($data as $line)
        {
        	$oddOrEven = ($rowcount == 0) ? "odd" : "even";
        	$messageid = $line['messageid'];
        	$unseen = $line['read'];
        	$subject = etrimstr($line['subject']);
        	$mess = $this->uri(array('module' => 'webmail', 'action' => 'getmessage', 'msgid' => $messageid));
        	$this->objPop->set('location',$mess);
        	$this->objPop->set('linktext', $subject);
        	$this->objPop->set('width','500');
			$this->objPop->set('height','500');
			$this->objPop->set('left','500');
			$this->objPop->set('top','600');
			$this->objPop->set('scrollbars', 'yes');
			$this->objPop->putJs();
			$linktxt = $this->objPop->show();

			if ($unseen != ' ')
        	{
        		$subject = "<strong>" . $linktxt . "</strong>";
        		$address = "<strong>" . etrimstr($line['address']) . "</strong>";
        		$date = "<strong>" . substr((fixdate($line['date'])),0,16) . "</strong>";
        	}
        	else {
        		$subject = $linktxt; //$this->href->showlink($mess, $line['subject']);
        		$address = etrimstr($line['address']);
        		$date = substr((fixdate($line['date'])),0,16);
        	}

        	$tableRow[]=$address;
        	$tableRow[]= $subject;
			$tableRow[]=$date;
			$table->addRow($tableRow, $oddOrEven);
            $tableRow=array();
            $rowcount = ($rowcount == 0) ? 1 : 0;
        }
    }
}

//add middle column
$cssLayout->setMiddleColumnContent($table->show());

echo $cssLayout->show();

?>
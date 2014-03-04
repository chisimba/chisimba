<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Class to biuld the calendar interface
*
* @author Wesley Nitsckie
* @copyright (c) 2005 University of the Western Cape
* @package eventscalendar
* @version 1
*
*
*/
class calendarbiulder extends object
{

	var $NOW;
   var $curMonth;
   var $curYear;
   var $curtxtMonth;
   var $curDate;
   var $curDay;
   var $thisMonth;
   var $TempMonth;
   var $totalDays;

	/**
	* Constructor
	*/
	public function init()
	{
	    $this->_objConfig = $this->getObject('altconfig', 'config');

		$this->curtxtMonth=array("","January","February","March","April","May","June","July","August","September","October","November","December");
  	    $this->curDay=1;
  	    $this->assignDate(0,0);
  	    $this->appendArrayVar('headerParams','<link href="'.$this->_objConfig->getModuleURI().'/calendarbase/resources/calendarstyle.css" rel="stylesheet" type="text/css"/>');

  	    $this->_objDBEventsCalendar = & $this->newObject('dbeventscalendar', 'eventscalendar');
  	    $this->_objUser = & $this->newObject('user', 'security');
  	    $this->eventsArr = array();//& $this->_objDBEventsCalendar->getEventsByType('user', $this->_objUser->userId(), $this->getParam('month'), $this->getParam('year'));
  	    $this->domTT = & $this->newObject('domtt', 'htmlelements');
	}


	/**
	* Method to show the calendar
	* @return string
	* @access public
	* @param array $arrEvents The events array
	*/
	public function show($type = 'simple', $arrEvents = NULL)
	{
        $this->eventsArr = $arrEvents;
		if($type != 'simple')
		{
			$this->calType = 'big';
			return $this->bigCalendar($arrEvents);
		} else {
			$this->calType = 'simple';
			return $this->simpleCalendar($arrEvents);
		}

	}

	/**
	* Method to biuld a simple calender
	* @return string
	* @access public
	* @param array $arrEvents The events array
	*/
	public function simpleCalendar($arrEvents = NULL)
	{
		try {


			  //the days in the month
			  $daysInMonth = date("t",mktime(23,59,59,$this->curMonth, $this->curDay, $this->curYear));

			  //the week number at the end of the month
			  $weekNo = date("W",mktime(23,59,59,$this->curMonth, $daysInMonth, $this->curYear));

			  //the first day of the week Mon = 0 Tues=1 etc
			  $firstDayOfTheMonth = date("w",mktime(23,59,59,$this->curMonth, 1, $this->curYear));

			  //last day of the month
			  $lastDayOfTheMonth = date("w",mktime(23,59,59,$this->curMonth, 1, $this->curYear));

			  //week number at the beginning of the month
			  $weekNoEndOfMonth = date("W",mktime(23,59,59,$this->curMonth, $this->curDay, $this->curYear));

			  //number of weeks for the month
			  $weekNo = ($weekNo - $weekNoEndOfMonth  ) + 1;
			//  print $weekNoEndOfMonth;

				//the start flag
			  $start = true;

			  //the day counter
			  $dayCounter = 1;

			  //next months days counter
			  $nextMonthsDays = 1;

			  //previous months days
			  $previousMonthsDays = date("t",mktime(23,59,59,$this->curMonth-1, $this->curDay, $this->curYear)) - $firstDayOfTheMonth + 1;


		      $str2 = '<!-- START CALENDAR GENERATETOR --><table class="mainTable" cellspacing="1" cellpadding="0">
						 <tr>

						  <td class="monthYearText monthYearRow" colspan="7" >';
		      $objGetIcon = & $this->newObject('geticon', 'htmlelements');
		      $objGetIcon->setIcon('prev');


		       if($this->curMonth-1 == 0)
			  {
			  	$prevYear =$this->curYear - 1;
			  	$prevMonth = 12;
			  } else {
			  	$prevYear =$this->curYear;
			  	$prevMonth = $this->curMonth-1;
			  	$objGetIcon->alt = date("F",mktime(23,59,59,$this->curMonth-1, $this->curDay, $this->curYear));
			  }

			  $str2 .= '<a href="'. $this->uri(array("action" => "events", "month" => $prevMonth, "year" => $prevYear )) .'"> '.$objGetIcon->show().' </a>';
			  $objGetIcon->setIcon('next');
			  $str2 .= date("M", mktime(0,0,0,$this->curMonth+1,0,0)).' - '.$this->curYear;
			 if($this->curMonth+1 == 13)
			  {
			  	$nextYear =$this->curYear + 1;
			  	$nextMonth = 1;
			  } else {
			  	$nextYear =$this->curYear;
			  	$nextMonth = $this->curMonth+1;
			  	$objGetIcon->alt = date("F",mktime(23,59,59,$this->curMonth+1, $this->curDay, $this->curYear));
			  }
			  $str2 .= '<a href="'.$this->uri(array('action' => 'events', 'month' => $nextMonth, 'year' => $nextYear )).'"> '.$objGetIcon->show().'</a>
						  </td>
						 </tr>';

		      $str2 .='<tr class="dayNamesText">';

		      //get the day names
		      for($i=1 ; $i < 8 ; $i++)
	      	  {
					$str2 .=' <td class="dayNamesRow" width="14%">'.date("D", mktime(0,0,0,10,$i,2006)).'</td>';
	      	  }
			  $str2 .= '</tr>';





			  //get the weeks in the month
			  for($i = 0; $i <= $weekNo ; $i++)
			  {
			        //loop the weeks for this month
			        $str2 .= '<tr  class="rows">';

			        //loop the days for this week
			        //but first format the first days of the calendar month
			        if($start)
			        {
			            //add the previous months days
				        for($k = 0; $k < $firstDayOfTheMonth ; $k++)
				        {
				            $str2 .= '<td class="sOther">'.$previousMonthsDays.'</td>';
				            $previousMonthsDays++;
				        }
				        $start = false;
			        }

			        //add the current months days
			        for ($j = $firstDayOfTheMonth; $j<7 ; $j++)
			        {
			            if($dayCounter <= $daysInMonth)
			            {
			                //get the event for this day
			                $str2 .= $this->getEventDay($this->curYear,$this->curMonth,$dayCounter);
			            } else {
			                //add the next  months days
			                $str2 .= '<td class="sOther">'.$nextMonthsDays.'</td>';
			                $nextMonthsDays++;
			            }
			            //increment the day counter
			            $dayCounter++;

			            //set the first day flag to 0
			            $firstDayOfTheMonth = 0;
			        }

			        //close the row
			        $str2 .= '</tr>';
			  }

			  //close the table
		      $str2 .= "</table>\r<!-- END START CALENDAR GENERATETOR -->";


		      return $str2;

	      }
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
	}


	/**
	* Method to biuld a big calender
	* @return string
	* @access public
	* @param array $arrEvents The events array
	*/
	public function bigCalendar($arrEvents = NULL)
	{
		try {


			  //the days in the month
			  $daysInMonth = date("t",mktime(23,59,59,$this->curMonth, $this->curDay, $this->curYear));

			  //the week number at the end of the month
			  $weekNo = date("W",mktime(23,59,59,$this->curMonth, $daysInMonth, $this->curYear));

			  //the first day of the week Mon = 0 Tues=1 etc
			  $firstDayOfTheMonth = date("w",mktime(23,59,59,$this->curMonth, 1, $this->curYear));

			  //last day of the month
			  $lastDayOfTheMonth = date("w",mktime(23,59,59,$this->curMonth, 1, $this->curYear));

			  //week number at the beginning of the month
			  $weekNoEndOfMonth = date("W",mktime(23,59,59,$this->curMonth, $this->curDay, $this->curYear));
			//  $weekNoEndOfMonth = 52;
			  //number of weeks for the month
			  $weekNo = ($weekNo - $weekNoEndOfMonth  ) + 1;


				//the start flag
			  $start = true;

			  //the day counter
			  $dayCounter = 1;

			  //next months days counter
			  $nextMonthsDays = 1;

			  //previous months days
			  $previousMonthsDays = date("t",mktime(23,59,59,$this->curMonth-1, $this->curDay, $this->curYear)) - $firstDayOfTheMonth + 1;


		      $str2 = '<!-- START CALENDAR GENERATETOR --><table class="mainTableTOC" cellspacing="1" cellpadding="0">
						 <tr>

						  <td class="monthYearText monthYearRow" colspan="7" >';
		      $objGetIcon = & $this->newObject('geticon', 'htmlelements');
		      $objGetIcon->setIcon('prev');

		      //the previous month navigation
		       if($this->curMonth-1 == 0)
			  {
			  	$prevYear =$this->curYear - 1;
			  	$prevMonth = 12;
			  } else {
			  	$prevYear =$this->curYear;
			  	$prevMonth = $this->curMonth-1;
			  }

			  //the previous link
			  $str2 .= '<a href="'. $this->uri(array("action" => "events", "month" => $prevMonth, "year" => $prevYear )) .'"> '.$objGetIcon->show().' </a>';

			  //set the next icon
			  $objGetIcon->setIcon('next');

			  //the month and year eg October - 2006
			  $str2 .= date("F", mktime(0,0,0,$this->curMonth+1,0,0)).' - '.$this->curYear;

			  //the next month
			 if($this->curMonth+1 == 13)
			  {
			  	$nextYear =$this->curYear + 1;
			  	$nextMonth = 1;
			  } else {
			  	$nextYear =$this->curYear;
			  	$nextMonth = $this->curMonth+1;
			  }
			  $str2 .= '<a href="'.$this->uri(array('action' => 'events', 'month' => $nextMonth, 'year' => $nextYear )).'"> '.$objGetIcon->show().'</a>
						  </td>
						 </tr>';

			  //get the day names
		      $str2 .='<tr class="dayNamesTextTOC">';
	      	  for($i=1 ; $i < 8 ; $i++)
	      	  {
					$str2 .=' <td class="dayNamesRowTOC" width="14%">'.date("l", mktime(0,0,0,10,$i,2006)).'</td>';
	      	  }
			  $str2 .= '</tr>';

			  	  //get the weeks in the month
			  for($i = 0; $i <= $weekNo ; $i++)
			  {
			  	//loop the weeks for this month
			  	$str2 .= '<tr  class="rowsTOC">';

			  	//loop the days for this week
			  	//but first format the first days of the calendar month
			  	if($start)
			  	{
			  		//add the previous months days
				  	for($k = 0; $k < $firstDayOfTheMonth ; $k++)
				  	{
				  		$str2 .= '<td class="sOther">'.$previousMonthsDays.'</td>';
				  		$previousMonthsDays++;
				  	}
				  	$start = false;
			  	}

			  	//add the current months days
			  	for ($j = $firstDayOfTheMonth; $j<7 ; $j++)
			  	{
			  		if($dayCounter <= $daysInMonth)
			  		{
			  			//get the event for this day
			  			$str2 .= $this->getEventDay($this->curYear,$this->curMonth,$dayCounter);
			  		} else {
			  			//add the next  months days
			  			$str2 .= '<td class="sOther">'.$nextMonthsDays.'</td>';
			  			$nextMonthsDays++;
			  		}
			  		//increment the day counter
			  		$dayCounter++;

			  		//set the first day flag to 0
			  		$firstDayOfTheMonth = 0;
			  	}

			  	//close the row
			  	$str2 .= '</tr>';
			  }

			  //close the table
		      $str2 .= "</table>\r<!-- END START CALENDAR GENERATETOR -->";


		      return $str2;

	      }
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
	}


	function assignDate($month,$year)
	{

		if($month > 12 || $month < 1)
		{
			 $month = 0; $year=0;
		}

		if($month == 0){
			$this->curMonth= date("m");
		}
		else{
			$this->curMonth= $month;
		}

		if($year == 0){
			$this->curYear = date("Y");
		}
		else{
			$this->curYear = $year;
		}
		$this->thisMonth=$this->curMonth;
	    $this->TempMonth=date("m");
   }

   function GenerateCalendar(){

   }

   /**
   * Method to get the Event Day
   * @param string $year The year
   * @param string $month The month
   * @param string $day The day
   * @access private
   * @return string
   *
   */
   private function getEventDay($year, $month, $day)
   {
   		$isEvents = false;
   		$givenDate  = $day."-".$month."-".$year;
   		$toc = ($this->calType=='big') ? 'TOC' : '';
   		$smallevents = '';
   		$eventsForToday = FALSE;
   		$cssClass = "s2".$toc;
   		$ev = '';
   		//check for today
   		if($day == date("d") && $month == date("n") && $year == date("Y"))
   		{
   			$cssClass="s2 today";
   		}

   		//setup the event info
    	foreach ($this->eventsArr as $event)
    	{
    			//print_r($event);
    			$eventDate = date("j-m-Y", trim($event['event_date']));

    			if($eventDate == $givenDate)
    			{
    				if ($this->calType=='big')
    				{
						 $ev .= '<div class="titleTOC">';
						 $ev .= stripslashes($event['description']);
						 $ev .='</div>';
    				} else {
                        $smallevents .= htmlentities('<span class=\"subheading\">'.$event['title'].'</span><br><span class=\"highlight\">'.stripslashes($event['description']).'</span>');
                    }

    				$isEvents = TRUE;
    			}

    	}

   		if($isEvents)
   		{

   			//$this->domTT->type = 'nested';
   			//$link = $this->domTT->show('title', $table , $day, $url = "#", $event['id']);
   			if($this->calType=='big')
            {
                $eventsForToday =  '<td class="s20TOC">';
                $eventsForToday .= '<div class="todayTOC">';
                $eventsForToday .= $day;
                $eventsForToday .= '</div>';
                $eventsForToday .= $ev;
                $eventsForToday .= '</td>';
            } else {
                $eventsForToday = '<td style="background-color: #A4CAE6;">';
                $eventsForToday .=  $this->domTT->show($eventDate,$smallevents,$day, $this->uri(array('action' => 'events','month' =>$month ),'eventscalendar'));//$day.$smallevents;
                $eventsForToday .= '</td>';
            }


   			return $eventsForToday;
   		} else {

   			$eventsForToday = '<td class="s20'.$toc.'">';
   			$eventsForToday .= ($this->calType=='big') ? '<div class="daynumTOC">':'';
   			$eventsForToday .= $day;
   			$eventsForToday .= ($this->calType=='big') ? '</div>':'';
   			$eventsForToday .= '</td>';
   			return $eventsForToday;
    		//return 	'<td class="'.$cssClass.'">'.$day.'</td>';
   		}


   }


}
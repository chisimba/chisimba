<?php

/**

* Handles attachments to events.

*/

class dbincident extends dbTable{
  var $finaltotal =  0;
	/**
	 *Constructor
 	*/
    	function init()
	    {
		    parent::init('tbl_incident');
	    }


  /**
   *function to insert all claimant details into a db table - tev   
   */     

 	    function addincident($incidentdetails)
	    {
        $results = $this->insert($incidentdetails);
        return $results;
      }

    function calcutotal()
    {
        //$expenses = array();
              
        $incident = $this->getSession('incidentdetails');
        $finaltotal =  '';
          if(!empty($incident))
          {
               foreach($incident as $sesIncident){
        
                  $finaltotal = $finaltotal + $sesIncident['cost'];       //need to fix up
              } 
            
          }
          return $finaltotal;
    }
      
    function  showincident()
    {
      $sessionIncident  = $this->getSession('incidentdetails');
 if(!empty($sessionIncident)){
//Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate
 
  $objTable = & $this->newObject('htmltable', 'htmlelements');
  $objTable->cellspacing = '2';
  $objTable->cellpadding = '2';
  $objTable->border='1';
  $objTable->width = '100%';
  
  $objTable->startHeaderRow();
  $objTable->addHeaderCell('Date ');
  $objTable->addHeaderCell('Vendor' );
  $objTable->addHeaderCell('Description');
  $objTable->addHeaderCell('Rate');
  $objTable->addHeaderCell('Currency');
  $objTable->addHeaderCell('Exchange Rate');
  $objTable->addHeaderCell('Exchange Rate File');
  $objTable->addHeaderCell('Online Source');
 // $objTable->addHeaderCell('Incident Rate File');
  $objTable->addHeaderCell('Receipt');
  $objTable->addHeaderCell('Affidavit');
  $objTable->endHeaderRow();

  
  $rowcount = '0';
  
  foreach($sessionIncident as $sesIncident){
     
  $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
  $objTable->startRow();
  $objTable->addCell($sesIncident['date'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['vendor'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['description'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['cost'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['currency'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['exchangerate'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['incidentratefile'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['quotesource'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['receiptfiles'], '', '', '', $oddOrEven);
  $objTable->addCell($sesIncident['affidavitfiles'], '', '', '', $oddOrEven);
  $objTable->endRow();
  }
   return $objTable->show();
  
}
   
    
    }  
 
  

}



?>

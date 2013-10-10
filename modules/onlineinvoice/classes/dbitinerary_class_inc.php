<?php
      /**
       *create a class for the itinerary       
       */    
       
       /**

* Handles attachments to events.

*/

class dbitinerary extends dbTable{

	/**
    * Constructor
    */

	function init()
	{
		parent::init('tbl_itinerary');
	}


  /**
   *create a funtion to add itinerary information
   */     
	function additinerary($itinerarydetails)
	{
	      
	      $results = $this->insert($itinerarydetails);
        return $results;
  }

	function showtitinerary()
	{
               $sessionItinerary = $this->getSession('addmultiitinerary');
 
               if(!empty($sessionItinerary)){
//Create table to display dates in session and the rates for breakfast, lunch and dinner and the total rate 
                        $objItineraryTable =& $this->newObject('htmltable', 'htmlelements');
                        $objItineraryTable->cellspacing = '2';
                        $objItineraryTable->cellpadding = '2';
                        $objItineraryTable->border='1';
                        $objItineraryTable->width = '100%';
  
                        $objItineraryTable->startHeaderRow();
                        $objItineraryTable->addHeaderCell('Departure Date');
                        $objItineraryTable->addHeaderCell('Departure Time' );
                        $objItineraryTable->addHeaderCell('Departure City');
                        $objItineraryTable->addHeaderCell('Arrival Date');
                        $objItineraryTable->addHeaderCell('Arrival Time');
                        $objItineraryTable->addHeaderCell('Arrival City');
                        $objItineraryTable->endHeaderRow();

  
                        $rowcount = '0';
  
                              foreach($sessionItinerary as $sesItinerary){
     
                                        $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
                                        $objItineraryTable->startRow();
                                        $objItineraryTable->addCell($sesItinerary['departuredate'], '', '', '', $oddOrEven);
                                        $objItineraryTable->addCell($sesItinerary['departuretime'], '', '', '', $oddOrEven);
                                        $objItineraryTable->addCell($sesItinerary['departurecity'], '', '', '', $oddOrEven);
                                        $objItineraryTable->addCell($sesItinerary['arrivaledate'], '', '', '', $oddOrEven);
                                        $objItineraryTable->addCell($sesItinerary['arrivaltime'], '', '', '', $oddOrEven);
                                        $objItineraryTable->addCell($sesItinerary['arrivalcity'], '', '', '', $oddOrEven);
                                        $objItineraryTable->endRow();
                              }
                
                return $objItineraryTable->show();
                } 
                
                
                
                

  }
  

}





         

?>

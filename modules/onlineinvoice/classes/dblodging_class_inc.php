<?php

class dbLodging extends dbTable{
  

 var $finaltotal  = 0;

	/**
	* Constructor
	*/

	function init()
	{
		parent::init('tbl_lodging');
	}


	function addlodge($data)
	{
   $results = $this->insert($data); 
   return $results;
  }


	function calculodgerate()
	{
	
	   $lodgerate = $this->getSession('lodgedetails');
	   $finaltotal  = '';
	   
          if(!empty($lodgerate))
          {
               foreach($lodgerate as $sesLodge){
        
                  $finaltotal = $finaltotal + $sesLodge['cost'];       //need to fix up
                  
              } 
            
          }
          return $finaltotal;
  }
  
  
  function showlodge()
  {
          $sessionLodge = $this->getSession('lodgedetails');
                if(!empty($sessionLodge)){
                    //Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate 
                    $objLodgeTable =& $this->newObject('htmltable', 'htmlelements');
                    $objLodgeTable->cellspacing = '2';
                    $objLodgeTable->cellpadding = '2';
                    $objLodgeTable->border='1';
                    $objLodgeTable->width = '100%';
  
                    $objLodgeTable->startHeaderRow();
                    $objLodgeTable->addHeaderCell('Date ');
                    $objLodgeTable->addHeaderCell('Vendor' );
                    $objLodgeTable->addHeaderCell('Currency');
                    $objLodgeTable->addHeaderCell('Room Rate');
                    $objLodgeTable->addHeaderCell('Exchange Rate');
                    $objLodgeTable->addHeaderCell('Online Source');
                    $objLodgeTable->addHeaderCell('Exchange Rate File');
                    $objLodgeTable->addHeaderCell('Receipt');
                    $objLodgeTable->addHeaderCell('Affidavit');
                    $objLodgeTable->endHeaderRow();

  
                    $rowcount = '0';
  
                        foreach($sessionLodge as $sesDat){
     
                            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
                            $objLodgeTable->startRow();
                            $objLodgeTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
                            $objLodgeTable->addCell($sesDat['vendor'], '', '', '', $oddOrEven);
                            $objLodgeTable->addCell($sesDat['currency'], '', '', '', $oddOrEven);
                            $objLodgeTable->addCell($sesDat['cost'], '', '', '', $oddOrEven);
                            $objLodgeTable->addCell($sesDat['exchangerate'], '', '', '', $oddOrEven);
                            $objLodgeTable->addCell($sesDat['quotesource'], '', '', '', $oddOrEven);
                            $objLodgeTable->addCell($sesDat['exchangefile'], '', '', '', $oddOrEven);
                            $objLodgeTable->addCell($sesDat['receiptfilename'], '', '', '', $oddOrEven);
                            $objLodgeTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
                            $objLodgeTable->endRow();
  
                            /**$objLodgeTable->startRow();
                              $objLodgeTable->addCell('');    
                              $objLodgeTable->addCell($sesDat['rateto'], '', '', '', $oddOrEven);  
                              $objLodgeTable->endRow();*/
    
                        }
                                return $objLodgeTable->show();
                  }
        
    }

 

}

?>

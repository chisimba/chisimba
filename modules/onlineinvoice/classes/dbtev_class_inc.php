<?php

/**

* Handles attachments to events.

*/

class dbTev extends dbTable{

	/**
	 *Constructor
 	*/
	
	

	function init()
	{
		parent::init('tbl_tev');
	}


  /**
   *function to insert all claimant details into a db table - tev   
   */     

 	function addclaimant($claimantdetails)
	{   $results = '';
      $results = $this->insert($claimantdetails);
      return $results;
  }


	
  /**
   *function to get all claimant information from the db
   */
        
	 function showclaimant()
	  { 
         
   
       $sessionClaimant [] = $this->getSession('claimantdata');
       if(!empty($sessionClaimant)){
//Create table to display dates in session and the rates for breakfast, lunch and dinner and the total rate 
        $objClaimantTable =& $this->newObject('htmltable', 'htmlelements');
        $objClaimantTable->cellspacing = '2';
        $objClaimantTable->cellpadding = '2';
        $objClaimantTable->border='1';
        $objClaimantTable->width = '100%';
        $objClaimantTable->cssClass = 'webfx-tab-style-sheet';
        $objClaimantTable->footing = 'Please submit or edit information';
      
        $objClaimantTable->startHeaderRow();
        $objClaimantTable->addHeaderCell('Name');
        $objClaimantTable->addHeaderCell('Title' );
        $objClaimantTable->addHeaderCell('Address');
        $objClaimantTable->addHeaderCell('City');
        $objClaimantTable->addHeaderCell('Province');
        $objClaimantTable->addHeaderCell('Postal Code');
        $objClaimantTable->addHeaderCell('Country');
        $objClaimantTable->addHeaderCell('Travel Purpose');
        $objClaimantTable->endHeaderRow();
        
        $rowcount = '0';
  
         foreach($sessionClaimant as $sesClaim){
     
        $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
        $objClaimantTable->startRow();
        $objClaimantTable->addCell($sesClaim['name'], '', '', '', $oddOrEven);
        $objClaimantTable->addCell($sesClaim['title'], '', '', '', $oddOrEven);
        $objClaimantTable->addCell($sesClaim['address'], '', '', '', $oddOrEven);
        $objClaimantTable->addCell($sesClaim['city'], '', '', '', $oddOrEven);
        $objClaimantTable->addCell($sesClaim['province'], '', '', '', $oddOrEven);
        $objClaimantTable->addCell($sesClaim['postalcode'], '', '', '', $oddOrEven);
        $objClaimantTable->addCell($sesClaim['country'], '', '', '', $oddOrEven);
        $objClaimantTable->addCell($sesClaim['travelpurpose'], '', '', '', $oddOrEven);
        $objClaimantTable->endRow();
        }
      }
          return $objClaimantTable->show();
          
     }
   
  
  /**
   *function to delete claimant information from the db
   */
   function removeClaimant()
  {}          
  
  

}



?>

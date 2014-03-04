<?php
      /**
       *create a class for the itinerary       
       */    
       
       /**

* Handles attachments to events.

*/

class dbsupplies extends dbTable{

	/**

	* Constructor

	*/

	function init()
	{
		parent::init('tbl_supplies');
		$this->objUser = & $this->getObject('user','security');
		$this->objFile = & $this->getObject('dbfile', 'filemanager');
	}


  /**
   *create a funtion to add itinerary information
   */     
	function addsupplies($supplydetails)
	{
	      
	      $results = $this->insert($supplydetails);
        return $results;
  }

	

	function getsupplies()
	{
      /**
       *get all information from the supplies form 
      */
      
      
       $supplydata  = array(
                  'createdby'          =>  $this->objUser->fullname(),
                  'datecreated'        =>  date('Y-m-d'),  
                  'modifiedby'         =>  $this->objUser->fullname(),
                  'datemodified'       =>  date('Y-m-d'),
                  'updated'            =>  date('Y-m-d'),
                  'date'               =>  $this->getParam('txtsuppliesdate'),        //itinerary departure date need to change
                  'vendorname'         =>  $this->getParam('txtvendor'),
                  'itemdescription'    =>  $this->getParam('txtdescriptionSupplies'),
                  'currency'           =>  $this->getParam('currency'),
                  'supplycost'         =>  $this->getParam('txtcost'),
                  'quotesource'        =>  $this->getParam('txtquotesource'),
                  'exchangerate'       =>  $this->getParam('txtexchange'),
                  'supplyexchratefile' =>  $this->objFile->getFileName($this->getParam('uploadrate')),
                  'attachreceipt'      =>  $this->objFile->getFileName($this->getParam('receiptfile')),
                  'affidavitfilename' =>  $this->objFile->getFileName($this->getParam('affidavitfile')), 
                  );   
      
      $this->setSession('supplydetails',$supplydata);            
                             
      
                   
  }
  
  function showSupply()
  {
          $sessionSupply [] = $this->getSession('supplydetails');
                if(!empty($sessionSupply)){
                    //Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate 
                    $objSupplyTable =& $this->newObject('htmltable', 'htmlelements');
                    $objSupplyTable->cellspacing = '2';
                    $objSupplyTable->cellpadding = '2';
                    $objSupplyTable->border='1';
                    $objSupplyTable->width = '100%';
  
                    $objSupplyTable->startHeaderRow();
                    $objSupplyTable->addHeaderCell('Date ');
                    $objSupplyTable->addHeaderCell('Vendor' );
                    $objSupplyTable->addHeaderCell('Description');
                    $objSupplyTable->addHeaderCell('Currency');
                    $objSupplyTable->addHeaderCell('Room Rate');
                    $objSupplyTable->addHeaderCell('Exchange Rate');
                    $objSupplyTable->addHeaderCell('Online Source');
                    $objSupplyTable->addHeaderCell('Exchange Rate File');
                    $objSupplyTable->addHeaderCell('Receipt');
                    $objSupplyTable->addHeaderCell('Affidavit');
                    $objSupplyTable->endHeaderRow();

  
                    $rowcount = '0';
  
                        foreach($sessionSupply as $sesDat){
     
                            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
                            $objSupplyTable->startRow();
                            $objSupplyTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
                            $objSupplyTable->addCell($sesDat['vendor'], '', '', '', $oddOrEven);
                            $objSupplyTable->addCell($sesDat['itemdescription'], '', '', '', $oddOrEven);
                            $objSupplyTable->addCell($sesDat['currency'], '', '', '', $oddOrEven);
                            $objSupplyTable->addCell($sesDat['supplycost'], '', '', '', $oddOrEven);
                            $objSupplyTable->addCell($sesDat['exchangerate'], '', '', '', $oddOrEven);
                            $objSupplyTable->addCell($sesDat['quotesource'], '', '', '', $oddOrEven);
                            $objSupplyTable->addCell($sesDat['supplyexchratefile'], '', '', '', $oddOrEven);
                            $objSupplyTable->addCell($sesDat['attachreceipt'], '', '', '', $oddOrEven);
                            $objSupplyTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
                            $objSupplyTable->endRow();
  
                            /**$objSupplyTable->startRow();
                              $objSupplyTable->addCell('');    
                              $objSupplyTable->addCell($sesDat['rateto'], '', '', '', $oddOrEven);  
                              $objSupplyTable->endRow();*/
    
                        }
                                return $objSupplyTable->show();
                  }
                  
          }
  

}

?>
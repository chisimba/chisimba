<?php
      /**
       *create a class for the itinerary       
       */    
       
       /**

* Handles attachments to events.

*/

class dbodc extends dbTable{

	/**

	* Constructor

	*/

	function init()
	{
		parent::init('tbl_odc');
		$this->objUser = & $this->getObject('user','security');
		$this->objFile = & $this->getObject('dbfile', 'filemanager');
	}


  /**
   *create a funtion to add itinerary information
   */     
	function addodc($odcdetails)
	{
	      
	      $results = $this->insert($odcdetails);
        return $results;
  }

	

	function getodcexpenses()
	{
      /**
       *get all information from the supplies form 
      */
      
      
       $odcdata  = array(
                  'createdby'          =>  $this->objUser->fullname(),
                  'datecreated'        =>  date('Y-m-d'),  
                  'modifiedby'         =>  $this->objUser->fullname(),
                  'datemodified'       =>  date('Y-m-d'),
                  'updated'            =>  date('Y-m-d'),
                  'date'               =>  $this->getParam('txtodcdate'),        //itinerary departure date need to change
                  'vendorname'         =>  $this->getParam('txtvendor'),
                  'odcdescription'    =>  $this->getParam('txtodcdesc'),
                  'currency'           =>  $this->getParam('odccurrency'),
                  'odccost'         =>  $this->getParam('txtcost'),
                  'exchangerate'    => $this->getParam('txtexchange'),
                  'quotesource'        =>  $this->getParam('txtquotesource'),
                  'odcexchratefile' =>  $this->objFile->getFileName($this->getParam('exchangeratefile')),
                  'attachreceipt'      =>  $this->objFile->getFileName($this->getParam('receiptfile')),
                  'affidavitfilename' =>  $this->objFile->getFileName($this->getParam('affidavitfile')),
                  );   
      
      $this->setSession('odcdetails',$odcdata);            
                             
      
                   
  }
  
  function showOdc()
  {
          $sessionOdc [] = $this->getSession('odcdetails');
                if(!empty($sessionOdc)){
                    //Create table to display itinerary details in session and the rates for breakfast, lunch and dinner and the total rate 
                    $objOdcTable =& $this->newObject('htmltable', 'htmlelements');
                    $objOdcTable->cellspacing = '2';
                    $objOdcTable->cellpadding = '2';
                    $objOdcTable->border='1';
                    $objOdcTable->width = '100%';
  
                    $objOdcTable->startHeaderRow();
                    $objOdcTable->addHeaderCell('Date ');
                    $objOdcTable->addHeaderCell('Vendor' );
                    $objOdcTable->addHeaderCell('Currency');
                    $objOdcTable->addHeaderCell('Room Rate');
                    $objOdcTable->addHeaderCell('Exchange Rate');
                    $objOdcTable->addHeaderCell('Online Source');
                    $objOdcTable->addHeaderCell('Exchange Rate File');
                    $objOdcTable->addHeaderCell('Receipt');
                    $objOdcTable->addHeaderCell('Affidavit');
                    $objOdcTable->endHeaderRow();

  
                    $rowcount = '0';
  
                        foreach($sessionOdc as $sesDat){
     
                            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
                            $objOdcTable->startRow();
                            $objOdcTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
                            $objOdcTable->addCell($sesDat['vendor'], '', '', '', $oddOrEven);
                            $objOdcTable->addCell($sesDat['odcdescription'], '', '', '', $oddOrEven);
                            $objOdcTable->addCell($sesDat['currency'], '', '', '', $oddOrEven);
                            $objOdcTable->addCell($sesDat['odccost'], '', '', '', $oddOrEven);
                            $objOdcTable->addCell($sesDat['exchangerate'], '', '', '', $oddOrEven);
                            $objOdcTable->addCell($sesDat['quotesource'], '', '', '', $oddOrEven);
                            $objOdcTable->addCell($sesDat['odcexchratefile'], '', '', '', $oddOrEven);
                            $objOdcTable->addCell($sesDat['attachreceipt'], '', '', '', $oddOrEven);
                            $objOdcTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
                            $objOdcTable->endRow();
  
                            /**$objOdcTable->startRow();
                              $objOdcTable->addCell('');    
                              $objOdcTable->addCell($sesDat['rateto'], '', '', '', $oddOrEven);  
                              $objOdcTable->endRow();*/
    
                        }
                                return $objOdcTable->show();
         	}

		}
}
      

?>

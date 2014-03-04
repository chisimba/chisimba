<?php
      /**
       *create a class for the itinerary       
       */    
       
       /**

* Handles attachments to events.

*/

class dbequipment extends dbTable{

	/**

	* Constructor

	*/

	function init()
	{
		parent::init('tbl_equipment');
		$this->objUser = & $this->getObject('user','security');
		$this->objFile = & $this->getObject('dbfile', 'filemanager');
	}


  /**
   *create a funtion to add itinerary information
   */     
	function addequipment($equipmentdetails)
	{
	      
	      $results = $this->insert($equipmentdetails);
        return $results;
  }

	

	function getequipment()
	{
      /**
       *get all information from the equipment expenses form 
      */
      
      $equipmentdata  = array(
                  'createdby'          =>  $this->objUser->fullname(),
                  'datecreated'        =>  date('Y-m-d'),  
                  'modifiedby'         =>  $this->objUser->fullname(),
                  'datemodified'       =>  date('Y-m-d'),
                  'updated'            =>  date('Y-m-d'),
                  'date'               =>  $this->getParam('txtequipmentdate'),        //itinerary departure date need to change
                  'vendorname'         =>  $this->getParam('txtvendor'),
                  'equipdescription'   =>  $this->getParam('txtdescriptionequipment'),
                  'currency'           =>  $this->getParam('currency'),
                  'equipcost'          =>  $this->getParam('txtcost'),
                  'quotesource'        =>  $this->getParam('txtquotesource'),
                  'exchangerate'       =>  $this->getParam('txtexchange'),
                  'equipexchratefile'  =>  $this->objFile->getFileName($this->getParam('uploadrate')),
                  'attachreceipt'      =>  $this->objFile->getFileName($this->getParam('receiptfile')),
                  'affidavitfilename'  =>  $this->objFile->getFileName($this->getParam('affidavitfile')), 
                  );   
      
      $this->setSession('equipmentdetails',$equipmentdata);            
                             
      
                   
  }
  
  function showEquipment()
  { 
          $sessionEquipment [] = $this->getSession('equipmentdetails');
                if(!empty($sessionEquipment)){
                    //Create table to display Equipment details in session and the rates for breakfast, lunch and dinner and the total rate 
                    $objEquipmentTable =& $this->newObject('htmltable', 'htmlelements');
                    $objEquipmentTable->cellspacing = '2';
                    $objEquipmentTable->cellpadding = '2';
                    $objEquipmentTable->border='1';
                    $objEquipmentTable->width = '100%';
  
                    $objEquipmentTable->startHeaderRow();
                    $objEquipmentTable->addHeaderCell('Date ');
                    $objEquipmentTable->addHeaderCell('Vendor' );
                    $objEquipmentTable->addHeaderCell('Description');
                    $objEquipmentTable->addHeaderCell('Currency');
                    $objEquipmentTable->addHeaderCell('Cost');
                    $objEquipmentTable->addHeaderCell('Exchange Rate');
                    $objEquipmentTable->addHeaderCell('Online Source');
                    $objEquipmentTable->addHeaderCell('Exchange Rate File');
                    $objEquipmentTable->addHeaderCell('Receipt');
                    $objEquipmentTable->addHeaderCell('Affidavit');
                    $objEquipmentTable->endHeaderRow();

  
                    $rowcount = '0';
  
                        foreach($sessionEquipment as $sesDat){
     
                            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
  									 $objEquipmentTable->startRow();
 									 $objEquipmentTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
 									 $objEquipmentTable->addCell($sesDat['vendorname'], '', '', '', $oddOrEven);
 									 $objEquipmentTable->addCell($sesDat['equipdescription'], '', '', '', $oddOrEven);
 									 $objEquipmentTable->addCell($sesDat['currency'], '', '', '', $oddOrEven);
 									 $objEquipmentTable->addCell($sesDat['equipcost'], '', '', '', $oddOrEven);
 									 $objEquipmentTable->addCell($sesDat['exchangerate'], '', '', '', $oddOrEven);
 									 $objEquipmentTable->addCell($sesDat['quotesource'], '', '', '', $oddOrEven);
 									 $objEquipmentTable->addCell($sesDat['equipexchratefile'], '', '', '', $oddOrEven);
 									 $objEquipmentTable->addCell($sesDat['attachreceipt'], '', '', '', $oddOrEven);
  									 $objEquipmentTable->addCell($sesDat['affidavitfilename'], '', '', '', $oddOrEven);
                            $objEquipmentTable->endRow();
  
                            /*$objEquipmentTable->startRow();
                              $objEquipmentTable->addCell('');    
                              $objEquipmentTable->addCell($sesDat['rateto'], '', '', '', $oddOrEven);  
                              $objEquipmentTable->endRow();*/
    
                        }
                                return $objEquipmentTable->show();
                  }
                  
          }
  

}





         

?>

<?php
/**
  *create a class that contains functions to manipulate data in db table tbl_pierdiem
  *  
  */
   
   class dbperdiem extends dbTable{
      
     // var $bval = 0.25;
     // var $lval = 0.30;
     // var $dval = 0.45;
     var $total  = 0.0;
     var $finaltotal  = 0.0; 
	      
      /**
        * Constructor of the dbInvoice class
      */   
      function init()
      {
  	       parent::init('tbl_pierdiem');
      }
    
    /**
      *function to add invoice dates to the db
      */         
  	
       function addperdiem($perdiemdetails)
       {
            $results = $this->insert($perdiemdetails);
            return $results;
       }
       
       /**
        * Fucntion used to calculate the breakfast, lunch, dinner rate of the traveler
        * 
        * @return float $total Total expenditure for the day        
        */                               
       function calculate()
       {
            //$total = '0';
            switch($this->getParam('rates_radio')){
                case 'foreign':
                
                    $breakfastTotal = ($this->getParam('txtbreakfastRate') * 0.25) + $this->getParam('txtbreakfastRate');
                    $lunchTotal = ($this->getParam('txtlunchRate') * 0.30) +  $this->getParam('txtlunchRate');
                    $dinnerTotal = ($this->getParam('txtdinnerRate') * 0.45) + $this->getParam('txtdinnerRate') ;
                    
                    $total = $breakfastTotal + $lunchTotal + $dinnerTotal;
                    break;
                    
                case 'domestic':
                    $total = $this->getParam('txtbreakfastRate') + $this->getParam('txtlunchRate') + $this->getParam('txtdinnerRate');
                    break;     
            }
            return  $total;
       } 
       /**
        *function used to calculate the total of daily breakfast....rates combined
        *@return float $finaltotal Total of all expenditures         
        */
                       
       
       function calcutotal()
       {
        //$expenses = array();
              
            $expenses = $this->getSession('perdiemdetails');
            $finaltotal =  '';
          if(!empty($expenses))
          {
               foreach($expenses as $sesExp){
        
                  $finaltotal = $finaltotal + $sesExp['total'];       //need to fix up
              } 
            
          }
          return $finaltotal;
      }
  
  
  function showperdiem()
  {
                $sessionDates = $this->getSession('perdiemdetails');  
                if(!empty($sessionDates)){
//Create table to display dates in session and the rates for breakfast, lunch and dinner and the total rate 
                        $objExpByDateTable =& $this->newObject('htmltable', 'htmlelements');
                        $objExpByDateTable->cellspacing = '2';
                        $objExpByDateTable->cellpadding = '2';
                        $objExpByDateTable->border='1';
                        $objExpByDateTable->width = '100%';
  
                        $objExpByDateTable->startHeaderRow();
                        $objExpByDateTable->addHeaderCell('Date ');
                        $objExpByDateTable->addHeaderCell('Breakfast Location' );
                        $objExpByDateTable->addHeaderCell('Breakfast Rate');
                        $objExpByDateTable->addHeaderCell('Lunch Location');
                        $objExpByDateTable->addHeaderCell('Lunch Rate');
                        $objExpByDateTable->addHeaderCell('Dinner Location');
                        $objExpByDateTable->addHeaderCell('Dinner Rate');
                        $objExpByDateTable->addHeaderCell('Total');
                        $objExpByDateTable->endHeaderRow();

  
                        $rowcount = '0';
  
                        foreach($sessionDates as $sesDat){
     
                             $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
                             $objExpByDateTable->startRow();
                             $objExpByDateTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
                             $objExpByDateTable->addCell($sesDat['blocation'], '', '', '', $oddOrEven);
                             $objExpByDateTable->addCell($sesDat['btrate'], '', '', '', $oddOrEven);
                             $objExpByDateTable->addCell($sesDat['llocation'], '', '', '', $oddOrEven);
                             $objExpByDateTable->addCell($sesDat['lRate'], '', '', '', $oddOrEven);
                             $objExpByDateTable->addCell($sesDat['dlocation'], '', '', '', $oddOrEven);
                             $objExpByDateTable->addCell($sesDat['drrate'], '', '', '', $oddOrEven);
                             $objExpByDateTable->addCell($sesDat['total'], '', '', '', $oddOrEven);
                             $objExpByDateTable->endRow();
                       }
                 
                      return $objExpByDateTable->show();
                 }
                 
               

  }
  function perdiemtotal(){
     
     $perdiemtot  = $this->getSession('perdiemdetails');
                if(!empty($perdiemtot)){
                   $objExpByDateTable =& $this->newObject('htmltable', 'htmlelements');
                   $objExpByDateTable->cellspacing = '2';
                   $objExpByDateTable->cellpadding = '2';
                   $objExpByDateTable->border='1';
                   $objExpByDateTable->width = '25%';
  
                   $objExpByDateTable->startHeaderRow();
                  // $objExpByDateTable->addHeaderCell('Date');
                  // $objExpByDateTable->addHeaderCell('Total for per diem daily rates ');
                   $objExpByDateTable->addHeaderCell('Per Diem Final Total' );
                   
                   $objExpByDateTable->endHeaderRow();

  
                        $rowcount = '0';
  
                        foreach($perdiemtot as $sesDat){
     
                             $oddOrEven = ($rowcount == 0) ? "odd" : "even";
     
                             $objExpByDateTable->startRow();
                     //        $objExpByDateTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
                    //         $objExpByDateTable->addCell($sesDat['total'], '', '', '', $oddOrEven);
                             $objExpByDateTable->addCell($sesDat['finaltotal'], '', '', '', $oddOrEven);
                             $objExpByDateTable->endRow();
                        }
                 
                      return $objExpByDateTable->show();
                 }
 }
}
  
?>

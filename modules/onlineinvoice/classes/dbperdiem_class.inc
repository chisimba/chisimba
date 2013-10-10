<?php
  /**
   *create a class that contains functions to manipulate data in db table tbl_pierdiem
   */
   
   class dbperdiem extends dbTable{

	      
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
  }
?>

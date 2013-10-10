<?php

class dbInvoice extends dbTable{

	    /**
        * varaible $begindate used to store the value user selects as the start of the invoice
        * @public 
     */     
             
      public  $begindate = null;
	
	    /**
        * variable $enddate used to store the value user selects as the start of the invoice
        * @public 
     */     
             
      public  $enddate = null;
      
        
    /**
      * Constructor of the dbInvoice class
    */   
	   function init()
  	{
	   	parent::init('tbl_invoice');
    }
    
    /**
     *function to add invoice dates to the db
     */         
  	
     function addinvoice($invdates)
	   {
        $results = $this->insert($invdates);
        return $results;
     }
     
     /**
      *function to remove invoice details from the db
      */
                          
	   function deleteinvoice()
	   {
       /*remove an invoice */
     }
    
    /**
     *function to get invoice details from the db
     */
     
  	function getinvoicedates()
  	{
   /**
    *function used to get invoice details from the db table -- invoice
   */

    }



}
?>

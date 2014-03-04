<?php
include 'SCA/SCA.php';

/**
 * @service
 * @binding.soap
 * @binding.jsonrpc
 * @binding.xmlrpc
 * @binding.restrpc
 * 
 * @types http://server chisimba.xsd
 */ 
 class server
 {

 	/**
      * Method to say hello
      *
      * @param string $name
      * @return string
      */
      public function hello($name)
      {
          return 'hello '.$name;
      }
 	
 	  /**
	   * @param string $name (who to look up)
	   * @return ChisimbaType http://server (the person)
	   */
	   public function lookup($name)
	   {
			if ($name == 'William Shakespeare') {
				$person = SCA::createDataObject('http://server','ChisimbaType');
				$person->name = $name;
				$person->dob = 'April 1564, most likely 23rd';
				$person->pob = 'Stratford-upon-Avon, Warwickshire';
				return $person;
			} else {
				return NULL;
			}
	   }

      // any PHP scalar will work here, boolean, string, integer, float, NULL etc
      // arrays and complextypes need an XSD doc.
      
      // maybe we need to curl params into the framework to use methods?
      // maybe a native class? 
      
      // ugh!
}
?>
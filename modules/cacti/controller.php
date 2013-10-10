<?php
/** This module integrate cacti to chisimba
* To use this module you need to have  Cacti running on a server 
* This module connect you to your cacti server
*@Author Emmanuel Togo
*@Package cacti
*
*/
class cacti extends controller
{
    /**
     * Intialiser for the controller
     */
    public $objLanguage;


	/**
        	*Constructor method to instantiate objects and get varialbes
	*/

    public function init()
    {
	$this->sysConfig = $this->getObject('dbsysconfig','sysconfig');
	$this->objLanguage = $this->getObject('language','language');
	
    }
    
    /**
     * Method to process actions to be taken
     *
    */

    public function dispatch()
    {
			
	$url = $this->sysConfig->getValue('cacti_url','cacti');
	
	switch($url) {
		default:
	if($url == 'none'){
               return 'error_tpl.php';
        }
  	else {
// 			 $url= $this->sysConfig->getValue('cacti_url','cacti');
			$this->setVarByRef('url',$url);
	}		return "dump_tpl.php";
	}
    }
    
}

<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* paypal is a block that redirects visitors to www.paypal.com so they 
*can donate money through  paypal
*
* 
* 
* 
*
*/
class block_paypal extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
     * Variable to override the block type;
     *
     * @var string
     */
    public $blockType;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
    	try {
    		$this->objLanguage = & $this->getObject('language', 'language');
    		$this->objSysConfig =& $this->getObject('dbsysconfig','sysconfig');
    		$this->blockType = 'none';
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
    
    /**
    * Standard block show method. It uses the renderform
    * class to render the login box
    */
    public function show()
    {
    	try {
    		
    		$business=$this->objSysConfig->getValue('PAYPAL_BUSINESS', 'blockalicious');
    		$itemName=$this->objSysConfig->getValue('PAYPAL_ITEM_NAME', 'blockalicious');
    		$currencyCode=$this->objSysConfig->getValue('PAYPAL_CURRENCY_CODE', 'blockalicious');
    		$amount=$this->objSysConfig->getValue('PAYPAL_AMOUNT', 'blockalicious');
    		
    		$form='<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">';
				$form.='<input type="hidden" name="cmd" value="_xclick"/>';
				$form.='<input type="hidden" name="business" value="'.$business.'"/>';
				$form.='<input type="hidden" name="item_name" value="'.$itemName.'"/>';
				$form.='<input type="hidden" name="currency_code" value="'.$currencyCode.'"/>';
				$form.='<input type="hidden" name="amount" value="'.$amount.'"/>';
				$form.='<input type="image" src="skins/_common/icons/paypalDonate.gif" border="0" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!"/>';
				$form.='</form>';
    		return "<center>$form</center>";
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}
?>
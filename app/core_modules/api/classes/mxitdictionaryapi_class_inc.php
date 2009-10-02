<?php


// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


class mxitdictionaryapi extends object
{

    /**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */

	public function init()

	{

		try {

			$this->objConfig = $this->getObject('altconfig', 'config');
            $this->objLanguage = $this->getObject('language', 'language');
            //database abstraction object
            $this->objModules = $this->getObject('modules', 'modulecatalogue');
            $this->isReg = $this->objModules->checkIfRegistered('mxitdictionary');
            if($this->isReg === TRUE)
            {
				$this->objMxitDictionary = $this->getObject('dbcontacts', 'mxitdictionary');
			}
		}
		catch (customException $e)

		{

		customException::cleanUp();

		exit;
		}
	}


	//Gets all the words the database
	public function getAll($params)
	{
		// get alph
		$param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }

        $alph = $param->scalarval(); 

		// get start number
		$param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }

        $start = $param->scalarval();
		
		// get number to return
		$param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }

        $num = $param->scalarval();
            
        $wordStruct = array();
		$data = $this->objMxitDictionary->listAll($alph, $start, $num);
					
		foreach($data as $newdata)
            {
                $struct = new XML_RPC_Value(array(
                    new XML_RPC_Value($newdata['word'], "string"),
                    new XML_RPC_Value($newdata['definition'], "string")), "array");
                $wordStruct[] = $struct;
            }
         $wordArray = new XML_RPC_Value($wordStruct,"array");
         return new XML_RPC_Response($wordArray);
            
//
	}


	// Gets the defition by word
	public function getDefinition($params)
	{
		// get word
		$param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }

        $word = $param->scalarval(); 

		// get the definition by the word.
		$message = $this->objMxitDictionary->getDefinition($word);
		$val = $message['definition'];

		// return the value as an XML-RPC value and response.
		$val2send = new XML_RPC_Value($val, 'string');

		return new XML_RPC_Response($val2send);
		}
}
?>

<?php
/**
*
* Class to parse a string (e.g. page content) that contains a request
* to load a personal data item into a page in place of a pseudotag in the form
* [PDATA]item[/PDATA]
*
* @author Derek Keats
*
*/

class parse4pdata extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     */
    public function init()
    {
		$this->objUser = $this->getObject("user", "security");
    }
    
    /**
    *
    * Method to parse the string
    * @param String $str The string to parse
    * @return The parsed string
    *
    */
    public function parse($str)
    {
        $str = stripslashes($str);
        //Get all the tags into an array
        preg_match_all('/\\[PDATA](.*?)\\[\/PDATA]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
        	$dataItem = $results[1][$counter];
        	$replacement = $this->getReplacement($dataItem);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }
    
    private function getReplacement($dataItem)
    {
    	$dataMethod = strtolower($dataItem);
    	if (method_exists($this, $dataMethod)) {
    		return $this->$dataMethod();
    	} else {
    	    return "No corresponding method for: }}}$dataItem{{{";
    	}
    }
    
    /**
    * 
    * Method to correspond to FIRSTNAME
    * 
    */
    private function firstname()
    {
    	$ret = $this->objUser->getFirstName($this->objUser->userId());
    	if ($ret == "") {
    	    $ret = "Guest";
    	}
        return $ret;
    }

    /**
    * 
    * Method to correspond to FULLNAME
    * 
    */
    private function fullname()
    {
    	$ret = $this->objUser->getFirstName($this->objUser->userId()) . " " . $this->objUser->getSurname($this->objUser->userId());
    	if ($ret == " ") {
    	    $ret = "Guest";
    	}
        return $ret;
    }

    /**
    * 
    * Method to correspond to SURNAME
    * 
    */
    private function surname()
    {
    	$ret = $this->objUser->getSurname($this->objUser->userId());
    	if ($ret == "") {
    	    $ret = "Guest";
    	}
        return $ret;
    }
    
    /**
    * 
    * Method to correspond to USERPIC
    * 
    */
    private function userpic()
    {
        return $this->objUser->getUserImage($this->objUser->userId());
    }

    /**
    * 
    * Method to correspond to TITLE
    * 
    */
    private function title()
    {
    	$pkId = $this->objUser->PKId($this->objUser->userId());
        return $this->objUser->getItemFromPkId($pkId, "title");
    }

}
?>
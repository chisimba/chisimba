<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
* Class to clean html
* @package safehtml
* @category utilities
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version
* @author Wesley  Nitsckie
* @example :
*/
require_once($this->getResourceUri('safehtml/safehtml.php', 'utilities');
class htmlcleaner  extends object
{
   /**
	 * @var object $_objSafeHtml The safehtml object
	 * @access protected
	 */
    protected $_objSafeHtml;

    /**
     * Construtor
     */
    public function init()
    {

        $this->_objSafeHtml =& new safehtml();
    }

    /**
     * Method to clean the html
     * @param string $html the html
     * @return string the clean scrubbed html
     * @access public
     * @author Wesley Nitsckie
     */
    public function cleanHtml($html = NULL)
    {
        if(is_null($html))
        {
            return $html;
        } else {
            return $this->_objSafeHtml->parse($html);
        }
    }

}

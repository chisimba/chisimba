<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to Access List of Creative Commons Licenses in the Database
 * @author Tohir Solomons
 */
class dbcreativecommons extends dbTable
{
    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_creativecommonstypes');
    }
    
    /**
    * Method to get the details of a license
    * @param string $license License Code (eg. by-sa)
    * @return array
    */
    public function getLicense($license)
    {
        return $this->getRow('code', $license);
    }

    
}
?>
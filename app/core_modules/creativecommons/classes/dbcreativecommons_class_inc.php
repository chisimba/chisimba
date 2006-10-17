<?php

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

    
}
?>
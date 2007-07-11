<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The class that demonstrates how to use blocks
*
* @author Derek Keats

*
* $Id$
*
*/
class block_table extends object
{
    public $title;

    public function init()
    {
        $this->title="Type: table";
    }

    public function show()
	{
		return "This is an example of a block rendered using type table. It places the title in a normal header cell, and the block output in a table cell.";
    }
}
?>
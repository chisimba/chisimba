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
* @author Paul Scott based on methods by Derek Keats

*
* $Id$
*
*/
class block_tabbedbox extends object
{
    public $title;

    public function init()
    {
        $this->title="Type: tabbedbox with a very long string  title";
    }

    public function show()
	{
		return "This is an example of a block rendered using type tabbedbox. It places the title in a tab, and the block output in a tabbed box.";
    }
}
?>
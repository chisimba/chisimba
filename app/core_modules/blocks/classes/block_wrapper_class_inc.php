<?
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
class block_wrapper extends object
{
    public $title;

    public function init()
    {
        $this->title="Type: wrapper";
    }

    public function show()
	{
		return "This is an example of a block rendered using type wrapper. It places the title in a dark outside layer, and the block output in a light inside layer.";
    }
}
?>
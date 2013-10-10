<?php
/**
*
* Menu for dkeats.com
*
* This class provides a menu for the dkeats.com site.
*
* @author Derek Keats
* @package dkeatscom
*
*/

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Menu for dkeats.com
*
* This class provides a menu for the dkeats.com site. It is derived from the
* elearn toolbar, but not very related to it.
*
* @author Derek Keats
* @package dkeatscom
*
*/
class toolbardwk extends object
{
    /**
    *
    * @var string $objUser String object property for holding the user object
    *
    * @access public
    *
    */
    //public $objUser;

    /**
    *
    * Constructor for the class
    * @access public
    *
    */
    public function init()
    {
        //$this->objUser = $this->getObject('user', 'security');
        $this->loadClass('link','htmlelements');
    }

    /**
    *
    * Method to display the dkeats.com toolbar
    * @access public
    * @return string The formatted toolbar
    *
    */
    public function show()
    {
        $objBreadcrumbs = $this->getObject('breadcrumbs', 'toolbar');
        // Should something be shown to not logged in users
        return $this->generateMenu()
          . '<div id="breadcrumbs">'
          .$objBreadcrumbs->show().'</div>';
    }

    /**
     * Method to generate the elearn toolbar
     */
    private function generateMenu()
    {
        $menuItem = array();
        $css="class=\"default\"";
        $str = "<div id='dwkmenu'>";
        $spacer = "&nbsp;|&nbsp;";
        // Home link
        $homeLink = new link('home');
        $homeLink->link = 'Home';
        $homeLink->href = $this->uri(array(), "_default");
        $str .= $spacer . $homeLink->show();
        // Podcast link
        $podLink = new link('podcast');
        $podLink->link = 'Podcasts';
        $podLink->href = $this->uri(array(), "podcast");
        $str .= $spacer . $podLink->show();
        $str .= $spacer;
        // Close the opening div
        $str .= "</div>";
        return $str;

    }

}
?>
<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building a rounded div box for KEWL.nextgen.
*
* The class builds a css style div rounded corners box
*
* @category  Chisimba
* @author    Prince Mbekwa
* @package   htlmelements
* @version   $Id$
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @link      http://avoir.uwc.ac.za
*/

class roundcorners extends object
{

    /**
     * Holds the content of the box
     * @var    string
     * @access public
     */
    public $content;

    /**
     * Constructor
     *
     */
    function init(){

    }


    /**
     * Method to display the box
     *
     * @param  string $content
     * @return string Return box
     * @access public
     */
    function show($content='')
    {
        if ($content != '') {
            $this->content = $content;
        }
        
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/cornerz.js'));
        $this->appendArrayVar('headerParams', "<script type=\"text/javascript\">
    jQuery(document).ready(function(){
        jQuery('.roundcorners').cornerz();
    })
    </script>");
         return '<div class="roundcorners">'.$this->content.'</div>';
    }

}
?>
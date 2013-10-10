<?php
/**
 *
 * A Facebook apps class
 *
 * A Facebook apps class to generate standard facebook snippets for inclusion
 * into Chisimba. Note that you can to use
 * $this->loadFbApiScript();
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @version    0.001
 * @package    facebookapps
 * @author     Derek Keats <derek@dkeats.com>
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * 
 * A Facebook apps class
 *
 * A Facebook apps class to generate standard facebook snippets for inclusion
 * into Chisimba. Note that you can to use $this->loadFbApiScript();
 *
 * @category  Chisimba
 * @author    Derek Keats <derek@dkeats.com>
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class fbapps extends object
{

    /**
     *
     * @var string Object $objUser String for the user object
     * @access public
     *
     */
    public $objUser;
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        // Get an instance of the languate object
        $this->objLanguage = $this->getObject('language', 'language');
    }
    /**
     * 
     * Get Facebook comments for the current URL
     *
     * @param string $width The width of the block 
     * @param string $numPosts The number of posts to show
     * @param string $url The URL to which the posts apply
     * @return string Facebook comments
     * @access public
     * 
     */
    public function getComments($width=425, $numPosts=10, $url=FALSE)
    {
        if (!$url) {
            $objUrl = $this->getObject('urlutils', 'utilities');
            $url = $objUrl->curPageURL();
        }
        $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $apikey = $this->objDbSysconfig->getValue('apid', 'facebookapps');
        $script = $this->loadFbApiScript();

        //$this->appendArrayVar('afterBodyScripts','TEST');
        //$this->appendArrayVar('afterBodyScripts','TEST2');

        return '<div id="fb-root"></div>' . $script . '<fb:comments numposts="'
          . $numPosts . '" width="' . $width
          . '" data-href="' . $url 
          . '" publish_feed="true"></fb:comments>';
    }

    /**
     *
     * Render the FB API script.
     * 
     * @return string The rendered script
     * @access public
     * 
     */
    public function loadFbApiScript()
    {
        $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $apikey = $this->objDbSysconfig->getValue('apid', 'facebookapps');
        $script = '<script src="http://connect.facebook.net/en_US/all.js#appId=' 
          . $apikey . '&amp;xfbml=1"></script>';
        //$this->appendArrayVar('headerParams', $script);
        //return TRUE;
        return $script;
        
    }

    /**
     *
     * Get the number of comments from Facebook.
     *
     * @return string Comments and label
     */
    public function insertCommentCount()
    {
        $pageUrl = 'http';
        if (!empty($_SERVER["HTTPS"])) {
            $pageURL .= "s";
        }
        $pageUrl .= "://";
         if ($_SERVER["SERVER_PORT"] != "80") {
            $pageUrl .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
         } else {
            $pageUrl .= $_SERVER["SERVER_NAME"].htmlentities($_SERVER["REQUEST_URI"]);
         }
         //echo 'HEREHEREHERE: ' . $pageUrl;
         //$pageUrl = urlencode($pageUrl);
         return "<fb:comments-count href=$pageUrl/></fb:comments-count>";
    }
}
?>

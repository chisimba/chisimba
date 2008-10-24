<?php

/**
*
* Parser for disqus.com discussions
*
* Class to parse a string (e.g. page content) that contains a filter
* tag to insert a discussion from disqus.com.  It takes the form
* [DISQUS: url=http://somesite.com]
*
* PHP version 5
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
* @category  Chisimba
* @package   filters
* @author    Derek Keats <dkeats@uwc.ac.za>
* @copyright 2007 Derek Keats
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id: parse4disqus_class_inc.php 3695 2008-03-29 21:39:23Z dkeats $
* @link      http://avoir.uwc.ac.za
*
*/
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check




/**
*
* Class to parse a string (e.g. page content) that contains a filter
* tag to insert a discussion from disqus.com.  It takes the form
* [DISQUS: url=http://somesite.com]
*
* @category  Chisimba
* @package   filters
* @author    Derek Keats <dkeats@uwc.ac.za>
* @copyright 2007 Derek Keats
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id: parse4pdf_class_inc.php 2813 2007-08-03 09:29:14Z paulscott $
* @link      http://avoir.uwc.ac.za
* @see
*
*/
class parse4disqus extends object
{

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    *
    * @var String object $objExpar is a string to hold the parameter extractor object
    * @access public
    *
    */
    public $objExpar;

    /**
     *
     * @var String $url is the URL of display item
     * Valid: text, textime
     * @access public
     *
     */
    public $url;

    /**
     *
     * @var boolean $_ok Whether or not it is OK to parse as disqus is installed
     * @access private
     *
     */
    private $_ok;

    /**
     *
     * Constructor for the DISQUS filter
     *
     * @return void
     * @access public
     *
     */
    public function init()
    {
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Instantiate the disqus elements class
        if ($this->_checkInstalled()) {
            //Get an instance of the config object
            $objConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $disqusEnabled = $objConfig->getValue('mod_disqus_enabled', 'disqus');
            if ($disqusEnabled !=='FALSE') {
                $this->disqusUser = $objConfig->getValue('mod_disqus_defaultuser', 'disqus');
                $this->objDq = $this->getObject('disquselems', 'disqus');
                // Get an instance of the params extractor
                $this->objExpar = $this->getObject("extractparams", "utilities");
                $this->_ok=TRUE;
            } else {
                $this->_ok=FALSE;
            }
        } else {
            $this->_ok=FALSE;
        }
    }

    /**
    *
    * Method to parse the string. Note the ? in the regex is
    * important to enable the multiline feature, else it greedy
    *
    * @param  string $str The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
        // Filter regex to match the filter
        preg_match_all('/(\\[DISQUS:)(.*?)(\\])/iusm', $txt, $results);
       	$counter = 0;
       	foreach ($results[2] as $item) {
            // Parse for the parameters.
            $str = trim($results[2][$counter]);
            // The whole match must be replaced.
            $replaceable = $results[0][$counter];
            if ($this->_ok) {
                $ar = $this->objExpar->getArrayParams($str, ",");
                $this->setupPage();
                $url = $this->url;
                unset($this->url);
                $disqusDiv = md5($url);
                $replacement = $this->_getDisqus($url, $disqusDiv);
            } else {
                $replacement = $results[0][$counter] . $this->_errNotInst();
            }
        	$txt = str_replace($replaceable, $replacement, $txt);
            unset($replacement);
        	$counter++;
        }

        return $txt;
    }

    /**
     * create notification message to display on filter when Disqus is not installed
     * @return void
     */
    private function _errNotInst()
    {
        return "<br /><span class='error'>"
           . $this->objLanguage->languageText('mod_filters_error_disqusnotinst', 'filters')
           . "</span><br />";
    }

    /**
    *
    * Method to set up the parameter / value pairs for th efilter
    * @access public
    * @return VOID
    *
    */
    private function setUpPage()
    {
        //Get url
        if (isset($this->objExpar->url)) {
            $this->url = urldecode($this->objExpar->url);
        } else {
            $this->url=NULL;
        }
    }

    /**
    *
    * Checks if the required module is instaled
    * @access private
    * @return boolean TRUE|FALSE
    *
    */
    private function _checkInstalled()
    {
        //Instantiate the modules class to check if youtube is registered
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the disqus API module is registered and set a param

        return $objModule->checkIfRegistered('disqus', 'disqus');
    }

    private function _getDisqus(& $url, & $disqusDiv)
    {
        $ret = $this->objDq->getDiv($disqusDiv)
          . $this->objDq->getInlineJs($url, $disqusDiv)
          . $this->objDq->getEmbedJs($this->disqusUser);

        //return nl2br(htmlentities($ret));

        return $ret;
    }

}
?>
<?php
/**
 *
 * Load the skin Javascript
 *
 * Load the skin Javascript into the page header.
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
 * @package   skin
 * @author    Derek Keats <derek.keats@wits.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id
 * @link      http://avoir.uwc.ac.za
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
 * Load the skin Javascript
 *
 * Load the skin Javascript into the page header.
*
* @package   skin
* @author    Derek Keats <derek.keats@wits.ac.za>
*
*/
class skinjavascript extends object
{
    /**
     * Instance of the mischtml class in htmlelements.
     *
     * @access protected
     * @var    object
     */
    protected $objMiscHTML;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objSysConfig;

    /**
    *
    * Intialiser for the skin chooser
    * @access public
    *
    */
    public function init()
    {
        $this->objMiscHTML = $this->getObject('mischtml', 'htmlelements');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    }

    /**
    * Method to return the common JavaScript that is used and needs to go into the page templates
    * This loads Prototype and JavaScript into the page templates
    *
    * @param string $mime Mimetype of Page - Either text/html or application/xhtml+xml
    * @param string array $headerParams List of items that needs to go into the header of the page
    * @param string array $bodyOnLoad List of items that needs to go into the bodyOnLoad section of the page
    *
    */
    public function loadAll($mime='text/html', $headerParams=NULL, $bodyOnLoad=NULL)
    {
        if ($mime != 'application/xhtml+xml') {
            $mime = 'text/html';
        }
        $str = '';
        $str .= $this->getScriptaculous($mime);
        $str .= $this->getJQuery();
        $str .= $this->getChromeFrame();
        $str .= $this->getHeaderParams($headerParams);
        $str .= $this->getBodyParams($bodyOnLoad);
        return $str;
    }

    /**
    *
    * Insert the scriptaculous library into the page head
    *
    * @param string $mime Mimetype of Page
    * @return string The rendered javascript
    * @access public
    *
    */
    public function getScriptaculous($mime)
    {
        $supressPrototype = $this->getVar('SUPPRESS_PROTOTYPE', false);
        if (!$supressPrototype){
            // Add Scriptaculous
            $scriptaculous = $this->getObject('scriptaculous', 'htmlelements');
            return $scriptaculous->show($mime);
        } else {
            return NULL;
        }
    }

    /**
    *
    * Insert the jQuery library into the page head
    *
    * @return string The rendered javascript
    * @access public
    *
    */
    public function getJQuery()
    {
        $supressJQuery = $this->getVar('SUPPRESS_JQUERY', false);
        $jQueryVersion = $this->getVar('JQUERY_VERSION', '1.2.3');
        if (!$supressJQuery){
            $jquery = $this->getObject('jquery', 'jquery');
            $jquery->setVersion($jQueryVersion);
            return $jquery->show();
        } else {
            return NULL;
        }
    }

    /**
     * Insert the Chrome Frame Metadata and JavaScript into the document head.
     *
     * @access protected
     * @return string The necessary HTML5 markup.
     * @see    http://www.chromium.org/developers/how-tos/chrome-frame-getting-started
     */
    public function getChromeFrame()
    {
        $enable              = $this->objSysConfig->getValue('chrome_frame', 'skin');
        $suppressChromeFrame = $this->getVar('SUPPRESS_CHROME_FRAME', FALSE);
        $suppressJQuery      = $this->getVar('SUPPRESS_JQUERY', FALSE);

        if ($enable && !$suppressChromeFrame) {
            $html = $this->objMiscHTML->httpEquiv('X-UA-Compatible', 'chrome=1');
            if ($suppressJQuery) {
                $html .= $this->objMiscHTML->importScript('http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js');

                $this->appendArrayVar('bodyOnLoad', 'CFInstall.check({mode:"overlay"});');
            } else {
                $html .= $this->getJavascriptFile('chromeframe.js', 'skin');
            }
        } else {
            $html = '';
        }

        return $html;
    }

    /**
    *
    * Insert the headerparams into the page head
    *
    * @var string #headerParams The header parameters
    * @return string The rendered header parameters
    * @access public
    *
    */
    public function getHeaderParams($headerParams=NULL)
    {
        if ($headerParams == NULL) {
            $headerParams = $this->getVar('headerParams');
        }

        if (is_array($headerParams)) {
            $headerParams = array_unique($headerParams);
            $ret ="";
            foreach ($headerParams as $headerParam) {
                $ret .= $headerParam."\n\n";
            }
            return $ret;
        } else {
            return NULL;
        }
    }

    /**
    *
    * Insert the body parameters
    *
    * @return string The rendered body params
    * @access public
    *
    */
    public function getBodyParams($bodyOnLoad=NULL)
    {
        if ($bodyOnLoad == NULL) {
            $bodyOnLoad = $this->getVar('bodyOnLoad');
        }

        if (is_array($bodyOnLoad)) {
            $str = '<script type="text/javascript">'
              . 'window.onload = function() {'."\n";
            foreach ($bodyOnLoad as $bodyParam) {
                $str .= '   '.$bodyParam."\n";
            }
            $str .= '}
</script>'."\n\n";
            return $str;
        } else {
            return NULL;
        }
        
    }
}
?>

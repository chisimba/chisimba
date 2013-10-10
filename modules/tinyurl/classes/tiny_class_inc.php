<?php
/**
 *
 *  An interface for TinyURL
 *
 * PHP version 5.1.0+
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
 * @package   tinyurl
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * An interface for TinyURL
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package tinyurl
 *
 */
class tiny extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;

    public $objCurl;

    /**
     * Location of TinyURL API
     *
     * @var         string      $api            URL of TinyURL API
     * @static
     */
    protected $api = 'http://tinyurl.com/api-create.php';


    /**
     * Constructor

     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objCurl = $this->getObject('curlwrapper', 'utilities');
    }

    /**
     * Create a TinyURL
     *
     * @access      public
     * @param       string      $destination    The URL to make tiny
     * @return      string
     * @static
     */
    public function create($destination) {
        $uri = $this->api . '?url=' . $destination;
        $this->objCurl->initializeCurl($uri);
        // set some options
        $this->objCurl->setProxy();
        $this->objCurl->setopt(CURLOPT_URL, $uri);
        $this->objCurl->setopt(CURLOPT_HEADER, false);
        $this->objCurl->setopt(CURLOPT_RETURNTRANSFER, true);
        $this->objCurl->setopt(CURLOPT_USERAGENT, 'Chisimba');

        $result = $this->objCurl->getUrl();

        if ($result == FALSE) {
            // throw a tantrum
            throw new customException("curl crapped out");
        }

        $this->objCurl->closeCurl();

        if (!preg_match('/^https?:\/\//i', $result)) {
            throw new customException('Unexpected response from the API');
        }

        return $result;
    }

    /**
     * Do a reverse lookup of a TinyURL
     *
     * @access      public
     * @param       string      $url        TinyURL to look up
     * @return      string      The destination URL of the TinyURL
     * @static
     */
    public function lookup($url) {
        if (!preg_match('/^http:\/\/tinyurl.com\/[a-z0-9]+/i', $url)) {
            throw new customException('Invalid TinyURL ' . $url);
        }
        $this->objCurl->initializeCurl($url);
        // set some options
        $this->objCurl->setProxy();
        $this->objCurl->setopt(CURLOPT_URL, $url);
        $this->objCurl->setopt(CURLOPT_FOLLOWLOCATION, true);
        $this->objCurl->setopt(CURLOPT_HEADER, true);
        $this->objCurl->setopt(CURLOPT_NOBODY, true);
        $this->objCurl->setopt(CURLOPT_RETURNTRANSFER, true);
        $this->objCurl->setopt(CURLOPT_USERAGENT, 'Chisimba');

        $result = $this->objCurl->getUrl($url);

        $this->objCurl->closeCurl();

        $m = array();
        if (preg_match("/Location: (.*)\n/", $result, $m)) {
            if (isset($m[1]) && preg_match('/^https?:\/\//i', $m[1])) {
                return trim($m[1]);
            }
        }

        throw new customException('No redirection found');
    }
}
?>
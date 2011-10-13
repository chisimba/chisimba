<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * Chisimba's ping functions
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Charl Mert <charl.mert@gmail.com>
 * @copyright GNU/GPL AVOIR/UWC 2009
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class ping extends object
{
	/**
	 * @var boolean $isLocal member to store the bool to indicate weather the server
	 * is local or not.
	 * @access protected
	 */
	protected $isLocal;

	/**
	 * Constructor
	 */
	public function init()
	{
		$this->isLocal = false;
	}

	/**
	 * Determine if the requesting client is making a call to a web server on the same machine
	 * @return boolean TRUE if it is and FALSE if not
	 * @access public
	 */
	public function isLocal()
	{
		$isLocal = FALSE;
		$hostName = $_SERVER['HTTP_HOST'];

		if ($hostName == 'localhost' ||
				preg_match ('/^127\..*/isU', $hostName) ) {
			$isLocal = TRUE;
		}

		$this->isLocal = $isLocal;

		return $isLocal;
	}


	/**
	 * Determine if the web server at the given hostname can be reached.
	 * @return boolean TRUE if it can and FALSE if not
	 * @access public
	 */
	public function webPing($url) {
		//Ensuring the scheme is prepended
		if (!preg_match('/.*http\:\/\/.*/isU', $url)) {
			$url = 'http://' . $url;
		}

		if (!@fopen($url, 'r')) {
			return FALSE;
		} else {
			return TRUE;
		}
	}


}

<?php

/**
 * Comet implementation for Chisimba
 *
 * Comet is....
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
 * @category  chisimba
 * @package   comet
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id:  $
 * @link      http://avoir.uwc.ac.za
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
ini_set("max_execution_time", -1);
// end security check


/**
 * Comet class
 *
 * Comet is...
 *
 * @category  chisimba
 * @package   comet
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   gpl
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class comet extends controller
{

	/**
     * Description for public
     * @var    unknown
     * @access public
     */
	public $objLanguage;

	/**
     * Description for public
     * @var    unknown
     * @access public
     */
	public $objConfig;

	/**
     * Constructor method to instantiate objects and get variables
     */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->objConfig = $this->getObject('altconfig', 'config');
			require_once($this->getResourcePath('Phico.class.php', 'comet'));
		}
		catch(customException $e)
		{
			customException::cleanUp();
			exit;
		}

	}

	/**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
	public function dispatch($action = Null)
	{
		switch ($action) {
			default:
			    $js = $this->getParam('JavaScript');
			    $ph = $this->getParam('Phico');

				if(isset($js)) {
                    Phico::JavaScript(true, $this->getResourcePath('Phico.js', 'comet'));
				}
                elseif(isset($ph)) {
                    Phico::init($ph);
                    Phico::send('Hello, Phico is ready to tell you what time is it.');
                    sleep(1);
                    while(true) {
                        Phico::send('GMT '.gmdate('Y-m-d H:i:s'));
                        sleep(1);
                    }
                }
                else {
                    $script = $this->getJavascriptFile('Phico.js', 'comet').'
                        <script>
                        onload = function(){
                            var i = 0,
                            p = Phico.init("?PHP_SELF", function(data) {
                                document.getElementById("time").innerHTML = data;
                                // comment this line to test "infinite" connection
                                if(++i === 20) p.disconnect();
                            });
                            setTimeout(function(){
                                p.connect();
                            }, 10);
                        };
                        </script>
                        <div id="time"></div>';
                }
		        break;

			case 'donothing':

			    break;
	     }
     }

     public function requiresLogin() {
         return FALSE;
     }
}
?>
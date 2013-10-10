<?php
/**
 *
 * My blockview
 *
 * Create and display personal templates stored in the users upload directory.
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
 * @package   myblockview
 * @author    Derek Keats derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbmyblockview.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
* My blockview
*
* Create and display personal templates stored in the users upload directory.
*
* @author Derek Keats
* @package myblockview
*
*/
class mytemplates extends object
{

    public $templateDir;
    public $objUser;

    /**
    *
    * Intialiser for the myblockview database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig=$this->newObject('altconfig','config');
    }

    /**
    *
    * Return a template that is specified in the querystring, defaulting to
    * the directory templates/default
    *
    * @return string The template content (or boolean FALSE if the file is not found)
    *
    */
    public function getMyTemplate()
    {
        $templateName = $this->getParam('template', "default");
        if ($templateDir = $this->getPersonalTemplateDir($templateName)) {
            $templateFile = $templateDir . 'template.txt';
            if (file_exists($templateFile)) {
                $pageContent = file_get_contents($templateFile);
                return $pageContent;
            }
        }
        return FALSE;
    }

    /**
     *
     * Get the default personal template directory for the logged-in user
     *
     * @return string The user template directory
     * @access private
     *
     */
    private function getPersonalTemplateDir($templateName)
    {
        try {
            if ($this->objUser->isLoggedIn()) {
                // Check for cached value
                if (isset($this->templateDir)) {
                    $templateDir = $this->templateDir;
                } else {
                    $templateDir = $this->objConfig->getSiteRootPath()
                      . 'usrfiles/users/' . $this->objUser->userId()
                      . '/templates/' . $templateName . "/";
                    $this->templateDir = $templateDir;
                }
            } else {
                $templateDir = FALSE;
            }
            return $templateDir;
        } catch(Exception $e) {
            throw customException($e->message());
            exit();
        }
    }
}
?>
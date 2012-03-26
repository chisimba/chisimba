<?php
/**
 *
 * Installer class for the module
 * 
 * The installer class for the module creates a 
 * group called SchoolUserManagers
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
 * @package   wurfl
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-07-28 09:35:42Z charlvn $
 * @link      http://chisimba.com/
 * @see       http://wurfl.sourceforge.net/
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
 * Installer class for the module
 * 
 * The installer class for the module creates a 
 * group called Usermanagers
 * 
 * @category  Chisimba
 * @package   schoolregisterusers
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2010 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-12-31 16:12:33Z dkeats $
 * @link      http://chisimba.com/
 * @see       http://wurfl.sourceforge.net/
 */
class systext_installscripts extends dbtable
{
    /**
     * Instance of the altconfig class in the config module.
     *
     * @access private
     * @var    object
     */
    private $objAltConfig;

    /**
     * The object property initialiser.
     *
     * @access public
     */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->PKId();
        $this->facet = $this->getObject('systext_facet', 'systext');
    }

    /**
     * The actions to perform after installation of the module.
     *
     * @access public
     * @return void
     * 
     */
    public function postinstall($version)
    {
        if ($version == '1.9')
        {
            $systemId = $this->facet->addSystemType('eLearningSchools', $this->userId);
            $this->facet->addAbstractText($systemId, 'init_1', 'course', $this->userId);
            $this->facet->addAbstractText($systemId, 'init_2', 'courses', $this->userId);
            $this->facet->addAbstractText($systemId, 'init_3', 'educator', $this->userId);
            $this->facet->addAbstractText($systemId, 'init_4', 'educators', $this->userId);
            $this->facet->addAbstractText($systemId, 'init_5', 'school', $this->userId);
            $this->facet->addAbstractText($systemId, 'init_6', 'schools', $this->userId);
            $this->facet->addAbstractText($systemId, 'init_7', 'student', $this->userId);
            $this->facet->addAbstractText($systemId, 'init_8', 'students', $this->userId);
            $textArray = array();
            $textArray['level'] = 'grade';
            $textArray['levels'] = 'grades';
            $textArray['subject'] = 'subject';
            $textArray['subjects'] = 'subjects';
            $textArray['grouping'] = 'class';
            $textArray['groupings'] = 'classes';
            foreach ($textArray as $text => $abstract)
            {
                $textId = $this->facet->addTextItem($text, $this->userId);
                $this->facet->addAbstractText($systemId, $textId, $abstract, $this->userId);
            }
        }
    }

}
?>
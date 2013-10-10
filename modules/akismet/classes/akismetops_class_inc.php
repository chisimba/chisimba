<?php

/**
 * Facade class to the akismet library.
 * 
 * Library methods for interacting with the akismet web service.
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
 * @package   akismet
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: akismetops_class_inc.php 18890 2010-09-05 21:52:48Z charlvn $
 * @link      http://avoir.uwc.ac.za/
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
 * Facade class to the akismet library.
 * 
 * Library methods for interacting with the akismet web service.
 * 
 * @category  Chisimba
 * @package   akismet
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: akismetops_class_inc.php 18890 2010-09-05 21:52:48Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class akismetops extends object
{
    /**
     * Instance of the Akismet class.
     *
     * @access private
     * @var    object
     */
    private $objAkismet;

    /**
     * Instance of the altconfig class of the config module.
     *
     * @access private
     * @var    object
     */
    private $objAltConfig;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objSysConfig;

    /**
     * Retrieves and sets the configuration.
     *
     * @access public
     */
    public function init()
    {
        include $this->getResourcePath('Akismet.class.php');

        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

        $url = $this->objAltConfig->getsiteRoot();
        $key = $this->objSysConfig->getValue('key', 'akismet');

        $this->objAkismet = new Akismet($url, $key);
    }

    /**
     * Rates a submission as ham or spam.
     *
     * @access public
     * @param  string  $content The content.
     * @param  string  $author  The name of the author.
     * @param  string  $uri     The author's URI.
     * @param  string  $email   The author's email address.
     * @return boolean True if spam, false if ham.
     */
    public function isSpam($content, $author=NULL, $uri=NULL, $email=NULL)
    {
        $this->objAkismet->setCommentContent($content);
        $this->objAkismet->setCommentAuthor($author);
        $this->objAkismet->setCommentAuthorURL($uri);
        $this->objAkismet->setCommentAuthorEmail($email);
        
        return $this->objAkismet->isCommentSpam();
    }
}

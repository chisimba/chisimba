<?php

/**
 * SIOC implementation for Chisimba
 *
 * SIOC
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
 * @package   sioc
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
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
 * Sioc class
 *
 * SIOC is...
 *
 * @category  chisimba
 * @package   sioc
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   gpl
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class siocexport extends controller
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

	public $objSiocMaker;

	/**
     * Constructor method to instantiate objects and get variables
     */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->objConfig = $this->getObject('altconfig', 'config');
            $this->objSiocMaker = $this->getObject('siocmaker');
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
			    $siocData = array();
			    $siocData['title'] = "my blog";
			    $siocData['url'] = "http://www.somesite.com/index.php?module=blog";
			    $siocData['sioc_url'] = "http://www.somesite.com/index.php?module=blog#";
			    $siocData['encoding'] = "UTF-8";
			    $siocData['generator'] = "http://www.somesite.com/index.php?module=siocexport&action=dumprdf";

			    // make the site data
			    $siteData = array();
			    $siteData['url'] = "http://www.somesite.com/index.php?module=blog";
			    $siteData['name'] = "My blog and stuff";
			    $siteData['description'] = "test site data";

			    $fora = array();
			    $fora[0]['id'] = '1';
			    $fora[0]['url'] = "http://www.somesite.com/index.php?module=blog&userid=1";

			    $users = array();
			    $user[0]['id'] = 1;
			    $user[0]['url'] = "http://www.somesite.com/index.php";

			    $this->objSiocMaker->setSite($siteData);
			    $this->objSiocMaker->setFora($fora);
			    $this->objSiocMaker->setUsers($users);

			    $this->objSiocMaker->createForum(1, "http://www.somesite.com/index.php?module=blog&userid=1", 1, 'test', "A test forum description");

			    $posts = array();
			    $posts[0]['id'] = '001';
			    $posts[0]['url'] = 'http://www.somesite.com/index.php?module=blog&postid=1';
			    $posts[1]['id'] = '002';
			    $posts[1]['url'] = 'http://www.somesite.com/index.php?module=blog&postid=2';

			    $this->objSiocMaker->forumPosts($posts);

			    // user
			    $user = array();
			    $user['id'] = 'init_1';
			    $user['url'] = $this->uri('');
			    $user['name'] = "Paul Scott";
			    $user['email'] = 'pscott@uwc.ac.za';
			    $user['homepage'] = $this->uri('');
			    $user['role'] = "admin";

			    $this->objSiocMaker->createUser($user);

			    // post
			    $this->objSiocMaker->createPost($this->uri(''), 'test post', strip_tags('some test content'), 'some content', date('r'), $updated = "", $tags = array(), $links = array());

			    echo $this->objSiocMaker->dumpSioc($siocData);
                die();
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
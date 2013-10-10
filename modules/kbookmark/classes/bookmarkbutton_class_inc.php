<?php
/**
 * This file contains the button class which is used to generate
 * HTML button elements for forms
 *
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
/**
 * @category  Chisimba
 * @package   kbookmark
 * @author    Qhamani Fenama <qfenama@uwc.ac.za/qfenama@gmail.com>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: bookmark_button_class_inc.php 16438 2010-01-22 15:38:42Z $
 * @link      http://avoir.uwc.ac.za
 *
 */
class bookmarkbutton extends object
{

	public $objExtJS;


	public $objSysConfig;


	public $ext;


	public function init()
	{
		$this->objSysConfig  = $this->getObject('altconfig','config');
		$this->objExtJS = $this->getObject('extjs','ext');
		$this->objExtJS->show();
		$this->objUser = $this->getObject('user', 'security');
	}

    public function bookmark_button($title=null, $url = null, $description = null, $tags=null)
    {
		$this->title = $title;
        $this->url = $url;
        $this->description = $description;
        $this->tags = $tags;
		$this->appendArrayVar('headerParams', '
			    	<script type="text/javascript">	
						var baseuri = "'.$this->objSysConfig->getsiteRoot().'index.php";
						var defId = "root'.$this->objUser->userId().'";        		
			    		var vtitle = "'.$this->title.'";
						var vurl = "'.$this->url.'";
						var vdescription = "'.$this->description.'";
						var vtags = "'.$this->tags.'";
						var button = true;
			    	</script>');
		$ext = '<link rel="stylesheet" href="'.$this->getResourceUri('iconcss.css', 'kbookmark').'" type="text/css" />';
		$ext .= $this->getJavaScriptFile('bookmark.js', 'kbookmark');
		$ext .= $this->getJavaScriptFile('bookmarkbtn.js', 'kbookmark');
		$this->appendArrayVar('headerParams', $ext);
    }
    
    
    public function show()
    {
		$str =  '<div id="btn"></div>';
        return $str;
    }
}

?>

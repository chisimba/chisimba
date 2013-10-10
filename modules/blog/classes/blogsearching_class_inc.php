<?php
/**
 * Class to handle blog elements (searching).
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
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
 * @version    $Id: blogsearching_class_inc.php 16509 2010-01-26 17:33:06Z dkeats $
 * @package    blog
 * @subpackage blogops
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
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
 * Class to handle blog elements (searching)
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @category  Chisimba
 * @package   blog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: blogsearching_class_inc.php 16509 2010-01-26 17:33:06Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class blogsearching extends object
{
    /**
     * Description for public
     *
     * @var    mixed
     * @access public
     */
    public $objConfig;
    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init() 
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objDbBlog = $this->getObject('dbblog');
            $this->loadClass('href', 'htmlelements');
            $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->showfullname = $this->sysConfig->getValue('show_fullname', 'blog');
            $this->objUser = $this->getObject('user', 'security');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
        if (!extension_loaded("imap")) {
            $this->mail2blog = FALSE;
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $term Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    public function quickSearch($term) 
    {
        $ret = $this->objDbBlog->quickSearch($term);
        return $ret;
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  boolean $featurebox Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function searchBox($featurebox = TRUE) 
    {
        $this->loadClass('textinput', 'htmlelements');
        $qseekform = new form('qseek', $this->uri(array(
            'action' => 'blogsearch',
        )));
        $qseekform->addRule('searchterm', $this->objLanguage->languageText("mod_blog_phrase_searchtermreq", "blog") , 'required');
        $qseekterm = new textinput('searchterm');
        $qseekterm->size = 15;
        $qseekform->addToForm($qseekterm->show());
        $this->objsTButton = &new button($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setValue($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setIconClass("search");
        $this->objsTButton->setToSubmit();
        $qseekform->addToForm($this->objsTButton->show());
        $qseekform = $qseekform->show();
        if ($featurebox == FALSE) {
            return $qseekform;
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_qseek", "blog") , $this->objLanguage->languageText("mod_blog_qseekinstructions", "blog") . "<br />" . $qseekform);
            return $ret;
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  array  $searchres Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
    public function displaySearchResults($searchres) 
    {
        $res = NULL;
        if (empty($searchres)) {
            $res.= "<hr>";
            $res.= "<h1>" . $this->objLanguage->languageText("mod_blog_noresultsfound", "blog") . "</h1>";
            return $res;
        } else {
            $res.= "<h3>" . $this->objLanguage->languageText("mod_blog_searchresults", "blog") . "</h3><br />";
        }
        foreach($searchres as $results) {
            if ($this->showfullname == "FALSE") {
                $blogger = $this->objUser->userName($results['userid']);
            } else {
                $blogger = $this->objUser->fullName($results['userid']);
            }
            $image = $this->objUser->getUserImage($results['userid']);
            $link = new href($this->uri(array(
                'module' => 'blog',
                'action' => 'viewsingle',
                'postid' => $results['id']
            )) , $results['post_title']);
            $teaser = $results['post_excerpt'] . "<br />";
            // pull together a table
            $srtable = $this->newObject('htmltable', 'htmlelements');
            $srtable->cellpadding = 2;
            // set up the header row
            $srtable->startHeaderRow();
            $srtable->addHeaderCell('');
            $srtable->addHeaderCell('');
            $srtable->endHeaderRow();
            $srtable->startRow();
            $srtable->addCell("<strong>" . $blogger . "</strong>" . "<br />" . $image);
            $srtable->addCell("<br />" . $link->show() . "<br />" . $teaser);
            $srtable->endRow();
            $res.= $srtable->show() . "<br /><hr><br />";
        }
        return $res;
    }
}
?>
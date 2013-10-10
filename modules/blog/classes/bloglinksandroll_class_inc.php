<?php
/**
 * Class to handle blog elements (links and blogroll).
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
 * @version    $Id: bloglinksandroll_class_inc.php 16797 2010-02-13 15:06:27Z dkeats $
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
 * Class to handle blog elements (links and blogroll)
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @category  Chisimba
 * @package   blog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: bloglinksandroll_class_inc.php 16797 2010-02-13 15:06:27Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class bloglinksandroll extends object
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
            die($e);
        }
        if (!extension_loaded("imap")) {
            $this->mail2blog = FALSE;
        }
    }
    /**
     * Methods to control blog links and blogrolls...
     *
     * @param integer $userid     The user id
     * @param boolean $featurebox The featurebox switch
     *
     * @return string $str The rendered output
     */
    public function showBlinks($userid, $featurebox = FALSE) 
    {
        $this->loadClass('href', 'htmlelements');
        // grab all of the links for the user
        $links = $this->objDbBlog->getUserLinksonly($userid);
        if (empty($links)) {
            return NULL;
        }
        $str = NULL;
        foreach($links as $link) {
            $hr = new href($link['link_url'], $link['link_name'], 'target="' . $link['link_target'] . '" alt="' . $link['link_description'] . '"');
            $str.= "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $hr->show() . "<br /></span>";
        }
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_links", "blog") , $str, 'bloglinks', 'default');
            return $ret;
        } else {
            return $str;
        }
    }
    /**
     * Method to display a link categories box
     *
     * @param  array  $linkcats
     * @param  bool   $featurebox
     * @return string
     */
    public function showLinkCats($linkcats, $featurebox = FALSE) 
    {
        $this->objUser = &$this->getObject('user', 'security');
        // cycle through the link categories and display them
        foreach($linkcats as $lc) {
            $ret = "<em>" . $lc['catname'] . "</em><br />";
            $linkers = $this->objDbBlog->getLinksCats($this->objUser->userid() , $lc['id']);
            if (!empty($linkers)) {
                $ret.= "";
                foreach($linkers as $lk) {
                    $ret.= "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">";
                    $alt = htmlentities($lk['link_description']);
                    $link = new href(htmlentities($lk['link_url']) , htmlentities($lk['link_name']) , "alt='{$alt}'");
                    $ret.= $link->show();
                    $ret.= "<br /></span>";
                }
            }
        }
        if ($featurebox == FALSE) {
            return $ret;
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            if (!isset($ret)) {
                $ret = NULL;
            }
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_linkcategories", "blog") , $ret);
            return $ret;
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param integer $userid     Parameter description (if any) ...
     * @param boolean $featurebox Parameter description (if any) ...
     *
     * @return string  Return description (if any) ...
     * @access public
     */
    public function showBroll($userid, $featurebox = FALSE) 
    {
        $this->loadClass('href', 'htmlelements');
        // grab all of the links for the user
        $links = $this->objDbBlog->getUserbroll($userid);
        if (empty($links)) {
            return NULL;
        }
        $str = NULL;
        foreach($links as $link) {
            $hr = new href($link['link_url'], $link['link_name'], 'target="' . $link['link_target'] . '" alt="' . $link['link_description'] . '"');
            $str.= "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $hr->show() ."<br /></span>";
        }
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_blogroll", "blog") , $str, 'blogroll', 'default');
            return $ret;
        } else {
            return $str;
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param boolean $featurebox Parameter description (if any) ...
     * @param string  $ldata      Parameter description (if any) ...
     *
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function editBlinks($featurebox = FALSE, $ldata = NULL) 
    {
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->objUser = $this->getObject('user', 'security');
        if ($ldata == NULL) {
            $lform = new form('addlink', $this->uri(array(
                'action' => 'addlink',
            )));
        } else {
            $ldata = $ldata[0];
            $lform = new form('addlink', $this->uri(array(
                'action' => 'linkedit',
                'mode' => 'edit',
                'id' => $ldata['id']
            )));
        }
        // add rules
        $lform->addRule('lurl', $this->objLanguage->languageText("mod_blog_phrase_lurlreq", "blog") , 'required');
        $lform->addRule('lname', $this->objLanguage->languageText("mod_blog_phrase_lnamereq", "blog") , 'required');
        // start a fieldset
        $lfieldset = $this->getObject('fieldset', 'htmlelements');
        $ladd = $this->newObject('htmltable', 'htmlelements');
        $ladd->cellpadding = 3;
        // url textfield
        $ladd->startRow();
        $lurllabel = new label($this->objLanguage->languageText('mod_blog_lurl', 'blog') . ':', 'input_lurl');
        $lurl = new textinput('lurl');
        $lurl->size = "56%";
        if (isset($ldata['link_url'])) {
            $lurl->setValue(htmlentities($ldata['link_url'], ENT_QUOTES));
        }
        $ladd->addCell($lurllabel->show());
        $ladd->addCell($lurl->show());
        $ladd->endRow();
        // name
        $ladd->startRow();
        $lnamelabel = new label($this->objLanguage->languageText('mod_blog_lname', 'blog') . ':', 'input_lname');
        $lname = new textinput('lname');
        $lname->size = '56%';
        if (isset($ldata['link_name'])) {
            $lname->setValue($ldata['link_name']);
        }
        $ladd->addCell($lnamelabel->show());
        $ladd->addCell($lname->show());
        $ladd->endRow();
        // description
        $ladd->startRow();
        $ldesclabel = new label($this->objLanguage->languageText('mod_blog_ldesc', 'blog') . ':', 'input_ldesc');
        $ldesc = new textarea('ldescription');
        $ldesc->setColumns(48);
        if (isset($ldata['link_description'])) {
            $ldesc->setValue($ldata['link_description']);
        }
        $ladd->addCell($ldesclabel->show());
        $ladd->addCell($ldesc->show());
        $ladd->endRow();
        // link target dropdown
        $ladd->startRow();
        $ltargetlabel = new label($this->objLanguage->languageText('mod_blog_ltarget', 'blog') . ':', 'input_ltarget');
        $ltarget = new dropdown('ltarget');
        $ltarget->extra = ' style="width:64%;" ';
        $ltarget->addOption('_blank', $this->objLanguage->languageText("mod_blog_linktarget_blank", 'blog'));
        $ltarget->addOption('_self', $this->objLanguage->languageText("mod_blog_linktarget_self", 'blog'));
        $ltarget->addOption('_parent', $this->objLanguage->languageText("mod_blog_linktarget_parent", 'blog'));
        $ltarget->addOption('_top', $this->objLanguage->languageText("mod_blog_linktarget_top", 'blog'));
        $ladd->addCell($ltargetlabel->show());
        $ladd->addCell($ltarget->show());
        $ladd->endRow();
        // link type dropdown
        $ladd->startRow();
        $ltypelabel = new label($this->objLanguage->languageText('mod_blog_ltype', 'blog') . ':', 'input_ltype');
        $ltype = new dropdown('ltype');
        $ltype->extra = ' style="width:64%;" ';
        $ltype->addOption('blogroll', $this->objLanguage->languageText("mod_blog_linktype_blogroll", 'blog'));
        $ltype->addOption('bloglink', $this->objLanguage->languageText("mod_blog_linktype_bloglink", 'blog'));
        $ladd->addCell($ltypelabel->show());
        $ladd->addCell($ltype->show());
        $ladd->endRow();
        // notes
        $ladd->startRow();
        $lnoteslabel = new label($this->objLanguage->languageText('mod_blog_lnotes', 'blog') . ':', 'input_lnotes');
        $lnotes = new textarea('lnotes');
        $lnotes->setColumns(48);
        if (isset($ldata['link_notes'])) {
            $lnotes->setValue($ldata['link_notes']);
        }
        $ladd->addCell($lnoteslabel->show());
        $ladd->addCell($lnotes->show());
        $ladd->endRow();
        // end off the form and add the buttons
        $this->objLButton = new button($this->objLanguage->languageText('word_save', 'system'));
        $this->objLButton->setIconClass("save");
        $this->objLButton->setValue($this->objLanguage->languageText('word_save', 'system'));
        $this->objLButton->setToSubmit();
        $lfieldset->addContent($ladd->show());
        $lform->addToForm($lfieldset->show());
        $lform->addToForm($this->objLButton->show());
        $lform = $lform->show();
        // ok now the table with the edit/delete for each rss feed
        $elinks = $this->objDbBlog->getUserLinks($this->objUser->userId());
        $eltable = $this->newObject('htmltable', 'htmlelements');
        $eltable->cellpadding = 3;
        // $eltable->border = 1;
        // set up the header row
        $eltable->startHeaderRow();
        $eltable->addHeaderCell($this->objLanguage->languageText("mod_blog_lhead_name", "blog"));
        $eltable->addHeaderCell($this->objLanguage->languageText("mod_blog_lhead_description", "blog"));
        $eltable->addHeaderCell($this->objLanguage->languageText("mod_blog_lhead_type", "blog"));
        $eltable->addHeaderCell('');
        $eltable->endHeaderRow();
        // set up the rows and display
        if (!empty($elinks)) {
            foreach($elinks as $rows) {
                $eltable->startRow();
                $linklink = new href($rows['link_url'], $rows['link_name']);
                $eltable->addCell($linklink->show());
                $eltable->addCell(($rows['link_description']));
                $eltable->addCell(($rows['link_type']));
                $this->objIcon = &$this->getObject('geticon', 'htmlelements');
                $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'addlink',
                    'mode' => 'edit',
                    'id' => $rows['id'],
                    'module' => 'blog'
                )));
                $delIcon = $this->objIcon->getDeleteIconWithConfirm($rows['id'], array(
                    'module' => 'blog',
                    'action' => 'deletelink',
                    'id' => $rows['id']
                ) , 'blog');
                $eltable->addCell($edIcon . $delIcon);
                $eltable->endRow();
            }
            // $eltable = $eltable->show();
            
        }
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_linkedit", "blog") , $lform . $eltable->show());
            return $ret;
        } else {
            return $lform . $eltable->show();
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid     Parameter description (if any) ...
     * @param  array   $check      Parameter description (if any) ...
     * @param  array   $page       Parameter description (if any) ...
     * @param  boolean $featurebox Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function pageEditor($userid, $check = NULL, $page = NULL, $featurebox = FALSE) 
    {
        // start a form object
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        // var_dump($page);
        if ($page != NULL) {
            $pform = new form('setpage', $this->uri(array(
                'action' => 'setpage',
                'mode' => 'editpage',
                'id' => $page[0]['id']
            )));
        } else {
            $pform = new form('setpage', $this->uri(array(
                'action' => 'setpage',
                'mode' => 'savepage',
            )));
        }
        $pfieldset = $this->newObject('fieldset', 'htmlelements');
        // $pfieldset->setLegend($this->objLanguage->languageText('mod_blog_setpage', 'blog'));
        $ptable = $this->newObject('htmltable', 'htmlelements');
        $ptable->cellpadding = 3;
        $ptable->startHeaderRow();
        $ptable->addHeaderCell('');
        $ptable->addHeaderCell('');
        $ptable->endHeaderRow();
        // page name field
        $ptable->startRow();
        $bnamelabel = new label($this->objLanguage->languageText('mod_blog_pagename', 'blog') . ':', 'input_pagename');
        $bname = new textinput('page_name');
        if (isset($page[0]['page_name'])) {
            $bname->setValue($page[0]['page_name']);
        }
        $bname->size = 59;
        // $bname->setValue();
        $ptable->addCell($bnamelabel->show());
        $ptable->addCell($bname->show());
        $ptable->endRow();
        // content page field
        $ptable->startRow();
        $bprflabel = new label($this->objLanguage->languageText('mod_blog_pagecontent', 'blog') . ':', 'input_pagecontent');
        $bprf = $this->newObject('htmlarea', 'htmlelements');
        $bprf->setName('page_content');
        if (isset($page[0]['page_content'])) {
            $bprf->setcontent($page[0]['page_content']);
        }
        $ptable->addCell($bprflabel->show());
        $ptable->addCell($bprf->show());
        $ptable->endRow();
        // put it all together and set up a submit button
        $pfieldset->addContent($ptable->show());
        $pform->addToForm($pfieldset->show());
        $this->objPButton = new button($this->objLanguage->languageText('word_save', 'system'));
        $this->objPButton->setIconClass("save");
        $this->objPButton->setValue($this->objLanguage->languageText('word_save', 'system'));
        $this->objPButton->setToSubmit();
        $pform->addToForm($this->objPButton->show());
        $pform = $pform->show();
        // ok now the table with the edit/delete for each rss feed
        $efeeds = $this->objDbBlog->getUserRss($this->objUser->userId());
        $ftable = $this->newObject('htmltable', 'htmlelements');
        $ftable->cellpadding = 3;
        // $ftable->border = 1;
        // set up the header row
        $ftable->startHeaderRow();
        $ftable->addHeaderCell($this->objLanguage->languageText("mod_blog_phead_name", "blog"));
        // $ftable->addHeaderCell($this->objLanguage->languageText("mod_blog_phead_description", "blog"));
        $ftable->addHeaderCell('');
        $ftable->endHeaderRow();
        // set up the rows and display
        if (!empty($check)) {
            foreach($check as $rows) {
                $ftable->startRow();
                $feedlink = new href($this->uri(array(
                    'action' => 'showpage',
                    'pageid' => $rows['id']
                )) , $rows['page_name'], 'target="_blank" alt="' . $rows['page_name'] . '"');
                $ftable->addCell($feedlink->show());
                // $ftable->addCell(htmlentities($rows['name']));
                $this->objIcon = &$this->getObject('geticon', 'htmlelements');
                $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'setpage',
                    'mode' => 'editpage',
                    'id' => $rows['id'],
                    'module' => 'blog'
                )));
                $delIcon = $this->objIcon->getDeleteIconWithConfirm($rows['id'], array(
                    'module' => 'blog',
                    'action' => 'deletepage',
                    'id' => $rows['id']
                ) , 'blog');
                $ftable->addCell($edIcon . $delIcon);
                $ftable->endRow();
            }
            // $ftable = $ftable->show();
            
        }
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_editpages", "blog") , $pform . $ftable->show());
            return $ret;
        } else {
            return $pform . $ftable->show();
        }
        // return $pform;
        
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid     Parameter description (if any) ...
     * @param  boolean $featurebox Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function showPages($userid, $featurebox = FALSE) 
    {
        $this->loadClass('href', 'htmlelements');
        // grab all of the links for the user
        $pages = $this->objDbBlog->getPages($userid);
        if (empty($pages)) {
            return NULL;
        }
        $str = NULL;
        foreach($pages as $page) {
            $link = $this->uri(array(
                'action' => 'showpage',
                'pageid' => $page['id']
            ));
            $hr = new href($link, $page['page_name'], ' alt="' . $page['page_name'] . '"');
            $str.= "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $hr->show() . "<br /></span>";
        }
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_pages", "blog") , $str, 'blogpages', 'default');
            return $ret;
        } else {
            return $str;
        }
    }
    
    /**
     * Method to display a link to all the blogs on the system
     * Setting $featurebox = TRUE will display the link in a block style featurebox
     *
     * @param  bool   $featurebox
     * @return string
     */
    public function showBlogsLink($featurebox = FALSE) 
    {
        // set up a link to the other users blogs...
        $oblogs = new href($this->uri(array(
            'action' => 'allblogs'
        )) , $this->objLanguage->languageText("mod_blog_viewallblogs", "blog") , NULL);
        // Link for siteblogs Added by Irshaad Hoodain
        $ositeblogs = new href($this->uri(array(
            'action' => 'siteblog'
        )) , $this->objLanguage->languageText("mod_blog_viewsiteblogs", "blog") , NULL);

        $defmodLink = new href($this->uri(array() , '_default') , $this->objLanguage->languageText("mod_blog_returntosite", "blog") , NULL);
        if ($featurebox == FALSE) {
            $ret = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
              . $oblogs->show() . "<br /></span>"
              . "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
              . $defmodLink->show() . "</span>";
        } else {
            $boxContent = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
              . $oblogs->show() . "<br /></span>";
            $boxContent.= "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
              . $defmodLink->show() . "<br /></span>";
            //
            // database abstraction object
            $this->objDbBlog = $this->getObject('dbblog');
            $postresults = $this->objDbBlog->getAllPosts($userid = 1, null);
            if (!$postresults == null) {
                $boxContent.= "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $ositeblogs->show() . "<br /></span>";
            }
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_otherblogs", "blog") , $boxContent);
        }
        return $ret;
    }
    
}
?>
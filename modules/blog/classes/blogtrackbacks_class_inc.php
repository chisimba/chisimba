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
 * @version    $Id: blogtrackbacks_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
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
 * @version   $Id: blogtrackbacks_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class blogtrackbacks extends object
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
     * Method to show the trackbacks in the trackback table to the user on a singleview post display
     *
     * @param  string $pid
     * @return string
     */
    public function showTrackbacks($pid) 
    {
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $tbs = $this->objDbBlog->grabTrackbacks($pid);
        // loop through the trackbacks and build a featurebox to show em
        if (empty($tbs)) {
            // shouldn't happen except on permalinks....?
            return $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_trackback4post", "blog") , "<em>" . $this->objLanguage->languageText("mod_blog_trackbacknotrackback", "blog") . "</em>");
        }
        $tbtext = NULL;
        foreach($tbs as $tracks) {
            // build up the display
            $tbtable = $this->newObject('htmltable', 'htmlelements');
            $tbtable->cellpadding = 2;
            // $tbtable->width = '80%';
            // set up the header row
            $tbtable->startHeaderRow();
            $tbtable->addHeaderCell('');
            $tbtable->addHeaderCell('');
            $tbtable->endHeaderRow();
            // where did it come from?
            $whofromhost = $tracks['tburl'];
            $link = new href(htmlentities($whofromhost) , htmlentities($whofromhost) , NULL);
            $whofromhost = $link->show();
            $blogname = stripslashes($tracks['blog_name']);
            // title and excerpt
            $title = stripslashes($tracks['title']);
            $excerpt = stripslashes($tracks['excerpt']);
            $tbtable->startRow();
            $tbtable->addCell($this->objLanguage->languageText("mod_blog_tbremhost", "blog"));
            $tbtable->addCell($whofromhost);
            $tbtable->endRow();
            $tbtable->startRow();
            $tbtable->addCell($this->objLanguage->languageText("mod_blog_tbblogname", "blog"));
            $tbtable->addCell($blogname);
            $tbtable->endRow();
            $tbtable->startRow();
            $tbtable->addCell($this->objLanguage->languageText("mod_blog_tbblogtitle", "blog"));
            $tbtable->addCell($title);
            $tbtable->endRow();
            $tbtable->startRow();
            $tbtable->addCell($this->objLanguage->languageText("mod_blog_tbblogexcerpt", "blog"));
            $tbtable->addCell($excerpt);
            $tbtable->endRow();
            // add in a delete option...
            $this->objIcon = &$this->getObject('geticon', 'htmlelements');
            $tbdelIcon = $this->objIcon->getDeleteIconWithConfirm($tracks['id'], array(
                'module' => 'blog',
                'action' => 'deletetb',
                'id' => $tracks['id'],
                'pid' => $pid
            ) , 'blog');
            $tbtext.= $tbtable->show() . $tbdelIcon;
            $tbtable = NULL;
        }
        $this->bbcode = $this->getObject('washout', 'utilities');
        $tbtext = $this->bbcode->parseText($tbtext);
        $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_trackback4post", "blog") , $tbtext);
        return $ret;
    }
    /**
     * Method to build the form to send a trackback to another blog
     *
     * @param  array  $postinfo
     * @return string
     */
    public function sendTrackbackForm($postinfo) 
    {
        // start a form object
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $stbform = new form('tbsend', $this->uri(array(
            'action' => 'tbsend'
        )));
        $tbfieldset = $this->newObject('fieldset', 'htmlelements');
        $tbfieldset->setLegend($this->objLanguage->languageText('mod_blog_sendtb', 'blog'));
        $tbtable = $this->newObject('htmltable', 'htmlelements');
        $tbtable->cellpadding = 3;
        $tbtable->startHeaderRow();
        $tbtable->addHeaderCell('');
        $tbtable->addHeaderCell('');
        $tbtable->endHeaderRow();
        // post url field
        $tbtable->startRow();
        $myurllabel = new label($this->objLanguage->languageText('mod_blog_posturl', 'blog') . ':', 'input_tbmyurl');
        $myurl = new textinput('url');
        $myurl->size = 59;
        $myurl->setValue($postinfo['url']);
        $tbtable->addCell($myurllabel->show());
        $tbtable->addCell($myurl->show());
        $tbtable->endRow();
        // post id field
        $tbtable->startRow();
        $pidlabel = new label($this->objLanguage->languageText('mod_blog_postid', 'blog') . ':', 'input_postid');
        $pid = new textinput('postid');
        $pid->size = 59;
        $pid->setValue($postinfo['postid']);
        $tbtable->addCell($pidlabel->show());
        $tbtable->addCell($pid->show());
        $tbtable->endRow();
        // blog_name field
        $tbtable->startRow();
        $bnlabel = new label($this->objLanguage->languageText('mod_blog_blogname', 'blog') . ':', 'input_tbbname');
        $bn = new textinput('blog_name');
        $bn->size = 59;
        $bn->setValue(stripslashes($postinfo['blog_name']));
        $tbtable->addCell($bnlabel->show());
        $tbtable->addCell($bn->show());
        $tbtable->endRow();
        // title field
        $tbtable->startRow();
        $titlabel = new label($this->objLanguage->languageText('mod_blog_posttitle', 'blog') . ':', 'input_tbtitle');
        $tit = new textinput('title');
        $tit->size = 59;
        $tit->setValue(stripslashes($postinfo['title']));
        $tbtable->addCell($titlabel->show());
        $tbtable->addCell($tit->show());
        $tbtable->endRow();
        // post excerpt field
        $tbtable->startRow();
        $exlabel = new label($this->objLanguage->languageText('mod_blog_postexcerpt', 'blog') . ':', 'input_tbexcerpt');
        $ex = new textarea('excerpt');
        $ex->setColumns(50);
        $ex->setValue(stripslashes($postinfo['excerpt']));
        $tbtable->addCell($exlabel->show());
        $tbtable->addCell($ex->show());
        $tbtable->endRow();
        // trackback url field
        $tbtable->startRow();
        $tburllabel = new label($this->objLanguage->languageText('mod_blog_trackbackurl', 'blog') . ':', 'input_tburl');
        $tburl = new textinput('tburl');
        $tburl->size = 59;
        $tbtable->addCell($tburllabel->show());
        $tbtable->addCell($tburl->show());
        $tbtable->endRow();
        // add some rules
        $stbform->addRule('url', $this->objLanguage->languageText("mod_blog_phrase_tburlreq", "blog") , 'required');
        $stbform->addRule('postid', $this->objLanguage->languageText("mod_blog_phrase_tbidreq", "blog") , 'required');
        $stbform->addRule('blog_name', $this->objLanguage->languageText("mod_blog_phrase_tbbnreq", "blog") , 'required');
        $stbform->addRule('title', $this->objLanguage->languageText("mod_blog_phrase_tbtitreq", "blog") , 'required');
        $stbform->addRule('excerpt', $this->objLanguage->languageText("mod_blog_phrase_tbexreq", "blog") , 'required');
        $stbform->addRule('tburl', $this->objLanguage->languageText("mod_blog_phrase_tbtburlreq", "blog") , 'required');
        // put it all together and set up a submit button
        $tbfieldset->addContent($tbtable->show());
        $stbform->addToForm($tbfieldset->show());
        $this->objTBButton = new button($this->objLanguage->languageText('mod_blog_word_sendtb', 'blog'));
        $this->objTBButton->setValue($this->objLanguage->languageText('mod_blog_word_sendtb', 'blog'));
        $this->objTBButton->setToSubmit();
        $stbform->addToForm($this->objTBButton->show());
        $stbform = $stbform->show();
        // bust out a featurebox for consistency
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_sendtb", "blog") , $stbform);
        return $ret;
    }
}
?>
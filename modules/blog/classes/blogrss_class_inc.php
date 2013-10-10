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
 * @version    $Id: blogrss_class_inc.php 20147 2010-12-31 12:30:20Z dkeats $
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
 * @version   $Id: blogrss_class_inc.php 20147 2010-12-31 12:30:20Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class blogrss extends object
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
     * Method to output a rss feeds box
     *
     * @param  string $url
     * @param  string $name
     * @return string
     */
    public function rssBox($url, $name) 
    {
        $url = urldecode($url);
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $objRss = $this->getObject('rssreader', 'feed');
        $objRss->parseRss($url);
        $head = $this->objLanguage->languageText("mod_blog_word_headlinesfrom", "blog");
        $head.= " " . $name;
        $content = "<ul>\n";
        foreach($objRss->getRssItems() as $item) {
            if (!isset($item['link'])) {
                $item['link'] = NULL;
            }
            @$content.= "<li><a href=\"" . htmlentities($item['link']) . "\">" . htmlentities($item['title']) . "</a></li>\n";
        }
        $content.= "</ul>\n";
        return $objFeatureBox->show($head, $content);
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $rssurl Parameter description (if any) ...
     * @param  string  $name   Parameter description (if any) ...
     * @param  unknown $feedid Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function rssRefresh($rssurl, $name, $feedid) 
    {
        // echo $rssurl; die();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $objRss = $this->getObject('rssreader', 'feed');
        $this->objConfig = $this->getObject('altconfig', 'config');
        // get the proxy info if set
        $objProxy = $this->getObject('proxyparser', 'utilities');
        $proxyArr = $objProxy->getProxy();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rssurl);
        // curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
            curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'] . ":" . $proxyArr['proxy_port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'] . ":" . $proxyArr['proxy_pass']);
        }
        $rsscache = curl_exec($ch);
        curl_close($ch);
        // var_dump($rsscache);
        // put in a timestamp
        $addtime = time();
        $addarr = array(
            'url' => $rssurl,
            'rsstime' => $addtime
        );
        // write the file down for caching
        $path = $this->objConfig->getContentBasePath() . "/blog/rsscache/";
        $rsstime = time();
        if (!file_exists($path)) {
            mkdir($path);
            chmod($path, 0777);
            $filename = $path . $this->objUser->userId() . "_" . $rsstime . ".xml";
            if (!file_exists($filename)) {
                touch($filename);
            }
            $handle = fopen($filename, 'wb');
            fwrite($handle, $rsscache);
        } else {
            $filename = $path . $this->objUser->userId() . "_" . $rsstime . ".xml";
            $handle = fopen($filename, 'wb');
            fwrite($handle, $rsscache);
        }
        // update the db
        $addarr = array(
            'url' => $rssurl,
            'rsscache' => $filename,
            'rsstime' => $addtime
        );
        // print_r($addarr);
        $this->objDbBlog->updateRss($addarr, $feedid);
        // echo $rsscache;
        $objRss->parseRss($rsscache);
        $rssbits = $objRss->getRssItems();
        if (empty($rssbits) || !isset($rssbits)) {
            $objRss2 = $this->newObject('rssreader', 'feed');
            // fallback to the known good url
            $objRss2->parseRss($rssurl);
            $head = $this->objLanguage->languageText("mod_blog_word_headlinesfrom", "blog");
            $head.= " " . $name;
            $content = "<ul>\n";
            foreach($objRss2->getRssItems() as $item) {
                if (!isset($item['link'])) {
                    $item['link'] = NULL;
                }
                @$content.= "<li><a href=\"" . htmlentities($item['link']) . "\">" . htmlentities($item['title']) . "</a></li>\n";
            }
            $content.= "</ul>\n";
            return $objFeatureBox->show($head, $content);
        } else {
            foreach($objRss->getRssItems() as $item) {
                if (!isset($item['link'])) {
                    $item['link'] = NULL;
                }
                @$content.= "<li><a href=\"" . htmlentities($item['link']) . "\">" . htmlentities($item['title']) . "</a></li>\n";
            }
            $content.= "</ul>\n";
        }
        return $objFeatureBox->show($head, $content);
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  boolean $featurebox Parameter description (if any) ...
     * @param  array   $rdata      Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function rssEditor($featurebox = FALSE, $rdata = NULL) 
    {
        // print_r($rdata);
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->objUser = $this->getObject('user', 'security');
        if ($rdata == NULL) {
            $rssform = new form('addrss', $this->uri(array(
                'action' => 'addrss'
            )));
        } else {
            $rdata = $rdata[0];
            $rssform = new form('addrss', $this->uri(array(
                'action' => 'rssedit',
                'mode' => 'edit',
                'id' => $rdata['id']
            )));
        }
        // add rules
        $rssform->addRule('rssurl', $this->objLanguage->languageText("mod_blog_phrase_rssurlreq", "blog") , 'required');
        $rssform->addRule('name', $this->objLanguage->languageText("mod_blog_phrase_rssnamereq", "blog") , 'required');
        // start a fieldset
        $rssfieldset = $this->getObject('fieldset', 'htmlelements');
        $rssadd = $this->newObject('htmltable', 'htmlelements');
        $rssadd->cellpadding = 3;
        // url textfield
        $rssadd->startRow();
        $rssurllabel = new label($this->objLanguage->languageText('mod_blog_rssurl', 'blog') . ':', 'input_rssuser');
        $rssurl = new textinput('rssurl');
        $rssurl->extra = ' style="width:100%;" ';
        if (isset($rdata['url'])) {
            $rssurl->setValue(htmlentities($rdata['url'], ENT_QUOTES));
            // $rssurl->setValue('url');
            
        }
        $rssadd->addCell($rssurllabel->show());
        $rssadd->addCell($rssurl->show());
        $rssadd->endRow();
        // name
        $rssadd->startRow();
        $rssnamelabel = new label($this->objLanguage->languageText('mod_blog_rssname', 'blog') . ':', 'input_rssname');
        $rssname = new textinput('name');
        $rssname->extra = ' style="width:100%;" ';
        if (isset($rdata['name'])) {
            $rssname->setValue($rdata['name']);
        }
        $rssadd->addCell($rssnamelabel->show());
        $rssadd->addCell($rssname->show());
        $rssadd->endRow();
        // description
        $rssadd->startRow();
        $rssdesclabel = new label($this->objLanguage->languageText('mod_blog_rssdesc', 'blog') . ':', 'input_rssname');
        $rssdesc = new textarea('description');
        $rssdesc->extra = ' style="width:100%;" ';
        if (isset($rdata['description'])) {
            // var_dump($rdata['description']);
            $rssdesc->setValue($rdata['description']);
        }
        $rssadd->addCell($rssdesclabel->show());
        $rssadd->addCell($rssdesc->show());
        $rssadd->endRow();
        // end off the form and add the buttons
        $this->objRssButton = &new button($this->objLanguage->languageText('word_save', 'system'));
        $this->objRssButton->setIconClass("save");
        $this->objRssButton->setValue($this->objLanguage->languageText('word_save', 'system'));
        $this->objRssButton->setToSubmit();
        $rssfieldset->addContent($rssadd->show());
        $rssform->addToForm($rssfieldset->show());
        $rssform->addToForm($this->objRssButton->show());
        $rssform = $rssform->show();
        // ok now the table with the edit/delete for each rss feed
        $efeeds = $this->objDbBlog->getUserRss($this->objUser->userId());
        $ftable = $this->newObject('htmltable', 'htmlelements');
        $ftable->cellpadding = 3;
        // $ftable->border = 1;
        // set up the header row
        $ftable->startHeaderRow();
        $ftable->addHeaderCell($this->objLanguage->languageText("mod_blog_fhead_name", "blog"));
        $ftable->addHeaderCell($this->objLanguage->languageText("mod_blog_fhead_description", "blog"));
        $ftable->addHeaderCell('');
        $ftable->endHeaderRow();
        // set up the rows and display
        if (!empty($efeeds)) {
            foreach($efeeds as $rows) {
                $ftable->startRow();
                $feedlink = new href($rows['url'], $rows['name']);
                $ftable->addCell($feedlink->show());
                // $ftable->addCell(htmlentities($rows['name']));
                $ftable->addCell(($rows['description']));
                $this->objIcon = &$this->getObject('geticon', 'htmlelements');
                $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'addrss',
                    'mode' => 'edit',
                    'id' => $rows['id'],
                    // 'url' => $rows['url'],
                    // 'description' => $rows['description'],
                    'module' => 'blog'
                )));
                $delIcon = $this->objIcon->getDeleteIconWithConfirm($rows['id'], array(
                    'module' => 'blog',
                    'action' => 'deleterss',
                    'id' => $rows['id']
                ) , 'blog');
                $ftable->addCell($edIcon . $delIcon);
                $ftable->endRow();
            }
            // $ftable = $ftable->show();
            
        }
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_importblog", "blog") , $imform . $ftable);
            return $ret;
        } else {
            return $rssform . $ftable->show();
        }
    }
    /**
     * Method to build and create the feeds options box
     *
     * @param      integer $userid
     * @param      bool    $featurebox
     * @return     string
     * @deprecated - old method
     */
    public function showFeeds($userid, $featurebox = FALSE, $showOrHide = 'none') 
    {
        $this->objUser = $this->getObject('user', 'security');
        $leftCol = NULL;
        if ($featurebox == FALSE) {
            $leftCol.= "<em>" . $this->objLanguage->languageText("mod_blog_feedheader", "blog") . "</em><br />";
        }
        // RSS2.0
        $rss2 = $this->getObject('geticon', 'htmlelements');
        $rss2->align = "top";
        $rss2->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array(
            'action' => 'feed',
            'format' => 'rss2',
            'userid' => $userid
        )) , $this->objLanguage->languageText("mod_blog_word_rss2", "blog"));
        $rss2feed = $rss2->show() . $link->show() . "<br />";
        // RSS0.91
        $rss091 = $this->getObject('geticon', 'htmlelements');
        $rss091->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array(
            'action' => 'feed',
            'format' => 'rss091',
            'userid' => $userid
        )) , $this->objLanguage->languageText("mod_blog_word_rss091", "blog"));
        $leftCol.= $rss091->show() . $link->show() . "<br />";
        // RSS1.0
        $rss1 = $this->getObject('geticon', 'htmlelements');
        $rss1->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array(
            'action' => 'feed',
            'format' => 'rss1',
            'userid' => $userid
        )) , $this->objLanguage->languageText("mod_blog_word_rss1", "blog"));
        $leftCol.= $rss1->show() . $link->show() . "<br />";
        // PIE
        $pie = $this->getObject('geticon', 'htmlelements');
        $pie->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array(
            'action' => 'feed',
            'format' => 'pie',
            'userid' => $userid
        )) , $this->objLanguage->languageText("mod_blog_word_pie", "blog"));
        $leftCol.= $pie->show() . $link->show() . "<br />";
        // MBOX
        $mbox = $this->getObject('geticon', 'htmlelements');
        $mbox->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array(
            'action' => 'feed',
            'format' => 'mbox',
            'userid' => $userid
        )) , $this->objLanguage->languageText("mod_blog_word_mbox", "blog"));
        $leftCol.= $mbox->show() . $link->show() . "<br />";
        // OPML
        $opml = $this->getObject('geticon', 'htmlelements');
        $opml->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array(
            'action' => 'feed',
            'format' => 'opml',
            'userid' => $userid
        )) , $this->objLanguage->languageText("mod_blog_word_opml", "blog"));
        $leftCol.= $opml->show() . $link->show() . "<br />";
        // ATOM
        $atom = $this->getObject('geticon', 'htmlelements');
        $atom->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array(
            'action' => 'feed',
            'format' => 'atom',
            'userid' => $userid
        )) , $this->objLanguage->languageText("mod_blog_word_atom", "blog"));
        $atomfeed = $atom->show() . $link->show() . "<br />";
        // Plain HTML
        $html = $this->getObject('geticon', 'htmlelements');
        $html->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array(
            'action' => 'feed',
            'format' => 'html',
            'userid' => $userid
        )) , $this->objLanguage->languageText("mod_blog_word_html", "blog"));
        // Comment RSS2.0
        $rss2comm = $this->getObject('geticon', 'htmlelements');
        $rss2comm->align = "top";
        $rss2comm->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array(
            'action' => 'commentfeed',
            'format' => 'rss2',
            'userid' => $userid
        )) , $this->objLanguage->languageText("mod_blog_word_commentrss2", "blog"));
        $rss2comments = $rss2comm->show() . $link->show() . "<br />";
        $leftCol.= $html->show() . $link->show() . "<br />";
        /* scriptaculous moved to default page template / no need to suppress XML*/
        // $this->setVar('pageSuppressXML',true);
        $objIcon = &$this->getObject('geticon', 'htmlelements');
        $objIcon->setIcon('toggle');
        // Does this use scriptaculous ---- ?????????@TODO Change to jQuery [[scriptaculous]]
        $str = "<a href=\"javascript:;\" onclick=\"Effect.toggle('feedmenu','slide');\">" . $this->objLanguage->languageText("mod_blog_moreoptions", "blog") . "</a>";
        // $objIcon->show() . "</a>";
        $topper = $rss2feed . $atomfeed;
        $str.= '<div id="feedmenu"  style="width:170px;overflow: hidden;display:' . $showOrHide . ';"> ';
        $str.= $leftCol;
        $str.= '</div>';
        if ($featurebox == FALSE) {
            return $str;
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_feedheader", "blog") , $topper . "<br />" . $str);
            return $ret;
        }
    }
}
?>
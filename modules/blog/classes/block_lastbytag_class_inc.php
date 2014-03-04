<?php
/**
 * Latest n blog posts by tag block
 *
 * Class to show latest n posts by tag in a block for addition to the main UI
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
 * @version    $Id: block_lastten_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @package    blog
 * @subpackage blocks
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2010 AVOIR
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
 * A block to return the last 10 blog posts
 *
 * @category  Chisimba
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @version   0.1
 * @copyright 2006-2007 AVOIR
 *
 */
class block_lastbytag extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * last ten posts box
     *
     * @var    object
     * @access public
     */
    public $display;
    /**
     * Blog operations class
     *
     * @var    object
     * @access public
     */
    public $blogOps;
    /**
     * Description for public
     *
     * @var    object
     * @access public
     */
    public $objLanguage;
    /**
     * Standard init function
     *
     * Instantiate language and user objects and create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objDbBlog = $this->getObject('dbblog');
        $this->objUser = $this->getObject('user', 'security');
        $this->title = $this->objLanguage->languageText("mod_blog_block_intheblogbytag", "blog");
        $this->objHumanizeDate = $this->getObject("translatedatedifference", "utilities");
        $this->expose = TRUE;
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('href', 'htmlelements');
        $blogLink = new link ($this->uri(NULL, 'blog'));
        $blogLink->link = $this->objLanguage->languageText('mod_blog_blogs', 'blog', 'Blogs');
        return $this->getLastData() 
          .'<p>' .$blogLink->show().'</p>';
    }

    public function getLastData($num=6)
    {
        $tag = $this->sysConfig->getValue('blog_blockposttag', 'blog');
        $data = $this->objDbBlog->getNumPostsByTag($num, $tag);
        $ret="";
            if (!empty($data)) {
                //$data = $data[0];
                $count=1;
                $ret="<table width='100%'>";
                // var_dump($data); die;
                foreach($data as $item) {
                    $linkuri = $this->uri(array(
                        'action' => 'viewsingle',
                        'postid' => $item[0]['id'],
                        'userid' => $item[0]['userid']
                    ));
                    $link = new href($linkuri, stripslashes($item[0]['post_title']));
                    $posterName = '<div class="blogpreviewuser">'
                      . $this->objUser->fullname($item[0]['userid'])
                      . '</div>';
                    $fixedTime = strtotime($item[0]['post_date']);
                    $fixedTime = date('Y-m-d H:i:s', $fixedTime);
                    $postDate = $this->objHumanizeDate->getDifference($fixedTime);
                    $postExcerpt = $item[0]['post_excerpt'];
                    if ($count == 1) {
                        $before="<tr>";
                        $after="";
                    } elseif ($count%3==0) {
                        $before="";
                        $after="</tr><tr>";
                    } else {
                        $before="";
                        $after="";
                    }
                    $ret .= $before . "<td width='33.3%' valign='top'><div class='blogpreview'>"
                      .  "<div class='blogpreviewtitle'>" . $link->show() . "</div>"
                      . $postExcerpt . "<br />" . $posterName
                      . "<div class='blogpreviewpostdate'>"
                      . $postDate . "</div>"
                      . "</div></td>" . $after;
                    $count++;
                }
                if ($count < 6) {
                    while ($count <=6) {
                        $ret .= "<td><div class='blogpreviewnodata'>&nbsp;</div></td>";
                        $count++;
                    }
                    $ret .= "</tr>";
                }
                $ret = $ret . "</table>";
            }
            //$ret = htmlentities($ret);
            return $ret;
           
    }
}
?>

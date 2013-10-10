<?php
/**
 *
 * An blog export class for generating raw output
 *
 * An blog export class for generating raw output of individual blog posts
 * for cross site blog posting. it receives the blog ID in the querystring
 * and returns a blog post content for embedding on another site. Two types
 * of content can be returned: raw and XML.
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
 * @package   oembed
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: imageprovider_class_inc.php 1 2010-01-01 16:48:15Z dkeats $
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
* An blog export class for generating raw output
*
* An blog export class for generating raw output of individual blog posts
* for cross site blog posting. it receives the blog ID in the querystring
* and returns a blog post content for embedding on another site. Two types
* of content can be returned: raw and XML.
*
* @author Derek Keats
* @package oembed
*
*/
class blogexport extends object
{

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    *
    * Constructor for the blogexport class
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }


    /**
    *
    * Method to extract the components of the provided URL and set them
    * as class properties. It also sets an err property if it fails to
    * generate the JSON for any reason.
    *
    * @param string $imageUrl The URL for the image to provide.
    * @access public
    *
    * @return boolean TRUE|FALSE True if the image URL produces valid JSON,
    *   false if not
    *
    */
    public function show()
    {
        $postId = $this->getParam('postid',FALSE);
        if ($postId) {
            $blogDb = $this->getObject('dbblog', 'blog');
            $item = $blogDb->getPostByPostID($postId);
            $title = $item[0]['post_title'];
            $excerpt = $item[0]["post_excerpt"];
            $content = $item[0]["post_content"];
            $content = $this->parseContent($content);
            $postDate = $item[0]["post_date"];
            $userId = $item[0]["userid"];
            $objUser = $this->getObject('user', 'security');
            $user = $objUser->fullname($userId);
            return $this->formatAsHtml($title, $excerpt, $content, $postDate, $user);
        } else {
            return '404 not found';
        }

    }

    private function formatAsHtml($title, $excerpt, $content, $postDate, $user)
    {
        //@Todo multilingualize
        $ret = $content .
          "<br /><span class = \"small\">Originally posted as:"
          . $title . " on " . $postDate . " by " . $user . "</span>";
        return $ret;
    }

    private function parseContent($content)
    {
        $objWashout = $this->getObject('washout', 'utilities');
        // Avoid parsing the Ajax-based filters
        return $objWashout->parseText($content, TRUE, 
          array('blog','deltags','tweets', 'quickembed'));
    }
}
?>
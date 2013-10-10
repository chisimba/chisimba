<?php
/**
 *
 * An blog snippet provider for oembed
 *
 * An blog snippet provider for oembed. oEmbed is an open format designed to allow
 * embedding content from a website into another page. This content is of the
 * types photo, video, link or rich. An oEmbed exchange occurs between a
 * consumer and a provider. A consumer wishes to show an embedded representation
 * of a third-party resource on their own website, such as a photo or an
 * embedded video. A provider implements the oEmbed API to allow consumers to
 * fetch that representation. This is a provider for blog posts that are
 * created using the blog module.
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
* An blog snippet provider for oembed
*
* An blog snippet provider for oembed. oEmbed is an open format designed to allow
* embedding content from a website into another page. This content is of the
* types photo, video, link or rich. An oEmbed exchange occurs between a
* consumer and a provider. A consumer wishes to show an embedded representation
* of a third-party resource on their own website, such as a photo or an
* embedded video. A provider implements the oEmbed API to allow consumers to
* fetch that representation. This is a provider for blog posts that are
* created using the blog module.
*
* @author Derek Keats
* @package oembed
*
*/
class blogprovider extends object
{
    // Note that these properties violate naming standards in Chisimba
    // but that is necessary for the oembed naming standards.
   
    public $type;
    public $version;
    public $title;
    public $author_name;
    public $url;
    public $width;
    public $height;

    /**
    *
    * Constructor for the imageprovider class
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
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
    public function parseId($id)
    {
        if ($id) {
            $blogDb = $this->getObject('dbblog', 'blog');
            $item = $blogDb->getPostByPostID($id);
            $title = $item[0]['post_title'];
            $this->title = $title;
            $content = $item[0]["post_content"];
            $content = $this->parseContent($content);
            $excerpt = $item[0]["post_excerpt"];
            $postDate = $item[0]["post_date"];
            $userId = $item[0]["userid"];
            $objUser = $this->getObject('user', 'security');
            $user = $objUser->fullname($userId);
            $this->url = $this->uri(array(
                'action' => 'viewsingle',
                'postid' => $id,
                'userid' => $userId
            ), 'blog');
            $this->html = $this->formatAsHtml($title, $excerpt, $content, $postDate, $user);
            $this->author_name = $user;
            $this->width=800;
            $this->height=600;
            // Turn the appropriate class properties into an array.
            $ar = $this->createArray();
            $this->xml = NULL;
            $this->json = $this->makeJson($ar);
            return TRUE;
        } else {
            $this->err = "404 Not Found";
            return FALSE;
        }
    }

    /**
    *
    * Parse the content to create the HTML to return in the
    * html of the rich oembed type
    *
    * @param string $content The content to parse
    * @return string The parsed content
    * @access private
    *
    */
    private function parseContent($content)
    {
        $objWashout = $this->getObject('washout', 'utilities');
        // Avoid parsing the Ajax-based filters
        return $objWashout->parseText($content, TRUE,
          array('blog','deltags','tweets'));
    }

    /**
     *
     * Format the blog content as HTML for returning as the html
     * of the rich type
     *
     * @param string $title The blog post title
     * @param string $excerpt An excerpt of the blog post
     * @param string $content The content of the blog post
     * @param string $postDate The date of the blog post
     * @param string $user The username of the blogger
     * @return string The parsed content formatted as required in HTML
     * @access private
     *
     */
    private function formatAsHtml($title, $excerpt, $content, $postDate, $user)
    {
        //@Todo multilingualize
        $ret = $content .
          "<br /><span class = \"minute\">Originally posted as:"
          . "<a href=\"$this->url\" target=\"_blank\">"
          . $title . "</a> on " . $postDate . " by " . $user . "</span>";
        return $ret;
    }

    /**
    * Method to create an array from the class properties
    *
    * @return sting array THe array of keys and values
    * @access private
    *
    */
    private function createArray()
    {
        $ar = array(
          'type' => 'rich',
          'version' => '1.0',
          'title' => $this->title,
          'url' => $this->url,
          'width' => $this->width,
          'height' => $this->height,
          'html' => $this->html);
        return $ar;
    }

    /**
    *
    * Make json from the array
    *
    * @access private
    * @param string $ar The array of keys/values to make into json
    * @return string the JSON code
    * 
    */
    private function makeJson($ar)
    {
        $callBack = $this->getParam("callback", NULL);
        if ($callBack) {
            return $callBack . "(" . $this->json = json_encode($ar) . ")";
        } else {
            return $this->json = json_encode($ar);
        }
    }

    /**
    *
    * Make XML from the array
    *
    * @access private
    * @param string $ar The array of keys/values to make into XML
    * @return string The XML as a string
    * 
    */
    private function makeXml($ar)
    {
        $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<oembed>\n";
        foreach ($ar as $key=>$value) {
            $key = strtolower($key);
            $xml .= "    <$key>$value</$key>\n";
        }
        $xml .= "</oembed>\n";
        return $xml;
    }

    /**
    * Determine from the querystring if they want XML or JSON.
    *
    * @access private
    * @return boolean TRUE|FALSE
    */
    private function asXml()
    {
        if ($this->getParam('format', 'json') == 'xml') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>
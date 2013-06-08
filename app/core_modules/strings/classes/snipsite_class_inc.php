<?php
/**
 *
 * A site snipper for grabbing a summary of a site
 *
 * A site snipper for grabbing a summary of a site to display inline
 * similarly to the way Facebook does the site summary insert.
 *
 * etc
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
 * @package   snipsite
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://www.chisimba.com
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
 * A site snipper for grabbing a summary of a site
 *
 * A site snipper for grabbing a summary of a site to display inline
 * similarly to the way Facebook does the site summary insert.
*
* @author Derek Keats
* @package snipsite
*
*/
class snipsite extends object
{
    
    public $pageContent;
    public $dom;


    /**
    *
    * Intialiser for snipsite. It loads the page and creates the DOM object.
    * 
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $url = $this->getParam('url', FALSE);
        if ($url) {
            $pageContents = $this->fetchUrl($url);
            // Load the page into the DOM object.
            $this->dom = new DOMDocument;
            @$this->dom->loadHTML($pageContents);
        }
    }

    /**
     * 
     * Use the curl wrapper to get the content of the page
     * 
     * @param string $url The URL of the page to fetch
     * @return string The fetched page content
     * @access public
     * 
     */
    public function fetchUrl($url)
    {
        $objCurl = $this->getObject('curlwrapper', 'utilities');
        return $objCurl->exec(urldecode($url));
    }
    
    /**
     * 
     * Use the DOM to extract the page title
     * 
     * @return string/boolean The title or FALSE
     * @access public
     * 
     */
    public function getTitle()
    {
        $titles = $this->dom->getElementsByTagName('title');
        if ($titles->length > 0) {
            $title = $titles->item(0)->textContent;
        } else  {
            $title = FALSE;
        }
        return $title;
    }
    
    public function getSiteSummary()
    {
        // First try the meta description tag from open graph data
        $res = $this->getMetaOgDescription();
        if ($res) {
            if (strlen($res) < 300) {
                $res .= $this->getParagraph(200);
            }
        }
        return $res;
    }
    
    public function getMetaOgDescription()
    {
        $metaTags = $this->dom->getElementsByTagName('meta');
        if ($metaTags->length > 0) {
            foreach ($metaTags as $tag) {
                $prop = $tag->getAttribute('property');
                if ($prop == 'og:description') {
                    $desc = $tag->getAttribute('content');
                    return $desc;
                }
            }
            return FALSE;
        } else {
            // No meta tags found.
            return FALSE;
        }
    }
    
    
    /**
     * 
     * Use the DOM to extract the first decent sized paragraph. Look for 
     * paragraphs longer than $pLen to avoid short things.
     * 
     * @param integer $pLen the minimum Length of paragraph to look for
     * @return string/boolean The title or FALSE
     * @access public
     * 
     */
    public function getParagraph($pLen = 300)
    {
        // Get the paragraphs.
        $paras = $this->dom->getElementsByTagName('p');
        $ret = NULL;
        if ($paras->length > 0) {
            foreach ($paras as $para) {
                $p = $para->textContent;
                if (strlen($p) > $pLen) {
                    $ret = $p;
                    break;
                }
            }
        }
        return " " . strip_tags($ret);
    }
    
    /**
    * 
    * Use the DOM to extract one image to display
    * 
    * @return string The image src or NULL
    * @access public
    * 
    */
    public function getImage()
    {
        // Get the images.
        $images = $this->dom->getElementsByTagName('img');
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            $size = @getimagesize($src);
            if ($size[0] >= 60 || $size[1]>= 60){
                return $src;
            }
         }
         return NULL;
    }

}
?>
<?php
/**
 *
 * An oembed provider for oembed
 *
 * An oembed provider for Chisimba. oEmbed is an open format designed to allow
 * embedding content from a website into another page. This content is of the
 * types photo, video, link or rich. An oEmbed exchange occurs between a
 * consumer and a provider. A consumer wishes to show an embedded representation
 * of a third-party resource on their own website, such as a photo or an
 * embedded video. A provider implements the oEmbed API to allow consumers to
 * fetch that representation. This is a master provider that calls other
 * providers depending on what is requested.
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
* An image provider for oembed
*
* An image provider for oembed. oEmbed is an open format designed to allow
* embedding content from a website into another page. This content is of the
* types photo, video, link or rich. An oEmbed exchange occurs between a
* consumer and a provider. A consumer wishes to show an embedded representation
* of a third-party resource on their own website, such as a photo or an
* embedded video. A provider implements the oEmbed API to allow consumers to
* fetch that representation. This is a provider for images stored in Chisimba
* using Chisimba's file manager.
*
* @author Derek Keats
* @package oembed
*
*/
class provider extends object
{
    /**
    *
    * Constructor for the provider class
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
    * Get the type of request based on the url
    * Currently supported are image and podcast. Everything else will be unknown.
    *
    * @param string $targetUrl The url being evaluated
    * @return string The type of request
    * @access public
    *
    */
    public function getRequestType($targetUrl)
    {
        if ($this->isHttp($targetUrl)) {
            if ($ext = $this->getExtenstion($targetUrl)) {
                if ($this->isImage($ext)) {
                    return "image";
                }
                if ($this->isMp3($ext)) {
                    return "podcast";
                }
            } else {
                return $this->getTypeNoExt($targetUrl);
            }
        } else {
            return "unknown";
        }
        return "unknown";
    }

    /**
     *
     * Get the type from URLs where there is no file extension. The default
     * format for this is http://site/path/module/uniqueid
     *
     * @todo add a Type so it becomes http://site/path/type/module/uniqueid
     * where type is the jason or xml.
     *
     *
     * @param <type> $targetUrl
     * @return <type>
     * @access private
     *
     */
    private function getTypeNoExt($targetUrl)
    {
        // Instantiate the configuration object.
        $objConfig = $this->getObject('altconfig', 'config');
        // Get the site root as a URL.
        $siteRoot = $objConfig->getsiteRoot();
        // Take off the siteRoot from the image URL.
        $targetForExplode = str_replace($siteRoot, NULL, $targetUrl, $count);
        // If we didn't remove the siteRoot then it can't be a local request
        if ($count == 0) {
            $this->err = "404 Not Found";
            return FALSE;
        }
        // Explode it into an array, the easiest way to get the file name
        $ar = explode("/", $targetForExplode);
        //There should be two entries
        if (count($ar) !==2) {
            $this->err = "404 Not Found";
            return FALSE;
        }
        $this->id = $ar[1];
        return $ar[0];
    }

    /**
    *
    * Get the extension of the file in the requested URL
    *
    * @param string $targetUrl The url being evaluated
    * @return string The file extension
    * http://site/path/module/uniqueid
    *
    */
    private function getExtenstion($targetUrl) {
        $tmpAr = explode(".", $targetUrl);
        if (count($tmpAr) > 1) {
            $extLocation = count($tmpAr)-1;
            $ext =  strtolower($tmpAr[$extLocation]);
        } else {
            $ext = FALSE;
        }
        return $ext;
    }

    /**
    *
    * Evaluate if the given extension is an image
    *
    * @param string $ext The file extension from the URL
    * @return boolean TRUE|FALSE
    * @access private
    *
    */
    private function isImage($ext)
    {
        
        $fileTypes=array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($ext, $fileTypes)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Evaluate if the given extension is an MP3 file
    *
    * @param string $ext The file extension from the URL
    * @return boolean TRUE|FALSE
    * @access private
    *
    */
    private function isMp3($ext)
    {
        $fileTypes = array('mp3');
        if (in_array($ext, $fileTypes)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    * Method to test if the link is valid HTML
    *
    * @param string $imageUrl The URL for the image being tested
    * @return boolean TRUE | FALSE
    * @access private
    *
    */
    private function isHttp($targetUrl)
    {
         return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $targetUrl);
    }
    
}
?>
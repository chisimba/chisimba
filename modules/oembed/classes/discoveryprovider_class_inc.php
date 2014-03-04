<?php
/**
 *
 * A discovery provider for oembed
 *
 * A discovery provider for oembed. oEmbed is an open format designed to allow
 * embedding content from a website into another page. This content is of the
 * types photo, video, link or rich. An oEmbed exchange occurs between a
 * consumer and a provider. A consumer wishes to show an embedded representation
 * of a third-party resource on their own website, such as a photo or an
 * embedded video. A provider implements the oEmbed API to allow consumers to
 * fetch that representation. oEmbed providers can choose to make their oEmbed
 * support discoverable by adding elements to the head of their existing
 * (X)HTML documents. For example:
 * <link rel="alternate" type="application/json+oembed"
 * 	href="http://flickr.com/services/oembed?url=
 *      http%3A//flickr.com/photos/bees/2362225867/&format=json"
 *	title="Bacon Lollys oEmbed Profile" />
 * or
 * <link rel="alternate" type="text/xml+oembed"
 *	href="http://flickr.com/services/oembed?url=
 *      http%3A//flickr.com/photos/bees/2362225867/&format=xml"
 *	title="Bacon Lollys oEmbed Profile" />
 *
 * This is a provider for discovery header links
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
 * @version   1
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
 * A discovery provider for oembed
 *
 * A discovery provider for oembed. oEmbed is an open format designed to allow
 * embedding content from a website into another page. This content is of the
 * types photo, video, link or rich. An oEmbed exchange occurs between a
 * consumer and a provider. A consumer wishes to show an embedded representation
 * of a third-party resource on their own website, such as a photo or an
 * embedded video. A provider implements the oEmbed API to allow consumers to
 * fetch that representation. oEmbed providers can choose to make their oEmbed
 * support discoverable by adding elements to the head of their existing
 * (X)HTML documents. For example:
 * <link rel="alternate" type="application/json+oembed"
 * 	href="http://flickr.com/services/oembed?url=
 *      http%3A//flickr.com/photos/bees/2362225867/&format=json"
 *	title="Bacon Lollys oEmbed Profile" />
 * or
 * <link rel="alternate" type="text/xml+oembed"
 *	href="http://flickr.com/services/oembed?url=
 *      http%3A//flickr.com/photos/bees/2362225867/&format=xml"
 *	title="Bacon Lollys oEmbed Profile" />
 *
 * This is a provider for discovery header links
*
* @author Derek Keats
* @package oembed
*
*/
class discoveryprovider extends object
{
   
    /**
    *
    * Constructor for the discoveryprovider class
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
    * description here
    *
    * @param string $imageUrl The URL for the image to provide.
    * @access public
    *
    * @return boolean TRUE|FALSE True if the image URL produces valid JSON,
    *   false if not
    *
    */
    public function show($imageUrl)
    {
             return TRUE;
        
    }

}
?>

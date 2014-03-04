<?php
/**
 * Feed reader class based on PEAR::RSS.
 *
 * Used to parse RSS feeds
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
 * @version    $Id: rssreader_class_inc.php 12038 2009-01-08 10:51:35Z jsc $
 * @package    feeds
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2008 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

class rssreader extends object
{
	/**
	 * RSS Object
	 *
	 * @var object
	 */
	public $rss;

	/**
	 * Language items
	 *
	 * @var object
	 */
	public $objLanguage;

	/**
	 * Standard Chisimba init method
	 *
	 * @access public
	 * @param void
	 * @return string template
	 */
	public function init()
	{
		try {
			// Load up the language class
			$this->objLanguage = $this->getObject('language', 'language');
		}
		catch (customException $e)
		{
			// Clean up any messes
			customException::cleanUp();
			exit;
		}

		// Check that the PEAR RSS file is available...
		if (!@include_once ($this->getPearResource('XML/RSS.php'))) {
            continue;
        }
        elseif(!@require_once $this->getPearResource("XML/RSS.php")) {
            require_once("XML/RSS.php");
        }
        else {
            //throw new customException($this->objLanguage->languageText("mod_feed_sanity_xmlrssnotfound", "feed"));
            // todo: find out why this bogus error was happening
        }

	}

	/**
	 * Method to parse an RSS feed
	 *
	 * @param URL to the feed $url
	 * @return string parsed string
	 */
	public function parseRss($url)
	{
		$this->rss =& new XML_RSS($url);
		return $this->rss->parse();
	}

	/**
	 * Method to get RSS items
	 *
	 * @return object
	 */
	public function getRssItems()
	{
		return $this->rss->getItems();
	}

	/**
	 * Get the struct of the RSS feed
	 *
	 * @return struct
	 */
	public function getRssStruct()
	{
		return $this->rss->getStructure();
	}

	/**
	 * Return the channel (RSS channel) info
	 *
	 * @return struct
	 */
	public function getChanInfo()
	{
		return $this->rss->getChannelInfo();
	}

	/**
	 * Grab any image references from the feed
	 *
	 * @return array
	 */
	public function getRssImages()
	{
		return $this->rss->getImages();
	}

	/**
	 * Text of hte RSS
	 *
	 * @return array
	 */
	public function getRssTextInputs()
	{
		return $this->rss->getTextinputs();
	}
}
?>

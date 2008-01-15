<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * This class creates a sitemap for a module, then aggregates all module sitemaps to a siteMapIndex
 *
 * @category  Chisimba
 * @package   utitilies
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class sitemap extends object
{

	public $objConfig;

	/**
	* Constructor
	*/
	public function init()
	{
		$this->objConfig = $this->getObject('altconfig', 'config');
	}

	/**
	 * Function to create a sitemap. 
	 * 
	 * @param array $arrayOfVals
	 * @see http://www.sitemaps.org/protocol.php
	 * @return string $str the sitemap xml
	 */
	public function createSiteMap($arrayOfVals)
	{
		$str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
    <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
		foreach($arrayOfVals as $vals)
		{
			// lets check that the required bits are there!
			if(!isset($vals['url']))
			{
				// continue and hope we have better luck next time!
				continue;
			}
			if(!isset($vals['lastmod']))
			{
				// date format should be YYYY-mm-dd
				$vals['lastmod'] = date('Y-m-d', time());
			}
			if(!isset($vals['changefreq']))
			{
				// change freq can be always, hourly, daily, weekly, monthly, yearly or never
				$vals['changefreq'] = 'weekly';
			}
			if(!isset($vals['priority']))
			{
				// default priority is 0.5 must be between 0.0 and 1.0
				$vals['priority'] = 0.5;
			}
			$str .= "<url>
      				 <loc>".htmlentities($vals['url'], ENT_QUOTES)."</loc>
      				 <lastmod>".$vals['lastmod']."</lastmod>
      			     <changefreq>".$vals['changefreq']."</changefreq>
      				 <priority>".$vals['priority']."</priority>
   					 </url>\n";
		}
		$str .= "</urlset>";

		return $str;
	}

	/**
	 * Method to create a sitemap index file
	 *
	 * @param array $arrOfMaps - set of sitemaps
	 * @return string
	 * @see http://www.sitemaps.org/protocol.php
	 */
	public function siteMapIndex($arrOfMaps)
	{
		$str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
			    <sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
		foreach($arrOfMaps as $maps)
		{
			// lets check that the required bits are there!
			if(!isset($maps['url']))
			{
				// continue and hope we have better luck next time!
				continue;
			}
			if(!isset($maps['lastmod']))
			{
				// date format should be YYYY-mm-dd
				$vals['lastmod'] = date('Y-m-d', time());
			}
			$str .= "<sitemap>\n";
			$str .= "<loc>".$maps['url']."</loc>\n";
			$str .= "<lastmod>".$maps['lastmod']."</lastmod>\n";
			$str .= "</sitemap>\n";
		}
		$str .= "</sitemapindex>";

		return $str;
	}

	/**
	 * Method to write a sitemap to disc
	 *
	 * @param sitemap xml $str
	 * @param sitemap name $mapname
	 * @return boolean true on success
	 */
	public function writeSitemap($str, $mapname)
	{

		$path = $this->objConfig->getsiteRootPath();
		if(file_put_contents($path.$mapname.".xml", $str))
		{
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Method to update a sitemap with a new entry (append or delete or update)
	 *
	 * @param array $smarr single entry array 
	 * @param string $mapname the name of the sitemap to update.
	 */
	public function appendupdateSiteMap($smarr, $mapname)
	{
		$path = $this->objConfig->getsiteRootPath().$mapname.'.xml';
		$xml = simplexml_load_file($path);
		$entries = $xml->url;

		foreach($entries as $recs)
		{
			$old[] = $recs->loc;
		}
		if(in_array($$smarr['url'], $old))
		{
			continue;
		}
		else {
			// now add the new url to the end
			$url = $xml->addChild('url');
			$url->addChild('loc', htmlentities($smarr['url'], ENT_QUOTES));
			$url->addChild('lastmod', $smarr['lastmod']);
			$url->addChild('changefreq', $smarr['changefreq']);
			$url->addChild('priority', $smarr['priority']);
		}
		//unlink($this->objConfig->getsiteRootPath().$mapname.'.xml');
		file_put_contents($this->objConfig->getsiteRootPath().$mapname.'.xml', $xml->__toString());
	}

	function updateSitemap($smarr, $mapname)
	{
		$path = $this->objConfig->getsiteRootPath().$mapname.'.xml';
		// Loads an XML file into an object
		$go = simplexml_load_file($path);

		// We are now able to manipulate the object ($go) and add a new url child element to it.
		$sitemap = $go->addChild('url');

		// Now we create are four child elements.
		$sitemap->addChild('loc', $smarr['url']);
		$sitemap->addChild('priority', $smarr['priority']);

		$sitemap->addChild('lastmod', $smarr['lastmod']);
		$sitemap->addChild('changefreq', $smarr['changefreq']);

		// Return a well-formed XML string
		$xml = $go->asXML();

		// Write our string variable $xml back to the sitemap.xml file.
		file_put_contents($path, $xml);

		// Destroy variable $xml
		unset($xml);

		return true;

	}
}
?>
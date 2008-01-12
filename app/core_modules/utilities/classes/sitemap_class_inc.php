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
	/**
	* Constructor
	*/
	public function init()
	{
		
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
		$str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>/n
			    <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
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
			$str .= "/t<url>/n
      				 /t/t<loc>".htmlentities($vals['url'], ENT_QUOTES)."</loc>/n
      				 /t/t<lastmod>".$vals['lastmod']."</lastmod>/n
      			     /t/t<changefreq>".$vals['changefreq']."</changefreq>/n
      				 /t/t<priority>.".$vals['priority']."</priority>/n
   					 /t</url>/n";
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
		$str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>/n
			    <sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">/n";
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
        	$str .= "/t<sitemap>/n";
      		$str .= "/t/t<loc>".$maps['url']."</loc>/n";
      		$str .= "/t/t<lastmod>".$maps['lastmod']."</lastmod>/n";
   			$str .= "/t</sitemap>/n";
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
}
?>
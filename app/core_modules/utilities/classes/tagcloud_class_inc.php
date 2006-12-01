<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Tag Cloud class
 * This is an adaptor pattern that wraps the functionality found in PEAR::HTML_TagCloud
 *
 * @access public
 * @author Paul Scott
 * @copyright AVOIR
 * @link http://pear.php.net/package/html_tagcloud
 * @filesource
 */

class tagcloud extends object
{
	/**
	 * Tag Cloud class
	 *  This package can be used to generate tag coulds
	 * in HTML and CSS.
	 * A tag cloud is an visual representation of list of so-called "tags" or keywords,
	 * that have a different font size depending on how often they occur on the page/blog.
	 * More information on tag clouds is available in Wikipedia.
	 * This package does not only visualize frequency, but also timeline infomation.
	 * The newer the tag is, the deeper its color will be;
	 * older tags will have a lighter color.
	 *
	 * @author Paul Scott <pscott@uwc.ac.za>
	 */

	/**
	 * Tags object to hold the tag cloud
	 *
	 * @var object
	 */
	public $tags;

	/**
	 * Standard init function for the engine
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		if (!@include_once('HTML/TagCloud.php'))
		{
			throw new customException("Unable to locate PEAR::HTML_TagCloud, please install it with pear install --alldeps html_tagcloud-beta");
		}
		else {
			$this->tags = new HTML_TagCloud();
		}

	}

	/**
	 * Example function
	 *
	 * @param void
	 * @return string
	 */
	public function exampletags()
	{

		// add Elements
		$this->tags->addElement('PHP'       ,'http://www.php.net'  , 39, strtotime('-1 day'));
		$this->tags->addElement('XML'       ,'http://www.xml.org'  , 21, strtotime('-2 week'));
		$this->tags->addElement('Perl'      ,'http://www.xml.org'  , 15, strtotime('-1 month'));
		$this->tags->addElement('PEAR'      ,'http://pear.php.net' , 32, time());
		$this->tags->addElement('MySQL'     ,'http://www.mysql.com', 10, strtotime('-2 day'));
		$this->tags->addElement('PostgreSQL','http://pgsql.com'    ,  6, strtotime('-3 week'));
		// output HTML and CSS
		return $this->tags->buildALL();
	}

	/**
	 * Build the tag cloud and return the cloud in a featurebox or not.
	 *
	 * It is possible to modify the colors used by the CSS.
	 * You need to define your own class which extends HTML_TagCloud and override color and size properties.
	 *
	 * if you don't want to add timeline information and have the color changing accordingly,
	 * just omit the fourth parameter to addElement().
	 * When doing this, the current time is set.
	 *
	 * @access public
	 * @param array $tagarr
	 * @return string tagcloud
	 */
	public function buildCloud($tagarr)
	{
		//loop through an associative array
		foreach($tagarr as $tags)
		{
			$this->tags->addElement($tags['name'], $tags['url'], $tags['weight'], $tags['time']);
		}
		return $this->tags->buildAll();
	}

}
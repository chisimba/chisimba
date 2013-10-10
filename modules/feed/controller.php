<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* http client Controller
*
* @author Paul Scott
* @copyright (c) 2004 University of the Western Cape
* @package httpclient
* @version 1
*/
class feed extends controller
{

    public $objFeed;
    public $objFeedCreator;
    public $objClient;
    public $objLog;

	/**
	* Constructor method to instantiate objects and get variables
	*/
    public function init()
    {
        try {
        	$this->objFeed = $this->getObject('feeds');
        	$this->objFeedCreator = $this->getObject('feeder');
        	$this->objClient = $this->getObject('client','httpclient');
        	//Get the activity logger class
        	$this->objLog=$this->newObject('logactivity', 'logger');
        	//Log this module call
        	$this->objLog->log();
        }
        catch (customException $e)
        {
        	echo customException::cleanUp();
        	die();
        }
    }

    /**
	* Method to process actions to be taken
    *
    * @param string $action String indicating action to be taken
	*/
    public function dispatch($action=Null)
    {
        switch ($action)
        {
            default:
            case 'importfeed':
            	$url = $this->getParam('url');
                try {
                	$feed1 = $this->objClient->getUrl('http://api.flickr.com/services/feeds/photos_public.gne?id=46242866@N00&format=rss_200');
					//$feed = $this->objFeed->importFile($feed1);
					$feed = $this->objFeed->importString($feed1);

                	foreach ($feed->items as $item) {
 					   echo "<p>" . $item->title() . "<br />";
    					echo  $item->link()  . "</p>";

					}
    			} catch (customException $e) {
        			echo $e->getMessage();
    			}
                break;
            case 'createfeed':
            		$format = $this->getParam('format');
            		$this->objFeedCreator->setrssImage('pic', 'http://www.dailyphp.net/images/logo.gif', '', 'some image', $iTruncSize = 500, $desHTMLSyn = true);
            		$this->objFeedCreator->setupFeed(TRUE, 'test feed', "A test feed from the Chisimba Framework", 'http://5ive.uwc.ac.za', 'http://127.0.0.1/chi/5ive/app/index.php?module=feed&action=createfeed');


            		$this->objFeedCreator->addItem('testing 123', 'http://5ive.uwc.ac.za/apidocs/dev', 'API Docs now available', 'here', 'Paul');
            		switch ($format) {
            			case 'rss2':
            				$feed = $this->objFeedCreator->output(); //defaults to RSS2.0
            				break;
            			case 'rss091':
            				$feed = $this->objFeedCreator->output('RSS0.91');
            				break;
            			case 'rss1':
            				$feed = $this->objFeedCreator->output('RSS1.0');
            				break;
            			case 'pie':
            				$feed = $this->objFeedCreator->output('PIE0.1');
            				break;
            			case 'mbox':
            				$feed = $this->objFeedCreator->output('MBOX');
            				break;
            			case 'opml':
            				$feed = $this->objFeedCreator->output('OPML');
            				break;
            			case 'atom':
            				$feed = $this->objFeedCreator->output('ATOM0.3');
            				break;
            			case 'html':
            				$feed = $this->objFeedCreator->output('HTML');
            				break;
            			case 'js':
            				$feed = $this->objFeedCreator->output('JS');
            				break;

            			default:
            				$feed = $this->objFeedCreator->output(); //defaults to RSS2.0
            				break;
            		}

            		echo $feed;
        }
    }
}
?>
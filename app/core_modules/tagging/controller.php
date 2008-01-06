<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
class tagging extends controller
{
    public $objLog;
    public $objLanguage;
    public $objDbTags;
    
    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objDbTags = $this->getObject('dbtags');
            //Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
    public function dispatch($action = Null)
    {
        switch ($action) {
            default:
            	//build a site wide tag cloud and display it
            	$cloud = $this->siteTagCloud();
            	$this->setVarByRef('cloud', $cloud);
            	return 'sitecloud_tpl.php';
            	break;
            	
            case 'importblogs':
            	$this->objDbTags->migrateBlogTags();
            	break;
        }
    }
    
    /**
     * Method to build a tag cloud from site entry tags
     *
     * @return array
     */
    public function siteTagCloud($showOrHide = 'none')
    {
        $this->objTC = $this->getObject('tagcloud', 'utilities');
        //get all the tags
        $tagarr = $this->objDbTags->getAllTags();
        if(empty($tagarr))
        {
            return NULL;
        }
        foreach($tagarr as $uni)
        {
            $t[] = $uni['meta_value'];
        }
        $utags = array_unique($t);
        foreach($utags as $tag)
        {
            //create the url
            $url = $this->uri(array(),$uni['module']);
            //get the count of the tag (weight)
            $weight = $this->objDbTags->getSiteTagWeight($tag, NULL);
            $weight = $weight*1000;
            $tag4cloud = array(
                'name' => $tag,
                'url' => $url,
                'weight' => $weight,
                'time' => time()
            );
            $ret[] = $tag4cloud;
        }
        
        return $this->objTC->buildCloud($ret);
    }
}
?>
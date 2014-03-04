<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
/**
*  This class makes use of the Google Ajax Search API to deliver recent newsitems
*  key obtained from http://code.google.com/apis/ajaxsearch/signup.html
*
* @author Nic Appleby
* @copyright UWC 2006
* @license GNU/GPL
* @package LRS
*/
class googlenews extends object {

	var $key;
	var $hasKey;

	function init() {
		$this->objSysconfig = $this->getObject('dbsysconfig','sysconfig');
		$this->hasKey = $this->objSysconfig->getValue('has_google_api_key','award');
	}

	function getScript($div,$defaultSearch) {
	     $defaultSearch = str_replace('"','\"',$defaultSearch);
		 if($this->hasKey == 1) {
		 	$this->key = $this->objSysconfig->getValue('google_api_key','award');
		 	$script = '
		 		<script src="http://www.google.com/jsapi?key='.$this->key.'" type="text/javascript"></script>
    			<script language="Javascript" type="text/javascript">
    				//<![CDATA[

    				google.load("search", "1");

    				function getNews() {
      					// Create a search control
      					var searchControl = new google.search.SearchControl();

      					// web search, open
        				options = new GsearcherOptions();
        				options.setExpandMode(GSearchControl.EXPAND_MODE_OPEN);
        				searchControl.addSearcher(new google.search.WebSearch(),options);
      					// Tell the searcher to draw itself and tell it where to attach
      					searchControl.draw(document.getElementById("'.$div.'"));

      					// Execute an inital search
      					searchControl.execute("'.$defaultSearch.'");
    				}
    
    				google.setOnLoadCallback(getNews);

    				//]]>
    			</script>';

		 	return $script;
		 } else {
		 	return false;
		 }
	}
}
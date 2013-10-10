<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

//Preventing Notices
if (!isset($searchKey)) {
    $searchKey = '';
}
if (!isset($cluster)) {
    $cluster = '';
}
if (!isset($searchCluster)) {
    $searchCluster = '';
}
if (!isset($defaultClusters)) {
    $defaultClusters = '';
}

$script = <<<STARTSEARCH
<script type="text/javascript">
    //Method to initiate a search against a particular data source
    function submitSearch(divId, sourceId, searchKey, subjectCluster) {
        //targetDiv
        //alert (workflow);

        var theUrl = "?module=librarysearch&action=execworkflow&id=" + sourceId + '&search_key=' + searchKey + '&subject_cluster=' + subjectCluster;
        //var theUrl = "http://localhost/fresh";
        //alert(theUrl);
        jQuery.ajax({
            url: theUrl,
            method: 'GET',
            dataType: 'html',
            beforeSend: function() {
            },
            success: function(data) {
                //alert(data);
				if (data == ''){
					data = 'Couldn\'t Connect to Host';
				}
                jQuery('#' + divId).replaceWith(data);
            },
            error: function(data) {
            },
            complete: function(data) {
            }
        });
        //*/              
        
    }

    /* //Will implement Queue based query if flat doesn't work
    jQuery(document).ready(function(){
        startSearch();
    });
    */
    
</script>
STARTSEARCH;
$this->appendArrayVar('headerParams', $script);
//Getting the sources asscociated with the searched cluster

//$leftContent = 'Left Column';
$middleContent = "Now searching for \"<b>$searchKey</b>\" in the \"<b>$cluster</b>\" subject cluster:<br/>";

$clusterArr = $this->objClusters->getCluster($cluster);

/*
//TODO: Implement Search for all default clusters
foreach ($defaultClusters as $cluster) {

}
*/

$loadIcon = $this->getObject('geticon', 'htmlelements');
$loadIcon->setIcon('loader');
$loadIcon->title = 'Loading';

$table = new htmlTable();
$table->width = "100%";
$table->cellspacing = "0";
$table->cellpadding = "0";
$table->border = "0";
$table->attributes = "align ='center'";

$searchScript = '';

if (!empty($clusterArr) && isset($clusterArr['id'])) {
    $table->startRow();
    $table->addCell("<h3>$clusterArr[title]</h3>");
    $table->endRow();
    
    $sources = $this->objClusters->getClusterSources($clusterArr['id']);
    foreach ($sources as $src) {
        //The javascript responsible for executing the queued ajax search requests
        $searchScript .= "
        <script language='javascript'>
            submitSearch('res_$src[id]', '$src[id]', '$searchKey', '$cluster');
        </script>";

        $table->startRow();
        $table->addCell("&nbsp;&nbsp;&nbsp;<i>$src[title]</i>", null,'top','left', null, 'colspan="2"');
        $table->addCell("<div id='res_$src[id]'> Connecting... ".$loadIcon->show()." </div>");
        $table->endRow();
        
    }
}

$middleContent .= $table->show() . $searchScript;

//$this->setVar('leftContent', $leftContent);
$this->setVar('middleContent', $middleContent);

?>
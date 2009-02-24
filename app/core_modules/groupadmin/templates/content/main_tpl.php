<?php

$objIcon = $this->getObject('geticon', 'htmlelements');
$tabcontent = $this->newObject('tabcontent', 'htmlelements');
//$tab = $this->newObject('tabbedbox', 'htmlelements');
$objIcon->setIcon('loader');
$loading = $objIcon->show();


$script = $this->getJavaScriptFile('groupadmin.js');
$this->appendArrayVar('headerParams', $script);
$this->appendArrayVar('bodyOnLoad', "loadAll();");

/*$tab->tabbedbox();
$tab->addTabLabel($this->objLanguage->languageText('mod_groupadmin_sitegroups', 'groupadmin'));
$tab->addBoxContent('site groups go here');
   */        

$sacontent = '<div class="groupadmincontent">
							<div class="siteadminlist">
								<div id="siteadminscontent">'.$loading.'</div>
							</div>
							<div id="siteadmintoolbox" >Ajax seach goes here</div>
						</div>';

$tabcontent->addTab($this->objLanguage->languageText('mod_groupadmin_siteadmins','groupadmin'), $this->objOps->loadContent('siteadmins'));
$tabcontent->addTab(ucwords($this->objLanguage->languageText('mod_context_authors','context')),$this->objOps->loadContent('lecturers'));
$tabcontent->addTab(ucwords($this->objLanguage->languageText('mod_context_readonly','context')),$this->objOps->loadContent('students'));
$tabcontent->width = '90%';
echo  $tabcontent->show();
//$admins = array('name' => 'Site Administrators', 'content' => 'ggg');
//$objTabs->addTab($admins);



//echo "Loading......".$objIcon->show();



//$middleContent .= '<div id="browsefiles">'.$loading->show().'</div>';


//echo $objTabs->show();

$script = $this->getJavaScriptFile('jquery/jquery.autocomplete.js', 'htmlelements');
$this->appendArrayVar('headerParams', $script);
$str = '<link rel="stylesheet" href="'.$this->getResourceUri('jquery/jquery.autocomplete.css', 'htmlelements').'" type="text/css" />';
$this->appendArrayVar('headerParams', $str);

?>

<form onsubmit="return false;" action="">
<p>
Ajax City Autocomplete: (try a few examples like: 'Little Grebe', 'Black-crowned Night Heron', 'Kentish Plover')<br />
<input type="text" style="width: 200px;" value="" id="CityAjax" class="ac_input"/>
<input type="button" onclick="lookupAjax();" value="Get Value"/>
</p>
</form>

<script type="text/javascript">
function findValue(li) {
if( li == null ) return alert("No match!");

// if coming from an AJAX call, let's use the CityId as the value
if( !!li.extra ) var sValue = li.extra[0];

// otherwise, let's just display the value in the text box
else var sValue = li.selectValue;

//alert("The value you selected was: " + sValue);
}

function selectItem(li) {
findValue(li);
}

function formatItem(row) {
return row[0] + " (id: " + row[1] + ")";
}

function lookupAjax(){
var oSuggest = $("#CityAjax")[0].autocompleter;
oSuggest.findValue();
return false;
}

function lookupLocal(){
var oSuggest = $("#CityLocal")[0].autocompleter;

oSuggest.findValue();

return false;
}


$("#CityAjax").autocomplete(
"autocomplete.php",
{
delay:10,
minChars:2,
matchSubset:1,
matchContains:1,
cacheLength:10,
onItemSelect:selectItem,
onFindValue:findValue,
formatItem:formatItem,
autoFill:true
}
);

</script>
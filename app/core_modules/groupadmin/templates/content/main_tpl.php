<?php
$objIcon = $this->getObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');
$loader = $objIcon->show();
$button = $this->getObject('button', 'htmlelements');
$button->setToSubmit();
$button->value = 'Search';
$button->id = 'Search';


$form = $this->newObject('form', 'htmlelements');
$form->action = $this->uri(array('action'=> 'showgroup'), 'groupadmin');
$form->addToForm($this->objOps->searchGroupDropDown());
$form->addToForm('&nbsp;<input type="submit" value="View Group" />');

$scripts = $this->getJavaScriptFile('jquery/jquery-ui-personalized-1.6rc6/jquery-1.3.1.js', 'htmlelements');
$scripts .= $this->getJavaScriptFile('jquery/jquery-ui-personalized-1.6rc6/jquery-ui-personalized-1.6rc6.js', 'htmlelements');
$scripts .= '<link type="text/css" href="'.$this->getResourceUri('jquery/jquery-ui-personalized-1.6rc6/theme/ui.all.css', 'htmlelements').'" rel="Stylesheet" />';
$scripts .= '<script type="text/javascript">
			$(function(){

				// Accordion
				$("#accordion").accordion({ header: "h3", autoheight: true }).
					bind("accordionchange", function(event, ui) {  
						tabId = ui.newHeader[0].id;
						loadGroupTab(tabId);
						//alert(ui.newHeader[0].id);
					});

				// Tabs
				$(\'#tabs\').tabs({
					select: function(event, ui) {
						id = stripId(ui.panel.id);
						loadGroupTab(id);						
					}
	
					});			

			});
function stripId(str)
{
	return str.substring(0, str.indexOf("_list"));
}
		</script>';

$this->appendArrayVar('headerParams', $scripts);
//style="position: fixed; top:200px; width:200px; left:700px;padding:10px;"
//get all the groups'.$this->objOps->getGroups().'

echo  '<label>Search Groups</label><br/>'.$form->show();

echo '	<div style=" width:650px;border:0px solid black;">
				<div style="float:left;width:420px;padding-right:10px">
					'.$this->objOps->showGroup($this->getParam('groupid')).' 
				<div style="clear:both"></div>
		</div >';
if($this->getParam('groupid')){
	
echo '
		<div style="position: fixed; top:200px; width:200px; left:700px;padding:10px;" >
			<form id="searchform" name="searchform" autocomplete="off">
				<p>
					<label>Search Users</label><br/>
					<input type="text" id="suggest4">
					<input type="hidden" id="hiddensuggest4">
					<input type="hidden" id="groupid">
					<div class="warning" id="groupname">'.$loader.'</div>
					<input id="searchbutton" type="button" onclick="submitSearchForm(this.form)" value="Add to Group" />
				</p>
				<p>
					<div id="multipleusers" style="height:150px; overflow:auto;">
					'.$loader.'
					</div>
				</p>
			</form>
		</div>
</div><div style="clear:both"></div>';
}
if($this->getParam('groupid'))
{
	$groupId = $this->getParam('groupid');//$this->objOps->getFirstGroupId();
	$this->appendArrayVar('bodyOnLoad', 'loadGroupTab('.$groupId.');');
}

$objIcon = $this->getObject('geticon', 'htmlelements');
$tabcontent = $this->newObject('tabcontent', 'htmlelements');
$objIcon->setIcon('loader');
$loading = $objIcon->show();

$script = $this->getJavaScriptFile('groupadmin.js');
$this->appendArrayVar('headerParams', $script);


$script = $this->getJavaScriptFile('jquery/jquery.autocomplete.js', 'htmlelements');
$this->appendArrayVar('headerParams', $script);
$str = '<link rel="stylesheet" href="'.$this->getResourceUri('jquery/jquery.autocomplete.css', 'htmlelements').'" type="text/css" />';
$this->appendArrayVar('headerParams', $str);

	
	$str = '<script type="text/javascript">
$().ready(function() {

	function findValueCallback(event, data, formatted) {
		$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
	}

	function formatItem(row) {
		return row[0] + " (<strong>username: " + row[1] + "</strong>)";
	}
	function formatResult(row) {
		//return row[0].replace(/(<.+?>)/gi, \'\');
		return row[0];
	}

$(":text, textarea").result(findValueCallback).next().click(function() {
		$(this).prev().search();
	});


	$("#suggest4").autocomplete(\'index.php?module=groupadmin&action=searchusers\', {
		width: 300,
		multiple: false,
		matchContains: true,
		formatItem: formatItem,
		formatResult: formatResult,
		
	}).result(function (evt, data, formatted) {				
					$("#hiddensuggest4").val(data[1]);
					});

					
	$("#searchgroup").autocomplete(\'index.php?module=groupadmin&action=searchusers\', {
		width: 300,
		multiple: false,
		matchContains: true,
		formatItem: formatItem,
		formatResult: formatResult,
		
	}).result(function (evt, data, formatted) {				
					$("#hiddensuggest4").val(data[1]);
					});

	$("#clear").click(function() {
		$(":input").unautocomplete();
	});
});

function submitSearch(data)
{

	alert(data[0]);
}


function changeOptions(){
	var max = parseInt(window.prompt(\'Please type number of items to display:\', jQuery.Autocompleter.defaults.max));
	if (max > 0) {
		$("#suggest1").setOptions({
			max: max
		});
	}
}

function submitSearchForm(frm)
{	
	username = frm.hiddensuggest4.value;
	groupId = frm.groupid.value;
	if(username)
	{
		addUser(groupId, username);
	}
	
	frm.hiddensuggest4.value = "";
	frm.suggest4.value = "";
	
}
	</script>';
	$this->appendArrayVar('headerParams', $str);

	
?><!--h3>Result:</h3> <ol id="result"></ol><div id="hiddenIDbox"></div-->




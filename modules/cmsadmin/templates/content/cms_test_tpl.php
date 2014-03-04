<?PHP


$objEditor = $this->getObject('htmlarea', 'htmlelements');
echo $objEditor->showTinyMCE();

echo "
<script type='text/javascript'>
tinyMCE.get('tinymce_editor').show();
</script>
";

/*Testing jQuery Corners*/
/*
$jQuery = $this->newObject('jquery', 'htmlelements');
$jQuery->loadCornerPlugin();

ob_start();
?>
<script type="text/javascript">
jQuery(function(){
        jQuery('#makeround').corner();
    });
</script>
<?php
$script = ob_get_contents();
ob_end_clean();

$this->appendArrayVar('headerParams', $script);

?>
<div id="makeround" style="background-color:#e3e3e3; background-image: url('http://localhost/test/packages/cmsadmin/resources/apple_back.png') ; background-repeat: repeat-x;height: 140; width: 300">

<div>

<?php
/*Testing Wesley Nitskies User Admin Implementation*/

/*

$objIcon = $this->getObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');
$loader = $objIcon->show();

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

//get all the groups'.$this->objOps->getGroups().'
echo '  <div style=" width:650px;border:0px solid black;">
                <div style="float:left;width:420px;padding-right:10px">
                    '.$this->objOps->getGroups().'
                <div style="clear:both"></div>
        </div >
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
$groupId = $this->objOps->getFirstGroupId();
$this->appendArrayVar('bodyOnLoad', 'loadGroupTab('.$groupId.');');

$objIcon = $this->getObject('geticon', 'htmlelements');
$tabcontent = $this->newObject('tabcontent', 'htmlelements');
$objIcon->setIcon('loader');
$loading = $objIcon->show();

$script = $this->getJavaScriptFile('groupadmin.js', 'groupadmin');
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

    




/*

//Testing FG Menu
$jQuery = $this->newObject('jquery', 'htmlelements');
$jQuery->loadFgMenuPlugin();

ob_start();
?>
<style>
.hidden {
	display: none;
}

#cmscontrolpanelitems li {
	background-image : url('');
}
</style>

<script type="text/javascript">
jQuery(function(){
		jQuery('#cmscontrolpanelmenu').menu({
			content: jQuery('#cmscontrolpanelmenu').next().html(),
			backLink: false
		});
    });
</script>
<?php
$script = ob_get_contents();
ob_end_clean();

$this->appendArrayVar('headerParams', $script);

/* //Only implementing this for Control Panel type tree
$objCmsTree =$this->newObject('sectionstreemenu', 'cmsadmin');
echo $objCmsTree->getCMSAdminTree($currentNode);
*/

//Default Menu Below:
/*
?>

<a tabindex="0" href="#news-items-2" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="cmscontrolpanelmenu">Control Panel</a>
<div id="cmscontrolpanelitems" class="hidden">
<ul>

	<li><a href="#">Content</a>
		<ul>
			<li><a href="#">Add Content</a></li>
		</ul>
	</li>
	<li><a href="#">Sections</a>
		<ul>
			<li><a href="#">Add Section</a></li>
		</ul>
	</li>
	<li><a href="#">Templates</a>
		<ul>
			<li><a href="#">Add Template</a></li>
		</ul>
	</li>
	<li><a href="#">RSS Feeds</a>
		<ul>
			<li><a href="#">Add RSS Feed</a></li>
		</ul>
	</li>
	<li><a href="#">Archive</a></li>
	
</ul>
</div>

<?PHP
*/












/*
   Recursive deleting of a given directory

*/
/*
$objDir = $this->getObject('dir', 'files');

if ($objDir->deleteRecursive('/home/charl/trash/kim_wits')) {
	echo "Successfully Deleted /home/charl/trash/kim_wits";
} else {
	echo "Failed to delete /home/charl/trash/kim_wits";
}

/*
var_dump($_REQUEST);

echo "Body Returned: <br/>".$_REQUEST['body'];

//Testing a possible FCKEditor Bug:
//Editor doesn't submit the content back, getting tangled in DOM somehow

$objUtils = $this->newObject('cmsutils', 'cmsadmin');

echo $objUtils->topNav('createcontent');
echo "<br/><br/><br/><br/><br/>";
echo $objUtils->ContentTestForm();

/*


$action = ''; //default to load current template again for testing
$objForm = new form('addfrm', $this->uri(array('action' => $action, 'id' => $templateId), 'cmsadmin'));
$objForm->setDisplayType(3);

$introInput = $this->newObject('htmlarea', 'htmlelements');
$introInput->init('body', $introInputValue);
$introInput->setContent($introInputValue);
$introInput->setCMSToolBar();
$introInput->height = '200px';
$introInput->width = '100%';

$objForm->addToForm($introInput->show());
$objForm->addToForm("<br/>");
$objForm->addToForm(" <input type='submit' value='submit'> ");


$objForm->addRule('input_body', 'Please Type Something you Dumbass', 'required');

echo $objForm->show();
*/

//Testing SimpleTree Plugin
$jQuery = $this->newObject('jquery', 'jquery');
$jQuery->loadSimpleTreePlugin();

ob_start();
?>
<script type="text/javascript">
var simpleTreeCollection;
jQuery(document).ready(function(){
    simpleTreeCollection = jQuery('.simpleTree').simpleTree({
        autoclose: true,
        afterClick:function(node){
            alert("text-"+$('span:first',node).text());
        },
        afterDblClick:function(node){
            //alert("text-"+$('span:first',node).text());
        },
        afterMove:function(destination, source, pos){
            //alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);
        },
        afterAjax:function()
        {
            //alert('Loaded');
        },
        animate:true
        //,docToFolderConvert:true
    });
});
</script>
<?php
$script = ob_get_contents();
ob_end_clean();

$this->appendArrayVar('headerParams', $script);


$objCmsTree =$this->newObject('simpletreemenu', 'cmsadmin');
echo $objCmsTree->getCMSAdminTree($currentNode);


//Default Menu Below:
?>

<div class="contextMenu" id="myMenu1">
    <ul>
        <li id="add"><img src="images/folder_add.png" /> Add child</li>
        <li id="reload"><img src="images/arrow_refresh.png" /> Reload</li>
        <li id="edit"><img src="images/folder_edit.png" /> Edit</li>
        <li id="delete"><img src="images/folder_delete.png" /> Delete</li>
    </ul>
</div>
<div class="contextMenu" id="myMenu2">
    <ul>
        <li id="edit"><img src="images/page_edit.png" /> Edit</li>
        <li id="delete"><img src="images/page_delete.png" /> Delete</li>
    </ul>
</div>
<ul class="simpleTree">
    <li class="root" id='1'><span>Tree Root 1</span>
        <ul>
            
            <li class="open" id='2'><span>Tree Node 1</span>
                <ul>
                    
                    <li id='3'><span>Tree Node 1-1</span>
                        <ul class="ajax">
                            <li id='4'>{url:loadTree.php?tree_id=1}</li>
                        </ul>
                    </li>
                    
                </ul>
            </li>
            
            <li id='5'><span>Tree Node 2</span>
                <ul>
                    
                    <li id='6'><span>Tree Node 2-1</span>
                        <ul>
                            
                            <li id='7'><span>Tree Node 2-1-1</span></li>
                            
                            <li id='8'><span>Tree Node 2-1-2</span></li>
                            
                            <li id='9'><span>Tree Node 2-1-3</span></li>
                            
                            <li id='10'><span>Tree Node 2-1-4</span>
                                <ul class="ajax">
                                    <li id='11'>{url:loadTree.php?tree_id=1}</li>
                                </ul>
                            </li>
                            
                        </ul>
                    </li>
                    
                    <li id='12'><span>Tree Node 2-2</span>
                        <ul>
                            
                            <li id='13'><span>Tree Node 2-2-1</span></li>
                            
                        </ul>
                    </li>
                    
                    
                    <li id='14'><span>Tree Node 2-3</span>
                        <ul>
                            
                            <li id='15'><span>Tree Node 2-3-1</span>
                                    <ul>
                                        
                                        <li id='16'><span>Tree Node 2-3-1-1</span></li>
                                        
                                        <li id='17'><span>Tree Node 2-3-1-2</span></li>
                                        
                                        <li id='18'><span>Tree Node 2-3-1-3</span>
                                            <ul>
                                                
                                                <li id='19'><span>Tree Node 2-3-1-3-1</span></li>
                                                
                                            </ul>
                                        </li>
                                        
                                        <li id='20'><span>Tree Node 2-3-1-4</span></li>
                                        
                                        <li id='21'><span>Tree Node 2-3-1-5</span></li>
                                        
                                        <li id='22'><span>Tree Node 2-3-1-6</span>
                                            <ul>
                                                
                                                <li id='23'><span>Tree Node 2-3-1-6-1</span></li>
                                                
                                            </ul>
                                        </li>
                                        
                                        <li id='24'><span>Tree Node 2-3-1-7</span></li>
                                        
                                        <li id='25'><span>Tree Node 2-3-1-8</span></li>
                                        
                                        <li id='26'><span>Tree Node 2-3-1-9</span>
                                            <ul>
                                                
                                                <li id='27'><span>Tree Node 2-3-1-9-1</span></li>
                                                
                                            </ul>
                                        </li>
                                        
                                    </ul>
                            </li>
                            
                        </ul>
                    </li>
                    
                </ul>
            </li>
            
        </ul>
    </li>
</ul>

<?php

/*
//Testing Flexigrid Plugin
$jQuery = $this->newObject('jquery', 'htmlelements');
$jQuery->loadJqGridPlugin();

ob_start();
?>
<script type="text/javascript"> 
    // initialise Superfish 
    jQuery(document).ready(function(){
        jQuery("#list2").jqGrid({
            // the url parameter tells from where to get the data from server
            // adding ?nd='+new Date().getTime() prevent IE caching
            //url:'example.php?nd='+new Date().getTime(),
            url:'flexijson.php',
            // datatype parameter defines the format of data returned from the server
            // in this case we use a JSON data
            datatype: "json",
            // colNames parameter is a array in which we describe the names
            // in the columns. This is the text that apper in the head of the grid.
            colNames:['Folder Title','Section Name', 'Pages', 'Display Type','Order','Published','Options', 'Date'],
            // colModel array describes the model of the column.
            // name is the name of the column,
            // index is the name passed to the server to sort data
            // note that we can pass here nubers too.
            // width is the width of the column
            // align is the align of the column (default is left)
            // sortable defines if this column can be sorted (default true)
            colModel:[
                //{name:'id',index:'id', width:55},
                {name:'title',index:'title', width:150},
                {name:'sectionname',index:'title asc, date', width:100},
                {name:'pages',index:'pages', width:40, align:"center"},
                {name:'display',index:'layout', width:90, align:"right"},
                {name:'order',index:'order', width:80,align:"right"},   
                {name:'published',index:'published', width:80, sortable:false},
                {name:'options',index:'options', width:80,align:"right"},
                {name:'date',index:'date', width:80,align:"right"}
            ],
            multiselect: true,
            height: "100%",
            // pager parameter define that we want to use a pager bar
            // in this case this must be a valid html element.
            // note that the pager can have a position where you want
            //pager: jQuery('#pager2'),
            // rowNum parameter describes how many records we want to
            // view in the grid. We use this in example.php to return
            // the needed data.
            rowNum:100,
            // rowList parameter construct a select box element in the pager
            //in wich we can change the number of the visible rows
            rowList:[10,20,30],
            // path to mage location needed for the grid
            imgpath: 'themes/sand/images',
            // sortname sets the initial sorting column. Can be a name or number.
            // this parameter is added to the url
            sortname: 'id',
            //viewrecords defines the view the total records from the query in the pager
            //bar. The related tag is: records in xml or json definitions.
            viewrecords: true,
            //sets the sorting order. Default is asc. This parameter is added to the url
            sortorder: "desc",
            caption: "Sections Manager"
        });
//*/

/*
        //jQuery("#list2").jqGrid();
    });
</script>
<?php
$jqgrid = ob_get_contents();
ob_end_clean();

$this->appendArrayVar('headerParams', $jqgrid);
?>

<!-- the grid definition in html is a table tag with class 'scroll' -->
<table id="list2" class="scroll" cellpadding="0" cellspacing="0"></table>

<!-- pager definition. class scroll tels that we want to use the same theme as grid -->
<div id="pager2" class="scroll" style="text-align:center;"></div>

<?php
/*
//Testing Flexigrid Plugin
$jQuery = $this->newObject('jquery', 'htmlelements');
$jQuery->loadFlexgridPlugin();


ob_start();
?>
<script type="text/javascript"> 
    // initialise Superfish 
    jQuery(document).ready(function(){
/*
         //XML Implementation
        jQuery("#flexixml").flexigrid
                    (
                    {
                    url: 'flexixml.xml',
                    dataType: 'xml',
                    colModel : [
                        {display: 'ISO', name : 'iso', width : 40, sortable : true, align: 'center'},
                        {display: 'Name', name : 'name', width : 180, sortable : true, align: 'left'},
                        {display: 'Printable Name', name : 'printable_name', width : 120, sortable : true, align: 'left'},
                        {display: 'ISO3', name : 'iso3', width : 130, sortable : true, align: 'left', hide: true},
                        {display: 'Number Code', name : 'numcode', width : 80, sortable : true, align: 'right'}
                        ],
                    searchitems : [
                        {display: 'title', name : 'title'},
                        {display: 'Name', name : 'name', isdefault: true}
                        ],
                    sortname: "title",
                    sortorder: "asc",
                    usepager: true,
                    title: 'Sections Manager',
                    useRp: true,
                    rp: 15,
                    showTableToggleBtn: true,
                    width: 700,
                    height: 200
                    }
                    );   
    //*/
/*
         //JSON Implementation
        jQuery("#flexjson").flexigrid
                    (
                    {
                    url: 'flexijson.php',
                    dataType: 'json',
                    colModel : [
                        {display: 'ISO', name : 'iso', width : 40, sortable : true, align: 'center'},
                        {display: 'Name', name : 'name', width : 180, sortable : true, align: 'left'},
                        {display: 'Printable Name', name : 'printable_name', width : 120, sortable : true, align: 'left'},
                        {display: 'ISO3', name : 'iso3', width : 130, sortable : true, align: 'left', hide: true},
                        {display: 'Number Code', name : 'numcode', width : 80, sortable : true, align: 'right'}
                        ],
                    searchitems : [
                        {display: 'title', name : 'title'},
                        {display: 'Name', name : 'name', isdefault: true}
                        ],
                    sortname: "title",
                    sortorder: "asc",
                    usepager: true,
                    title: 'Sections Manager',
                    useRp: true,
                    rp: 15,
                    showTableToggleBtn: true,
                    width: 700,
                    height: 200
                    }
                    );   
    //*/
    /*
        jQuery("#flex1").flexigrid({
            title: 'Sections Manager',
            width: 700,
            height: 200,
        });
    //*/
/*
        jQuery("#flexme1").flexigrid();
    //*/
/*
    }); 

    </script>
<?php
$flexjs = ob_get_contents();
ob_end_clean();

$this->appendArrayVar('headerParams', $flexjs);

$css = "    

     .flexigrid div.fbutton .add
        {
            background: url(css/images/add.png) no-repeat center left;
        }   

    .flexigrid div.fbutton .delete
        {
            background: url(css/images/close.png) no-repeat center left;
        }   

";

$this->appendArrayVar('headerParams', '<style>'.$css.'</style>');



?>

<b>Example 1</b>
<p>
The most basic example with the zero configuration, with a table converted into flexigrid 
(<a href="#" onclick="$(this).parent().next().toggle(); return false;">Show sample code</a>)
</p>
<div class="code">
    <pre>$('.flexme').flexigrid();</pre>

</div>
<table id="flexme1">
    <thead>
            <tr>
                <th width="100">Col 1</th>
                <th width="100">Col 2</th>
                <th width="100">Col 3 is a long header name</th>
                <th width="300">Col 4</th>

            </tr>
    </thead>
    <tbody>
            <tr>
                <td>This is data 1 with overflowing content</td>
                <td>This is data 2</td>
                <td>This is data 3</td>

                <td>This is data 4</td>
            </tr>
            <tr>
                <td>This is data 1</td>
                <td>This is data 2</td>
                <td>This is data 3</td>
                <td>This is data 4</td>

            </tr>
            <tr>
                <td>This is data 1</td>
                <td>This is data 2</td>
                <td>This is data 3</td>
                <td>This is data 4</td>
            </tr>

            <tr>
                <td>This is data 1</td>
                <td>This is data 2</td>
                <td>This is data 3</td>
                <td>This is data 4</td>
            </tr>
            <tr>

                <td>This is data 1</td>
                <td>This is data 2</td>
                <td>This is data 3</td>
                <td>This is data 4</td>
            </tr>
            <tr>
                <td>This is data 1</td>

                <td>This is data 2</td>
                <td>This is data 3</td>
                <td>This is data 4</td>
            </tr>
            <tr>
                <td>This is data 1</td>
                <td>This is data 2</td>

                <td>This is data 3</td>
                <td>This is data 4</td>
            </tr>
            <tr>
                <td>This is data 1</td>
                <td>This is data 2</td>
                <td>This is data 3</td>

                <td>This is data 4</td>
            </tr>
            <tr>
                <td>This is data 1</td>
                <td>This is data 2</td>
                <td>This is data 3</td>
                <td>This is data 4</td>

            </tr>
            <tr>
                <td>This is data 1</td>
                <td>This is data 2</td>
                <td>This is data 3</td>
                <td>This is data 4</td>
            </tr>

            <tr>
                <td>This is data 1</td>
                <td>This is data 2</td>
                <td>This is data 3</td>
                <td>This is data 4</td>
            </tr>
            <tr>

                <td>This is data 1</td>
                <td>This is data 2</td>
                <td>This is data 3</td>
                <td>This is data 4</td>
            </tr>
            
    </tbody>
</table>

<br />


<?PHP



//$script = ob_get_contents();
//ob_end_flush();

//ob_end_clean();
//$this->appendArrayVar('headerParams', $script);


/*
$treeMenu = $this->newObject('superfishtree', 'cmsadmin');

echo $treeMenu->getCMSAdminTree(0);
*/
/*

//Tree Style Sections List (Spaces)

$link =  $this->newObject('link', 'htmlelements');

$arrSections = $this->getsections();

/* //With images
foreach($arrSections as $section) {
    $pref = "";
    $matches = split('<', $section['title']);
    $img = split('>', $matches[1]);
    $image = '<'.$img[0].'>';
    $linkText = $img[1];
    $noSpaces = strlen($matches[0]);
    
    for ($i = 1; $i < $noSpaces; $i++) {
        $pref .= '&nbsp;&nbsp;';
    }
    $pref .= $image;
    
    $section = $this->_objSections->getSection($section['id']);
    //View section link
    $link->link = $linkText;
    $link->href = $this->uri(array('action' => 'viewsection', 'id' => $section['id']));
    $viewSectionLink = $pref.$link->show();
    echo $viewSectionLink.'<br/>';
}
*/
/*
//For the sections select list!

$sectionList =  "<select name='test'>";
foreach($arrSections as $section) {
    $pref = "";
    $matches = split('<', $section['title']);
    $img = split('>', $matches[1]);
    $image = '<'.$img[0].'>';
    $linkText = $img[1];
    $noSpaces = strlen($matches[0]);
var_dump($section['title'] . ' ' . $noSpaces);    
    for ($i = 1; $i < $noSpaces; $i++) {
        $pref .= '&nbsp;&nbsp;';
    }
    
    $section = $this->_objSections->getSection($section['id']);
    //View section link
    $link->link = $linkText;
    $link->href = $this->uri(array('action' => 'viewsection', 'id' => $section['id']));
    $viewSectionLink = $pref.$link->show();
    $sectionList .= '<option>'.$viewSectionLink.'</option>';
}
$sectionList .= "</select>";

echo $sectionsList;

//*/

/*
$jTab = $this->newObject('jquerytabs', 'htmlelements');

$jTab->addTab('tab1', 'content1');
$jTab->addTab('tab2', 'content2');
$jTab->addTab('tab3', 'content3');

$jTab->removeTab('tab3');

echo $jTab->show();

*/

/*
$jQuery = $this->newObject('jquery', 'htmlelements');
$jQuery->loadUITabbing();

$script = '<script>
  jQuery(document).ready(function(){
    jQuery("#jQueryTab_5 > ul").tabs({
        selected: 2
    });
    
  });
  </script>';

$this->appendArrayVar('headerParams', $script);

?>

<div id="jQueryTab_5" class="flora">
            <ul>

                <li><a href="#fragment-1"><span>One</span></a></li>
                <li><a href="#fragment-2"><span>Two</span></a></li>
                <li><a href="#fragment-3"><span>Three</span></a></li>
            </ul>
            <div id="fragment-1" class="selected">
                <p>First tab is active by default:</p>
                <pre><code>$('#example > ul').tabs();</code></pre>
            </div>
            <div id="fragment-2">
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
            </div>
            <div id="fragment-3">
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
            </div>
        </div>

<?php

/*

$jQuery = $this->newObject('jquery', 'htmlelements');

//Testing jQuery 1.2.6 SuperFish Menu
$jQuery->loadSuperFishMenuPlugin();

ob_start();
?>

    <script type="text/javascript"> 
    // initialise Superfish 
    jQuery(document).ready(function(){ 
        jQuery("ul.sf-menu").superfish({ 
        animation: {opacity:'show'},   // slide-down effect without fade-in 
        width: 300,
        delay:     0,               // 1.2 second delay on mouseout 
        speed: 'fast',
        dropShadows: false
        }); 
    }); 
    
    </script>


<?PHP
$script = ob_get_contents();
ob_end_clean();

$this->appendArrayVar('headerParams', $script);
?>


<ul class="sf-menu sf-vertical">
            <li class="current">
                <a href="#a">menu item &#8594;</a>
                <ul>
                    <li>
                        <a href="#aa">menu item that is quite long</a>
                    </li>
                    <li class="current">
                        <a href="#ab">menu item &#8594;</a>
                        <ul>
                            <li class="current"><a href="#">menu item</a></li>
                            <li><a href="#aba">menu item</a></li>
                            <li><a href="#abb">menu item</a></li>
                            <li><a href="#abc">menu item</a></li>
                            <li><a href="#abd">menu item</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">menu item &#8594;</a>
                        <ul>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">menu item &#8594;</a>
                        <ul>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">menu item</a>
            </li>
            <li>
                <a href="#">menu item &#8594;</a>
                <ul>
                    <li>
                        <a href="#">menu item &#8594;</a>
                        <ul>
                            <li><a href="#">short</a></li>
                            <li><a href="#">short</a></li>
                            <li><a href="#">short</a></li>
                            <li><a href="#">short</a></li>
                            <li><a href="#">short</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">menu item &#8594;</a>
                        <ul>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">menu item &#8594;</a>
                        <ul>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">menu item &#8594;</a>
                        <ul>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">menu item &#8594;</a>
                        <ul>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                            <li><a href="#">menu item</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">menu item</a>
            </li>   
        </ul>




<?php

/*
$jQuery->loadAccordionMenuPlugin();


ob_start();
?>

<script type="text/javascript">
jQuery().ready(function(){  
    // applying the settings
    jQuery('#theMenu').Accordion({
        active: 'h3.selected',
        header: 'h3.head',
        alwaysOpen: false,
        animated: true,
        showSpeed: 400,
        hideSpeed: 800
    });
    jQuery('#xtraMenu').Accordion({
        active: 'h4.selected',
        header: 'h4.head',
        alwaysOpen: false,
        animated: true,
        showSpeed: 400,
        hideSpeed: 800
    });
}); 
</script>


<?php
$script = ob_get_contents();
ob_end_clean();

$this->appendArrayVar('headerParams', $script);

?>

    
        <h1>jQuery Accordion Example</h1>
        
        <h2>A Navigation Menu</h2>
        <p>Unordered List with anchors and nested lists. Title 2 also demonstrates how to add a second level list.<br />Take a look at the source code to see how easy it's done!</p>
        <p><a href="index.php">The original example is found here</a>.</p>


        <ul id="theMenu">
            <li>
                <h3 class="head"><a href="javascript:;">Title 1</a></h3>
                <ul>
                    <li><a href="index-multi.php">Content 1 1</a></li>
                    <li><a href="index-multi.php">Content 1 2</a></li>
                    <li><a href="index-multi.php">Content 1 3</a></li>
                </ul>
            </li>
            <li>
                <h3 class="head"><a href="javascript:;">Title 2</a></h3>
                <ul>
                    <li>
                        <ul id="xtraMenu">
                            <li>
                                <h4 class="head"><a href="javascript:;">Content xtra 2 1a</a></h4>
                                <ul>
                                    <li><a href="index-multi.php">Content 2 1 1</a></li>
                                    <li><a href="index-multi.php">Content 2 1 2</a></li>
                                    <li><a href="index-multi.php">Content 2 1 3</a></li>
                                </ul>
                            </li>
                            <li>
                                <h4 class="head"><a href="javascript:;">Content xtra 2 1b</a></h4>
                                <ul>
                                    <li><a href="index-multi.php">Content 2 2 1</a></li>
                                    <li><a href="index-multi.php">Content 2 2 2</a></li>
                                    <li><a href="index-multi.php">Content 2 2 3</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a href="index-multi.php">Content 2 2</a></li>
                    <li><a href="index-multi.php">Content 2 3</a></li>
                </ul>
            </li>
            <li>
                <h3 class="head"><a href="javascript:;">Title 3</a></h3>
                <ul>
                    <li><a href="index-multi.php">Content 3 1</a></li>
                    <li><a href="index-multi.php">Content 3 2</a></li>
                    <li><a href="index-multi.php">Content 3 3</a></li>
                </ul>
            </li>
            <li>
                <h3 class="head"><a href="javascript:;">Title 4</a></h3>
                <ul>
                    <li><a href="index-multi.php">Content 4 1</a></li>
                    <li><a href="index-multi.php">Content 4 2</a></li>
                    <li><a href="index-multi.php">Content 4 3</a></li>
                </ul>
            </li>
            <li>
                <h3 class="head"><a href="javascript:;">Title 5</a></h3>
                <ul>
                    <li><a href="index-multi.php">Content 5 1</a></li>
                    <li><a href="index-multi.php">Content 5 2</a></li>
                    <li><a href="index-multi.php">Content 5 3</a></li>
                </ul>
            </li>
        </ul>

    <p>Here's some text to show that the height (350px) for "theMenu" doesn't affect the following text...<br />
    You don't really need it but you can...</p>

<?PHP
*/
?>

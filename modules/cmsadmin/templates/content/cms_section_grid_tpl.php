<?PHP

//Loading the jQuery Grid Plugin
$jQuery = $this->newObject('jquery', 'jquery');
//$jQuery->loadJqGridPlugin('3.2.4', 'coffee');
//$jQuery->loadJqGridPlugin('3.2.4', 'green');
//$jQuery->loadJqGridPlugin('3.2.4', 'sand');
$jQuery->loadJqGridPlugin('3.2.4', 'cms');

$script = <<<GRIDSCRIPT
<script type="text/javascript"> 
    // initialise Superfish 
    jQuery(document).ready(function(){
        jQuery("#list2").jqGrid({
            // the url parameter tells from where to get the data from server
            // adding ?nd='+new Date().getTime() prevent IE caching
            url:'index.php?module=cmsadmin&action=jsonsectiongrid',
            // datatype parameter defines the format of data returned from the server
            // in this case we use a JSON data
            datatype: "json",
            // colNames parameter is a array in which we describe the names
            // in the columns. This is the text that apper in the head of the grid.

            colNames:['{$this->objLanguage->languageText('mod_cmsadmin_menuname', 'cmsadmin')}',
                      //'{$this->objLanguage->languageText('mod_cmsadmin_nameofsection', 'cmsadmin')}',
                      '{$this->objLanguage->languageText('word_pages')}',
                      '{$this->objLanguage->languageText('mod_cmsadmin_displaytype', 'cmsadmin')}',
                      '{$this->objLanguage->languageText('word_order')}',
                      '{$this->objLanguage->languageText('word_published')}',
                      '{$this->objLanguage->languageText('word_options')}',
                      '{$this->objLanguage->languageText('word_date')}',],

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
                //{name:'sectionname',index:'title asc, date', width:100},
                {name:'pages',index:'pages', width:50, align:"center", sortable:false},
                {name:'display',index:'layout', width:90, align:"left"},
                {name:'ordering',index:'ordering', width:50,align:"left", sortable:false},
                {name:'published',index:'published', width:65, align:"center", sortable:false},
                {name:'options',index:'options', width:80,align:"left", sortable:false},
                {name:'datecreated',index:'datecreated', width:130,align:"left"}
            ],
            multiselect: true,
            height: "100%",
            // pager parameter define that we want to use a pager bar
            // in this case this must be a valid html element.
            // note that the pager can have a position where you want
            //pager: jQuery('#sec_nav'),
            // rowNum parameter describes how many records we want to
            // view in the grid. We use this in example.php to return
            // the needed data.
            rowNum:100,
            // rowList parameter construct a select box element in the pager
            //in wich we can change the number of the visible rows
            rowList:[10,20,30],
            // path to mage location needed for the grid
            //imgpath: 'themes/sand/images',
            imgpath: 'core_modules/htmlelements/resources/jquery/plugins/jqgrid/3.2.4/themes/cms/images',
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
        //jQuery("#list2").jqGrid();
    });
</script>
GRIDSCRIPT;

$this->appendArrayVar('headerParams', $script);
?>

<!-- the grid definition in html is a table tag with class 'scroll' -->
<table id="list2" class="scroll" cellpadding="0" cellspacing="0"></table>

<!-- pager definition. class scroll tels that we want to use the same theme as grid -->
<div id="sec_nav" class="scroll" style="text-align:center;"></div>

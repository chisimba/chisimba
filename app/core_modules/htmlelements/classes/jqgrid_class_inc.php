<?php
/**
 * jQuery Grid Class
 * 
 * HTML control class to create a sortable Grid that accepts JSON, XML as ajax requests
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author Charl Mert <charl.mert@gmail.com>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id: tabber_class_inc.php 10308 2008-08-26 12:18:36Z tohir $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* HTML control class to create multiple column grids using the jQuery jqGrid plugin.
*
* The loadGrid($theme) function accepts a theme, 'basic'. This function should be called
* only once per page.
* 
* The Grid Panel is optional and can be added simply by setting the $this->panelId to a non empty string
* 
* @abstract 
* @package jqgrid
* @category HTML Controls
* @copyright 2007, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Charl Mert
* @example
*/
class jqgrid extends object 
{

    /**
    * @var string $gridId: The div id of the grid container
    * @access public
    */
    public $gridId = '';
    
    /**
    * @var string $panelId: The div id of the panel container (grid control panel)
    * @access public
    */
    public $panelId = '';

    /**
    * @var string $caption: The Display Name of the grid
    * @access public
    */
    public $caption = '';

    /**
    * @var string $theme: The name of the theme to load 
    * (can be located in 'core_modules/htmlelements/resources/jquery/plugins/jqgrid/3.2.4/themes')
    * @access public
    */
    public $theme = '';
    
    /**
    * @var $columns array :  Array that holds all the columns for the grid
    * @access private
    */
    private $columns = array();

    /**
    * @var string $url: The url to load the data from (ajax request)
    * @access public
    */
    public $url = '';

    /**
    * @var string $editurl: The url to handle grid db edit events
    * @access public
    */
    public $editurl = '';

    /**
    * @var string $dataType: dataType to load. can be json or xml
    * @access public
    */
    public $dataType = '';
   
   /**
    * @var string multiselect: This option enables the ability to select multiple rows in the grid
    * @access public
    */
    public $multiselect = '';

   /**
    * @var string $width: This can be used to set the overall width of the tab container
    * @access public
    */
    public $height = '100%';

   /**
    * Constuctor
    * 
    * @access public
    * @return void
    */    
    public function init()
    {
		$this->jQuery = $this->newObject('jquery', 'htmlelements');
        $this->jQuery->loadLiveQueryPlugin();
        $this->loadClass('layer', 'htmlelements');

        $this->gridId = 'chisimba_grid_01';
        $this->panelId = '';
        $this->containerId = 'chisimba_grid_container_01';
        $this->theme = 'basic';
        $this->columns = array();
        $this->url = '';
        $this->dataType = 'json';
        $this->multiselect = 'true';
        $this->height = '100%';
        $this->loadComplete = '';
    }

   /**
    * Loads the grid with the specified theme
    * 
    * @access public
    * @return void
    */    
    public function show()
    {
        $str = "
<div id='chisimba_grid_container' class='grid_padding_bottom'>

<!-- the grid definition in html is a table tag with class 'scroll' -->
<table id=\"".$this->gridId."\" class=\"scroll\" cellpadding=\"0\" cellspacing=\"0\"></table>
";

        //panelId is optional
        if ($this->panelId != ''){
        $str .= "
<!-- pager definition. class scroll tels that we want to use the same theme as grid -->
<div id=\"".$this->panelId."\" class=\"scroll\" style=\"text-align:center;\"></div>

</div>
";


        }

        $objLayer = new layer();
        $objLayer->cssId = $this->containerId;
        $objLayer->str = $str;

        return $objLayer->show();
    }

   /**
    * Loads the grid with the specified theme
    * Developers should only load one grid per page
    * @access public
    * @return void
    */    
    public function loadGrid($theme = 'basic')
    {
        $this->theme = $theme;
        $this->jQuery->loadJqGridPlugin('3.2.4', $this->theme);
    }

   /**
    * Sets up the grid instance.
    * Can be called many times for any number of different grids to load
    * 
    * @access public
    * @return void
    */    
    public function buildGrid($caption = '')
    {

        //panelId is optional
        if ($this->panelId != ''){
            $strPanelId = 'pager: jQuery("#'.$this->panelId.'"),'."\n";
        }

        //Generating the colNames construct
        if(isset($this->columns) && is_array($this->columns)){

            $str = 'colNames:[';

            foreach ($this->columns as $key => $value){
                $str .= "\n'".$this->columns[$key]['name']."',\n";
            }   
            $str = substr($str, 0, strlen($str) - 2);

            $str .= '],'."\n";

        }

        $colNames = $str;

        //Generating the colModel construct
        if(isset($this->columns) && is_array($this->columns)){            
//{name:'title',index:'title', width:150},
            $str = 'colModel:[';

            foreach ($this->columns as $key => $value){

                $sortText = ($this->columns[$key]['sortable'])? 'true' : 'false';

                $str .= '{';
    
                $str .= 'name:\''.$this->columns[$key]['name']."',";
                $str .= 'index:\''.$this->columns[$key]['index']."',";
                $str .= 'width:'.$this->columns[$key]['width'].",";
                $str .= 'align:\''.$this->columns[$key]['align']."',";
                $str .= 'sortable:'.$sortText."},\n";
            }
            $str = substr($str, 0, strlen($str) - 2);

            $str .= '],'."\n";

        }

        $colModel = $str;
        $multiselect = ($this->multiselect == true) ? 'true' : 'false';
        
        $script = <<<GRIDSCRIPT
<script type="text/javascript"> 
    // Initialise jQuery Grid
    jQuery(document).ready(function(){
        jQuery("#{$this->gridId}").jqGrid({
            // the url parameter tells from where to get the data from server
            // adding ?nd='+new Date().getTime() prevent IE caching
            url:'{$this->url}',
            editurl:'{$this->editurl}',
            // datatype parameter defines the format of data returned from the server
            // in this case we use a JSON data
            datatype: "{$this->dataType}",
            // colNames parameter is a array in which we describe the names
            // in the columns. This is the text that apper in the head of the grid.

            $colNames

            // colModel array describes the model of the column.
            // name is the name of the column,
            // index is the name passed to the server to sort data
            // note that we can pass here nubers too.
            // width is the width of the column
            // align is the align of the column (default is left)
            // sortable defines if this column can be sorted (default true)

            $colModel

            multiselect: $multiselect,
            height: "{$this->height}",
            // pager parameter define that we want to use a pager bar
            // in this case this must be a valid html element.
            // note that the pager can have a position where you want
            $strPanelId
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
            caption: "{$this->caption}",

            //Callback that gets fired when grid has loaded
            loadComplete: {$this->loadComplete}
        });
//*/
        //jQuery("#list2").jqGrid();
    });
</script>
GRIDSCRIPT;

        $this->appendArrayVar('headerParams', $script);
        return true;
    }
        
   /**
    * Method that adds a column
    * 
    * @access public
    * @param array $tab : Can hold the following values
    * name string
    * content string
    * extra string
    * @return void
    */    
    function addColumn($name = NULL, $index = NULL, $width = '80', $align = 'left', $sortable = true){
        //{name:'options',index:'options', width:80,align:"left", sortable:false},

	    $columns['name'] = $name;
        $columns['index'] = $index;
        $columns['width'] = $width;
        $columns['align'] = $align;
        $columns['sortable'] = $sortable;
    	array_push($this->columns,$columns);

    }

   /**
    * Method that removes a tab
    * 
    * @access public
    * @param array $index the index of the column you want to delete
    * @return void
    */    
    function removeColumn($index = NULL){

		$tmpColumns = array();
		foreach ($this->columns as $key => $value){
			if ($this->columns[$key]['index'] != $name){
    			array_push($tmpColumns, $value);
			}
		}
        $this->columns = $tmpColumns;
    }
    
    /**
    * Method to get a list of current tabs
    *
    * @access public
    * @return array $tabArray: The tabs for an instance of the tabber object
    */
    public function getColumns()
    {
        $columnArray = $this->columns;
        return $columnArray;
    }
    
    /**
    * Method to attach an element's event to the dialog (to trigger the dialog)
    * The default event is "click". These can be any of the valid jquery events:
    *
    * blur, change, click, dblclick, error, focus, keydown, keypress, keyup, 
    * mousedown, mousemove, mouseout, mouseout, resize, scroll, select, submit etc...
    *
    * See http://docs.jquery.com/Events for an extensive list of events.
    *
    * @access public
    * @param string $id The id of the element that will be clicked/{event} to trigger the grid to reload
    * @param string $event To specify which event will trigger the reload.
    * @return void
    */
    public function attachRefreshEvent($id, $event = 'click')
    {

$script = <<<REFRESHBIND
    <script type="text/javascript" >
        jQuery(document).ready(function(){
            jQuery('#$id').livequery('$event',function(){
                jQuery('#{$this->gridId}').trigger('reloadGrid');
            });
        });
    </script>
REFRESHBIND;
        $this->appendArrayVar('headerParams', $script);
    }


    /**
    * Method to attach an element's event to the delete(row_id) method.
    * The trigger will cause the grid to delete the row at the specified id via ajax request.
    * By using liveQuery, the event's are seamlessly rebound even when elements are loaded via ajax.
    * 
    * @access public
    * @param string $id The id of the element that will be clicked/{event} to trigger the grid to reload
    * @param string $event To specify which event will trigger the reload.
    * @return void
    */
    public function attachDeleteEvent($id, $row_id,$event = 'click')
    {

$script = <<<DELETEBIND
<script type="text/javascript" >
jQuery(document).ready(function(){
    jQuery('#$id').livequery('$event',function(){
        jQuery('#chisimba_grid_01').delGridRow('$row_id');
    });
});
</script>
DELETEBIND;
        $this->appendArrayVar('headerParams', $script);
    }


    /**
    * Method to attach an element's event to the delete(row_id's) method.
    * The trigger will cause the grid to delete the the currently selected rows via ajax request.
    * 
    * @access public
    * @param string $id The id of the element that will be clicked/{event} to trigger the grid to reload
    * @param string $event To specify which event will trigger the reload.
    * @return void
    */
    public function attachDeleteMultipleEvent($id, $event = 'click')
    {

$script = <<<DELETEMULTIBIND
<script type="text/javascript" >
jQuery(document).ready(function(){
    jQuery('#$id').livequery('$event',function(){
        jQuery('#chisimba_grid_01').delGridRow('test');
    });
});
</script>
DELETEMULTIBIND;
        $this->appendArrayVar('headerParams', $script);
    }


}
?>

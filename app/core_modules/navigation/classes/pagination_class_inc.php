<?php


// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building an ajax pagination item
*
* This class builds an interface to an ajax pagination system. Developers need
* to call this class and then simply build the results in an ajax function of
* their modules controller
*
* Results are taken from:
* index.php?module=<yourmodule>&action=<youraction>&page={ajaxdriven}
*
* @author Tohir Solomons
* @copyright (c)2008 UWC
* @package navigation
* @verson 0.1
*/

class pagination extends object
{
    /**
    *
    * @var string $id: the ID tag from the CSS
    * @access public
    */
    public $id;
    
    /**
    *
    * @var string $module: Module results should be returned from
    * @access public
    */
    public $module;
    
    /**
    *
    * @var string $action: Action in module to use to generate results
    * @access public
    */
    public $action;
    
    /**
    *
    * @var int $numPages: Number of Pagination links
    * @access public
    */
    public $numPageLinks;

    /**
    * Method to construct the class.
    */
    public function init()
    {
        $this->id = 'pagenavigation';
        $this->module = 'navigation';
        $this->action = 'testpage';
        $this->numPageLinks = 20;
    }

    /**
    * Method to show the pagination
    *
    * @param null
    * @access publc
    * @return string
    */
    public function show()
    {
        $this->addJS();
        
        $str = '<div id="paginationresults_'.$this->id.'">...</div>';
        $str .= '<div id="pagination_'.$this->id.'" class="pagination"></div><br style="clear:both;" />';
        
        $str .= '
<script language="JavaScript" type="text/javascript" >

    // Method to load pagination results
    function loadPaginationResults_'.$this->id.'(page_id, jq){
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module='.$this->module.'&action='.$this->action.'&page="+page_id,
            success: function(msg){
                jQuery("#paginationresults_'.$this->id.'").html(msg);
            }
        });
    }
    
    // Create pagination element
    jQuery("#pagination_'.$this->id.'").pagination('.$this->numPageLinks.', {
        items_per_page: 1,
        num_edge_entries: 2,
        num_display_entries: 10,
        callback: loadPaginationResults_'.$this->id.'
    });
    
    // Load First Page
    loadPagination_'.$this->id.'(0);

</script>
';
        
        return $str;
    }
    
    /**
    * Method to load the JavaScript and CSS
    */
    private function addJS()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery_pagination/jquery.pagination.js'));
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" href="'.$this->getResourceUri('jquery_pagination/pagination.css').'" />');
    }
}
?>
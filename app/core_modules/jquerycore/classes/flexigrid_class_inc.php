<?php
/**
 *
 * Flexigrid class for jquery
 *
 * This class is a wrapper for the jquery flexigrid plugin
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
 * @package   jquerycore
 * @author    Kevin Cyster kcyster@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Wrapper class for the jquery flexigrid plugin
*
* @package   jquerycore
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class flexigrid extends object
{
    /**
     * 
     * Variable to hold the id of the element
     * 
     * @access proteced
     * @var string
     */
    protected $cssId = "flexi_grid";

    /**
     * 
     * Variable to hold the title option
     * 
     * @access protected
     * @var string
     */
    protected $title;

    /**
     * 
     * Variable to the ajax url
     * 
     * @access proteced
     * @var string
     */
    protected $url;

    /**
     * 
     * Variable to hold the data type
     * 
     * @access proteced
     * @var string
     */
    protected $dataType = 'json';

    /**
     * 
     * Variable to hold the columns
     * 
     * @access proteced
     * @var array
     */
    protected $colModel = array();

    /**
     * 
     * Variable to hold the buttons
     * 
     * @access protected
     * @var array
     */
    protected $buttons = array();

    /**
     * 
     * Variable to hold the search items
     * 
     * @access protected
     * @var array
     */
    protected $searchitems = array();

    /**
     * 
     * Variable to hold the sortname option
     * 
     * @access protected
     * @var string
     */
    protected $sortname;

    /**
     * 
     * Variable to hold the sortorder option
     * 
     * @access protected
     * @var string
     */
    protected $sortorder = 'asc';
    /**
     * 
     * Variable to hold the usepager option
     * 
     * @access protected
     * @var boolean
     */
    protected $usepager = TRUE;

    /**
     * 
     * Variable to hold the useRp (user specified results per page) option
     * 
     * @access protected
     * @var boolean
     */
    protected $useRp = TRUE;

    /**
     * 
     * Variable to hold the rp (results per page) option
     * 
     * @access protected
     * @var inetger
     */
    protected $rp = 10;

    /**
     * 
     * Variable to hold the show table toggle button option
     * 
     * @access protected
     * @var boolean
     */
    protected $showTableToggleBtn = TRUE;

    /**
     * 
     * Variable to hold the resizable option
     * 
     * @access protected
     * @var boolean
     */
    protected $resizable = TRUE;

    /**
     * 
     * Variable to hold the width option
     * 
     * @access protected
     * @var mixed
     */
    protected $width = 'auto';

    /**
     * 
     * Variable to hold the height option
     * 
     * @access protected
     * @var mixed
     */
    protected $height = 'auto';

    /**
     * 
     * Variable to hold the single select option
     * 
     * @access protected
     * @var boolean
     */
    protected $singleSelect = TRUE;

    /**
     * 
     * Variable to hold the seperator option
     * 
     * @access protected
     * @var array
     */
    protected $separatorAfter = array();

    /**
     *
     * Intialiser for the flexigrid class.
     * 
     * @access public
     * @return VOID
     */
    public function init()
    {
        $loadedPlugins = $this->getSession('plugins', array(), 'skin');
        $loadedPlugins[] = 'flexigrid';
        $uniquePlugins = array_unique($loadedPlugins);
        $this->setSession('plugins', $uniquePlugins, 'skin');
    }
    
    /**
     *
     * Method to set the flexigrid element id.
     * 
     * @access public
     * @param string $cssId The id of the flexigrid element
     * @return VOID
     */
    public function setCssId($cssId)
    {
        if (!empty($cssId) && is_string($cssId))
        {
            $this->cssId = $cssId;
        }
    }
    
    /**
     *
     * Method to set the title option.
     * 
     * @access public
     * @param integer $title The title of the grid
     * @return VOID
     */
    public function setTitle($title)
    {
        if (!empty($title) && is_string($title));
        {
            $this->title = $title;
        }
    }

    /**
     *
     * Method to set the ajax url.
     * 
     * @access public
     * @param string $url The ajax url
     * @return VOID
     */
    public function setUrl($url)
    {
        if (!empty($url) && is_string($url))
        {
            $this->url = $url;
        }
    }
    
    /**
     *
     * Method to set the data type.
     * 
     * @access public
     * @param string $url The data type JSON/XML
     * @return VOID
     */
    public function setDataType($dataType)
    {
        if (!empty($dataType) && ($dataType == 'json' || $dataType == 'xml'))
        {
            $this->dataType = $dataType;
        }
    }
    
    /**
     *
     * Method to add an array of grid columns. The following format must be used.
     * array(
     *     array(
     *        'display' => 'First Name',  // name on the grid
     *        'name' => 'first_name',  // database column to use
     *        'width' => 40,  // width on the grid
     *        'sortable' => TRUE,  // is the column sortable
     *        'align' => 'left',  // The data alignment in the column
     *      ),
     *     array(
     *        'display' => 'Surname',  // name on the grid
     *        'name' => 'last_name',  // database column to use
     *        'width' => 40,  // width on the grid
     *        'sortable' => TRUE,  // is the column sortable
     *        'align' => 'left', // The data alignment in the column
     *      ),
     * )
     * 
     * @access public
     * @param array $columns An array of column options
     * @return VOID
     */
    public function setColModel(array $columns)
    {
        if (!empty($columns) && is_array($columns))
        {
            $this->colModel = $columns;
        }
    }
    
    /**
     *
     * Method to add a grid column.
     * 
     * @access public
     * @param string $display The column title
     * @param string $columnName The name of the database column to use
     * @param mixed $width The width of the column in pixels or 'auto'
     * @param boolean $sortable TRUE if the column is sortable | FALSE if not
     * @param string $align The alignment of the text in the column
     * @return VOID
     */
    public function addColumn($display, $columnName, $width = 150, $sortable = TRUE, $align = 'left')
    {
        if (isset($display) && isset($columnName))
        {
            $column = array(
                'display' => $display,
                'name' => $columnName,
                'width' => $width,
                'sortable' => $sortable,
                'align' => $align,
            );

            $this->colModel[] = $column;
        }
    }
    
    /**
     *
     * Method to add an array of buttons. The following format must be used
     * e.g.
     * array(
     *     array(
     *         'name' => 'Add'  // the name on the button
     *         'bclass' => 'add'  // the css class of the button
     *         'onpress' => 'doAdd'  // action to perform on click
     *     ),
     *     array(
     *         'name' => 'Edit'  // the name on the button
     *         'bclass' => 'edit'  // the css class of the button
     *         'onpress' => 'doEdit'  // action to perform on click
     *     ),
     * )
     * 
     * @access public
     * @param array $buttons An array of buttons options
     * @return VOID
     */
    public function setButtons(array $buttons)
    {
        if (!empty($buttons) && is_array($buttons))
        {
            $this->buttons = $buttons;
        }
    }
    
    /**
     *
     * Method to add a button
     * 
     * @access public
     * @param string $name The name of the button
     * @param string $bclass The css class of the button
     * @param string $onpress The action to perform on click 
     * @return VOID
     */
    public function addButton($name, $onpress, $bclass = NULL)
    {
        if (isset($name) && isset($onpress))
        {
            $button = array(
                'name' => $name,
                'onpress' => $onpress,
                'bclass' => $bclass,
            );
            
            $this->buttons[] = $button;
        }        
    }    
    
    /**
     *
     * Method to add a separator after a button
     * 
     * @access public
     * @param string $button The button to place a separator after
     * @return VOID 
     */
    public function setSeparatorAfter($button)
    {
        if (!empty($button) && is_string($button))
        {
            $this->separatorAfter[] = $button;
        }
    }


    /**
     *
     * Method to set the grid height option.
     * 
     * @access public
     * @param mixed $height The height of the grid - default is "auto"
     * @return VOID
     */
    public function setHeight($height)
    {
        if (!empty($height) && (is_numeric($height) || $height == 'auto'));
        {
            $this->height = $height;
        }
    }
    
    /**
     *
     * Method to set the grid width option.
     * 
     * @access public
     * @param mixed $width The width of the grid - default is "auto"
     * @return VOID
     */
    public function setWidth($width)
    {
        if (!empty($width) && (is_numeric($width) || $width == 'auto'));
        {
            $this->width = $width;
        }
    }
    
    /**
     *
     * Method to set the resizable option.
     * 
     * @access public
     * @param boolean $resizable TRUE if the grid is resizable | FALSE if not
     * @return VOID
     */
    public function setResizable($resizable)
    {
        if (!empty($resizable) && is_bool($resizable));
        {
            $this->resizable = $resizable;
        }
    }

    /**
     *
     * Method to set the userRp option.
     * 
     * @access public
     * @param boolean $useRp TRUE if the user can select results per page | FALSE if not
     * @return VOID
     */
    public function setUseRp($useRp)
    {
        if (!empty($useRp) && is_bool($useRp));
        {
            $this->useRp = $useRp;
        }
    }

    /**
     *
     * Method to set the rp option.
     * 
     * @access public
     * @param integer $rp The initial results per page
     * @return VOID
     */
    public function setRp($rp)
    {
        if (!empty($rp) && is_integer($rp));
        {
            $this->rp = $rp;
        }
    }

    /**
     *
     * Method to set an array of searches.
     * e.g.
     * array(
     *     array(
     *         'diaplay' => 'First name'  // the name to display
     *         'name' => 'first_name'  // the column name
     *         'isdefault' => FALSE  // TRUE if the deafult search option | FALSE if not
     *     ),
     *     array(
     *         'diaplay' => 'Surname'  // the name to display
     *         'name' => 'last_name'  // the column name
     *         'isdefault' => TRUE // TRUE if the deafult search option | FALSE if not
     *     ),
     * )
     * 
     * @access public
     * @param array $searchitems An array of search items options
     * @return VOID
     */
    public function setSearchitems(array $searchitems)
    {
        if (!empty($searchitems) && is_array($searchitems))
        {
            $this->searchitems = $searchitems;
        }
    }
    
    /**
     *
     * Method to add a search item
     * 
     * @access public
     * @param string $display The display option in the search
     * @param string $name The column name to search
     * @param boolean $isdefault TRUE if this is the deafult search to display | FALSE if not
     * @return VOID
     */
    public function addSearchitem($display, $name, $isdefault = FALSE)
    {
        if (isset($display) && isset($name))
        {
            $searchitem = array(
                'display' => $display,
                'name' => $name,
                'isdefault' => $isdefault,
            );
            
            $this->searchitems[] = $searchitem;
        }        
    }    
    /**
     *
     * Method to set the sort order option.
     * 
     * @access public
     * @param string $sortorder The sort order
     * @return VOID
     */
    public function setSortname($sortname)
    {
        if (!empty($sortname) && is_string($sortname));
        {
            $this->sortname = $sortname;
        }
    }

    /**
     *
     * Method to set the sort order option.
     * 
     * @access public
     * @param string $sortorder The sort order
     * @return VOID
     */
    public function setSortorder($sortorder)
    {
        if (!empty($sortorder) && ($sortorder == 'ASC') || $sortorder == 'DESC');
        {
            $this->sortorder = $sortorder;
        }
    }

    /**
     *
     * Method to set the use singleSelect option.
     * 
     * @access public
     * @param boolean $singleSelect TRUE if only one record can be selected | FALSE if not
     * @return VOID
     */
    public function setSingleSelect($singleSelect)
    {
        if (!empty($singleSelect) && is_bool($singleSelect));
        {
            $this->singleSelect = $singleSelect;
        }
    }

    /**
     *
     * Method to set the use pager option.
     * 
     * @access public
     * @param boolean $usepager TRUE if the page must be displayed | FALSE if not
     * @return VOID
     */
    public function setUsepager($usepager)
    {
        if (!empty($usepager) && is_bool($usepager));
        {
            $this->usepager = $usepager;
        }
    }

    /**
     *
     * Method to set the showTableToggleBtn option.
     * 
     * @access public
     * @param boolean $showTableToggleBtn TRUE if the table can be toggled | FALSE if not
     * @return VOID
     */
    public function setShowTableToggleBtn($showTableToggleBtn)
    {
        if (!empty($showTableToggleBtn) && is_bool($showTableToggleBtn));
        {
            $this->showTableToggleBtn = $showTableToggleBtn;
        }
    }

    /**
     *
     * Method to generate the tooltip javascript and add it to the page
     * 
     * @access public
     * @return VOID 
     */
    public function show()
    {
        $script = "<script type=\"text/javascript\">";
        $script .= "jQuery(function() {";
        $script .= "jQuery(\"#$this->cssId\").flexigrid({";
        $script .= "type: 'POST'";
        $script .= ",url: '$this->url'";
        $script .= ",dataType: '$this->dataType'";
        $script .= ",colModel: [";
        $i = 0;
        foreach ($this->colModel as $column)
        {
            $i++;
            $script .= "{";
            $ii = 0;
            foreach ($column as $key => $data)
            {
                $ii++;
                if ($key == 'display' || $key == 'name' || $key == 'align')
                {
                    $data = "'$data'";
                }
                elseif ($key == 'sortable')
                {
                    $data = $data ? 'true' : 'false';
                }
                else
                {
                    $data = is_numeric($data) ? $data : "'$data'";
                }
                $script .= "$key: $data";
                if ($ii != count($column))
                {
                    $script .= ",";
                }
            }
            $script .= "}";
            if ($i != count($this->colModel))
            {
                $script .= ",";
            }
        }
        $script .= "]";
        $script .= ",buttons: [";
        $i = 0;
        foreach ($this->buttons as $button)
        {
            $i++;
            $script .= "{";
            $ii = 0;
            foreach ($button as $key => $data)
            {
                $ii++;
                if ($key == 'name' || $key == 'bclass')
                {
                    $data = "'$data'";
                }
                $script .= "$key: $data";
                if ($ii != count($button))
                {
                    $script .= ",";
                }
            }
            $script .= "}";
            if (!empty($this->separatorAfter))
            {
                if (in_array($button['name'], $this->separatorAfter))
                {
                    $script .= ",{separator: true}";
                }
            }
            if ($i != count($this->buttons))
            {
                $script .= ",";
            }
        }
        $script .= "]";
        $script .= ",searchitems: [";
        $i = 0;
        foreach ($this->searchitems as $searchitem)
        {
            $i++;
            $script .= "{";
            $ii = 0;
            foreach ($searchitem as $key => $data)
            {
                $ii++;
                if ($key == 'isdefault')
                {
                    $data = $data ? 'true' : 'false';
                }
                else
                {
                    $data = "'$data'";
                }
                $script .= "$key: $data";
                if ($ii != count($searchitem))
                {
                    $script .= ",";
                }
            }
            $script .= "}";
            if ($i != count($this->searchitems))
            {
                $script .= ",";
            }
        }
        $script .= "]";        
        $script .= ",sortname: \"$this->sortname\"";
        $script .= ",sortorder: \"$this->sortorder\"";
        $script .= $this->usepager ? ",usepager: true" : ",usepager: false";
        $script .= ",title: \"$this->title\"";
        $script .= $this->useRp ? ",useRp: true" : ",useRp: false";
        $script .= ",rp: $this->rp";
        $script .= $this->resizable ? ",resizable: true" : ",resizable: false";
        $script .= $this->showTableToggleBtn ? ",showTableToggleBtn: true" : ",showTableToggleBtn: false";
        $script .= is_numeric($this->height) ? ",height: $this->height" : ",height: \"$this->height\"";
        $script .= is_numeric($this->width) ? ",width: $this->width" : ",width: \"$this->width\"";
        $script .= $this->singleSelect ? ",singleSelect: true" : ",singleSelect: false";
        $script .= "});});</script>";
        
        $this->appendArrayVar('headerParams', $script);
        
        return "<table id=\"$this->cssId\" style=\"display:none\"></table>";
    }
}
?>
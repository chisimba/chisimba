<?php


// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building a step menu.
*
* The class builds a css-based step menu to show users how many steps
* a process is, what the steps are, and which is the current step they
* are on
*
* @author Tohir Solomons
* @copyright (c)2008 UWC
* @package featurebox
* @verson 0.1
*/

class stepmenu extends object
{
    /**
    *
    * @var string $id: the ID tag from the CSS
    * @access public
    */
    private $steps = array();
    
    public $current = 1;

    /**
    * Method to construct the class.
    **/
    public function init()
    {
        $this->id = '';
    }
    
    public function addStep($name, $description)
    {
        $this->steps[] = array('name'=>$name, 'description'=>$description);
    }
    
    public function setCurrent($current)
    {
        $this->current = $current;
    }
    
    public function show()
    {
        if ($this->current > count($this->steps)) {
            $this->current = 1;
        }
        
        $str = '<ul id="stepmenu" class="stepmenu'.count($this->steps).'">';
        $counter = 1;
        
        foreach ($this->steps as $step)
        {
            $css = '';
            
            if ($counter < ($this->current-1)) {
                $css = 'done';
            } else
            if ($counter == ($this->current-1)) {
                $css = 'lastdone';
            } else if ($counter == $this->current) {
                $css = 'current';
            }
            
            if ($counter == count($this->steps)) {
                $css .= ' mainNavNoBg';
            }
            
            if ($css != '') {
                $css = ' class="'.trim($css).'"';
            }
            
            $str .= '<li'.$css.'><a title=""><em>'.$step['name'].'</em> <span>'.$step['description'].'</span></a></li>';
            
            $counter++;
        }
        
        $str .= '</ul>';
        
        return '<div>'.$str.'<br clear="both" /></div>';
    }
}
?>
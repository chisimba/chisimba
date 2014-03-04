<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Class to Render Dynamic Blocks for the Podcast Module
*
* @author Tohir Solomons
*
*/
class podcastdynamicblock extends object
{

    
    /**
    * Constructor for the class
    */
    function init()
    {
        //Create an instance of the language object
        $this->objLanguage =& $this->getObject('language','language');
        $this->objPodcast =& $this->getObject('dbpodcast','podcast');
        $this->objUser =& $this->getObject('user','security');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('windowpop', 'htmlelements');
        //Set the title
        $this->title='Latest Podcast';
    }
    
    /**
    * Method to output a block with information on how help works
    */
    function showBlock($userId)
    {
        $podcasts = $this->objPodcast->getUserPodcasts($userId);
        
        if (count($podcasts) == 0) {
            return '';
        } else {
            
            $str = '<ul>';
            
            foreach ($podcasts as $podcast)
            {
                $podCastLink = new link ($this->uri(array('action'=>'viewpodcast', 'id'=>$podcast['id']), 'podcast'));
                $podCastLink->link = $podcast['title'];
                
                $str .= '<li>'.$podCastLink->show().'</li>';
            }
            
            $str .= '</ul>';
            
            return $str;
        }
        
    }
}

?>
<?php


// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building the sidebar navigation for KEWL.nextgen.
*
* The class builds a css style navigation menu 
*
* @author Wesley Nitsckie
* @copyright (c)2004 UWC
* @package sidebar
* @version 0.1
*/

class sidebar extends object
{
	
	/**
     * The nodes array
     *
     * @access private
     * @var array
    */
    protected $nodes;
    
	
    /**
    * Method to construct the class.
    **/
    public function init()
    {
   		try{
    		$this->nodes = array();
    	}catch (Exception $e){
       		echo 'Caught exception: ',  $e->getMessage();
        	exit();
        }
    }
    
    /**
     * Method to set the array
     * 
     * @param array $nodes The list of nodes
     * @access public
     * @return bool
     */
    public function setNodes($nodes)
    {
    	try{
	    	$this->nodes = $nodes;
	    	return TRUE;
    	}catch (Exception $e){
       		echo 'Caught exception: ',  $e->getMessage();
        	exit();
        }
    }
    
    /**
     * Method to show the sidebar
     * 
     * @param array $nodes
     * @param string $activeId This variable is used to check which record should be set to active
     * @access publc
     * @return string
     */
    public function show($nodes, $activeId = NULL)
    {
    	try{
    		//var_dump($nodes);
    		
    		$cssClass = ' class="first" ';
    		
    		$str = '<ul id="nav-secondary">';	
    		 $str .='<li class="first"><a href="'.$this->uri(null, 'default').'">Home</a></li>';
    		//loop through the nodes
    		foreach($nodes as $node)
    		{
				if($node['sectionid'] == $activeId)
				{
					$cssClass = ' class="active" ';	
					
				}
				
				$str .='<li><a href="'.$node['uri'].'">'.$node['text'].'</a>';
				
				
				if(is_array($node['haschildren']))
				{
					//print $node['text'].'has chlren';
					$str .= '<ul>';
					$cssClass2 = ' class="first" ';
					foreach($node['haschildren'] as $child)
					{
						$str .='<li '.$cssClass2.'><a href="'.$child['uri'].'">'.$child['text'].'</a></li>';
					}
					
					//$cssClass2 = ' class="last"';
					$str .= '</ul>';
					
					//$str .= $this->show($node['haschildren']);
				}
				$str .= '</li>';
				//reset the cssclass
				$cssClass = '';
    		}
    		
    		$str .'</ul>';
    		return $str;
	  		
  		
  		}catch (Exception $e){
       		echo 'Caught exception: ',  $e->getMessage();
        	exit();
        }
  		  	
    }
}
?>
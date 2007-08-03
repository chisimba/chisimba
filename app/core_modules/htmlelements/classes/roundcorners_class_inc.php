<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building a rounded div box for KEWL.nextgen.
*
* The class builds a css style div rounded corners box 
*
* @author    Prince Mbekwa
* @copyright (c)2004 UWC
* @package   div class
* @version   0.1
*/

class roundcorners extends object 
{

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $content;
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
	function init(){
		
	}
	
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  string $content Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	function show($content='')
    {
		if ($content != '') {
            $this->content = $content;
        }
        
        
        $this->appendArrayVar('headerParams', "<script type=\"text/javascript\">
//<![CDATA[
function roundedCorners() {
  var divs = document.getElementsByTagName('div');
  var rounded_divs = [];
  for (var i = 0; i < divs.length; i++) {
    if (/\brounded\b/.exec(divs[i].className)) {
      rounded_divs[rounded_divs.length] = divs[i];
    }
  }
  for (var i = 0; i < rounded_divs.length; i++) {
    var original = rounded_divs[i];
    /* Make it the inner div of the four */
    original.className = original.className.replace('rounded', '');
    /* Now create the outer-most div */
    var tr = document.createElement('div');
    tr.className = 'rounded2';
    /* Swap out the original (we'll put it back later) */
    original.parentNode.replaceChild(tr, original);
    /* Create the two other inner nodes */
    var tl = document.createElement('div');
    var br = document.createElement('div');
    /* Now glue the nodes back in to the document */
    tr.appendChild(tl);
    tl.appendChild(br);
    br.appendChild(original);
  }
}
//]]>
</script>");

        $this->appendArrayVar('bodyOnLoad', 'roundedCorners();');
        return '<div class="rounded">'.$this->content.'</div>';
	}
    
}
?>
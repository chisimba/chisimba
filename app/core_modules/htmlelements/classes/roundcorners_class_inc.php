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
* @category  Chisimba
* @author    Prince Mbekwa
* @package   htlmelements
* @version   $Id$
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @link      http://avoir.uwc.ac.za
*/

class roundcorners extends object 
{

    /**
     * Holds the content of the box
     * @var    string
     * @access public
     */
    public $content;
    
    /**
     * Constructor
     * 
     */
	function init(){
		
	}
	
	
    /**
     * Method to display the box
     * 
     * @param  string $content
     * @return string Return box
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
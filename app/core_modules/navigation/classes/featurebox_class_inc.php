<?php


// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building a feature box for KEWL.nextgen.
*
* The class builds a css style feature box
*
* @author Wesley Nitsckie
* @copyright (c)2004 UWC
* @package featurebox
* @verson 0.1
*/

class featurebox extends object
{
    /**
    *
    * @var string $id: the ID tag from the CSS
    * @access public
    */
    public $id;

    /**
    * Method to construct the class.
    **/
    public function init()
    {
        $this->id = '';
    }

    /**
     * Method to show the sidebar
     *
     * @param null
     * @access publc
     * @return string
     */
    public function show($title = null, $content = null, $id = null, $hidden = 'default', $showToggle = TRUE)
    {
        $objIcon = $this->newObject('geticon', 'htmlelements');

          $sidebar = '<div class="featurebox">';
          $toggle = '';
          
        //Adding support for styling corners
        $sidebar .= '<div class="featureboxtopcontainer">

                        <div class="featureboxtopleft"></div>
                        <div class="featureboxtopborder"></div>
                        <div class="featureboxtopright"></div>
                        
                     </div>';

        if(!empty($id) && $showToggle)
        {
              $objIcon->setIcon('toggle');
            $objIcon->extra = "style='vertical-align:middle'";
            $toggle = "<a href=\"javascript:;\" onclick=\"Effect.toggle('".$id."','slide', adjustLayout());\">".$objIcon->show()."</a>";
            $title = "<a href=\"javascript:;\" onclick=\"Effect.toggle('".$id."','slide', adjustLayout());\">".$title."</a>";
        }
        
        $sidebar .= '   <h5 class="featureboxheader">'.$toggle.'  '.$title.'</h5>';
        $sidebar .= '<div class="featureboxcontent"';
        if($id != NULL){
            $sidebar .= ' id="'.$id.'" style="overflow: hidden;display:'.$hidden.';" >';
        }else{
            $sidebar .= '>';
        }
    
        
        $sidebar .= $content.'</div>';

        $sidebar .= '<div class="featureboxbottomcontainer">

                        <div class="featureboxbottomleft"></div>
                        <div class="featureboxbottomborder"></div>
                        <div class="featureboxbottomright"></div>
                        
                    </div>';

        $sidebar .= '</div>';
          return $sidebar;

    }

    /**
     * Method to show a content featurebox
     *
     * @param string $title
     * @param string $content
     * @return string
     */
    public function showContent($title = null, $content = null)
    {

          $contentbox = '<div class="contentfeaturebox">';
        $contentbox .= '    <h3>'.$title.'</h3>';
        $contentbox .= $content;
        $contentbox .= '</div>';

          return $contentbox;

    }
    
    /**
     * Method to show a comment featurebox
     *
     * @param string $title
     * @param string $content
     * @return string
     */
    public function showComment($title = null, $content = null)
    {

          $contentbox = '<div class="contentfeaturebox">';
        $contentbox .= '    <h3>'.$title.'</h3>';
        $contentbox .= $content;
        $contentbox .= '</div>';

          return $contentbox;

    }
}
?>

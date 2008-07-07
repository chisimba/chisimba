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
     * A flag on whether to show the first home link or not
     *
     * @author Tohir Solomons
     * @access public
     * @var boolean
     */
    public $showHomeLink = TRUE;


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
    public function show($nodes, $activeId = NULL, $uriaction = NULL, $urimodule = '_default', $homelink = "Home")
    {
        try{
            $cssClass = ' class="first" ';
            //var_dump($nodes);
            $str = '<ul id="nav-secondary">
                                            ';

            if ($this->showHomeLink) {
                 $str .='<li class="first">
                            <a href="'.$this->uri($uriaction,$urimodule).'">'.$homelink.'</a>
                        </li>
                                ';
            }
            //loop through the nodes
            foreach($nodes as $node)
            {
                if(!isset($node['nodeid']))
                {
                    $node['nodeid'] = NULL;
                }
                
                if (isset($node['css'])) {
                    $nodeCss = ' '.$node['css'];
                } else {
                    $nodeCss = '';
                }
                
                if($node['nodeid'] == $activeId || isset($node['haschildren']))
                {
                    $cssClass = ' class="active'.$nodeCss.'" ';
                    $str .='<li '.$cssClass.'>
                                <a href="'.$node['uri'].'">'.$node['text'].'</a>
                                ';
                } else {
                    if ($nodeCss == '') {
                        $cssClass = '';
                    } else {
                        $cssClass = ' class="'.$nodeCss.'"';
                    }
                    $str .='<li'.$cssClass.'>
                                <a href="'.$node['uri'].'">'.$node['text'].'</a>
                                ';
                }



                $cssClass2 = ' class="first" ';
                if(isset($node['haschildren']))
                {
                    $cnt = $node['haschildren'];
                    $c = 0;
                    $str .= '<ul>
                                ';

                    foreach($node['haschildren'] as $child)
                    {
                        $c++;

                        if($c == $cnt)
                        {
                            $cssClass2 = ' class="last" ';
                        } else {
                            $cssClass2 = '';
                        }

                        $str .='<li '.$cssClass2.'>
                                    <a href="'.$child['uri'].'">'.$child['text'].'</a>
                                </li>
                                    ';
                    }

                    //check for the last item in the arra


                    $str .= '</ul>
                                ';

                    //$str .= $this->show($node['haschildren']);
                }
                $str .= '</li>'
                            ;
                //reset the cssclass
                $cssClass = '';

            }

            $str .='</ul>
                        ';
            return $str;


        }catch (Exception $e){
            echo 'Caught exception: ',  $e->getMessage();
            exit();
        }

    }
}
?>
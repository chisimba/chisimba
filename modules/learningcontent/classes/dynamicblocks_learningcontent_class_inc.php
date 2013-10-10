<?php
/**
 * Context Content Dynamic Blocks
 *
 * Class to generate the content of dynamic blocks in context content
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
 * @version    $Id: dynamicblocks_learningcontent_class_inc.php 11217 2008-10-30 20:45:37Z charlvn $
 * @package    learningcontent
 * @author     Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
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
// end security check

/**
 * Context Content Dynamic Blocks
 *
 * Class to generate the content of dynamic blocks in context content
 *
 * @author Tohir Solomons
 *
 */
class dynamicblocks_learningcontent extends object
{

    /**
    * Constructor
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objContextChapters = $this->getObject('db_learningcontent_contextchapter');
        $this->objContentOrder = $this->getObject('db_learningcontent_order');
        $this->loadClass('link', 'htmlelements');
    }
    
    /**
     * Method to render the contents of a chapter as a block
     * @param string $id Record Id of the block
     * @return string Contents of Chapter as a block
     */
    public function renderChapter($id)
    {
        $chapter = $this->objContextChapters->getRow('id', $id);
        
        if ($chapter == FALSE) {
            return '';
        } else {
            return $this->objContentOrder->getTree($chapter['contextcode'], $chapter['chapterid'], 'htmllist');
        }
    }
    
    /**
     * Method to list chapters in a context
     * @param string $contextCode Context Code
     * @return list of chapters as a block
     */
    public function listChapters($contextCode)
    {
        $chapters = $this->objContextChapters->getContextChapters($contextCode);
        
        if (count($chapters) == 0) {
            return '<div class="noRecordsMessage">'.$this->objLanguage->code2Txt('mod_learningcontent_contexthasnochaptersorcontent', 'learningcontent', NULL, 'This [-context-] does not have chapters or content').'</div>';
        } else {
            $str = '<ol>';
            foreach ($chapters as $chapter)
            {
                $link = new link ($this->uri(array('action'=>'viewchapter', 'id'=>$chapter['chapterid']), 'learningcontent'));
                $link->link = $chapter['chaptertitle'];
                
                $str .= '<li>'.$link->show().'</li>';
            }
            
            $str .= '</ol>';
            
            return $str;
        }
    }
    
    /**
     * Method to list chapters in a context
     * @param string $contextCode Context Code
     * @return list of chapters as a block
     */
    public function listChaptersWide($contextCode)
    {
        return $this->listChapters($contextCode);
    }
    /**
     * Method to return resizeable Image JS
     * @param string $imageId Image Id
     * @param string $imageType Image Type - Picture or Formula
     * @return javascript string
     */
    public function createResizeableImageJS($imageId='',$imageType = ''){
        $str = '<script type="text/javascript">
                /*!
                 * Ext JS Library 3.0.0
                 * Copyright(c) 2006-2009 Ext JS, LLC
                 * licensing@extjs.com
                 * http://www.extjs.com/license
                 */
                var ResizableExample'.$imageId.' = {
                    init : function(){
                        var custom'.$imageId.' = new Ext.Resizable(\'custom'.$imageId.'\', {
                            wrap:true,
                            pinned:true,
                            minWidth:50,
                            minHeight: 50,
                            preserveRatio: true,
                            handles: \'all\',
                            draggable:true,
                            dynamic:true
                        });
                        var custom'.$imageId.'El = custom'.$imageId.'.getEl();
                        // move to the body to prevent overlap on my blog
                        document.body.insertBefore(custom'.$imageId.'El.dom, document.body.firstChild);
        
                        custom'.$imageId.'El.on(\'dblclick\', function(){
                        // DO Ajax
                       jQuery.ajax({
                        type: "GET", 
                        url: "index.php", 
                        data: "module=learningcontent&action=trackviewimage&imageId='.$imageId.'&imagetype='.$imageType.'", 
                        success: function(msg){
                        }
                       });
                            custom'.$imageId.'El.hide(true);
                        });
                        custom'.$imageId.'El.hide();
                        
                        Ext.get(\'showMe'.$imageId.'\').on(\'click\', function(){
                           // DO Ajax
                           jQuery.ajax({
                            type: "GET", 
                            url: "index.php", 
                            data: "module=learningcontent&action=trackviewimage&imageId='.$imageId.'&imagetype='.$imageType.'", 
                            success: function(msg){
                            }
                           });
                           custom'.$imageId.'El.center();
                           custom'.$imageId.'El.show(true);
                        });        
                    }
                };
                Ext.EventManager.onDocumentReady(ResizableExample'.$imageId.'.init, ResizableExample'.$imageId.', true);
                </script>';
        return $str;
    }
    /**
     * Method to return resizeable Image JS
     * @param string $imageId Image Id
     * @param $sqrImgPath Path to resizeable side square image
     * @return javascript string
     */
    public function createResizeableImageCSS($imageId=Null, $sqrImgPath){
        $str = '<style type="text/css">
                 #basic'.$imageId.', #animated'.$imageId.' {
                  border:1px solid #c3daf9;
                  color:#1e4e8f;
                  font:bold 14px tahoma,verdana,helvetica;
                  text-align:center;
                  padding-top:20px;
                }
                #custom'.$imageId.' {
                 cursor:move;
                }
                #custom'.$imageId.'-rzwrap{
                 z-index: 100;
                }
                #custom'.$imageId.'-rzwrap .x-resizable-handle{
                 width:11px;
                 height:11px;
                 background:transparent url('.$sqrImgPath.') no-repeat;
                 margin:0px;
                }
                #custom'.$imageId.'-rzwrap .x-resizable-handle-east, #custom'.$imageId.'-rzwrap .x-resizable-handle-west{
                 top:45%;
                }
                #custom'.$imageId.'-rzwrap .x-resizable-handle-north, #custom'.$imageId.'-rzwrap .x-resizable-handle-south{
                 left:45%;
                }
                </style>';
        return $str;
    }
}
?>

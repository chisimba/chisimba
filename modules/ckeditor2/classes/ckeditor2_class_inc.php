<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_ckeditor_class_inc
 *
 * @author monwabisi
 */
class ckeditor2 extends object {

        var $domDoc;

        /**
         * @var object
         */
        var $jsFile;

        //put your code here
        function init() {
                $this->domDoc = new DOMDocument('utf-8');
                $this->jsFile = $this->getJavaScriptFile('ckeditor.js', 'ckeditor2');
        }

        private function createEditor($trigerElementId, $parent, $event, $parentAttribute) {
                
        }

        /**
         * 
         * @param string $trigerElementId
         * @param strig $event
         * @param string $parentAttribute
         * @param string $attributeValue
         */
        function createEditorById($trigerElementId, $event, $parentId) {
                $javascriptFile = $this->getJavaScriptFile('ckeditor.js','ckeditor2');
                $domElelents['script'] = $this->domDoc->createElement('script');
                $text = "<script>
                        jQuery(document).ready(function(){
                                jQuery('.{$trigerElementId}').on('{$event}',function(e){
                                        e.preventDefault();
                                                CKEDITOR.appendTo('#{$parentId}',null,null);
                                        });
                        });
                        </script>";
//                $domElelents['script']->appendChild($this->domDoc->createTextNode($text));
//                $this->domDoc->appendChild($this->domDoc->createTextNode($javascriptFile));
//                $this->domDoc->appendChild($domElelents['javascriptInclude']);
                $this->domDoc->appendChild($domElelents['script']);
                return $javascriptFile.$text;
        }

        function appendEditorByClass($trigerElementId, $triggerEvent, $attributeValue) {
                
        }

        function show() {
//                return $this->createEditorById($trigerElementId, $event, $parrentId);
        }

}

?>

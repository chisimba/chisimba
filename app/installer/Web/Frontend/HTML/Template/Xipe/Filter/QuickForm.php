<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997, 1998, 1999, 2000, 2001, 2002, 2003 The PHP Group |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Wolfram Kriesing <wolfram@kriesing.de>                      |
// +----------------------------------------------------------------------+
//  $Id$
//

require_once 'HTML/Template/Xipe/Options.php';

/**
*
*
*   @package    HTML_Template_Xipe_Filter_QuickForm
*   @access     public
*   @version    03/04/22
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class HTML_Template_Xipe_Filter_QuickForm extends HTML_Template_Xipe_Options
{
    /**
    *   for passing values to the class, i.e. like the delimiters
    *   @access private
    *   @var    array   $options    the options for initializing the filter class
    */
    var $options = array(   'delimiter'     =>  array()      // first value of the array is the begin delimiter, second the end delimiter
                        );

    var $_namespace = 'form';
    
    var $_allFrozen = false;
                        
    function HTML_Template_Xipe_Filter_QuickForm($options=array(),$namespace='form')
    {
        parent::HTML_Template_Xipe_Options($options);
        $this->_namespace = $namespace;
    }
                        
    function process($input,&$form) 
    {
        // do only run the methods below if we find a '<form:' string in the $input
        // otherwise is just invain
        if (strpos($input,'<'.$this->_namespace.':')) {
            $input = $this->elementExists($input,$form);
            $input = $this->elementName($input,$form);
            $input = $this->element($input,$form);
            $input = $this->showOnEdit($input);
        }
        return $input;
    }
    
    function element($input,&$form) 
    {
        if ($elements=$this->_process('~<'.$this->_namespace.':element\s.*/>~iU',$input)) {
            // set it to true, so the first not frozen element will correct it again
            // this way the checking is easier
            $this->_allFrozen = true;
            foreach ($elements as $tag=>$parsed) {
                $elHtml = '';
                if (isset($parsed['attributes'])) {
                    $attribs = $parsed['attributes'];
                    if (isset($attribs['NAME'])) {
                        $elName = $attribs['NAME'];
                        if (!PEAR::isError($element = $form->getElement($elName))) {
                            if ($form->isElementRequired($elName)) {
                                $element->updateAttributes(array('class'=>'required'));
                            }
                            $frozen = $element->isFrozen();
                            $elHtml = $frozen?$element->getFrozenHtml():$element->toHtml();
                            if ($error=$form->getElementError($elName)) {
                                $elHtml = '<font class="warning">'.$error.'</font><br>'.$elHtml;
                            }
                            // if the current element is not frozen and if it is 
                            // not a submit button or a hidden field
                            // then we set _allFrozen to false
                            // for some reason QF doesnt freeze a file element, so we ignore it here
                            $type = $element->getType();
                            if (!$frozen && $type!='submit' && $type!='hidden' && $type!='file') {
                                $this->_allFrozen = false;
                            }
                        }
                    }
                }
                $input = str_replace($tag,$this->_escape($elHtml),$input);
            }
        } 
        return $input;
    }

    function elementName($input,&$form) 
    {
        if ($elements=$this->_process('~<'.$this->_namespace.':elementName\s.*/>~iU',$input)) {
            foreach ($elements as $tag=>$parsed) {
                $label = '';
                if (isset($parsed['attributes'])) {
                    $attribs = $parsed['attributes'];
                }
                // replace PHP_SELF by {$_SERVER['PHP_SELF']}
                if (isset($attribs['NAME'])) {
                    $elName = $attribs['NAME'];
                    if (!PEAR::isError($element = $form->getElement($elName))) {
                        $label = $element->getLabel();
                        if ($form->isElementRequired($elName)) {
                            $label.= '&nbsp;<font class="required">*</font>';
                        }
                    }
                }
                $input = str_replace($tag,$this->_escape($label),$input);
            }
        }        
        return $input;
    }
    
    /**
    *   This handles the tag <form:showOnEdit/>
    *
    *   This tag is used for surrounding stuff that shall only be shown
    *   when editing the data. This is i.e. some instructions of how to 
    *   insert text etc. When the frozen data will be shown this info is
    *   mostly not needed. If this is the case simply use this tag to surround
    *   the pieces you dont want to see with the frozen data.
    *
    */
    function showOnEdit($input)
    {
        $tagName = $this->_namespace.':showOnEdit';
        if ($this->_allFrozen) {
            // remove all the stuff which is inside 'showOnEdit' 
            $input = preg_replace("~<$tagName>.*<\/$tagName>~Uis",'',$input);            
        } else {
            // remove the 'showOnEdit' tags, since the stuff inside there shall be shown
            $input = preg_replace('~<'.$tagName.'>~Ui','',$input);
            $input = preg_replace('~</'.$tagName.'>~Ui','',$input);
        }
        return $input;
    }
    
    /**
    *   This can be used to surround a piece of template code to check 
    *   if the given element exists at all, if it is not given the piece
    *   of code will simply be removed.
    *
    *
    */
    function elementExists($input,&$form)
    {
        $tagName = $this->_namespace.':elementExists';
        preg_match_all('~<'.$tagName.'\s+name="([^"]+)".*>.*<\/'.$tagName.'>~Uis',$input,$results);
        if (sizeof($results[1])) {
            foreach ($results[1] as $k=>$aName) {
                if ($form->elementExists($aName)) {
                    // remove the <form:elementExists opening and closing XML tag
                    $input = preg_replace('~<'.$tagName.'\s+name="'.preg_quote($aName).'".*>(.*)<\/'.$tagName.'>~Us','$1',$input);
                } else {
                    // remove all the content and the XML tags
                    $input = preg_replace('~<'.$tagName.'\s+name="'.preg_quote($aName).'".*>.*<\/'.$tagName.'>~Uis','',$input);
                }
            }
        }
        return $input;
    }
    
    function _process($regExp,&$input) 
    {
        $ret = array();
        if (preg_match_all($regExp,$input,$_elTags)) {
            $elTags = array_unique($_elTags[0]);
            
            // replace each tag with the given attributes
            foreach ($elTags as $aTag) {
                $p = xml_parser_create();
                xml_parse_into_struct($p,$aTag,$vals);
                xml_parser_free($p);  
                $attribs = array();
                $ret[$aTag] = $vals[0];
            }
        }
        return $ret;
    }
    
    /**
    * Escape all delimiters, since the content is not meant to be interpreted!
    *
    *
    */
    function _escape($string)
    {
        $string = str_replace($this->options['delimiter'][0],'\\'.$this->options['delimiter'][0],$string);
        return str_replace($this->options['delimiter'][1],'\\'.$this->options['delimiter'][1],$string);
    }
}

?>

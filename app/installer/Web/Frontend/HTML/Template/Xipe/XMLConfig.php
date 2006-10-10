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

require_once 'HTML/Template/Xipe/Main.php';

/**
*
*   @package    HTML_Template_Xipe
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class HTML_Template_Xipe_XMLConfig extends HTML_Template_Xipe_Main
{

    /**
    *   applies the xml-config files and the xml config contained in the file
    *
    *   @access     private
    *   @version    2002/03/11
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     true if the template should be recompiled, false otherwise
    */
    function _applyXmlConfig()
    {
        $xmlConfigFiles = $this->_getXmlConfigFiles();

        if( sizeof($xmlConfigFiles) )
        {
            foreach( $xmlConfigFiles as $aConfigFile )
                if( PEAR::isError($ret=$this->_setOptionsByXmlConfig($aConfigFile)) )
                    return $ret;
        }

        // apply the xml config which is in the template file at last
        if( ($cfgString = $this->_getXmlConfigString()) )
            if( PEAR::isError($ret=$this->_setOptionsByXmlConfig( $cfgString , true )) )
                return $ret;
    }



    /**
    *   checks along the path of the current template for xml config files
    *   and returns them in the order they shall be applied
    *   the order is from the lowest path up to the actual template file
    *
    *   @access     public
    *   @version    01/12/14
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function _getXmlConfigFiles()
    {
        $configFile = $this->getOption('xmlConfigFile');

        // start at the templateDir and go up to the directory where the
        // current template is in and return all xml-config files found on the way
        $curTplDir = dirname($this->_templateFile);

        // remove the template directory from the path to search in
        // we start only at the templateDir-root
        // and get every single directory in the $path-array
        $path = explode( '/' , str_replace( $this->getOption('templateDir') , '' , $curTplDir ));

        $xmlConfigFiles = array();
        if(sizeof($path))
        {
            $curDir = $this->getOption('templateDir');
            foreach($path as $aDir)
            {
                $curDir.= $aDir ? '/'.$aDir : '' ;  // add directory by directory to the curDir, until we got to the current tempalte's directory

                $possibleConfigFile = $curDir.'/'.$configFile;  // the name of the possible xml-config file
                if( file_exists($possibleConfigFile))
                {
                    $xmlConfigFiles[] = $possibleConfigFile;    // remember the xml config files that need to be applied to the current template
                    if( !$this->_isUpToDate($possibleConfigFile) )  // check if one of the xml config files is newer than the compiled template
                    {
                        $this->_needsRecompile = true;  // if so remember that, so the template gets recompiled
                        $this->_log('recompile because XML-config file is newer than compiled template: '.$possibleConfigFile);
                    }
                }
            }
        }
        return $xmlConfigFiles;
    }

    /**
    *   applies the given xml config file
    *
    *   @access     public
    *   @version    01/12/14
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function _setOptionsByXmlConfig( $configFileOrString , $isString=false )
    {
//print '_setOptionsByXmlConfig ... '.nl2br(htmlentities($configFileOrString))." , $isString<br>";
        // include this so i can get the xml file prepared in the tree shape
        // and i can use the tree methods to retreive the options i need to set :-)
        if( !@include_once('Tree/Tree.php') )
        {
            return $this->_error(   "xml-config could not be parsed, because the class 'Tree/Tree.php' could not be included<br>".
                                    '1. pleace be sure to have the latest PEAR::Tree package installed '.
                                    '(<a href="http://pear.php.net/package-info.php?pacid=104">you get it from here</a>)' ,
                                    PEAR_ERROR_DIE );
        }

        if( $isString )
        {
            $config = Tree::setupMemory( 'XML' );
            $config->setupByRawData( $configFileOrString );
        }
        else
        {
            $config = Tree::setupMemory( 'XML' , $configFileOrString );
            $config->setup();
        }

        //
        //  add the filters set in the xml config files
        //
// TODO check for prefilter defines in xml config
        if( $id = $config->getIdByPath('html_template_xipe/prefilter') )  // are any preFilter given?
        {
            $this->_applyFiltersFromXMLConfig( $config , $id , true );
        }

        //
        //  apply xml given options to this class, do this after applying the filters
        //
        if( $id = $config->getIdByPath('html_template_xipe/options') )  // are any options given?
        {
            $delimiter = $config->getElementByPath('delimiter',$id);
            if( $delimiter )// set new delimiters?
            {
                $begin = $delimiter['attributes']['begin'];
                $end = $delimiter['attributes']['end'];
                if( $begin && $end )                // only if both delimiters are given !!!
                {
                    $setOptions['delimiter'] = array(trim($begin),trim($end));
                }
            }
            if( $autoBraces = $config->getIdByPath('autobraces',$id) )// set autoBraces?
            {
                $setOptions['autoBraces'] = false;
                if( strtolower(trim($config->data[$autoBraces]['attributes']['value'])) == 'true' )
                    $setOptions['autoBraces'] = true;
            }
            if( $localeId = $config->getIdByPath('locale',$id) )// set locale?
            {
                $locale = trim($config->data[$localeId]['attributes']['value']);
                if( $locale )
                    $setOptions['locale'] = $locale;
            }

            //
            //  find the cache tag
            //  <cache>
            //      <time value="10" unit="minutes"/>
            //      <depends value="$_GET $_POST"/>
            //  </cache>
            //
            if( $cacheId = $config->getIdByPath('cache',$id))  // cache-tag given?
            {
                if( @$config->data[$cacheId]['attributes']['dontcache']=='true' )
                {
                    $setOptions['cache']['time'] = false;
                    $this->_log('XMLConfig: dont cache this file!');
                }
                else
                    if( $timeId = $config->getIdByPath('time',$cacheId) )   // and the mandatory time-tag?
                    {
                        $time = $config->data[$timeId]['attributes']['value'];

                        if( $unit = $config->data[$timeId]['attributes']['unit'] )
                        {
                            switch(strtolower($unit))
                            {
                                case 'week':
                                case 'weeks':   $time = $time*7;
                                case 'day':
                                case 'days':    $time = $time*24;
                                case 'hour':
                                case 'hours':   $time = $time*60;
                                case 'minute':
                                case 'minutes': $time = $time*60;
                                case 'second':  break;
                            }
                        }
        //print "XML: cache this page for $time seconds<br><br>";
                        // if a valid time was found parse also the other tags, valid is also 0, that's why this strange check
                        // accept only integers
                        if( $time == (int)$time )
                        {
                            $cacheOption['time'] = (int)$time;

                            if( ($id = $config->getIdByPath('depends',$cacheId )) &&
                                ($depends = $config->data[$id]['attributes']['value'] ))
                            {
                                $cacheOption['depends'] = $depends;
                            }

                            $setOptions['cache'] = $cacheOption;
                        }
                        else
                        {
                            $this->_log("ERROR in your xml config, caching-time: $time, is not valid, in: ".
                                        $isString?'tpl-file-embedded config':$configFileOrString);
                        }
                    }
            }

            // apply the options to this class
            $this->setOptions($setOptions);

            $this->_applyOptionsToFilterClasses($setOptions);
        }
    }

    /**
    *   this method applies the given options to all the filters which are registered as object-methods
    *   by definition every filter class that needs to know the delimiters or any option
    *   set in this class here, needs to have an array options and make the methods setOptions available
    *   which simply gets the options passed, the name for the options must be defined as in here
    *
    *   @access     public
    *   @version    01/12/19
    *   @param      array   $setOptions options to apply
    *   @return     string  the modified template file, the config part is removed
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function _applyOptionsToFilterClasses($setOptions)
    {
        // go thru all filters and get each class ONCE in the variable $filters
        $allFilters = array_merge( $this->_preFilters , $this->_postFilters );
        foreach( $allFilters as $aFilter )
        {
            if( !is_array($aFilter) ||  // is it not an array so it is simply a function name
                ( is_array($aFilter) && is_object($aFilter[0]) )    // or is it an array(&$object,'methodname')?
              )
            {
                if( is_array($aFilter) )
                    call_user_func( array(&$aFilter[0],'setOptions') , $setOptions );
            }
            else
            {
                if( is_array($aFilter[0]) )
                    call_user_func( array(&$aFilter[0][0],'setOptions') , $setOptions );
            }
        }
    }

    /**
    *   apply filter that are given in the xml-config
    */
    function _applyFiltersFromXMLConfig( &$treeObj , $elementId , $preFilter=false )
    {
        $filters = $treeObj->getChildrenIds($elementId);

        $registerMethod = $preFilter ? 'registerPrefilter' : 'registerPostfilter' ;
        $allFilterMethod = $preFilter ? 'allPrefilters' : 'allPostfilters' ;

        foreach( $filters as $aFilter )
        {
// FIXXME we only handle a tiny bit of all possible settings yet
            $class = @$treeObj->data[$aFilter]['attributes']['class'];
            $classFile = @$treeObj->data[$aFilter]['attributes']['classFile'];
            // do we have a class name? then make an instance of it and apply all(Pre|Post)Filters
            if( $class )
            {
                if( !$classFile )
                    $classFile = str_replace('_','/',$class).'.php';
                require_once($classFile);
                $filterInstance = new $class($this->options);
                // for some reason php doesnt see 0 as a real param, so it would bring an error if this->getOp... returns 0
                // so i have to pass it as a string :-/ very strange
                $fLevel = $this->getOption('filterLevel') ? $this->getOption('filterLevel') : '0';
                $this->$registerMethod(array(&$filterInstance,$allFilterMethod),$fLevel);
            }

        }

    }

    /**
    *   find xml-tags for configuration inside the template
    *
    *   @access     public
    *   @version    01/12/19
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string      the xml-config string
    */
    function _getXmlConfigString()
    {
        // read the entire file into one variable
        if( $input = @file($this->_templateFile) )
            $fileContent = implode( '' , $input );
        else
            return false;

        // remove all HTML-comments first, in case the config-part was commented out
        require_once('HTML/Template/Xipe/Filter/Basic.php');    // do only include if we really get here, should save some time
        $fileContent = HTML_Template_Xipe_Filter_Basic::removeHtmlComments($fileContent);

        if( preg_match( '/<html_template_xipe>.*<\/html_template_xipe>/Uis' , $fileContent , $configString ) )
        {
            return $configString[0];
        }
        return false;
    }

}
?>

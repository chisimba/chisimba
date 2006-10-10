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
//
//  $Id$
//

/**
*   this class is a wrapper around the actual engine to make it work with multiple templates using one instance
*
*   the problem this wrapper solves is the use of multiple templates with
*   one template-engine-instance. a problem occured when caching came into the
*   game. now there is not necessarily given, that the instance is only used
*   for one template at one time. because the caching takes place when calling the
*   compiled template we might need to use the instance for the template engine
*   another time, for writing the file to cache. since another template might have
*   been compiled inbetween the entire environment (properties, etc.) for the template
*   that we want to cache is set for another template, so we would have to rengenerate
*   the env again, which is really too stressy and bares too many sources of failure.
*
*   therefore this class manages a single object instance for each template file
*   that is used during one run of the engine (and one php-runtrough).
*   we could also tell the user to create a new instance of the template engine for
*   each template, but this doesnt seem convinient to me (at least).
*   so we manage the different instances in here be wrapping them into one object
*   which works like a container and returns results depending on the result of the
*   instance created for the current template file.
*   (i hope i could make myself clear :-) )
*
*   @package    HTML_Template_Xipe
*
*/
class HTML_Template_Xipe
{

    /**
    *   @var    array       here we save the objects, one for each tpl-file
    *   @access private
    */
    var $_objectPool = array();

    var $_lastUsedObjectKey = null;

    var $_preFilters = array();
    var $_postFilters = array();

    /**
    *
    *
    *   @access     public
    *   @version    02/05/20
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      array       the options
    */
    function HTML_Template_Xipe( $options=array() )
    {
        // just to let the constructor run once, so it can do some integrity checks (if needed)
        $tplClass = 'Main';

        if( isset($options['enable-XMLConfig']) && $options['enable-XMLConfig'] )
            $tplClass = 'XMLConfig';

        if( isset($options['enable-Cache']) && $options['enable-Cache'] )  // if the cache is on XMLConfig is needed, turn it on just to have the options properly set
        {
            $options['enable-XMLConfig'] = true;
            $tplClass = 'Cache';
        }

        if( !include_once('HTML/Template/Xipe/'.$tplClass.'.php') )
            die('could not include HTML/Template/Xipe/'.$tplClass.'.php');

        $tplClass = 'HTML_Template_Xipe_'.$tplClass;
        $this->_objectPool['defaultObject'] = new $tplClass( $options );
        // set it to a defined value so methods like 'getOption' is available after the constructor call
        $this->_lastUsedObjectKey = 'defaultObject';

        // copy all the options in here, so external use of $tpl->options work properly - using $tpl->options is deprectated!!!
        // ATTENTION: use $tpl->getOption[s]() instead
        $this->options = $this->_objectPool['defaultObject']->getOptions();
    }

    /**
    *   this method handles the filterlevel, since that is a thing, that is
    *   handled in this file, not in the Main.php
    *
    *   @access     public
    *   @version    02/06/21
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function _activateFilterLevel( $objKey )
    {
// FIXXME all filters that get registered before $tpl->setOption('filterLevel',x) will be lost !!!!
// because of the following line!!!
        $this->_objectPool[$objKey]->unregisterFilter();
        $filterLevel = $this->_objectPool[$objKey]->getOption('filterLevel');
        if( $filterLevel > 0 )
        {
            require_once('HTML/Template/Xipe/Filter/TagLib.php');
            // pass the options used in the template class, so we set the same delimiters in the filter
            $tagLib = new HTML_Template_Xipe_Filter_TagLib($this->_objectPool[$objKey]->getOptions());
            $this->_objectPool[$objKey]->registerPrefilter(array(&$tagLib,'allPrefilters'),$filterLevel);

            require_once('HTML/Template/Xipe/Filter/Basic.php');
            $tplFilter = new HTML_Template_Xipe_Filter_Basic($this->_objectPool[$objKey]->getOptions());
            $this->_objectPool[$objKey]->registerPrefilter(array(&$tplFilter,'allPrefilters'),$filterLevel);
            $this->_objectPool[$objKey]->registerPostfilter(array(&$tplFilter,'allPostfilters'),$filterLevel);
        }
    }


    /**
    *
    *
    *   @access     public
    *   @version    02/05/25
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      mixed       the funtion to call, or an array(&$object,'methodname')
    *   @param      mixed       if given parameters are passed to the function/method
    */
    function registerPostfilter( $functionName , $params=null )
    {
        $this->_postFilters[] = array( $functionName , $params );
    }

    /**
    *
    *
    *   @access     public
    *   @version    02/05/25
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      mixed       the funtion to call, or an array(&$object,'methodname')
    *   @param      mixed       if given parameters are passed to the function/method
    */
    function registerPrefilter( $functionName , $params=null )
    {
        $this->_preFilters[] = array( $functionName , $params );
    }

    function _setAllFilters( $objKey )
    {   
        // process the given filter level
        $this->_activateFilterLevel($objKey);

        // add all pre and post filters, dont use references here, so the filters are each an instance of its own
        // this is necessary since the options might be changed by any xml-config!
        if( sizeof($this->_preFilters) )
            foreach( $this->_preFilters as $aFilter )
                $this->_objectPool[$objKey]->registerPrefilter($aFilter[0],$aFilter[1]);
        if( sizeof($this->_postFilters) )
            foreach( $this->_postFilters as $aFilter )
                $this->_objectPool[$objKey]->registerPostfilter($aFilter[0],$aFilter[1]);
    }

    /**
    *   compile the template
    *
    *   @access     public
    *   @version    02/05/25
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the template file
    *   @return
    */
    function compile( $file )
    {
        $ret = $this->_methodWrapper( $file , 'compile' );
// just to be backwards compatible - should be removed one day!!!
// this doesnt work with caching, so use getCompiledTemplate() !!!
        $this->compiledTemplate = $this->getCompiledTemplate();
        return $ret;
    }                
    
    /**
    *   tells if the current template needed to be compiled
    *   if 'compile()' was called before and the template didnt
    *   need to be recompiled this method will return false too
    *
    *   @see        compile()
    *   @access     private
    *   @version    2003/01/10
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     boolean if the template was compiled
    */
    function compiled($file=null)
    {                            
        if ($file) {
            return $this->_methodWrapper($file,'compiled');
        }
        return $this->_objectPool[$this->_lastUsedObjectKey]->compiled();
    }

    /**
    *   may be we get it working one day :-)
    *
    *   @access     public
    *   @version    02/05/25
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the template file
    */
    function show($file)
    {
        return $this->_methodWrapper( $file , 'show' );
    }

    /**
    *
    *
    *   @access     public
    *   @version    02/05/25
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the template file
    */
    function isCached($file)
    {
        return $this->_methodWrapper($file,'isCached');
    }

    /**
    *   this method is implemented in Cache
    *
    *   @access     public
    *   @version    03/03/04
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the template file
    */
    function forceRecache($file)
    {
        return $this->_methodWrapper($file,'forceRecache');
    }    
    
    /**
    *   call the given method of the object that can be identified by the
    *   filename
    *
    *   @access     private
    *   @version    02/05/20
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string      the file name for which the object shall be retreived
    *   @return     object      a reference to an object from the object pool
    */
    function _methodWrapper(&$filename,$method)
    {
        $templateDir = $this->getOption('templateDir');
        // if we are on a windows, which we check using the directory-separator (is there an easier way?)
        // we better work with case independent filenames, apache converts
        // i.e. the doc-root to lower case (at least on my test machine)
        if (OS_WINDOWS) {
            $filename = strtolower($filename);
            $templateDir = strtolower($templateDir);
        }
        if (strpos($filename ,$templateDir) !== 0) {
            $filename = $templateDir.$filename;
        }

        $objKey = md5($filename);
        if (!isset($this->_objectPool[$objKey])) {
            // use __clone in php5
            // copy the default object with all its properties, yes COPY,
            // this is important we dont want a reference here
            $this->_objectPool[$objKey] = $this->_objectPool['defaultObject'];
            $this->_setAllFilters( $objKey );
        }
        $this->_lastUsedObjectKey = $objKey;
        $obj = &$this->_objectPool[$objKey];

//print "$objKey .... $filename, call: $method<br>";
//        return $this->_objectPool[$objKey];

        if (PEAR::isError($ret=$obj->setup( $filename ))) {
            return $ret;
        }

        // check if the method exists, this might be necessary if 'enable-Cache' is false but someone calls 'isCached'
        if (method_exists($obj,$method)) {
            return $obj->$method();
        } else {
            switch ($method) {
                case 'isCached':    // we return false for isCached since this keeps the application working
                    return false;
                    break;
                default:
                    return $obj->_error( "ERROR HTML_Template_Xipe: method $method does not exist" , PEAR_ERROR_RETURN );
            }
        }
    }

    // wrapper methods

    /**
    *   gets the delimiter which starts a template-tag, default is '{'
    *
    *   @version    01/12/07
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string      the begin delimiter
    */
    function getBeginDelimiter()
    {
        return $this->_objectPool[$this->_lastUsedObjectKey]->getOption('delimiter',0);
    }

    /**
    *   gets the delimiter which ends a template-tag, default is '}'
    *
    *   @version    01/12/07
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string      the end delimiter
    */
    function getEndDelimiter()
    {
        return $this->_objectPool[$this->_lastUsedObjectKey]->getOption('delimiter',1);
    }

    /**
    *   gets the template directory
    *
    *   @version    01/12/14
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string      path
    */
    function getTemplateDir()
    {
        return $this->_objectPool[$this->_lastUsedObjectKey]->getOption('templateDir');
    }

    /**
    *   gets the compiled tempalte name for the proper object
    *
    *   @version    02/05/25
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string      the complete file name
    */
    function getCompiledTemplate()
    {
        return $this->_objectPool[$this->_lastUsedObjectKey]->getCompiledTemplate();
    }

    /**
    *   Return the rendered content
    *   Thanks to Michael Johnson
    *
    *   @param  string  the template file to be compiled.
    *   @param  array   this is the array of values built using 'compact()'
    *   @return string  the rendered content
    */
    function getRenderedTemplate($file,$data=array())
    {
        $this->compile($file);
        ob_start();
        extract($data);
        include $this->getCompiledTemplate();
        $ret = ob_get_contents();
        ob_end_clean();
        
        return  $ret;
    }
    
    //
    //  pass on the options-method calls
    //

    function getOption( $option )
    {
        return $this->_objectPool[$this->_lastUsedObjectKey]->getOption( $option );
    }
    function getOptions()
    {
        return $this->_objectPool[$this->_lastUsedObjectKey]->getOptions();
    }                                 
    
    /**
    *   setting options is always done on the default object!!!!
    *   this means before using a real tpl-object (by calling any method that goes on a file, like compile, isCached, etc.)
    *   the options stuff should be done, this does not effect the xml-options!!!
    */
    function setOptions( $options , $force=false )
    {
// i think we should better set the option for each object and the lastUsed !!!
        return $this->_objectPool['defaultObject']->setOptions( $options , $force );
    }
    function setOption( $option , $value , $force=false )
    {
        $ret = $this->_objectPool['defaultObject']->setOption( $option , $value , $force );
        if( $option == 'filterLevel' ) // handle the filterLevel special, since it is handled in this file here!!!
            $this->_setAllFilters('defaultObject');
        return $ret;
    }

}
?>

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

require_once 'PEAR.php';
require_once 'HTML/Template/Xipe/Options.php';
require_once 'HTML/Template/Xipe/Filter/Internal.php';
require_once 'Log.php';

/**
*   the intention is to use normal php in the template without the need to write
*   <?php or <?= all the time
*   but smarty, IT[X] and so on just dont give me enough power (i need referencing the reference of a variable and so on)
*   and i want to have the entire power of php inside the template, without
*   making the code look so ugly as it sometimes does, only because you want to write a varibale
*   so this template engine will not do much, but fulfill my needs
*
*   @package    HTML_Template_Xipe
*
*/
class HTML_Template_Xipe_Main extends HTML_Template_Xipe_Options
{

// FIXXME a problem we have here: is that if i use {include(a.php)} the varibales of this file overwrite the ones
// set for the template where it is included, ... ***create a namespace for each file*** somehow, which is unique for every tempalte ... but how
// without the need for the user to change its programming behaviour,
// one solution would be copying everything from $GLOBALS into a unique varibale and replacing all variables in the
// template using this unique variable, but this makes it a HUGE overhead, but would work

// FIXXME2 by testing a site i realized, that a lot of php tags inside a tempalte slow slow down rendering
// very much, somehoe it seems php is not too good in rendering html-pages with a lot of php tags inside
// so we can write a filter, which can be applied that translates the entire template to use echo's only :-)
// some work ha?

// TODO
// - enable a taglib/filter for sql statements inside the template, even though i will never use it :-)
//   if we put that in a seperate file we can load it only on request, saves some php-compiling time
// - add a taglib/filter for translate, smthg like: {%t $phpValue %}, to explicitly translate a string
//   in case someone doesnt like to use the filter "applyTranslateFunction"
// - add a filter for converting strings to html-entities, could work like "applyTranslateFunction" on
//   mark up delimiters, or could be a taglib-tag too ... whatever
//
// MAY BE features
// - url obfuscating (or whatever that is called), rewrite urls and decode them when retreiving a call to a specific page ... somehow
//   i dont know if that belongs in a template engine, may be just write in the tutorial how to attach those products easily
//   using a filter or whatever
// -
//
//
    /**
    *   for customizing the class
    *
    *   compileDir'    =>  'tmp',  // by default its always the same one as where the template lies in, this might not be desired
    *   delimiter'     =>  array('{','}'),
    *   templateDir'   =>  '',
    *   autoBraces'    =>  true,   // see method 'autoBraces' for explaination
    *   makePhpTags'   =>  true,   // set this to false if you dont want the delimiters to be translated to php tags automativally
    *   forceCompile'  =>  false,  // only suggested for debugging
    *   xmlConfigFile' =>  'config.xml', // name of the xml config file which might be found anywhere in the directory structure
    *   locale'        =>  'en',   // default language
    *   cache'         =>  array(
                               'time'      => false// set this to the number of seconds for which the final (html-)file shall be cached
                                                    // false means the file is not cached at all
                                                    // if you set it to 0 it is cached forever
                                ,'depends'  => 0    // what does it depend on if the cache file can be reused
                                                    // i.e. could be $_REQUEST $_COOKIES
                                ),
    *   logLevel'      =>  1,      // 1 - only logs new compiles, 0 - logs nothing, 2 - logs everything even only deliveries
    *   filterLevel'   =>  10,     // 0    is no filter, use this if u want to register each filter u need yourself
                                    // 1-7  can still be defined :-)
                                    // 8    comments stay in the code
                                    // 9    as 10 only that the resulting HTML-code stays readable
                                    // 10   use all default filters, uses the allPre/Postfilters methods of each filter class
    *   enable-XMLConfig'=>false,
    *   enable-Cache'  =>  false,  // if you only turn on the Cache XMLConfig will be turned on too, since it is needed

    *   verbose'       =>  true,   // set this to true if the engine shall output errors on the screen
                                    // in addition to returning a PEAR_Error
                                    // this makes getting the engine running easier
                                    // on a production system you should turn this off

    *   logFileExtension'=>'log',  // the extension for the log file
    *   cacheFileExtension'=>'html',// file extension for the cached file
    *   compiledTemplateExtension'=>'php',// the extension the generated template shall have
    *
    *   The templateExtension is currently used in the Filter/TagLib, for determining if
    *   an included file is a template file or a macro file. Templates are handled a bit
    *   differently, they are included as often as the {%include%} tag occurs.
    *   Macro-files are surrounded by an if() clause, to prevent multiple declarations
    *   of macros (which are simply php-functions)
    *   <br>
    *       'templateExtension'=>   'tpl',<br>
    *       'macroExtension'=>      'mcr'
    *
    *   @access private
    *   @var    array   $options    the options for initializing the template class
    */
    var $options = array(
                            'compileDir'    =>  'tmp',
                            'delimiter'     =>  array('{','}'),
                            'templateDir'   =>  '',
                            'autoBraces'    =>  true,
                            'makePhpTags'   =>  true,
                            'forceCompile'  =>  false,
                            'xmlConfigFile' =>  'config.xml',
                            'locale'        =>  'en',
                            'cache'         =>  array(
                                                    'time'      => false
                                                    ,'depends'  => 0
                                                     ),
                            'logLevel'      =>  1,
                            'filterLevel'   =>  10,
                            'enable-XMLConfig'=>false,
                            'enable-Cache'  =>  false,
                            'verbose'       =>  true,
                            'logFileExtension'=>'log',
                            'cacheFileExtension'=>'html',
                            'compiledTemplateExtension'=>'php',
                            'debug'         =>  0,
                            'templateExtension'=>   'tpl',
                            'macroExtension'=>      'mcr'
                        );

    /**
    *   the current template file for this instance
    */
    var $_templateFile = '';

    var $_compiledTemplate = '';

    var $_didSetup = false;

    var $_logFileName = '';

    var $_compiledFilePrefix = '';

    /**
    *   @var    boolean     will be set to true if a recompile is needed,
    *                       this is when any xml-config file that applies to the current template has changed
    *                       or the template itself, or if the compiled template was removed, etc...
    */
    var $_needsRecompile = false;

    /**
    *   saves the preFilters which will be applied before compilation
    *
    *   @access private
    *   @var    array   methods/functions that will be called as prefilter
    */
    var $_preFilters = array();

    /**
    *   saves the postFilters which will be applied after compilation
    *
    *   @access private
    *   @var    array   methods/functions that will be called as postfilter
    */
    var $_postFilters = array();

    /**
    *   @var    float   the time compiling/delivering took
    */
    var $_compileTime = 0;
                           
    /**
    *   @var    boolean if the template was compiled
    *   @see    compiled(), compile()
    */
    var $_compiled = false;

    var $logObject = null;
    
    /**
    *   the constructor, pass the options to it as needed
    *
    *   @see        $options
    *   @version    01/12/03
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function HTML_Template_Xipe_Main( $options=array() )
    {
        foreach( $options as $key=>$aOption )
            $this->setOption( $key , $aOption );

        // replace (multiple) backslashes with forward slashes and replace multiple slashes with single slashes
        // this way we can quitely sleep and use windows too :-) and only work with forward slashes
        // all over the template engine
        // the replacing of multiple slashes is doen because i realized that something like
        // $DOCUMENT_ROOT.'/libs/' might come out to be '/document/root//libs' depending on the apache configuration
        $this->setOption('compileDir' , preg_replace("/\\\+|\/+/" , DIRECTORY_SEPARATOR , $this->getOption('compileDir') ));
        $this->setOption('templateDir' , preg_replace("/\\\+|\/+/" , DIRECTORY_SEPARATOR , $this->getOption('templateDir') ));

    }

    /**
    *   this method sets up an instance of the engine
    *   since the philosophie has changed to manage a single object-instance for
    *   each page we need a method here so we dont need to setup all the internal
    *   vars many times
    *
    *   @version    02/05/25
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string      the template filename for which this instance shall be setup
    *   @return     boolean     if setup was necessary or not
    */
    function setup( $filename )
    {
        if( $this->_didSetup == true )
            return false;

        //
        // setup all the internal vars, like compiledTemplate, etc.
        //

        $this->_templateFile = $filename;
/*
        $pathInfo = pathinfo($this->_templateFile);
        if( in_array($pathInfo['extension'],array('sxw','sxc','sxi','sxd')) )
        {

        }
*/
        // build the filename-prefix that will be used for the compiled file
        if( PEAR::isError( $ret = $this->_getCompiledFilePrefix() ) )
        {
            return $ret;
        }
        $this->_compiledFilePrefix = $ret;

//print "this->_templateFile = $this->_templateFile<br>this->_compiledFilePrefix = $this->_compiledFilePrefix<br><br>";

        $this->_compiledTemplate = $this->_compiledFilePrefix.$this->getOption('compiledTemplateExtension');

//print ".........BEFORE SETUP METHOD..........<br>";
//print_r($this);print "<br><br>";
        //
        //  do all the xml config stuff
        //
        if( $this->getOption('enable-XMLConfig') )
        {
            if( PEAR::isError($ret=$this->_applyXmlConfig()) )
                return $ret;
        }

        if( $this->getOption('enable-Cache') )
        {
/*            $this->_cachedFilename = $this->_getCacheFileName($templateFile);

            // set the internal values which are needed by the internal methods
            if( $this->_needsRecompile() || $this->_applyXmlConfigIfNeeded() )

            $this->doCompile = ???  // shall the compile method do anything or return true right away?
*/
        }

//print ".........END OF SETUP METHOD..........<br>";
//print_r($this);print "<br><br>";

        $this->_didSetup = true;
        return true;
    }

    /**
    *   gets the destination file name prefix
    *   i.e.
    *   for a template in /path/to/tpl/file.tpl
    *   it returns  <templateDir>/<compileDir>/<rest of the path>/file.tpl.<locale>
    *
    *   @access     private
    *   @version    2002/03/11
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string      the filename-prefix that will be used for the compiled file
    */
    function _getCompiledFilePrefix()
    {
        // remove the 'tempalteDir' from the templateFile name
        if( strpos( $this->_templateFile , $this->getOption('templateDir') ) === 0 )
            $this->_templateFile = str_replace( $this->getOption('templateDir') , '' , $this->_templateFile );

        // replace multiple slashes
        $this->_templateFile = preg_replace( '/\/+/' , DIRECTORY_SEPARATOR , $this->_templateFile );
        // set the template file property and remove leading slashes
        $this->_templateFile = preg_replace( '/^\/+/' , '' , $this->_templateFile );

        // if the compileDir doesnt contain the document root then we figure it starts under the
        // template dir, i.e. the template dir is: '/usr/local/httpd/htdocs/myProject'
        // and the compile dir is simply: '/tmp'
        if( strpos( $this->getOption('compileDir') , $_SERVER['DOCUMENT_ROOT'] )!==0 )
        {
            $compileDir = preg_replace('/^\//' , '' , $this->getOption('compileDir') ); // strip of a leading '/' to be sure not to have 2 slashes there :-)
            $this->setOption( 'compileDir' , $this->getOption('templateDir').DIRECTORY_SEPARATOR.$compileDir);
        }

        $compileDir = $this->getOption('compileDir');

        if (!@is_dir($compileDir)) {                 // check if the compile dir has been created
            return $this->_error(   "'compileDir' could not be accessed <br>".
                                    "1. please create the 'compileDir' which is: <b>'$compileDir'</b><br>2. give write-rights to it" ,
                                    PEAR_ERROR_DIE );
        }

        if (!@is_writeable($compileDir)) {
// i dont know how to check if "enter" rights are given
            return $this->_error(   "can not write to 'compileDir', which is <b>'$compileDir'</b><br>".
                                    "1. please give write and enter-rights to it" , PEAR_ERROR_DIE );
        }

//print "file=$file<br>";
        $directory = dirname( $this->_templateFile );
        $filename = basename( $this->_templateFile );

        // extract dirname to create directorie(s) in compileDir in case they dont exist yet
        // we just keep the directory structure as the application uses it, so we dont get into conflict with names
        // i dont see no reason for hashing the directories or the filenames
        if ($directory!='.') {  // $directory is '.' also if no dir is given
            $path = explode(DIRECTORY_SEPARATOR,$directory);
            foreach ($path as $aDir) {
                $compileDir = $compileDir.DIRECTORY_SEPARATOR.$aDir;
                if (!@is_dir($compileDir)) {
                    umask(0000);                        // make that the users of this group (mostly 'nogroup') can erase the compiled templates too
                    if (!@mkdir($compileDir,0777)) {
                        return $this->_error(   "couldn't make directory: <b>'$aDir'</b> under <b>'".
                                                $this->getOption('compileDir')."'</b><br>".
                                                "1. please give write permission to the 'compileDir', ".
                                                "so Xipe can create directories inside" , PEAR_ERROR_DIE );
                    }
                }
            }
        }

        // build the filename prefix, add locale only if given
        $ret =  $this->getOption('compileDir').DIRECTORY_SEPARATOR.
                $this->_templateFile.
                ($this->getOption('locale') ? '.'.$this->getOption('locale') : '' ).
                '.';

        $this->_templateFile = $this->getOption('templateDir').DIRECTORY_SEPARATOR.$this->_templateFile;

        return $ret;
    }


    /**
    *   DONT USE YET, since i didnt find a way to make it workin, because no
    *   variable in the template is known if i include it here
    *   use: $ tpl->compile('index.tpl');
    *        include($ tpl->getCompiledTemplate());
    *   instead
    *
    *   @version    01/12/03
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function show()
    {
        if( $this->compile($file) )
        {
            include( $this->_compiledTemplate );
        }
        else
        {
            return $this->_error( "ERROR: couldnt get compiled template!!!" , PEAR_ERROR_DIE );
        }
    }

    /**
    *   here all the replacing, filtering and writing of the compiled file is done
    *   well this is not much work, but still its in here :-)
    *
    *   @access     private
    *   @version    01/12/03
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function parse()
    {
        // read the entire file into one variable
        if( $input = @file($this->_templateFile) )
            $fileContent = implode( '' , $input );
        else
            $fileContent = '';                      // if the file doesnt exist, write a template anyway, an empty one but write one

        // pass option to know the delimiter in the filter, but parse the xml-config before!!!, see line above
        $defaultFilter = new HTML_Template_Xipe_Filter_Internal($this->options);

        //  apply pre filter
        $fileContent = $this->applyFilters( $fileContent , $this->_preFilters );

        // this filter does all the default replacement of the delimiters
        $internalFilters = array();                 // empty them every time, in case this method is called multiple times in one script

        // if xml config is on, remove the xml-config tags now
        // do this first to reduce the file size and reduce later parsing times
        if( $this->getOption('enable-XMLConfig') == true )
            $internalFilters[] = array(&$defaultFilter,'removeXmlConfigString' );

        if( $this->getOption('makePhpTags') == true )
            $internalFilters[] = array(&$defaultFilter,'makePhpTags');

        // if the option autoBraces is on apply the _first_ postFilter right here
        // which does the autBracing
        if( $this->getOption('autoBraces') == true )
            $internalFilters[] = array(&$defaultFilter,'autoBraces');

        if( $this->getOption('enable-Cache') && $this->getOption('cache','time') !== false )
            $internalFilters[] = array( &$this , '_makeCacheable' );

        $fileContent = $this->applyFilters( $fileContent , $internalFilters );

        //  apply post filter
        $fileContent = $this->applyFilters( $fileContent , $this->_postFilters );


        // write the compiled template into the compiledTemplate-File
        if( ($cfp = fopen( $this->_compiledTemplate , 'w' )) )
        {
            fwrite($cfp,$fileContent);
            fclose($cfp);
            chmod($this->_compiledTemplate,0777);
        }

        return true;
    }

    /**
    *   compile the template
    *
    *   @access     public
    *   @version    01/12/03
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $file   relative to the 'templateDir' which you set when calling the constructor
    *   @return
    */
    function compile()
    {
//print $this->_templateFile.'<br>';
        // cant the log-class do that???
        $startTime = split(" ",microtime());
        $startTime = $startTime[1]+$startTime[0];

        $this->_compiled = false;
        if( $this->_needsRecompile() )
        {
            $this->_log( 'compile started' );
            $this->_log('Locale:'.$this->options['locale'] );

            $this->_log( 'needsRecompile is true' );
            if( !$this->parse() )
                return false;

            $endTime = split(" ",microtime());
            $endTime = $endTime[1]+$endTime[0];
            $itTook = ($endTime - $startTime)*100;
            $this->_log("(compilation and) deliverance took: $itTook ms" );

            $this->_compiled = true;
        }

        return true;
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
    function compiled()
    {
        return $this->_compiled;
    }

    /**
    *   checks if the current template needs to be recompiled
    *   this is the case for either case:
    *   - if forceCompile option is on
    *   - if the template has changed/was removed, etc.
    *
    *   @access     private
    *   @version    2002/03/11
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     true if the template should be recompiled, false otherwise
    */
    function _needsRecompile()
    {
        if( $this->getOption('forceCompile') )
        {
            $this->_log('recompile because option "forceCompile" is true');
            return true;
        }

        if( !$this->_isUpToDate() )              // check if the template has changed
        {
            $this->_log('recompile because tpl has changed/was removed: '.$this->_templateFile);
            return true;
        }

        if( $this->_needsRecompile == true )        // this will be set to true i.e. by the xml-config check, if any of the xml-files has changed
            return true;

        return false;
    }

    /**
    *   checks if the compiled template is still up to date
    *
    *   @access     private
    *   @version    01/12/03
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string      $fileToCheckAgainst if given this file is checked if it is newer than the compiled template
    *                                               this is useful if for example only an xml-config file has changed but not the
    *                                               template itself
    *   @return     boolean     true if it is still up to date
    */
    function _isUpToDate( $fileToCheckAgainst='' )
    {
        if( $fileToCheckAgainst == '' )
            $checkFile = $this->_templateFile;
        else
            $checkFile = $fileToCheckAgainst;

        if( !file_exists( $this->_compiledTemplate ) ||
            !file_exists( $checkFile ) ||
            filemtime( $checkFile ) > filemtime( $this->_compiledTemplate )
          )
        {
            return false;
        }

        return true;
    }

    function getCompiledTemplate()
    {
        return $this->_compiledTemplate;
    }

// filter stuff


    /**
    *   register a prefilter, which will be executed BEFORE the template
    *   is being compiled
    *
    *   @access     public
    *   @version    01/12/03
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      mixed       the funtion to call, or an array(&$object,'methodname')
    *   @param      mixed       if given parameters are passed to the function/method
    */
    function registerPrefilter( $functionName , $params=null )
    {
        $this->_registerFilter( 'pre' , $functionName , $params );
    }

    /**
    *   register a postfilter, which will be executed AFTER the template
    *   was compiled
    *
    *   @access     public
    *   @version    01/12/07
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      mixed       the funtion to call, or an array(&$object,'methodname')
    *   @param      mixed       if given parameters are passed to the function/method
    */
    function registerPostfilter( $functionName , $params=null )
    {
        $this->_registerFilter( 'post' , $functionName , $params );
    }

    /**
    *   put the filter either in the pre or postFilter array
    *   check for language filters too! those have to go first
    *
    *   @access     public
    *   @version    02/09/22 (day of the elections in germany :-) )
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      mixed       the funtion to call, or an array(&$object,'methodname')
    *   @param      mixed       if given parameters are passed to the function/method
    */
    function _registerFilter( $which , $functionName , $params )
    {
        $langFilter = false;
        if( is_array($functionName) )
        {
            if( method_exists($functionName[0],'isLanguageFilter') )
                $langFilter = call_user_func( array($functionName[0],'isLanguageFilter') );
        }

        if( $params != null )
        {
            settype($params,'array');
            $thisFilter = array($functionName,$params);   // use reference here !!! see comment above in registerPrefilter
        }
        else
            $thisFilter = $functionName;

        $arrayName = &$this->{'_'.$which.'Filters'};
        if( $langFilter )   // language filters have to be applied first, so all other filters also have effect on it
            array_unshift($arrayName,$thisFilter);
        else
            $arrayName[] = $thisFilter;
    }

    /**
    *   unregister a filter
    *
    *   @access     public
    *   @version    02/06/21
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string      the filter name, if not given all filters are unregistered
    */
    function unregisterFilter( $name=null )
    {
        if( $name==null )
        {
            $this->_postFilters = array();
            $this->_preFilters = array();
        }
    }

    /**
    *   actually it will only be used to apply the pre and post filters
    *
    *   @access     public
    *   @version    01/12/10
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input      the string to filter
    *   @param      array   $filters    an array of filters to apply
    *   @return     string  the filtered string
    */
    function applyFilters( $input , $filters )
    {
        if( sizeof($filters) )
        foreach( $filters as $aFilter )
        {
// FIXXME use log class
            $startTime = split(" ",microtime());
            $startTime = $startTime[1]+$startTime[0];
            $sizeBefore = strlen($input);

            if( !is_array($aFilter) ||  // is it not an array so it is simply a function name
                ( is_array($aFilter) && is_object($aFilter[0]) )    // or is it an array(&$object,'methodname')?
              )
            {
                $input = call_user_func_array( $aFilter , array($input) );

                $appliedFilter = $aFilter;
                if( is_array($aFilter) )
                    $appliedFilter = $aFilter[1];
            }
            else
            {
                array_unshift($aFilter[1],$input);
                $input = call_user_func_array( $aFilter[0] , $aFilter[1] );

                $appliedFilter = $aFilter[0];
                if( is_array($aFilter[0]) )
                    $appliedFilter = $aFilter[0][1];
            }

            $sizeAfter = strlen($input);
// FIXXME use log class
            $endTime = split(" ",microtime());
            $endTime = $endTime[1]+$endTime[0];
            $itTook = ($endTime - $startTime)*100;

            $this->_log("applying filter: '$appliedFilter' \ttook=$itTook ms, \tsize before: $sizeBefore Byte, \tafter: $sizeAfter Byte");
        }


        return $input;
    }



// private stuff


    /**
    *   show an error on the html page, format it, so it is obvious
    *
    *   @access     private
    *   @version    02/02/25
    *   @param      string  $message    the error message
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function _error( $message , $mode = PEAR_ERROR_RETURN )
    {
        if( $this->getOption('verbose') == true )
        {
            echo '<span style="color:red; background-color:FBFEA1; font-weight:bold;">HTML_Template_Xipe ERROR</span><br>';
            echo '<span style="color:008000; background-color:FBFEA1;">';
            echo $message;
            echo '</span><br><br>';
        }
        return new PEAR_Error( $message , null , $mode );
    }

    /**
    *   logs errors depending on the loglevel it does either write them
    *   in a file or leaves it all the way
    *
    *   @access     private
    *   @version    02/05/13
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the log message
    *   @param      int     the log level of the message
    */
    function _log($message)
    {
        if ($this->getOption('logLevel') == 0) {
            return;                             
        }

        if ($this->getOption('debug') > 0) {
            print("LOG MESSAGE: $message.<br>");
        }
        // dont log the same messages multiple times
        if (!empty($this->_loggedMessages[md5($message)])) {
            return;
        }
        
        $this->_loggedMessages[md5($message)] = true;

        if ($this->logObject==null || !is_object($this->logObject) || !$this->_logFileName) {
            $this->_logFileName = $this->_compiledFilePrefix.$this->getOption('logFileExtension');
            $this->logObject =& Log::factory('file',$this->_logFileName);
            $this->_log('---------------------');
//FIXXME write the options in the log-file but nicer!!! than here
            $this->_log('options: '.serialize($this->options));
            $this->_log( 'current logLevel is: '.$this->getOption('logLevel') );
        }
        $this->logObject->log($message);
    }
}
?>

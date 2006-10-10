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

require_once 'HTML/Template/Xipe/XMLConfig.php';

/**
*   this class does the caching of final pages in pure html (or anything you use it for)
*   ok first an apology to the PEAR::Cache-team :-) i tried to
*   use the PEAR::Cache and it worked fine (was easy to implement) until i got to the
*   point that i wanted to include the cached content,
*   since the philosophy of this template engine is still based
*   on including either the template or the cached output.
*   the PEAR::Cache has a wonderful Cache implementation but
*   what i need here is the final file and there are 2 things that
*   make it impossible (or would need a change in the concept of this template engine) to
*   use the PEAR::Cache
*   1.  PEAR::Cache saves the file encoded and adds extra stuff in the file not only
*       the actual result, the html page in the normal case
*       it always has the expiration data and extra user data saved in the
*       cached file, and additionally the real data are encoded either bbase64
*       or serialized, which means a simple include of the cached file is not possible
*       i would have to decode it first etc.
*   2.  PEAR::Cache doesnt provide a method to give back the full path and filename of the
*       cached file, it does always only return the content of it
*       which of course is right for the way PEAR::Cache works, since the
*       data have to be decoded
*   so i have to implement the caching my self :-/
*
*   to use the cache u have to set the option 'enable-Cache' to true
*   when making an instance of the template engine
*   and in the file that shall be cached do the following
*   (assuming $tpl is an instance of the template engine with the cache option on):
*
*   // before calling '$tpl->isCached()' be sure that all the variables
*   // that the cache-file depends on (see your xml config) are set to the proper values
*   // so the cahe methods can properly determine if the file is already cached
*   // BE WARNED: letting a cache-file depend on i.e. $_REQUEST makes it possible to
*   // run a DOS-attack, since a new cache file has to be created everytime any
*   // request parameter changes, i.e. http://your.site.com/index.php?whatever
*   // this might flood your webservers diskspace
*   $myTplFile = 'your/template/file.tpl';
*   if( !$tpl->isCached( $myTplFile ) )
*   {
*       do all the stuff needed to be done to build a cacheable page
*       all the time consuming stuff, like db-requests, or getting data
*       using a webservice (which was my actual need for caching)
*   }
*   // simply include the file as usual
*   $tpl->compile( $myTplFile );
*   include( $tpl->getCompiledTemplate() );
*
*
*   @package    HTML_Template_Xipe
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class HTML_Template_Xipe_Cache extends HTML_Template_Xipe_XMLConfig
{

    /**
    *   @var    string      the complete filename including path to the cached file
    */
    var $_cachedFile = '';

    /**
    *   @var    string      the global object reference name
    */
    var $_cacheObjReference = '';

    /**
    *   this can be set to true using forceRecache()
    *   and it will rebuild the cached file, no matter if it is already cached
    *   @see    forceRecache()
    */
    var $_forceRecache = false;
    
    /**
    *   checks if the file is cached
    *
    *   @access     public
    *   @version    02/05/26
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     boolean     returns true if the output is cached, false otherwise
    */
    function isCached()
    {
        if ($this->getOption('cache','time')===false) {
            return false;
        }
        // lets make sure that we define the object reference globally, which is called
        // from within the template
        // if we wouldnt do this here we would have a problem if a compiled template
        // would be included but the reference to $this is not defined, which is needed to call _cacheEnd()
        // at the end of the template
        $this->_createCacheObjRef();

        // check the forceRecache after creating the cacheObjRef, so the
        // template can access it
        if ($this->_forceRecache) {                 // if _forceRecache is true we say the file is not cached
            return false;
        }

        $cacheFile = $this->_getCacheFileName();

        if (!file_exists($cacheFile)) {             // if the cached file doesnt exist
            $this->_log('CACHE: cached file doenst exist: '.$cacheFile);
            return false;
        }

        // has the cached file expired?
        // the filetime is the time when it expires, this way we dont have to save the
        // caching time anywhere
        if (time()>filemtime($cacheFile)) {
            // if the caching time is 0 it shall be cached forever, so set the expire time to one year ahead
            // again
            if ($this->getOption('cache','time')===0) {
                touch( $cacheFile , time()+60*60*24*365 );
                return true;
            }
            $this->_log('CACHE: cached file has expired: '.$cacheFile);
            return false;
        }

        // if the template needs a recompile we dont cache
        if ($this->_needsRecompile()) {
            $this->_log('CACHE: template needs recompile');
            // remove the cached file so we know that we will recreate it
            // i am doing this because getCompiledTemplate() would return the old cached filename
            // also if the tpl was recompiled, because after compilation, needsRecompile is false and the cached file
            // does also exists, only that the expiration time might have changed, which getCompileTemplate, which calls
            // isCached, has no chance to notice
            @unlink($cacheFile);
            return false;
        }
        return true;
    }

    /**
    *   tell the engine to rebuild the cached file
    *
    *   @access     public
    *   @version    03/03/04
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function forceRecache()
    {
        $this->_forceRecache = true;
    }    
    
    /**
    *   get the filename of the destination file
    *
    *   @access     public
    *   @version    02/05/26
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string  returns the complete filename
    */
    function _getCacheFileName()
    {
        if ($this->_cachedFile) {
            return $this->_cachedFile;
        }
        // add the hash which is calculated over the dependency data, like $_REQUEST, to have a unique filename
        // for the different dependency data

        $extension = '';
        if (($depends = $this->getOption('cache','depends'))) {
//print $depends;
            $depends = explode(' ',$depends);
            // init $vars with the names that shall be cached, to be sure that if the values dont change but the names
            // we have to create a new name
            $vars = $depends;
            foreach ($depends as $aDepend) {
                // if the variable name is like $var['varName'] do only globalize '$var'
                // even though things like _REQUEST,_SESSION, etc. dont need to be globalized - but it does no harm either
                // and if it is a $class-> globalize only $class
                $globalize = preg_replace('/\[.*\]/','',$aDepend);
                $globalize = preg_replace('/->.*/','',$globalize);
                // serilaize the var since it might also be an array or object, or whatever
//print("global $globalize;\$var = serialize($aDepend);<br>");
                eval("global $globalize;\$var = serialize($aDepend);");
//print("$aDepend = $var<br>");
//print $this->_templateFile." ... $var<br>";
                $vars = md5("$vars:$var");
            }
            $extension = md5($vars).'.';
        }
//print "extension=$extension<br>depends = ";print_r($depends);

        $cacheFileName = $this->_compiledFilePrefix.$extension.$this->getOption('cacheFileExtension');
        $this->_cachedFile = $cacheFileName;
        return $this->_cachedFile;
    }

    /**
    *   this is called from within the template to write the content that shall be cached
    *
    *   @access     public
    *   @version    02/05/26
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function _cacheEnd()
    {
        $content = ob_get_contents();

        // get the destination filename
        $file = $this->_getCacheFileName();

//print '_cacheEnd write into: '.$file.' <br><br>';

        if (file_exists($file)) {
            unlink($file);
        }
        if (($cfp = fopen( $file , 'w' ))) {
            if ($this->getOption('debug')>0) {
                fwrite($cfp,'CACHED CONTENT<br>'.$content);
            } else {
                fwrite($cfp,$content);
            }
            fclose($cfp);
            chmod($file,0777);
        }

        // set file modification time to the time when it expires,
        // this saves us the checking of the config data, either in options array or the xml file
        $expires = time()+$this->getOption('cache','time');
        if ($this->getOption('cache','time')===0) { // if the caching time is 0 set it to one year ahead
            $expires = time()+60*60*24*365;
        }

        touch($file,$expires);

        $this->_log('CACHE: caching file for '.$this->getOption('cache','time').' seconds (0 means forever), until: '.
                    date( 'd.m.Y H:i:s' , $expires ));

        ob_end_flush();
    }

    /**
    *   this adds the calls to the template needed for caching this file
    *   it is used as a filter by the parse method
    *
    *   @see        parse()
    *   @access     private
    *   @version    02/05/26
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the actual file content
    *   @return     string  the modified file content
    */
    function _makeCacheable( $input )
    {
        $input = sprintf(   '<'.'?php ob_start() ?'.'>%s<'.'?php $%s->_cacheEnd() ?'.'>' ,
                            $input ,
                            $this->_createCacheObjRef() );
        return $input;
    }


    /**
    *
    *
    *   @access     private
    *   @version    02/05/26
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string  the name of the global object ref
    */
    function _createCacheObjRef()
    {
        if ($this->_cacheObjReference) {
            return $this->_cacheObjReference;
        }
        // create global (!) object which can be called from within the template file
        // to call the cacheStart/cacheEnd-methods
        $this->_cacheObjReference = '_'.md5($this->_templateFile.'cache');   // has to start with a valid character for a varibale name, '_'
        // its a reference to this instance, so we can simply call the cache methods
        // we need to do it like this since we dont know the instance name of the template
        // engine and of this special instance neither, that's the easiest way to do it
        $GLOBALS[$this->_cacheObjReference] = &$this;
//print "_createCacheObjRef = {$this->_cacheObjReference} for {$this->_templateFile}<br>";
        return $this->_cacheObjReference;
    }

    //
    //  overwritten methods
    //

    /**
    *   return the cached file name if it is set, and it is only
    *   set if the file is cached and valid, that means not expired etc.
    *
    *   @access     public
    *   @version    02/05/26
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string  the filename used to include
    */
    function getCompiledTemplate()
    {
        if ($this->isCached()) {
            return $this->_cachedFile;
        }
        return parent::getCompiledTemplate();
    }

    /**
    *   do only really compile if the file is not cached
    *
    *   @access     public
    *   @version    02/05/26
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     boolean     actually always true
    */
    function compile()
    {
        if ($this->isCached()) {
            return true;
        }
        return parent::compile();
    }

}
?>

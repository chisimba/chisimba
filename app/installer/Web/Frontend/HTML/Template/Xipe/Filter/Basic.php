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

require_once 'HTML/Template/Xipe/Options.php';

/**
*   the default filter(s) i use and Xipe needs
*
*   @package    HTML_Template_Xipe
*   @access     public
*   @version    01/12/10
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class HTML_Template_Xipe_Filter_Basic extends HTML_Template_Xipe_Options
{
// i need the method setOption, that's why i extend myPEAR_Common

    /**
    *   for passing values to the class, i.e. like the delimiters
    *   @access private
    *   @var    array   $options    the options for initializing the filter class
    */
    var $options = array(   'delimiter'     => array(), // first value of the array is the begin delimiter, second the end delimiter
                            'autoBraces'    => true );  // we need to check this for some filters, since some depend on it or have to behave differently

    /**
    *   apply (almost) all filters available in this class
    *   thanks to hint from Alan Knowles
    *   i am only applying those filters which i think are useful in mostly every case
    *   i.e. applyHtmlEntites i am not applying since it would convert every output to html
    *   and that is not desired in every case
    *
    *   @version    02/05/22
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the actual input string, to which the filters will be applied
    *   @param      int     the filter level
    *   @return     string  the resulting string
    */
    function allPrefilters( $input , $filterLevel )
    {
        if( $filterLevel > 8 )  // see Main.php for what the filter levels are supposed to do
        {
            $input = $this->removeHtmlComments($input);
            $input = $this->removeCStyleComments($input);
        }
        $input = $this->decodeHtmlEntities($input);
        $input = $this->addIfBeforeForeach($input);
        $input = $this->escapeShortTags($input);
        return $input;
    }

    /**
    *   see allPrefilters()
    *
    *   @see        allPrefilters()
    *   @version    02/05/22
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the actual input string, to which the filters will be applied
    *   @param      int     the filter level
    *   @return     string  the resulting string
    */
    function allPostfilters( $input , $filterLevel )
    {
        if( $filterLevel > 9 )  // see Main.php for what the filter levels are supposed to do
        {
            $input = $this->removeEmptyLines($input);
            $input = $this->trimLines($input);
            $input = $this->optimizeHtmlCode($input);
        }
        // this is default since, it enables you to also use 'switch case' blocks
        // if we wouldnt optimize the php here then there would be spaces printed between switch and case
        // which php doesnt allow! (only with autoBraces of course)
        //  i.e.    {switch($which)} ....those spaces here bother php...
        //              {case 'this':}
        //
        // and its better to optimize it always anyway!
        $input = $this->optimizePhpTags($input);
        return $input;
    }

    /**
    *   remove unnecessary php-tags, looks for ? > only spaces here < ?php  and merges them
    *   but watch out might be dangerous, since it also does that on < ?=
    *   better dont use it as it is if u are not 100% sure it will work (u were warned :-) )
    *
    *   @version    01/12/07
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function optimizePhpTags($input)
    {                             
        // replace ' } ? > <spaces> < ?php' by '}' AND
        // replace ' { ? > <spaces> < ?php' by '}'
        // since the big number of php tags only takes up a lot of parsing by php
        // NOTE: the space before the $1 is important, since PHP freaks out with '< ?php}' it needs '< ?php }'
        $input = preg_replace( '/\s*({|})(\s*)\?>(\s*)<\?php/U' , ' $1' , $input ); //"

        return $input;
    }

    /**
    *   removes HTML comments, use as preFilter
    *
    *   @version    01/12/07
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function removeHtmlComments($input)
    {
        return preg_replace('/<!--.*-->/Us','',$input); //worked until now, that i had nested html comments, not cool, but may happen when using {%include ...%}
// gotta live with that for now :-( see manual, recursive patterns
/*       return preg_replace('/<!--((?>[^(<!--)(-->)])|(?R))*-->/Usx','',$input);*/
    }

    /**
    *   removes C-style comments, use as preFilter
    *   but dont remove it if it is inside an html/xml tag <...>
    *   and dont remove it when there is a colon in front, like for a url
    *
    *   @version    01/12/07
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function removeCStyleComments($input)
    {
/*        $input = preg_replace( '/\/\/.+\n/U',"\n",$input);  // remove '//' but only on one line
removes <!DOCTYPE ... //W3C// > too :-(

        $input = preg_replace( '/(([^<].+)'.   // dont remove lines where double slashes are inside a html/xml tag <..>
                                '|([^:]))'.     // dont remove if there is a colon in front of the //, this is a url
                                '\/\/.+'.       // find the actual //
                                '(.+[^>])'.     // that checks for the closing >
                                '\n/U',"\n",$input);  // remove '//' but only on one line
removes the entire line if there is a // also only at the end

 doesnt work properly on this ...
<script>
    // fuck comment
    ftp://fuckyou.com
    http://fuckyou.com
</script>
http://fuck it
<a href="http://www.home.de">home</a>
http://dhsfsk

but this works:
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<a href="http://www.home.de">home</a>


*/                                          
        // remove // commments when they are at the beginning of the line or if there are only spaces before
        $input = preg_replace('~^\s*//.*$~Um','',$input);
                                           
        // watch out, that weird strings, like reg-exps dont fuck this up!
        $input = preg_replace('~/\*.+\*/~Us','',$input);  // remove /* */ on multiple lines too
        return $input;
    }

    /**
    *   removes empty lines, leave indention as they are (i need this filter in autoBrace as it is!!!)
    *
    *   @version    01/12/09
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function removeEmptyLines($input)
    {
        return preg_replace('/\n\s*\n/s',"\n",$input);
    }

    /**
    *   removes trailing spaces from lines
    *   use only as a POST-filter, if you are using 'autoBrace', since it needs the indention
    *
    *   @version    01/12/09
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function trimLines($input)
    {
        return preg_replace('/\n\s*/s',"\n",$input);
    }

    /**
    *   concatenates HTML tags which are spread over many lines
    *   removes spaces inbetween a > and a <
    *   removes new lines before > and />
    *   use only as a POST-filter, if you are using 'autoBrace', since it needs the indention
    *
    *   @version    01/12/16
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function optimizeHtmlCode($input)
    {
// all those in here are in use, but not tested all the way, i.e. what happens with compares in JS/PHP using < or >"

        // make lines at least 100 characters long
// dont know hoe yet...
//        $input = preg_replace('/((.*)\n(.*)){100,}/Us','$2 $3',$input);

        // removes new lines before > and />
        // this only works for tags where there are no PHP tags inside :-(
        $input = preg_replace('/\n([\/>])/U','$1',$input);

        // concatenates HTML tags which are spread over many lines,
        // and replace spaces which are before and after the new line by one space only
        // this only works for tags where there are no PHP tags inside :-(
        $input = preg_replace('/<(.*)\n(.*)>/U','<$1 $2>',$input);

        // remove only spaces between > and <
        // not the newlines, because this is supposed to be done before in this method
        $input = preg_replace('/>(\040)*</U','><',$input);

        return $input;
    }

    /**
    *   concatenates short lines, to longer once, reduce number of lines
    *   use only as a POST-filter, if you are using 'autoBrace', since it needs the indention
    *
    *   @version    01/12/16
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function concatLines($input)
    {
// i think i better dont write this filter, since it might be screwy for
// <pre> tags, JS and what every else ... think about it
// its not done yet anyway

/*        $lines = explode("\n",$input);

        $output = array();
        $curOutputLine = 0;
        foreach( $lines as $aLine)
        {
            // is the line at least 100 characters long?
            if( strlen($output[$curOutputLine])>100 )
            {
                $curOutputLine++;
                $output[$curOutputLine] = '';
            }

            $newLine = trim($aLine);
            if(  )

            $output[$curOutputLine].= $newLine;
        }

        return implode("\n",$output);
*/
    }

    /**
    *   this places a {if(sizeof($x))} before a {foreach($x as ..)}
    *   so i dont have to make this check in every place myself (since i mostly need the
    *   check anyway or PHP will freak if $x is an empty array)
    *   its just the same as "show a block only if it really contains data"
    *   use as a PRE filter
    *   out of this:       
    *
    *     {foreach($x as $oneX)}
    *         {$oneX}
    *     {else}
    *         no x's available
    *
    *   it makes
    *
    *     {if(sizeof($x))foreach($x as $oneX)}
    *         {$oneX}
    *     {else}
    *         no x's available
    *
    *   NOTE:   that you can also use {else} on a 'foreach', because it will then be used for the 'if'
    *   NOTE1:  this filter can only be applied if the delimiters are not set via the xml
    *           options inside the file, this doesnt work yet ... :-(
    *           since the xml data change the delimiter, which was passed to the constructor when
    *           making an instance of this class
    *
    *   @version    01/12/11
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function addIfBeforeForeach($input)
    {
        // this i.e. doesnt work with autoBraces off
        // {if(...):}  {foreach(...):}{endforeach}{else:}{endif}  ... :-(
        if ($this->getOption('autoBraces')==false) {
            return $input;
        } else {
            return preg_replace('/\n(\s*)'.             // get the indented spaces in $1, starting at the beginning of a line (^ didnt work here, at least not for me)
                                preg_quote($this->options['delimiter'][0]).
                                '\s*foreach\s*\('.    // check for the '{foreach(' and spaces anywhere inbetween, '\s*' does that
                                '\s*'.                  // spaces after the '(' might be allowed
                                '(\$.*)'.               // get the variable name in $2
                                '\s'.                   // and search for the next space, since that means the variable name ended here
                                '/U',                   // and be greedy ... dont know why but we need it (i dont understand what greedy means anyway)

                                "\n$1".
                                $this->options['delimiter'][0].
                                "if(is_array(@$2) && sizeof(@$2)>0)foreach($2 ",
                                $input);
        }
    }

    /**
    *
    *
    *   @version    01/12/11
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function convertEcho($input)
    {
// FIXXME problem here is if i want to replace {$x} but not {$x=7} with {echo $x}
// then i also have to check for $class->property, $a['array'] and so on ... dont know what to do now

// i wanted this filter so i dont always have to write { $x=7}, the space is what i need now, so it doesnt get an 'echo' inserted

        return preg_replace('/\{\$([a-zA-Z0-9_]*|'.
                            '[a-zA-Z0-9_]*->[a-zA-Z0-9_]*\(.*)\}/',"<?=\$$1 ?>",$input);
    }

    /**
    *   applies htmlentites to all the '{$' strings, so the
    *   printout will always be valid html
    *   do only use as a POST-filter!!
    *
    *   @version    02/05/13
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified input
    */
    function applyHtmlEntites($input)
    {
        return preg_replace( '/(<\?php=|<\?=)\$(.*)\?>/sU' , '<? echo htmlentities($$2)?>' , $input );   //"
    }

    /**
    *   converts &apos; &lt; &gt; &quot; etc. back to normal characters
    *   this is needed i.e. if you have an xml-file from Openoffice.org
    */
    function decodeHtmlEntities( $input )
    {   
        $open = preg_quote($this->getOption('delimiter',0));
        $close = preg_quote($this->getOption('delimiter',1));

        $transTable = get_html_translation_table(HTML_SPECIALCHARS);
        $transTable = array_flip($transTable);
        $transTable['&apos;'] = '\'';

        // for some reason this reg-exp doesnt find when a closing delimiter is
        // right before an opening delimiter i.e. '}{$x[&apos;' strange .. i think i have no idea of regexps :-)
        $regExp = "/[^\\\]$open.*[^\\\]$close/Usm";    //"
        // so we search just for that what the above dont find
        $regExp1 = "/$close$open.*[^\\\]$close/Usm";    //"

        // since all the below dont work we use this, this looks like it works :-)
        // it also shows how much time+code reg-exps can save (if i was able enough :-( )
        preg_match_all($regExp,$input,$res);
        $allReplaceables = $res[0];
        preg_match_all($regExp1,$input,$res1);
        $allReplaceables = array_merge($allReplaceables,$res1[0]);

        $allReplaceables = array_unique($allReplaceables);
        $replaced = array();
        foreach( $allReplaceables as $key=>$aReplaceable )
        {
            foreach( $transTable as $old=>$new )
            {
                if( strpos($aReplaceable,$old) !== false )
                {
                    // we write something in $replaced only when there is something to replace
                    // this way we will execute less reg-exps later
                    if( !isset($replaced[$key]) )
                        $replaced[$key] = $aReplaceable;
                    // we only modify the strings in $replaced, so we can simply use the regexp to replace the string later
                    // and we still have the origins in $allReplaceables
                    $replaced[$key] = str_replace($old,$new,$replaced[$key]);
                }
            }
        }

        foreach( $replaced as $key=>$aReplaced )
        {                                                                
            $input = preg_replace( '/'.preg_quote($allReplaceables[$key]).'/' , $aReplaced , $input );
        }


/*
    this doesnt work, see comment on stripslashes :-(

        $input = preg_replace(  //'/([^\\\]'.$open.'.*[^\\\]'.$close.')/Ue',
                                //'/([^\\\]{.*[^\\\]})/Use',
                                $regExp,
                                // its strange that i need stripslashes here, i think
                                // this is necessary because a string like 'foo["bar"]' is given as 'foo[\"bar\"]'
                                // i dont know why but that's how it is :-( took me some time :-)
                                // cant use stripslashes either, since that screws up \{ inside delimiters, and SimpleTag needs that
                                "strtr(stripslashes('$0'),\$transTable)",
                                $input );
*/
/*  this works on each entry in $transTable but still the problem with stripslashes as described above exists here too :-(
        foreach( $transTable as $old=>$new )
        {
            $regExp = "/[^\\\]$open.*[^\\\]$close/Use";    //"
            $input = preg_replace( $regExp , "str_replace(\$old,\$new,stripslashes('$0'))" , $input );
        }
*/

        return $input;
    }

        

    /**
    *   replace < ?xml by printing them via php, so short_open_tags can be left on
    *   since we can not turn it off anyway :-)
    *
    *   @version    02/11/05
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function escapeShortTags( $input )
    {
        if( ini_get('short_open_tag') )
        {             
            $input = preg_replace(  '/<\?xml/i',
                                    $this->getOption('delimiter',0).' echo "<?xml"'.$this->getOption('delimiter',1),
                                    $input);
        }
        return $input;
    }

}
?>

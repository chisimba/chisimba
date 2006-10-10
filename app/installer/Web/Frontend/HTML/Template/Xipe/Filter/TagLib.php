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
*   this file is intended to realize stuff like this
*   - add custom tags, which are no PHP (but replaced by it)!!! therefore they go like this: {%...%} where { and } are the delimiters
*       {%repeat $x times%}, {%repeat $x times, loopname=$loopCounter%}, replace by a simple for loop, if
*       loopname is given use this as the loop varibale name
*
*       {%copy block x here%} this replaces a defined block which is somewhere in the file
*           {%block x%}
*       {%include directory/file.tpl %}  this might define different blocks, which can be copied by using the above tag {%copy ...%}
*
*       {%strip whitespaces%}
*       {%strip%}
*
*       {%trim $x after 20 characters and add '...'%}
*       {%trim $x 20 '...'%}
*
*       {%trim $x by words after 20 characters and add '...'%}
*       {%trim $x by words 20 '...'%}
*
*   @package    HTML_Template_Xipe
*   @version    01/12/15
*/
class HTML_Template_Xipe_Filter_TagLib extends HTML_Template_Xipe_Options
{
// i need the method setOption, that's why i extend myPEAR_Common

    /**
    *   for passing values to the class, i.e. like the delimiters
    *   @access private
    *   @var    array   $options    the options for initializing the filter class
    */
    var $options = array(   'delimiter'     =>  array()      // first value of the array is the begin delimiter, second the end delimiter
                            ,'templateDir'   =>  ''          // we need the template dir for the include directive
                            ,'templateExtension'=>  ''
                            ,'macroExtension'=>     ''
                            ,'autoBraces'   =>      false
                        );

// remove the constructor one day, i feel that passing the delimiters to this class makes it all somehow unclean
// but therefore we have to move addIfBeforeForeach too, since it depends on having the delimiters

    /**
    *   @var    array   all the files that get included
    */
    var $_includedFiles = array();

    /**
    *   @var    array   all the macros that are defined
    */
    var $_macros = array();


    /**
    *   actually i made a constructor only to pass the delimiters to this class
    *   at a definite point
    *
    *   @version    01/12/15
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      array   $options    need to be given, use the options from your tempalte class
    */
    function HTML_Template_Xipe_Filter_TagLib($options=array())
    {
        if(sizeof($options))
            foreach( $options as $key=>$aOption )
                $this->setOption( $key , $aOption );
    }

    /**
    *   apply all filters available in this class
    *   thanks to hint from Alan Knowles
    *
    *   @version    02/05/22
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the actual input string, to which the filters will be applied
    *   @return     string  the resulting string
    */
    function allPrefilters( $input )
    {
        $input = $this->includeFile($input);
        $input = $this->block($input);
        // do block and include before other tags, so the other tags also work
        // when they were used in a block !!!
        $input = $this->macro($input);

        // do trim words before trim!! so trim doesnt catch the tag first :-)
        $input = $this->trimByWords($input);
        $input = $this->trim($input);
        $input = $this->repeat($input);

        $input = $this->applyHtmlEntites($input);

        $input = $this->loop($input);
        $input = $this->condition($input);
        $input = $this->end($input);

        return $input;
    }

    /**
    *   NOT IMPLEMENTED, AND I WONT
    *   removes spaces and new lines
    *   ACTUALLY i think this is unnecessary, simply use filters trimLines and optimizeHtml, this
    *   does everything, at least it works perfect for me
    *
    *   @version    01/12/15
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function strip( $input )
    {
        return $input;
    }

    /**
    *   {%repeat $x times%}, {%repeat $x times using $loopCounter%}, replace by a simple for loop, if
    *   a variable is given use this as the loop varibale name
    *
    *   @version    01/12/15
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function repeat( $input )
    {
        $openDel = preg_quote($this->getOption('delimiter',0));
        $closeDel = preg_quote($this->getOption('delimiter',1));
        $_openDel = $this->getOption('delimiter',0);
        $_closeDel = $this->getOption('delimiter',1);
        $autoBraces = $this->getOption('autoBraces');
        
        // if someone has autoBraces on and uses this.... well that would be a user-mistake, or a missing doc :-)
        $regExp = '/[^\\\]'.$openDel.'%\s*endrepeat\s*%'.$closeDel.'/Ui';
        $input = preg_replace( $regExp , $_openDel.($autoBraces?' \\} ':' endfor ').$_closeDel , $input );
                
        // find those repeats which dont have no variable that is given as the loop variable
        // we need to do this, since the next regExp needs this variable name, because
        // we can not use the $5 to check if it is given (down there in the second regExp)... bummer
        $counterName = '$_someUniqueVariableName';  // generate something here
        $input = preg_replace(  '/'.$openDel.
                                '%\s*repeat\s+([^\s%]+)([^\$]*)%'.$closeDel.
                                '/',

                                //"PRE-REPEAT:<br>1='$1'<br>2='$2'<br>3='$3'<br>4='$4'<br>5='$5'<br>" , // for testing
                                $_openDel.
                                "%repeat $1 $counterName%".
                                $_closeDel,

                                $input);

        $input = preg_replace(  '/\n(.*)'.          // save the indention in $1
                                $openDel.
                                '%\s*repeat\s+'.    // find begin delimiter repeat at least one space behind and variable spaces before
                                '([^\s]+)'.         // find everything until the next space, which is the count variable $2
                                '(([^\$%]*)?(\$[^\s]+)?)?'. // find the loop varibale name $5, a lot of stuff around it is optional (?)
                                                    // the variable name has to start with a $ and spaces are excluded, so we trim it too
                                '\s*%'.             // optional numbner of spaces before closing delimiter
                                $closeDel.
                                '/',

                                "\n$1".$_openDel.
                                "for($5=0;$5<$2;$5++)".($autoBraces?'':':').
                                $_closeDel,

                                //"REPEAT:<br>1='$1'<br>2='$2'<br>3='$3'<br>4='$4'<br>5='$5'<br>6='$6'<br>" , // for testing
                                $input);  // replace unnecessary spaces, so the next regexp is shorter and easier
                
        return $input;
        /* TESTS

        { $xx->methodCall=7}
        { $x=1}
        { $x1=1}
        { $x2=1}
        { $x_y=1}
        { $variableName_howEver_Long_it_mig111htBe=1}
        { $x4=1}

        <!--{%repeat $x->methodCall($easyVar)%} this works too, but i am too lazy to declare a class here-->
        {%repeat $xx->methodCall%}
            repeat 1
        <br>
        {%repeat sizeof($x)%}
            repeat 2
        <br>
        {%repeat $x times%}
            repeat 3
        <br>
        {%repeat $x1 times $y1%}
            repeat 4
        <br>
        {%    repeat     $x2    times    $y2   %}
            repeat 5
        <br>
        {%repeat $x_y times using $y%}
            repeat 6
        <br>
        {%repeat $variableName_howEver_Long_it_mig111htBe times with $y3%}
            repeat 7
        <br>
        {%repeat sizeof($x4) times $y4%}
            repeat 8
        <br>
        */
    }

    /**
    *   trims strings after X characters and adds a given string, if given
    *   use as PRE filter
    *   @todo   the length can not be a variable yet, do this someday
    *
    *   tested with
    *   {$x1='What a long string'}<br>
    *   {$x2='I am here '}<br>
    *   {$that->fuck='He ho'}<br>
    *   <br><br>
    *   1. {%trim $x1 after 5 characters and add "JUST simple ..."%}<br>
    *   2. {%trim $x2 3 "REPLACE with this"%}<br>
    *   3. {%   trim    $that->fuck fucking off unitl it dies after no more than  200 characters ,kajdfa sdkjas dlkjas dfkjasdf lksjd fksjdf lksjdf lksjd flkj l reaplce with ""%}
    *   <br>
    *   4. {%trim $x2 3%}<br>
    *   5. {%trim $x2  after 3 letters %}<br>
    *   6. {%trim   $x2  3   %}<br>
    *   7. {%trim $x2 to the length of 3%}<br>
    *
    *   @version    01/12/18
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @param      string  this is an extra string which can be added behind trim, is used i.e. for "trim words"
    *   @return     string  the modified template
    */
    function trim( $input , $extra='' )
    {
        $exp =  $this->options['delimiter'][0].
                'echo ((strlen($1) > $2))?(substr($1,0,$2)."$4"):$1'.
                $this->options['delimiter'][1];
        if ($extra == 'by words') {
            $exp =  $this->options['delimiter'][0].
                    'echo ((strlen($1) > $2))?(substr($1,0,(($2)-(strlen(strrchr(substr($1,0,$2),\' \')))))."$4"):$1'.
                    $this->options['delimiter'][1];

            $extra = '\s+by\s+words';
        }




        return preg_replace(    '/'.preg_quote($this->options['delimiter'][0]).
                                '%\s*trim\s+'.      // find at least one space behind and any number of spaces between % and trim
                                '([^\s]+)'.         // find all until the next space, that will be our variable name $1
                                $extra.
                                '[^\d]+'.           // find anything until a decimal number comes, at least one character
                                '(\d+)'.            // put the decimal number in $2
                                '(\s+.*"(.*)")?'.   // this is saucy, we only need the most inner pair of (),
                                                    // that will be our string we use to add at the end in case we trim it
                                                    // all those other () are only for making each block optional (?), esp. for test 5 to work
                                '\s*%'.preg_quote($this->options['delimiter'][1]).
                                                    // allow any kind of spaces before the end delimiter
                                '/i' ,              // search case insensitive

                                $exp,
                                //"TRIM:<br>1='$1'<br>2='$2'<br>3='$3'<br>4='$4'<br>5='$5'<br>" , // for testing

                                $input );
    }

    /**
    *   this trims strings but only after a space
    *   NOTE: be sure to put this filter before "trim"
    *
    *   @version    02/05/30
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @param      string  this is an extra string which can be added behind trim, is used i.e. for "trim words"
    *   @return     string  the modified template
    */
    function trimByWords( $input )
    {
        return $this->trim( $input , 'by words' );
    }

    /**
    *   {%include xxx.tagLib%}
    *
    *   @version    01/12/18
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function includeFile( $input )
    {
//print "<br>includeFile<br>";
//print_r($this->getOptions());
        $openDel = preg_quote($this->getOption('delimiter',0));
        $closeDel = preg_quote($this->getOption('delimiter',1));
        $_openDel = $this->getOption('delimiter',0);
        $_closeDel = $this->getOption('delimiter',1);

// FIXXXME discover all the functions that are used in the current file
// BUT only if it is no template, because then we assume it is a macro file!!! or smthg like this
// so only those functions are pasted inside the code!!! //"
// OTHER IDEA
// may be just compile the files that shall be included and replace the {%include by a php-include
                                
        // this var contains all the files this method has already included
        // we count them to prevent recursive inclusion of files!
        $justIncluded = array();

        // $parseAgain will be true if another template was included
        // if the last one is done $parseAgain will be set to false and the while loop quits
        $parseAgain = true;
        while ($parseAgain) {
            // if there are no more {%include%} tags in the file content, then we dont have to
            // loop again. This is to handle included files that have includes inside.
            if (!preg_match_all( '/'.$openDel.'%\s*include\s+(.+)\s*%'.$closeDel.'/U',$input,$includes)) {
                $parseAgain = false;
            } else {
    //print_r($includes);
                if (sizeof($includes[1])) {
                    foreach ($includes[1] as $index=>$aInclude) {
                        // get the relative path to templateDir or absolute if given
        // FIXXME unix specific!!!!
                        if ($aInclude[0] != '/') {          // add trailing slash if missing
                            $_aInclude = '/'.$aInclude;
                        }
                        $fileToInclude = $this->options['templateDir'].$_aInclude;
                        if (in_array($fileToInclude,$justIncluded)) {
//FIXXXME i need a nicer error handling way
// somehow i would need to make the Xipe-Error method from the Main.php to be called statically!!!
print "<b>Xipe-Error</b><br>The file <b>{$_aInclude}</b> is included recursively, this is not possible!<br>";
                            break(2);
                        }
                        $justIncluded[] = $fileToInclude;

                        // do only include a file that really exists, otherwise the tag also stays there, so the programmer removes it
                        // do also search for the file in the include path, but as the second option only!
                        if ($fileContent = @file_get_contents($fileToInclude)) {
                            $contentFile = $fileToInclude;
                        } else {
                            if ($fileContent = @file_get_contents($aInclude,true)) {
                                $contentFile = $aInclude;
                            }
                        }

                        if ($fileContent) {
                            $pathInfo = pathinfo($contentFile);

                            if ($this->getOption('macroExtension')==$pathInfo['extension']) {
                                // do only include the files content if we didnt include it yet
                                // just like 'include_once' only that it does it by default :-)
                                // this only works if we are only using one instance of the filter, which is not the case
                                // since every file might have different options, i.e. delimiters, so i changed
                                // it to make a new instance for every file, which means this has almost no effect
                                if (!in_array($contentFile,$this->_includedFiles)) {
            //print "including: $contentFile<br>";
                                    $this->_includedFiles[] = $contentFile;
                                    // put an if around the entire macro file, so it wont even be parsed
                                    // if it is already once in the code, this takes care of not multiple
                                    // times defining functions (macros in this case)
                                    // it also works if you compile multiple files with different instances of this filter
                                    // since php checks the variable $___HTML_Template_Xipe_TagLib_includedFile given here
                                    // use isset to prevent E_ALL-warning
                                    $testVar = "\$___HTML_Template_Xipe_TagLib_includedFile['$fileToInclude']";
                                    $fileContent =  "$_openDel if(!isset($testVar) || !$testVar)\\\{ $_closeDel".
                                                    $fileContent.
                                                    $_openDel." \$___HTML_Template_Xipe_TagLib_includedFile['$fileToInclude']=true;\\\}".$_closeDel;
                                } else {
            //print "already included: $contentFile<br>";
                                    $fileContent = '';
                                }
                            }

                            // replace the string from $includes[0] with the file
                            $input = preg_replace( '/'.preg_quote($includes[0][$index],'/').'/' , $fileContent , $input );
                        }
                    }
                }
            }   // end of if-any include-tags found
        }   // end of while
        return $input;
    }

    /**
    *   parses {%block xxx%} tags
    *   DEPRECATED, use macro instead!!!!
    *
    *   @version    01/12/18
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function block( $input )
    {
        // do somehow add the block-end tag first, use autoBraces, but needs modification first
        // for now you need to write the {%/block%} end tag
        $regExpToFindBlocks = '/{%\s*block\s+(.+)\s*%}.*{%\/\s*block\s*%}/Us';
        // retreive the block names only, since a block might contain another block
        // by not getting the block content here we can also put blocks in blocks...
        preg_match_all( $regExpToFindBlocks , $input , $blocks );

        if( sizeof($blocks[0]))
        {
            foreach( $blocks[1] as $index=>$aBlockName )
            {
                // we trim the block name here, so we only get the real block name
                // and we dont have to add this 'no spaces' in the regExp
                $realBlockName = trim($aBlockName);

                // !!!
                // get the block content now, because it might containes another copy-tag !!!
                // which was replaced by the according block, write it in $blockContent
                $blockRegExp = '/{%\s*block\s+'.$aBlockName.'\s*%}(.*){%\/\s*block\s*%}/Us';
                preg_match( $blockRegExp , $input , $blockContent );
                // and replace the block definitions with nothing
                $input = preg_replace( $blockRegExp , '' , $input );

                $this->blocks[$realBlockName] = $blockContent[1];

                // we need to get the number of spaces before each '{%copy' to maintain indention
                preg_match_all(  '/\n(\s*){%\s*copy\s+block\s+'.$realBlockName.'.*%}/' , $input , $copyTags );

                // now we need to go thru every '{%copy' tag that has to be replaced and get its indention
                // to keep it in front, this adds the indention that is given in the block too !!!

                if(sizeof($copyTags[0]))
                foreach( $copyTags[0] as $cpIndex=>$aCopyTag )
                {
                    $indentedBlockContent = preg_replace( '/\n/' , "\n".$copyTags[1][$cpIndex] , $blockContent[1] );
                    $input = preg_replace( '/'.$copyTags[0][$cpIndex].'/' , $indentedBlockContent , $input );
                }
            }
        }

        // go thru all blocks to replace copy-tags that are still left
        // in the first foreach we had only replaced copy tags which use blocks that
        // are defined in the same file
        if( isset($this->blocks) && sizeof($this->blocks) )
        foreach( $this->blocks as $realBlockName=>$blockContent )
        {
            // we need to get the number of spaces before each '{%copy' to maintain indention
            preg_match_all(  '/\n(\s*){%\s*copy\s+block\s+'.$realBlockName.'.*%}/' , $input , $copyTags );
            // now we need to go thru every '{%copy' tag that has to be replaced and get its indention
            // to keep it in front, this adds the indention that is given in the block too !!!
            if(sizeof($copyTags[0]))
            foreach( $copyTags[0] as $cpIndex=>$aCopyTag )
            {
                $indentedBlockContent = preg_replace( '/\n/' , "\n".$copyTags[1][$cpIndex] , $blockContent );
                $input = preg_replace( '/'.$copyTags[0][$cpIndex].'/' , $indentedBlockContent , $input );
            }
        }

        // we have replaced all that was to replace, remove {%copy-tags
        // that were not replaced by anything
        $input = preg_replace(  '/\n(\s*){%\s*copy\s+block\s+.*%}/' , '' , $input );

        return $input;

        /*
        tested with

        {% block x %}<br>
            hi i am your first block
            even a line break i
            contain
        {%/block %}

        {%block this_block%}
            this is this block INSERTED<br>
        {%/block %}

        1.<br>
        {%copy block this_block here %}
        <br><br>
        2.<br>
        {%    copy     block     x      here %}
        <br><br>
        3.<br>
        {%   copy     block    this_block%}
        <br><br>
        4.<br>
        {%copy block     this_block   %}
        <br><br>

        */
    }

    /**
    *   applies htmlentites to all the '{%$xxxx%}' strings, so the
    *   printout will always be valid html
    *
    *   @version    02/05/13
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified input
    */
    function applyHtmlEntites($input)
    {
        $input = preg_replace(  '/'.preg_quote($this->options['delimiter'][0]).
                                '%\$(.*)%'.preg_quote($this->options['delimiter'][1]).
                                '/U' ,
                                '<?=htmlentities($$1)?>' ,
                                $input );
        return $input;
    }

    /**
    *
    *
    *   @version    02/06/21
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified input"
    */
    function macro( $input )
    {
        $openDel = preg_quote($this->getOption('delimiter',0));
        $closeDel = preg_quote($this->getOption('delimiter',1));
        $_openDel = $this->getOption('delimiter',0);
        $_closeDel = $this->getOption('delimiter',1);

        // replace 'macro' with 'function'
        $regExp = '/'.$openDel.'%\s*(macro|function)\s+(.*)%'.$closeDel.'/Usi';
        
        // if autoBraces is off, we need to put the opening-brace here :-)
        if ($this->getOption('autoBraces')) {
            $input = preg_replace( $regExp , $_openDel.'function $2'.$_closeDel , $input );
        } else {
            $input = preg_replace( $regExp , $_openDel.'function $2 \\{ '.$_closeDel , $input );
        }

        // replace {%macroName()%} with {macroName()}
        $regExp = '/'.$openDel.'\s*function\s+(.*)\(.*\)\s*(\\\{)?\s*'.$closeDel.'/Usi';
        preg_match_all( $regExp , $input , $macroCalls );

        // merge the macros found now with the macros already found
        // do this because we might have some macros which are not defined in the current file
        // but we assume, that all the files that are being processed by the same instance of this filter
        // are merged to one big php-file, so the macro will be defined and available!
        $this->_macros = array_unique(array_merge($this->_macros,$macroCalls[1]));

        if (sizeof($this->_macros)) {
            foreach ($this->_macros as $aMacroCall) {
                $regExp = '/'.$openDel.'%\s*'.trim($aMacroCall).'\s*(\(.*\))%'.$closeDel.'/Ui';
                $input = preg_replace( $regExp , $_openDel.$aMacroCall.'$1'.$_closeDel , $input );
            }
        }

        // if someone has autoBraces on and uses this.... well that would be a user-mistake, or a missing doc :-)
        $regExp = '/[^\\\]'.$openDel.'%\s*endmacro\s*%'.$closeDel.'/Ui';
        $input = preg_replace( $regExp , $_openDel.' \\} '.$_closeDel , $input );

        return $input;
    }

    function loop( $input )
    {
        $input = $this->_replaceName( $input , 'while' );
        $input = $this->_replaceName( $input , 'for' );
        return $this->_replaceName( $input , 'foreach' );
    }

    function condition( $input )
    {
        return $this->_replaceName( $input , 'if' );
    }

    function _replaceName( $input , $name )
    {
        $openBrace = '{';
        if( $this->getOption('delimiter',0) == '{' )
            $openBrace = '\{';

        $input = preg_replace(  '/'.preg_quote($this->getOption('delimiter',0)).
                                '%\s*'.$name.'\s*\((.*)\)\s*%'.preg_quote($this->getOption('delimiter',1)).
                                '/Ui' ,
                                "<?php $name($1) $openBrace ?>" ,
                                $input );
        return $input;
    }

    function end( $input )
    {
        $closeBrace = '}';
        if( $this->getOption('delimiter',0) == '{' )
            $closeBrace = '\}';

        $input = preg_replace(  '/'.preg_quote($this->getOption('delimiter',0)).
                                '%\s*end\s*%'.preg_quote($this->getOption('delimiter',1)).
                                '/Umi' ,
                                "<?php $closeBrace ?>" ,
                                $input );
        return $input;
    }
}

?>

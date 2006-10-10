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
*   translation filters and helpers
*
*   @package    HTML_Template_Xipe
*   @access     public
*   @version    02/04/14
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class HTML_Template_Xipe_Filter_Translate extends HTML_Template_Xipe_Options
{

    /**
    *   apply a function/method to each output which translates the string, so i dont have to
    *   do it by hand every time
    *   use only as POST-filter, since it looks for '< ? =' tags
    *
    *   NOTE:   its quite couragous using this filter, since it might also translate usernames or other stuff
    *           that is dynamically generated, and if it also is in the translate-table then the page may become funny
    *
    *   @version    02/01/08
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @param      string  $functionName   the function to put around a string that is printed
    *   @param      array   $possibleMarkUpDelimiters   delimiters that are around a text that should be translated, see Language_Translate
    *   @see        $Language_Translate::possibleMarkUpDelimiters
    *   @return     string  the modified template
    */
    function applyTranslateFunction( $input , $functionName , $possibleMarkUpDelimiters )
    {
        if( sizeof($possibleMarkUpDelimiters) )
        foreach( $possibleMarkUpDelimiters as $begin=>$end )  // go thru all the delimiters and try to translate the strings
        {
// FIXXME the [ ^ ? > ] makes it impossible to translate the following string
// automatically: < ?=$key? > --- < ?=$aLoop? >
// if i had left a "." instead there it would result in: < ?=translateMathod($key? > --- < ?=$aLoop)? >
// but now we have the problem that ONLY to stuff inside the $possibleMarkUpDelimiters
// the translate function is applied, but we need the $possibleMarkUpDelimiters since
// we dont want to translate every < ?=.. tag, since those might also just be formatting
// things or in a style sheet simply a path or whatsoever, so $possibleMarkUpDelimiters IS DEFINITELY NEEDED
// but must become better

            $input = preg_replace(
                                    // $ [^ ? >]    takes care of only applying the method to the proper block,
                                    //              i think there is some reg-exp modifier for that too, but dont know yet
                                    // (->)?        takes care of class operators to be included in the translation
                                    '/('.$begin.'<\?php\secho\s)(\$([^?>](->)?)*)(\?>'.$end.')/i' ,
                                    "$1$functionName($2)$5" ,
                                    $input );
        }
        return $input;

/*
    TEST CASES THAT PASSED: "
    1. the problem here was the class-operator '->', since the '>' is also in the possibleMarkUpDelimiter
    <td class="listContent">< ?=$language->getName($aBookmark['language'])? ></td>

*/
    }

    /**
    *   this function will simply search variables that have the additional prefix $markString
    *   and applies the translate function to them, and only to them
    *   example:
    *       - your variable that shall be translated is $foo
    *       - your $functionName is 'translateMe', the function that translates the string
    *       - you have set the mark string to 'T_' (same as default)
    *       now all the varibales in the code, where you have written
    *       $T_foo instead of $foo
    *       will be replaced by 'translateMe($foo)'
    *       so that {$T_foo} will finally become '<? php echo translateMe($foo) ? >'
    *   that's all it does. note that the variable name will be reset to what
    *   it actually shall be, the 'T_' is only kind of a mark, which indicates
    *   translate this variable. There doesnt have to exist a variable with this name
    *
    *   @param
    *   @param
    *   @param  string  this is a short string, that marks how a variable name
    *                   has to start, if this is found the value will be translated
    *                   otherwise it will be left alone
    *
    */
    function translateMarkedOnly( $input , $functionName , $markString='T_' )
    {
//        $regExp = '/\\$'.preg_quote($markString).'([a-z0-9_\->]*)/i';
        $regExp = '/\\$'.preg_quote($markString).'(.*)\\?>/Ui';

        $input = preg_replace( $regExp , "$functionName($$1) ?>" , $input );

        return $input;
    }

}
?>

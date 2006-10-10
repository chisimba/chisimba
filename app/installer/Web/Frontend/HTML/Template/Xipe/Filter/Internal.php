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
require_once 'HTML/Template/Xipe/Filter/Basic.php';

/**
*   the internal filter(s) i use and Xipe needs
*
*   @package    HTML_Template_Xipe
*   @access     public
*   @version    01/12/10
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class HTML_Template_Xipe_Filter_Internal extends HTML_Template_Xipe_Options
{
// i need the method setOption, that's why i extend myPEAR_Common

    /**
    *   for passing values to the class, i.e. like the delimiters
    *   @access private
    *   @var    array   $options    the options for initializing the filter class
    */
    var $options = array(   'delimiter'     => array());    // see HTML_Template_Xipe

    /**
    *   actually i made a constructor only to pass the delimiters to this class
    *   at a definite point
    *
    *   @version    01/12/10
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      array   $options    need to be given, use the options from your tempalte class
    */
/*    function HTML_Template_Xipe_Filter_Internal($options)
    {
        foreach( $options as $key=>$aOption )
            $this->setOption( $key , $aOption );
    }
*/
    /**
    *   replace all delimiters with the right php tags
    *
    *   @version    01/12/10
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input  the original template code
    *   @return     string  the modified template
    */
    function makePhpTags($input)
    {
        $begin = $this->getOption('delimiter',0);
        $end = $this->getOption('delimiter',1);

        // quote the strings
        $qBegin = preg_quote($begin);
        $qEnd = preg_quote($end);

        // replace all varibales with $GLOBALS around it, so we can use the method "show"
// dont replace $GLOBALS
//       $input = preg_replace(  #'/\{(.*)\$([^(GLOBALS)]+)([^a-zA-Z0-9_].*)\}/U' , works fine but doesnt replace multiple $x inside one php tag
//                                '/\{(.*)\$([^(GLOBALS)]+)([^a-zA-Z0-9_].*)\}/U' ,
//                                '{$1$GLOBALS[\'$3\']$4 /*...1=$1...2=$2...3=$3....4=$4....5=$5.... */}' ,
//                                $input );

        // replace '{' by < ?php  ('{' is the delimiter)
        $regExp = "/(^|[^\\\])$qBegin([^%])/Um";    // modifier m makes '{foo}{bar}' on one line work too
        $input = preg_replace(  $regExp ,
                                '$1<?php $2' ,
                                $input );
        // replace } by ? >  (} is the delimiter)
        $regExp = "/([^\\\%])$qEnd/Um";    // modifier m makes '{foo}{bar}' on one line work too
        $input = preg_replace(  $regExp ,
                                '$1 ?>' ,
                                $input );
        // replace '< ?php $' by '< ?php echo @$' optionally a @ before a $
        $regExp = "/<\?php\s(@)?\\$/Um";
        $input = preg_replace(  $regExp ,
                                '<?php echo $1$' ,
                                $input );
        // replace all php tags, where there is only one space after it
        // by echoing the stuff that follows, but not if a control structure follows
        // actually we only do this for CONSTANTS now. And they have to be capital, at least the first letter!!!
        $regExp = "/<\?php\s([A-Z]+)/Um";
        $input = preg_replace(  $regExp ,
                                '<?php echo $1' ,
                                $input );
         

        //
        // replace escaped delimiters like \{ or \}
        //
        // preg_quote the entire string, to be sure that reg-expr. characters are quoted too
        // i.e. if $begin is '[' it is escaped and so is the '\\' too, well it works like this, it didnt properly before :-)
        $regExp = preg_quote('/\\'.$begin.'/');
        $input = preg_replace( $regExp , $begin , $input );

        $regExp = preg_quote('/\\'.$end.'/');
        $input = preg_replace( $regExp , $end , $input );

        return $input;
    }

    /**
    *   to leave out those nerv wrecking {{ } }} things we check indention (like python does)
    *   so that if the following comes
    *       {{ if(somecheck) }}
    *           {{$someVar}}
    *       <normal> html </normal> again
    *   we can automatically set the { } around this indented part, this would make
    *   code readable and force users to write nice code :-) i like that.... :-)
    *   NOTE:   for now the indention only works if the starting delimiter and end delimiter is on the same line
    *           and only one time each
    *           i guess tabs dont work either, but i didnt try it
    *
    *   @access     private
    *   @version    01/12/07
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input    the entire file content
    *   @return     string  modified string
    */
    function autoBraces( $input )
    {

// FIXXXXME  the following fucks it up, because of the indented next line, but the echo doesnt need a {
// see proxy/modules/convert/templates/phpManual/default.tpl
// maybe check for words which require braces, like if, foreach and so on ... but this limits it again :-/ and if we
// forgot one its fucked
// and what it does wrong too, is it puts the { behind: $aBookmark['url'], see compiled template
//                         <a href="{$aBookmark['url']}" target="main" title="{echo htmlentities($aBookmark['selection'])}">
//                            {$aBookmark['title']}
//                                {if($aBookmark[$orderBy] && $orderBy!='title')}
//                                    ({$aBookmark[$orderBy]})
//                        </a>
// this fails too, though it is not correct, since the second line doesnt need to be indented
// {%trim $x 20 ''%}
//   {%trim $x 20 ''%}
//
// ths is also buggy the '{%' is translated to be '< ? php }' even though the '>' is before, the '}' should go before the '>'
//     {if($aFolder['id'] == $fid)}
//        class="curFolder"
//     >{%trim $aFolder['extName'] after 40 "..."%}


        $openDel = $this->getOption('delimiter',0);
        $closeDel = $this->getOption('delimiter',1);

        // remove empty lines, so we can check if the _next_ line is indented (if you buy 'autoBraces' you get that too no way around :-)  )
        // this way we dont run into problems if the line after an {if(...)} is empty but the next
        // indented (as i had it, to make the code more readable)
        $preFilter = new HTML_Template_Xipe_Filter_Basic;
        $input = call_user_func( array($preFilter,'removeEmptyLines') , $input );

        // this ONLY works if '{$' is not replaced by '< ?php xxx'  but by '< ?=' as it is now
        // so we gotta fix that, because short tags prevent from proper use for XML :-)
        $begin = '<?php';
        $end = '?>';
        $file = explode("\n",$input);
        // this variable we use like a stack to put the open indentions on it, we fill this stack from the
        // bottom, means index 0 always contains the last indention, since we are working them off this way
        $openIndentions = array();

        foreach( $file as $curLineIndex=>$aLine )
        {
            // count number of spaces at the beginning of this, prev and next line
            $numSpaces = strlen($aLine) - strlen(ltrim($aLine));

            $nextLine = '';
            if( sizeof($file) > $curLineIndex+1 )   // check the array boundary, since php4.1 and a stricter php.ini it throws a warning
                $nextLine = $file[$curLineIndex+1];
            $numSpacesNextLine = strlen($nextLine) - strlen(ltrim($nextLine));

            $numSpacesPrevLine = 0;
            if( $curLineIndex>0 )
            {
                $prevLine = $file[$curLineIndex-1];
                $numSpacesPrevLine = strlen($prevLine) - strlen(ltrim($prevLine));
            }

            // are there any open indentions that need the closing brace?
            if( sizeof($openIndentions) &&
                $numSpaces <= $openIndentions[0]['numSpaces']
              )
            {
//print("end indention ".htmlentities($file[$curLineIndex]).'<br>');
                do
                {
                    $spaces = '';
                    for( $i=0 ; $i<$openIndentions[0]['numSpaces'] ; $i++ )       // make the code look nice, indent the closing brace
                        $spaces.= ' ';

                    // are delimiters at the begining of this line!!!? then we only add our braces before them
                    // dont add the closing brace after a piece of html, so that this works too:
                    //  {if(...)}
                    //      test
                    //  <br>{somePhpCode}       // dont add the brace in the {somePhpCode} but before the <br> otherwise the logic would be false!
                    if( preg_match( '/^\s*'.preg_quote($begin).'.+[^echo].*'.preg_quote($end).'/U' , $file[$curLineIndex] ) )
                    {
//print "line = $curLineIndex --- ".htmlentities($file[$curLineIndex]);
                        // this also fixes the problem i had with the 'else', which doesnt except closing the php-tag before or after it
                        // since we are always writing the braces inside the existing php-tag now :-)
                        // replace only the first $begin with "$begin }" and dont forget to put the spaces back!! ($1)
                        // and there needs to be a space between a opening php-tag and a closing curly brace, otherwise php brings an error!
                        $file[$curLineIndex] = preg_replace( '/^(\s*)'.preg_quote($begin).'/' , "$1$begin }" , $file[$curLineIndex] );
                    }
                    else    // no delimiters on this line, so we new once
                    {
//print "else line = $curLineIndex --- ".htmlentities($file[$curLineIndex]);
                        $file[$curLineIndex] = $spaces.$begin.' }'.$end."\n".$file[$curLineIndex];
                    }
//print " ....... after = $curLineIndex --- ".htmlentities($file[$curLineIndex])."<br>";

                    array_shift( $openIndentions );
                    $openIndentions_0_numSpaces = 0;
                    if( isset($openIndentions[0]['numSpaces']) )
                        $openIndentions_0_numSpaces = $openIndentions[0]['numSpaces'];
                }
                // shift out all the elements of the array until we have reached the indention that has the number of
                // spaces as the indention in index 0, mostly its the first one, but in case
                // you have a 'for' after an 'if' there are 2 indentions which have to be closed (on one line)
                // i.e.
                //  {if(sizeof($array))}
                //      {foreach($array as $x)}
                //          print something here
                //  <next line of html> or other code       ... above this line 2 indentions need to be closed
                while( $numSpaces <= $openIndentions_0_numSpaces && sizeof($openIndentions)>0 );
            }

            //
            //  are there delimiters on this line?
            //
            // use the line read from the file, not the modified line which is in $file[$curLineIndex]
            // because there might be a php-tag added, and that would make us find a possible indention here, even
            // though the tag might had just been generated before to close a brace - trust me i found it while debugging
            // example code that wouldnt work:
            // line 1     {if($aWritableFolder['uid']!=$user->uid)}
            // line 2         <option> # # # {$users[$aWritableFolder['uid']]['username']} # # # </option>
            // line 3     <option value="{$aWritableFolder['id']}">
            // the parser would now have added a php tag with closing braces before line 3
            // and the following if _would_ find it, if we checked the modified line, as i used to, bummer
            if( preg_match( '/^\s*'.preg_quote($begin).'.+[^echo].*'.preg_quote($end).'/U' , $aLine ) )
            {
                // is the next line indented? if so we save this indention to remember to set the closing braces
                if( $numSpacesNextLine > $numSpaces )
                {
                    array_unshift( $openIndentions , array('numSpaces'=>$numSpaces,'lineNumber'=>$curLineIndex) );
                    // write the opening brace in the
                    $file[$curLineIndex] = str_replace( $end , ' { '.$end , $file[$curLineIndex] );
//print("started indention ".htmlentities($file[$curLineIndex]).'<br>');
                }
            }

        }

        if( sizeof($openIndentions) )
        {
            $file[$curLineIndex].= "\n<?php ";
            foreach( $openIndentions as $x )
            {
                $file[$curLineIndex].='}';
            }
            $file[$curLineIndex].= ' ?>';
        }

        return implode("\n",$file);
    }

    /**
    *   removes the xml-config string
    *
    *   @access     public
    *   @version    02/05/25
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string      the input string, template file
    *   @return     string      the modified string
    */
    function removeXmlConfigString( $input )
    {
        return preg_replace( '/<html_template_xipe>.*<\/html_template_xipe>/Uis' , '' , $input );
    }

}
?>

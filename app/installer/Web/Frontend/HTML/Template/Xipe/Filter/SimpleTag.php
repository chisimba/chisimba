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
*
*
*   @package    HTML_Template_Xipe
*   @access     public
*   @version    02/09/21
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class HTML_Template_Xipe_Filter_SimpleTag extends HTML_Template_Xipe_Options
{

    /**
    *   accept the given options, to be set in this class, since we need them
    */
    var $options = array(   'delimiter' =>  array() );

    /**
    *   this class is a language filter, so apply it before all
    *   others, therefore we return 'true' here!
    */
    function isLanguageFilter()
    {
        return true;
    }

    /**
    *   we dont need the filter level here since we handle basic tpl-language
    *   constructs
    */
    function allPrefilters( $input , $filterLevel )
    {
        $input = $this->controlStructure($input);
        return $input;
    }

    function controlStructure( $input )
    {
        $open = $this->getOption('delimiter',0);
        $close = $this->getOption('delimiter',1);

        // now the shitty way ...
        $regExp = '/#(foreach|if)(\(([^()]*(\([^()]*\))*)*\))/Uim';
        $input = preg_replace( $regExp , $open.'$1$2\\{'.$close , $input );

/*  i dont get it to run with the recursive reg-exp :-(

        // '#foreach(anything)'     =>  {foreach(anything)\{}
        // '#if(anything)'          =>  {if(anything)\{}
        $input = preg_replace(  //'/#(foreach|if)\((.*)\)/Uim',
                                '/#(if|foreach)\s*(\(((? >[^()]+))\)|(?R))* /i',
                                "0=$0\r\n1=$1\r\n2=$2\r\n\r\nfinal=$1$2\r\n",   //"
                                //$open.'$1$2\\{'.$close,       //"
                                $input);


*/
        // this works for as many nested () inside one another
// this works :-) i dont exactly know why but it seems to do the job
//print preg_replace( '/#(if|foreach)(\(((? >[^()]+))\)|(?R))*/' , '$0<br>1=$1<br>2=$2' , '#if(a+b-c+(x-5*3))' );

//        $input = preg_replace( '/\((((? >[^()]+)|(?R))*)\)/mi' , '_1=$1_2=$2_' , $input );
//        $input = preg_replace( '/#foreach(\(((?'.'>[^()]+)|(?R))\)*)/m' , $open.'foreach$1'.$close , $input );

        //$input = preg_replace( '/#foreach\(((? >[^()]+)|(?R))*\)/im' , '_foreach_' , $input );


        // '#end'    =>  {\}}
        $input = preg_replace(  '/#end/',
                                $open.' \\}'.$close,        //"
                                $input);

        return $input;
    }

    /* TODO
        - we need addIfBeforeForeach, since the Basic-Filter that does that depends on autoBraces=true and we cant do that here
    */

}
?>

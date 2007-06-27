<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4ÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊ|
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP GroupÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊ|
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,ÊÊÊÊÊÊÊ|
// | that is bundled with this package in the file LICENSE, and isÊÊÊÊÊÊÊÊ|
// | available at through the world-wide-web atÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊ|
// | http://www.php.net/license/2_02.txt.ÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊ|
// | If you did not receive a copy of the PHP license and are unable toÊÊÊ|
// | obtain it through the world-wide-web, please send a note toÊÊÊÊÊÊÊÊÊÊ|
// | license@php.net so we can mail you a copy immediately.ÊÊÊÊÊÊÊÊÊÊÊÊÊÊÊ|
// +----------------------------------------------------------------------+
// | Author: George Schlossnagle <george@omniti.com>                      | 
// +----------------------------------------------------------------------+
//
// $Id$

/*
 * Text_Word calculates the number of syllables in a word, based off of
 * the number of contiguous vowel groupings in the word and applying
 * matches to detect special cases.

 * require_once 'Text/Word.php'
 * $word = new Text_Word('word');
 * $word->numSyllables();  // returns 1
 *
 * @package Text_Word
 * @author George Schlossnagle <george@omniti.com>
 */


class Text_Word {
    /* The word 
     *
     * @var string
     * @access public
     */
    var $word;

    /* The number of syllables.  This is internal, the value should be 
     * accessed through the accessor.
     *
     * @var number
     * @access protected
     */
    var $_numSyllables = 0;

    /* The special cases of fragments which detect as 1 but should be 2
     * syllables.
     *
     * @var array
     * @access static protected
     */
    var $doubleSyllables = array('/\wlien/', // alien but not lien
                                 '/bl$/',   // syllable
                                 '/io/',    // biography
                                 );

    /* The special cases of fragments which detect as 2 but should be 1
     * syllables.
     *
     * @var array
     * @access static protected
     */
    var $silentSyllables = array('/\wely$/',    // absolutely but not ely
                                 '/\wion/',
                                 '/iou/',
                                );
    
    /*
     * Constructs a word by name.
     *
     * @param  string
     * @access public
     */
    function Text_Word($name = '') 
    {
        $this->word = $name;
    }

    /*
     * Helper function, canocalizes the word.
     *
     * @param  string
     * @access protected
     */
    function _mungeWord($scratch) 
    {
        // conanonicalize the word
        $scratch = strtolower($scratch);
        // trailings e's are almost always silent in English
        // so remove them
        $scratch = preg_replace("/e$/", "", $scratch);
        return $scratch;
    }

    /*
     * Helper function, counts syllable exceptions
     *
     * @param  string
     * @access protected
     */
    function _countSpecialSyllables($scratch) 
    {
        $mod = 0;
        // decrement our syllable count for words that contain
        // 'silent' syllables, e.g. ely in 'abosultely'
        foreach( $this->silentSyllables as $pat ) {
            if(preg_match($pat, $scratch)) {
                $mod--;
            }
        }
        // increment syllable count for certain conjoined vowel
        // patterns which produce two syllables e.g.
        // 'io' in 'biology'
        foreach( $this->doubleSyllables as $pat ) {
            if(preg_match($pat, $scratch)) {
                $mod++;
            }
        }
        return $mod;
    }

    /*
     * Returns the number of syllables.  Caches the value in the object.
     *
     * @access public
     */
    function numSyllables() 
    {
        // cache the value in this object
        if($this->_numSyllables) {
            return $this->_numSyllables;
        }
        $scratch = $this->_mungeWord($this->word);
        // Split the word on the vowels.  a e i o u, and for us always y
        $fragments = preg_split("/[^aeiouy]+/", $scratch);

        // remove null elements at the head and tail of 
        // $fragments
        if(!$fragments[0]) {
            array_shift($fragments);
        }
        if(!$fragments[count($fragments) - 1]) {
            array_pop($fragments);
        }

        // modify our syllable count for special cases
        $this->_numSyllables += $this->_countSpecialSyllables($scratch);
        // now count our syllable
        if(count($fragments)) {
            $this->_numSyllables += count($fragments);
        }
        else {
            $this->_numSyllables = 1;
        }
        return $this->_numSyllables;
    }
}
?>

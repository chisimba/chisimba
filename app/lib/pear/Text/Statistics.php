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
 * Text_Statistics calculates some basic readability metrics on a 
 * block of text.  The number of words, the number of sentences,
 * and the number of total syllables is counted.  These statistics
 * can be used to calculate the Flesch score for a sentence, which
 * is  a number (usually between 0 and 100) that represents the 
 * readability of the text.  A basic breakdown of scores is:
 *
 * 90 to 100  5th grade
 * 80 to 90   6th grade
 * 70 to 80   7th grade
 * 60 to 70   8th and 9th grade
 * 50 to 60   10th to 12th grade (high school)
 * 30 to 50   college
 * 0 to 30    college graduate
 *
 * More info can be read up on at 
 * http://www.mang.canterbury.ac.nz/courseinfo/AcademicWriting/Flesch.htm
 *
 * require 'Text/Statistics.php';
 * $block = Text_Statistics($sometext);
 * $block->flesch; // returns flesch score for $sometext
 *
 * see the unit tests for additional examples.
 *
 * @package Text_Statistics
 * @author  George Schlossnagle <george@omniti.com>
 */

require_once "Text/Word.php";

class Text_Statistics {
    /*
     * The document text.
     *
     * @var string
     * @access public
     */
    var $text = '';

    /*
     * The number of syllables in the document.
     *
     * @var number
     * @access public
     */
    var $numSyllables = 0;

    /*
     * The number of words in the document.
     *
     * @var number
     * @access public
     */
    var $numWords = 0;

    /*
     * The number of unique words in the document.
     *
     * @var number
     * @access public
     */
    var $uniqWords = 0;

    /*
     * The number of sentences in the document.
     *
     * @var number
     * @access public
     */
    var $numSentences = 0;

    /*
     * The Flesch score of the document.
     *
     * @var number
     * @access public
     */
    var $flesch = 0;

    /*
     * Some abbreviations we should expand.  THis list could/should
     * be much larger.
     *
     * @var number
     * @access protected
     */
    var $_abbreviations = array('/Mr\./'   => 'Misterr',
                                '/Mrs\./i' => 'Misses', // Phonetic
                                '/etc\./i' => 'etcetera',
                                '/Dr\./i'  => 'Doctor',
                               );

    /*
     * Constructor.
     *
     * @param string
     * @access public
     */
    function Text_Statistics($block) 
    {
        $this->text = $block;
        $this->_analyze();
    }

    /*
     * Compute statistics for the document object.
     *
     * @access protected
     */
    function _analyze() 
    {
        $lines = explode("\n", $this->text);
        foreach( $lines as $line ) {
            $this->_analyze_line($line);
        }
        $this->flesch = 206.835 - 
            (1.015 * ($this->numWords/$this->numSentences)) -
            (84.6 * ($this->numSyllables/$this->numWords));
    } 

    /*
     * Helper function, computes statistics on a given line.
     *
     * @param string
     * @access protected
     */
    function _analyze_line($line) 
    {
        // expand abbreviations for counting syllables
        $line = preg_replace(array_keys($this->_abbreviations), 
                 array_values($this->_abbreviations),
                 $line);
        preg_match_all("/\b(\w[\w'-]*)\b/", $line, $words);
        foreach( $words[1] as $word ) {
            $w_obj = new Text_Word($word);
            $this->numSyllables += $w_obj->numSyllables();
            $this->numWords++;
            if($this->_uniques[strtolower($word)]++ == 0) {
               $this->uniqWords++;
            }
        }
        preg_match_all("/[.!?]/", $line, $matches);
        $this->numSentences += count($matches[0]);
    }
}
?>

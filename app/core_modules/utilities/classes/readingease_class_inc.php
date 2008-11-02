<?php
/* -------------------- string class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 

/**
* 
* Class for calculating the reading ease scores. 
* 
* Readability measures are primarily based on factors such as the 
* number of words in the sentences and the number of letters or syllables 
* per word (i.e., as a reflection of word frequency). Two of the most 
* commonly used measures are the Flesch Reading Ease formula and the 
* Flesch-Kincaid Grade Level. Another is the Gunning-Fox Index, which
* represents the approximate reading age of the text - the age someone 
* will need to be to understand what they are reading.
* 
* This class calculates:
* 1. Flesh reading ease
* 2. Flesh-Kincaid grade level
* 3. Gunning-Fox Index
* This is based on the tutorials by Dave Child at
* http://www.ilovejackdaniels.com/php/flesch-kincaid-function/
*  and at 
* http://www.ilovejackdaniels.com/php/gunning-fox-function/
* 
* The output of the Flesch Reading Ease formula is a number from 
* 0 to 100, with a higher score indicating easier reading. The average 
* document has a Flesch Reading Ease score between 6-70.
* 
* The output of the Flesh-Kincaid Grade level is...
* 
* The output of the Gunning-Fox Index
* 
* 
* @category  Chisimba
 * @package   <module name>
 * @author Derek Keats 
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
*/
class readingease extends object {
  
  
    /**
    * Method to construct the class
    */
    function init()
    {

    } #functin init
    
    /**
    * 
    * The Flesch reading ease score is worked out using the following 
    * calculation, which gives a number. The higher that number is, 
    * the easier the text is to read.
    * 
    * 206.835 - (1.015 * average_words_sentence) - (84.6 * average_syllables_word)
    * 
    * 
    * @param string $str The string to analyse
    * 
    */
    function calculateFlesch($str) 
    {
        return (206.835 - (1.015 * $this->averageWordsSentence($str)) 
          - (84.6 * $this->averageSyllablesWord($str)));
    }
    
    
    /**
    * 
    * Method to caluclate the Flesch-Kincaid reading grade.
    * 
    * The Flesch-Kincaid grade level gives a number that 
    * corresponds to the grade a person will need to have reached 
    * to understand it. For example, a Grade level score of 8 
    * means that an eighth grader will understand the text.
    * 
    * @param string $str The string to analyse
    * 
    */
    function calculateReadingGrade($str) 
    {
        return ((.39 * $this->averageWordsSentence($str)) 
          + (11.8 * $this->averageSyllablesWord($str)) - 15.59);
    }
    
    /**
    * 
    * Method to calculate the Gunning-Fox Index of a string.
    * 
    * The output is a number, which is a rough measure of the age 
    * someone must be to understand the content. The lower the 
    * number, the more understandable the content will be to your 
    * visitors. Web sites should aim to have content that falls 
    * roughly in the 11-15 range for this test. Any number returned 
    * over the value of 22 can be taken to be just 22, and is roughly 
    * equivalent to post-graduate level.
    * 
    * The formula is:
    * (average_words_sentence + percentage_of_words_with_more_than_three_syllables) * 0.4
    * 
    */
    function calculateReadingAge($str)
    {
        return (($this->averageWordsSentence($str) 
          + $this->percentageNumberWords3Syllables($str)) 
          * 0.4) + 5;
    
    } #function calculateReadingAge
    
    /**
    * 
    * Method to count the words in a sentence
    * 
    * 
    * @param string $str The string to analyse
    * 
    */
    function averageWordsSentence($str) 
    {
        $sentences = strlen(preg_replace('/[^\.!?]/', '', $str));
        $words = strlen(preg_replace('/[^ ]/', '', $str));
        return ($words/$sentences);
    } #function averageWordsSentence

    /**
    * 
    * Method to count the average number of syllables per word
    * 
    * @param string $str The string to analyse
    * 
    */
    function averageSyllablesWord($str) 
    {
        $words = explode(' ', $str);
        $syllables="";
        for ($i = 0; $i < count($words); $i++) {
            $syllables = $syllables + $this->countSyllables($words[$i]);
        }
        return ($syllables/count($words));
    } #function averageSyllablesWord

    /**
    * 
    * Method to count the number of syllables in a word
    * 
    * @param string $word The word to analyse
    * 
    */
    function countSyllables($word) {

        $subsyl = Array(
          'cial'
          ,'tia'
          ,'cius'
          ,'cious'
          ,'giu'
          ,'ion'
          ,'iou'
          ,'sia$'
          ,'.ely$'
        );

        $addsyl = Array(
          'ia'
          ,'riet'
          ,'dien'
          ,'iu'
          ,'io'
          ,'ii'
          ,'[aeiouym]bl$'
          ,'[aeiou]{3}'
          ,'^mc'
          ,'ism$'
          ,'([^aeiouy])\1l$'
          ,'[^l]lien'
          ,'^coa[dglx].'
          ,'[^gq]ua[^auieo]'
          ,'dnt$'
        );

        // Based on Greg Fast's Perl module Lingua::EN::Syllables
        $valid_word_parts="";
        $word = preg_replace('/[^a-z]/is', '', strtolower($word));
        $word_parts = preg_split('/[^aeiouy]+/', $word);
        foreach ($word_parts as $key => $value) {
            if ($value <> '') {
                $valid_word_parts[] = $value;
            }
        }
    
        $syllables = 0;
        foreach ($subsyl as $syl) {
            if (strpos($word, $syl) !== FALSE) {
                $syllables--;
            }
        }
        foreach ($addsyl as $syl) {
            if (strpos($word, $syl) !== FALSE) {
                $syllables++;
            }
        }
        if (strlen($word) == 1) {
            $syllables++;
        }
        $syllables += count($valid_word_parts);
        $syllables = ($syllables == 0) ? 1 : $syllables;
        return $syllables;
    } #function countSyllables
    
    /**
    * Method to calculate the percentage of words with 3 or more syllables
    * 
    * @param string $str The string to analyse
    * 
    */
    function percentageNumberWords3Syllables($str) {
        $syllables = 0;
        $words = explode(' ', $str);
        for ($i = 0; $i < count($words); $i++) {
            if ($this->countSyllables($words[$i]) > 2) {
                $syllables ++;
            }
        }
        $score = number_format((($syllables / count($words)) * 100));
        return ($score);
    } #function percentageNumberWords3Syllables
    
} #class

?>
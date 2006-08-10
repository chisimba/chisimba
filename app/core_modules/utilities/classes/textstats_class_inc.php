<?php
/* -------------------- string class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 

/**
* Class for calculating stats on strings. Main use in analysing text
* such as discussion forums
* 
* @uses 
* 
* @author Derek Keats 
*/
class textstats extends object {

    var $evaluation_string;
    
    var $words;
    var $letters;
    var $sentences;
    var $length;
    var $sentences_arr=array();
    var $avg_sentence_words;
    var $max_sentence_words;
    var $min_sentence_words;
    var $std_sentence_words;

  
    /**
    * Method to construct the class
    */
    function init()
    {
        $this->objElemStats = $this->getObject('elemstats');
    }
    
    
    /**
    * Method to get the length of the string
    */
    function calc_length($str=NULL)
    {
        if (!$str) {
            $str=$this->evaluation_string;
        }
       $this->length = strlen($str);
       return $this->length;
    }
    /**
    * Method to count the letters in a string
    */
    function count_letters($str=NULL)
    {
        if (!$str) {
            $str=$this->evaluation_string;
        }
       $_count = 0; 
       for ($iCount=0;$iCount<strlen($str);$iCount++) { 
         $_char = substr($str,$iCount,1); 
         if (preg_match("/[a-z]/",$_char)) $_count++; 
       } 
       $this->letters=$_count;
       return $this->letters;
    }
    
        
    /**
    * Method to calculate the number of words and store it in $this->words;
    */
    function count_words($str=NULL)
    {
        if (!$str) {
            $str=$this->evaluation_string;
        }
        $this->words = str_word_count($str);
        return $this->words;
    }
    

    /**
    * Method to calculate the average number of words per 
    * sentence and other sentence stats
    */
    function calc_sentence_stats($arr=NULL)
    {
        $this->loadClass("elemstats");
        if (!$arr) {
            $arr=$this->sentences_arr;
        }
        $numElements=count($arr);
        $arr_wrds=array();
        //Loop through the array and make an array of word counts
        for ($counter=0; $counter < $numElements; $counter++) {
            $arr_wrds[$counter] = str_word_count($arr[$counter]);
            //echo "}}".trim($arr[$counter])."||".$arr_wrds[$counter]."{{<br/>";
        }
        $this->objElemStats->calc_basic_stats($arr_wrds);
        $this->avg_sentence_words = $this->objElemStats->mean;
        $this->min_sentence_words = $this->objElemStats->min;
        $this->max_sentence_words = $this->objElemStats->max;
        $this->std_sentence_words = $this->objElemStats->std;
        


        
    }
    
    /**
    * Method to count the sentences in a string
    */
    function count_sentences($str=NULL)
    {
        if (!$str) {
            $str=$this->evaluation_string;
        }
        $str=str_replace('. ','|-|',$str);
        $str=str_replace('? ','|-|',$str);
        $str=str_replace('! ','|-|',$str);
        $this->sentences_arr=explode('|-|', $str);
        $this->sentences=count($this->sentences_arr) + 1;
        return $this->sentences;
    }
    
    /************ Methods for cleaning up strings ************************/
    
    /**
    * Method to clean up the string
    * @var string $str: the string to clean, defaults to 
    * $this->evaluation_string
    */
    function clean_string($str=NULL)
    {
        if (!$str) {
            $str=$this->evaluation_string;
        }
        $str=trim($str);
        $str=strip_tags($str);
    }
    
    /**
    * Method to remove non alphanumeric characters from a string
    * @var string $str: the string from which to remove the non alphanumeric 
    *  characters, defaults to $this->evaluation_string
    */
    function strip_nonalphanumeric($str=NULL)
    {
        if (!$str) {
            $str=$this->evaluation_string;
        }
        return preg_replace ("/[^a-z 0-9]/i",'',$str);
    }
    
    
    /************ Private methods below this line ************************/
    
    /**
    * Method to calculate the number of words and but do not set the class
    * property
    */
    function _count_words_noset($str)
    {
        return str_word_count($str);

    }
} 

?>
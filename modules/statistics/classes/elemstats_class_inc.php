<?php
/* -------------------- string class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 

/**
* Class for calculating elementary stats. Main use with text_class_inc.php 
* in analysing text such as discussion forums
* 
* @author Derek Keats 
* @porter Dean Van Niekerk
* ported from php4 to php5
*/
class elemstats extends controller {
    
     public $mean;
     public $n;
     public $std;
     public $stderr;
     public $sum;
     public $min;
     public $max;
    
    /**
    * Method to calculate basic stats on an array
    * @var array $arr: the array on which to perform the operations
    */
    public function calc_basic_stats($arr=array())
    {
        $this->n = count($arr);
        $sum=0;
        for ($current_sample = 0; $this->n > $current_sample; ++$current_sample) {
            $sum = $sum + $arr[$current_sample];
            $sample_square[$current_sample] = pow($arr[$current_sample], 2);
        }
        $this->sum=round($sum, 2);
        $this->mean = round(($sum / $this->n), 2);
        $this->min = min($arr);
        $this->max = max($arr);
        $this->std = round(sqrt(array_sum($sample_square) / $this->n - pow((array_sum($arr) / $this->n), 2)), 2);
        
    
    }

    /************ Private methods below this line ************************/
    

} 

?>
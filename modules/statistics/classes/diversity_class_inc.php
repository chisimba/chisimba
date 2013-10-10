<?php
/* -------------------- string class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 

/**
* Class for calculating diversity stats. 
* 
* @author Derek Keats 
* @porter Dean Van Niekerk
* ported from php4 to php5
*/
class diversity extends object {
    

    public $S;
    public $H1;
    public $E;
    public $D;
    
    public $_sum;
    
    
    
    /**
    * Method to calculate basic stats on an array
    * @var array $arr: the array on which to perform the operations
    */
    public function diversityCalc($arr=array())
    {
        $this->S = count($arr);
        $sum=0;
        $pi=array();
        for ($current_sample = 0; $this->S > $current_sample; ++$current_sample) {
            $sum = $sum + $arr[$current_sample];
            $sample_square[$current_sample] = pow($arr[$current_sample], 2);
        }
        $this->_sum=round($sum, 2);
        $sumpilogpi = 0;
        for ($current_sample = 0; $this->S > $current_sample; ++$current_sample) {
            $pi = $arr[$current_sample]/sum * log($arr[$current_sample]/sum, 2);
            $sumpilogpi .= $pi;
        }
        $this->H1=$sumpilogpi;       
    }

    
} 

?>
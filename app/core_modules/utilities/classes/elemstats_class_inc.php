<?php
/* -------------------- string class extends controller ----------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* Class for calculating elementary stats. Main use with text_class_inc.php 
* in analysing text such as discussion forums
* 
* @category  Chisimba
* @package   utilities
* @author Derek Keats
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
*/


class elemstats extends controller {
    
    /**
    * @var $mean 
    *Property to hold the mean value
    */
    var $mean;
    /**
    * @var $n 
    *Property to hold the counter for the array
    */
    var $n;
    /**
    * @var $std 
    * Property to hold the std value
    */
    var $std;
    /**
    * @var $stderr 
    * Property to hold the error value
    */
    var $stderr;
    /**
    * @var $sum
    * Property to hold the sum value
    */
    var $sum;
    /**
    * @var $min
    * Property to hold the smallest value in the array
    */
    var $min;
    /**
    * @var $max
    * Property to hold the largest value in the array
    */
    var $max;
    
    /**
    * Method to calculate basic stats on an array
    * @var array $arr: the array on which to perform the operations
    */
    function calc_basic_stats($arr=array())
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
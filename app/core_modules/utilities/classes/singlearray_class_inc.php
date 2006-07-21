<?
/**
* Class to Convert a Multidimensional array into a single array
*
* This class takes a multidimensional (associative) array and converts it into a single array
* Useful if you know the structure of an array, but do not wish to recurse it
*
* Example: 
* $details = array('name'=>array('firstname'=>'Tohir', 'surname'=>'Solomons'), 'info'=>array('age'=>27, 'sex'=>'male'));
*   Will be converted to
* $details = array('firstname'=>'Tohir', 'surname'=>'Solomons', 'age'=>27, 'sex'=>'male');
*
* @author Tohir Solomons
*/

class singlearray extends object
{
    /**
    * @var array $finalArray Variable to Hold the Final Array
    */
    var $finalArray;
    
    /**
    * Constructor
    */
    function init()
    {  }
    
    /**
    * Method to Convert the Array
    * @param array $array Array to Convert
    * @return array Converted Single Array
    */
    function convertArray($array)
    {
        // create array
        $this->finalArray = array();
        
        // Loop through array
        $this->_looparray($array);
        
        // Clean up so that it can be reused without initialising it again
        $final = $this->finalArray;
        $this->finalArray = NULL;
        
        // Return the converted array
        return $final;
    }
    
    /**
    * Method to loop through the array, and add single values to the converted array
    * @param array $array;
    * @access private
    */
    function _looparray($array)
    {
        // Only perform if array
        if (is_array($array))
        {
            // Loop Through Items in Array
            foreach ($array as $item=>$value)
            {
                // Check if item value is array
                if (is_array($value)) {
                    // If it is, loop through item value array
                    $this->_looparray($value);
                } else {
                    // Add Value to Array with Item as Key
                    $this->finalArray[$item] = $value;
                }
            }
        }
        
        return;
    }
}
?>
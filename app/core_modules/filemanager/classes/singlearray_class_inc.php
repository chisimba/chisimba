<?
/**
* Class to Convert a Multidimensional array into a single array
*
* This class takes a multidimensional (associative) array and converts it into a single array
* Useful if you know the structure of an array, but do not wish to recurse it
* 
* UPDATE: This class is now more aimed at serializing data from the get Id3 Class
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
    private $finalArray;
    
    /**
    * Constructor
    */
    public function init()
    {  }
    
    /**
    * Method to Convert the Array
    * @param array $array Array to Convert
    * @return array Converted Single Array
    */
    public function convertArray($array)
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
    private function _looparray($array)
    {
        // Only perform if array
        if (is_array($array))
        {
            // Loop Through Items in Array
            foreach ($array as $item=>$value)
            {
                // Check if item value is array
                if (is_array($value)) {
                    if (count($value) == 1 && isset($value[0]) && !is_array($value[0])) {
                        $this->addItem($item, $value[0]);
                    } else {
                        // If it is, loop through item value array
                        $this->_looparray($value);
                    }
                } else {
                    // Add Value to Array with Item as Key
                    $this->addItem($item, $value);
                }
            }
        }
        
        return;
    }
    
    /**
     * Function to add item to main array
     *
     * @param string $item Name of the Item
     * @param string $value Value of the Item
     */
    private function addItem ($item, $value)
    {
        if ($value != '') { // Check if Item is not Null
             $this->finalArray[$item] = $value;
        }
    }
}
?>
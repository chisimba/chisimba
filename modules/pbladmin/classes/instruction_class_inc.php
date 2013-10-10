<?php
/**
* Class instruction extends object.
* @package pbladmin
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die('You cannot view this page directly');
}
// end security check

/**
 * Class to create an instruction object
 * Class is used when parsing an xml file.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbladmin
 * @version 0.9
 */

class instruction extends object
{
    public $attrs = array(); // atributes
    public $instrs = array(); // point to nested instructions
    public $ip = 0; // instruction pointer
    public $name; // instruction name
    public $bIsParsed = FALSE; // instruction parsing completed

    /**
     * Constructor method to initialise objects
     */
    public function instruction()
    {
    }

    /**
     * Constructor method to initialise the instruction.
     *
     * @param string $name Instruction name
     * @param string $attributes Instruction attributes
     * @return
     */
    public function makeInstruction($name, $attrs)
    {
        $this->attrs = $attrs;
        $this->name = $name;
    }

    /**
     * Method to add a nested instruction
     * @param string $ip The instruction.
     * @return
     */
    public function addInstr($ip)
    {
        $this->instrs[$this->ip] = $ip;
        $this->ip++;
    }

    /**
     * Method to return the attribute value for the given attribute key.
     * @return string $v The attribute value
     */
    public function getAttrVal($key)
    {
        if ($this->attrs) {
            foreach($this->attrs as $k => $v) {
                $i = 1;
                if (strtolower($k) == strtolower($key)) {
                    return $v;
                }
            }
        }
        return -1;
    }

    /**
     * Method to return the nested instructions.
     * @return string $instrs The nested instructions
     */
    public function getInstructions()
    {
        return $this->instrs;
    }

    /**
     * Method to determine if parsing of this instruction has completed.
     * @return bool $bIsParsed TRUE if parsing of this instruction has completed
     */
    public function isParsed()
    {
        return $this->bIsParsed;
    }

    /**
     * Method to set the parsing completed to TRUE or FALSE
     * @return
     */
    public function setParsed($value)
    {
        $this->bIsParsed = $value;
    }
}
?>
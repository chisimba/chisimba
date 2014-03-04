<?php
/**
* Class xmlParser extends object.
* @package pbladmin
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die('You cannot view this page directly');
}
// end security check

 /**
 * Class for parsing an xml file.
 * This class should provide functionality to parse an xml file.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbladmin
 * @version 0.9
 */

class xmlParser extends object
{
    public $instrs = array();
    public $ip = 0, $ni = 0;

    /**
     * Constructor method to initialise objects
     */
    public function init()
    {
//        $this->instruction = &$this->getObject('instruction');
        $this->loadClass('instruction');
    }

    /**
    * Method to call the parser and return an instruction object.
    *
    * @param string $filename The xml file to parse.
    * @return array $instrs The parsed file as an array of instruction objects.
    */
    public function parse($filename)
    {
        if($this->parser($filename)){
            return $this->instrs;
        }
        return FALSE;
    }

    /**
     * Method to load and parse the specified xml file
     *
     * @param string $filename The xml file to parse
     * @return bool
     */
    public function parser($filename)
    {
        $xml_parser = xml_parser_create();
        xml_set_object($xml_parser, $this);
        xml_set_element_handler($xml_parser, "startElement", "endElement");
        if (!($fp = fopen($filename, "r"))) {
            return FALSE;
        } while ($data = fread($fp, 4096)) {
            if (!xml_parse($xml_parser, $data, feof($fp))) {
                return FALSE;
            }
        }
        xml_parser_free($xml_parser);
        return TRUE;
    }

    /**
     * Method to insert (nest) an instruction into the instructions tree.
     * @param string $ip The instruction
     * @return
     */
    public function insertInstruction($ip)
    {
        for($i = $ip-1;$i >= 0;$i--) {
            if ($this->instrs[$i]->isParsed() == FALSE) {
                $this->instrs[$i]->addInstr($ip);
            }
        }
    }

    /**
     * Method to set the last non-terminated instruction to parsed (completed).
     * @param string $ip The instruction
     * @return
     */
    public function closeInstruction($ip)
    {
        // Recurse instructions, find the last parsed instruction (FALSE)
        // and set it to parsed (TRUE) then exit the loop.
        for($i = $ip-1;$i >= 0;$i--) {
            if ($this->instrs[$i]->isParsed() == FALSE) {
                $this->instrs[$i]->setParsed(TRUE);
                break;
            }
        }
    }

    /**
     * Method to start processing each xml element.
     * The method creates a new instruction for the element and adds it to an
     * array of instructions.
     * @param string $parser The xml parser
     * @param string $name The name of the instruction
     * @param string $attrs The attributes of the instruction
     * @return
     */
    public function startElement($parser, $name, $attrs)
    {
        $this->instrs[$this->ni] = new instruction(); //$this->instruction;
        $this->instrs[$this->ni]->makeInstruction(strtolower($name), $attrs);
        if ($this->ni > 0) {
            $this->insertInstruction($this->ni);
        }
        $this->ni++;
    }

    /**
     * Method to be called when a closing tag is found.
     * @return
     */
    public function endElement($parser, $name)
    {
        $this->closeInstruction($this->ni);
    }

    /**
     * Method to find a specified instruction.
     * @param string $name The name of the instruction
     * @return string $i The index number of the instruction
     */
    public function findInstruction($name)
    {
        $ninstrs = count($this->instrs);
        for($i = 0;$i < $ninstrs;$i++) {
            if ($this->instrs[$i]->name == $name){
                return $i;
            }
        }
        return -1;
    }
}
?>
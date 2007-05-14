<?php
/**
* OpenDocument_TextElement class
* 
* OpenDocument_TextElement represents text in OpenDocument
*
* PHP version 5
*
* LICENSE: This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
* 
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
* 
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
* 
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/

require_once 'Element.php';

/**
* OpenDocument_TextElement class
* 
* OpenDocument_TextElement represents text in OpenDocument
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_TextElement extends OpenDocument_Element
{
    /**
     * Element text
     *
     * @var string
     */
    private $text;
    
    /**
     * Constructor
     *
     * @param DOMNode $node
     * @param OpenDocument $document
     */
    public function __construct(DOMNode $node, OpenDocument $document)
    {
        parent::__construct($node, $document);
        $this->text = $node->wholeText;
    }

    /**
     * Create object instance
     *
     * @param mixed $object
     * @param string $text
     * @return OpenDocument_TextElement
     */
    public static function instance($object, $text)
    {
        if ($object instanceof OpenDocument) {
            $document = $object;
            $node = $object->cursor;
        } else if ($object instanceof OpenDocument_Element) {
            $document = $object->getDocument();
            $node = $object->getNode();
        } else {
            throw new Exception('Object must be OpenDocument or OpenDocument_Element');
        }
        $element = new OpenDocument_TextElement($node->ownerDocument->createTextNode($text), $document);
        $node->appendChild($element->node);
        $element->text = $text;
        return $element;
    }
    
    /**
     * Magic method
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     */
    protected function __set($name, $value)
    {
        switch ($name) {
        case 'text':
            $this->text = $value;
            $this->setText($value);
            break;
        default:
        }
    }
    
    /**
     * Magic method
     * Get property value
     *
     * @param string $name
     * @return mixed
     */
    protected function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
    }
    
    /**
     * Set element text
     *
     * @param string $text
     */
    private function setText($text)
    {
        $node = $this->node->ownerDocument->createTextNode($text);
        $this->node->parentNode->replaceChild($node, $this->node);
    }
}
?>
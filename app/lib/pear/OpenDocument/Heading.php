<?php
/**
* OpenDocument_Heading class
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

require_once 'StyledElement.php';

/**
* OpenDocument_Heading element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_Heading extends OpenDocument_StyledElement
{
    /**
     * Heading level
     *
     * @var integer
     */
    private $level;
    
    /**
     * Node namespace
     */
    const nodeNS = OpenDocument::NS_TEXT;
    
    /**
     * Node namespace
     */
    const nodePrefix = 'text';
    
    /**
     * Node name
     */
    const nodeName = 'h';
    
    /**
     * Element style name prefix
     */
    const styleNamePrefix = 'H';

    /**
     * Constructor
     *
     * @param DOMNode $node
     * @param OpenDocument $document
     */
    public function __construct(DOMNode $node, OpenDocument $document)
    {
        parent::__construct($node, $document);
        $this->level = $node->getAttributeNS(OpenDocument::NS_TEXT, 'outline-level');
        
        $this->allowedElements = array(
            'OpenDocument_Span',
            'OpenDocument_Hyperlink',
        );
    }
    
    /**
     * Create OpenDocument_Heading element
     *
     * @param mixed $object
     * @param mixed $content
     * @param integer $level optional
     * @return OpenDocument_Heading
     */
    public static function instance($object, $content, $level = 1)
    {
        if ($object instanceof OpenDocument) {
            $document = $object;
            $node = $object->cursor;
        } else if ($object instanceof OpenDocument_Element) {
            $document = $object->getDocument();
            $node = $object->getNode();
        } else {
            throw new OpenDocument_Exception(OpenDocument_Exception::ELEM_OR_DOC_EXPECTED);
        }
        
        $element = new OpenDocument_Heading($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document);
        $node->appendChild($element->node);

        if (is_scalar($content)) {
            $element->createTextElement($content);
        }
        
        $element->__set('level', $level);

        return $element;
    }
    
    /**
     * Set element properties
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        switch ($name) {
        case 'level':
            if (!is_int($value) && !ctype_digit($value)) {
                $value = 1;
            }
            $this->type = $value;
            $this->node->setAttributeNS(OpenDocument::NS_TEXT, 'outline-level', $value);
            break;
        default:
        }
    }
    
    /**
     * Get element properties
     *
     * @param string  $name
     * @return mixed
     */
    protected function __get($name)
    {
        if ($value = parent::__get($name)) {
            return $value;
        }
        if (isset($this->$name)) {
            return $this->$name;
        }
    }
    
    /**
     * Generate element new style name
     *
     * @return string
     */
    public function generateStyleName()
    {
        self::$styleNameMaxNumber ++;
        return self::styleNamePrefix . self::$styleNameMaxNumber;
    }

    /************** Elements ***********************/
    
    /**
     * Create OpenDocument_TextElement
     *
     * @param string $text
     * @return OpenDocument_TextElement
     */
    public function createTextElement($text)
    {
        return OpenDocument_TextElement::instance($this, $text);
    }

    /**
     * Create OpenDocument_Hyperlink
     *
     * @param string $text
     * @param string $location
     * @param string $type optional
     * @param string $target optional
     * @param string $name optional
     * @return OpenDocument_Hyperlink
     */
    public function createHyperlink($text, $location, $type = 'simple', $target = '', $name = '')
    {
        return OpenDocument_Hyperlink::instance($this, $text, $location, $type, $target, $name);
    }
    
    /**
     * Create OpenDocument_Span element
     *
     * @param string $text
     * @return OpenDocument_Span
     */
    public function createSpan($text)
    {
        return OpenDocument_Span::instance($this, $text);
    }
}
?>
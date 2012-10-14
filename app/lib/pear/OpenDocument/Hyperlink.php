<?php
/**
* OpenDocument_Hyperlink class
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
* OpenDocument_Hyperlink element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_Hyperlink extends OpenDocument_StyledElement
{
    /**
     * Link location
     *
     * @var string
     */
    private $location;
    
    /**
     * Link type
     *
     * @var string
     */
    private $type;
    
    /**
     * Link target
     *
     * @var string
     */
    private $target;
    
    /**
     * Link name
     *
     * @var string
     */
    private $name;
    
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
    const nodeName = 'a';
    
    /**
     * Element style name prefix
     */
    const styleNamePrefix = 'A';

    /**
     * Constructor
     *
     * @param DOMNode $node
     * @param OpenDocument $document
     */
    public function __construct(DOMNode $node, OpenDocument $document)
    {
        parent::__construct($node, $document);
        $this->location = $node->getAttributeNS(OpenDocument::NS_XLINK, 'href');
        $this->type     = $node->getAttributeNS(OpenDocument::NS_XLINK, 'type');
        $this->target   = $node->getAttributeNS(OpenDocument::NS_OFFICE, 'target');
        $this->name     = $node->getAttributeNS(OpenDocument::NS_OFFICE, 'name');
        
        $this->allowedElements = array(
            'OpenDocument_Span',
        );
    }

    /**
     * Create OpenDocument_Hyperlink instance
     *
     * @param mixed $object
     * @param mixed $content
     * @param string $location
     * @param string $type optional
     * @param string $target optional 
     * @param string $name optional
     * @return OpenDocument_Hyperlink
     */
    public static function instance($object, $content, $location, $type = 'simple', $target = '', $name = '')
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
        
        $element = new OpenDocument_Hyperlink($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document);
        $node->appendChild($element->node);

        $element->__set('location', $location);
        $element->__set('type', $type);
        $element->__set('target', $target);
        $element->__set('name', $name);

        if (is_scalar($content)) {
            $element->createTextElement($content);
        }

        return $element;
    }

    /**
     * Set element property
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        switch ($name) {
        case 'location':
            $this->location = $value;
            $this->node->setAttributeNS(OpenDocument::NS_XLINK, 'href', $value);
            break;
        case 'type':
            if (!in_array($value, array('simple'))) {
                $value = 'simple';
            }
            $this->type = $value;
            $this->node->setAttributeNS(OpenDocument::NS_XLINK, 'type', $value);
            break;
        case 'target':
            $this->target = $value;
            $this->node->setAttributeNS(OpenDocument::NS_OFFICE, 'target-frame-name', $value);
            break;
        case 'name':
            $this->name = $value;
            $this->node->setAttributeNS(OpenDocument::NS_OFFICE, 'name', $value);
            break;
        default:
        }
    }
    
    /**
     * Get element property
     *
     * @param string $name
     * @return mixed
     */
    protected function __get($name)
    {
        /*if ($value = parent::__get($name)) {
            return $value;
        }*/
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
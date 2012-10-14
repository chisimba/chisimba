<?php
/**
* OpenDocument_Element abstract class
* 
* OpenDocument_Element absract class - all other elements inherit from this one
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

/**
* OpenDocument_Element abstract class
* 
* OpenDocument_Element absract class - all other elements inherit from this one
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
abstract class OpenDocument_Element
{
    /**
     * Element DOMNode
     *
     * @var DOMNode
     */
    protected $node;
    
    /**
     * Element OpenDocument
     *
     * @var OpenDocument
     */
    protected $document;
    
    /**
     * Array of allowed documents
     *
     * @var array
     */
    protected $allowedElements;
    
    /**
     * ArrayObject of children elements
     *
     * @var ArrayObject
     */
    protected $children;

    /**
     * Constructor
     *
     * @param DOMNode $node
     * @param OpenDocument $document
     */
    public function __construct(DOMNode $node, OpenDocument $document)
    {
        $this->node = $node;
        $this->document = $document;
        $this->allowedElements = array();
    }

    /**
     * Get element DOMNode
     *
     * @return DOMNode
     */
    public function getNode()
    {
        return $this->node;
    }
    
    /**
     * Get element document
     *
     * @return OpenDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Delete element
     *
     */
    public function delete()
    {
        $this->document->deleteElement($this);
    }
    
    /**
     * Get element children
     *
     * @return ArrayIterator
     */
    public function getChildren()
    {
        $this->listChildren();
        return $this->children->getIterator();
    }
    
    /**
     * Prepare element children
     *
     */
    protected function listChildren()
    {
        $this->children = new ArrayObject;
        if ($this->node instanceof DOMNode) {
            $childrenNodes = $this->node->childNodes;
            foreach ($childrenNodes as $child) {
                if ($child instanceof DOMText) {
                    $element = new OpenDocument_TextElement($child, $this->document);
                    $this->children->append($element);
                } else {
                    foreach ($this->allowedElements as $class) {
                        $ReflectionClass = new ReflectionClass($class);
                        $tag = $ReflectionClass->getConstant('nodePrefix') . ':' . $ReflectionClass->getConstant('nodeName');
                        if ($child->nodeName == $tag) {
                            $element = new $class($child, $this->document);
                            $this->children->append($element);
                            break;
                        }
                    }
                }
            }
        }
    }
}
?>
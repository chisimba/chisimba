<?php
/**
* OpenDocument_StyledElement abstract class
* 
* OpenDocument_StyledElement absract class - all elements that have styles inherit from this one
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
require_once 'ElementStyle.php';

/**
* OpenDocument_StyledElement abstract class
* 
* OpenDocument_StyledElement absract class - all elements that have styles inherit from this one
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
abstract class OpenDocument_StyledElement extends OpenDocument_Element
{
    /**
     * Style information
     *
     * @var array
     */
    protected $style;

    /**
     * Style name suffix max value
     *
     * @var integer
     */
    protected static $styleNameMaxNumber = 0;
    
    /**
     * Style name prefix
     *
     */
    const styleNamePrefix = 'E';
    
    /**
     * Generate new style name
     *
     */
    abstract public function generateStyleName();
    
    /**
     * Constructor
     *
     * @param DOMNode $node
     * @param OpenDocument $document
     */
    public function __construct(DOMNode $node, OpenDocument $document)
    {
        parent::__construct($node, $document);
        $this->style = new OpenDocument_ElementStyle($this);
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
        if ($name == 'style') {
            return $this->style;
        }
    }
    
    /**
     * Get style information
     *
     * @param array $properties
     * @return array
     */
    public function getStyle($properties)
    {
        return $this->document->getStyle($this->getStyleName(), $properties);
    }
    
    /**
     * Get style name
     *
     * @return string
     */
    public function getStyleName()
    {
        return $this->node->getAttributeNS(OpenDocument::NS_TEXT, 'style-name');
    }
    
    /**
     * Get style name prefix
     *
     * @return string
     */
    public function getStyleNamePrefix()
    {
        return $this->styleNamePrefix;
    }
    
    /**
     * Get style name suffix max value
     *
     * @return integer
     */
    public static function getStyleNameMaxNumber()
    {
        return self::$styleNameMaxNumber;
    }
    
    /**
     * Set style name suxxif max value
     *
     * @param integer $number
     */
    public static function setStyleNameMaxNumber($number)
    {
        self::$styleNameMaxNumber = $number;
    }

    /**
     * Apply style information
     *
     * @param string $name
     * @param mixed $value
     */
    public function applyStyle($name, $value)
    {
        $style_name = $this->node->getAttributeNS(OpenDocument::NS_TEXT, 'style-name');
        $style_name = $this->document->applyStyle($style_name, $name, $value, $this);
        $this->node->setAttributeNS(OpenDocument::NS_TEXT, 'style-name', $style_name);
    }
}
?>
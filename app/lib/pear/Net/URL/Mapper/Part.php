<?php
/**
 * URL parser and mapper
 *
 * PHP version 5
 *
 * LICENSE:
 * 
 * Copyright (c) 2006, Bertrand Mansion <golgote@mamasam.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the 
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products 
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Net
 * @package    Net_URL_Mapper
 * @author     Bertrand Mansion <golgote@mamasam.com>
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Net_URL_Mapper
 */

abstract class Net_URL_Mapper_Part
{
    protected $defaults;
    protected $rule;
    protected $public;
    protected $type;
    protected $required = false;
    
    /**
    * Part name if dynamic or content, generated from path
    * @var string
    */
    public $content;
    
    const DYNAMIC = 1;
    const WILDCARD = 2;
    const FIXED = 3;

    public function __construct($content, $path)
    {
        $this->content = $content;
        $this->path = $path;
    }

    public function setRule($rule)
    {
        $this->rule = $rule;
    }

    abstract public function getFormat();
    
    abstract public function getRule();

    public function addSlash($str)
    {
        $str = trim($str, '/');
        if (($pos = strpos($this->path, '/')) !== false) {
            if ($pos == 0) {
                $str = '/'.$str;
            } else {
                $str .= '/';
            }
        }
        return $str;
    }

    public function addSlashRegex($str)
    {
        $str = trim($str, '/');
        if (($pos = strpos($this->path, '/')) !== false) {
            if ($pos == 0) {
                $str = '\/'.$str;
            } else {
                $str .= '\/';
            }
        }
        if (!$this->isRequired()) {
            $str = '('.$str.'|)';
        }
        return $str;
    }

    public function setDefaults($defaults)
    {
        $this->defaults = (string)$defaults;
    }

    public function getType()
    {
        return $this->type;
    }

    public function accept($visitor, $method = null)
    {
        $args = func_get_args();
        $visitor->$method($this, $args);
    }

    public function setRequired($required)
    {
        $this->required = $required;
    }

    public function isRequired()
    {
        return $this->required;
    }

    abstract public function generate($value = null);
    
    public function match($value)
    {
        $rule = $this->getRule();
        return preg_match('/^'.$rule.'$/', $this->addSlash($value));
    }

}

?>
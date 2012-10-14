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

require_once 'Net/URL/Mapper/Part.php';

class Net_URL_Mapper_Part_Dynamic extends Net_URL_Mapper_Part
{
    
    public function __construct($content, $path)
    {
        $this->type = Net_URL_Mapper_Part::DYNAMIC;
        $this->setRequired(true);
        parent::__construct($content, $path);
    }

    public function getFormat()
    {
        return $this->addSlashRegex('[^\/]+');
    }

    public function getRule()
    {
        if (!empty($this->rule)) {
            return '(?P<'.$this->content.'>'.$this->addSlashRegex($this->rule).')';
        }
        return '(?P<'.$this->content.'>'.$this->addSlashRegex('[^\/]+').')';
    }

    public function generate($value = null)
    {
        if (is_array($value) && isset($value[$this->content])) {
            $val = $value[$this->content];
        } elseif (!is_array($value) && !is_null($value)) {
            $val = $value;
        } else {
            $val = $this->defaults;
        }
        return $this->addSlash(urlencode($val));
    }
}
?>
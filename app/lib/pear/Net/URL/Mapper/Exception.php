<?php
/**
 * Exception classes for Net_URL_Mapper
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

/**
 * Base class for exceptions in PEAR
 */
require_once 'PEAR/Exception.php'; 

/**
 * Base class for exceptions in Net_URL_Mapper package
 *
 * Such a base class is required by the Exception RFC:
 * http://pear.php.net/pepr/pepr-proposal-show.php?id=132
 * It will rarely be thrown directly, its specialized subclasses will be
 * thrown most of the time.
 *
 * @category   Net
 * @package    Net_URL_Mapper
 * @version    Release: @package_version@
 */
class Net_URL_Mapper_Exception extends PEAR_Exception
{
}

/**
 * Exception thrown when a path is invalid
 *
 * A path can conform to a given structure, but contain invalid parameters.
 * <code>
 * $m = Net_URL_Mapper::getInstance();
 * $m->connect('hi/:name', null, array('name'=>'[a-z]+'));
 * $m->match('/hi/FOXY'); // Will throw the exception
 * </code>
 *
 * @category   Net
 * @package    Net_URL_Mapper
 * @version    Release: @package_version@
 */
class Net_URL_Mapper_InvalidException extends Net_URL_Mapper_Exception
{
    protected $path;
    protected $url;

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }
} 
?>
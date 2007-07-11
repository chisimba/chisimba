<?php

/**
 * DocBlock Generator
 *
 * PHP version 5
 *
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * + Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * + Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * + The names of its contributors may not be used to endorse or
 * promote products derived from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  PHP
 * @package   PHP_DocBlockGenerator
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PHP_DocBlockGenerator
 */

/**
 * License repository: license full name, text template and URL.
 *
 * @category  PHP
 * @package   PHP_DocBlockGenerator
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PHP_DocBlockGenerator
 */

class PHP_DocBlockGenerator_License
{
    /**
     * The licenses full names, texts and URLs
     *
     * @var    array
     * @access private
     */
    private $license = array(// /
        'apache20' => array(// /
            'full_name' => 'The Apache License, Version 2.0',
            'url' => 'http://www.apache.org/licenses/LICENSE-2.0',
            'text' => array(// /
                'Licensed under the Apache License, Version 2.0 (the "License");',
                'you may not use this file except in compliance with the License.',
                'You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0',
                'Unless required by applicable law or agreed to in writing, software',
                'distributed under the License is distributed on an "AS IS" BASIS,',
                'WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.',
                'See the License for the specific language governing permissions and',
                'limitations under the License.',
                ),
            ),
        'bsd' => array(// /
            'full_name' => 'The BSD License',
            'url' => 'http://www.opensource.org/licenses/bsd-license.php',
            'text' => array(// /
                'All rights reserved.',
                'Redistribution and use in source and binary forms, with or without modification,',
                'are permitted provided that the following conditions are met:',
                '+ Redistributions of source code must retain the above copyright notice,',
                'this list of conditions and the following disclaimer.',
                '+ Redistributions in binary form must reproduce the above copyright notice,',
                'this list of conditions and the following disclaimer in the documentation and/or',
                'other materials provided with the distribution.',
                '+ Neither the name of the <ORGANIZATION> nor the names of its contributors may be used to endorse or',
                'promote products derived from this software without specific prior written permission.',
                'THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS',
                '"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT',
                'LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR',
                'A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR',
                'CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,',
                'EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,',
                'PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR',
                'PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF',
                'LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING',
                'NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS',
                'SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.',
                ),
            ),
        'lgpl21' => array(// /
            'full_name' => 'The GNU LESSER GENERAL PUBLIC LICENSE, Version 2.1',
            'url' => 'http://www.gnu.org/copyleft/lesser.html',
            'text' => array(// /
                'This library is free software; you can redistribute it and/or',
                'modify it under the terms of the GNU Lesser General Public',
                'License as published by the Free Software Foundation; either',
                'version 2.1 of the License, or (at your option) any later version.',
                'This library is distributed in the hope that it will be useful,',
                'but WITHOUT ANY WARRANTY; without even the implied warranty of',
                'MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU',
                'Lesser General Public License for more details.',
                'You should have received a copy of the GNU Lesser General Public',
                'License along with this library; if not, write to the Free Software',
                'Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA',
                ),
            ),
        'mit' => array(// /
            'full_name' => 'The MIT License',
            'url' => 'http://www.opensource.org/licenses/mit-license.php',
            'text' => array(// /
                'Permission is hereby granted, free of charge, to any person obtaining a copy',
                'of this software and associated documentation files (the "Software"), to deal',
                'in the Software without restriction, including without limitation the rights',
                'to use, copy, modify, merge, publish, distribute, sublicense, and/or sell',
                'copies of the Software, and to permit persons to whom the Software is',
                'furnished to do so, subject to the following conditions:',
                'The above copyright notice and this permission notice shall be included in',
                'all copies or substantial portions of the Software.',
                'THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR',
                'IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,',
                'FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE',
                'AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER',
                'LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,',
                'OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN',
                'THE SOFTWARE.',
                ),
            ),
        'php301' => array(// /
            'full_name' => 'The PHP License, version 3.01',
            'url' => 'http://www.php.net/license/3_01.txt',
            'text' => array(// /
                'LICENSE: This source file is subject to version 3.01 of the PHP license',
                'that is available through the world-wide-web at the following URI:',
                'http://www.php.net/license/3_01.txt.  If you did not receive a copy ',
                'the PHP License and are unable to obtain it through the web, ',
                'send a note to license@php.net so we can mail you a copy immediately.',
                ),
            ),

 	'gpl' => array(// /
            'full_name' => 'The GNU General Public License',
            'url' => 'http://www.gnu.org/licenses/gpl-2.0.txt',
            'text' => array(// /
                'This program is free software; you can redistribute it and/or modify ',
                'it under the terms of the GNU General Public License as published by ',
                'the Free Software Foundation; either version 2 of the License, or ',
                '(at your option) any later version.',
                '',
                'This program is distributed in the hope that it will be useful, ',
                'but WITHOUT ANY WARRANTY; without even the implied warranty of ',
                'MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the ',
                'GNU General Public License for more details.',
                '',
                'You should have received a copy of the GNU General Public License ',
                'along with this program; if not, write to the ',
                'Free Software Foundation, Inc., ',
                '59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.',
                ),
            ),

        );

    /**
     * Gets the license full name
     *
     * @param  string $name the license name: apache20 | bsd | lgpl21 | mit | php301
     * @return string the license full name or null if invalid
     * @access public
     */
    public function getFullName($name)
    {
        return $this->isValid($name)? $this->license[$name]['full_name'] : '';
    }

    /**
     * Gets the license text
     *
     * @param  string $name the license name: apache20 | bsd | lgpl21 | mit | php301
     * @return string the license text or null if invalid
     * @access public
     */
    public function getText($name)
    {
        return $this->isValid($name)? $this->license[$name]['text'] : array();
    }

    /**
     * Gets the license URL
     *
     * @param  string $name the license name: apache20 | bsd | lgpl21 | mit | php301
     * @return string the license URL or null if invalid
     * @access public
     */
    public function getURL($name)
    {
        return $this->isValid($name)? $this->license[$name]['url'] : '';
    }

    /**
     * Verifies the license template is valid
     *
     * @param  string  $name the license name: apache20 | bsd | lgpl21 | mit | php301
     * @return boolean true if the license is valid, false otherwise
     * @access public
     */
    public function isValid($name)
    {
        return isset($this->license[$name]);
    }
}

?>
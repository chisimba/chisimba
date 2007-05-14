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

require_once 'Net/URL.php';
require_once 'Net/URL/Mapper/Part/Dynamic.php';
require_once 'Net/URL/Mapper/Part/Wildcard.php';
require_once 'Net/URL/Mapper/Part/Fixed.php';

class Net_URL_Mapper_Path
{
    private $path = '';
    private $N = 0;
    public $token;
    public $value;
    private $line = 1;
    private $state = 1;


    protected $alias;
    protected $rules = array();
    protected $defaults = array();
    protected $parts = array();
    protected $rule;
    protected $format;
    protected $minKeys;
    protected $maxKeys;
    protected $fixed = true;
    protected $required;

    public function __construct($path = '', $defaults = array(), $rules = array())
    {
        $this->path = '/'.trim(Net_URL::resolvePath($path), '/');
        $this->setDefaults($defaults);
        $this->setRules($rules);

        try {
            $this->parsePath();
        } catch (Exception $e) {
            // The path could not be parsed correctly, treat it as fixed
            $this->fixed = true;
            $part = self::createPart(Net_URL_Mapper_Part::FIXED, $this->path, $this->path);
            $this->parts = array($part);
        }
        $this->getRequired();
    }

    public function getPath()
    {
        return $this->path;
    }

    protected function parsePath()
    {
        while ($this->yylex()) { }
    }

    /**
    * Get the path alias
    * Path aliases can be used instead of full path
    * @return null|string
    */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
    * Set the path name
    * @param string Set the path name
    * @see getAlias()
    */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
    * Get the path parts default values
    * @return null|array
    */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
    * Set the path parts default values
    * @param array  Associative array with format partname => value
    */    
    public function setDefaults($defaults)
    {
        if (is_array($defaults)) {
            $this->defaults = $defaults;
        } else {
            $this->defaults = array();
        }
    }

    /**
    * Set the path parts default values
    * @param array  Associative array with format partname => value
    */    
    public function setRules($rules)
    {
        if (is_array($rules)) {
            $this->rules = $rules;   
        } else {
            $this->rules = array();
        }
    }

    /**
    * Returns the regular expression used to match this path
    * @return string  PERL Regular expression
    */ 
    public function getRule()
    {
        if (is_null($this->rule)) {
            $this->rule = '/^';
            foreach ($this->parts as $path => $part) {
                $this->rule .= $part->getRule();
            }
            $this->rule .= '$/';
        }
        return $this->rule;
    }

    public function getFormat()
    {
        if (is_null($this->format)) {
            $this->format = '/^';
            foreach ($this->parts as $path => $part) {
                $this->format .= $part->getFormat();
            }
            $this->format .= '$/';
        }
        return $this->format;
    }

    protected function addPart($part)
    {
        if (array_key_exists($part->content, $this->defaults)) {
            $part->setRequired(false);
            $part->setDefaults($this->defaults[$part->content]);
        }
        if (isset($this->rules[$part->content])) {
            $part->setRule($this->rules[$part->content]);
        }
        $this->rule = null;
        if ($part->getType() != Net_URL_Mapper_Part::FIXED) {
            $this->fixed = false;
            $this->parts[$part->content] = $part;
        } else {
            $this->parts[] = $part;
        }
        return $part;
    }

    public static function createPart($type, $content, $path)
    {
        switch ($type) {
            case Net_URL_Mapper_Part::DYNAMIC:
                return new Net_URL_Mapper_Part_Dynamic($content, $path);
                break;
            case Net_URL_Mapper_Part::WILDCARD:
                return new Net_URL_Mapper_Part_Wildcard($content, $path);
                break;
            default:
                return new Net_URL_Mapper_Part_Fixed($content, $path);
        }
    }

    /**
    * Checks whether the path contains the given part by name
    * If value parameter is given, the part also checks if the 
    * given value conforms to the part rule.
    * @param string Part name
    * @param mixed  The value to check against 
    */
    public function hasKey($partName, $value = null)
    {
        if (array_key_exists($partName, $this->parts)) {
            if (!is_null($value) && $value !== false) {
                return $this->parts[$partName]->match($value);
            } else {
                return true;
            }
        } elseif (array_key_exists($partName, $this->defaults) &&
            $value == $this->defaults[$partName]) {
            return true;
        }
        return false;
    }

    public function generate($values = array(), $qstring = array(), $anchor = '')
    {
        $path = '';
        foreach ($this->parts as $part) {
            $path .= $part->generate($values);
        }
        $path = '/'.trim(Net_URL::resolvePath($path), '/');
        if (!empty($qstring)) {
            $path .= '?'.http_build_query($qstring);
        }
        if (!empty($anchor)) {
            $path .= '#'.ltrim($anchor, '#');
        }
        return $path;
    }

    public function getRequired()
    {
        if (!isset($this->required)) {
            $req = array();
            foreach ($this->parts as $part) {
                if ($part->isRequired()) {
                    $req[] = $part->content;
                }
            }
            $this->required = $req;
        }
        return $this->required;
    }

    public function getMaxKeys()
    {
        if (is_null($this->maxKeys)) {
            $this->maxKeys = count($this->required);
            $this->maxKeys += count($this->defaults);
        }
        return $this->maxKeys;
    }




    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{'yylex' . $this->_yy_state}();
    }

    function yypushstate($state)
    {
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
    }

    function yypopstate()
    {
        $this->_yy_state = array_pop($this->_yy_stack);
    }

    function yybegin($state)
    {
        $this->_yy_state = $state;
    }



    function yylex1()
    {
        $tokenMap = array (
              1 => 1,
              3 => 1,
              5 => 1,
              7 => 1,
              9 => 1,
            );
        if ($this->N >= strlen($this->path)) {
            return false; // end of input
        }
        $yy_global_pattern = "/^(\/?:\/?\\(([a-zA-Z0-9_]+)\\))|^(\/?\\*\/?\\(([a-zA-Z0-9_]+)\\))|^(\/?:([a-zA-Z0-9_]+))|^(\/?\\*([a-zA-Z0-9_]+))|^(\/?([^\/:*]+))/";

        do {
            if (preg_match($yy_global_pattern, substr($this->path, $this->N), $yymatches)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        'an empty string.  Input "' . substr($this->path,
                        $this->N, 5) . '... state START');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    if ($this->N >= strlen($this->path)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {                    $yy_yymore_patterns = array(
        1 => "^(\/?\\*\/?\\(([a-zA-Z0-9_]+)\\))|^(\/?:([a-zA-Z0-9_]+))|^(\/?\\*([a-zA-Z0-9_]+))|^(\/?([^\/:*]+))",
        3 => "^(\/?:([a-zA-Z0-9_]+))|^(\/?\\*([a-zA-Z0-9_]+))|^(\/?([^\/:*]+))",
        5 => "^(\/?\\*([a-zA-Z0-9_]+))|^(\/?([^\/:*]+))",
        7 => "^(\/?([^\/:*]+))",
        9 => "",
    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        if (preg_match($yy_yymore_patterns[$this->token],
                              substr($this->path, $this->N), $yymatches)) {
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token = key($yymatches); // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count("\n", $this->value);
                        }
                    } while ($this->{'yy_r1_' . $this->token}() !== null);
                    // accept
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    return true;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->path[$this->N]);
            }
            break;
        } while (true);
    } // end function


    const START = 1;
    function yy_r1_1($yy_subpatterns)
    {

    $c = $yy_subpatterns[0];
    $part = self::createPart(Net_URL_Mapper_Part::DYNAMIC, $c, $this->value);
    $this->addPart($part);
    }
    function yy_r1_3($yy_subpatterns)
    {

    $c = $yy_subpatterns[0];
    $part = self::createPart(Net_URL_Mapper_Part::WILDCARD, $c, $this->value);
    $this->addPart($part);
    }
    function yy_r1_5($yy_subpatterns)
    {

    $c = $yy_subpatterns[0];
    $part = self::createPart(Net_URL_Mapper_Part::DYNAMIC, $c, $this->value);
    $this->addPart($part);
    }
    function yy_r1_7($yy_subpatterns)
    {

    $c = $yy_subpatterns[0];
    $part = self::createPart(Net_URL_Mapper_Part::WILDCARD, $c, $this->value);
    $this->addPart($part);
    }
    function yy_r1_9($yy_subpatterns)
    {

    $c = $yy_subpatterns[0];
    $part = self::createPart(Net_URL_Mapper_Part::FIXED, $c, $this->value);
    $this->addPart($part);
    }

}

?>
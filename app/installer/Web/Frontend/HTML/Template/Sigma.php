<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Ulf Wendel <ulf.wendel@phpdoc.de>                           |
// |          Alexey Borzov <avb@php.net>                                 |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once 'PEAR.php';

define('SIGMA_OK',                         1);
define('SIGMA_ERROR',                     -1);
define('SIGMA_TPL_NOT_FOUND',             -2);
define('SIGMA_BLOCK_NOT_FOUND',           -3);
define('SIGMA_BLOCK_DUPLICATE',           -4);
define('SIGMA_CACHE_ERROR',               -5);
define('SIGMA_UNKNOWN_OPTION',            -6);
define('SIGMA_PLACEHOLDER_NOT_FOUND',     -10);
define('SIGMA_PLACEHOLDER_DUPLICATE',     -11);
define('SIGMA_BLOCK_EXISTS',              -12);
define('SIGMA_INVALID_CALLBACK',          -13);
define('SIGMA_CALLBACK_SYNTAX_ERROR',     -14);

/**
* HTML_Template_Sigma: implementation of Integrated Templates API with 
* template 'compilation' added.
*
* The main new feature in Sigma is the template 'compilation'. Consider the
* following: when loading a template file the engine has to parse it using
* regular expressions to find all the blocks and variable placeholders. This
* is a very "expensive" operation and is definitely an overkill to do on 
* every page request: templates seldom change on production websites. This is
* where the cache kicks in: it saves an internal representation of the 
* template structure into a file and this file gets loaded instead of the 
* source one on subsequent requests (unless the source changes, of course).
* 
* While HTML_Template_Sigma inherits PHPLib Template's template syntax, it has
* an API which is easier to understand. When using HTML_Template_PHPLIB, you
* have to explicitly name a source and a target the block gets parsed into.
* This gives maximum flexibility but requires full knowledge of template 
* structure from the programmer.
* 
* Integrated Template on the other hands manages block nesting and parsing 
* itself. The engine knows that inner1 is a child of block2, there's
* no need to tell it about this:
*
* + __global__ (hidden and automatically added)
*     + block1
*     + block2
*         + inner1
*         + inner2
*
* To add content to block1 you simply type:
* <code>$tpl->setCurrentBlock("block1");</code>
* and repeat this as often as needed:
* <code>
*   $tpl->setVariable(...);
*   $tpl->parseCurrentBlock();
* </code>
*
* To add content to block2 you would type something like:
* <code>
* $tpl->setCurrentBlock("inner1");
* $tpl->setVariable(...);
* $tpl->parseCurrentBlock();
*
* $tpl->setVariable(...);
* $tpl->parseCurrentBlock();
*
* $tpl->parse("block2");
* </code>
*
* This will result in one repetition of block2 which contains two repetitions
* of inner1. inner2 will be removed if $removeEmptyBlock is set to true (which 
* is the default).
*
* Usage:
* <code>
* $tpl = new HTML_Template_Sigma( [string filerootdir], [string cacherootdir] );
*
* // load a template or set it with setTemplate()
* $tpl->loadTemplatefile( string filename [, boolean removeUnknownVariables, boolean removeEmptyBlocks] )
*
* // set "global" Variables meaning variables not beeing within a (inner) block
* $tpl->setVariable( string variablename, mixed value );
*
* // like with the HTML_Template_PHPLIB there's a second way to use setVariable()
* $tpl->setVariable( array ( string varname => mixed value ) );
*
* // Let's use any block, even a deeply nested one
* $tpl->setCurrentBlock( string blockname );
*
* // repeat this as often as you need it.
* $tpl->setVariable( array ( string varname => mixed value ) );
* $tpl->parseCurrentBlock();
*
* // get the parsed template or print it: $tpl->show()
* $html = $tpl->get();
* </code>
*
* @author   Ulf Wendel <ulf.wendel@phpdoc.de>
* @author   Alexey Borzov <avb@php.net>
* @version  $Revision$
* @access   public
* @package  HTML_Template_Sigma
*/
class HTML_Template_Sigma extends PEAR
{
   /**
    * First character of a variable placeholder ( _{_VARIABLE} ).
    * @var      string
    * @access   public
    * @see      $closingDelimiter, $blocknameRegExp, $variablenameRegExp
    */
    var $openingDelimiter = '{';

   /**
    * Last character of a variable placeholder ( {VARIABLE_}_ )
    * @var      string
    * @access   public
    * @see      $openingDelimiter, $blocknameRegExp, $variablenameRegExp
    */
    var $closingDelimiter = '}';

   /**
    * RegExp for matching the block names in the template.
    * Per default "sm" is used as the regexp modifier, "i" is missing.
    * That means a case sensitive search is done.
    * @var      string
    * @access   public
    * @see      $variablenameRegExp, $openingDelimiter, $closingDelimiter
    */
    var $blocknameRegExp = '[0-9A-Za-z_-]+';

   /**
    * RegExp matching a variable placeholder in the template.
    * Per default "sm" is used as the regexp modifier, "i" is missing.
    * That means a case sensitive search is done.
    * @var      string    
    * @access   public
    * @see      $blocknameRegExp, $openingDelimiter, $closingDelimiter
    */
    var $variablenameRegExp = '[0-9A-Za-z_-]+';

   /**
    * RegExp used to find variable placeholder, filled by the constructor
    * @var      string    Looks somewhat like @(delimiter varname delimiter)@
    * @see      HTML_Template_Sigma()
    */
    var $variablesRegExp = '';

   /**
    * RegExp used to strip unused variable placeholders
    * @see      $variablesRegExp, HTML_Template_Sigma()
    */
    var $removeVariablesRegExp = '';

   /**
    * RegExp used to find blocks and their content, filled by the constructor
    * @var      string
    * @see      HTML_Template_Sigma()
    */
    var $blockRegExp = '';

   /**
    * Controls the handling of unknown variables, default is remove
    * @var      boolean
    * @access   public
    */
    var $removeUnknownVariables = true;

   /**
    * Controls the handling of empty blocks, default is remove
    * @var      boolean
    * @access   public
    */
    var $removeEmptyBlocks = true;

   /**
    * Name of the current block
    * @var      string
    */
    var $currentBlock = '__global__';

   /**
    * Template blocks and their content
    * @var      array
    * @see      _buildBlocks()
    * @access   private
    */
    var $_blocks = array();

   /**
    * Content of parsed blocks
    * @var      array
    * @see      get(), parse()
    * @access   private
    */
    var $_parsedBlocks = array();

   /**
    * Variable names that appear in the block
    * @var      array
    * @see      _buildBlockVariables()
    * @access   private
    */
    var $_blockVariables = array();

   /**
    * Inner blocks inside the block
    * @var      array
    * @see      _buildBlocks()
    * @access   private
    */
    var $_children = array();

   /**
    * List of blocks to preserve even if they are "empty"
    * @var      array
    * @see      touchBlock(), $removeEmptyBlocks
    * @access   private
    */
    var $_touchedBlocks = array();

   /**
    * List of blocks which should not be shown even if not "empty"
    * @var      array
    * @see      hideBlock(), $removeEmptyBlocks
    * @access   private
    */
    var $_hiddenBlocks = array();

   /**
    * Variables for substitution.
    *
    * Variables are kept in this array before the replacements are done.
    * This allows automatic removal of empty blocks.
    * 
    * @var      array
    * @see      setVariable()
    * @access   private
    */
    var $_variables = array();

   /**
    * Global variables for substitution
    * 
    * These are substituted into all blocks, are not cleared on
    * block parsing and do not trigger "non-empty" logic. I.e. if 
    * only global variables are substituted into the block, it is
    * still considered "empty".
    *
    * @var      array
    * @see      setVariable(), setGlobalVariable()
    * @access   private
    */
    var $_globalVariables = array();

   /**
    * Root directory for "source" templates
    * @var    string
    * @see    HTML_Template_Sigma(), setRoot()
    */
    var $fileRoot = '';

   /**
    * Directory to store the "prepared" templates in
    * @var      string
    * @see      HTML_Template_Sigma(), setCacheRoot()
    * @access   private
    */
    var $_cacheRoot = null;

   /**
    * Flag indicating that the global block was parsed
    * @var    boolean
    */
    var $flagGlobalParsed = false;

   /**
    * Options to control some finer aspects of Sigma's work.
    * 
    * $_options['preserve_data'] If false, then substitute variables and remove empty 
    * placeholders in data passed through setVariable (see also bugs #20199, #21951)
    * $_options['trim_on_save'] Whether to trim extra whitespace from template on cache save.
    * Generally safe to have this on, unless you have <pre></pre> in templates or want to 
    * preserve HTML indentantion
    */
    var $_options = array(
        'preserve_data' => false,
        'trim_on_save'  => true
    );

   /**
    * Function name prefix used when searching for function calls in the template
    * @var    string
    */
    var $functionPrefix = 'func_';

   /**
    * Function name RegExp
    * @var    string
    */
    var $functionnameRegExp = '[_a-zA-Z]+[A-Za-z_0-9]*';

   /**
    * RegExp used to grep function calls in the template (set by the constructor)
    * @var    string
    * @see    _buildFunctionlist(), HTML_Template_Sigma()
    */
    var $functionRegExp = '';

   /**
    * List of functions found in the template.
    * @var    array
    * @access private
    */
    var $_functions = array();

   /**
    * List of callback functions specified by the user
    * @var    array
    * @access private
    */
    var $_callback = array();

   /**
    * RegExp used to find file inclusion calls in the template (should have 'e' modifier)
    * @var  string
    */
    var $includeRegExp = '#<!--\s+INCLUDE\s+(\S+)\s+-->#ime';

   /**
    * Files queued for inclusion
    * @var    array
    * @access private
    */
    var $_triggers = array();


   /**
    * Constructor: builds some complex regular expressions and optionally 
    * sets the root directories.
    *
    * Make sure that you call this constructor if you derive your template
    * class from this one.
    *
    * @param string  root directory for templates
    * @param string  directory to cache "prepared" templates in
    * @see   setRoot(), setCacheRoot()
    */
    function HTML_Template_Sigma($root = '', $cacheRoot = '')
    {
        // the class is inherited from PEAR to be able to use $this->setErrorHandling()
        $this->PEAR();
        $this->variablesRegExp       = '@' . $this->openingDelimiter . '(' . $this->variablenameRegExp . ')' .
                                       '(:(' . $this->functionnameRegExp . '))?' . $this->closingDelimiter . '@sm';
        $this->removeVariablesRegExp = '@'.$this->openingDelimiter.'\s*('.$this->variablenameRegExp.')\s*'.$this->closingDelimiter.'@sm';
        $this->blockRegExp           = '@<!--\s+BEGIN\s+('.$this->blocknameRegExp.')\s+-->(.*)<!--\s+END\s+\1\s+-->@sm';
        $this->functionRegExp        = '@' . $this->functionPrefix . '(' . $this->functionnameRegExp . ')\s*\(@sm';
        $this->setRoot($root);
        $this->setCacheRoot($cacheRoot);

        $this->setCallbackFunction('h', 'htmlspecialchars');
        $this->setCallbackFunction('u', 'urlencode');
        $this->setCallbackFunction('j', array(&$this, '_jsEscape'));
    }


   /**
    * Sets the file root for templates. The file root gets prefixed to all 
    * filenames passed to the object.
    * 
    * @param    string  directory name
    * @see      HTML_Template_Sigma()
    * @access   public
    */
    function setRoot($root)
    {
        if (('' != $root) && ('/' != substr($root, -1))) {
            $root .= '/';
        }
        $this->fileRoot = $root;
    }


   /**
    * Sets the directory to cache "prepared" templates in, the directory should be writable for PHP.
    * 
    * The "prepared" template contains an internal representation of template 
    * structure: essentially a serialized array of $_blocks, $_blockVariables, 
    * $_children and $_functions, may also contain $_triggers. This allows 
    * to bypass expensive calls to _buildBlockVariables() and especially 
    * _buildBlocks() when reading the "prepared" template instead of 
    * the "source" one.
    * 
    * The files in this cache do not have any TTL and are regenerated when the
    * source templates change.
    * 
    * @param    string  directory name
    * @see      HTML_Template_Sigma(), _getCached(), _writeCache()
    * @access   public
    */
    function setCacheRoot($root)
    {
        if (empty($root)) {
            return true;
        } elseif (('' != $root) && ('/' != substr($root, -1))) {
            $root .= '/';
        }
        $this->_cacheRoot = $root;
    }


   /**
    * Sets the option for the template class
    * 
    * @access public
    * @param  string  option name
    * @param  mixed   option value
    * @return mixed   SIGMA_OK on success, error object on failure
    */
    function setOption($option, $value)
    {
        if (isset($this->_options[$option])) {
            $this->_options[$option] = $value;
            return SIGMA_OK;
        }
        return $this->raiseError($this->errorMessage(SIGMA_UNKNOWN_OPTION, $option), SIGMA_UNKNOWN_OPTION);
    }


   /**
    * Returns a textual error message for an error code
    *  
    * @access public
    * @param  integer  error code
    * @param  string   additional data to insert into message
    * @return string   error message
    */
    function errorMessage($code, $data = null)
    {
        static $errorMessages;
        if (!isset($errorMessages)) {
            $errorMessages = array(
                SIGMA_ERROR                 => 'unknown error',
                SIGMA_OK                    => '',
                SIGMA_TPL_NOT_FOUND         => 'Cannot read the template file \'%s\'',
                SIGMA_BLOCK_NOT_FOUND       => 'Cannot find block \'%s\'',
                SIGMA_BLOCK_DUPLICATE       => 'The name of a block must be unique within a template. Block \'%s\' found twice.',
                SIGMA_CACHE_ERROR           => 'Cannot save template file \'%s\'',
                SIGMA_UNKNOWN_OPTION        => 'Unknown option \'%s\'',
                SIGMA_PLACEHOLDER_NOT_FOUND => 'Variable placeholder \'%s\' not found',
                SIGMA_PLACEHOLDER_DUPLICATE => 'Placeholder \'%s\' should be unique, found in multiple blocks',
                SIGMA_BLOCK_EXISTS          => 'Block \'%s\' already exists',
                SIGMA_INVALID_CALLBACK      => 'Callback does not exist',
                SIGMA_CALLBACK_SYNTAX_ERROR => 'Cannot parse template function: %s'
            );
        }

        if (PEAR::isError($code)) {
            $code = $code->getCode();
        }
        if (!isset($errorMessages[$code])) {
            return $errorMessages[SIGMA_ERROR];
        } else {
            return (null === $data)? $errorMessages[$code]: sprintf($errorMessages[$code], $data);
        }
    }


   /**
    * Prints a block with all replacements done.
    * 
    * @access  public
    * @param   string  block name
    * @see     get()
    */
    function show($block = '__global__')
    {
        print $this->get($block);
    }


   /**
    * Returns a block with all replacements done.
    * 
    * @param    string     block name
    * @param    bool       whether to clear parsed block contents
    * @return   string     block with all replacements done
    * @throws   PEAR_Error
    * @access   public
    * @see      show()
    */
    function get($block = '__global__', $clear = false)
    {
        if (!isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $block), SIGMA_BLOCK_NOT_FOUND);
        }
        if ('__global__' == $block && !$this->flagGlobalParsed) {
            $this->parse('__global__');
        }
        // return the parsed block, removing the unknown placeholders if needed
        if (!isset($this->_parsedBlocks[$block])) {
            return '';

        } else {
            $ret = $this->_parsedBlocks[$block];
            if ($clear) {
                unset($this->_parsedBlocks[$block]);
            }
            if ($this->removeUnknownVariables) {
                $ret = preg_replace($this->removeVariablesRegExp, '', $ret);
            }
            if ($this->_options['preserve_data']) {
                $ret = str_replace($this->openingDelimiter . '%preserved%' . $this->closingDelimiter, $this->openingDelimiter, $ret);
            }
            return $ret;
        }
    }


   /**
    * Parses the given block.
    *    
    * @param    string    block name
    * @param    boolean   true if the function is called recursively (do not set this to true yourself!)
    * @param    boolean   true if parsing a "hidden" block (do not set this to true yourself!)
    * @access   public
    * @see      parseCurrentBlock()
    * @throws   PEAR_Error
    */
    function parse($block = '__global__', $flagRecursion = false, $fakeParse = false)
    {
        static $vars;

        if (!isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $block), SIGMA_BLOCK_NOT_FOUND);
        }
        if ('__global__' == $block) {
            $this->flagGlobalParsed = true;
        }
        if (!isset($this->_parsedBlocks[$block])) {
            $this->_parsedBlocks[$block] = '';
        }
        $outer = $this->_blocks[$block];

        if (!$flagRecursion) {
            $vars = array();
        }
        // block is not empty if its local var is substituted
        $empty = true;
        foreach ($this->_blockVariables[$block] as $allowedvar => $v) {
            if (isset($this->_variables[$allowedvar])) {
                $vars[$this->openingDelimiter . $allowedvar . $this->closingDelimiter] = $this->_variables[$allowedvar];
                $empty = false;
                // vital for checking "empty/nonempty" status
                unset($this->_variables[$allowedvar]);
            }
        }

        // processing of the inner blocks
        if (isset($this->_children[$block])) {
            foreach ($this->_children[$block] as $innerblock => $v) {
                $placeholder = $this->openingDelimiter.'__'.$innerblock.'__'.$this->closingDelimiter;

                if (isset($this->_hiddenBlocks[$innerblock])) {
                    // don't bother actually parsing this inner block; but we _have_
                    // to go through its local vars to prevent problems on next iteration
                    $this->parse($innerblock, true, true);
                    unset($this->_hiddenBlocks[$innerblock]);
                    $outer = str_replace($placeholder, '', $outer);

                } else {
                    $this->parse($innerblock, true, $fakeParse);
                    // block is not empty if its inner block is not empty
                    if ('' != $this->_parsedBlocks[$innerblock]) {
                        $empty = false;
                    }

                    $outer = str_replace($placeholder, $this->_parsedBlocks[$innerblock], $outer);
                    $this->_parsedBlocks[$innerblock] = '';
                }
            }
        }

        // add "global" variables to the static array
        foreach ($this->_globalVariables as $allowedvar => $value) {
            if (isset($this->_blockVariables[$block][$allowedvar])) {
                $vars[$this->openingDelimiter . $allowedvar . $this->closingDelimiter] = $value;
            }
        }
        // if we are inside a hidden block, don't bother
        if (!$fakeParse) {
            if (0 != count($vars) && (!$flagRecursion || !empty($this->_functions[$block]))) {
                $varKeys     = array_keys($vars);
                $varValues   = $this->_options['preserve_data']? array_map(array(&$this, '_preserveOpeningDelimiter'), array_values($vars)): array_values($vars);
            }

            // check whether the block is considered "empty" and append parsed content if not
            if (!$empty || ('__global__' == $block) || !$this->removeEmptyBlocks || isset($this->_touchedBlocks[$block])) {
                // perform callbacks
                if (!empty($this->_functions[$block])) {
                    foreach ($this->_functions[$block] as $id => $data) {
                        $placeholder = $this->openingDelimiter . '__function_' . $id . '__' . $this->closingDelimiter;
                        // do not waste time calling function more than once
                        if (!isset($vars[$placeholder])) {
                            $args         = array();
                            $preserveArgs = isset($this->_callback[$data['name']]['preserveArgs']) && $this->_callback[$data['name']]['preserveArgs'];
                            foreach ($data['args'] as $arg) {
                                $args[] = (empty($varKeys) || $preserveArgs)? $arg: str_replace($varKeys, $varValues, $arg);
                            }
                            if (isset($this->_callback[$data['name']]['data'])) {
                                $res = call_user_func_array($this->_callback[$data['name']]['data'], $args);
                            } else {
                                $res = isset($args[0])? $args[0]: '';
                            }
                            $outer = str_replace($placeholder, $res, $outer);
                            // save the result to variable cache, it can be requested somewhere else
                            $vars[$placeholder] = $res;
                        }
                    }
                }
                // substitute variables only on non-recursive call, thus all
                // variables from all inner blocks get substituted
                if (!$flagRecursion && !empty($varKeys)) {
                    $outer = str_replace($varKeys, $varValues, $outer);
                }

                $this->_parsedBlocks[$block] .= $outer;
                if (isset($this->_touchedBlocks[$block])) {
                    unset($this->_touchedBlocks[$block]);
                }
            }
        }
        return $empty;
    }


   /**
    * Sets a variable value.
    * 
    * The function can be used either like setVariable("varname", "value")
    * or with one array $variables["varname"] = "value" given setVariable($variables)
    * 
    * @access public
    * @param  mixed     variable name or array ('varname'=>'value')
    * @param  string    variable value if $variable is not an array
    */
    function setVariable($variable, $value = '')
    {
        if (is_array($variable)) {
            $this->_variables = array_merge($this->_variables, $variable);
        } else {
            $this->_variables[$variable] = $value;
        }
    }


   /**
    * Sets a global variable value.
    * 
    * @access public
    * @param  mixed     variable name or array ('varname'=>'value')
    * @param  string    variable value if $variable is not an array
    * @see    setVariable()
    */
    function setGlobalVariable($variable, $value = '')
    {
        if (is_array($variable)) {
            $this->_globalVariables = array_merge($this->_globalVariables, $variable);
        } else {
            $this->_globalVariables[$variable] = $value;
        }
    }


   /**
    * Sets the name of the current block: the block where variables are added
    *
    * @param    string      block name
    * @return   mixed       SIGMA_OK on success, error object on failure
    * @throws   PEAR_Error
    * @access   public
    */
    function setCurrentBlock($block = '__global__')
    {
        if (!isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $block), SIGMA_BLOCK_NOT_FOUND);
        }
        $this->currentBlock = $block;
        return SIGMA_OK;
    }


   /**
    * Parses the current block
    * 
    * @see      parse(), setCurrentBlock()
    * @access   public
    */
    function parseCurrentBlock()
    {
        return $this->parse($this->currentBlock);
    }


   /**
    * Returns the current block name
    *
    * @return string    block name
    * @access public
    */
    function getCurrentBlock()
    {
        return $this->currentBlock;
    }


   /**
    * Preserves the block even if empty blocks should be removed.
    *
    * Sometimes you have blocks that should be preserved although they are 
    * empty (no placeholder replaced). Think of a shopping basket. If it's 
    * empty you have to show a message to the user. If it's filled you have
    * to show the contents of the shopping basket. Now where to place the 
    * message that the basket is empty? It's not a good idea to place it 
    * in you application as customers tend to like unecessary minor text
    * changes. Having another template file for an empty basket means that 
    * one fine day the filled and empty basket templates will have different
    * layouts. 
    * 
    * So blocks that do not contain any placeholders but only messages like 
    * "Your shopping basked is empty" are intoduced. Now if there is no 
    * replacement done in such a block the block will be recognized as "empty"
    * and by default ($removeEmptyBlocks = true) be stripped off. To avoid this
    * you can call touchBlock()
    *
    * @param    string      block name
    * @return   mixed       SIGMA_OK on success, error object on failure
    * @throws   PEAR_Error    
    * @access   public
    * @see      $removeEmptyBlocks, $_touchedBlocks
    */
    function touchBlock($block)
    {
        if (!isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $block), SIGMA_BLOCK_NOT_FOUND);
        }
        if (isset($this->_hiddenBlocks[$block])) {
            unset($this->_hiddenBlocks[$block]);
        }
        $this->_touchedBlocks[$block] = true;
        return SIGMA_OK;
    }


   /**
    * Hides the block even if it is not "empty".
    * 
    * Is somewhat an opposite to touchBlock().
    * 
    * Consider a block (a 'edit' link for example) that should be visible to 
    * registered/"special" users only, but its visibility is triggered by 
    * some little 'id' field passed in a large array into setVariable(). You 
    * can either carefully juggle your variables to prevent the block from 
    * appearing (a fragile solution) or simply call hideBlock()
    *
    * @param    string      block name
    * @return   mixed       SIGMA_OK on success, error object on failure
    * @throws   PEAR_Error    
    * @access   public
    */
    function hideBlock($block)
    {
        if (!isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $block), SIGMA_BLOCK_NOT_FOUND);
        }
        if (isset($this->_touchedBlocks[$block])) {
            unset($this->_touchedBlocks[$block]);
        }
        $this->_hiddenBlocks[$block] = true;
        return SIGMA_OK;
    }


   /**
    * Sets the template.
    *
    * You can either load a template file from disk with LoadTemplatefile() or set the
    * template manually using this function.
    * 
    * @access public
    * @param  string      template content
    * @param  boolean     remove unknown/unused variables?
    * @param  boolean     remove empty blocks?
    * @return mixed       SIGMA_OK on success, error object on failure
    * @see    loadTemplatefile()
    */
    function setTemplate($template, $removeUnknownVariables = true, $removeEmptyBlocks = true)
    {
        $this->_resetTemplate($removeUnknownVariables, $removeEmptyBlocks);
        $list = $this->_buildBlocks('<!-- BEGIN __global__ -->'.$template.'<!-- END __global__ -->');
        if (PEAR::isError($list)) {
            return $list;
        }
        return $this->_buildBlockVariables();
    }


   /**
    * Loads a template file.
    * 
    * If caching is on, then it checks whether a "prepared" template exists.
    * If it does, it gets loaded instead of the original, if it does not, then
    * the original gets loaded and prepared and then the prepared version is saved.
    * addBlockfile() and replaceBlockfile() implement quite the same logic.
    *
    * @param    string      filename
    * @param    boolean     remove unknown/unused variables?
    * @param    boolean     remove empty blocks?
    * @access   public
    * @return   mixed       SIGMA_OK on success, error object on failure
    * @see      setTemplate(), $removeUnknownVariables, $removeEmptyBlocks
    */
    function loadTemplateFile($filename, $removeUnknownVariables = true, $removeEmptyBlocks = true)
    {
        if ($this->_isCached($filename)) {
            $this->_resetTemplate($removeUnknownVariables, $removeEmptyBlocks);
            return $this->_getCached($filename);
        }
        $template = $this->_getFile($this->_sourceName($filename));
        if (PEAR::isError($template)) {
            return $template;
        }
        $this->_triggers = array();
        $template = preg_replace($this->includeRegExp, "\$this->_makeTrigger('\\1', '__global__')", $template);
        if (SIGMA_OK !== ($res = $this->setTemplate($template, $removeUnknownVariables, $removeEmptyBlocks))) {
            return $res;
        } else {
            return $this->_writeCache($filename, '__global__');
        }
    }


   /**
    * Adds a block to the template changing a variable placeholder to a block placeholder.
    *
    * This means that a new block will be integrated into the template in
    * place of a variable placeholder. The variable placeholder will be 
    * removed and the new block will behave in the same way as if it was 
    * inside the original template.
    *
    * The block content must not start with <!-- BEGIN blockname --> and end with
    * <!-- END blockname -->, if it does the error will be thrown.
    * 
    * @param    string    name of the variable placeholder, the name must be unique within the template.
    * @param    string    name of the block to be added
    * @param    string    content of the block
    * @return   mixed     SIGMA_OK on success, error object on failure
    * @throws   PEAR_Error
    * @see      addBlockfile()
    * @access   public
    */
    function addBlock($placeholder, $block, $template)
    {
        if (isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_EXISTS, $block), SIGMA_BLOCK_EXISTS);
        }
        $parents = $this->_findParentBlocks($placeholder);
        if (0 == count($parents)) {
            return $this->raiseError($this->errorMessage(SIGMA_PLACEHOLDER_NOT_FOUND, $placeholder), SIGMA_PLACEHOLDER_NOT_FOUND);
        } elseif (count($parents) > 1) {
            return $this->raiseError($this->errorMessage(SIGMA_PLACEHOLDER_DUPLICATE, $placeholder), SIGMA_PLACEHOLDER_DUPLICATE);
        }
        
        $template = "<!-- BEGIN $block -->" . $template . "<!-- END $block -->";
        $list     = $this->_buildBlocks($template);
        if (PEAR::isError($list)) {
            return $list;
        }
        $this->_replacePlaceholder($parents[0], $placeholder, $block);
        return $this->_buildBlockVariables($block);
    }
    

   /**
    * Adds a block taken from a file to the template, changing a variable placeholder 
    * to a block placeholder.
    * 
    * @param      string    name of the variable placeholder
    * @param      string    name of the block to be added
    * @param      string    template file that contains the block
    * @return     mixed     SIGMA_OK on success, error object on failure
    * @throws     PEAR_Error
    * @see        addBlock()
    * @access     public
    */
    function addBlockfile($placeholder, $block, $filename)
    {
        if ($this->_isCached($filename)) {
            return $this->_getCached($filename, $block, $placeholder);
        }
        $template = $this->_getFile($this->_sourceName($filename));
        if (PEAR::isError($template)) {
            return $template;
        }
        $template = preg_replace($this->includeRegExp, "\$this->_makeTrigger('\\1', '{$block}')", $template);
        if (SIGMA_OK !== ($res = $this->addBlock($placeholder, $block, $template))) {
            return $res;
        } else {
            return $this->_writeCache($filename, $block);
        }
    }


   /**
    * Replaces an existing block with new content.
    * 
    * This function will replace a block of the template and all blocks 
    * contained in it and add a new block instead. This means you can 
    * dynamically change your template.
    * 
    * Sigma analyses the way you've nested blocks and knows which block 
    * belongs into another block. This nesting information helps to make the 
    * API short and simple. Replacing blocks does not only mean that Sigma 
    * has to update the nesting information (relatively time consuming task) 
    * but you have to make sure that you do not get confused due to the 
    * template change yourself.
    * 
    * @param   string    name of a block to replace
    * @param   string    new content
    * @param   boolean   true if the parsed contents of the block should be kept
    * @access  public
    * @see     replaceBlockfile(), addBlock()
    * @return  mixed     SIGMA_OK on success, error object on failure
    * @throws  PEAR_Error
    */
    function replaceBlock($block, $template, $keepContent = false)
    {
        if (!isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $block), SIGMA_BLOCK_NOT_FOUND);
        }
        // should not throw a error as we already checked for block existance
        $this->_removeBlockData($block, $keepContent);
        $template = "<!-- BEGIN $block -->" . $template . "<!-- END $block -->";

        $list = $this->_buildBlocks($template);
        if (PEAR::isError($list)) {
            return $list;
        }
        // renew the variables list
        return $this->_buildBlockVariables($block);
    }


   /**
    * Replaces an existing block with new content from a file.
    * 
    * @access     public
    * @param      string    name of a block to replace
    * @param      string    template file that contains the block
    * @param      boolean   true if the parsed contents of the block should be kept
    * @return     mixed     SIGMA_OK on success, error object on failure
    * @throws     PEAR_Error
    * @see        replaceBlock(), addBlockfile()
    */
    function replaceBlockfile($block, $filename, $keepContent = false)
    {
        if ($this->_isCached($filename)) {
            if (PEAR::isError($res = $this->_removeBlockData($block, $keepContent))) {
                return $res;
            } else {
                return $this->_getCached($filename, $block);
            }
        }
        $template = $this->_getFile($this->_sourceName($filename));
        if (PEAR::isError($template)) {
            return $template;
        }
        $template = preg_replace($this->includeRegExp, "\$this->_makeTrigger('\\1', '{$block}')", $template);
        if (SIGMA_OK !== ($res = $this->replaceBlock($block, $template, $keepContent))) {
            return $res;
        } else {
            return $this->_writeCache($filename, $block);
        }
    }


   /**
    * Checks if the block exists in the template
    *
    * @param  string  block name
    * @return bool
    * @access public
    */
    function blockExists($block)
    {
        return isset($this->_blocks[$block]);
    }


   /**
    * Returns the name of the (first) block that contains the specified placeholder.
    *
    * @param    string  Name of the placeholder you're searching
    * @param    string  Name of the block to scan. If left out (default) all blocks are scanned.
    * @return   string  Name of the (first) block that contains the specified placeholder.
    *                   If the placeholder was not found an empty string is returned.
    * @access   public
    * @throws   PEAR_Error
    */
    function placeholderExists($placeholder, $block = '')
    {
        if ('' != $block && !isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $block), SIGMA_BLOCK_NOT_FOUND);
        }
        if ('' != $block) {
            // if we search in the specific block, we should just check the array
            return isset($this->_blockVariables[$block][$placeholder])? $block: '';
        } else {
            // _findParentBlocks returns an array, we need only the first element
            $parents = $this->_findParentBlocks($placeholder);
            return empty($parents)? '': $parents[0];
        }
    } // end func placeholderExists


   /**
    * Sets a callback function.
    *
    * Sigma templates can contain simple function calls. This means that the 
    * author of the template can add a special placeholder to it: 
    * func_h1("embedded in h1")
    * Sigma will parse the template for these placeholders and will allow 
    * you to define a callback function for them. Callback will be called 
    * automatically when the block containing such function call is parse()'d.
    *
    * Please note that arguments to these template functions can contain 
    * variable placeholders: func_translate('Hello, {username}'), but not 
    * blocks or other function calls.
    * 
    * This should NOT be used to add logic (except some presentation one) to
    * the template. If you use a lot of such callbacks and implement business
    * logic through them, then you're reinventing the wheel. Consider using
    * XML/XSLT, native PHP or some other template engine.
    *
    * <?php
    * function h_one($arg) {
    *    return '<h1>' . $arg . '</h1>';
    * }
    * ...
    * $tpl = new HTML_Template_Sigma( ... );
    * ...
    * $tpl->setCallbackFunction('h1', 'h_one');
    * ?>
    *
    * template:
    * func_h1('H1 Headline');
    *
    * @param    string    Function name in the template
    * @param    mixed     A callback: anything that can be passed to call_user_func_array()
    * @param    bool      If true, then no variable substitution in arguments will take place before function call
    * @return   mixed     SIGMA_OK on success, error object on failure
    * @throws   PEAR_Error
    * @access   public
    */
    function setCallbackFunction($tplFunction, $callback, $preserveArgs = false)
    {
        if (!is_callable($callback)) {
            return $this->raiseError($this->errorMessage(SIGMA_INVALID_CALLBACK), SIGMA_INVALID_CALLBACK);
        }
        $this->_callback[$tplFunction] = array(
            'data'         => $callback,
            'preserveArgs' => $preserveArgs
        );
        return SIGMA_OK;
    } // end func setCallbackFunction


   /**
    * Returns a list of blocks within a template.
    *
    * If $recursive is false, it returns just a 'flat' array of $parent's
    * direct subblocks. If $recursive is true, it builds a tree of template
    * blocks using $parent as root. Tree structure is compatible with 
    * PEAR::Tree's Memory_Array driver.
    * 
    * @param    string  parent block name 
    * @param    bool    whether to return a tree of child blocks (true) or a 'flat' array (false)
    * @access   public
    * @return   array   a list of child blocks
    * @throws   PEAR_Error
    */
    function getBlockList($parent = '__global__', $recursive = false)
    {
        if (!isset($this->_blocks[$parent])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $parent), SIGMA_BLOCK_NOT_FOUND);
        }
        if (!$recursive) {
            return isset($this->_children[$parent])? array_keys($this->_children[$parent]): array();
        } else {
            $ret = array('name' => $parent);
            if (!empty($this->_children[$parent])) {
                $ret['children'] = array();
                foreach (array_keys($this->_children[$parent]) as $child) {
                    $ret['children'][] = $this->getBlockList($child, true);
                }
            }
            return $ret;
        }
    }


   /**
    * Returns a list of placeholders within a block.
    * 
    * Only 'normal' placeholders are returned, not auto-created ones.
    *
    * @param    string  block name
    * @access   public
    * @return   array   a list of placeholders
    * @throws   PEAR_Error
    */
    function getPlaceholderList($block = '__global__')
    {
        if (!isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $block), SIGMA_BLOCK_NOT_FOUND);
        }
        $ret = array();
        foreach ($this->_blockVariables[$block] as $var => $v) {
            if ('__' != substr($var, 0, 2) || '__' != substr($var, -2)) {
                $ret[] = $var;
            }
        }
        return $ret;
    }


   /**
    * Clears the variables
    * 
    * Global variables are not affected. The method is useful when you add
    * a lot of variables via setVariable() and are not sure whether all of 
    * them appear in the block you parse(). If you clear the variables after
    * parse(), you don't risk them suddenly showing up in other blocks.
    * 
    * @access public
    * @see    setVariable()
    */
    function clearVariables()
    {
        $this->_variables = array();
    }


    //------------------------------------------------------------
    //
    // Private methods follow
    //
    //------------------------------------------------------------


   /**
    * Reads the file and returns its content
    * 
    * @param    string    filename
    * @return   string    file content (or error object)
    * @access   private
    */    
    function _getFile($filename)
    {
        if (!($fh = @fopen($filename, 'r'))) {
            return $this->raiseError($this->errorMessage(SIGMA_TPL_NOT_FOUND, $filename), SIGMA_TPL_NOT_FOUND);
        }
        $content = fread($fh, max(1, filesize($filename)));
        fclose($fh);
        return $content;
    }


   /**
    * Recursively builds a list of all variables within a block.
    *
    * Also calls _buildFunctionlist() for each block it visits
    * 
    * @param    string block name
    * @see      _buildFunctionlist()
    * @access   private
    */
    function _buildBlockVariables($block = '__global__')
    {
        $this->_blockVariables[$block] = array();
        $this->_functions[$block]      = array();
        preg_match_all($this->variablesRegExp, $this->_blocks[$block], $regs, PREG_SET_ORDER);
        foreach ($regs as $match) {
            $this->_blockVariables[$block][$match[1]] = true;
            if (!empty($match[3])) {
                $funcData = array(
                    'name' => $match[3],
                    'args' => array($this->openingDelimiter . $match[1] . $this->closingDelimiter)
                );
                $funcId   = substr(md5(serialize($funcData)), 0, 10);

                // update block info
                $this->_blocks[$block] = str_replace($match[0], $this->openingDelimiter . '__function_' . $funcId . '__' . $this->closingDelimiter, $this->_blocks[$block]);
                $this->_blockVariables[$block]['__function_' . $funcId . '__'] = true;
                $this->_functions[$block][$funcId] = $funcData;
            }
        }
        if (SIGMA_OK != ($res = $this->_buildFunctionlist($block))) {
            return $res;
        }
        if (isset($this->_children[$block]) && is_array($this->_children[$block])) {
            foreach ($this->_children[$block] as $child => $v) {
                if (SIGMA_OK != ($res = $this->_buildBlockVariables($child))) {
                    return $res;
                }
            }
        }
        return SIGMA_OK;
    }


   /**
    * Recusively builds a list of all blocks within the template.
    * 
    * @param    string    template to be scanned
    * @see      $_blocks
    * @throws   PEAR_Error
    * @return   mixed     array of block names on success or error object on failure
    * @access   private
    */
    function _buildBlocks($string)
    {
        $blocks = array();
        if (preg_match_all($this->blockRegExp, $string, $regs, PREG_SET_ORDER)) {
            foreach ($regs as $k => $match) {
                $blockname    = $match[1];
                $blockcontent = $match[2];
                if (isset($this->_blocks[$blockname]) || isset($blocks[$blockname])) {
                    return $this->raiseError($this->errorMessage(SIGMA_BLOCK_DUPLICATE, $blockname), SIGMA_BLOCK_DUPLICATE);
                }
                $this->_blocks[$blockname] = $blockcontent;
                $blocks[$blockname] = true;
                $inner              = $this->_buildBlocks($blockcontent);
                if (PEAR::isError($inner)) {
                    return $inner;
                }
                foreach ($inner as $name => $v) {
                    $pattern     = sprintf('@<!--\s+BEGIN\s+%s\s+-->(.*)<!--\s+END\s+%s\s+-->@sm', $name, $name);
                    $replacement = $this->openingDelimiter.'__'.$name.'__'.$this->closingDelimiter;
                    $this->_blocks[$blockname]          = preg_replace($pattern, $replacement, $this->_blocks[$blockname]);
                    $this->_children[$blockname][$name] = true;
                }
            }
        }
        return $blocks;
    }


   /**
    * Resets the object's properties, used before processing a new template
    *
    * @access   private
    * @param    boolean     remove unknown/unused variables?
    * @param    boolean     remove empty blocks?
    * @see      setTemplate(), loadTemplateFile()
    * @access   private
    */
    function _resetTemplate($removeUnknownVariables = true, $removeEmptyBlocks = true)
    {
        $this->removeUnknownVariables = $removeUnknownVariables;
        $this->removeEmptyBlocks      = $removeEmptyBlocks;
        $this->currentBlock           = '__global__';
        $this->_variables             = array();
        $this->_blocks                = array();
        $this->_children              = array();
        $this->_parsedBlocks          = array();
        $this->_touchedBlocks         = array();
        $this->_functions             = array();
        $this->flagGlobalParsed       = false;
    } // _resetTemplate


   /**
    * Checks whether we have a "prepared" template cached.
    * 
    * If we do not do caching, always returns false
    * 
    * @access private
    * @param  string source filename
    * @return bool yes/no
    * @see loadTemplatefile(), addBlockfile(), replaceBlockfile()
    */
    function _isCached($filename)
    {
        if (null === $this->_cacheRoot) {
            return false;
        }
        $cachedName = $this->_cachedName($filename);
        $sourceName = $this->_sourceName($filename);
        // if $sourceName does not exist, error will be thrown later
        $sourceTime = @filemtime($sourceName);
        if ((false !== $sourceTime) && @file_exists($cachedName) && (filemtime($cachedName) > $sourceTime)) {
            return true;
        } else {
            return false;
        }
    } // _isCached


   /**
    * Loads a "prepared" template file
    *
    * @access   private
    * @param    string  filename
    * @param    string  block name
    * @param    string  variable placeholder to replace by a block
    * @return   mixed   SIGMA_OK on success, error object on failure
    * @see loadTemplatefile(), addBlockfile(), replaceBlockfile()
    */
    function _getCached($filename, $block = '__global__', $placeholder = '')
    {
        // the same checks are done in addBlock()
        if (!empty($placeholder)) {
            if (isset($this->_blocks[$block])) {
                return $this->raiseError($this->errorMessage(SIGMA_BLOCK_EXISTS, $block), SIGMA_BLOCK_EXISTS);
            }
            $parents = $this->_findParentBlocks($placeholder);
            if (0 == count($parents)) {
                return $this->raiseError($this->errorMessage(SIGMA_PLACEHOLDER_NOT_FOUND, $placeholder), SIGMA_PLACEHOLDER_NOT_FOUND);
            } elseif (count($parents) > 1) {
                return $this->raiseError($this->errorMessage(SIGMA_PLACEHOLDER_DUPLICATE, $placeholder), SIGMA_PLACEHOLDER_DUPLICATE);
            }
        }
        $content = $this->_getFile($this->_cachedName($filename));
        if (PEAR::isError($content)) {
            return $content;
        }
        $cache = unserialize($content);
        if ('__global__' != $block) {
            $this->_blocks[$block]         = $cache['blocks']['__global__'];
            $this->_blockVariables[$block] = $cache['variables']['__global__'];
            $this->_children[$block]       = $cache['children']['__global__'];
            $this->_functions[$block]      = $cache['functions']['__global__'];
            unset($cache['blocks']['__global__'], $cache['variables']['__global__'], $cache['children']['__global__'], $cache['functions']['__global__']);
        }
        $this->_blocks         = array_merge($this->_blocks, $cache['blocks']);
        $this->_blockVariables = array_merge($this->_blockVariables, $cache['variables']);
        $this->_children       = array_merge($this->_children, $cache['children']);
        $this->_functions      = array_merge($this->_functions, $cache['functions']);

        // the same thing gets done in addBlockfile()
        if (!empty($placeholder)) {
            $this->_replacePlaceholder($parents[0], $placeholder, $block);
        }
        // pull the triggers, if any
        if (isset($cache['triggers'])) {
            return $this->_pullTriggers($cache['triggers']);
        }
        return SIGMA_OK;
    } // _getCached


   /**
    * Returns a full name of a "prepared" template file
    * 
    * @access private
    * @param string  source filename, relative to root directory
    * @return string filename
    */
    function _cachedName($filename)
    {
        if ('/' == $filename{0} && '/' == substr($this->_cacheRoot, -1)) {
            $filename = substr($filename, 1);
        }
        $filename = str_replace('/', '__', $filename);
        return $this->_cacheRoot. $filename. '.it';
    } // _cachedName


   /**
    * Returns a full name of a "source" template file
    *
    * @param string   source filename, relative to root directory
    * @access private
    * @return string
    */
    function _sourceName($filename)
    {
        if ('/' == $filename{0} && '/' == substr($this->fileRoot, -1)) {
            $filename = substr($filename, 1);
        }
        return $this->fileRoot . $filename;
    } // _sourceName


   /**
    * Writes a prepared template file.
    * 
    * Even if NO caching is going on, this method has a side effect: it calls 
    * the _pullTriggers() method and thus loads all files added via <!-- INCLUDE -->
    *
    * @access private
    * @param string   source filename, relative to root directory
    * @param string   name of the block to save into file
    * @return mixed   SIGMA_OK on success, error object on failure
    */
    function _writeCache($filename, $block)
    {
        // do not save anything if no cache dir, but do pull triggers
        if (null !== $this->_cacheRoot) {
            $cache = array(
                'blocks'    => array(),
                'variables' => array(),
                'children'  => array(),
                'functions' => array()
            );
            $cachedName = $this->_cachedName($filename);
            $this->_buildCache($cache, $block);
            if ('__global__' != $block) {
                foreach (array_keys($cache) as $k) {
                    $cache[$k]['__global__'] = $cache[$k][$block];
                    unset($cache[$k][$block]);
                }
            }
            if (isset($this->_triggers[$block])) {
                $cache['triggers'] = $this->_triggers[$block];
            }
            if (!($fh = @fopen($cachedName, 'w'))) {
                return $this->raiseError($this->errorMessage(SIGMA_CACHE_ERROR, $cachedName), SIGMA_CACHE_ERROR);
            }
            fwrite($fh, serialize($cache));
            fclose($fh);
        }
        // now pull triggers
        if (isset($this->_triggers[$block])) {
            if (SIGMA_OK !== ($res = $this->_pullTriggers($this->_triggers[$block]))) {
                return $res;
            }
            unset($this->_triggers[$block]);
        }
        return SIGMA_OK;
    } // _writeCache


   /**
    * Builds an array of template data to be saved in prepared template file
    *
    * @access private
    * @param array   template data
    * @param string  block to add to the array
    */
    function _buildCache(&$cache, $block)
    {
        if (!$this->_options['trim_on_save']) {
            $cache['blocks'][$block] = $this->_blocks[$block];
        } else {
            $cache['blocks'][$block] = preg_replace(
                                         array('/^\\s+/m', '/\\s+$/m', '/(\\r?\\n)+/'),
                                         array('', '', "\n"),
                                         $this->_blocks[$block]
                                       );
        }
        $cache['variables'][$block] = $this->_blockVariables[$block];
        $cache['functions'][$block] = isset($this->_functions[$block])? $this->_functions[$block]: array();
        if (!isset($this->_children[$block])) {
            $cache['children'][$block] = array();
        } else {
            $cache['children'][$block] = $this->_children[$block];
            foreach (array_keys($this->_children[$block]) as $child) {
                $this->_buildCache($cache, $child);
            }
        }
    }


   /**
    * Recursively removes all data belonging to a block
    * 
    * @param    string    block name
    * @param    boolean   true if the parsed contents of the block should be kept
    * @return   mixed     SIGMA_OK on success, error object on failure
    * @see      replaceBlock(), replaceBlockfile()
    * @access   private
    */
    function _removeBlockData($block, $keepContent = false)
    {
        if (!isset($this->_blocks[$block])) {
            return $this->raiseError($this->errorMessage(SIGMA_BLOCK_NOT_FOUND, $block), SIGMA_BLOCK_NOT_FOUND);
        }
        if (!empty($this->_children[$block])) {
            foreach (array_keys($this->_children[$block]) as $child) {
                $this->_removeBlockData($child, false);
            }
            unset($this->_children[$block]);
        }
        unset($this->_blocks[$block]);
        unset($this->_blockVariables[$block]);
        unset($this->_hiddenBlocks[$block]);
        unset($this->_touchedBlocks[$block]);
        unset($this->_functions[$block]);
        if (!$keepContent) {
            unset($this->_parsedBlocks[$block]);
        }
        return SIGMA_OK;
    }


   /**
    * Returns the names of the blocks where the variable placeholder appears
    *
    * @param    string    variable name
    * @return    array    block names
    * @see addBlock(), addBlockfile(), placeholderExists()
    * @access   private
    */
    function _findParentBlocks($variable)
    {
        $parents = array();
        foreach ($this->_blockVariables as $blockname => $varnames) {
            if (!empty($varnames[$variable])) {
                $parents[] = $blockname;
            }
        }
        return $parents;
    }


   /**
    * Replaces a variable placeholder by a block placeholder.
    * 
    * Of course, it also updates the necessary arrays
    * 
    * @param    string  name of the block containing the placeholder
    * @param    string  variable name
    * @param    string  block name
    * @access   private
    */
    function _replacePlaceholder($parent, $placeholder, $block)
    {
        $this->_children[$parent][$block] = true;
        $this->_blockVariables[$parent]['__'.$block.'__'] = true;
        $this->_blocks[$parent]    = str_replace($this->openingDelimiter.$placeholder.$this->closingDelimiter,
                                                        $this->openingDelimiter.'__'.$block.'__'.$this->closingDelimiter,
                                                        $this->_blocks[$parent] );
        unset($this->_blockVariables[$parent][$placeholder]);
    }


   /**
    * Generates a placeholder to replace an <!-- INCLUDE filename --> statement
    * 
    * @access   private
    * @param    string  filename
    * @param    string  current block name
    * @return   string  a placeholder
    */
    function _makeTrigger($filename, $block)
    {
        $name = 'trigger_' . substr(md5($filename . ' ' . uniqid($block)), 0, 10);
        $this->_triggers[$block][$name] = $filename;
        return $this->openingDelimiter . $name . $this->closingDelimiter;
    }


   /**
    * Replaces the "trigger" placeholders by the matching file contents.
    * 
    * @see _makeTrigger(), addBlockfile()
    * @param    array   array ('trigger placeholder' => 'filename')
    * @return   mixed   SIGMA_OK on success, error object on failure
    * @access   private
    */
    function _pullTriggers($triggers)
    {
        foreach ($triggers as $placeholder => $filename) {
            if (SIGMA_OK !== ($res = $this->addBlockfile($placeholder, $placeholder, $filename))) {
                return $res;
            }
            // we actually do not need the resultant block...
            $parents = $this->_findParentBlocks('__' . $placeholder . '__');
            // merge current block's children and variables with the parent's ones
            if (isset($this->_children[$placeholder])) {
                $this->_children[$parents[0]] = array_merge($this->_children[$parents[0]], $this->_children[$placeholder]);
            }
            $this->_blockVariables[$parents[0]] = array_merge($this->_blockVariables[$parents[0]], $this->_blockVariables[$placeholder]);
            if (isset($this->_functions[$placeholder])) {
                $this->_functions[$parents[0]] = array_merge($this->_functions[$parents[0]], $this->_functions[$placeholder]);
            }
            // substitute the block's contents into parent's
            $this->_blocks[$parents[0]] = str_replace(
                                            $this->openingDelimiter . '__' . $placeholder . '__' . $this->closingDelimiter, 
                                            $this->_blocks[$placeholder], 
                                            $this->_blocks[$parents[0]]
                                          );
            // remove the stuff that is no more needed
            unset($this->_blocks[$placeholder], $this->_blockVariables[$placeholder], $this->_children[$placeholder], $this->_functions[$placeholder]);
            unset($this->_children[$parents[0]][$placeholder], $this->_blockVariables[$parents[0]]['__' . $placeholder . '__']);
        }
        return SIGMA_OK;
    }


   /**
    * Builds a list of functions in a block.
    *
    * @access   private
    * @param    string  Block name
    * @see _buildBlockVariables()
    */
    function _buildFunctionlist($block)
    {
        $template = $this->_blocks[$block];
        $this->_blocks[$block] = '';

        while (preg_match($this->functionRegExp, $template, $regs)) {
            $this->_blocks[$block] .= substr($template, 0, strpos($template, $regs[0]));
            $template = substr($template, strpos($template, $regs[0]) + strlen($regs[0]));

            $state = 1;
            $funcData = array(
                'name' => $regs[1],
                'args' => array()
            );
            for ($i = 0, $len = strlen($template); $i < $len; $i++) {
                $char = $template{$i};
                switch ($state) {
                    case 0:
                    case -1:
                        break 2;

                    case 1:
                        $arg = '';
                        if (')' == $char) {
                            $state = 0;
                        } elseif (',' == $char) {
                            $error = 'Unexpected \',\'';
                            $state = -1;
                        } elseif ('\'' == $char || '"' == $char) {
                            $quote = $char;
                            $state = 5;
                        } elseif (!ctype_space($char)) {
                            $arg  .= $char;
                            $state = 3;
                        }
                        break;

                    case 2: 
                        $arg = '';
                        if (',' == $char || ')' == $char) {
                            $error = 'Unexpected \'' . $char . '\'';
                            $state = -1;
                        } elseif ('\'' == $char || '"' == $char) {
                            $quote = $char;
                            $state = 5;
                        } elseif (!ctype_space($char)) {
                            $arg  .= $char;
                            $state = 3;
                        }
                        break;

                    case 3: 
                        if (')' == $char) {
                            $funcData['args'][] = rtrim($arg);
                            $state  = 0;
                        } elseif (',' == $char) {
                            $funcData['args'][] = rtrim($arg);
                            $state = 2;
                        } elseif ('\'' == $char || '"' == $char) {
                            $quote = $char;
                            $arg  .= $char;
                            $state = 4;
                        } else {
                            $arg  .= $char;
                        }
                        break;

                    case 4:
                        $arg .= $char;
                        if ($quote == $char) {
                            $state = 3;
                        }
                        break;

                    case 5:
                        if ('\\' == $char) {
                            $state = 6;
                        } elseif ($quote == $char) {
                            $state = 7;
                        } else {
                            $arg .= $char;
                        }
                        break;

                    case 6;
                        $arg  .= $char;
                        $state = 5;
                        break;

                    case 7:
                        if (')' == $char) {
                            $funcData['args'][] = $arg;
                            $state  = 0;
                        } elseif (',' == $char) {
                            $funcData['args'][] = $arg;
                            $state  = 2;
                        } elseif (!ctype_space($char)) {
                            $error = 'Unexpected \'' . $char . '\' (expected: \')\' or \',\')';
                            $state = -1;
                        }
                        break;
                } // switch
            } // for
            if (0 != $state) {
                return $this->raiseError($this->errorMessage(SIGMA_CALLBACK_SYNTAX_ERROR, (empty($error)? 'Unexpected end of input': $error) . ' in ' . $regs[0] . substr($template, 0, $i)), SIGMA_CALLBACK_SYNTAX_ERROR);
            } else {
                $funcId   = 'f' . substr(md5(serialize($funcData)), 0, 10);
                $template = substr($template, $i);

                $this->_blocks[$block] .= $this->openingDelimiter . '__function_' . $funcId . '__' . $this->closingDelimiter;
                $this->_blockVariables[$block]['__function_' . $funcId . '__'] = true;
                $this->_functions[$block][$funcId] = $funcData;
            }
        } // while 
        $this->_blocks[$block] .= $template;
        return SIGMA_OK;
    } // end func _buildFunctionlist


   /**
    * Replaces an opening delimiter by a special string.
    * 
    * Used to implement $_options['preserve_data'] logic
    * 
    * @access   private
    * @param string
    * @return string
    */
    function _preserveOpeningDelimiter($str)
    {
        return (false === strpos($str, $this->openingDelimiter))? 
                $str:
                str_replace($this->openingDelimiter, $this->openingDelimiter . '%preserved%' . $this->closingDelimiter, $str);
    }


   /**
    * Quotes the string so that it can be used in Javascript string constants
    *
    * @access private
    * @param  string
    * @return string
    */
    function _jsEscape($value)
    {
        return strtr($value, array(
                    "\r" => '\r', "'"  => "\\'", "\n" => '\n', 
                    '"'  => '\"', "\t" => '\t',  '\\' => '\\\\'
               ));
    }
}
?>

<?php
/**
 * Parses function doc comments.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHP/CodeSniffer/CommentParser/AbstractParser.php';
require_once 'PHP/CodeSniffer/CommentParser/ParameterElement.php';
require_once 'PHP/CodeSniffer/CommentParser/PairElement.php';
require_once 'PHP/CodeSniffer/CommentParser/SingleElement.php';

/**
 * Parses function doc comments.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 0.6.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_CommentParser_FunctionCommentParser extends PHP_CodeSniffer_CommentParser_AbstractParser
{

    /**
     * The parameter elements within this function comment.
     *
     * @var array(PHP_CodeSniffer_CommentParser_ParameterElement)
     */
    private $_params = array();

    /**
     * The return element in this function comment.
     *
     * @var PHP_CodeSniffer_CommentParser_PairElement.
     */
    private $_return = null;


    /**
     * The throws element list for this function comment.
     *
     * @var array(PHP_CodeSniffer_CommentParser_PairElement)
     */
    private $_throws = array();


    /**
     * Constructs a PHP_CodeSniffer_CommentParser_FunctionCommentParser.
     *
     * @param string $comment The comment to parse.
     */
    public function __construct($comment)
    {
        parent::__construct($comment);

    }//end __construct()


    /**
     * Parses parameter elements.
     *
     * @param array(string) $tokens The tokens that conmpise this sub element.
     *
     * @return PHP_CodeSniffer_CommentParser_ParameterElement
     */
    protected function parseParam($tokens)
    {
        $param           = new PHP_CodeSniffer_CommentParser_ParameterElement($this->previousElement, $tokens);
        $this->_params[] = $param;
        return $param;

    }//end parseParam()


    /**
     * Parses return elements.
     *
     * @param array(string) $tokens The tokens that conmpise this sub element.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    protected function parseReturn($tokens)
    {
        $return        = new PHP_CodeSniffer_CommentParser_PairElement($this->previousElement, $tokens, 'return');
        $this->_return = $return;
        return $return;

    }//end parseReturn()


    /**
     * Parses throws elements.
     *
     * @param array(string) $tokens The tokens that conmpise this sub element.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    protected function parseThrows($tokens)
    {
        $throws          = new PHP_CodeSniffer_CommentParser_PairElement($this->previousElement, $tokens, 'throws');
        $this->_throws[] = $throws;
        return $throws;

    }//end parseThrows()


    /**
     * Returns the parameter elements that this function comment contains.
     *
     * Returns an empty array if no parameter elements are contained within
     * this function comment.
     *
     * @return array(PHP_CodeSniffer_CommentParser_ParameterElement)
     */
    public function getParams()
    {
        return $this->_params;

    }//end getParams()


    /**
     * Returns the return element in this fucntion comment.
     *
     * Returns null if no return element exists in the comment.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    public function getReturn()
    {
        return $this->_return;

    }//end getReturn()


    /**
     * Returns the throws elements in this fucntion comment.
     *
     * Returns empty array if no throws elements in the comment.
     *
     * @return array(PHP_CodeSniffer_CommentParser_PairElement)
     */
    public function getThrows()
    {
        return $this->_throws;

    }//end getThrows()


    /**
     * Returns the allowed tags that can exist in a function comment.
     *
     * @return array(string => boolean)
     */
    protected function getAllowedTags()
    {
        return array(
                'param'  => false,
                'return' => true,
                'throws' => false,
               );

    }//end getAllowedTags()


}//end class

?>
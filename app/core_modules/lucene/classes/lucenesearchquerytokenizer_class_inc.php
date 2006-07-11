<?php

class lucenesearchquerytokenizer implements Iterator
{
    /**
     * inputString tokens.
     *
     * @var array
     */
    protected $_tokens = array();

    /**
     * tokens pointer.
     *
     * @var integer
     */
    protected $_currToken = 0;


    /**
     * lucenesearchquerytokenize constructor needs query string as a parameter.
     *
     * @param string $inputString
     */
    public function __construct($inputString)
    {
        if (!strlen($inputString)) {
            throw new customException('Cannot tokenize empty query string.');
        }

        $currentToken = '';
        for ($count = 0; $count < strlen($inputString); $count++) {
            if (ctype_alnum( $inputString{$count} )) {
                $currentToken .= $inputString{$count};
            } else {
                // Previous token is finished
                if (strlen($currentToken)) {
                    $this->_tokens[] = new lucenesearchquerytoken(lucenesearchquerytoken::TOKTYPE_WORD,
                                                                $currentToken);
                    $currentToken = '';
                }

                if ($inputString{$count} == '+' || $inputString{$count} == '-') {
                    $this->_tokens[] = new lucenesearchquerytoken(lucenesearchquerytoken::TOKTYPE_SIGN,
                                                                $inputString{$count});
                } elseif ($inputString{$count} == '(' || $inputString{$count} == ')') {
                    $this->_tokens[] = new lucenesearchquerytoken(lucenesearchquerytoken::TOKTYPE_BRACKET,
                                                                $inputString{$count});
                } elseif ($inputString{$count} == ':' && $this->count()) {
                    if ($this->_tokens[count($this->_tokens)-1]->type == lucenesearchquerytoken::TOKTYPE_WORD) {
                        $this->_tokens[count($this->_tokens)-1]->type = lucenesearchquerytoken::TOKTYPE_FIELD;
                    }
                }
            }
        }

        if (strlen($currentToken)) {
            $this->_tokens[] = new lucenesearchquerytoken(lucenesearchquerytoken::TOKTYPE_WORD, $currentToken);
        }
    }


    /**
     * Returns number of tokens
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_tokens);
    }


    /**
     * Returns TRUE if a token exists at the current position.
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_currToken < $this->count();
    }


    /**
     * Resets token stream.
     *
     * @return integer
     */
    public function rewind()
    {
        $this->_currToken = 0;
    }


    /**
     * Returns the token at the current position or FALSE if
     * the position does not contain a valid token.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->valid() ? $this->_tokens[$this->_currToken] : false;
    }


    /**
     * Returns next token
     *
     * @return lucenesearchquerytoken
     */
    public function next()
    {
        return ++$this->_currToken;
    }


    /**
     * Return the position of the current token.
     *
     * @return integer
     */
    public function key()
    {
        return $this->_currToken;
    }

}
?>
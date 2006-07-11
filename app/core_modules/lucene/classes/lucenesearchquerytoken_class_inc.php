<?php

class lucenesearchquerytoken
{
    /**
     * Token type Word.
     */
    const TOKTYPE_WORD = 0;

    /**
     * Token type Field.
     * Field indicator in 'field:word' pair
     */
    const TOKTYPE_FIELD = 1;

    /**
     * Token type Sign.
     * '+' (required) or '-' (absentee) sign
     */
    const TOKTYPE_SIGN = 2;

    /**
     * Token type Bracket.
     * '(' or ')'
     */
    const TOKTYPE_BRACKET = 3;


    /**
     * Token type.
     *
     * @var integer
     */
    public $type;

    /**
     * Token text.
     *
     * @var integer
     */
    public $text;


    /**
     * IndexReader constructor needs token type and token text as a parameters.
     *
     * @param $tokType integer
     * @param $tokText string
     */
    public function __construct($tokType, $tokText)
    {
        switch ($tokType) {
            case self::TOKTYPE_BRACKET:
                // fall through to the next case
            case self::TOKTYPE_FIELD:
                // fall through to the next case
            case self::TOKTYPE_SIGN:
                // fall through to the next case
            case self::TOKTYPE_WORD:
                break;
            default:
                throw new customException("Unrecognized token type \"$tokType\".");
        }

        if (!strlen($tokText)) {
            throw new customException('Token text must be supplied.');
        }

        $this->type = $tokType;
        $this->text = $tokText;
    }
}
?>
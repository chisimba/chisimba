<?php

class lucenesearchqueryparser
{

    /**
     * Parses a query string, returning a search_Query
     *
     * @param string $strQuery
     * @return search_Lucene_Search_Query
     */
    static public function parse($strQuery)
    {
        $tokens = new lucenesearchquerytokenizer($strQuery);

        // Empty query
        if (!$tokens->count()) {
            throw new customException('Syntax error: query string cannot be empty.');
        }

        // Term query
        if ($tokens->count() == 1) {
            if ($tokens->current()->type == lucenesearchquerytoken::TOKTYPE_WORD) {
                return new lucenesearchqueryterm(new lucenesearchindexterm($tokens->current()->text, 'contents'));
            } else {
                throw new customException('Syntax error: query string must contain at least one word.');
            }
        }


        /**
         * MultiTerm Query
         *
         * Process each token that was returned by the tokenizer.
         */
        $terms = array();
        $signs = array();
        $prevToken = null;
        $openBrackets = 0;
        $field = 'contents';
        foreach ($tokens as $token) {
            switch ($token->type) {
                case lucenesearchquerytoken::TOKTYPE_WORD:
                    $terms[] = new lucenesearchindexterm($token->text, $field);
                    $field = 'contents';
                    if ($prevToken !== null &&
                        $prevToken->type == lucenesearchquerytoken::TOKTYPE_SIGN) {
                            if ($prevToken->text == "+") {
                                $signs[] = true;
                            } else {
                                $signs[] = false;
                            }
                    } else {
                        $signs[] = null;
                    }
                    break;
                case lucenesearchquerytoken::TOKTYPE_SIGN:
                    if ($prevToken !== null &&
                        $prevToken->type == lucenesearchquerytoken::TOKTYPE_SIGN) {
                            throw new customException('Syntax error: sign operator must be followed by a word.');
                    }
                    break;
                case lucenesearchquerytoken::TOKTYPE_FIELD:
                    $field = $token->text;
                    // let previous token to be signed as next $prevToken
                    $token = $prevToken;
                    break;
                case lucenesearchquerytoken::TOKTYPE_BRACKET:
                    $token->text=='(' ? $openBrackets++ : $openBrackets--;
            }
            $prevToken = $token;
        }

        // Finish up parsing: check the last token in the query for an opening sign or parenthesis.
        if ($prevToken->type == lucenesearchquerytoken::TOKTYPE_SIGN) {
            throw new customException('Syntax Error: sign operator must be followed by a word.');
        }

        // Finish up parsing: check that every opening bracket has a matching closing bracket.
        if ($openBrackets != 0) {
            throw new customException('Syntax Error: mismatched parentheses, every opening must have closing.');
        }

        switch (count($terms)) {
            case 0:
                throw new customException('Syntax error: bad term count.');
            case 1:
                return new lucenesearchqueryterm($terms[0],$signs[0] !== false);
            default:
                return new lucenesearchquerymultiterm($terms,$signs);
        }
    }

}
?>
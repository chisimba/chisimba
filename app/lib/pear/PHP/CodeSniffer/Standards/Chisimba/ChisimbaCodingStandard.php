<?php
/**
 * Chisimba Coding Standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://gnu.org/licence GPL Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHP/CodeSniffer/Standards/CodingStandard.php';

 /**
 * Chisimba Coding Standard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://gnu.org/licence GPL Licence
 * @version   Release: 0.6.0
 * @link      http://avoir.uwc.ac.za
 */
class PHP_CodeSniffer_Standards_Chisimba_ChisimbaCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{


    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The Chisimba standard uses some generic sniffs.
     *
     * @return array
     */
    function getIncludedSniffs()
    {
        return array(
                'Generic/Sniffs/Formatting/MultipleStatementAlignmentSniff',
                'Generic/Sniffs/Functions/OpeningFunctionBraceKernighanRitchieSniff',
                'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff',
                'Generic/Sniffs/PHP/UpperCaseConstantSniff',
                'Generic/Sniffs/PHP/DisallowShortOpenTagSniff',
               );

    }//end getIncludedSniffs()


}//end class
?>

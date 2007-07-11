<?php
require_once 'PHPUnit.php';
$this->loadClass( 'strvalidate', 'strings');

class strvalidate_test extends PHPUnit_TestCase
{
    var $strValidate;

    function strvalidate_test( $name ) {
        $this->PHPUnit_TestCase( $name );
    }
    
    function setUp() {
        global $_globalObjEngine;
        $this->strValidate =& new strvalidate( &$_globalObjEngine, 'strvalidate');
    }

    function tearDown() {
        unset( $this->strValidate );
    }
    
    function test_IsAlphaNumeric() {
        $valid   = array ( 'abc', '1ab', 'a1b', 'ab1', '123', 'a b', '12b', '1b2', ' ' );

        foreach( $valid as $string ) {
            $result = $this->strValidate->isAlphaNumeric( $string );
            $this->assertTrue( $result, "Error validating valid string \"$string\"" );
        }

        $invalid = array ( 'abc@', '@abc', 'a@b', '', '1@', '!@' );
        foreach( $invalid as $string ) {
            $result = $this->strValidate->isAlphaNumeric( $string );
            $this->assertFalse( $result, "Error validated invalid string \"$string\"" );
        }
    }

    function test_IsNumeric() {
        $valid   = array ( '123', '12', '1', ' 1', '1 ', ' '  );

        foreach( $valid as $string ) {
            $result = $this->strValidate->isNumeric( $string );
            $this->assertTrue( $result, "Error validating valid string \"$string\"" );
        }

        $invalid = array ( 'abc', '@ab', '', '!@#' );
        foreach( $invalid as $string ) {
            $result = $this->strValidate->isNumeric( $string );
            $this->assertFalse( $result, "Error validated invalid string \"$string\"" );
        }
    }

    function test_IsAlpha() {
        $valid   = array ( 'abc', 'ab', 'c', ' a', 'a ', ' ' );

        foreach( $valid as $string ) {
            $result = $this->strValidate->isAlpha( $string );
            $this->assertTrue( $result, "Error validating valid string \"$string\"" );
        }

        $invalid = array ( '123', '@12', '', '!@#' );
        foreach( $invalid as $string ) {
            $result = $this->strValidate->isAlpha( $string );
            $this->assertFalse( $result, "Error validated invalid string \"$string\"" );
        }
    }

}
?>
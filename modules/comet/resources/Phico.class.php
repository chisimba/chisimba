<?php
/** Phico - Php in comet
 * @author  Andrea Giammarchi
 * @site    webreflection.blogspot.com
 * @date    2008/04/28
 * @version 0.1.5
 * @license Mit Style License
 */
class Phico {

    /**
     * public static constants
     * @param   int         FIXLEN is a "start to interact" browser work around (IE + Safari)
     * @param   string      FIXEOL is another workaround (Safari dedicated)
     * @param   string      TYPE is the type used inside the script tag
     * @param   string      VERSION is the Phico version
     */
    const   FIXLEN  = 1024,
            FIXEOL  = '<br />',
            TYPE    = 'text/javascript',
            VERSION = '0.1.5';

    /**
     * static public methods
     */

    /**
     * Initialize a Phico session, based on Singleton desing pattern.
     * This method should be used when the client use connect method
     * and will automatically send a Phico GET key with expected index as value.
     * @param   int         client side array index
     * @raise   Exception   if index is not an integer or is less than zero
     * @example
     * if(isset($_GET['Phico']))
     *     Phico::init($_GET['Phico']);
     */
    public  static  function    init($__index__){
        if(intval($__index__) == $__index__ && -1 < $__index__){
            if(!isset(self::$_instance))
                self::$_instance = new self($__index__);
        } else
            throw new Exception('Received index is not valid: '.$__index__);
    }

    /**
     * Send data to connected client.
     * @param   mixed       every kind of JSON compatible value.
     */
    public  static  function    send($data){
        if(is_string($value = @json_encode($data)))
            self::$_instance->_send(
                '<script type="'.
                    self::TYPE.
                '">parent.Phico.onData('.
                    self::$_instance->__index__.','.
                    $value.
                ');</script>'
            );
    }

    /**
     * Create an optimized version of JavaScript Phico file.
     * @param   boolean     if true, uses ob_gzhandler and add specified content-type printing library.
     *                      if false, return minified version of Phico JS library.
     * @param   string      optional file name to read if Phico.js, for some reason, is in another folder.
     * @example
     * -- Phico JavaScript library with file in the same folder of this one
     * <?php require 'Phico.class.php'; Phico::JavaScript(true); ?>
     */
    public  static  function    JavaScript($headers, $file = 'Phico.js'){
        $result = '// (C) Andrea Giammarchi - Mit Style License - V '.
            self::VERSION.PHP_EOL.
            trim(preg_replace(
                array(
                    '#(/\*\*)[^\2]+?(\*/)#',
                    '#//[^\n\r]+?(\n|\r|\r\n)#',
                    '/([^\\2])([[:space:]])[[:space:]]+/',
                    '/[[:space:]]+([[:space:]])([^\\1]]])/',
                    '/(\w+)[[:space:]]+(\W+)/',
                    '/(\W+)[[:space:]]+(\w+)/',
                    '/(\W+)[[:space:]]+(\W+)/'
                ),
                array(
                    '',
                    '',
                    '\\1\\2',
                    '\\1\\2',
                    '\\1\\2',
                    '\\1\\2',
                    '\\1\\2'
                ),
                file_get_contents($file)
            ));
        if($headers){
            ob_start('ob_gzhandler');
            header('Content-type: '.self::TYPE);
            echo    $result;
        } else
            return  $result;
    }

    /**
     * private stuff
     */

    private function    __construct($__index__){
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);
        if(count(ob_list_handlers()) < 2){
            ob_start();
            ob_implicit_flush(true);
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
        }
        $this->__index__    = $__index__;
        $this->_send(str_repeat(chr(32), self::FIXLEN));
    }

    private function    _send($data){
        echo    $data.self::FIXEOL,
                ob_get_clean();
    }

    private static  $_instance;
    private         $__index__;

}

if(!function_exists('json_encode')){
    require 'JSON.php';
    function json_encode($data){
        $json = new Services_JSON();
        return    $json->encode($data);
    }
}

?>
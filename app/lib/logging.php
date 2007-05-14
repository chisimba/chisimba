<?php

$enable_debug_logging = TRUE;

if ($enable_debug_logging == TRUE)
{
    require_once 'pear/Log.php';

    $conf = array('mode' => 0644, 'timeFormat' => '%Y-%m-%d %H:%M:%S');
    $log = &Log::singleton('file', 'error_log/system_errors.log', 'framework', $conf);
    $GLOBALS['DEBUG_LOG_OBJ'] = $log;

    function log_debug($str)
    {
        ob_start();
        print_r($str);
        $logstr = ob_get_contents();
        ob_end_clean();

        $logger = $GLOBALS['DEBUG_LOG_OBJ'];
        $logger->log($logstr, PEAR_LOG_DEBUG);
    }
}
else
{
    function log_debug($str)
    {
    }
}

?>
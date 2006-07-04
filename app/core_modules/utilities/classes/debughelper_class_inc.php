<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * Debug helper class
 * 
 * @package utilities
 * @copyright AVOIR UWC GNU/GPL
 * @author Paul Scott
 */

class debughelper extends object 
{
	/**
	 * Standard init method
	 */
	public function init()
	{
		
	}
	
	/**
     * Debug helper function.  This is a wrapper for var_dump() that adds
     * the <pre /> tags, cleans up newlines and indents, and runs
     * htmlentities() before output.
     *
     * @param  mixed  $var The variable to dump.
     * @param  string $label An optional label.
     * @return string
     */
    static public function dump($var, $label=null, $echo=true)
    {
        // format the label
        $label = ($label===null) ? '' : rtrim($label) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $label
                    . PHP_EOL . $output 
                    . PHP_EOL;
        } else {
            $output = '<pre>'
                    . $label
                    . htmlentities($output, ENT_QUOTES)
                    . '</pre>';
        }

        if ($echo) {
            echo($output);
        }
        return $output;
    }
}
<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * HTTP Response Class
 * This class simply parses HTTP responses and returns a status.
 *
 * @access public
 * @copyright AVOIR
 * @author Paul Scott
 */

class httpresponse extends object
{
	/**
     * HTTP Response Code (i.e. 404 or 200)
     *
     * @var null|int
     */
    protected $_code;

    /**
     * Response Headers
     *
     * @var array
     */
    protected $_headers;

    /**
     * Response body
     *
     * @var null|string
     */
    protected $_body;


	/**
	 * Class Constructor, read and parse HTTP response
	 *
	 * @param null
	 */
	public function __construct($code=null, $headers=array(), $body=null)
	{
		$this->_code    = $code;
		$this->_headers = $headers;
		$this->_body    = $body;
	}


	/**
	 * Check if Response is an error
	 *
	 * @return boolean
	 */
	public function isError()
	{
		// Check if response is one of the 400 or 500 error codes
		return substr($this->_code, 0, 1) == 4 || substr($this->_code, 0, 1) == 5;
	}


	/**
	 * Check if Response is successful
	 *
	 * @return bool
	 */
	public function isSuccessful()
	{
		return substr($this->_code, 0, 1) == 2;
	}


	/**
	 * Check if Response is a redirect
	 *
	 * @return boolean
	 */
	public function isRedirect()
	{
		return substr($this->_code, 0, 1) == 3;
	}


	/**
	 * Get Response Body
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->_body;
	}


	/**
	 * Return Response Status
	 *
	 * @return int
	 */
	public function getStatus()
	{
		return $this->_code;
	}


	/**
	 * Return Response Headers
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}

}
?>
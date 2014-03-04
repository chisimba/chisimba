<?php

class httpclientfile extends abhttpclient
{
    protected $_filename = '';

    /**
     * Class Constructor
     *
     * httpclientfile file ignores URIs.  The setUri() method is simply ignored.
     * The filename to read may be set by setFilename().
     *
     * @param  null|string|uri $filename
     * @param  array $headers
     * @return void
     */
    public function __construct($uri = null, $headers = array())
    {
        if ($uri !== null) {
            $this->setUri($uri);
        }

    	if ($headers !== array()) {
    		$this->setHeaders($headers);
    	}
    }


    /**
     * Sets the filename to read for get() response.
     *
     * @param string $filename
     * @return void
     */
    public function setFilename($filename)
    {
        if (!is_string($filename)) {
            throw new customException('Filename must be a string');
        }

        $this->_filename = $filename;
    }


   /**
     * Send a GET HTTP Request
     *
     * @return httpresponse
     */
    public function get()
    {
        // if the filename was never set or set to '', fake a code 400
        if (empty($this->_filename)) {
            return new httpresponse(400, array(), '');
        }

        $file = @file_get_contents($this->_filename);
        if ($file === false) {
            throw new customException("Failed reading file \"{$this->_filename}\"");
        }

        return new httpresponse(200, array(), $file);
    }


    /**
     * Send a POST HTTP Request
     *
     * @param string $data Data to send in the request
     * @return Zend_Http_Response
     */
    public function post($data)
    {
        $request = array('POST <uri> HTTP/1.0',
                         'Host: <uri>',
                         'Content-length: ' . strlen($data),
                         'Accept: */*');

        echo( get_class($this)
              . " does not support PUT. Would issue the following request:\n\n"
              . implode("\n", $request) . "\n\n"
              . $data . "\n" );

        return new httpresponse(201, array(), '');
    }


    /**
     * Send a PUT HTTP Request
     *
     * @param string $data Data to send in the request
     * @return Zend_Http_Response
     */
    public function put($data)
    {
        $request = array('PUT ' . $this->_uri . ' HTTP/1.0',
                         'Host: ' . $this->_uri,
                         'Content-length: ' . strlen($data),
                         'Accept: */*');

        echo( get_class($this)
              . " does not support PUT. Would issue the following request:\n\n"
              . implode("\n", $request) . "\n\n"
              . $data . "\n" );

        return new httpresponse(200, array(), '');
    }


    /**
     * Send a DELETE HTTP Request
     *
     * @return Zend_Http_Response
     */
    public function delete()
    {
        $request = array('DELETE ' . $this->_uri . ' HTTP/1.0',
                         'Host: ' . $this->_uri);

        echo( get_class($this)
              . " does not support DELETE. Would issue the following request:\n\n"
              . implode("\n", $request) . "\n" );

        return new httpresponse(204, array(), '');
    }

}
?>
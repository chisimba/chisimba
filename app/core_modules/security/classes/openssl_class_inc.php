<?php
 /**
 * OpenSSL class
 * 
 * Class to encapsulate most of the functionality of the OpenSSL extension of PHP.
 * 
 * PHP version 5
 *  
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * 
 * @category  Chisimba
 * @package   security
 * @author Paul Scott <pscott@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to encapsulate most of the functionality of the OpenSSL extension of PHP
 * This class will allow the user to create:
 * <pre>
 * <li>Private Keys</li>
 * <li>Public Keys</li>
 * <li>Certificates</li>
 * <li>and much more</li>
 *
 * @access public
 * @copyright AVOIR
 * @author Paul Scott
 * @package security
 */
class openssl extends object
{
    /**
     * Distinguished name (fully qualified)
     *
     * @var array
     */
    public $distinguished_name = FALSE;

    /**
     * Errors (deprecated)
     *
     * @deprecated uses customException now
     * @var mixed
     */
    public $error;

    /**
     * Private key that is generated
     *
     * @var string
     */
    public $private_key;

    /**
     * Text of the private key
     *
     * @var string
     */
    public $private_key_text;

    /**
     * Secret passphrase
     *
     * @var mixed
     */
    public $passphrase;

    /**
     * The certificate from the CA
     *
     * @var mixed
     */
    public $certificate;

    /**
     * Certificate request
     *
     * @var string
     */
    public $csr;

    /**
     * Text from the CSR
     *
     * @var string
     */
    public $csr_text;

    /**
     * The text of the CA's certificate
     *
     * @var string
     */
    public $certificate_text;

    /**
     * Self signed certificate (in case of no available CA)
     *
     * @var string
     */
    public $sscert;

    /**
     * Standard init (constructor)
     *
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {

    }

    /**
     * This method should be called to set up the DN or distinguished name. This will set the DN for your certificate and keys
     *
     * @param string $country
     * @param string $state
     * @param string $city
     * @param string $organization_name
     * @param string $organization_dept
     * @param string $name
     * @param mixed $email
     */
    public function setDN($country,$state,$city,$organization_name,$organization_dept,$name,$email)
    {
        $this->distinguished_name = array(
           "countryName" => $country,
           "stateOrProvinceName" => $state,
           "localityName" => $city,
           "organizationName" => $organization_name,
           "organizationalUnitName" => $organization_dept,
           "commonName" => $name,
           "emailAddress" => $email
        );
    }

    /**
     * Public method to generate a key pair from the cert
     *
     * @param mixed $passphrase
     * @param integer $valid_days
     * @return bool
     */
    public function generateKeyPair($passphrase = NULL, $valid_days = 99999)
    {
        //check for the presence of the DN
        if($this->distinguished_name == FALSE)
        {
            throw new customException('The distinguished name MUST be set beforehand via $this->setDN()');
        }
        // Generate a new private (and public) key pair
        if (!$this->private_key = openssl_pkey_new()) {
            throw new customException("OpenSSL Private Key generation failed!");
            return false;
        }

        // Generate a certificate signing request
        if (!$this->csr = openssl_csr_new($this->distinguished_name,$this->private_key)) {
            throw new customException("OpenSSL certificate signing request generation failed");
            return false;
        }

        // You will usually want to create a self-signed certificate at this
        // point until your CA fulfills your request.
        // This creates a self-signed cert that is valid for 365 days
        if (!$this->sscert = openssl_csr_sign($this->csr, null, $this->private_key, $valid_days)) {
            throw new customException("OpenSSL self-signed certificate generation failed");
            return false;
        }

        // Now you will want to preserve your private key, CSR and self-signed
        // cert so that they can be installed into your web server, mail server
        // or mail client (depending on the intended use of the certificate).

        // Typically, you will send the CSR on to your CA who will then issue
        // you with the "real" certificate.
        if (!openssl_csr_export($this->csr, $this->csr_text)) {
            throw new customException("OpenSSL CSR export failed");
            return false;
        }
        if (!openssl_x509_export($this->sscert, $this->certificate_text)) {
            throw new customException("OpenSSL x509 certificate export failed");
            return false;
        }
        if (!openssl_pkey_export($this->private_key, $this->private_key_text, $this->passphrase)) { // and debug_zval_dump($pkeyout);
            throw new customException("OpenSSL private key export failed");
            return false;
        }
        $this->private_key = $this->private_key_text;
        $this->certificate = $this->certificate_text;
        $this->csr = $this->csr_text;

        return true;

    }

    /**
     * Method to encrypt data with your public key
     *
     * @param mixed $data
     * @param string $certificate
     * @return string $data
     */
    public function public_key_encrypt($data, $certificate = FALSE)
    {
        if (!$certificate)
        {
            $certificate = $this->certificate;
        }

        if (!$public = openssl_get_publickey($certificate))
        {
            throw new customException("Invalid x509 certificate");
            return false;
        }

        if (!openssl_public_encrypt($data, $crypted, $public))
        {
            throw new customException("OpenSSL public key encryption failed");
            return false;
        }

        $data = base64_encode($crypted);

        return $data;
    }

    /**
     * Method to decrypt data using your private key
     *
     * @param mixed $data
     * @param string $private_key
     * @param string $passphrase
     * @return mixed
     */
    public function private_key_decrypt($data, $private_key=false, $passphrase=NULL)
    {
        if (!$private_key)
        {
            $private_key = $this->private_key;
        }

        if (!$private = openssl_get_privatekey($private_key, $passphrase))
        {
            throw new customException("Invalid private key");
            return false;
        }

        $crypted = base64_decode($data);
        if (!openssl_private_decrypt($crypted, $decrypted, $private))
        {
            throw new customException("OpenSSL private key decryption failed");
            return false;
        }

        return $decrypted;
    }

    /**
     * Method to decrypt data using your private key
     *
     * @param mixed $data
     * @param mixed $private_key
     * @param string $passphrase
     * @return string
     */
    public function private_key_encrypt($data, $private_key = false, $passphrase=NULL)
    {
        if (!$private_key)
        {
            $private_key = $this->private_key;
        }

        if (!$private = openssl_get_privatekey($private_key,$passphrase)) {
            throw new customException("Invalid private key");
            return false;
        }

        if (!openssl_private_encrypt($data, $crypted, $private)) {
            throw new customException("OpenSSL private key encryption failed");
            return false;
        }
        $data = base64_encode($crypted);

        return $data;
    }

    /**
     * Decrypt data using your public key
     *
     * @param mixed $data
     * @param string $certificate
     * @return mixed $encrypted
     */
    public function public_key_decrypt($data, $certificate = false)
    {
        if (!$certificate)
        {
            $certificate = $this->certificate;
        }
        if (!$public = openssl_get_publickey($certificate))
        {
            throw new customException("Invalid x509 certificate");
            return false;
        }

        $crypted = base64_decode($data);
        if (!openssl_public_decrypt($crypted, $decrypted, $public))
        {
            $this->error = "OpenSSL public key decryption failed";
            return false;
        }

        return $decrypted;
    }

    /**
     * pseudo destructer method (for brevity sake)
     *
     * @param void
     * @return void
     */
    public function clearData()
    {
        $this->distinguished_name = FALSE;
        $this->certificate = NULL;
        $this->certificate_text = NULL;
        $this->csr = NULL;
        $this->csr_text = NULL;
        $this->passphrase = NULL;
        $this->private_key = NULL;
        $this->private_key_text = NULL;
        $this->sscert = NULL;

    }
}
?>

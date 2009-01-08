<?php
/* -------------------- USER CLASS ----------------*/

/**
* A class that implements authentication by making calls to any authentication
* plugins that exist and are configured in security module. Note that those plugin
* classes must also implement ifauth. This class implements the Chain of Command
* or Chain of Responsibility design pattern as per Gamma, Helm, Johnson,
* Vlissides, 1994, p. 223.
*
* Intent: "Avoid coupling the sender of a request to its receiver by
* giving more than one object a chance to handel the requiest. Chain
* the receiving objects and pass the request along the chain until an
* object handles it."(Gamma, Helm, Johnson, Vlissides, 1994, p. 223)
*
* According to Wikipedia, the chain-of-responsibility pattern is a design
* pattern consisting of a source of command objects and a series of
* processing objects. Each processing object contains a set of logic
* that describes the types of command objects that it can handle, and
* how to pass off those that it cannot to the next processing object
* in the chain. A mechanism also exists for adding new processing
* objects to the end of this chain.
*
* Basically this allows us to add new authentication methods as they
* become available, and to prioritize them when a user is authenticating.
*
* Usaeage:  TODO
*
* @author Derek Keats
* @category Chisimba
* @package security
* @copyright AVOIR
* @licence GNU/GPL
*/
class authenticate extends object
{
    private $username;
    private $password;

    /**
    *
    * An array to hold the chain of command list of authorized authentication
    * methods. For example, array("database", "LDAP", "PAM", "Kerberos"). The
    * authUser method then processes this array until it finds a successful
    * login, or reaches the end and login fails.
    *
    */
    protected $authChainOfCommand=array('database');

    /**
    *
    * Standard init method. It reads the configuration data for a list
    * of allowed authentication methods, and the order in which they occur.
    * It then builds the $authChainOfCommand array so that the authenticateUser
    * method can then process the user for login.
    *
    *
    */
    public function init()
    {
        //Instantiate the configuration object
        $objConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $authMeth = $objConfig->getValue('MOD_SECURITY_AUTHMETHODS', 'security');
        if (strstr($authMeth, ',')) {
            $this->authChainOfCommand = explode(",", $authMeth);
        } else {
            $this->authChainOfCommand[] = trim($authMeth);
        }
    }

    /**
    *
    * A method to implement the chain of command authentication. It loops over
    * the possible authentication menthods until one succeeds, exiting when the first
    * one succeeds. If none succeeds then the login fails.
    */
    public function authenticateUser($username, $password, $remember = NULL)
    {
        foreach ($this->authChainOfCommand as $authMethod) {
               try {
                   $authClass = "auth_" . trim($authMethod);
                   $objAuth = $this->getObject($authClass, "security");
                   if ($objAuth->authenticate($username, $password, $remember)) {
                       //Authentication succeeded
                       $objAuth->initiateSession();
                       $objAuth->storeInSession();
                       return TRUE;
                   } else {
                       //Authentication failed
                       continue;
                   }
               } catch (customException $e) {
                customException::cleanUp();
                return FALSE;
               }
        }
        //If it gets through them all then fail the login
        return FALSE;
    }
}
?>

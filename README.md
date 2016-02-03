Chisimba
========

Chisimba is a PHP framework for building web applications and applications that need a web API. It implements a model-view-controller (MVC) design pattern, implemented on a modular architecture. There is a core framework, and numerous modules that implement functionality ranging from blogs through CMS to a eLearning system. The interface design is flexible and implemented via canvases (skins, or themes). There is an online package management system, and developers can build modules rapidly by generating a working module from which to code. In order to fully install and use the Chisimba framework to its full potential, it is necessary to install a number of PHP extensions, as well as to have a few PEAR objects on hand. The word “Chisimba” is the Chichewa (Malawi) word for the framework used to build a traditional African house.


Chisimba was created as a product of a collaboration of the 13 African universities involved in the African Virtual Open Initiatives and Resources (AVOIR) project. Its main purpose is to foster capacity building in Software Engineering among African universities through the collaborative development of Free Software.  However, it is an awesome technology for running and building web applications. See http://chisimba.com for more information.

Please make sure that you have a working Apache installation, as well as a functional database hosted on one of the following database servers:

1. MySQL - 5.1.x
2. PostgreSQL - 8.1 (note that we have not done much testing on PostGreSQL, so MySQL is a safer bet).

You will also need PHP version 5.1.2 or above.

The web based installer found in /path/to/webroot/chisimba_framework/installer/index.php will help you configure and  der to use the mail to blog functionality, you will need the IMAP PHP extension. On most GNU/Linux distributions, this is a simple command to the package manager. On Windows based systems, it is as simple as uncommenting the extension in php.ini and downloading the required .dll.

If you have any questions, comments or other issues, please do not hesitate to post a message to our users mailing list found at:

http://groups.google.com/group/chisimba‐dev

Have fun! Enjoy Chisimba!

Hello world
==========

A Chisimba MVC hello world

This chapter assumes that you have:

an installed and working Chisimba installation on your computer;
knowledge of PHP;
read the chapter on Module Catalogue, and carried out the activities in it.

A "hello world" program is a computer program that prints out "Hello, World!" on a display device. It is used in many introductory tutorials for teaching a programming language. Such a program is typically one of the simplest programs possible in a computer language. In this case, we will use it as a very simple example to ensure that you understand the MVC approach, and the minimum structure for a Chisimba module. Once you understand this structure, you can apply your existing skills to Chisimba very easily.

The M in MVC refers to the model, which is typically the data access layer. In this case, we are not going to access any external data yet, so we will ignore the model, and concentrate on the view and the controller.

Create a directory called hellochisimba (or hellowhatever) in your chisimba_modules directory: 	/path/to/chisimba/chisimba_modules/hellochisimba, 
where /path/to/chisimba/ is the place where your chisimba files are located, for example /var/www/ on Linux or c:\Inetpub on Windows. 

Create a text file in that directory and name it controller.php. Open it in your favorite text editor (or IDE) and first enter the opening PHP tag and the required description of your module as follows:

```
<?php
/**
 * 
 * Hello  Chisimba
 * 
 * A classic hello world type module to introduce you to Chisimba.
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
 * @category  Chisimba
 * @package   helloforms
 * @author    Your Name youremail@yourdomain
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: controller.php,v 1.4 2007-11-25 09:13:27 your-user-name Exp $
 * @link      http://avoir.uwc.ac.za
 */

Please note that you should have these document blocks in all your code, as this is how the code documentation is generated. Code that is not documented like this should not be committed to subversion as it will be removed with no warning, and it should not exist on your computer for more than 3-12 minutes. In general, it is best to write the doc blocks before you write any code.  

Now start entering the controller code, beginning with the standard security check as follows:

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 * 
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *         
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

This prevents the code from being executed if it is not being run as a Chisimba controller. The name “kewl_entry_point_run” is used for backward compatibility with previous versions. Make sure you include the comments as well.

The next set of lines in your controller is the PHPDocumentor style comments for the controller class.

/**
* 
* Hello Chisimba
* 
* Controller class for Chisimba for the module hellochisimba
*
* @author Derek Keats
* @package hellochisimba
*
*/

As noted, it is always a good practice to write these comments first, as code without well written comments will not be allowed into the Chisimba code base or any of its modules, and your code will be seen by other Chisimba programmers as being of amateur quality. You may get growled at on the mailing list as well, so to avoid this kind of embarrassment, write your comments first. We will return to the PHPDocumentor style comments later. For now, you can just enter them as above, replacing Yourname Here with your name.

Now begins the real business of the code. Create the class definition as follows:

class hellochisimba extends controller
{
}

Note that the class has the same name as the module and the directory that it is in, and must extend the framework controller class. This is always the case, otherwise the engine will not find and execute your module. Note also that the curly braces are underneath the letter “c” of the word class, and that all names are in lower case. All indents are 4 spaces (not tabs).  Violate this convention at your own peril. 

Please note that there might be a hellochisimba module already in the subversion repository, in which case you will not be able to give yours the same name. Rather then use helloyourname (where yourname is obviously your shoe size). Remember the directory and the controller class must have the same name.

All Chisimba classes that extend the framework have a constructor that is named init(), so creating that constructor is the next bit of code needed. A common use of the constructor is to set up default values for object properties, instantiate common objects, and in this case we will not be using it at all until we get to multilingual code.

    /**
    * 
    * Constructor for the hellochisimba controller
    *
    * @access public
    * @return void
    * 
    */
    public function init()
    {

    }

For the hello world example, we do not need any code in the init() method, so we can continue to the dispatch() method. A controller must have a dispatch() method that is invoked by the engine to process the logic of the controller.  The dispatch() method uses methods determined from the action  parameter of the  querystring and executes the appropriate method, returning its appropriate template. The dispatch() method of many older Chisimba modules use case statements instead or the $this->$method() approach. Either is acceptable, but this method produces cleaner code that is easier to read.  The code is as follows:

    /**
     * 
     * The standard dispatch method for the hellochisimba module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     *
     * @access public
     * @return The output of the executed method
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'view');
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return $this->$method();
    }
All Chisimba dispatch() methods will have exactly the same code, so once you have written this, can write a dispatcher for any module.

There is only one method needed for this module, which is the __view() method. Note the double underscore (_ and _) in front of the method name, which is required for all methods that correspond to actions.
    /**
    * 
    * Method corresponding to the view action. It sets the layout template
    * and fetches the appropriate content template, in this case, 
    * default_tpl.php.
    * 
    * @access private
    * 
    */
    private function __view()
    {
        $this->setLayoutTemplate('layout_tpl.php');
        return 'default_tpl.php';
    }

The remainder of the controller is always the same, although additional methods will be included to cater for different actions. Keep the controller as simple as possible.

The next method returns a template populated with an error message if the action is not found.

    /**
    * 
    * Method to return an error when the action is not a valid 
    * action method
    * 
    * @access private
    * @return string The dump template populated with the error message
    * 
    */
    private function __actionError()
    {
        $this->setVar('str', "<h3>"
          . $this->objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $action . "</h3>");
        return 'dump_tpl.php';
    }

The next method determines if the action is valid by examining if the corresponding method exists.

    /**
    * 
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (__). If it __action 
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return boolean TRUE|FALSE
    * 
    */
    function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

The next method converts the action into the appropriate method if it is a valid action.

    /**
    * 
    * Method to convert the action parameter into the name of 
    * a method of this class.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return stromg the name of the method
    * 
    */
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }

The last method is a check to determine if the method requires the user to be logged in. In this case, we are allowing anonymous access to the view action, with all others requiring login. When you develop your module, you will use this if you need to allow access to certain methods when they are not logged in. For example, a blog generally requires public viewing and searching.

    /**
    *
    * This is a method to determine if the user has to 
    * be logged in or not. Note that this is an example, 
    * and if you use it view will be visible to non-logged in 
    * users. Delete it if you do not want to allow annonymous access.
    * It overides that in the parent class
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        $action=$this->getParam('action','NULL');
        switch ($action)
        {
            case 'view':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
  }

Next, let us create three blocks, a wide one for the middle of our template, and two narrow ones for the left and right side of our default template. Blocks are a key feature of Chisimba, and unless there is good reason to do otherwise, all new code developed in Chisimba should render output as blocks. All blocks take the same form:

<?php
/**
 *
 * Hello demo 1
 *
 * A demo of the chisinmba dynamic canvas
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
 * @version
 * @package    dynamiccanvas
 * @author     Derek Keats <derek@dkeats.com>
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * 
 * Hello demo 1
 *
 * A demo of the chisinmba dynamic canvas
 *
 * @category  Chisimba
 * @author    Derek Keats
 * @version
 * @copyright 2010 AVOIR
 *
 */
class block_hello1 extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->title = "Hello";
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        return "Hello Chisimba";
    }
}
?>


Blocks have at minimum a title property ($title), an init() method that may assign the title, and a show() method that returns the content of the block. The above code creates a $title, asigns the value “Hello” to it, and renders the text “Hello Chisimba” in the block content. Simple, no?

Create the above block, and name it block_hello1_class_inc.php. All blocks take this form, block_blockname_class_inc.php. Note that the blockname and the class name must be the same, and the block must extend the framework class 'object'. Blocks reside in the classes directory of your module.

Create another block, and name it block_hello2_class_inc.php, and let it contain the following (or anything else you want to put into it):

<?php
/**
 *
 * Hello demo 2
 *
 * A demo of the chisinmba dynamic canvas
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
 * @version
 * @package    dynamiccanvas
 * @author     Derek Keats <derek@dkeats.com>
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * 
 * Hello demo 2
 *
 * A demo of the chisinmba dynamic canvas
 *
 * @category  Chisimba
 * @author    Derek Keats
 * @version
 * @copyright 2010 AVOIR
 *
 */
class block_hello2 extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->title = "Hello again";
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        return "This will appear in the right panel of your page. This is a much better way to write Chisimba code than the old way of putting code into templates.";
    }
}
?>

Then create one more block, name it block_hello3_class_inc.php, and let it contain:

<?php
/**
 *
 * Hello demo 3
 *
 * A demo of the chisinmba dynamic canvas
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
 * @version
 * @package    dynamiccanvas
 * @author     Derek Keats <derek@dkeats.com>
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * 
 * Hello demo 3
 *
 * A demo of the chisinmba dynamic canvas
 *
 * @category  Chisimba
 * @author    Derek Keats
 * @version
 * @copyright 2010 AVOIR
 *
 */
class block_hello3 extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;

    /**
     * Expose the block for remote blocks
     *
     * @var string $expose
     * @access public
     */
    public $expose;

    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        // Set the title of the block.
        $this->title = "Hello middle";
        // Expose this block to external sites.
        $this->expose = TRUE;
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        return 'This is a wide block. Lorem ipsum dolor
        sit amet, euismod at diam, ac tellus mollitia praesent vitae,
        aliquam lacus. Mi est eu. Sed nulla non fringilla malesuada. Feugiat
        wisi amet, urna tempus rhoncus felis. Cursus dictumst. Velit tortor
        condimentum molestie mollis elementum et, pulvinar sed magna dapibus
        nisl, justo dolor vestibulum vel mauris. Ullamcorper nunc eleifend,
        sollicitudin quis mauris congue habitant enim nec. Mollis nec, nunc
        dui varius, gravida diam mollis, orci nulla facilisi proin elit ligula,
        sit non mauris. Morbi ut quisque commodo etiam at orci, at duis,
        lorem aptent augue pellentesque, diam ligula amet risus ducimus
        bibendum. Arcu eros turpis sed mattis libero consequat, urna laoreet
        morbi erat, fusce nam. Magna velit cras.';
    }
}
?>

This will be a wide block, for display in the wide content area.

Now let us look at the default_tpl.php Create this file in the templates/content directory of your module.

<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        {
            "display" : "block",
            "module" : "hellochisimba",
            "block" : "hello1"
        }
    </div>
    <div id="Canvas_Content_Body_Region3">
        {
            "display" : "block",
            "module" : "hellochisimba",
            "block" : "hello2"
        }
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "hellochisimba",
            "block" : "hello3"
        }
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>

Let us analyse this template, which uses the JSON method to get blocks to render in the template. First there is the following code:

<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

This [ob_start();] enables output buffering, which allows all output to be captured into a variable for transmission to the layout template. The next two lines load a class that fixes the length of the columns so that they are all the same, using Javascript, and then executes the code to do the column length adjustment. PHP is turned off at this point, as the next section is the HTML template.

This code sets up a three column layout, with the layers nested within it corresponding to the left (Canvas_Content_Body_Region1), right (Canvas_Content_Body_Region3) and middle (Canvas_Content_Body_Region2) columns. Note that they must be presented in this order.

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
    </div>
    <div id="Canvas_Content_Body_Region3">
    </div>
    <div id="Canvas_Content_Body_Region2">
    </div>
</div>

Within each of these layers is the JSON that will render the block, and it takes the form:

        {
            "display" : "block",
            "module" : "hellochisimba",
            "block" : "hello2"
        }

Where the first item is either block or externalblock (not covered here), the second is the module from which to retrieve the block, and the third the name of the block.

Finally we reactivate PHP capture the page contents into the variable $pageContent clean the output buffer, and pass the variable $pageContent to the layout template using Chisimba's $this->setVar method.

<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>

Create the layout template as layout_tpl.php in the templates/layout directory of your module. It need only contain:

<?php
$objBlocks = $this->getObject('blockfilter', 'dynamiccanvas');
$pageContent = $this->getVar('pageContent');
$pageContent = $objBlocks->parse($pageContent);
echo $pageContent;
?>

This instantiates the block filter from the dynamiccanvas module, and parses the page content against it, then renders the parsed content to the page using echo. This is what parses the JSON blocks to render the actual block content.


We have one more step to perform, and that is that we have to make our module visible to the Module catalogue for installation. To do this, we need to create a register.conf file in the module's top-level directory. 

Create a file called register.conf in the root directory of your module, and add the following code to it:
MODULE_ID: hellochisimba
MODULE_NAME: Hello Chisimba
MODULE_DESCRIPTION: A classic hello world type module to introduce you to chisimba.
MODULE_AUTHORS: Derek Keats
MODULE_RELEASEDATE: 2010 07 21
MODULE_VERSION: 0.01
MODULE_PATH: hellochisimba
MODULE_CATEGORY: hellochisimba

MENU_CATEGORY: user

BLOCK: hello1
BLOCK: hello2
WIDEBLOCK: hello3

TEXT: mod_hellochisimba_name|Hello Chisimba|Hello Chisimba

See the chapter on Module catalogue for an explanation of these strings. For now just cut and paste this into the register.conf file. Note the registration of the blocks hello1, hello2, and the wide block hello3.

Now lets review the module from the MVC perspective. We do not have a model class for this module since we are not yet accessing any data. We have a view (template) and controller only, in addition to which we have a register configuration file. The files our module uses are as follows:

hellochisimba/controller.php
hellochisimba/register.conf
 	hellochisimba/templates/content/default_tpl.php
	hellochisimba/templates/content/dump_tpl.php
	hellochisimba/classes/block_hello1_class_inc.php
	hellochisimba/classes/block_hello2_class_inc.php
	hellochisimba/classes/block_hello3_class_inc.php

This is the bare minimum necessary to have a functioning module based on the JSON templates with block-based rendering. 

The next step is to install your module. Open your Chisimba installation in a web browser, and login as a user with administrative rights. If you still have your default Chisimba installation, the default user with these rights is “admin”, with the password “a” (be sure to change it).

On the Admin menu, locate Module Catalogue, and open Module Catalogue. In Module Catalogue, you will find a link (highlighted in yellow, as shown below) that says Update catalogue. Click that link to be sure that your new module is available for installation. Then enter Hello in the search box and click Search. Your module is called Hello Chisimba, as per your register.conf file. You should see the results below, with Hello Chisimba available for installation.


Click on Install, or tick the tick box and click the Install selected button. You should see the install results below.



Since we gave it a menu category of user in register.conf (MENU_CATEGORY: user), the module will appear on the User menu of the Chisimba toolbar as shown below, where it is called Hello Chisimba. Note that there is no module icon at this stage.



Selecting Hello Chisimba from the menu, as highlighted above, opens the module, and your content is rendered to the screen in a Chisimba interface as shown below.


As you can see, you can have a feature rich module up and running fairly quickly, as the framework takes care of the interface and navigation. You only need to worry about the content and functionality of your module. Leave the rest of the interface to the framework.


A word on JSON templates

The use of JSON templates provides for improved reuse capabilities in Chisimba. Modules created this way will always have their output available as blocks. This means that any other Chisimba module can also use those blocks. For example, try adding the following to your left or right content area in you JSON template:

        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "test1"
        }

What happens?

Not only that, but this method allows blocks to be exposed across multiple Chisimba servers. Try including the following in the middle area of your JSON template:

{ 
    "display" : "externalblock", 
    "server" : "http://www.dkeats.com/", 
    "module" : "blog", 
    "block" : "lastbytag" 
}

This shows the last six blog posts on www.dkeats.com according to what ever the site owner has set as the default tag. Currently, this is 'chisimba'.  Now that is reusability of objects on steroids. Coding all rendering as blocks will make Chisimba even more awesome than it already is at present.

Making your text localizable
In the previous example, we had text that was hard coded in the controller code. This code would never be accepted into a real Chisimba module because it is not multi-lingualized. Therefore, this section addresses multi-lingualization of code using the Chisimba framework. 

The first step in multi-lingualization is to define the text codes in the register.conf file located in the module's directory. The register.conf file provides for two methods of adding text strings. The first one uses the TEXT keyword, and provides a variable of the form 

mod_hellochisimba_greeting

The convention is that the variable is named in three parts mod_modulecode_identifier, where mod indicates that it belongs to a module, modulecode indicates the module that owns it, and identifier is any combination of meaningful character that can identify the string. A description is provided to help translators, and is separated from the text string, containing what will display on the interface, by a pipe (|) character.

Thus, for our example, the register.conf file would contain: 

TEXT: mod_hellochisimba_greeting|A greeting to say hello to someone|Hello there Chisimba user. Welcome to my first module.

Add this line to the register.conf file, and save it.

To make this work, we need to introduce and add the framework language object, which comes from a helper module called language. First, add a property to hold the object:

/**
* @var $objLanguage String object property for holding the 
* language object
* @access public
*/
public $objLanguage;

Then, instantiate the language object in our init() method of the controller. Note, that we instantiate it here so it is available to other methods, which we will add later. In most cases, we would instantiate the class close to where we use it. Insert the code below in the init() method of the controller.

//Instantiate the language object
$this->objLanguage = $this->getObject('language', 'language');

The init() method now contains the following:

public function init()
{
    //Instantiate the language object
    $this->objLanguage = $this->getObject('language', 'language');
    //Assign the value of the greeting to the language element mod_hellochisimba_greeting
    $this->greeting = $this->objLanguage->languageText("mod_hellochisimba_greeting", "hellochisimba");
}

Now if you go to your hellochisimba module, and select it from the menu, you will see that the interface displays:

Language item not found: mod_hellochisimba_greeting from hellochisimba
The reason for this is because you have effectively made a patch, but you have not run the patch through module catalogue. Therefore, you need to look in your register.conf file, and find the line that says

MODULE_VERSION: 0.001

and change it to read

MODULE_VERSION: 0.002

This increases the version number so that module catalogue can find it, and automatically apply your language patch. If you increase the final digit each time you add language elements, you will make sure that you never break the interface with missing language elements. Thus, patching interface elements is very simple in Chisimba.

This will result in a straight rendering of the text string using the languageText() method. Another method allows the replacement of one or more codes in a string with a variable name. This method is code2txt(), and it helps ensure that translations containing variables are rendered in the correct syntax for the translation language. Add the following line from to your register.conf file, increase the version number and save it. Go to module catalogue and apply the patch.


TEXT: mod_hellochisimba_helloperson|The text that the helloChisimba module renders to say hello to a particular user by their name|Hello there [-FIRSTNAME-], you interesting person whose username is [-USERNAME-].  

Note the convention for including the variables, enclosed in [- and -], and in capital letters (actually, they are not case sensitive, but it is easier to see them if they are in capitals stick with the convention).

To see the benefit of this we need to introduce another framework object, the user object. Language and user are probably the two most often used objects in the framework's helper modules.

Rather than cluttering up the init() method with this kind of thing, let us create a method to parse this language element. Into that method, add the following line:

// Get the user object
$objUser = $this->getObject('user', 'security');

User is another framework element that comes from the security module. It provides a set of properties and methods for working with basic user information. Add the following lines to the method:

// Get the first name of the logged in user
$firstName = $objUser->getFirstName(); 
// Get the user name of the logged in user
$userName = $objUser->userName();

This will retrieve the first name and username of the current user, and assign them to variables.

Then we can parse the language item to replace the codes with those values. The code to do that is shown:

$rep = array(  
  'FIRSTNAME' => $firstName, 
  'USERNAME' => $userName);
//Return the string with the codes replaced by the values
return $this->objLanguage->code2Txt("mod_hellochisimba_helloperson", "hellochisimba", $rep);

First we create an array with indexes corresponding to the codes to be replaced, and assign them the values of $firstName and $userName. This array is then passed as a parameter to the code2Txt method of the language object.

Now our method contains the following code:

public function greetUser()
{
        // Get the user object
        $objUser = & $this->getObject('user', 'security');
        // Get the first name of the logged in user
        $firstName = & $objUser->getFirstName(); 
        // Get the user name of the logged in user
        $userName = & $objUser->userName();
        // Set an array of replacements for code2txt
        $rep = array(  
          'FIRSTNAME' => $firstName, 
          'USERNAME' => $userName);
        //Return the string with the codes replaced by the values
        return $this->objLanguage->code2Txt("mod_hellochisimba_helloperson",
		"hellochisimba", $rep); 
}

Now it just remains to change our dispatch method to include:

$this->greeting = $this->greetUser();

Reloading the Hello Chisimba now should produce:

Hello there Administrative, you interesting person whose username is admin.

or whatever information is appropriate to your login credentials. 

This gives us the ability to generate interface elements that are correct for the syntax of a particular language. It allows translators to position the code elements so that when translated into a language, the variable elements are in the correct place.

Summary

Thus in this chapter we have introduced some key concepts of Chisimba programming, including 
the files that make up the basic MVC pattern, and where they are located in a module
how to create a module
how to create a register.conf for a module
how to install the module
how to make it multi-lingual
how to add and patch language items by changing the version number of the module in register.conf
how to run such a patch using module catalogue
how to parse special codes in language elements to replace them with variables.


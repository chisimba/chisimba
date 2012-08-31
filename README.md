Chisimba
========

Chisimba is a PHP framework for building web applications and applications that need a web API. It implements a model-view-controller (MVC) design pattern, implemented on a modular architecture. There is a core framework, and numerous modules that implement functionality ranging from blogs through CMS to a eLearning system. The interface design is flexible and implemented via canvases (skins, or themes). There is an online package management system, and developers can build modules rapidly by generating a working module from which to code.


Chisimba is the product of a collaboration of the 13 African universities involved in the African Virtual Open Initiatives and Resources (AVOIR) project. Its main purpose is to foster capacity building in Software Engineering among African universities through the collaborative development of Free Software. The framework includes contributions from others outside AVOIR as well. Its physical home, and the location of the highest concentration of developers is in the Free Software Innovation Unit at the University of the Western Cape (UWC). The word “Chisimba” is the Chichewa (Malawi) word for the framework used to build a traditional African house.
In order to fully install and use the Chisimba framework to its full potential, it is necessary to install a number of PHP extensions, as well as to have a few PEAR objects on hand.

Please make sure that you have a working Apache installation, as well as a functional database hosted on one of the following database servers:

1. MySQL - 5.1.x
2. PostgreSQL - 8.1

You will also need at least PHP version 5.1.2 or above, however, PHP-5.2.0 is not yet supported. We recommend that you use PHP-5.1.6 for running Chisimba based sites.

The web based installer found in /path/to/webroot/chisimba_framework/installer/index.php will help you configure and install all of the required PEAR components. If a specific module requires an additional PEAR object, it will throw an error message with explicit instructions on how to obtain the specific package required.

NOTE: Some modules require some PHP extensions that are not required elsewhere. For example, in order to use the mail to blog functionality, you will need the IMAP PHP extension. On most GNU/Linux distributions, this is a simple command to the package manager. On Windows based systems, it is as simple as uncommenting the extension in php.ini and downloading the required .dll.

If you have any questions, comments or other issues, please do not hesitate to post a message to our users mailing list found at:

http://avoir.uwc.ac.za/mailman/listinfo/nextgen-users

or on the forums at http://avoir.uwc.ac.za/

Have fun! Enjoy Chisimba!

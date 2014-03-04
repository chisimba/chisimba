NOTE: This module has no end user functionality. The controller is purely for the developer to use in testing the code.

This module provides an interface to the jQuery Jeditable plugin for building an editable area or table cell. It allows a user to click and edit the content of various xhtml elements. User clicks text on web page and the block of text becomes a form. The form contains two parameters: id and value. A user edits content and presses submit button, or return (or onblur if set). The new text is sent to webserver in a form and may be retrieved in PHP and saved. You need return ONLY the text received for VALUE with no headers or page elements, and then the form becomes normal text again. 

If you want to know what the field was that was edited, then you can retrieve id using 
  $fieldName = $this->getParam('id', FALSE);
To retrieve its value, you can use:
  $fieldValue = $this->getParam('value', FALSE);
Note you can also use a variable variable and do
  $id = $this->getParam('id', FALSE);
  $$id = $this->getParam('value', FALSE);

It is based on Jeditable by Mika Tuupola available at:
   http://www.appelsiini.net/projects/jeditable

Here is an example of use:

  // Instantiate the object
  $this->objTh = & $this->getObject('jqeditablehelper', 'jqeditable');
  // Load the javascript library
  $this->objTh->loadJs();
  // The array of control parameters
  $arrayParams =  array('indicator' => 'Saving...',
     'tooltip' => 'Click to edit...',
     'type' => 'textarea',
     'cancel' => 'Cancel',
     'submit' => 'OK');
  // Build the target url in the standard Chisimba way
  $targetUrl = $this->uri(array('action' => 'save'), 'jqeditable');
  // Undo the damage we cause in the uri method
  $targetUrl = str_replace('&amp;', '&', $targetUrl);
  // Build the jquery ready function
  $this->objTh->buildReadyFunction($arrayParams, $targetUrl);
  // Load the ready function in the page header
  $this->objTh->loadReadyFunction();
 
The template containing the editable elements will contain one or more
layers or spans something like:
  <div class="edit" id="dolor">Dolor ipsum stuff</div>

You can retrieve the value using something like
   $str = $this->getParam('value', 'Failed to retrieve value for dolor');
and write it to a database using your modules standard methods. A typical
save method might look something like:
    private function __save()
    {
        $this->setPageTemplate('plain_tpl.php');
        $str = $this->getParam('value', FALSE);
        // ....... put your save code here (not done in this example) ...... 
        $this->setVarByRef('str', $str);
        return "postsave_tpl.php";
    }

Note that I am setting a plain page template and returning only the value retrieved. This ensures that the page and the database are in sync.

Happy Chisimba hacking.


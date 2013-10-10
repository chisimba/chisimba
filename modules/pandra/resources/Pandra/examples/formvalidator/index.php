<?php
/**
 * This form is an example usage of the native Pandra validation API for implementing
 * a server-consolidated validator via jQuery.  A basic child class of
 * PandraColumnFamily has been created containing columns with expected input
 * type definitions.
 *
 * A hybrid of validation techniques has been defined, both via the getColumn()->setValue()
 * method, as well as via direct calls to PandraValidator::check() for simple spam
 * agent detection.
 *
 * An active connection is not required for this demonstration, as no data is saved.
 * 
 */

session_start();

if (!empty($_SESSION['badAgent'])) {
//    echo 'BAD AGENT';
//    exit;    // bail on problem agents
}

error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../../config.php');

/**
 * Simple PandraColumnFamily class with a few columns and type defnitions
 */
class Customer extends PandraColumnFamily {
    public function init() {
        $this->addColumn('firstName', array('minlength=3', 'maxlength=20'));
        $this->addColumn('emailAddress', 'email');
        $this->addColumn('age', 'int');
    }
}

$customer = new Customer();

// text inputs will be created for every column defined in the class
$cColumns = $customer->listColumns();

// minimum # of seconds between calling the script (via GET), and posting the actual form
$minPostWindow = 5;

/**
 * translate results to an expected <result> response
 * @param string $fieldName field label
 * @param array $errorMessages field error messages
 */
function ajaxResult($fieldName = '', $errorMessages = array()) {
	header('Content-type: text/xml');        
	echo "<result>";
	if (empty($errorMessages)) {
            echo "OK";
	} else {
            $errorMessages = array_pop($errorMessages);            
            foreach ($errorMessages[$fieldName] as $message) {
                echo htmlentities($message);
            }
	}
	echo "</result>";
}

// validation error
$vError = FALSE;

// form OK, 'saved'
$formSaved = FALSE;

// POST form processing.
if ($_SERVER['REQUEST_METHOD'] == "POST") {

	$errorMessages = array();

        // 'validate' post method validates a single column against its type definition
        // (ajax call)
	if (isset($_POST['method']) && $_POST['method'] == 'validate') {
            
            // retrieve the validating column from the customer instance
            $column = $customer->getColumn($_POST['fieldName']);
        
            if ($column != NULL && FALSE === $column->setValue($_POST['fieldValue'])) {
                    // return last error on the column if setValue failed
                    ajaxResult($_POST['fieldName'], $column->getErrors());
            } else {
                    // everything OK. (fieldName's which are not columns in customer also being skipped)
                    ajaxResult();
            }
            exit;

        // 'save' post method validates all form data and saves.
	} elseif (isset($_POST['method']) && $_POST['method'] == 'save') {
		// Check that the session was started via a GET, and that the referrer is ourselves
		$honeyCatch = ($_SESSION['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR']) || (mktime() - $_SESSION['StartTime'] <= $minPostWindow) || empty($_SERVER['HTTP_REFERER']);

                // 'url' will be a hidden field which should always remain empty,
                // and is not shown to the user.  This will serve as the honeypot
                if (!$honeyCatch && !empty($_POST['url'])) {
                    $honeyValue = $_POST['url'];                    
                    $eTmp = '';
                    // validate an individual field
                    $honeyCatch = !PandraValidator::check($_POST['url'], 'url', 'isempty', $etmp);
                }

		// It's just a regularly POST'ed form, so validate before we 'save'
		if (!$honeyCatch) {
                    $vError = !$customer->populate($_POST);
                    
                    // PandraColumnFamily maintains a running log of all errors on the instance, in this case
                    // we can just extract the unique column keys
                    $validationErrors = array();
                    foreach ($customer->errors as $errors) {
                        foreach ($errors as $column => $colError) {
                            $validationErrors[$column] = array_pop($colError);
                        }                       
                    }
		} else {
                    // Do something here if it looks to be a bot.
                    // I'll just flag the session as bogus, and stop the script
                    echo 'BAD AGENT';
                    $_SESSION['badAgent'] = TRUE;
                    exit;
                }

                // no validation errors, and nothing in 'url'?
		if (!$vError && !$honeyCatch) {
			// It's all looking good - we can save the form data or perform whatever additional actions here
                        // $customer->save();
			$formSaved = TRUE;
		}
	}
} else {
	$_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['StartTime'] = time();

        // init
        foreach ($cColumns as $colName) $_POST[$colName] = '';
}

// Begin HTML and JS form.  Input fields will change colour depending upon their state
$colourNeutral = '#FFFFFF';
$colourGood = '#B5FBAD';
$colourBad = '#FBADBA';
?>
<html>
    <head>
        <style type="text/css">
            .deliciousBeeTreats {
                display: none;
            }
            body {
                color: #333333; padding:0px; margin:10px;
                font-size: 0.7em;
                font-family: Verdana, "Verdana CE",  Arial, "Arial CE", "Lucida Grande CE", lucida, "Helvetica CE", sans-serif;
                height:500px;
            }
        </style>
        
        <script type="text/javascript" src="../jquery-1.3.2.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {

                function validateInput(fieldName) {
                    $("#validateResult" + fieldName).html('<img src="spinner_mac.gif">');

                    $.post("<?=$_SERVER['SCRIPT_NAME']?>", {
                        method : 'validate',
                        fieldName: fieldName,
                        fieldValue: $("input.#" + fieldName).val()
                    }, function(xml) {
                        var resultOK = ($("result", xml).text() == 'OK');
                        
                        $("#validateResult" + fieldName).html(resultOK ? '': $("result", xml).text());
                        $("input.#" + fieldName).css('background', resultOK ? '<?=$colourGood?>' : '<?=$colourBad?>');                        
                    });
                }

                // Setup the 'old value' variables, and new inputs with blur event handlers
                <? foreach ($cColumns as $fieldName) { ?>
                    var old<?=$fieldName?> = <?=(empty($_POST[$fieldName]) ? "null" : "'".$_POST[$fieldName]."'")?>;

                    // add blur event handler
                    $("#<?=$fieldName?>").blur(function(e){
                        fieldValue = document.getElementById('<?=$fieldName?>').value;
                        if (fieldValue != old<?=$fieldName?>) {
                            e.preventDefault();
                            validateInput('<?=$fieldName?>');
                        }
                        old<?=$fieldName?> = fieldValue;
                    });
                <? } ?>
            });
        </script>
    </head>
    <body>
        <form method="post" action="<?=$_SERVER['PHP_SELF'];?>">
            <input type="hidden" name="method" id="method" value="save">
            <input type="text" class="deliciousBeeTreats" name="url" id="url" value="">
            <h2>Test Validation Form</h2>
            <?
            if ($vError) {
                echo "<h4>There were errors, try again</h4>";
            } else if ($formSaved) {
                    echo "<h4>*** SAVED</h4>";
                }
            ?>
            <table>
                <? foreach ($cColumns as $columnName) { ?>
                <tr>
                    <td><?=$columnName;?></td>
                    <td>
                        <input style="background-color:<?=(isset($validationErrors[$columnName])) ? $colourBad : $colourNeutral;?>;"
                               type="text"
                               name="<?=$columnName?>"
                               id="<?=$columnName?>"
                               value="<?=$_POST[$columnName]?>">
                    </td>
                    <td><div id="validateResult<?=$columnName?>"
                             name="validateResult<?=$columnName?>"><?=(isset($validationErrors[$columnName]) ? $validationErrors[$columnName] : '')?>
                        </div>
                    </td>
                </tr>
                <? } ?>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" value="Submit">
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
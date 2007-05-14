<?php 
// ----------------------------------------------------------------------------------
// PHP Script: custom_test.php
// ----------------------------------------------------------------------------------
/*
 * This is an online demo of RDF API for PHP. 
 * You can paste RDF code into the text field below and choose how the data should be 
 * processed. It's possible to parse, serialize, reify and query the data.
 * The size of your RDF code is limited to 10.000 characters, due to resource restrictions.
 * 
 * @author Chris Bizer <chris@bizer.de>
 * @autor Seairth Jacobs <seairth@seairth.com>
 * @author Daniel Westphal <dawe@gmx.de>
 *
 */
?>

<head>
    <title>RAP - RDF API for PHP online demo</title>
    <link href="phpdoc.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%" border="0">
<TR> 
  <TD align=left vAlign=top>
   <DIV align="right"><BR>
      &nbsp;<a href="http://www.w3.org/RDF/" target="_blank"><img src="rdf_metadata_button.gif" width="95" height="40" border="0" alt="RDF Logo"></a> 
      &nbsp;<a href="http://www.php.net" target="_blank"><img src="php_logo.gif" width="120" height="64" border="0" alt="PHP Logo"></a></div>
    <H3>RDF API for PHP</H3>
    <H1>Online API Demo</H1><BR>
     
<?php 
// Function: Output a string with line numbers
function echo_string_with_linenumbers ($input_string)
{
    $input_string = str_replace (" ", "&nbsp;&nbsp;", $input_string);
    $data_lines = explode('&lt;br />', str_replace('<', '&lt;', nl2br($input_string)));
    $return = '<table>';
    for ($i = 0; $i < (count($data_lines)); $i++) {
        $return .= '<TR><TD width="30" valign="top">' . ($i + 1) . '.</TD><TD>' . $data_lines[$i] . '</TD></TR>';
    } ;
    echo $return . '</TABLE>';
} ;
// Test if the form is submitted or the code is too long
if (!isset($_POST['submit']) OR (strlen($_POST['RDF']) > 10000)) {
    ?>

<form method="post" action="<?php echo $HTTP_SERVER_VARS['PHP_SELF'];
    ?>"> 
<p>This is an online demo of <a href="http://www.wiwiss.fu-berlin.de/suhl/bizer/rdfapi/index.html">RAP - RDF API for PHP V0.8</a> . You can paste RDF code into the text field below and choose how the data should be processed. It's possible to parse, serialize, reify and query the data.</p>
<p>The size of your RDF code is limited to 10.000 characters, due to resource restrictions.</p>
<H3>Please paste RDF code here:</H3>

<?php 
    // Show error message if the rdf is too long
    if ((isset($_POST['submit']) AND (strlen($_POST['RDF']) > 10000))) {
        echo "<center><h2>We're sorry, but your RDF is bigger than the allowed size</h2></center>";
    } ; 
    // //////////////////////////////////////////////////////////////////
    // Show input form
    // //////////////////////////////////////////////////////////////////
    ?>
<p><textarea cols="100" rows="20" name="RDF"><rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:ex="http://example.org/stuff/1.0/"
xmlns:s="http://description.org/schema/">
<rdf:Description rdf:about="http://www.w3.org/Home/Lassila">
<s:Creator>
<rdf:Description rdf:nodeID="b85740">
<rdf:type rdf:resource="http://description.org/schema/Person"/>
<ex:Name rdf:datatype="http://www.w3.org/TR/xmlschema-2#name">Ora Lassila</ex:Name>
<ex:Email rdf:datatype="http://www.w3.org/TR/xmlschema-2#string">lassila@w3.org</ex:Email>
</rdf:Description>
</s:Creator>
</rdf:Description>
</rdf:RDF></textarea>
        <br />
      </p>
      <H3>Please choose the output format(s):</H3>
      <table width="70%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td > <div align="center"> 
              <input name="view_triple" type="checkbox" id="view_triple" value="1" checked>
            </div></td>
          <td><strong>Table with RDF triples.</strong></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td> <div align="center"> 
              <input name="serialize" type="checkbox" id="serialize" value="1" checked>
            </div></td>
          <td><strong>Serialize model back to RDF.</strong></td>
        </tr>
        <TR><TD></TD><TD><table>
                <tr> 
          <td><div align="center"> 
              <input name="serial_attributes" type="checkbox" id="serial_attributes" value="1">
            </div></td>
          <td>Serialize to RDF using attributes for RDF properties whereever possible.</td>
        </tr>
        <tr> 
          <td><div align="center"> 
              <input name="serial_entities" type="checkbox" id="serial_entities" value="1">
            </div></td>
          <td>Serialize to RDF using XML entities for URIs.</td>
        </tr>
        <tr> 
          <td><div align="center"> 
              <input name="serial_wo_qnames" type="checkbox" id="serial_wo_qnames" value="1">
            </div></td>
          <td>Serialize to RDF without qnames for RDF tags.</td>
        </tr>
        </table>        
        </TD></TR>
        <tr> 
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td><div align="center">
              <input name="view_dump" type="checkbox" id="view_dump" value="1">
            </div></td>
          <td><strong>toSting: Output the model as text.</strong></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td><div align="center"> 
              <input name="reify" type="checkbox" id="reify" value="1">
            </div></td>
          <td> <strong>Reify the input model before output.</strong></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td><div align="center"> </div></td>
          <td><strong>Query model (&quot;blank&quot; will match 
            anything):</strong></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><br>
            <table width="99%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="21%" > <div align="left">Subject:</div></td>
                <td width="79%"><input name="query_subject" type="text" id="query_subject2" size="50">
                  <select name="subject_kind" id="object_kind">
                    <option value="resource" selected>Resource</option>
                    <option value="bnode">BlankNode</option>
                  </select></td>
              </tr>
              <tr> 
                <td >Predicate:</td>
                <td><input name="query_predicate" type="text" id="query_predicate2" size="50"></td>
              </tr>
              <tr> 
                <td >Object:</td>
                <td><input name="query_object" type="text" id="query_object2" size="50">
                  <select name="object_kind" id="object_kind">
                    <option value="resource" selected>Resource</option>
                    <option value="literal">Literal</option>
                    <option value="bnode">BlankNode</option>
                  </select>
                  <br>Object datatype: <input name="query_object_datatype" type="text" id="query_object_datatype2" size="47">
                  </td>
              </tr>
            </table></td>
        </tr>
      </table>
      <p><br />
        <br />        
        <input type="submit" name="submit" value="submit me!">
      </p>
      </form>
<?php
} else {
    // ///////////////////////////////////////////////////////////////
    // Process RDF
    // (if submitted and RDF smaller than 10000 chars)
    // ///////////////////////////////////////////////////////////////
    include 'RDF.php';
    include 'RDF/Model/Memory.php';
    // Prepare RDF
    $rdfInput = stripslashes($_POST['RDF']);
    // Show the submitted RDF
    echo "<BR><H3>Your original RDF input:</h3><BR>";
    echo_string_with_linenumbers($rdfInput);
    // Create a new Model_Memory
    $model =& new RDF_Model_Memory();
    // Load and parse document
    $parser =& new RDF_Parser();
    $model =& $parser->generateModel($rdfInput);
    // Set the base URI of the model
    $model->setBaseURI("http://www3.wiwiss.fu-berlin.de" . $HTTP_SERVER_VARS['PHP_SELF'] . "/DemoModel#");
    // Execute query on model if submitted
    if ($_POST['query_subject'] != '' OR $_POST['query_predicate'] != '' OR $_POST['query_object'] != '') {
        $comment_string = "<BR><H3>The following query has been executed:</H3><BR>";

        $query_subj = null;
        $query_pred = null;
        $query_obj = null;

        if ($_POST['query_subject'] != '') {
            if ($_POST['subject_kind'] == 'resource') {
                $query_subj =& RDF_Resource::factory($_POST['query_subject']);
            } else {
                $query_subj =& RDF_BlankNode::factory($_POST['query_subject']);
            } 
            $comment_string .= "Subject = " . $_POST['query_subject'] . "<BR>";
        } ;

        if ($_POST['query_predicate'] != '') {
            $query_pred =& RDF_Resource::factory($_POST['query_predicate']);
            $comment_string .= "Predicate = " . $_POST['query_predicate'] . "<BR>";
        } ;

        if ($_POST['query_object'] != '') {
            if ($_POST['object_kind'] == 'resource') {
                $query_obj =& RDF_Resource::factory($_POST['query_object']);
            } elseif ($_POST['object_kind'] == 'literal') {
                $query_obj =& RDF_Literal::factory($_POST['query_object']);
                if ($_POST['query_object_datatype'] != '') {
                    $query_obj->setDatatype($_POST['query_object_datatype']);
                } 
            } else {
                $query_obj =& RDF_BlankNode::factory($_POST['query_object']);
            } ;
            $comment_string .= "Object = " . $_POST['query_object'] . "<BR>";
        } ; 
        // Execute query and display what has been done
        $model = $model->find($query_subj, $query_pred, $query_obj);
        echo $comment_string;
    } 
    // Reify the model if checked in submitted form
    if (isset($_POST['reify']) and $_POST['reify'] == "1") {
        $model = &$model->reify();
        echo "<BR><BR><h3>Your original model has been refied.</h3><BR>";
    } ;
    // Output Triples as Table if checked in submitted form
    if ($_POST['view_triple'] == "1") {
        echo "<BR><BR><h3>View input as HTML table:</h3><BR>";
        RDF_Util::writeHTMLTable($model);
        echo "<P>";
    } ;
    // serialize model to RDF with default configuration if checked in submitted form
    if ($_POST['serialize'] == '1') {
        // Create Serializer
        $ser =& new RDF_Serializer();
        $msg_string = '';
        if (isset($_POST['serial_attributes']) and $_POST['serial_attributes'] == '1') {
            $ser->configUseAttributes(true);
            $msg_string .= 'Use Attributes ';
        } ;
        if (isset($_POST['serial_entities']) and $_POST['serial_entities'] == '1') {
            $ser->configUseEntities(true);
            $msg_string .= 'Use XML Entities ';
        } ;
        if (isset($_POST['serial_wo_qnames']) and $_POST['serial_wo_qnames'] == '1') {
            $ser->configUseQnames(false);
            $msg_string .= 'Without Qnames ';
        } ;
        $rdf = &$ser->serialize($model);
        echo "<p><BR><h3>Serialization of input model";
        if (isset($msg_string)) echo " (Options: " . $msg_string . ")";
        echo ":</h3>";
        echo_string_with_linenumbers($rdf);
    } ;
    // Show dump of the model including triples if submitted in form
    if (isset($_POST['view_dump']) and $_POST['view_dump'] == '1') {
        echo "<BR><BR><h3>Dump of the model including triples</h3>";
        echo_string_with_linenumbers($model->toStringIncludingTriples());
    } ;

    ?> <center><a href="<?php echo $HTTP_SERVER_VARS['PHP_SELF'] ?>"><h2>Go back to input form.</h2></a></center><?php
} // end of "form submitted"

?>

<BR><H1>Feedback</H1>

</p>
    <p>Please send bug reports and other comments to <a href="mailto:chris@bizer.de">Chris Bizer</a>.<br>
</p></body>
</html>

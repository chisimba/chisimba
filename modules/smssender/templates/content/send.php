<script type="text/javascript">
<!-- Dynamic Version by: Nannette Thacker -->
<!-- http://www.shiningstar.net -->
<!-- Original by :  Ronnie T. Moore -->
<!-- Web Site:  The JavaScript Source -->
<!-- Use one function for multiple text areas on a page -->
<!-- Limit the number of characters per textarea -->

function textCounter(field,cntfield,maxlimit) {
if (field.value.length > maxlimit) // if too long...trim it!
field.value = field.value.substring(0, maxlimit);
// otherwise, update 'characters left' counter
else
cntfield.value = maxlimit - field.value.length;
}
</script>
<?php

// Load scriptaclous since we can no longer guarantee it is there
$scriptaculous = $this->getObject('scriptaculous', 'prototype');
$this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));

if ($mode == 'fixup') {
    $number = $this->getParam('autocomplete_parameter');
    $message = $this->getParam('message1');
    echo '<h1 class="error">Error: Message Could Not Be Sent</h1><br />';
    echo '<ul><li>'.$errorMessage.'</li></ul>';
} else {
    $number = '';
    $message = '';
    echo '<h1>Send a New SMS</h1><br />';
}

?>
<style type="text/css">
div.autocomplete {
      position:absolute;
      width:250px;
      background-color:white;
      border:1px solid #888;
      margin:0px;
      padding:0px;
    }
    div.autocomplete ul {
      list-style-type:none;
      margin:0px;
      padding:0px;
    }
    div.autocomplete ul li.selected { background-color: #ffb;}
    div.autocomplete ul li {
      list-style-type:none;
      display:block;
      margin:0;
      padding:2px;
      height:32px;
      cursor:pointer;
    }
</style>
<form name="myForm"
action="<?php $this->uri(array('action' => 'sendmessage')) ?>"
method="post">
<table  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><label for="autocomplete">To:</label></td>
    <td><input type="text" id="autocomplete" name="autocomplete_parameter" value="<?php echo $number; ?>"/>
    <div id="autocomplete_choices" class="autocomplete"></div></td>
  </tr>
  <tr>
    <td valign="top" width="100"><label for="themessage">Message</label></td>
    <td><textarea id="themessage" name="message1" wrap="physical" cols="28" rows="5"
onkeydown="textCounter(document.forms['myForm'].message1,document.forms['myForm'].remLen1,125)"
onkeyup="textCounter(document.forms['myForm'].message1,document.forms['myForm'].remLen1,125)"><?php echo $message; ?></textarea>
      <br />
      <input readonly="readonly" type="text" name="remLen1" size="3" maxlength="3" value="125" />
characters left</td>
  </tr>
</table>
<input type="submit" name="sendsms" value="Send SMS" />
</form>
<script type="text/javaScript">
//<![CDATA[
    var pars   = 'module=smssender&action=listusers';
    new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "index.php", {parameters: pars});
//]]>
</script>
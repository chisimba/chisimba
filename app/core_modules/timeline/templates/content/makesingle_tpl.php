<?php
/*
 * Created on Jan 28, 2007
 */
?>
<style type="text/css">
.label {
	text-align:right
}
</style>

<h1><?php echo $this->objLanguage->languageText("mod_timeline_maketitle", "timeline"); ?></h1>
<br />
<p><?php echo $this->objLanguage->languageText("mod_timeline_createinstr", "timeline"); ?></p>
<form id="eventdetails">

<table>
<tr>
<td class="label"><label><?php echo $this->objLanguage->languageText("mod_timeline_eventtitle", "timeline"); ?></label></td><td><input size="50" id="etitle" /></td>
</tr>
<tr>
<td class="label"><label><?php echo $this->objLanguage->languageText("mod_timeline_eventstart", "timeline"); ?> *</label></td><td><input  size="50" id="estart" value="Jan 31 2005 00:00:00 GMT" /></td>
</tr>

<tr>
<td class="label"><label><?php echo $this->objLanguage->languageText("mod_timeline_eventend", "timeline"); ?></label></td><td><input size="50" id="eend" /></td>
</tr>
<tr>
<td class="label"><label><?php echo $this->objLanguage->languageText("mod_timeline_eventlink", "timeline"); ?></label></td><td><input size="50" id="elink" /></td>
</tr>
<tr>
<td class="label"><label><?php echo $this->objLanguage->languageText("mod_timeline_eventimgurl", "timeline"); ?></label></td><td><input size="50" id="eimg" /></td>
</tr>
<td class="label"><label><?php echo $this->objLanguage->languageText("mod_timeline_eventdesc", "timeline"); ?></label></td><td><textarea rows="5" cols="50" id="edesc" ></textarea></td>
</tr>
</table>

<a href="javascript:generateEventXML()"><?php echo $this->objLanguage->languageText("mod_timeline_generatexml", "timeline"); ?></a><br /><br />

<textarea name="results" id="results" rows="10" cols="80">
</textarea>
</form>
<p><?php echo $this->objLanguage->languageText("mod_timeline_makecredit", "timeline"); ?></p>
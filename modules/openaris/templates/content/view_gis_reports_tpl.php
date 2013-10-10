<?php
/**
 * ahis View GIS reports Template
 *
 * Template for viewing google earth gis reports
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
 * @package   ahis
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: view_gis_reports_tpl.php 13646 2009-06-10 09:40:28Z nic $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

$objSysconfig = $this->getObject('dbsysconfig','sysconfig');
$apiKey = $objSysconfig->getValue('google_maps_key','openaris');

$imageFolder = $this->objConfig->getsiteRoot()."skins/ahisskin/images";


$headers = '<style>
<!--
 /* Font Definitions */
 @font-face
	{font-family:Calibri;
	panose-1:2 15 5 2 2 2 4 3 2 4;
	mso-font-charset:0;
	mso-generic-font-family:swiss;
	mso-font-pitch:variable;
	mso-font-signature:-1610611985 1073750139 0 0 159 0;}
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-parent:"";
	margin-top:0in;
	margin-right:0in;
	margin-bottom:10.0pt;
	margin-left:0in;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:Calibri;
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";}
p.MsoHeader, li.MsoHeader, div.MsoHeader
	{margin-top:0in;
	margin-right:0in;
	margin-bottom:10.0pt;
	margin-left:0in;
	line-height:115%;
	mso-pagination:widow-orphan;
	tab-stops:center 3.0in right 6.0in;
	font-size:11.0pt;
	font-family:Calibri;
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";}
p.MsoFooter, li.MsoFooter, div.MsoFooter
	{margin-top:0in;
	margin-right:0in;
	margin-bottom:10.0pt;
	margin-left:0in;
	line-height:115%;
	mso-pagination:widow-orphan;
	tab-stops:center 3.0in right 6.0in;
	font-size:11.0pt;
	font-family:Calibri;
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";}
 /* Page Definitions */
 @page
	{mso-footnote-separator:url("p2search_files/header.htm") fs;
	mso-footnote-continuation-separator:url("p2search_files/header.htm") fcs;
	mso-endnote-separator:url("p2search_files/header.htm") es;
	mso-endnote-continuation-separator:url("p2search_files/header.htm") ecs;}
@page Section1
	{size:8.5in 11.0in;
	margin:1.0in 1.25in 1.0in 1.25in;
	mso-header-margin:.5in;
	mso-footer-margin:.5in;
	mso-paper-source:0;}
div.Section1
	{page:Section1;}
div.MsoNormal1 {mso-style-parent:"";
	margin-top:0in;
	margin-right:0in;
	margin-bottom:10.0pt;
	margin-left:0in;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:Calibri;
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";}
li.MsoNormal1 {mso-style-parent:"";
	margin-top:0in;
	margin-right:0in;
	margin-bottom:10.0pt;
	margin-left:0in;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:Calibri;
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";}
p.MsoNormal1 {mso-style-parent:"";
	margin-top:0in;
	margin-right:0in;
	margin-bottom:10.0pt;
	margin-left:0in;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:Calibri;
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";}
p.MsoNormal11 {mso-style-parent:"";
	margin-top:0in;
	margin-right:0in;
	margin-bottom:10.0pt;
	margin-left:0in;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:Calibri;
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";}
p.MsoNormal12 {mso-style-parent:"";
	margin-top:0in;
	margin-right:0in;
	margin-bottom:10.0pt;
	margin-left:0in;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:Calibri;
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";}
-->
</style>
<!--[if gte mso 10]>
<style>
 /* Style Definitions */
 table.MsoNormalTable
	{mso-style-name:"Table Normal";
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-parent:"";
	mso-padding-alt:0in 5.4pt 0in 5.4pt;
	mso-para-margin:0in;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman";
	mso-ansi-language:#0400;
	mso-fareast-language:#0400;
	mso-bidi-language:#0400;}
</style>
<![endif]-->
<script  type="text/javascript" src="http://www.google.com/jsapi?key='.$apiKey.'">
</script>

<script  type="text/javascript">
google.load("earth", "1");
var ge = null;

function init() {
  google.earth.createInstance("map3d", initCallback, failureCallback);
}

function initCallback(pluginInstance) {
  ge = pluginInstance;
  ge.getWindow().setVisibility(true); // required!

  ge.getNavigationControl().setVisibility(ge.VISIBILITY_AUTO);

  // add some layers
  ge.getLayerRoot().enableLayerById(ge.LAYER_BORDERS, true);
  ge.getLayerRoot().enableLayerById(ge.LAYER_ROADS, true);
}

function failureCallback(errorCode) {
  alert("Failure loading the Google Earth Plugin: " + errorCode);
}

// NOTE:  This JSON object can be produced in many ways, including having the entire page generated by PHP, with a SQL query returning all possible rows in this format.  Searches are implemented on the client in JavaScript in this design.

// "row" is 0-based, to make lookup work
var dataJSONObject = '.$jsonData.';

function submitQuery() {
	var now = new Date();
	
	if (document.getElementById("txtOutbreakRefNo").value > "") {
		//alert("Searching by Ref No");
		showResults();
	} else if (document.getElementById("lstCountry").selectedIndex > 0) {
		//alert("Searching by Country");
		
		var year = document.getElementById("txtYear1").value;
		var month = document.getElementById("txtMonth1").value;
		
		if (!year || isNaN(year) || year > now.getFullYear()) {
			alert ("Please enter a valid year");
			return false;
		}
		if (isNaN(month) || month > 12 || month < 1) {
			alert ("Please enter a valid month (1-12)");
			return false;
		}
		if (document.getElementById("lstAnimal1").selectedIndex == 0) {
			alert ("Please select the species");
			return false;
		}
		if (document.getElementById("lstDiseaseType1").selectedIndex == 0) {
			alert ("Please select the disease");
			return false;
		}
		showResults();
	} else if (document.getElementById("lstDistrict").selectedIndex > 0) {
		//alert("Searching by District");
		
		var year = document.getElementById("txtYear2").value;
		var month = document.getElementById("txtMonth2").value;
		
		if (!year || isNaN(year) || year > now.getFullYear()) {
			alert ("Please enter a valid year");
			return false;
		}
		if (isNaN(month) || month > 12 || month < 1) {
			alert ("Please enter a valid month (1-12)");
			return false;
		}
		if (document.getElementById("lstAnimal2").selectedIndex == 0) {
			alert ("Please select the species");
			return false;
		}
		if (document.getElementById("lstDiseaseType2").selectedIndex == 0) {
			alert ("Please select the disease");
			return false;
		}
		
		showResults();
	} else {
		alert("Insufficient Criteria");
	}
}

function showResults() {

	document.getElementById("btnSubmit").style.display = "none";
	document.getElementById("btnCancel").style.display = "none";
	document.getElementById("btnBack").style.display = "inline";
	document.getElementById("btnNew").style.display = "inline";

	//alert(">>>" + document.getElementById("lstDiseaseType1").options[document.getElementById("lstDiseaseType1").selectedIndex].value);


	var lastPoint;
	for(point in dataJSONObject.results) {
		
		//alert("" + dataJSONObject.results[point].diseasetype );
		
		if (document.getElementById("txtOutbreakRefNo").value > "") {
			if (dataJSONObject.results[point].refno == document.getElementById("txtOutbreakRefNo").value) {
				doPoint(point);
				lastPoint = point;
			}
		} else if (document.getElementById("lstCountry").selectedIndex > 0) {
			
			if (dataJSONObject.results[point].geolayer3 == document.getElementById("lstCountry").options[document.getElementById("lstCountry").selectedIndex].value &&	
				dataJSONObject.results[point].year == document.getElementById("txtYear1").value &&	
				dataJSONObject.results[point].month == document.getElementById("txtMonth1").value &&	
				dataJSONObject.results[point].animal == document.getElementById("lstAnimal1").options[document.getElementById("lstAnimal1").selectedIndex].value &&	
				dataJSONObject.results[point].diseasetype == document.getElementById("lstDiseaseType1").options[document.getElementById("lstDiseaseType1").selectedIndex].value) {
				doPoint(point);
				lastPoint = point;
			}
		} else if (document.getElementById("lstDistrict").selectedIndex > 0) {
			if (dataJSONObject.results[point].geolayer2 == document.getElementById("lstDistrict").options[document.getElementById("lstDistrict").selectedIndex].value &&	
				dataJSONObject.results[point].year == document.getElementById("txtYear2").value &&	
				dataJSONObject.results[point].month == document.getElementById("txtMonth2").value &&	
				dataJSONObject.results[point].animal == document.getElementById("lstAnimal2").options[document.getElementById("lstAnimal2").selectedIndex].value &&	
				dataJSONObject.results[point].diseasetype == document.getElementById("lstDiseaseType2").options[document.getElementById("lstDiseaseType2").selectedIndex].value) {
				doPoint(point);
				lastPoint = point;
			}
		}
	}
	if (dataJSONObject.results[lastPoint]) {
		var la = ge.createLookAt("");
		la.set(Number(dataJSONObject.results[lastPoint].lat), Number(dataJSONObject.results[lastPoint].long),
			0, ge.ALTITUDE_RELATIVE_TO_GROUND,0, 0, 3000000);
		ge.getView().setAbstractView(la);
	} else {
		document.getElementById("btnSubmit").style.display = "inline";
		document.getElementById("btnCancel").style.display = "inline";
		document.getElementById("btnBack").style.display = "none";
		document.getElementById("btnNew").style.display = "none";

		alert("No matching reports found.");
	}
}

function doPoint(point) {
		var mylat = Number(dataJSONObject.results[point].lat);
		var mylong = Number(dataJSONObject.results[point].long);

		var placemark = ge.createPlacemark(dataJSONObject.results[point].row);
		placemark.setName("" + dataJSONObject.results[point].locationname);
		
		// Placemark/Point
		var point = ge.createPoint("" + dataJSONObject.results[point].refno);
		var lookAt = ge.getView().copyAsLookAt(ge.ALTITUDE_RELATIVE_TO_GROUND);
		point.setLatitude(mylat);
		point.setLongitude(mylong);
		placemark.setGeometry(point);
		
		// Placemark/Style
		var style = ge.createStyle("");
		placemark.setStyleSelector(style);
		
		// Placemark/Style/IconStyle
		var icon = ge.createIcon("");
		icon.setHref("http://maps.google.com/mapfiles/kml/paddle/red-circle.png");
		style.getIconStyle().setIcon(icon);
		
		// add the placemark to Earth
		ge.getFeatures().appendChild(placemark);

		google.earth.addEventListener(placemark, "mouseover", function(event) {
		  // prevent the default balloon from popping up
		  event.preventDefault();
		  
		  var balloon = ge.createHtmlStringBalloon("");
		  balloon.setFeature(event.getTarget());
		  balloon.setMaxWidth(300);
		  		  
		  // balloon content
		  var point = event.getTarget().getId();

		  balloon.setContentString( "" + 
		  dataJSONObject.results[point].geolayer3 + ", " + dataJSONObject.results[point].locationname + "<BR/><BR/>" +
		  "Outbreak Summary" + "<BR/><BR/>" +
		  "<table>" +
		  //"<tr><td>Period:</td><td>" + dataJSONObject.results[point].period + "</td></tr>" +
		  "<tr><td>Country:</td><td>" + dataJSONObject.results[point].geolayer3 + "</td></tr>" +
		  "<tr><td>District:</td><td>" + dataJSONObject.results[point].geolayer2 + "</td></tr>" +
		  "<tr><td>Location:</td><td>" + dataJSONObject.results[point].locationname + "</td></tr>" +
		  "<tr><td>Outbreak Start:</td><td>" + dataJSONObject.results[point].outbreakstart + "</td></tr>" +
		  "<tr><td>Animal:</td><td>" + dataJSONObject.results[point].animal + "</td></tr>" +
		  "<tr><td>Disease:</td><td>" + dataJSONObject.results[point].diseasetype + "</td></tr>" +
		  "<tr><td>Cases:</td><td>" + dataJSONObject.results[point].cases + "</td></tr>" +
		  "<tr><td>Deaths:</td><td>" + dataJSONObject.results[point].deaths + "</td></tr>" +
		  "<tr><td>Destroyed:</td><td>" + dataJSONObject.results[point].destroyed + "</td></tr>" +
		  "<tr><td>Slaughtered:</td><td>" + dataJSONObject.results[point].slaughtered + "</td></tr>" +
		  //"<tr><td>Culled:</td><td>" + dataJSONObject.results[point].culled + "</td></tr>" +
		  "<tr><td>Vaccinated:</td><td>" + dataJSONObject.results[point].vaccinated + "</td></tr>" +
		  "<tr><td>Report date:</td><td>" + dataJSONObject.results[point].reportdate + "</td></tr>" +
		  "<tr><td>Source:</td><td>" + dataJSONObject.results[point].source + "</td></tr>" +
		  "</table>");
		  
		  ge.setBalloon(balloon);
		});
}
</script>';

$this->appendArrayVar('headerParams', $headers);
$this->appendArrayVar('bodyOnLoad', "init();");

?>

<!--[if gte mso 9]><xml>
 <o:shapedefaults v:ext="edit" spidmax="1049"/>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <o:shapelayout v:ext="edit">
  <o:idmap v:ext="edit" data="1"/>
 </o:shapelayout></xml><![endif]-->

<div class=Section1>

<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0 width=978
 style='width:571.5pt;margin-left:5.4pt;border-collapse:collapse;border:none;
 mso-border-alt:solid black .5pt;mso-yfti-tbllook:1184;mso-padding-alt:0in 5.4pt 0in 5.4pt;
 mso-border-insideh:.5pt solid black;mso-border-insidev:.5pt solid black'>
 <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;height:13.6pt'>
  <td width=268 valign=top style='width:202.5pt;border:solid black 1.0pt;
  mso-border-alt:solid black .5pt;padding:0in 5.4pt 0in 5.4pt;height:13.6pt'>
  <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b style='mso-bidi-font-weight:normal'>Outbreak
  Data<o:p></o:p></b></p>
  </td>
  <td width=704 valign=top style='width:369.0pt;border:solid black 1.0pt;
  border-left:none;mso-border-left-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  padding:0in 5.4pt 0in 5.4pt;height:13.6pt'>
  <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b style='mso-bidi-font-weight:normal'>Passive
  Surveillance Outbreak<o:p></o:p></b></p>
  </td>
 </tr>
 <tr style='mso-yfti-irow:1;mso-yfti-lastrow:yes;height:283.0pt'>
  <td width=268 valign=top style='width:202.5pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  padding:0in 5.4pt 0in 5.4pt;height:283.0pt'>
  <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal'><!--[if gte vml 1]><v:rect id="_x0000_s1045" style='position:absolute;
   margin-left:89.25pt;margin-top:12.15pt;width:101.25pt;height:15pt;z-index:10;
   mso-position-horizontal-relative:text;mso-position-vertical-relative:text'/><![endif]--><![if !vml]><![endif]></p>
  <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal'>&nbsp;</p>
  <form action="" method="post" name="frmOutbreakRefNo" id="frmOutbreakRefNo">
  <p>
      <label>Outbreak Ref. No:
        <input type="text" name="txtOutbreakRefNo" id="txtOutbreakRefNo" accesskey="N" tabindex="1">
      </label>
    </p>
</form>
<p align="center" class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal'>
  <![if !vml]>
  <![endif]>
  <strong>OR</strong>
    <o:p>&nbsp;</o:p>
</p>
<p align="center" class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal'>__________________________________ </p>
  <form action="" method="post" name="frmCountry" id="frmCountry">
    <p>
      <label>Geo Layer 3
        <select name="lstCountry" size="1" id="lstCountry" accesskey="C" tabindex="3">
          <option selected>Select one</option>
		  <?php foreach ($geo3 as $layer) {
					echo "<option value='{$layer['name']}'>{$layer['name']}</option>";
				}
		  ?>
          </select>
        </label>
      </p>
    <p>
      <label>Year
        <input name="txtYear1" type="text" id="txtYear1" accesskey="Y" tabindex="4" size="4" maxlength="4">
        </label>
      <label>Month
        <input name="txtMonth1" type="text" id="txtMonth1" accesskey="M" tabindex="5" size="2" maxlength="2">
        </label>
      </p>
    <p>
      <label>Animal
        <select name="lstAnimal1" size="1" id="lstAnimal1" accesskey="A" tabindex="6">
          <option selected>Select one</option>
		  <?php foreach ($species as $animal) {
					echo "<option value='{$animal['name']}'>{$animal['name']}</option>";
				}
		  ?>
          </select>
        </label>
      </p>
    <p>
      <label>Disease Type
        <select name="lstDiseaseType1" size="1" id="lstDiseaseType1" accesskey="D" tabindex="7">
          <option selected>Select one</option>
		  <?php foreach ($diseases as $dis) {
					echo "<option value='{$dis['name']}'>{$dis['name']}</option>";
				}
		  ?>
        </select>
        </label>
      </p>
  </form>
  <p align="center" class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal'><strong>OR</strong>
    <o:p>&nbsp;</o:p>
    <span class="MsoNormal" style="margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal">__________________________________</span></p>
  <form action="" method="post" name="frmDistrict" id="frmDistrict">
    <p>
      <label>Geo Layer 2 
        <select name="lstDistrict" size="1" id="lstDistrict" accesskey="C" tabindex="8">
          <option selected>Select one</option>
		  <?php foreach ($geo2 as $layer) {
					echo "<option value='{$layer['name']}'>{$layer['name']}</option>";
				}
		  ?>
        </select>
      </label>
    </p>
    <p>
      <label>Year
        <input name="txtYear2" type="text" id="txtYear2" accesskey="Y" tabindex="9" size="4" maxlength="4">
      </label>
      <label>Month
        <input name="txtMonth2" type="text" id="txtMonth2" accesskey="M" tabindex="10" size="2" maxlength="2">
      </label>
    </p>
    <p>
      <label>Animal
        <select name="lstAnimal2" size="1" id="lstAnimal2" accesskey="A" tabindex="11">
          <option selected>Select one</option>
		  <?php foreach ($species as $animal) {
					echo "<option value='{$animal['name']}'>{$animal['name']}</option>";
				}
		  ?>
        </select>
      </label>
    </p>
    <p>
      <label>Disease Type
        <select name="lstDiseaseType2" size="1" id="lstDiseaseType2" accesskey="D" tabindex="12">
          <option selected>Select one</option>
		  <?php foreach ($diseases as $dis) {
					echo "<option value='{$dis['name']}'>{$dis['name']}</option>";
				}
		  ?>
        </select>
      </label>
    </p>
  </form></td>
  <td width=704 style='width:369.0pt;border-top:none;border-left:none;
  border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;mso-border-top-alt:
  solid black .5pt;mso-border-left-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  padding:0in 5.4pt 0in 5.4pt;height:283.0pt'>

<div id="map3d_container" style="border: 1px solid silver; height: 450px; width: 600px;">  <div id="map3d" style="height: 100%;"></div></div>

  <b style='mso-bidi-font-weight:normal'>
  
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <a href="javascript:" onclick="submitQuery();" id='btnSubmit' style="display:inline"><img src="<?="$imageFolder/createmap.jpg"?>" alt="CREATE MAP" title="CREATE MAP" /></a>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <a href="<?= $this->uri(array('action'=>'view_reports')); ?>" id='btnCancel' style="display:inline"><img src="<?="$imageFolder/cancel.jpg"?>" alt="CANCEL" title="CANCEL" /></a>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <a href="javascript:" id='btnBack' onclick="window.location.reload();" style="display:none"><img src="<?="$imageFolder/backviewreports.jpg"?>" alt="BACK TO VIEW REPORTS" title="BACK TO VIEW REPORTS" /></a>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <a href="<?= $this->uri(array('action'=>'gis_reports', 'report'=>2)); ?>" id='btnNew' style="display:none"><img src="<?="$imageFolder/newgismap.jpg"?>" alt="NEW GIS MAP" title="NEWGISMAP" /></a></b></p>
  </td>
 </tr>
</table>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

</div>
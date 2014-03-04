function toggleRetiredDate() {
    $('retireddate_Day_ID').disabled       = !$('retireddate_Day_ID').disabled;
    $('retireddate_Month_ID').disabled     = !$('retireddate_Month_ID').disabled;
    $('retireddate_Year_ID').disabled      = !$('retireddate_Year_ID').disabled;
    $('retireddate_ID_Link').style.display = ($('retireddate_ID_Link').style.display == 'none')? 'inline' : 'none';
}

function toggleAhisUser() {
    $('input_username').disabled = !$('input_username').disabled;
    $('input_password').disabled = !$('input_password').disabled;
    $('input_confirm').disabled =  !$('input_confirm').disabled;
    
    if (!$('input_ahisuser').checked) {
        if ($('input_adminuser').checked) {
            $('input_adminuser').checked = false;
        }
        if ($('input_superuser').checked) {
            $('input_superuser').checked = false;
        }
    }
}

function toggleAdminUser() {
    if ($('input_adminuser').checked) {
        if (!$('input_ahisuser').checked) {
            $('input_ahisuser').checked = true;
            toggleAhisUser();
        }
    } else {
        if ($('input_superuser').checked) {
            $('input_superuser').checked = false;
        }
    }
}

function toggleSuperUser() {
    if ($('input_superuser').checked) {
        if (!$('input_ahisuser').checked) {
            $('input_ahisuser').checked = true;
            toggleAhisUser();
        }
        if (!$('input_adminuser').checked) {
            $('input_adminuser').checked = true;
        }
    }
}

function resetDate(fieldName) {
    var today = new Date();
    
    var day = today.getDate();
    var monthIndex = today.getMonth();
    var month = monthIndex + 1;
    var year = today.getFullYear();

    $(fieldName + '_Day_ID').selectedIndex = day - 1;
    $(fieldName + '_Month_ID').selectedIndex = monthIndex;
    $(fieldName + '_Year_ID').value = year;
    day += '';
    month += '';
    if (day.length < 2) {
        day = '0' + day;
    }
    if (month.length < 2) {
        month = '0' + month;
    }
    
    $('input_' + fieldName).value = year + '-' + month + '-' + day;
}

function clearDiseaseLocality() {
    $('input_localityTypeId').selectedIndex = 0;
	$('input_latDirec').selectedIndex = 0;
	$('input_longDirec').selectedIndex = 0;
	$('input_farmingSystemId').selectedIndex = 0;
	jQuery('#input_localityName').val('');
	jQuery('#input_latitude').val('');
	jQuery('#input_longitude').val('');
}

function clearNatureOfDiagnosis() {
    $('input_diagnosisId').selectedIndex = 0; 
}

function clearControlMeasures() {
    $('input_controlId').selectedIndex = 0; 
	$('input_otherControlId').selectedIndex = 0; 
}

function clearDiseaseNumbers() {
    $('input_speciesId').selectedIndex = 0;
	$('input_ageId').selectedIndex = 0;
	$('input_sexId').selectedIndex = 0;
	jQuery('#input_risk').val('');
	jQuery('#input_cases').val('');
	jQuery('#input_deaths').val('');
	jQuery('#input_destroyed').val('');
	jQuery('#input_slaughtered').val('');
	jQuery('#input_cumulativeCases').val('');
	jQuery('#input_cumulativeDeaths').val('');
	jQuery('#input_cumulativeDestroyed').val('');
	jQuery('#input_cumulativeSlaughtered').val('');
	
}

function boxLimiter(box) {
    if (box.value.length > 30) {
		box.value = box.value.substr(0,30);
    }
}

function clear_viewReports() {
	var today = new Date();
    var monthIndex = today.getMonth();
    var month = monthIndex + 1;
    var year = today.getFullYear();
	
	$('input_year').value = year;
	$('input_month').selectedIndex = monthIndex;
}

function changePartitionType() {
	jQuery('#input_partitionLevelId >option').remove();
	jQuery('#input_partitionId >option').remove();
	jQuery('#input_partitionLevelId').attr('disabled', true);
	jQuery('#input_partitionId').attr('disabled', true);
	var categoryId = jQuery('#input_partitionTypeId').val();
	//load levels
	jQuery.getJSON("index.php?module=openaris&action=ajax_getpartitionlevels&categoryId="+categoryId,
				   function(data) {
						jQuery.each(data, function(key, value) {
							jQuery('#input_partitionLevelId').append(jQuery("<option></option>").attr("value",key).text(value));
						});
						if (data.length != 0) {
							jQuery('#input_partitionLevelId').removeAttr('disabled');
						}
						changeNames();
				   });
	
}

function changeNames() {
	jQuery('#input_partitionId >option').remove();
	jQuery('#input_partitionId').attr('disabled', true);
	var countryId = jQuery('#input_countryId').val();
	if (countryId != -1) {
		var levelId = jQuery('#input_partitionLevelId').val();
		jQuery.getJSON("index.php?module=openaris&action=ajax_getpartitionnames&levelId="+levelId+"&countryId="+countryId,
				   function(data) {
						jQuery.each(data, function(key, value) {
							jQuery('#input_partitionId').append(jQuery("<option></option>").attr("value",key).text(value));
						});
						if (data.length != 0) {
							jQuery('#input_partitionId').removeAttr('disabled');
						}
				   });
	}
	
}

function changeCountry() {
	changeNames();
	changeOutbreakCode();
}

function changeOutbreakCode() {
	var countryId 	= jQuery('#input_countryId').val();
	var diseaseId 	= jQuery('#input_diseaseId').val();
	var year 		= jQuery('#input_year').val();
	if (countryId != -1 && year != '') {
		jQuery.getJSON("index.php?module=openaris&action=ajax_getoutbreakcode&diseaseId="+diseaseId+"&countryId="+countryId+"&year="+year,
				   function(data) {
						jQuery('#input_outbreakCode').val(data.code);
					});
	} else {
		jQuery('#outbreakCode').val('');
	}
}

function getOfficerInfo(role) {
	var userId = jQuery('#input_'+role+'OfficerId').val();
	var fax;
	var phone;
	var email;
	if (userId == -1) {
		jQuery('#input_'+role+'OfficerFax').val('');
		jQuery('#input_'+role+'OfficerTel').val('');
		jQuery('#input_'+role+'OfficerEmail').val('');
	} else {
		jQuery.getJSON("index.php?module=openaris&action=ajax_getofficerinfo&userid="+userId,
					   function(data) {
							jQuery('#input_'+role+'OfficerFax').val(data.fax);
							jQuery('#input_'+role+'OfficerTel').val(data.phone);
							jQuery('#input_'+role+'OfficerEmail').val(data.email);
					   });
	}
}

function toggleOutbreak(value) {
	if (value == 0) {
		jQuery('#input_reportTypeId').attr('disabled', true);
		jQuery('#input_outbreakId').attr('disabled', true);
		jQuery('#input_diseaseId').attr('disabled', true);
		jQuery('#input_occurenceId').attr('disabled', true);
		jQuery('#input_infectionId').attr('disabled', true);
		disableDatePicker('observationDate');
		disableDatePicker('vetDate');
		disableDatePicker('investigationDate');
		disableDatePicker('sampleDate');
		disableDatePicker('diagnosisDate');
		disableDatePicker('interventionDate');
		jQuery('[name=enter]').removeClass('nextButton');
		jQuery('[name=enter]').addClass('saveButton');
		
	} else {
		jQuery('#input_reportTypeId').removeAttr('disabled');
		if (jQuery('#input_reportTypeId').val() == 1) {
			jQuery('#input_outbreakId').removeAttr('disabled');
		}
		jQuery('#input_diseaseId').removeAttr('disabled');
		jQuery('#input_occurenceId').removeAttr('disabled');
		jQuery('#input_infectionId').removeAttr('disabled');
		enableDatePicker('observationDate');
		enableDatePicker('vetDate');
		enableDatePicker('investigationDate');
		enableDatePicker('sampleDate');
		enableDatePicker('diagnosisDate');
		enableDatePicker('interventionDate');
		jQuery('[name=enter]').removeClass('saveButton');
		jQuery('[name=enter]').addClass('nextButton');
		
	}
}

function disableDatePicker(name) {
	jQuery('#'+name+'_Day_ID').attr('disabled', true);
    jQuery('#'+name+'_Month_ID').attr('disabled', true);
    jQuery('#'+name+'_Year_ID').attr('disabled', true);
    jQuery('#'+name+'_ID_Link').hide();
}

function enableDatePicker(name) {
	jQuery('#'+name+'_Day_ID').removeAttr('disabled');
    jQuery('#'+name+'_Month_ID').removeAttr('disabled');
    jQuery('#'+name+'_Year_ID').removeAttr('disabled');
    jQuery('#'+name+'_ID_Link').show();
}

function toggleReportType() {

	if (jQuery('#input_reportTypeId').val() == 1) {
		jQuery('#input_outbreakCode').hide();

		jQuery('#input_outbreakId').show();
				changeoutbreak();

		jQuery('#input_diseaseId').attr('disabled', true);						
		jQuery('#input_occurenceId').attr('disabled', true);
		jQuery('#input_infectionId').attr('disabled', true);
		
	} else {
		jQuery('#input_outbreakCode').show();
		jQuery('#input_outbreakId').hide();
		jQuery('#input_diseaseId').removeAttr('disabled');
		jQuery('#input_occurenceId').removeAttr('disabled');
		jQuery('#input_infectionId').removeAttr('disabled');
	}
}

function addNumbers(name) {
	var total = (isNaN(parseInt(jQuery('#input_cumulative'+name).val())))? 0 : parseInt(jQuery('#input_cumulative'+name).val());
	jQuery('#input_cumulative'+name).val(total+parseInt(jQuery('#input_'+name).val()));
}

function numberVal()
{
	alert('Insert numerics only.');	
}

function confirmation()
{
    alert("Please add at least one Farm");

}

function changeBreed() {
	jQuery('#input_breedId >option').remove();
	jQuery('#input_breedId').attr('disabled', true);
	var classification = jQuery('#input_classification').val();
	if (classification != -1) {
		var breedsId = jQuery('#input_classification').val();
				jQuery.getJSON("index.php?module=openaris&action=ajax_getbreed&classification="+breedsId,
				   function(data) {
						jQuery.each(data, function(key, value) {
							jQuery('#input_breedId').append(jQuery("<option></option>").attr("value",key).text(value));
						});
						if (data.length != 0) {
							jQuery('#input_breedId').removeAttr('disabled');
						}
				   });
	}
}

function changeOutbreak() {
	jQuery('#input_diseaseId').attr('disabled', true);
	jQuery('#input_diseaseId >option').remove();
	var outbreakCode = jQuery('#input_outbreakref').val();
	jQuery.getJSON("index.php?module=openaris&action=ajax_getdisease&outbreakcode="+outbreakCode,
		function(data) {
			jQuery('#input_diseaseId').append(jQuery("<option></option>").attr("value",data.id).text(data.disease_name));
			if (data.length != 0) {
				jQuery('#input_diseaseId').removeAttr('disabled');
				changeDisease();
			}
		});
}

function changeDisease() {
	//jQuery('#input_diseaseId >option').remove();
	//jQuery('#input_diseaseId').attr('disabled', true);
	jQuery('#input_speciesId').attr('disabled', true);
	jQuery('#input_speciesId >option').remove();
	var diseaseId = jQuery('#input_diseaseId').val();
	var outbreakCode = jQuery('#input_outbreakref').val();
	jQuery.getJSON("index.php?module=openaris&action=ajax_getspecies&diseaseId="+diseaseId+"&outbreakCode="+outbreakCode,
				   function(data) {
						jQuery.each(data, function(key, value) {
							jQuery('#input_speciesId').append(jQuery("<option></option>").attr("value",value.id).text(value.speciesname));
						});
						if (data.length != 0) {
							jQuery('#input_speciesId').removeAttr('disabled');
						}
				   });
}


function changeSpecies() {
	jQuery('#input_speciesId >option').remove();
	jQuery('#input_speciesId').attr('disabled', true);
	var outbreakref = jQuery('#input_outbreakref').val();
	if (outbreakref != -1) {
		var diseaseId = jQuery('#input_outbreakref').val();
//alert(diseaseId);
				   jQuery.getJSON("index.php?module=openaris&action=ajax_getspeciesnames&outbreakcode="+diseaseId,
				   function(data) {
						jQuery.each(data, function(key, value) {
							jQuery('#input_speciesId').append(jQuery("<option></option>").attr("value",key).text(value));
						});
						if (data.length != 0) {
							jQuery('#input_speciesId').removeAttr('disabled');
						}
				   });
	}
	}
	
	function changeValues(vname){

	var condprovac  = jQuery('#input_cond'+vname).val();
	var cumvac;
	condprovac = condprovac/1;
	if (condprovac >= 0 && condprovac == parseInt(condprovac)|| condprovac ==0) {
		var condprovac = jQuery('#input_cond'+vname).val();
//alert(diseaseId);
				   jQuery.getJSON("index.php?module=openaris&action=ajax_getvalues&condprovac="+condprovac+"&filter="+vname,
				   function(data) {
							jQuery('#input_cum'+vname).val(data.cumvac);
							
					   });
	}else{
alert("Please Enter positive whole number");
var v = "";
								jQuery('#input_cond'+vname).val(v);
	
	}
	
	
	}

	function changeoutbreak(){
	jQuery('#input_outbreakId >option').remove();
	jQuery('#input_outbreakId').attr('disabled', true);
	var countryId = jQuery('#input_countryId').val();
		if (countryId != -1) {
	jQuery.getJSON("index.php?module=openaris&action=ajax_getoutbreakcountry&countryId="+countryId,
				   function(data){ 
						jQuery.each(data, function(key, value) {
							jQuery('#input_outbreakId').append(jQuery("<option></option>").attr("value",key).text(value));
						});
						if (data.length != 0) {
							jQuery('#input_outbreakId').removeAttr('disabled');
						}
						});
	
	}
	}

function valdirection(dname){

	var direction  = jQuery('#input_'+dname+'itude').val();
	var country = jQuery('#input_countryId').val();

	if (direction != -1) {
	var direction  = jQuery('#input_'+dname+'itude').val();
		var country = jQuery('#input_countryId').val();

				   jQuery.getJSON("index.php?module=openaris&action=ajax_valdirection&direction="+direction+"&filter="+dname+"&countryId="+country,
				   function(data) {
				   jQuery('#input_'+dname+'itude').val(data.direct);
				   if(data.status == 1){
				   
				   alert("Please,Lattitude is out of range.Range is between "+data.nlatt+" and "+data.slatt);
				   var def = 'NULL';
				   jQuery('#input_'+dname+'itude').val(def);
				   }
				   if(data.status == 2){
				   
			      alert("Please,Longitude is out of range.Range is between "+data.wlong+" and "+data.elong);
			      				  // var def1 = 'NULL';
			     jQuery('#input_'+dname+'itude').val(0);
				   }
					   });
	}
	
	
	}
	

	
function checkBreedSpecies()
{

var nobreed= parseInt(jQuery('#input_breedNumber').val());
var nospec= parseInt(jQuery('#input_totalNumSpecies').val());
if(nobreed> nospec){ 
	alert('Breed Number should be less than Total Number (Species)');
	jQuery('#input_breedNumber').val('');
	
	}	
}	

function checkProdNoSpecies()
{

var noprod= parseInt(jQuery('#input_productionno').val());
var nospec= parseInt(jQuery('#input_totalNumSpecies').val());
if(noprod> nospec){ 
	alert('Production Number should be less than Total Number (Species)');
	jQuery('#input_productionno').val('');
	
	}	
}

function checkAnimCatSpecies()
{

var nocat= parseInt(jQuery('#input_catNumber').val());
var nospec= parseInt(jQuery('#input_totalNumSpecies').val());
if(nocat> nospec){ 
	alert('Category Number should be less than Total Number (Species)');
	jQuery('#input_catNumber').val('');
	
	}	
}
	
function getTLU(){
//alert('nospecies');
var nospecies= jQuery('#input_totalNumSpecies').val();
if (nospecies !=-1){
var nospecies= jQuery('#input_totalNumSpecies').val();
if(nospecies> 40000){ 
	alert('Total Number should be less than 400,000');
	jQuery('#input_totalNumSpecies').val('');
	}
	if(nospecies< 0){ 
	alert('Total Number cannot be less than 0');
	jQuery('#input_totalNumSpecies').val('');
	}
jQuery.getJSON("index.php?module=openaris&action=ajax_gettlu&speciesno="+nospecies,
				   function(data) {
							jQuery('#input_tropicalLivestock').val(data.prod);
							
					   });
	}
}

function limitcomment(){

var comment = jQuery('#input_comment').val();
var clength = comment.length;

if(comment.length > 256){
jQuery('#input_comment').val(comment.substr(0,256));

}


}

function ignorenegative(value){

var caseinput = jQuery('#input_'+value+'vac').val();


if(caseinput == '-'){
alert("Please enter Positive whole number");
jQuery('#input_'+value+'vac').val(0);

}else{
 //var all = round(caseinput);
//if(isset(isInteger(caseinput))){
var ncaseinput = caseinput/1; 
if(ncaseinput == parseInt(ncaseinput)|| ncaseinput ==0){

}else{
alert("please enter positive whole numbers");
jQuery('#input_'+value+'vac').val(0);

}
//}
}


}
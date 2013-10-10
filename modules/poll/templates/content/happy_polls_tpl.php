<?php
/**
* Template to display the main page for the new forum
* @access public
*/
//Language items
$this->objLanguage = $this->getObject('language', 'language');
$lbEdit = $this->objLanguage->languageText('word_edit');
$lbDelete = $this->objLanguage->languageText('word_delete');
$lbSave = $this->objLanguage->languageText('word_save');
$lbRetype = $this->objLanguage->languageText('mod_poll_retype','poll');
$lbProcessing = $this->objLanguage->languageText('mod_poll_processing','poll');
$lbErrorOnSave = $this->objLanguage->languageText('mod_poll_erroronsave','poll');
$lbTypeOption = $this->objLanguage->languageText('mod_poll_typeoption','poll');
$lbEditOption = $this->objLanguage->languageText('mod_poll_editoption','poll');
$lbErrorMessage = $this->objLanguage->languageText('mod_poll_errormessage','poll');
$lbViewsSavedMsg = $this->objLanguage->languageText('mod_poll_viewssavedmsg','poll');
$lbSureDeleteQn = $this->objLanguage->languageText('mod_poll_deletequestionconfirm','poll');
$lbSureDelete = $this->objLanguage->languageText('mod_poll_deleteconfirm','poll');
$lbCompleteMsg = $this->objLanguage->languageText('mod_poll_viewssavedmsg','poll');
//The load icon
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');

//AJAX to submit form
    
    $this->appendArrayVar('headerParams', '
<script type="text/javascript">
//handle the radio buttons
var id, code, option, question, inputVisible, myanswer, qnId, ansId, theAnsId, theQnId, myAnsId, myQnId;
function onClickRadio()
{
      jQuery("#question_options").html(" ");		
       id = jQuery("#input_id").attr(\'value\');
	if (id==null)
	{
		id="0";
	}
      //document.getElementById("onfinish").click();
      code = jQuery("#input_contextcode").attr(\'value\');
      question = jQuery("#input_question").attr(\'value\');
      inputVisible = jQuery("input[name=visible]:checked").val();
      option = jQuery("input[@name=\'qntype\']:checked").val();              
      if (option=="open"){
	if(question == null){
                jQuery("#question_options").removeClass("success");	
                jQuery("#question_options").addClass("error");
	      jQuery("#question_options").html("'.$lbRetype.'");		
	      //make the 1st one checked
	      jQuery("input[@name=\'qntype\']:nth(1)").attr("checked","checked");
	}else{
                jQuery("#question_options").addClass("success");
	      jQuery("#question_options").html("<span id=\"contextpollprocess\">'.addslashes($objIcon->show()).' Processing ...</span>");
                // DO Ajax
                jQuery.ajax({
                type: "GET", 
                url: "index.php", 
                data: "module=poll&action=saveqntypeopen&question="+question+"&type="+option+"&visible="+inputVisible+"&context="+code+"&id="+id,  
                success: function(msg){               
                	// IF delete was successful                                          
                        if (msg == "notok") {                             
	                     jQuery("#question_options").addClass("error");
                             jQuery("#question_options").html("'.$lbErrorOnSave.'");
                        }else{
			     qnId = msg;
			     ansId = 0;				
                             jQuery("#question_options").html(null);	
	                     jQuery("#question_options").addClass("success");
                             jQuery("#question_options").html("'.$lbTypeOption.': <input name=\'opt_"+qnId+"\' id=\'ans_"+qnId+"\' type=\'text\' value=\' \' /> <a href=\'#\' onclick=\'saveAnswer(qnId,ansId)\'>Save</a>");
			}
		}
                });

	}
      }else{
                jQuery("#question_options").addClass("success");
                jQuery("#question_options").html("");	
      }

}
function saveAnswer(qnId, id){
	if (id==null)
	{
		id=0;
	}
      jQuery("#contextcodemessage").html("<span id=\"contextpollprocess\">'.addslashes($objIcon->show()).' Processing ...</span>");
	//Get values
	option = jQuery("input[@name=\'qntype\']:checked").val();              
	myanswer = jQuery("#ans_"+qnId).attr(\'value\');
        // DO Ajax
        jQuery.ajax({
        type: "GET", 
        url: "index.php", 
        data: "module=poll&action=saveanswertypeopen&answer="+myanswer+"&type="+option+"&qnId="+qnId+"&id="+id,  
        success: function(msg){               
        	// IF delete was successful                                          
                if (msg == "notok") {                       
		     jQuery("#contextcodemessage").html("");      
                     jQuery("#question_options").addClass("error");
                        jQuery("#question_options").html("'.$lbErrorMessage.'");
                }else{
		     jQuery("#contextcodemessage").html("");
		     jQuery("#contextpollprocess").html("");
                        jQuery("#theoptions").html(msg);
                     jQuery("#question_options").html("");
		     jQuery("#question_options").addClass("success");
		     theAnsId = "0";
		     theQnId = qnId;
		     jQuery("#question_options").html("'.$lbTypeOption.': <input name=\'opt_"+qnId+"\' id=\'ans_"+qnId+"\' type=\'text\'  value=\' \' /> <a href=\'#\' onclick=\'saveAnswer(theQnId,theAnsId)\'>Save</a>");
		}
	}
        });	
}
function editAnswer(id, qnId, answer){
	myAnsId = id;		
	myQnId = qnId;
	jQuery("#question_options").html(" ");
	jQuery("#question_options").addClass("success");
      	jQuery("#question_options").html("'.$lbEditOption.': <input name=\'opt_"+myQnId+"\' id=\'ans_"+myQnId+"\' type=\'text\' value=\'"+answer+"\' /> <a href=\'#\' onclick=\'saveAnswer(myQnId,myAnsId)\'>'.$lbSave.'</a>");
}
function onLoadEvent()
{
      //document.getElementById("onfinish").click();
      code = jQuery("#input_visible").attr(\'value\');
}
	function onAdd()
{
        // DO Ajax
        jQuery.ajax({
        type: "GET", 
        url: "index.php", 
        data: "module=poll&action=addcontextpoll", 
        success: function(msg){                                            
                        jQuery("#pollcontent").html(msg);
                }
        });
}       

function onEdit(id)
{
      code = jQuery("#input_"+id).attr(\'value\');
        // DO Ajax
        jQuery.ajax({
        type: "GET", 
        url: "index.php", 
        data: "module=poll&action=showeditcontext&id="+code, 
        success: function(msg){                                            
                        jQuery("#pollcontent").html(msg);
                }
        });
}       
function onDelete(id)
{
      code = jQuery("#input_"+id).attr(\'value\');
	  if (confirm("'.$lbSureDeleteQn.'")) {
                // DO Ajax
                jQuery.ajax({
                type: "GET", 
                url: "index.php", 
                data: "module=poll&action=deletecontextqn&id="+code, 
                success: function(msg){  
                	// IF delete was successful                                          
                        if (msg == "ok") {
		                jQuery.ajax({
		                type: "GET", 
		                url: "index.php", 
		                data: "module=poll&action=happyeval", 
		                success: function(msg){
		                             jQuery("#pollcontent").html(msg);
					}
		                });
                        }
                        }
                }); 
	  }
}       
function onDeleteAns(id)
{
      //code = jQuery("#input_"+id).attr(\'value\');
	  if (confirm("'.$lbSureDelete.'")) {
                // DO Ajax
                jQuery.ajax({
                type: "GET", 
                url: "index.php", 
                data: "module=poll&action=deleteqntypeopenans&id="+id, 
                success: function(msg){  
                	// IF delete was successful
                        if (msg == "ok") {
		                jQuery.ajax({
		                type: "GET", 
		                url: "index.php", 
		                data: "module=poll&action=happyeval", 
		                success: function(msg){
		                             jQuery("#pollcontent").html(msg);
					}
		                });
                        }
                        }
                }); 
	  }
}       

function clickButton()
  {
      //get the input values
	var thisQnid=0;
      code = jQuery("#input_contextcode").attr(\'value\');
      thisQnid = jQuery("#input_id").attr(\'value\');
      question = jQuery("#input_question").attr(\'value\');
      inputType = jQuery("input[name=qntype]:checked").val();
      inputVisible = jQuery("input[name=visible]:checked").val();
        // DO Ajax
        jQuery.ajax({
        type: "GET", 
        url: "index.php", 
        data: "module=poll&action=savecontextquestion&question="+question+"&type="+inputType+"&visible="+inputVisible+"&context="+code+"&qnId="+thisQnid, 
        success: function(msg){                                            
                // IF code exists
                if (msg == "ok") {
	                jQuery.ajax({
	                type: "GET", 
	                url: "index.php", 
	                data: "module=poll&action=happyeval", 
	                success: function(msg){
	                             jQuery("#pollcontent").html(msg);
	                        }
	                });
                // Else
                } else {
                        jQuery("#contextcodemessage").html("'.$lbErrorOnSave.'");
                        jQuery("#contextcodemessage").addClass("error");
                        jQuery("#input_question").addClass("inputerror");
                        jQuery("#contextcodemessage").removeClass("success");
                        jQuery("#submitbutton").attr("disabled","disabled");
                }                                
                }
        });
        }
//Function to save the learner responses
function saveResponse()
{
	//get the row count
	rowno = jQuery("#input_rowcount").attr(\'value\');
        jQuery("#contextcodemessage").addClass("success");
        jQuery("#contextcodemessage").html("<span id=\"contextpollprocess\">'.addslashes($objIcon->show()).' Processing ...</span>");
	//our counter
	i=1;
	while(i<rowno)
	{
		//get the qn Id
		qnId = jQuery("#input_"+i).attr(\'value\');
		//get the qn type bool yes or open
		qnType = jQuery("#input_qntype_"+i).attr(\'value\');
		//get the selected answer
		chosenAnswer = jQuery("input[@name=\'"+qnId+"\']:checked").val();
		if(chosenAnswer!==null){
		        // DO Ajax to save the response
		        jQuery.ajax({
		        type: "GET", 
		        url: "index.php", 
		        data: "module=poll&action=saveresponse&questionType="+qnType+"&questionId="+qnId+"&answer="+chosenAnswer, 
		        success: function(msg){                                            
		                        jQuery("#contextcodemessage").html(" ");
		                }
		        });
		}
		//increment our counter
		i++;
	}
	jQuery("#contextcodemessage").html(" ");
        jQuery("#pollcontent").html("'.$lbCompleteMsg.'");
}
//View Poll Results
function viewResults()
{
        // DO Ajax
        jQuery.ajax({
        type: "GET", 
        url: "index.php", 
        data: "module=poll&action=analysecontextpolls", 
        success: function(msg){                                            
                        jQuery("#pollcontent").html(msg);
                }
        });
}       
//Get answer primary id
function getAnswerId(qnId, chosenAnswer)
{
        // DO Ajax to get answerId then save
        jQuery.ajax({
        type: "GET", 
        url: "index.php", 
        data: "module=poll&action=getanswerid&questionId="+qnId+"&answer="+chosenAnswer, 
        success: function(msg){                                            
                selectedAnsId = msg;
        }
        });
	return selectedAnsId;
}       
//View Poll Results
function viewPolls()
{
        // DO Ajax
        jQuery.ajax({
        type: "GET", 
        url: "index.php", 
        data: "module=poll&action=happyeval", 
        success: function(msg){                                            
                        jQuery("#pollcontent").html(msg);
                }
        });
}       
    </script>');

//set the template  
$this->setLayoutTemplate('singlepoll_layout_tpl.php');
//Load the form
if($display!==null){
	echo '<div id="pollcontent">'.$display.'</div>';
}else{
	echo '<script>
 jQuery.facebox(function() {
    jQuery.facebox.close();
})
</script>';
}

?>
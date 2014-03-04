	<style type="text/css">
	span#toolbar {
		padding: 10px 4px;
	}
	</style>
<div id="test">
    <?php
    /*! \file test1.php
 * \brief The template file exists so developers can test small pieces of code.
 * This template file called by the action test1. This template file is not
 * being used and consists or a lot of dead expermintal code. If you want to experiment
 * with some extra stuff, then this is place to do it. If your code works, then push
 * it in to the actual module.
 */
    $jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());

  $objFormList=$this->getObject('view_form_list','formbuilder');
    echo $objFormList->showPaginationMenu(0);
    ?>
</div>

<div id="paginationPageFormer">
    <?php 
  $objFormList=$this->getObject('view_form_list','formbuilder');
  //  echo $objFormList->showPaginationIndicator(0);
    ?>
</div>
<div id="paginationPageLatter">

<?php


echo $objFormList->showPaginationIndicator(0);
echo $objFormList->show(0);

?>
</div>

<div id="getNumberOfPaginationRequests">
    <?php
echo    $objFormList->getNumberofPaginationRequests();

    ?>

</div>
<div id="dialog-paginationIndicator" title="Pagination Menu">
	<p><span class="ui-icon ui-icon-transferthick-e-w" style="float:left; margin:0 7px 20px 0;"></span>
            Select how many forms to be viewed in one page.</p>
        <?php
$this->loadClass('radio','htmlelements');
        $paginationBatchRadio = new radio('paginationBatchRadio');
        $paginationBatchRadio->addOption('5','5');
        $paginationBatchRadio->addOption('10','10');
        $paginationBatchRadio->addOption('15','15');
                $paginationBatchRadio->addOption('20','20');
                                $paginationBatchRadio->addOption('25','25');
        $paginationBatchRadio->setSelected('5');
        $paginationBatchRadio->setBreakSpace("<br>");
        echo $paginationBatchRadio->show().'<br>';
?>
</div>

<script type="text/javascript">

        function intObject() {
        this.i;
        return this;
    }

    function setUpAccordion()
    {
//                jQuery("#accodrdion").accordion({
//			autoHeight: true,
//			navigation: true
//		});

if (jQuery("#paginationPageFormer").children("#accordion").length > 0)
              {
                jQuery("#paginationPageFormer").children("#accordion").accordion({
			autoHeight: true,
			navigation: true
		});
              }
              else if (jQuery("#paginationPageLatter").children("#accordion").length > 0)
              {
                jQuery("#paginationPageLatter").children("#accordion").accordion({
			autoHeight: true,
			navigation: true
		});
              }
    }

    function setUpAccordionButtons(restrictiveUserButtons)
    {

                      jQuery(".formOptionsButton, .constructFormButton, .previewFormButton, .viewPublishingDataButton, .editPublishingDataButton , .viewSubmitResultsButton").button();
//              jQuery(".viewSubmitResultsButton").click( function (){
//                     jQuery("#toolbar").append('<span id="testdfjs" class="deleteSpan">'+this.href+'dfgdfgdf</span>');
////window.location.replace(this.href);
//      });
//                var accessRestrictionArray= new Array();
//                jQuery(".userAccessRestriction").each(function (index, domEle) {
//        // domEle == this
//       var a = jQuery(domEle).val();
//       accessRestrictionArray[index]= a;
//       if (a ==1)
//       {
//           jQuery(':input[name=viewSubmitResultsButton]').button( "disable" );
//       }
            {
 //jQuery("#toolbar").append('<span id="testdfjs" class="deleteSpan">'+a+index+'</span>');
        }
        //jQuery("#tempdivcontainer").append(domIDs+"<br>");
              // jQuery("#tempdivcontainer").show();
//        $(domEle).css("backgroundColor", "yellow");
//        if ($(this).is("#stop")) {
//          $("span").text("Stopped at div index #" + index);
//          return false;
   //     }
  //    });
//
//                   jQuery(restrictiveUserButtons).each(function (index, domEle) {
//                   var a= accessRestrictionArray[index];
//                    if (a==3)
//                        {
//                           jQuery(domEle).button( "disable" );
//                        }
//                    });
    }

    function setUpPaginationButtons(paginationNumber, numberOfPaginationRequests)
    {
	if (jQuery("#paginationMenu").children('#paginationIndicator').length > 0)
            {
                jQuery("#paginationMenu").children('#paginationIndicator').remove();
            }
           if (jQuery('#paginationPageFormer').children().length <= 0)
               {
                   jQuery(".previousButton").after( jQuery('#paginationPageLatter>button') );
               }
      else
          {
        jQuery(".previousButton").after( jQuery('#paginationPageFormer>button') );
          }
	
                jQuery(".previousButton").button({

            icons: {
                primary: 'ui-icon-seek-prev'
            },
            text: true
        }).next().button({
            icons: {
               // primary: 'ui-icon-seek-next',
                //secondary: 'ui-icon-seek-next'
            },
            text: true
        }).next().button({
            icons: {
               // primary: 'ui-icon-seek-next',
                secondary: 'ui-icon-seek-next'
            },
            text: true
        });
        if (paginationNumber.i <= 0)
    {
        jQuery(".previousButton").button('disable');
    }
    else
        {
                jQuery(".previousButton").button('enable');
        }
if (paginationNumber.i >= numberOfPaginationRequests-1)
    {
        jQuery(".nextButton").button('disable');
    }
    else
        {
                jQuery(".nextButton").button('enable');
        }
setUpPaginationIndicator();
}


function callAjaxQueryForPaginatedResults()
{

}



function setUpPaginationIndicator()
{
      jQuery("#paginationIndicator").unbind('click').bind('click',function () {
jQuery("#dialog-paginationIndicator").dialog({
			resizable: false,
			width:340,
			modal: true,
			buttons: {
				'Set': function() {
					jQuery(this).dialog('close');
				},
				Cancel: function() {

jQuery("input:radio[name=paginationBatchRadio]:eq(0)").attr('checked', "checked");
   //      jQuery('input:radio[name=paginationBatchRadio]:checked').val("5");

                                        jQuery(this).dialog('close');
				}
			}
		});
  });
}
            jQuery(document).ready(function() {
			//	jQuery("#formOptionButton").button();

                 //        var I;
        var myIntObject = new intObject();
setUpPaginationIndicator();
               // i = 1;
        myIntObject.i = 0;
               var formOptionsButton = jQuery('.formOptionsButton');
        var editPublishingDataButton = jQuery('.editPublishingDataButton');
        var viewSubmitResultsButton = jQuery('.viewSubmitResultsButton');
        

        var restrictiveUserButtons = jQuery([]).add(formOptionsButton).add(editPublishingDataButton).add(viewSubmitResultsButton);
        var numberOfPaginationRequests = jQuery("#getNumberOfPaginationRequests").html();




  jQuery(".nextButton").unbind('click').bind('click',function () {
    myIntObject.i++;
                if (jQuery('#paginationPageFormer').children().length <= 0)
                    {
  var dataToPost = {"paginationRequestNumber":myIntObject.i};
                        var myurlToProduceMSDropdown="<?php echo $_SERVER[PHP_SELF]; ?>?module=formbuilder&action=listAllFormsPaginated";
                jQuery('#paginationPageFormer').load(myurlToProduceMSDropdown , dataToPost ,function postSuccessFunction(html) {
jQuery('#paginationPageFormer').hide();
                    jQuery('#paginationPageFormer').html(html);
               jQuery('#paginationPageLatter').hide("slide", { direction: "left" }, 1000);
                                jQuery('#paginationPageLatter').children().remove();
 jQuery('#paginationPageFormer').show("slide", { direction: "right" }, 1000);
setUpPaginationButtons(myIntObject, numberOfPaginationRequests);
setUpAccordionButtons(restrictiveUserButtons);
setUpAccordion();
  });
}
else
                    {
  var dataToPost = {"paginationRequestNumber":myIntObject.i};
                        var myurlToProduceMSDropdown="<?php echo $_SERVER[PHP_SELF]; ?>?module=formbuilder&action=listAllFormsPaginated";
                jQuery('#paginationPageLatter').load(myurlToProduceMSDropdown , dataToPost ,function postSuccessFunction(html) {
jQuery('#paginationPageLatter').hide();
                    jQuery('#paginationPageLatter').html(html);
                jQuery('#paginationPageFormer').hide("slide", { direction: "left" }, 1000);
                jQuery('#paginationPageFormer').children().remove();
   jQuery('#paginationPageLatter').show("slide", { direction: "right" }, 1000);
   setUpPaginationButtons(myIntObject, numberOfPaginationRequests);
setUpAccordionButtons(restrictiveUserButtons);
setUpAccordion();
  });
}
  });

  jQuery(".previousButton").unbind('click').bind('click',function () {
    myIntObject.i--;
                if (jQuery('#paginationPageFormer').children().length <= 0)
                    {
  var dataToPost = {"paginationRequestNumber":myIntObject.i};
                        var myurlToProduceMSDropdown="<?php echo $_SERVER[PHP_SELF]; ?>?module=formbuilder&action=listAllFormsPaginated";
                jQuery('#paginationPageFormer').load(myurlToProduceMSDropdown , dataToPost ,function postSuccessFunction(html) {
jQuery('#paginationPageFormer').hide();
                    jQuery('#paginationPageFormer').html(html);
               jQuery('#paginationPageLatter').hide("slide", { direction: "left" }, 1000);
                                jQuery('#paginationPageLatter').children().remove();
 jQuery('#paginationPageFormer').show("slide", { direction: "right" }, 1000);
setUpPaginationButtons(myIntObject, numberOfPaginationRequests);
setUpAccordionButtons(restrictiveUserButtons);
setUpAccordion();
  });
}
else
                    {
  var dataToPost = {"paginationRequestNumber":myIntObject.i};
                        var myurlToProduceMSDropdown="<?php echo $_SERVER[PHP_SELF]; ?>?module=formbuilder&action=listAllFormsPaginated";
                jQuery('#paginationPageLatter').load(myurlToProduceMSDropdown , dataToPost ,function postSuccessFunction(html) {
jQuery('#paginationPageLatter').hide();
                    jQuery('#paginationPageLatter').html(html);
                jQuery('#paginationPageFormer').hide("slide", { direction: "left" }, 1000);
                jQuery('#paginationPageFormer').children().remove();
   jQuery('#paginationPageLatter').show("slide", { direction: "right" }, 1000);
   setUpPaginationButtons(myIntObject, numberOfPaginationRequests);
setUpAccordionButtons(restrictiveUserButtons);
setUpAccordion();
  });
}
  });

setUpPaginationButtons(myIntObject);

setUpAccordionButtons(restrictiveUserButtons);
setUpAccordion();
	});



</script>
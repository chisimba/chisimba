<?php
/*! \file view_submitted_results.php
 * \brief The template file is called by the action viewSubmittedResults in the controller.php.
 * \todo Comment this file for doxygen. This is a very big file to comment with a lot
 * of intricate logic. The developer Salman Noor will comment it soon.
 */
$jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());

$excanvasLibrary = '<script language="JavaScript" src="' . $this->getResourceUri('js/jqplot/excanvas', 'formbuilder') . '" type="text/javascript"></script>';
$jqplotLibrary = '<script language="JavaScript" src="' . $this->getResourceUri('js/jqplot/jquery.jqplot.js', 'formbuilder') . '" type="text/javascript"></script>';
$jqplotCSS = '<link rel="stylesheet" type="text/css" href="' . $this->getResourceUri('js/jqplot/jquery.jqplot.css', 'formbuilder') . '"';
$jqplotBarGraphLibrary = '<script language="JavaScript" src="' . $this->getResourceUri('js/jqplot/jqplot.barRenderer.js', 'formbuilder') . '" type="text/javascript"></script>';
$jqplotAxisLibrary = '<script language="JavaScript" src="' . $this->getResourceUri('js/jqplot/jqplot.categoryAxisRenderer.min.js', 'formbuilder') . '" type="text/javascript"></script>';
$jqplotPieGraphLibrary = '<script language="JavaScript" src="' . $this->getResourceUri('js/jqplot/jqplot.pieRenderer.js', 'formbuilder') . '" type="text/javascript"></script>';
$jqplotPntLabelsLibrary = '<script language="JavaScript" src="' . $this->getResourceUri('js/jqplot/jqplot.pointLabels.js', 'formbuilder') . '" type="text/javascript"></script>';

//[If browser is IE]; this library needs to be included to jqplot to work
$this->appendArrayVar('headerParams', $excanvasLibrary);
///[End IF]
$this->appendArrayVar('headerParams', $jqplotLibrary);
$this->appendArrayVar('headerParams', $jqplotCSS);
$this->appendArrayVar('headerParams', $jqplotBarGraphLibrary);
$this->appendArrayVar('headerParams', $jqplotAxisLibrary);
$this->appendArrayVar('headerParams', $jqplotPieGraphLibrary);
$this->appendArrayVar('headerParams', $jqplotPntLabelsLibrary);
?>
<style type="text/css">
    div.panes div.holder
    {
        padding: 15px 10px;
        border: 1px solid #999;
        border-top: 0;
        height: 100px;
        font-size: 14px;
        background-color: #FFFFFF;
    }
</style>
<div id="testDiv"></div>
<div>
<?php
$objSideMenu = $this->getObject('side_menu_handler', 'formbuilder');
echo $objSideMenu->showSlideMenu() . "<br>";
?>
</div><br>
<span id="getFormNumber">
<?php
/// This span will allow this parameter to be
///passed into the jQuery code.
$formNumber = $this->getParam('formNumber', NULL);
///This variable is used in the link with the action
///"buildCurrentForm". The variable needs to be
///trimmed to remove any white space before or after the variable.
echo $trimmedformNumber = trim("$formNumber");
?>
</span>

<style type="text/css" >
    div#message_box {
        position: absolute;
        top: 95%;
        left: 10%;
        z-index: 9;
        /*	   background:#ffc;*/
        padding:5px;
        border:1px solid #CCCCCC;
        text-align:center;
        font-weight:bold;

    }

</style>

<div id="message_box">
<!--    <img alt=""  id="pic_message" style="float:right;cursor:pointer"  src="packages/formbuilder/resources/images/down-arrow-animated.gif" />-->
    Scroll Down To Load More Results
</div>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">List All Submitted Results</a></li>
        <li><a href="#tabs-2">List Final Submitted Results</a></li>
        <li><a href="#tabs-3">View Multiple Submitted Results</a></li>
        <li><a href="#tabs-4">View Results Graphically</a></li>
    </ul>
    <div id="tabs-1">


        <div id="viewAllResults" STYLE="width:550px;float:left; clear:none;">
<?php
$objSubmitResultsHandler = $this->getObject('form_submit_results_handler', 'formbuilder');
$objSubmitResultsHandler->setFormNumber($trimmedformNumber);
$objSubmitResultsHandler->setNumberOfEntriesInPaginationBatch(15);
echo $objSubmitResultsHandler->buildCSVFileDownloadLink();
echo $objSubmitResultsHandler->getAllFormResults("allResults");
?>
        </div>
        <div id="viewParticularSubmitResult" STYLE="width:550px;float:right; clear:none;"> </div>
        <p STYLE="clear:both;"> </p>
    </div>
    <div id="tabs-2">
        <div id="viewLatestResults" STYLE="width:550px;float:left; clear:none;">
<?php
echo $objSubmitResultsHandler->getAllFormResults("latestResults");
?>
        </div>
        <div id="viewLatestParticularSubmitResult" STYLE="width:550px;float:right; clear:none;"> </div>
        <p STYLE="clear:both;"> </p>
    </div>
    <div id="tabs-3">
        <p>Page under Construction. Coming soon bar graphs and stats on multiple submissions.</p>

    </div>
    <div id="tabs-4">
        <p>Page under construction. Coming soon, bar charts, pie and line graphs on the form elements.</p>
    </div>
</div>
<div id="getNumberOfEntriesInPaginationBatch">
<?php
echo $objSubmitResultsHandler->getNumberOfEntriesInPaginationBatch();
?>
</div>




<script type="text/javascript">

    function setUpMainMenuIcons()
    {
        jQuery('.homeButton').button({
            text: true,
            icons: {
                primary: 'ui-icon-home'
            }
        }).next().button({
            icons: {
                primary: 'ui-icon-script'

            },
            text: true
        }).next().button({
            icons: {
                primary: 'ui-icon-document'

            },
            text: true
        }).next().button({
            icons: {
                // primary: 'ui-icon-seek-next',
                primary: 'ui-icon-help'
            },
            text: true
        });
    }
    function getMorePaginatedSubmittedResults(page,numberOfEntriesInPaginationBatch,latestOrAllResults,formNumber)
    {
        var myUrlToLoadMorePaginatedResults = "<?php echo html_entity_decode($this->uri(array('action' => 'getMorePaginatedSubmitResults'), 'formbuilder')); ?>";
        var DataToPost = {
            "paginationRequestNumber": page.i,
            "numberOfEntriesInPaginationBatch" : numberOfEntriesInPaginationBatch,
            "latestOrAllResults" :latestOrAllResults,
            "formNumber" : formNumber
        };
        jQuery('#message_box').html("<img alt=''  id='pic_message' style='cursor:pointer'  src='packages/formbuilder/resources/images/loading_animation.gif' width='70px' height='70px' />");

        jQuery.get(myUrlToLoadMorePaginatedResults, DataToPost ,function postSuccessFunction(response) {
            jQuery("#testDiv").html(response);
            var response=   jQuery("#testDiv").html();
            if (response == 0)
            {
                jQuery("#test").append(response);
                setTimeout(function() {
                    jQuery('#message_box').animate({
                        opacity: 0.15
                        //    top: '-=50'

                    }, 2000, function() {
                        // Animation complete.
                    });


                    jQuery('#message_box').html( "No More Results to Load");
                    setTimeout(function() {
                        jQuery('#message_box').remove();
                    },1500);
                }, 1500);
                return;
            }
            else if (latestOrAllResults == "allResults");
            {
                jQuery('#viewAllResults').append(response);

            }
            if (latestOrAllResults == "latestResults")
            {
                jQuery('#viewLatestResults').append(response);
            }
            setTimeout(function() {
                jQuery('#message_box').html( "Scroll Down To Load More Results");
            }, 1500);

            setUpAjaxQueryToViewParticularResult();
            setUpAjaxQueryToViewLatestResult();
            setUpajaxQueryToDownloadCSVSubmitResultsFile();
        });

        page.i++;
    }

    function intObject() {
        this.i;
        return this;
    }

    function setUpAjaxQueryToViewParticularResult()
    {
        jQuery('a[title=viewParticularResult]').unbind('click').bind('click',function(event){
            event.preventDefault();
            jQuery(this).parent().parent().addClass('ui-priority-primary');
            jQuery.get(this.href,{},function(response){
                jQuery('#viewParticularSubmitResult').html(response);
                jQuery('#viewParticularSubmitResult').show("slide", {}, 1000);
            });
        });
    }

    function setUpAjaxQueryToViewLatestResult()
    {
        jQuery('a[title=viewLatestParticularResult]').unbind('click').bind('click',function(event){
            event.preventDefault();
            jQuery(this).parent().parent().addClass('ui-priority-primary');

            jQuery.get(this.href,{},function(response){
                jQuery('#viewLatestParticularSubmitResult').html(response);
                jQuery('#viewLatestParticularSubmitResult').show("slide", {}, 1000);
            });
        });
    }

    function setUpajaxQueryToDownloadCSVSubmitResultsFile()
    {
        jQuery('.downloadCSVFileLink').button({
            icons: {
                primary: 'ui-icon-circle-arrow-s',
                secondary: 'ui-icon-circle-arrow-s'
            },
            text: true
        });
        jQuery('.downloadCSVFileLink').unbind('click').bind('click',function(event){
            //  event.preventDefault();
            jQuery(this).hide('slow').show('slow');
            var formNumber = jQuery("#getFormNumber").html();
            var dataToPost = {"formNumber":formNumber};
            var myurlToConstructCSVFile ="<?php echo html_entity_decode($this->uri(array('action' => 'downloadCSVSubmitResultsFile'), 'formbuilder')); ?>";
            jQuery("#testDiv").load( myurlToConstructCSVFile , dataToPost ,function postSuccessFunction(html) {
                //jQuery("#testDiv").show();
                var myurlToDownloadCSVFile ="<?php echo html_entity_decode($this->getResourceUri('textfiles/submit_results.csv', 'formbuilder')) ?>";
                window.location.href = myurlToDownloadCSVFile;
            });

        });
    }
    jQuery(document).ready(function() {

        jQuery("#getFormNumber").hide();
        jQuery("#testDiv").hide();
        jQuery("#getNumberOfEntriesInPaginationBatch").hide();
        var formNumber = jQuery("#getFormNumber").html();
        var   numberOfEntriesInPaginationBatch = jQuery("#getNumberOfEntriesInPaginationBatch").html();
        var $tabs = 	jQuery("#tabs").tabs();
        setUpMainMenuIcons();
        //scroll the message box to the top offset of browser's scrool bar
        jQuery(window).scroll(function()
        {
            jQuery('#message_box').animate({
                opacity: 1,
                top:jQuery(document).height()-150+"px"
                //bottom:jQuery(window).height-700+"px"
            },{queue: false, duration: 550});
        });

        // declare two ways to store an integer
        var I;
        var myIntObject = new intObject();

        // assign initial values
        i = 1;
        myIntObject.i = 1;

        jQuery(window).scroll(function(){
            if  ((jQuery(window).scrollTop()+100) == ((jQuery(document).height()+200) - jQuery(window).height()-100)){
                var selected = $tabs.tabs('option', 'selected');

                if (selected ==0)
                {
                    getMorePaginatedSubmittedResults(myIntObject,numberOfEntriesInPaginationBatch,"allResults",formNumber);
                }
                if (selected ==1)
                {
                    getMorePaginatedSubmittedResults(myIntObject,numberOfEntriesInPaginationBatch,"latestResults",formNumber);
                }

            }
        });

        setUpAjaxQueryToViewParticularResult();
        setUpAjaxQueryToViewLatestResult();
        setUpajaxQueryToDownloadCSVSubmitResultsFile();
        jQuery("#tabs").bind('tabsselect', function(event, ui) {
            jQuery('a[title=viewParticularResult]').unbind();
            jQuery('a[title=viewLatestParticularResult]').unbind();
            setUpAjaxQueryToViewParticularResult();
            setUpAjaxQueryToViewLatestResult();
            setUpajaxQueryToDownloadCSVSubmitResultsFile();
        });
        //var line1 = [1,4, 9, 16];
        //var line2 = [25, 12, 6, 3];
        //var line3 = [2, 7, 15, 30];
        //plot2 = jQuery.jqplot('chart1', [line1, line2, line3], {
        //    legend:{show:true, location:'ne', xoffset:55},
        //    title:'Bar Chart With Options',
        //   stackSeries: false,
        //   seriesDefaults:{
        //        renderer:jQuery.jqplot.BarRenderer,
        //
        //        rendererOptions:{barPadding: 8, barMargin: 20},
        //              pointLabels:{stackedValue: false}
        //
        //    },
        //    series:[
        //        {label:'Profits'},
        //        {label:'Expenses'},
        //        {label:'Sales'}
        //    ],
        //    axes:{
        //        xaxis:{
        //            renderer:jQuery.jqplot.CategoryAxisRenderer,
        //            ticks:['1st Qtr', '2nd Qtr', '3rd Qtr', '4th Qtr']
        //        },
        //        yaxis:{min:0}
        //    }
        //});
        //
        //jQuery('#tabs').bind('tabsshow', function(event, ui) {
        //  if (ui.index == 1 && plot1._drawCount == 0) {
        //    plot1.replot();
        //  }
        //  else if (ui.index == 2 && plot2._drawCount == 0) {
        //    plot2.replot();
        //  }
        //});

        //var line1 = [['frogs',3], ['buzzards',7], ['deer',2.5], ['turkeys',6], ['moles',5], ['ground hogs',4]];
        //plot2 = jQuery.jqplot('chart2', [line1], {
        //    title: 'Pie Chart with Legend and sliceMargin',
        //    seriesDefaults:{renderer:jQuery.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}},
        //    legend:{show:true}
        //});
        //var line1 = [14, 32, 41, 44, 40, 47, 53, 67];
        //plot1 = jQuery.jqplot('chartdiv1', [line1], {
        //    title: 'Chart with Point Labels',
        //    seriesDefaults: {showMarker:false},
        //    axesDefaults:{pad:1.3}
        //});
    });


</script>
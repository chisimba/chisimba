<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>
<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        
        {
        "display" : "block",
        "module" : "oer",
        "block" : "filterproduct",
        <?php
        echo '"configData":';
        echo '"' . $filteraction .'__'.$filteroptions. '"';
        ?>
        }




        <div id="leftdynamic_area" class="leftdynamic_area_layer"></div>
        <div id="leftfeedback_area" class="leftfeedback_area_layer"></div>
    </div>
    <div id="Canvas_Content_Body_Region3">
        <div class="featurebox">
            <div class='featureboxtopcontainer'>
                <h5 class="featureboxheader">
                    Important notice
                </h5>
                <div class="featureboxcontent" style="overflow: hidden; ">
                    <span class="warning" style="font-weight: normal;">Please note that this site is for staging purposes, it 
                    is not a production site. It contains code that is undergoing
                    development, and that is deployed here for testing. The site
                    only has enough content for basic testing purposes, and quite
                    likely has bugs.
                    </span>
                </div>
            </div>
        </div>
        {
        "display" : "block",
        "module" : "oer",
        "block" : "featuredoriginalproduct"
        }

        {
        "display" : "block",
        "module" : "oer",
        "block" : "mostarc"
        }
        <div id="rightdynamic_area" class="rightdynamic_area_layer">

        </div>
        <div id="rightfeedback_area" class="rightfeedback_area_layer"></div>
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
        "display" : "block",
        "module" : "oer",
        "block" : "originalproductslisting",
        <?php
        echo '"configData":';
        if (isset($filter)) {
            echo '"' . $mode . '__' . $filter .'__'.$filteroptions. '"';
        } else {
            echo '"' . $mode . '"';
        }
        ?>
        }


        <div id="middledynamic_area" class="middledynamic_area_layer">&nbsp;</div>
        <div id="middlefeedback_area" class="middlefeedback_area_layer">&nbsp;</div>
    </div>

</div>

<?php
// Get the contents for the layout template 
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>
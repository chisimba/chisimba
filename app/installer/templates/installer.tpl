<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

				
		

        <title>Chisimba | Install : <?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" type="text/css" href="../skins/echo/main.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="./domtt/example.css" />
        <link rel="stylesheet" type="text/css" href="../skins/echo/print.css" media="print" />
         <link rel="stylesheet" type="text/css" href="<?php echo $extras;?>/secondary.css" media="screen" />
            
        <script language="javascript" type="text/javascript"  src="<?php echo $extras;?>/general.js"></script>
		<script type="text/javascript" language="javascript" src="domtt/domLib.js"></script>
        <script type="text/javascript" language="javascript" src="domtt/fadomatic.js"></script>
        <script type="text/javascript" language="javascript" src="domtt/domTT.js"></script>
        <script type="text/javascript" src="../skins/echo/js/common.js"></script>
        <script>
            var domTT_styleClass = 'domTTOverlib';
            var domTT_oneOnly = true;
        </script>
		<script language="Javascript" type="text/javascript">
		
		var submitted = false;
		
		function do_working()
		{
			// if the form has already been submitted, alert the user
			if (submitted) {
				alert('The step is being processed, please wait');
				return false;
			}
			submitted = true;
			
			return true;     
		}
		
		function generateServerName() {
	     Stamp = new Date();
	     document.wizardform.generatedName.value = "TRUE";
	     document.wizardform.serverName.value = "gen"+Stamp.getHours()+"Srv"+Stamp.getMinutes()+"Nme"+Stamp.getSeconds();
		}
	
		</script>
        <script language="Javascript" type="text/javascript">
            if (document.images) {
			
				cancel_off = new Image();
				cancel_off.src = "<?php echo $extras;?>/cancel_off.gif";
				cancel_on = new Image();
				cancel_on.src = "<?php echo $extras;?>/cancel_on.gif";
				
				skip_off = new Image();
				skip_off.src = "<?php echo $extras;?>/skip_off.gif";
				skip_on = new Image();
				skip_on.src = "<?php echo $extras;?>/skip_on.gif";
				
				next_off = new Image();
				next_off.src = "<?php echo $extras;?>/next_off.gif";
				next_on = new Image();
				next_on.src = "<?php echo $extras;?>/next_on.gif";
				
				previous_off = new Image();
				previous_off.src = "<?php echo $extras;?>/previous_off.gif";
				previous_on = new Image();
				previous_on.src = "<?php echo $extras;?>/previous_on.gif";
            }
            
            function over(imgName, input) {
                if (document.images) {
                    imgOn = eval(imgName + "_on.src");
					input.src = imgOn;
                }
            }
            
            function off(imgName, input) {
                if (document.images) {
                    imgOn = eval(imgName + "_off.src");
					input.src = imgOn;
                }
            }
            
            function changeTextSize() {
                var contentDiv = document.getElementById('content');
                if (contentDiv.className == 'content-big') {
                    contentDiv.className = 'content-small';
                } else {
                    contentDiv.className = 'content-big';
                }
            }
        </script>
    </head>

    <body id="type-a">
	
	   <div id="wrap">
    	
            <div id="header">
                <a href="http://Kngforge.uwc.ac.za/">
    			<!--img id="kng_logo" src="<?php echo $extras;?>/largebanner.jpg" alt="KNG logo" title="KNG" width="550" height="80" border="0" /-->
    			<!--img id="kng_logo_rotate" style="display: none" src="<?php echo $extras;?>/smallbanner.jpg" alt="FSIU logo" title="FSIU" width="416" height="45" border="0" /-->
    			     <div id="site-name">Chisimba</div>
    			</a>
            </div> <!-- header div -->
            
            <div id="content-wrap">
	
		          <div id="content">
                    <div class="featurebox">
                    <h2> <?php echo $title;?></h2>
                                     
               
        		<div id="install_status">
        			<table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                         <tr>
                           <td>
                           </td>           
                            <td style="width: 100%; text-align: right;">
        						<!--a href="<?php echo $help_url;?>" target="_blank">wes<img src="<?php echo $extras;?>/yellow_help_off.png" border="0" alt="Help" title="Help"  /></a-->
        						<?php echo $help; ?> 
        						
        						</td>
                        </tr>
                        
                    </table>
                    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                         <tr>
                            <?php
                            $step_width = floor(580 / $total_steps);
                            // Display uncompleted TDs
                            for($i=0; $i<$total_steps; $i++) {
                            	if ($i == 0) 
                            		$text = 'Start';
                            	else if ($i == $total_steps - 1) 
                            		$text = 'Complete';
                            	else 
                            		$text = $i;
                            	
                            	if ($i < $current_step) {
                            		$step_class = 'complete-step';	
                            		$text = '<a href="./?current_step='.$i.'" style="display:block">'.$text.'</a>';
                            	}
                            	if ($i == $current_step) {
                            		$step_class = 'current-step';	
                            	}
                            	if ($i > $current_step) 
                            		$step_class = 'incomplete-step';	
                            	?>
                            		<td width="<?php echo $step_width?>" class="<?php echo $step_class;?>"><?php echo $text; ?></td>
                            	<?php
                            }
                            ?>
                        </tr>
                        
                    </table>
        		</div>
        		<div id="step_display">
        			<form name="wizardform" onsubmit="return do_working();" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
        			<input type="hidden" name="current_step" value="<?php echo $current_step;?>" />
        				
        				<p class="subdued">
        					<?php echo $step_details ?>
        					</p>
        				
        				
        				
        				<div class="error">
        					<?php echo $error ?>
        				</div>
        				
        				<div id="buttons">
        				<table>
        				
        				</table>
        				<?php if (!$complete) { ?>
        						<input id="next_button" type="image" onmouseover="over('next', this)" onmouseout="off('next', this)" src="./extra/next_off.gif" name="next" value="Next">
        					<?php } ?>
        					
        					<?php if ($enable_skip) {?>
        						<input type="image" onmouseover="over('skip', this)" onmouseout="off('skip', this)" src="./extra/skip_off.gif" name="skip" value="Skip Step">
        					<?php } ?>
        					
        					<?php if (!$start) { ?>
        						<input type="image" onmouseover="over('previous', this)" onmouseout="off('previous', this)" src="./extra/previous_off.gif" name="previous" value="Previous">
        					<?php } ?>
        					<?php if ($can_cancel) {?>
        						<input type="image" onmouseover="over('cancel', this)" onmouseout="off('cancel', this)" src="./extra/cancel_off.gif" name="cancel" value="Cancel">
        					<?php } ?>
        					        					
        				</div>
        			</form>
        		</div>
		
             </div>
         </div> <!-- content div -->
        </div> <!-- content-wrap div -->
	</div> <!-- wrap div -->
	
    </body>
</html>


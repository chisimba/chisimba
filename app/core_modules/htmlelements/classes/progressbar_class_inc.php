<?php
/**
 * Class to create a pseudo progress bar in php
 * @author Paul Scott based on a script by Chris Geisler
 * @copyright (c) UWC 2004
 */

class progressbar
{
	var $name;	// The name of the html element representing the progress bar.
	var $percent;   // The current percent value of the progress bar.
	var $width;     // The maximum width of the progress bar.

        function init()
	{
		$this->objLanguage = &$this->getObject('language','language');
	}
	
        function dispatch()
        {
          $this->name = $name;
          $this->percent = $percent;
          $this->width = $width;
          if($auto_create) {
            $this->create();
          }
        }
	/**
	 *Function progress_bar - The progress bar constructor function
	 *@param $name the name of the html element representing the progress bar.
	 *@param $percent the initial percent value of the progress bar.
	 *@param $width the initial width of the progress bar.
	 *@param $auto_create if set to TRUE the create() function will be called upon construction of the progress bar
	 */
	//function progress_bar($name = 'pbar',$percent = 1,$width = 100,$auto_create = TRUE)
	//{
	//	$this->name = $name;

	//	$this->percent = $percent;

	//	$this->width = $width;
	//	if($auto_create)
	//	{
	//		$this->create();
	//	}
  	//}

	/**
	 * Function create() - Dispalys the progress bar as an html element. (Warning: do not call this function twice or if 
	 * $auto_create is set to TRUE)
	 * @param $name sets the name of the html element (This function becomes usless after the create(); 
	 * function is called.)
	 */
	function create()
	{
		?>
		<div align="center">
		  <center>
		  <table height="20" name="<? echo('table_' . $this->name);?>" border="0" cellpadding="0" cellspacing="0" width="<? echo($this->width + 60);?>">
			<tr>
              <td width="52" height="20"><p>Progress: </p></td>
		      <td width="4" height="20" valign="top" align="left"><img border="0" src="begin.gif" width="4" height="20" /></td>
		      <td name="<? echo('cell_' . $this->name);?>" align="left" valign="middle" width="<? echo($this->width);?>" height="20" style="background-repeat: repeat-x" background="http://fill_bg.gif"><img name="<? echo($this->name)?>" border="0" src="fill.gif" width="<? echo(($percent * .01) * $width);?>" height="11" /></td>
		      <td width="4"  height="20" valign="top" align="left"><img border="0" src="end-filled.gif" width="4" height="20" /></td>
		    </tr>
		  </table>
		  </center>
		</div>
		<?
	}

	/**
	 * Function set_name() - Sets the $percent of the object based
 	 * on current tasks done and the total tasks to finish.
	 * @param $name sets the name of the html element
	 * (This function becomes usless after the create(); function is called.)
	 */
	function set_name($name)
	{
		$this->name = $name;
	}

	/**
	 * Function set_percent() - Sets the $percent of the progress bar
 	 * using a pre-calculated percent.
	 * @param $percent the pre-calculated percent
	 */
	function set_percent($percent)
	{
		$this->percent = $percent;

		echo('<script>document.images.' . $this->name . '.width = ' . ($this->percent / 100) * $this->width . '</script>');
	}

 	/**
	 * Function set_percent_adv() - Sets the $percent of the object based
 	 * on current tasks done and the total tasks to finish.
	 * @param $cur_amount the curent number of tasks completed in a script
	 * @param $max_amount the number of tasks to complete in a script
	 */
	function set_percent_adv($cur_amount,$max_amount)
	{
		$this->percent = ($cur_amount / $max_amount) * 100;
		echo('<script>document.images.' . $this->name . '.width = ' . ($this->percent / 100) * $this->width . '</script>');
	}

	/**
	 * Function set_width() - Sets the maximum $width of the progress bar.
	 * @param $cur_amount the curent number of tasks completed in a script
	 * @param $max_amount the number of tasks to complete in a script
	 */
	function set_width($width)
	{
		$this->width = $width;
	}
}

?>

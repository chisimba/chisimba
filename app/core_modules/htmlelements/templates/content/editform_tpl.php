<?php
/**
$paramArray=array(
	'action'=>'save',
	'mode'=>$mode);
//*/
?>
<body>
<?php 
echo $myForm;
echo '<br>'.$modes;
/*
<img src="<?php echo $objSkin->bannerImageBase() ?>smallbanner.jpg" alt="banner">

<form action="<?php echo $objEngine->uri($paramArray); ?>" 
   method="post" name="StoryEditForm" 
   id="StoryEditForm">
  <table width="80%">
  	<tr>
		<td class="odd" align="center">
			<b><?php echo $objLanguage->languageText("phrase_storyId"); ?></b>
		</td>
  	</tr>
	<tr>
		<td class="even" align="center">
			<input class="<?php echo $idclass; ?>" type="<?php echo $storyIdfieldtype ?>" name="storyId" value="<?php echo $storyId ?>">
			<?php //echo $displaytext ?>
		</td>
  	</tr>
	
  	<tr>
		<td class="odd" align="center"><b><?php echo $objLanguage->languageText("word_author"); ?></b></td>
  	</tr>
	<tr>
		<td class="even" align="center">
			<?php echo $objUser->fullname($userId); ?>
		</td>
  	</tr>
	
  	<tr>
		<td class="odd" align="center"><b><?php echo $objLanguage->languageText("word_title"); ?></b></td>
  	</tr>
	<tr>
		<td class="even" align="center">
			<textarea cols="60" rows="3" name="title" id="title"><?php echo $title; ?></textarea>
		</td>
  	</tr>
	
  	<tr>
		<td class="odd" align="center"><b><?php echo $objLanguage->languageText("word_abstract"); ?></b></td>
  	</tr>
	<tr>
		<td class="even" align="center">
			<textarea cols="60" rows="3" name="abstract" id="abstract"><?php echo $abstract; ?></textarea>
		</td>
  	</tr>
	
  	<tr>
		<td class="odd" align="center"><b><?php echo $objLanguage->languageText("word_story"); ?></b></td>
  	</tr>
	<tr>
		<td class="even" align="center">
			<textarea cols="60" rows="10" name="mainText" id="mainText"><?php echo $mainText; ?></textarea>
		</td>
  	</tr>
	
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td class="odd" align="center">
						<?php echo $objLanguage->languageText("phrase_isactive"); ?>
					</td>
					<td class="odd" align="center">
						<?php echo $objLanguage->languageText("phrase_dateposted"); ?>
					</td>
					<td class="odd" align="center">
						<?php echo $objLanguage->languageText("phrase_expirationdate"); ?>
					</td>
				</tr>
				<tr>
					<td align="center" class="even">
						<input type="text" name="isActive" id="isActive" 
						value="<?php echo $isActive; ?>">
					</td>
					<td align="center" class="even">
						<?php echo $creationDate; ?>
					</td>
					<td align="center" class="even">
						<input type="text" name="expirationDate" id="expirationDate" 
						value="<?php echo $expirationDate; ?>">
					</td>
				</tr>
			</table>
		</td>
  	</tr>
	
	<tr>
	 	<td>
			<?php echo $saveButton; ?>
		</td>
	</tr>
  </table>
</form>
*/
?>
</body>
</html>
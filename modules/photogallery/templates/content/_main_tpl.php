<?php
print '
<div id="wrap">
  <h1 id="albumName" spry:region="dsGallery">{sitename} <span class="return"></span></h1>
  <div id="previews">
    <div id="galleries" spry:region="dsGalleries">
    <form id="grid" name="grid>
      <label for="gallerySelect">View:</label>
      <select spry:repeatchildren="dsGalleries" id="gallerySelect" onchange="dsGalleries.setCurrentRowNumber(this.selectedIndex);document.forms[\'form1\'].galleryname.value = this.options[selectedIndex].value;">
        <option spry:if="{ds_RowNumber} == {ds_CurrentRowNumber}" selected="selected">{sitename}</option>
        <option spry:if="{ds_RowNumber} != {ds_CurrentRowNumber}">{sitename}</option>
      </select>
      </form>
    </div>
    <div id="controls">
      <ul id="transport">
        <li><a href="#" onclick="StopSlideShow(); AdvanceToNextImage(true);" title="Previous">Previous</a></li>
        <li class="pausebtn"><a href="#" onclick="if (gSlideShowOn) StopSlideShow(); else StartSlideShow();" title="Play/Pause" id="playLabel">Play</a></li>
        <li><a href="#" onclick="StopSlideShow(); AdvanceToNextImage();" title="Next">Next</a></li>
      </ul>
    </div>
    <div id="thumbnails" spry:region="dsPhotos dsGalleries dsGallery">
      <div spry:repeat="dsPhotos" onclick="HandleThumbnailClick(\'{ds_RowID}\');" onmouseover="GrowThumbnail(this.getElementsByTagName(\'img\')[0], \'{@thumbwidth}\', \'{@thumbheight}\');" onmouseout="ShrinkThumbnail(this.getElementsByTagName(\'img\')[0]);"> <img id="tn{ds_RowID}" alt="thumbnail for {@thumbpath}" src="usrfiles/galleries/{dsGalleries::@base}{dsGallery::thumbnail/@base}{@thumbpath}" width="24" height="24" style="left: 0px; right: 0px;" /> </div>
      <p class="ClearAll"></p>
    </div>
    
  </div>
  
  
    
  <div id="picture">
    <div id="mainImageOutline" style="width: 0px; height: 0px;"><img id="mainImage" alt="main image" /></div>
  </div>
  
  <p class="clear"></p>
  <div id="admin">
    '.$admin.'
    </div>
</div>';
?>
<script>

function getTheName()
{
    
    ind = document.forms['grid'].gallerySelect.selectedIndex;
    //alert(ind);
    e = document.forms['grid'].gallerySelect;
    document.form1.galleryname.value = e.options[ind].text;
    document.form1.submit();
}
</script>
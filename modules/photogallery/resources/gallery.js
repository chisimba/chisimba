 /* gallery.js - Revision: Spry Preview Release 1.4 */

// Copyright (c) 2006. Adobe Systems Incorporated.
// All rights reserved.
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
//
//   * Redistributions of source code must retain the above copyright notice,
//     this list of conditions and the following disclaimer.
//   * Redistributions in binary form must reproduce the above copyright notice,
//     this list of conditions and the following disclaimer in the documentation
//     and/or other materials provided with the distribution.
//   * Neither the name of Adobe Systems Incorporated nor the names of its
//     contributors may be used to endorse or promote products derived from this
//     software without specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
// AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
// IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
// ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
// LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
// CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
// SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
// INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
// CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
// ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
// POSSIBILITY OF SUCH DAMAGE.

// Global variables:

var gThumbWidth;
if (gThumbWidth == undefined)
  gThumbWidth = 24;
  
var gThumbHeight;
if (gThumbHeight == undefined)
  gThumbHeight = 24;

var gSlideShowInterval;
if (gSlideShowInterval == undefined)
  gSlideShowInterval = 3000; // msecs between images.

var gAutoStartSlideShow;
if (gAutoStartSlideShow == undefined)
  gAutoStartSlideShow = true;

var gBehaviorsArray = [];
var gSlideShowOn = false;
var gSlideShowTimer = null;
var gImageLoader = null;

// Register a callback on the dsPhotos data set so we can turn
// off the slide show before it attempts to load new data.

dsPhotos.addObserver(function(nType, notifier, data) {
  if (nType == "onPreLoad")
    StopSlideShow();
});

// Register a callback on the thumbnails region so we can show the first
// image in the data set after all the thumbnails have loaded.

Spry.Data.Region.addObserver("thumbnails", function(nType, notifier, data) {
  if (nType == "onPostUpdate")
  {
    ShowCurrentImage();
    if (gAutoStartSlideShow)
      StartSlideShow(true);
  }
});

// Trigger the transition animation from the current image
// being displayed to the image at imgPath.

function SetMainImage(imgPath, width, height, tnID)
{
  var img = document.getElementById("mainImage");
  if (!img)
    return;

  CancelBehavior("mainImage");

  Spry.Utils.SelectionManager.clearSelection("thumbnailSelection");

  if (tnID)
    Spry.Utils.SelectionManager.select("thumbnailSelection", document.getElementById(tnID), "selectedThumbnail");

  if (gImageLoader)
  {
    gImageLoader.onload = function() {};
    gImageLoader = null;
  }

  gBehaviorsArray["mainImage"] = new Spry.Effect.Opacity(img, Spry.Effect.getOpacity(img), 0, { duration: 400,
    finish: function()
	{
      gBehaviorsArray["mainImage"] = new Spry.Effect.Size(img.parentNode, Spry.Effect.getDimensions(img.parentNode), { width: width, height: height, units:"px"}, {duration: 400,
	    finish: function()
	    {
          // Use an image loader to make sure we only fade in the new image after
          // it is completely loaded.
          gImageLoader = new Image();
          gImageLoader.onload = function()
          {
            img.src = gImageLoader.src;
            gImageLoader = null;
            gBehaviorsArray["mainImage"] = new Spry.Effect.Opacity(img, 0, 1, { duration: 400,
              finish: function()
              {
                gBehaviorsArray["mainImage"] = null;

                // Our new image is fully visible now. If the slide show
                // is on, fire off the timer for the next image.

                if (gSlideShowOn)
					SetSlideShowTimer();
              }});
            gBehaviorsArray["mainImage"].start();
          };
          gImageLoader.src = imgPath;
        }
      });
      gBehaviorsArray["mainImage"].start();
    }
  });
  gBehaviorsArray["mainImage"].start();
}

// Cancel the animation behavior of the object with the given id.

function CancelBehavior(id)
{
  if (gBehaviorsArray[id])
  {
    gBehaviorsArray[id].cancel();
    gBehaviorsArray[id] = null;
  }
}

function SizeAndPosition(id, toX, toY, toWidth, toHeight, callback)
{
  CancelBehavior(id);
  var effectCluster = new Spry.Effect.Cluster( { finish: callback } );
  var ele = Spry.Effect.getElement(id); 
  var moveEffect = new Spry.Effect.Move(ele, Spry.Effect.getPosition(ele), { x: toX, y: toY, units: "px" }, { duration: 400 });
  var sizeEffect = new Spry.Effect.Size(ele, Spry.Effect.getDimensions(ele), { width: toWidth, height: toHeight, units: "px" }, { duration: 400 });
  
  effectCluster.addParallelEffect(moveEffect);
  effectCluster.addParallelEffect(sizeEffect);

  //effectCluster.finish = callback;
 
  gBehaviorsArray[id] = effectCluster;
  gBehaviorsArray[id].start();
}

// Trigger the animation of the thumbnail growing.

function GrowThumbnail(img, width, height)
{
  Spry.Utils.addClassName(img, "inFocus");
  img.style.zIndex = 150;

  var id = img.getAttribute("id");

  var twidth = Math.floor(width * .75);
  var theight = Math.floor(height * .75);
  var tx = (gThumbWidth - twidth) / 2;
  var ty = (gThumbHeight - theight) / 2;

  SizeAndPosition(id, tx, ty, twidth, theight, function(b){gBehaviorsArray[id] = null;});
}

// Trigger the animation of the thumbnail shrinking.

function ShrinkThumbnail(img)
{
  Spry.Utils.addClassName(img, "inFocus");
  img.style.zIndex = 1;

  var id = img.getAttribute("id");

  SizeAndPosition(id, 0, 0, gThumbWidth, gThumbHeight, function(b){gBehaviorsArray[id] = null; Spry.Utils.removeClassName(img, "inFocus");});
}

// Show the image of the current selected row inside the dsPhotos data set.

function ShowCurrentImage()
{
  var curRow = dsPhotos.getCurrentRow();
  SetMainImage("usrfiles/galleries/" + dsGalleries.getCurrentRow()["@base"] + "images/" + curRow["@path"], curRow["@width"], curRow["@height"], "tn" + curRow["ds_RowID"]);
}

// Utility function to advance (forwards or backwards) the current selected row
// in dsPhotos. This has the side effect of "selecting" the thumbnail and image
// of the new current row.

function AdvanceToNextImage(moveBackwards)
{
  var rows = dsPhotos.getData();
  var curRow = dsPhotos.getCurrentRow();
  
  if (rows.length < 1)
    return;

  for (var i = 0; i < rows.length; i++)
  {
    if (rows[i] == curRow)
    {
      if (moveBackwards)
        --i;
      else
        ++i;
      break;
    }
  }

  if (!moveBackwards && i >= rows.length)
    i = 0;
  else if (moveBackwards && i < 0)
    i = rows.length - 1;

  curRow = rows[i];
  dsPhotos.setCurrentRow(curRow["ds_RowID"]);
  ShowCurrentImage();
}

function SetSlideShowTimer()
{
  KillSlideShowTimer();
  gSlideShowTimer = setTimeout(function(){ gSlideShowTimer = null; AdvanceToNextImage(false); }, gSlideShowInterval);
}

function KillSlideShowTimer()
{
  if (gSlideShowTimer)
    clearTimeout(gSlideShowTimer);
  gSlideShowTimer = null;
}

// Start the slide show that runs forwards through all
// the rows in dsPhotos.

function StartSlideShow(skipTimer)
{
  gSlideShowOn = true;
  if (!skipTimer)
  	SetSlideShowTimer();
  var playLabel = document.getElementById("playLabel");
  if (playLabel)
    playLabel.firstChild.data = "Pause";
}

// Kill any slide show that is currently running.

function StopSlideShow()
{
  gSlideShowOn = false;
  KillSlideShowTimer();
  var playLabel = document.getElementById("playLabel");
  if (playLabel)
    playLabel.firstChild.data = "Play";
}

function HandleThumbnailClick(id)
{
  StopSlideShow();
  dsPhotos.setCurrentRow(id);
  ShowCurrentImage();
}

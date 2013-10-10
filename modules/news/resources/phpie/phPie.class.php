<?php
//////////////////////////////////////////////////////////////
///   phPie() by James Heinrich <info@silisoftware.com>     //
//        available at http://www.silisoftware.com         ///
//////////////////////////////////////////////////////////////
///        This code is released under the GNU GPL:         //
//           http://www.gnu.org/copyleft/gpl.html          ///
//////////////////////////////////////////////////////////////

class phPie {

	var $data              = array('NO DATA'=>1);
	var $width             = 400;   // width of generated image, in pixels
	var $height            = 250;   // height of generated image, in pixels
	var $CenterX           = null;  // defaults to round($width / 2)
	var $CenterY           = null;  // defaults to round($height / 2)
	var $DiameterX         = null;  // defaults to round($width * 0.95)
	var $DiameterY         = null;  // defaults to round($height * 0.95)
	var $MinDisplayPercent = 1;     // smallest slice drawn on chart, in percent of total
	var $MarginPercent     = 10;     // margin around graph, in percent of image area
	var $DisplayColors     = array('333399','339933','993333','339966', '336699', '663399', '669933', '993366', '996633', '66CC99', '6699CC', '9966CC', '99CC66', 'CC6699', 'CC9966');
	var $BackgroundColor   = 'FFFFFF';
	var $LineColor         = 'FFFFFF';
	var $OtherColor        = '666666';
	var $Legend            = true;  // if true (and LegendOnSlices is false), show legend down left side
	var $LegendOnSlices    = true;  // if true, label slices with data name and percentage
	var $FontNumber        = 5;     // size (1 = smallest, 5 = largest) of text written to graph
	var $SortData          = true;  // if true, sort data into largest-to-smallest order before graphing
	var $StartAngle        = 0;     // start point of pie: 0 = right, 90 = bottom, 180 = left, 270 = top
	var $SaveFilename      = '';    // if not empty, graph will be saved to this file instead of displayed
	var $antialias		   = 1.5;   // antialiasing amount (number larger than 1). Larger values give smoother display. Graph is rendered at $antialias times the final resolution and resampled down
	var $title			   = '';
	var $outline		   = 1;

	function phPie() {
		if (!function_exists('imagecopyresampled')) {
			$this->antialias = 1;
		}
		return true;
	}

	function DisplayPieChart() {
		if ($this->antialias) {
			$this->width  *= $this->antialias;
			$this->height *= $this->antialias;
		}
		if ($img = $this->InitializeCanvas()) {
			$this->PlotPie($img);
			if ($this->SaveFilename) {
				$this->SaveImage($img, $this->SaveFilename);
			} else {
				$this->OutputImage($img);
			}
			ImageDestroy($img);
			return true;
		}
		return false;
	}

	function ImageCreateSafe($width, $height) {
		if (function_exists('imagecreatetruecolor')) {
			if ($img = @ImageCreateTrueColor($width, $height)) {
				return $img;
			}
		}
		if (function_exists('imagecreate')) {
			if ($img = @ImageCreate($width, $height)) {
				return $img;
			}
		}
		return false;
	}

	function InitializeCanvas() {
		if ($img = $this->ImageCreateSafe($this->width, $this->height)) {
			return $img;
		}
		echo 'Cannot Initialize new GD image stream';
		return false;
	}


	function ImageHexColorAllocate(&$img, $HexColorString) {
		$R = hexdec(substr($HexColorString, 0, 2));
		$G = hexdec(substr($HexColorString, 2, 2));
		$B = hexdec(substr($HexColorString, 4, 2));
		return ImageColorAllocate($img, $R, $G, $B);
	}


	function AddItem($key, $value) {
		$this->data[$key] = $value;
		return true;
	}

	function PlotPie(&$img) {
		$background_color = $this->ImageHexColorAllocate($img, $this->BackgroundColor);
		$line_color       = $this->ImageHexColorAllocate($img, $this->LineColor);
		$other_color      = $this->ImageHexColorAllocate($img, $this->OtherColor);

		ImageFilledRectangle($img, 0, 0, ImageSX($img), ImageSY($img), $background_color);

		foreach ($this->DisplayColors as $displaycolor) {
			$fill_color[]  = $this->ImageHexColorAllocate($img, $displaycolor);
			$label_color[] = $this->ImageHexColorAllocate($img, $displaycolor);
		}

		$marginmultiplier = ((100 - $this->MarginPercent) / 100);
		if (is_null($this->CenterX)) {
			$this->CenterX = round($this->width / 2);
		}
		if (is_null($this->CenterY)) {
			$this->CenterY = round($this->height / 2);
		}
		if (is_null($this->DiameterX)) {
			$this->DiameterX = round($this->width * $marginmultiplier);
		}
		if (is_null($this->DiameterY)) {
			$this->DiameterY = round($this->height * $marginmultiplier);
		}
		if ($this->LegendOnSlices) {
			$this->DiameterX = 0.85 * min($this->DiameterX, $this->DiameterY);
			$this->DiameterY = $this->DiameterX;
		} elseif ($this->Legend) {
			$this->DiameterX = min($this->DiameterX, $this->DiameterY);
			$this->DiameterY = $this->DiameterX;
			$this->CenterX   = $this->width  - (($this->DiameterX / $marginmultiplier) / 2);
			$this->CenterY   = $this->height - (($this->DiameterY / $marginmultiplier) / 2);
		}

		if (!empty($this->title)) {
			$this->CenterY += ImageFontHeight($this->FontNumber);
		}

		$TotalArrayValues = array_sum($this->data);
		if ($this->SortData) {
			arsort($this->data);
		}

		$Start = $this->StartAngle;
		$valuecounter = 0;
		$ValuesSoFar  = 0;
		foreach ($this->data as $key => $value) {
			$ValuesSoFar += $value;

			if ($this->LegendOnSlices) {
				$text_height = ImageFontHeight($this->FontNumber);

				$startpoint = ($this->StartAngle / 360) + (($ValuesSoFar - ($value / 2)) / $TotalArrayValues);
				$x_pos = round($this->CenterX + cos($startpoint * 2 * pi()) * $this->DiameterX / 1.85);
				$y_pos = round($this->CenterY + sin($startpoint * 2 * pi()) * $this->DiameterY / 1.85) - round($text_height / 2);



				if ($x_pos < $this->CenterX) {
					// align text that's left-of-centre with right-edge-on-pie, leave other text left-edge-on-pie
					$available = $x_pos / ImageFontWidth($this->FontNumber) - 8;
					if (strlen($key) > $available) {
						$key = substr($key, 0, $available).'..'; //if text goes off screen, truncate and add ...
					}
					$text = $key.' '.number_format(@($value / $TotalArrayValues) * 100, 1).'%';
					$text_width  = ImageFontWidth($this->FontNumber) * strlen($text);
					$x_pos -= $text_width;
				} else {
					$available = ($this->width - $x_pos) / ImageFontWidth($this->FontNumber) - 8;
					if (strlen($key) > $available) {
						$key = substr($key, 0, $available).'..'; //if text goes off screen, truncate and add ...
					}
					$text = $key.' '.number_format(@($value / $TotalArrayValues) * 100, 1).'%';
				}

			} elseif ($this->Legend) {

				$x_pos = 5;
				$y_pos = round((ImageFontHeight($this->FontNumber) * 0.5) + ($valuecounter * 1.5 * ImageFontHeight($this->FontNumber)));
				$text = $key.' '.number_format(($value / $TotalArrayValues) * 100, 1).'%';

			}


			if (!$this->SortData || (($value / $TotalArrayValues) > ($this->MinDisplayPercent / 100))) {

				$End = $this->StartAngle + ceil(($ValuesSoFar / $TotalArrayValues) * 360);

				$this->FilledArc($img, $this->CenterX, $this->CenterY, $this->DiameterX, $this->DiameterY, $Start, $End, $line_color, $fill_color[$valuecounter % count($fill_color)]);

				if ($this->LegendOnSlices || $this->Legend) {
					ImageString($img, $this->FontNumber, $x_pos, $y_pos, $text, $label_color[$valuecounter % count($label_color)]);
				}
				$Start = $End;

			} else {

				// too small to bother drawing - just finish off the arc with no fill and break
				$End = $this->StartAngle + 360;
				if ((($TotalArrayValues - $ValuesSoFar) / $TotalArrayValues) > 0.0025) {
					// only fill in if more than 0.25%, otherwise colors might bleed
					$this->FilledArc($img, $this->CenterX, $this->CenterY, $this->DiameterX, $this->DiameterY, $Start, $End, $line_color, $other_color);
				}
				if ($this->LegendOnSlices || $this->Legend) {
					$oldtextlen = strlen($text);
					$text = 'Other '.number_format((($TotalArrayValues - $ValuesSoFar) / $TotalArrayValues) * 100, 1).'%';
                    
                    $othervalue = number_format((($TotalArrayValues - $ValuesSoFar) / $TotalArrayValues) * 100, 1).'%';
                    
                    
                    /* 
                    Addition by Tohir Solomons

                    Do not show if other is equal to 0.0%
                    */
                    if ($othervalue != '0.0%') {
                        $newtextlen = strlen($text);
                        $x_pos = $this->CenterX+$this->DiameterX/2+10;//$x_pos += ($oldtextlen - $newtextlen) * ImageFontWidth($this->FontNumber);
                        $y_pos = $this->CenterY-5;
                        ImageString($img, $this->FontNumber, $x_pos, $y_pos, $text, $other_color);
                    }
				}
				break;

			}
			$valuecounter++;
		}

		ImageRectangle($img, 0, 0, ImageSX($img)-1, ImageSY($img)-1, $line_color);
		$text_width  = ImageFontWidth($this->FontNumber) * strlen($this->title);
		$x_pos = $this->CenterX - $text_width/2;
		ImageString($img, $this->FontNumber, $x_pos, 5, $this->title, $line_color);
		return true;
	}

	function OutputImage(&$img) {
		// display image
		if (!headers_sent()) {

			if ($this->antialias) {
				$amount = 1 / $this->antialias;
				$anti = $this->ImageCreateSafe($this->width*$amount, $this->height*$amount);
				ImageCopyResampled($anti, $img, 0, 0, 0, 0, $this->width * $amount, $this->height * $amount, $this->width, $this->height);
				$img = $anti;
			}

			$imagetypes = imagetypes();
			if ($imagetypes & IMG_PNG) {
				header('Content-type: image/png');
				ImagePNG($img);
			} elseif ($imagetypes & IMG_GIF) {
				header('Content-type: image/gif');
				ImageGIF($img);
			} elseif ($imagetypes & IMG_JPG) {
				header('Content-type: image/jpeg');
				ImageJPEG($img);
			} else {
				echo 'ERROR: Cannot find compatible output method (JPG, PNG, GIF)';
				ImageDestroy($img);
				return false;
			}
			return true;
		}
		echo 'ERROR: headers already sent';
		return false;
	}

	function SaveImage(&$img, $filename) {
		$imagetypes = imagetypes();
		if ($imagetypes & IMG_PNG) {
			ImagePNG($img, $filename);
		} elseif ($imagetypes & IMG_GIF) {
			ImageGIF($img, $filename);
		} elseif ($imagetypes & IMG_JPG) {
			ImageJPEG($img, $filename);
		} else {
			echo 'ERROR: Cannot find compatible output method (JPG, PNG, GIF)';
			return false;
		}
		return true;
	}

	function gd_version() {
		$gd_info = gd_info();
		if (substr($gd_info['GD Version'], 0, strlen('bundled (')) == 'bundled (') {
			return (float) substr($gd_info['GD Version'], strlen('bundled ('), 3); // "2.0" (not "bundled (2.0.15 compatible)")
		}
		return (float) substr($gd_info['GD Version'], 0, 3); // "1.6" (not "1.6.2 or higher")
	}

	function FilledArc(&$img, $CenterX, $CenterY, $DiameterX, $DiameterY, $Start, $End, $line_color, $fill_color='none') {
		if ($this->gd_version() >= 2.0) {

			if ($fill_color != 'none') {
				// fill
				ImageFilledArc($img, $CenterX, $CenterY, $DiameterX, $DiameterY, $Start, $End, $fill_color, IMG_ARC_PIE);
			}
			// outline
			ImageFilledArc($img, $CenterX, $CenterY, $DiameterX, $DiameterY, $Start, $End, $line_color, IMG_ARC_EDGED | IMG_ARC_NOFILL | IMG_ARC_PIE);

		} else {

			// cbriouØorange-art*fr

			// To draw the arc
			ImageArc($img, $CenterX, $CenterY, $DiameterX, $DiameterY, $Start, $End, $line_color);

			// To close the arc with 2 lines between the center and the 2 limits of the arc
			$x = $CenterX + (cos(deg2rad($Start)) * ($DiameterX / 2));
			$y = $CenterY + (sin(deg2rad($Start)) * ($DiameterY / 2));
			ImageLine($img, $x, $y, $CenterX, $CenterY, $line_color);
			$x = $CenterX + (cos(deg2rad($End)) * ($DiameterX / 2));
			$y = $CenterY + (sin(deg2rad($End)) * ($DiameterY / 2));
			ImageLine($img, $x, $y, $CenterX, $CenterY, $line_color);

			if ($fill_color != 'none') {
				if (($End - $Start) > 0.5) {
					// ImageFillToBorder() will flood the wrong parts of the image if the slice is too small
					// thanks Jami Lowery <jamiØego-systems*com> for pointing out the problem

					// To fill the arc, the starting point is a point in the middle of the closed space
					$x = $CenterX + (cos(deg2rad(($Start + $End) / 2)) * ($DiameterX / 4));
					$y = $CenterY + (sin(deg2rad(($Start + $End) / 2)) * ($DiameterY / 4));
					ImageFillToBorder($img, $x, $y, $line_color, $fill_color);
				}
			}
		}
		return true;
	}

}

if (!function_exists('gd_info')) {
	// built into PHP v4.3.0+ (with bundled GD2 library)
	function gd_info() {
		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_end_clean();

		// based on code by johnschaefer at gmx dot de
		// from PHP help on gd_info()
		$gd_info = array(
			'GD Version'         => '',
			'FreeType Support'   => false,
			'FreeType Support'   => false,
			'FreeType Linkage'   => '',
			'T1Lib Support'      => false,
			'GIF Read Support'   => false,
			'GIF Create Support' => false,
			'JPG Support'        => false,
			'PNG Support'        => false,
			'WBMP Support'       => false,
			'XBM Support'        => false
		);
		foreach(explode("\n", $phpinfo) as $line) {
			foreach ($gd_info as $key => $value) {
				if (strpos($line, $key) !== false) {
					$newvalue = trim(str_replace($key, '', strip_tags($line)));
					$gd_info[$key] = (($newvalue == 'enabled') ? true : $newvalue);
				}
			}
		}
		return $gd_info;
	}
}

?>
<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2006-2007 Zack Bloom                                      |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU Lesser General Public              |
 | License as published by the Free Software Foundation; either            |
 | version 2.1 of the License, or (at your option) any later version. 	   |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU Lesser General Public License for more details.                     |
 |                                                                         |
 | You should have received a copy of the GNU Lesser General Public        |
 | License along with this library; if not, write to the Free Software     |
 | Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA           |
 | 02110-1301, USA                                                         |
 |                                                                         |
 +-------------------------------------------------------------------------+
 | Version 1.8.0 - April 7th, 2007                                         |
 +-------------------------------------------------------------------------+
 | Special Thanks to:                                                      |
 |   Miles Kaufmann - EvalMath Class Library                               |
 |   Walter Zorn    - Javascript Graph Library                             |
 |   Andreas Gorh   - PHP4 Backport                                        |
 |   All Those Who Love PHP :)                                             |
 +-------------------------------------------------------------------------+
 | Code updates and additional features are released frequently, the most  |
 | updated version can always be found at: http://www.zackbloom.org.       |
 |                                                                         |
 | Email me at: zackbloom@gmail.com with any comments, questions and bugs  |
 |                                                                         |
 | Works with PHP 4 & 5 with GD and TTF support.                           |
 +-------------------------------------------------------------------------+
 | - Advanced Graph Class Library - http://www.zackbloom.org/              |
 +-------------------------------------------------------------------------+
*/

class graph {
	public static $numGraphs;

	public $width, $height, $numPoints, $xPts, $yPts, $iVars, $dVars, $ids, $props, $time, $xMax, $yMax, $xMin, $yMin;

	private $img, $imgCache, $fileCache, $imgCacheFile, $lines, $errors, $div, $evalmath, $mode;

	private $font_name, $keyfont_name;

	public function __construct($x_width=400, $x_height=200, $xScale=10, $yScale=6, $mode='image', $div='jg') {
		$this->mode = $mode;

		/* set defaults */ 
        $this->props    = array(); 
        $this->xPts     = array(); 
        $this->yPts     = array(); 
        $this->iVars[0] = array(); 
        $this->dVars[0] = array(); 
        $this->errors   = array(); 
        $this->width    = $x_width; 
        $this->height   = $x_height;

        $this->font_name    = "arial.ttf" ; 
        $this->keyfont_name =  "arial.ttf"; 

        $this->setProp( 'font',    dirname(__FILE__) . '/fonts/' . $this-> font_name); 
        $this->setProp( 'keyfont', dirname(__FILE__) . '/fonts/' . $this-> keyfont_name); 

        if ($this->mode == 'div')
            $this->div = new  jgwrap($div, str_replace (".ttf", "",  str_replace("fonts/", "" , $this->font_name )), self::$numGraphs); 

		$this->numPoints[0] = 0;

		$this->xMax  = 1;
		$this->yMax  = 1;
		$this->xMin  = 0;
		$this->yMin  = 0;
		$this->lines = 1;

		$this->evalmath = new EvalMath;

		$this->setBulkProps("xsclpts:$xScale, ysclpts:$yScale, xsclpts:10, ysclpts:6, xsclmax:1, xsclmin:0,
			ysclmax:1, ysclmin:0, xSclInc:1, ySclInc:.165, sclline:5, onfreq:.2, actwidth:".($x_width+30).",
			actheight:".($x_height+30).", xincpts:10, yincpts:6, autoscl:true");

		$this->setProp("color", array(0,0,255,0));
		$this->setProp("backcolor", array(255,255,255));

		$this->img = imagecreatetruecolor($this->width,$this->height);
	}

	/* remove cached files */
	public function __destruct() {
		if (isset($this->fileCache) && !$this->getProp("keepcache",true)) {
			unlink($this->fileCache);
		}

		if (isset($this->imgCache) && !$this->getProp("keepcache",true)) {
			unlink($this->imgCache);
		}
	}

	public function setProp($name, $value, $l=-1) {
		if ($l+1 > $this->lines) $this->lines++;

		$this->props[$l][strtolower($this->strim($name))] = $this->strim($value);
	}

	public function __set($name,$value) {
		$this->setProp($name,$value);
	}

	public function storeGraph($file="graphCache.data", $image=false) {
		$this->fileCache = $file;

		if ($image) {
			$this->imgCacheFile = $image;
			ob_start();
			imagepng($this->img);
			unset($this->img);
			$img = ob_get_flush();
			file_put_contents($image,$img);
		}

		file_put_contents($file,serialize($this));

		if ($image)
			return array($file, $image);
		else
			return $file;
	}

	public function retriveGraph($file = "graphCache.data") {
		return unserialize(file_get_contents($file));
	}

	public function retriveImage($image) {
		$this->img = imagecreatefrompng($image);
	}

	public function setBulkProps($str) {
		if ($str=="") return false;

		if (func_num_args() == 1) {
			$str = str_replace(
					array(":red", ":green", ":blue", ":orange", ":yellow", ":pink", ":purple", ":teal", ":black", ":white", ":lightgray", ":darkgray"),
					array(":230-60-60", ":100-200-100", ":60-60-230", ":255-160-15", ":255-255-0", ":255-0-170", ":216-0-255", ":0-255-255", ":0-0-0", ":255-255-255", ":150-150-150", ":80-80-80"),
					$str);

			foreach (explode(",",$str) as $e) {
				$d = explode(":",$e);

				if (strpos($d[0],"|")){
					$g = explode("|",$d[0]);
					$d[0] = $g[1];
					$d[2] = $g[0];
				} else {
					$d[2] = -1;
				}

				if (strpos($d[0],"color")!==false) {
					$d[1] = explode("-",$d[1]);
				}

				$this->setProp($d[0],$d[1],$d[2]);
			}
		} else {
			$arg = func_get_args();

			foreach ($arg as $e) {
				if (is_string($e)) {
					$e = explode(":",$e);

					if (strpos("|",$e[0])) {
						$g = explode("|",$e[0]);
						$e[0] = $e[1];
						$e[2] = $e[0];
					} else {
						$e[2] = -1;
					}
				} else {
					if (isset($e[2])==false) {
						$e[2] = -1;
					}
				}

				$this->setProp($e[0],$e[1],$e[2]);
			}
		}
		return true;
	}

	private function strim($a) {
		if (is_string($a)) {
			return trim($a);
		} else {
			return $a;
		}
	}

	public function storePropArr($arr) {
		$this->props = $arr;
	}

	public function getPropArr() {
		return $this->props;
	}

	/* getProp - retrieves the values from the properties array.  Otherwise
	             returns the default value.
	   @arg $name - the property name to be searched
	   @arg $asu  - the default value if not found
	   @arg $l    - the property name array offset */
	public function getProp($name, $asu = false, $l=0) {
		$name = strtolower($this->strim($name));

		if (isset($this->props[$l][$name])) {
			return $this->props[$l][$name];
		} else {
			if (isset($this->props[-1][$name])) {
				return $this->props[-1][$name];
			} else {
				if ($asu===-9) {
					$this->error('The required property '.$name.' was not set');
				}elseif ($asu===-8) {
					$this->error('The property '.$name.' was not found');
				} else {
					return $this->strim($asu);
				}
			}
		}

		return false;
	}

	public function __get($name) {
		return $this->getProp($name,-8);
	}

	public function __call($name, $args) {
		if (count($args)!=2) {
			$this->error("Method $name not found.",true);
		}

		return $this->getProp($name, $args[0], $args[1]);
	}

	public function setColor($obj, $l, $r, $g=-1, $b=-1, $a=0) {
		if (substr($obj,-5)!="color") {
			$obj.="color";
		}
		
		if (is_array($r)){
			$g = $r[1];
			$b = $r[2];
			$r = $r[0];
		}
		
		if (is_string($r) && $g==-1 && $b==-1) {
			$colors = array("red","green","blue","orange","yellow","pink","purple","teal","black","white","lightgray","darkgray");
			$cid = array(array(230,60,60),array(100,200,100),array(60,60,230),array(255,160,15),array(255,255,0),array(255,0,170),array(216,0,255),array(0,255,255),array(0,0,0),array(255,255,255),array(150,150,150),array(80,80,80));
			$x=array_search(strtolower($r),$colors);

			if ($x!==false) {
				$this->setColor($obj,$l,$cid[$x][0],$cid[$x][1],$cid[$x][2],0);
			} else {
				$this->error("Color not found");
			}
		} else {
			$this->setProp($obj,array($r,$g,$b,$a),$l);
		}
	}

	private function xScale($x) {
		if (($this->xMax-$this->xMin)>0) {
			return $this->width * (($x-$this->xMin)/($this->xMax-$this->xMin));
		} else {
			return 0;
		}
	}

	private function yScale($y) {
		if ((($this->yMax-$this->yMin))>0) {
			return $this->height * (($y-$this->yMin)/($this->yMax-$this->yMin));
		} else {
			return 0;
		}
	}

	public function addPoint($d,$i=-5,$l=0) {
		if ($l+1>$this->lines) {
			$this->lines++;
			$this->numPoints[$l] = 0;
			$this->iVars[$l] = array();
			$this->dVars[$l] = array();
		}

		if (is_array($d)) {
			if (count($d)==3)
				return $this->addPoint($d[0],$d[1],$d[2]);
			elseif (count($d)==2 && isset($d[1]))
				return $this->addPoint($d[0],$d[1]);
			elseif (count($d)==2)
				return $this->addPoint($d[0],-5,$d[2]);
			else
				return $this->addPoint($d[0]);
		}

		if ($i==-5) {
			$i=(count($this->iVars[$l])>0?max($this->iVars[$l])+1:0);
		}

		$this->iVars[$l][] = $i;
		$this->dVars[$l][] = $d;
		$id = rand(1,10000000);
		$this->ids[$l][] = $id;
		$this->numPoints[$l]++;

		return $id;
	}

	public function addBulkPoints($a,$sep=":") {
		if (is_string($a)) {
			foreach (explode(",",$a) as $e) {
				$e = explode($sep,$e);

				if (strpos($e[0],"|")) {
					$d = explode('|',$e[0]);
					$e[0] = $d[1];
					$e[2] = $d[0];
				} else {
					$e[2] = 0;
				}

				if (isset($e[1])) {
					if (strpos($e[1],'-')) {
						$e[1] = strtotime($e[1]);
					}
				}

				$ids[] = $this->addPoint($e);
			}
		}elseif (func_num_args()>1) {
			$arg = func_get_args();
			foreach ($arg as $e)
				$ids[] = $this->addPoint($e);
		}elseif (is_array($a)) {
			foreach ($a as $e)
				$ids[] = $this->addPoint($e);
		} else {
			return false;
		}

		return $ids;
	}

	public function delBulkPoints($a) {
		if (is_string($a)) {
			foreach (explode(",",$a) as $e) {
				$ids[] = $this->delPoint($e);
			}
		}elseif (is_array($a)){
			foreach ($a as $e)
				$ids[] = $this->delPoint($e);
		} else {
			$arg = func_get_args();
			foreach ($arg as $e) {
				$ids[] = $this->delPoint($e);
			}
		}

		return $ids;
	}

	private function idSearch($b) {
		foreach ($this->ids as $k => $v) {
			$e = array_search($b,$v);

			if ($e!==false) {
				return array($e,$k);
			}
		}

		return false;
	}

	public function delPoint($id,$k=-1) {
		if ($k==-1) {
			$k = $this->idSearch($id);
		}

		if ($k === false) {
			return $k;
		}

		unset($this->dVars[$k[1]][$k[0]],$this->iVars[$k[1]][$k[0]],$this->ids[$k[1]][$k[0]]);
		$this->numPoints[$k[1]]--;

		return true;
	}

	public function clearPoints() {
		$this->dVars = array();
		$this->iVars = array();
		$this->ids   = array();

		foreach ($this->numPoints as $k => $e) {
			$this->numPoints[$k]=0;
		}
	}

	public function demoData($i=10,$mi=0,$ma=10) {
		for($j=0;$j<$i;$j++) {
			$this->addPoint(rand($mi,$ma));
		}
	}

	private function error($x="Error",$ext=false) {
		$log = $this->getProp("logfile",false);

		if ($x!==true) {
			$this->errors[] = $x;
		} else {
			if (!$this->getProp("production",false)) {
				foreach ($this->errors as $k => $x) {
					imagestring($this->img,2,10,10+$k*12,$x,imagecolorallocate($this->img,255,0,0));
				}
			}

			if ($log) {
				$l = fopen($log,'a');

				if ($this->errors) {
					fwrite($l,"\nSESSION ".date("D M j G:i:s T Y").', '.$_SERVER['REMOTE_ADDR']."\n");
				}

				foreach ($this->errors as $k => $x) {
					fwrite($l,$x."\n");
				}

				fclose($l);
			}
		}

		if ($log && $ext) {
			$l = fopen($log,'a');
			fwrite($l,'FATAL ERROR: '.$x."\n".date("D M j G:i:s T Y").', '.$_SERVER['REMOTE_ADDR']."\n\n");
		}

		if (!$this->getProp("production",false)) {
			if ($ext==true) {
				trigger_error($x,E_USER_ERROR);
			}
		} else {
			trigger_error("Their was an error, please contact the webmaster",E_USER_ERROR);
		}
	}

	public function importMySQL($table,$field,$rcodb=null,$user=null,$pass=null,$server="localhost",$freq=true){
		if (!is_resource($rcodb)) {
			$m = mysql_connect($server,$user,$pass);

			if (!$m) {
				$this->error("Could not connect to MySQL server.",true);
			}

			$d = mysql_select_db($rcodb,$m);

			if (!$d) {
				$this->error("Could not select MySQL database.",true);
			}

			$rcodb = $m;
		}

		$q = mysql_query("SELECT `$field` FROM $table");

		if (!$q) {
			$this->error("Error querying MySQL database, check table and field.",true);
		}

		while ($r = mysql_fetch_array($q, MYSQL_NUM)) {
			$a[] = $r[0];
		}

		if ($freq==true) {
			$s = array_count_values($a);

			foreach ($s as $e) {
				$this->addPoint($e);
			}

			$this->setProp("key",array_keys($s));
			$this->setProp("showkey",true);
		} else {
			$this->addBulkPoints($a);
		}
	}

	public function importCSV($file, $format='l,d,i', $dl=0) { //Read the readme
		@$handle = fopen($file,"r");

		if (!$handle) {
			$this->error("Error opening CSV!", E_USER_ERROR);

			return false;
		}

		foreach (explode(',',$format) as $k => $s) {
			$pos[$s] = $k;
		}

		while (($data[] = fgetcsv($handle, 1000)) !== FALSE) {
			/* load data array from file contents */
		}

		foreach ($data as $k => $d) {
			if (isset($pos['i'])) {
				$i = $d[$pos['i']];
			} else {
				$i = -5;
			}

			if (isset($pos['l'])) {
				$l = $d[$pos['l']];
			} else {
				$l = $dl;
			}

			if ($d[$pos['d']] && $i) {
				$idArr[] = $this->addPoint($d[$pos['d']],$i,$l);
			}
		}

		return $idArr;
	}

	public function importXML($file,$ind="i",$dep="d",$block="",$dl=0) { //Read the readme
		if ($block!="") {
			$eblock = "</".$block.">";
			$block  = "<".$block."( $dl=([0-9])*)>";
		}

		$str = file_get_contents($file);
		$str = str_replace(array("\n","\t"),'',$str);

		if ($str==false) {
			$this->error("Error opening XML File!",E_USER_ERROR);

			return false;
		}

		$str = trim($str);
		$arr = array();

		if (preg_match_all("[$block<([$ind|$dep])>([0-9])*</[($ind|$dep)]><([$ind|$dep])>([0-9])*</[($ind|$dep)]>$eblock]",$str,$arr)==0) {
			$this->error("No data found in XML file, check format",true);
		}

		$i=($arr[3][0]==$ind?4:6);
		$d=($arr[3][0]==$ind?6:4);

		foreach ($arr[$d] as $k => $r) {
			$idArr[] = $this->addPoint($arr[$d][$k],$arr[$i][$k],(is_string($dl)?$arr[2][$k]:$dl));
		}

		return $idArr;
	}

	public function graphFunction($func, $minX, $maxX, $l=0) {
		$inv = $this->getProp('funcinterval',.0625);
		$inv += .005;

		$this->evalmath->evaluate('graph(x)='.str_replace('$x','x',$func));

		for($x=$minX;$x<=$maxX;$x+=$inv) {
			$pts[] = $this->addPoint($this->evalmath->evaluate("graph($x)"),$x,$l);
		}

		$this->setProp('sort',false);

		return $pts;
	}

	public function evaluate($f) {
		return $this->evalmath->evaluate($f);
	}

	public function graph() { // Allows internalGraph to return false if it needs to restart
		do
			$x = $this->internalGraph();
		while ($x==false);
	}

	private function cacheImg($img=0) {
		if (isset($this->imgCache)) {
			return $this->imgCache;
		} else {
			$this->imgCache = $img;
		}

		return false;
	}

	/* determine the maximum value in a single dimensional array */
	private function multiMax($a) {
		$max = -1000000000;

		foreach ($a as $e) {
			if (max($e)>$max) {
				$max = max($e);
			}
		}

		return $max;
	}

	/* determine the mimimum value in a single dimensional array */
	private function multiMin($a) {
		$min = 100000000;

		foreach ($a as $e) {
			if (min($e)<$min) {
				$min = min($e);
			}
		}

		return $min;
	}

	/* interesting array index value checker */
	function actSort($a,$b) {
		return ($a[0] == $b[0] ? 0 : ($a[0] < $b[0] ? -1 : 1) );
	}

	private function indSort($e) {
		for($i=0; $i<$this->numPoints[$e]; $i++) {
			$tiVars[] = array($this->iVars[$e][$i], $this->dVars[$e][$i], $this->ids[$e][$i]);
		}

		usort($tiVars, array("graph", "actSort"));

		foreach ($tiVars as $k => $g) {
			$this->iVars[$e][$k] = $g[0];
			$this->dVars[$e][$k] = $g[1];
			$this->ids[$e][$k] = $g[2];
		}
	}

	private function drawBar($x1, $y1, $x2, $y2, $ccol) {
		$mode = $this->getProp("barstyle",0);
		$colorlist = $this->getProp("colorlist",false);

		if ($colorlist === true || $colorlist === 1 || $colorlist === "true") {
			$colorlist = array(array(255, 203, 3),array(220, 101, 29),array(189, 24, 51),array(214, 0, 127),array(98, 1, 96),array(0, 62, 136),array(0, 102, 179),array(0, 145, 195),array(0, 115, 106),array(178, 210, 52),array(137, 91, 74),array(82, 56, 47));
		}

		if ($colorlist) {
			$color = $colorlist[$ccol];
		}

		if (!isset($color[3])) {
				$color[3] = 0;
		} else {
			$color = $this->getProp("color", array(0,0,255,0));
		}

		$bord = $this->getProp("bordercolor",array(0,0,0,0));

		switch ($mode) {
		case 0:
			if ($this->mode!="div") {
				imagefilledrectangle($this->img,$x1,$y1,$x2,$y2,imagecolorallocatealpha($this->img,$color[0],$color[1],$color[2],$color[3]));
				imagerectangle($this->img,$x1,$y1,$x2,$y2,imagecolorallocatealpha($this->img,$bord[0],$bord[1],$bord[2],$bord[3]));
			} else {
				$this->div->setColor($color[0],$color[1],$color[2]);
				$this->div->drawFilledRect($x1,$y1,$x2-$x1,$y2-$y1);
				$this->div->setColor($bord[0],$bord[1],$bord[2]);
				$this->div->drawRect($x1,$y1,$x2-$x1,$y2-$y1);
			}

			break;
		case 1:
			$ge = $this->getProp("gendcolor",array(0,0,0,0));
			$gs = $this->getProp("gstartcolor",array(255,255,255,0));

			for($i=0;$i<4;$i++) {
				$c[$i] = ($ge[$i]-$gs[$i])/($y2-$y1);
				$cc[$i] = $gs[$i];
			}

			for($y=$y1;$y<$y2;$y++) {
				for($i=0;$i<4;$i++) {
					$cc[$i] += $c[$i];
				}

				if ($this->mode!="div") {
					imageline($this->img,$x1,$y,$x2,$y,imagecolorallocatealpha($this->img,$cc[0],$cc[1],$cc[2],$cc[3]));
				} else {
					$this->div->setColor($cc[0],$cc[1],$cc[2]);
					$this->div->drawLine($x1+1,$y,$x2,$y);
				}
			}

			break;
		default:
			$this->error("Bar style not understood.");

			break;
		}
	}

	private function flipMulti(&$arr) {
		foreach ($arr as $i => $e) {
			foreach ($e as $j => $k) {
				$n[$j][$i] = $k;
			}
		}

		$arr = $n;
	}

	function pieSort($a,$b) {
		return ($a==$b ? 0 : (abs($a-90) > abs($b-90) ? -1 : 1));
	}

	private function drawPie($ex) {
		$cx = $this->width/2;
		$cy = $this->height/2+20;
		$ang = $this->getProp("pieangle",35);

		if ($ang>90) {
			$this->error("Angles over 90 cannot be properly graphed.");
		}

		$h = min($this->width,$this->height-10);
		$h = ($h*((90-$ang)/90))+3;
		$w = min(min($this->width,$this->height-10)+$ang*1.5,$this->width);
		$colorlist = $this->getProp('colorlist',array(array(125, 203, 3),array(220, 101, 29),array(189, 24, 51),array(34, 78, 120),array(120, 1, 60),array(0, 62, 136),array(0, 102, 179),array(0, 145, 195),array(0, 115, 106),array(178, 210, 52),array(137, 91, 74),array(82, 56, 47)));
		$da['data'] = $this->dVars[$ex];
		$da['key'] = $this->iVars[$ex];

		foreach ($da['data'] as $i => $e){ //Allocate colors
			$rcolor[$i] = imagecolorallocate($this->img,$colorlist[$i][0],$colorlist[$i][1],$colorlist[$i][2]);
			$dcolor[$i] = imagecolorallocate($this->img,abs($colorlist[$i][0]-30),abs($colorlist[$i][1]-30),abs($colorlist[$i][2]-30));
		}

		$datasum = array_sum($da['data']);

		for($i=0;$i<count($da['data']);$i++){
			$da['part'][$i] = $da['data'][$i] / $datasum; //Get percents
		}

		if (($fd=array_sum($da['part']))<1) {
			$da['part'][count($da['part'])-1] += 1-$fd;
		}

		for($i=0;$i<count($da['data']);$i++) {
			$da['angle'][$i] = $da['part'][$i] * 360; //Get angles
		}

		for($i=0;$i<count($da['data']);$i++) {
			@$da['ansum'][$i] = array_sum(array_slice($da['angle'],0,$i+1)); //Get sums from 0 to each angle
		}

		for($i=1;$i<count($da['ansum'])+1;$i++) {
			$sortkeys[] = $da['ansum'][$i-1]; //Create sort array to make sure pie is graphed back to front
		}

		for($i=1;$i<count($sortkeys)+1;$i++) {
			if ($sortkeys[$i-1]<90 && $sortkeys[$i]>90) { // Make sure the one that actually crosses 90 is last
				$sortkeys[$i] = 90;
			}
		}

		uasort($sortkeys,array('graph','piesort'));

		$sk = array_keys($sortkeys);

		for($p=0;$p<=count($da['data'])-1;$p++) {
			$n = $sk[$p];
			$f = $n - 1;

			if ($da['angle'][$n] != 0) {
				for ($i = 0; $i < $ang; $i++) {
					if (($da['ansum'][$n]<=180 || $da['ansum'][$f]<=180) || ($n == count($da['data'])-1)) { //Draw 3d
						@imagefilledarc($this->img, $cx, $cy+$i, $w, $h, $da['ansum'][$f], $da['ansum'][$n], ($n==count($da['data'])-1?$rcolor[$n]:$dcolor[$n]), IMG_ARC_PIE);
					}
				}
			}
		}

		for($i=0;$i<=count($da['data'])-1;$i++) {
			$n = $i - 1;

			if ($da['angle'][$i] != 0) { //Draw top
				@imagefilledarc($this->img, $cx, $cy, $w, $h, $da['ansum'][$n], $da['ansum'][$i], $rcolor[$i], IMG_ARC_PIE);
			}
		}

		for($i=0;$i<count($da['data']);$i++) {
			@$da['ansum'][$i] = array_sum(array_slice($da['angle'],0,$i+1));
		}

		for($i=0;$i<count($da['data']);$i++) { //Draw keys
			$text  = ($this->getProp("useval",false)? $da['data'][$i] : round($da['part'][$i]*100,0).'%');

			$size = imagettfbbox($this->getProp("textsize",8),$this->getProp("textAngle",0),$this->getProp("font",realpath($this->font_name)),$text);

			if (!isset($da['ansum'][$i-1])) {
				$valuea = 0;
			} else {
				$valuea = $da['ansum'][$i-1];
			}

			if (!isset($da['ansum'][$i])) {
				$valueb = 0;
			} else {
				$valueb = $da['ansum'][$i];
			}

			$avang = ($valuea+$valueb)/2;

			imagettftext($this->img,
				$this->getProp("textsize",8),
				$this->getProp("textAngle",0),
				cos(deg2rad($avang))*$w/2+$cx+($avang<90||$avang>270?$this->getProp("numspace",5):-$size[2]-$this->getProp("numspace",5)),
				sin(deg2rad($avang))*$h/2+$cy+($avang<180&&$avang>0?-$size[5]+$ang:$size[5]),imagecolorallocate($this->img,0,0,0),
				$this->getProp("font",realpath($this->font_name)),
				$text);
		}
	}

	private function internalGraph() {
		if ($this->getProp("scale","numeric")=="date") {
			$start = $this->getProp("startdate",-9);
			$end = $this->getProp("enddate",time());

			if (is_string($start)) {
				$start = strtotime($start);
			}

			if (is_string($end)) {
				$end = strtotime($end);
			}
		}

		foreach ($this->iVars as $e => $g) {
			foreach ($g as $k => $i) {
				if (substr($this->iVars[$e][$k],0,2)=='d:') {
					$this->iVars[$e][$k] = strtotime(substr($this->iVars[$e][$k],2))/($end-$start);
				}
			}
		}

		for($i=0;$i<count($this->numPoints);$i++) {
			if ($this->getProp("reverse",0,$i)==1) {
				$this->dVars = array_reverse($this->dVars[$i]);
			}

			if ($this->getProp("flip",0,$i)==1) {
				$this->dVars = array_reverse($this->dVars[$i]);
				$this->iVars = array_reverse($this->iVars[$i]);
			}

			if ($this->getProp("sort",true,$i)==true) {
				$this->indSort($i);
			}
		}

		$time = microtime(true);
		$nu = ($this->getProp('type','line') != 'line'?1:2);

		for($k=0;$k<$this->lines;$k++) {
			if (isset($this->numPoints[$k])==false || $this->numPoints[$k]<$nu) {
				$this->error("Not enough points in dataset ".$k);
			}
		}

		if ($this->getProp("autoSize",true)) {
			if ($this->getProp("autoSizeX",true)) {
				$this->xMax = $this->multiMax($this->iVars);
				$this->xMin = $this->multiMin($this->iVars);
			}

			if ($this->getProp("autoSizeY",true)) {
				$this->yMax = $this->multiMax($this->dVars);
				$this->yMin = $this->multiMin($this->dVars);
			}
		}

		if ($this->getProp("type","line")=="bar" && $this->getProp("barnone",true)) {
			$this->yMin = 0;
			$this->setProp("xincpts",$this->numPoints[0]);
		}

		if ($this->getProp("autoScl",true)) {
			if ($this->getProp("autoSclX",true)) {
				round($this->setProp("xsclmax",$this->xMax),1);
				round($this->setProp("xsclmin",$this->xMin),1);
			}

			if ($this->getProp("autoSclY",true)) {
				round($this->setProp("ysclmax",$this->yMax),1);
				round($this->setProp("ysclmin",$this->yMin),1);
			}
		}

		@($this->setProp("xSclInc",(($this->getProp("xsclmax")-$this->getProp("xsclmin"))/(float)$this->getProp("xsclpts"))*$this->width/(float)($this->getProp("xsclmax")-$this->getProp("xsclmin"))));
		@($this->setProp("ySclInc",(($this->getProp("ysclmax")-$this->getProp("ysclmin"))/(float)$this->getProp("ysclpts"))*$this->height/(float)($this->getProp("ysclmax")-$this->getProp("ysclmin"))));

		$this->xPts = array();
		$this->yPts = array();

		foreach ($this->iVars as $e => $g) {
			foreach ($g as $k => $i) {
				$f = $this->xScale($i);
				$this->xPts[$e][] = $f;
			}
		}

		foreach ($this->dVars as $e => $g) {
			foreach ($g as $k => $i) {
				$f = $this->yScale($i);
				$this->yPts[$e][] = $f;
			}
		}

		$backcolor = $this->getProp("backcolor");
		$grids = $this->getProp("gridcolor",array(80,80,80));

		if ($this->getProp("showvertscale",true)) {
			for($i=0;$i<$this->getProp("yincpts")+1;$i++) { //Generate scale information for use in the graph image creation
				$vertScaleText[$i] = trim(round((($i/$this->getProp("yincpts"))*($this->yMax-$this->yMin))+$this->yMin,1));

				$vertScaleSize[$i] = imagettfbbox($this->getProp("textsize",8),
					$this->getProp("textAngle",0),
					$this->getProp("font",realpath($this->font_name)),
					$vertScaleText[$i]);

				$cSize[$i] = $vertScaleSize[$i][4];
			}

			$max = max($cSize);
			$this->setProp('actwidth',$this->width+10+$max);
		}

		$this->img = imagecreatetruecolor($this->getProp("actwidth"),$this->getProp("actheight")); // Create image.
		$back = imagecolorallocate($this->img,$backcolor[0],$backcolor[1],$backcolor[2]);
		$grid = imagecolorallocate($this->img,$grids[0],$grids[1],$grids[2]);

		imagefill($this->img,0,0,$back); //Fill with back color

		if ($this->getProp("showgrid",true)) {
			for($i=0;$i<round($this->getProp("sclline")*$this->getProp("onfreq"),0);$i++) { //Create grid line style
				$style[] = $grid;
			}

			for($i=0;$i<round($this->getProp("sclline")*(1-$this->getProp("onfreq")),0);$i++) {
				$style[] = IMG_COLOR_TRANSPARENT;
			}

			imagesetstyle($this->img, $style);

			if ($this->getProp("showxgrid",true)) {
				for($i=1;$i<$this->getProp("xsclpts");$i++) { //Create grid
					if ($this->mode=='image') {
						imageline($this->img, round($i*$this->getProp("xSclInc"),0), 0, round($i*$this->getProp("xSclInc"),0), $this->height, IMG_COLOR_STYLED);
					} else {
						$this->div->setColor($grids[0],$grids[1],$grids[2]);
						$this->div->setStyle('dotted');
						$this->div->drawLine(round($i*$this->getProp("xSclInc"),0),0,round($i*$this->getProp("xSclInc"),0),$this->height);
					}
				}
			}

			if ($this->getProp("showygrid",true)) {
				for($i=1;$i<$this->getProp("ysclpts");$i++) {
					if ($this->mode=='image') {
						imageline($this->img, 0, round($i*$this->getProp("ySclInc"),0), $this->width, round($i*$this->getProp("ySclInc"),0), IMG_COLOR_STYLED);
					} else {
						$this->div->drawLine(0,round($i*$this->getProp("ySclInc"),0),$this->width,round($i*$this->getProp("ySclInc"),0));
					}
				}
			}

			if ($this->mode=='image') {
				imageline($this->img, 0, $this->height-1, $this->width-1, $this->height-1, IMG_COLOR_STYLED); //Last lines
				imageline($this->img, $this->width-1, $this->height-1, $this->width-1, 0, IMG_COLOR_STYLED);
				imageline($this->img, 0, $this->height, 0, 0, IMG_COLOR_STYLED);
				imageline($this->img, 0, 0, $this->width, 0, IMG_COLOR_STYLED);
			} else {
				$this->div->drawLine(0,$this->height-1,$this->width-1,$this->height-1);
				$this->div->drawLine($this->width-1,$this->height-1,$this->width-1,0);
				$this->div->drawLine(0,$this->height,0,0);
				$this->div->drawLine(0,0,$this->width,0);
				$this->div->setStyle(1);
			}
		}

		if ($this->getProp("type","line")!="pie") {
			if ($this->getProp("showhorizscale",true)) {
				if ($this->getProp("scale","numeric")=="date") {
					$start = $this->getProp("startdate",-9);
					$end = $this->getProp("enddate",time());

					if (is_string($start)) {
						$start = strtotime($start);
					}

					if (is_string($end)) {
						$end = strtotime($end);
					}

					$start = getdate($start);
					$end = getdate($end);
					$dDate = ($end[0] - $start[0])/$this->getProp("xincpts");
					$showyr = $this->getProp("showyear",($start['year']!=$end['year']));
					$format = $this->getProp("dateformat",1);
				}elseif (is_array($this->getProp("scale","numeric"))) {
					$scale = $this->getProp("scale");
					$this->setProp("xincpts",count($scale)-1);
				}

				for($i=0;$i<$this->getProp("xincpts")+1;$i++) { //Create horiz scale
					if ($this->getProp("scale","numeric")=="numeric") {
						$text = ($this->getProp("type","line")=="bar"?$i:trim(round((($i/$this->getProp("xincpts"))*($this->xMax-$this->xMin))+$this->xMin,1)));
					}elseif ($this->getProp("scale","numeric")=="date") {
						$date = getdate($start[0]+($i*$dDate));

						if ($date['mday']>15 && $this->getProp('rounddateto',false)=='month') {
							$date = getdate($start[0]+(($i+0.5)*$dDate));
						}

						$text = ($format<=2?substr($date['month'],0,3):$date['month']);

						if ($showyr) {
							$text .= " ".substr($date['year'],($format==4 || $format==2?0:2),($format==4 || $format==2?4:2));
						}
					}elseif (is_array($this->getProp("scale","numeric"))) {
						$text = $scale[$i];
					} else {
						$this->error("Scale format not understood.");
					}

					$size = imagettfbbox($this->getProp("textsize",8),
						$this->getProp("textAngle",0),
						$this->getProp("font",realpath($this->font_name)),
						$text);

					if ($this->mode=="image") {
						imagettftext($this->img,$this->getProp("textsize",8),
							$this->getProp("textAngle",0),
							round(($i*($this->width/$this->getProp("xincpts")))-($i!=$this->getProp("xincpts")?($i!=0?(.5*$size[2]):0):$size[2]),0)-($this->getProp("type","line")=="bar"?.5*($this->width/count($this->xPts[0])):0),
							$this->height+abs($size[5])+3,
							$grid,
							$this->getProp("font",realpath($this->font_name)),
							$text);
					} else {
						$this->div->setFontSize(8);
						$this->div->drawString(round(($i*($this->width/$this->getProp("xincpts")))-($i!=$this->getProp("xincpts")?($i!=0?(.5*$size[2]):0):$size[2]),0)-($this->getProp("type","line")=="bar"?.5*($this->width/count($this->xPts[0])):0),$this->height,$text);
					}
				}
			}
			if ($this->getProp("showvertscale",true)) {
				for($i=0;$i<$this->getProp("yincpts")+1;$i++) { //Create vert scale
					$text = $vertScaleText[$i];
					$size = $vertScaleSize[$i];

					if ($this->mode=="image") {
						imagettftext($this->img,
							$this->getProp("textsize",8),
							$this->getProp("textAngle",0),
							$this->width+3,round((($this->getProp("yincpts")-$i)*($this->height/$this->getProp("yincpts")))-($text!=$this->yMax?($text!=$this->yMin?(.5*$size[5]):0):$size[5]),0),
							$grid,
							$this->getProp("font",realpath($this->font_name)),
							$text);
					} else {
						$this->div->setFontSize(8);
						$this->div->drawString($this->width+3,
							round((($this->getProp("yincpts")-$i)*($this->height/$this->getProp("yincpts")))-($i==0?abs($size[5]):0),0),
							$text);
					}
				}
			}
		}

		foreach ($this->xPts as $ex => $ind) {
			$xPts = $this->xPts[$ex];
			$yPts = $this->yPts[$ex];
			$g = $this->width/count($xPts);
			$color = $this->getProp("color",array(0,0,255,0),$ex);

			$fore = imagecolorallocatealpha($this->img,$color[0],$color[1],$color[2],$color[3]);

			if ($this->getProp("type","line")=="line") { //Draw graph
				for($i=1;$i<$this->numPoints[$ex];$i++) {
					$this->imageSmoothAlphaLine($this->img,$xPts[$i-1],$this->height-$yPts[$i-1],$xPts[$i],$this->height-$yPts[$i],$color[0],$color[1],$color[2],$color[3]);
				}
			}elseif ($this->getProp("type","line")=="bar") {
				for($i=0;$i<$this->numPoints[$ex];$i++) {
					$this->drawBar($g*$i+($i==0?$g*(1-$this->getProp("barwidth",1)):0),$this->height-$yPts[$i],$g*$i+$this->getProp("barwidth",1)*$g,$this->height-1,$i);
				}
			}elseif ($this->getProp("type","line")=="pie"){
				$this->drawPie($ex);
			}elseif ($this->getProp("type","line")!="dot") {
				$this->error("Type property not understood.",E_USER_ERROR);
			}

			$width  = $this->getProp("pointwidth",5,$ex);
			$height = $this->getProp("pointheight",$width,$ex);
			$point  = $this->getProp("pointcolor",$color,$ex);

			$col = imagecolorallocatealpha($this->img,$point[0],$point[1],$point[2],$point[3]);

			if ($this->getProp("pointstyle",0,$ex)!=0) {
				foreach ($xPts as $k => $xpt) {
					$x = $xPts[$k];
					$y = $this->height-$yPts[$k];
					if ($this->getProp("endstyle",0,$ex)!=0 && $k==count($xPts)-1) {
						continue;
					}

					switch ($this->getProp("pointstyle",0,$ex)) { //Draw points
					case 1: //Filled rectangle
						imagefilledrectangle($this->img,$x-.5*$width,$y-.5*$height,$x+.5*$width,$y+.5*$height,$col);

						break;
					case 2: //Open rectangle
						if (!$this->getProp("clearback",false,$ex)) {
							imagefilledrectangle($this->img,$x-.5*$width,$y-.5*$height,$x+.5*$width,$y+.5*$height,$back);
						}

						imagerectangle($this->img,$x-.5*$width,$y-.5*$height,$x+.5*$width,$y+.5*$height,$col);

						break;
					case 3: //Filled Triangle
						imagefilledpolygon($this->img,array($x-.5*$width,$y+.5*$height,$x+.5*$width,$y+.5*$height,$x,$y-.5*$height),3,$col);

						break;
					case 4: //Open Triangle
						if (!$this->getProp("clearback",false,$ex)) {
							imagefilledpolygon($this->img,array($x-.5*$width,$y+.5*$height,$x+.5*$width,$y+.5*$height,$x,$y-.5*$height),3,$back);
						}

						imagepolygon($this->img,array($x-.5*$width,$y+.5*$height,$x+.5*$width,$y+.5*$height,$x,$y-.5*$height),3,$col);

						break;
					case 5: //Filled n-gon, for testing only!
						$n = $this->getProp("pointsides",7);

						if ($n<7) {
							$this->error("Point shape must be 7 or more sides!");
						}elseif ($n>30){
							$this->error("Just use a circle ;)");
						}

						$s = $width;
						unset($points);

						for($i=1;$i<$n+1;$i++) {
							$o=($i)*(360/$n);
							$points[] = ($s*cos($o))+$x;
							$points[] = ($s*sin($o))+$y;
						}

						imagefilledpolygon($this->img,$points,$n,$col);

						break;
					case 6: //Open n-gon, for testing only!
						$n = $this->getProp("pointsides",7,$ex);

						if ($n<7) {
							$this->error("Point shape must be 7 or more sides!");
						}elseif ($n>30) {
							$this->error("Just use a circle ;)");
						}

						$s = $width;
						unset($points);

						for($i=1;$i<$n+1;$i++) {
							$o=($i)*(360/$n);
							$points[] = ($s*cos($o))+$x;
							$points[] = ($s*sin($o))+$y;
						}

						if (!$this->getProp("clearback",false,$ex)) {
							imagefilledpolygon($this->img,$points,$n,$back);
						}

						imagepolygon($this->img,$points,$n,$col);

						break;
					case 7:	//Filled ellipse, make width = height for a circle
						imagefilledellipse($this->img,$x,$y,$width,$height,$col);

						break;
					case 8: //Open ellipse
						if (!$this->getProp("clearback",false,$ex)) {
							imagefilledellipse($this->img,$x,$y,$width,$height,$back);
						}

						imageellipse($this->img,$x,$y,$width,$height,$col);

						break;
					case 9: //Image
						if ($this->getProp("pointimgsrc",false,$ex)===false) {
							$this->error("You must set the pointimgsrc property before using the image point style",true);
						}

						if (!isset($this->imgCache)) {
							$im = $this->loadimg($this->getProp("pointimgsrc",false,$ex));

							if ($this->getProp("pointimgscale",false,$ex)) {
								$height = round($width*(ImageSX($im)/ImageSY($im)),0);
							}

							$tmp = imagecreatetruecolor($width,$height);

							if (!imagecopyresized($tmp,$im,0,0,0,0,$width,$height,ImageSX($im),ImageSY($im))) {
								$this->error("Error resizing point image");
							}

							$this->cacheImg($tmp);
						}

						$tmp = $this->cacheImg();

						if (!imagecopy($this->img,$tmp,$x-.5*$width,$y-.5*$height,0,0,$width,$height)) {
							$this->error("Error inserting point image");
						}

						break;
					}
				}

				unset($this->imgCache);
			}

			$arrow = $this->getProp("arrowcolor",$color,$ex);

			$col = imagecolorallocatealpha($this->img,$arrow[0],$arrow[1],$arrow[2],$arrow[3]);

			$x2 = $xPts[count($xPts)-1];
			$y2 = $this->height-$yPts[count($yPts)-1];
			$x1 = $xPts[count($xPts)-2];
			$y1 = $this->height-$yPts[count($yPts)-2];

			switch ($this->getProp("endstyle",0,$ex)){ //Draw end
			case 1: //Open arrow
				$arrhead = $this->getProp("arrowwidth",25,$ex);
				$arrang = $this->getProp("arrowangle",14,$ex);
				$arrow = $this->drawArrowheads ($x1, $y1, $x2, $y2, $arrhead, $arrang);

				if (!$this->getProp("clearback",false,$ex)) {
					imagefilledpolygon($this->img,array (
						$arrow['x1'], $arrow['y1'],
						$arrow['x2'], $arrow['y2'],
						$x2, $y2
					),3,$back);
				}

				imageline($this->img, $arrow['x1'], $arrow['y1'], $arrow['x2'], $arrow['y2'], $col);
				imageline($this->img, $x2, $y2, $arrow['x1'], $arrow['y1'], $col);
				imageline($this->img, $x2, $y2, $arrow['x2'], $arrow['y2'], $col);

				break;
			case 2: //Filled arrow
				$arrhead = $this->getProp("arrowwidth",25,$ex);
				$arrang = $this->getProp("arrowangle",14,$ex);
				$arrow = $this->drawArrowheads ($x1, $y1, $x2, $y2, $arrhead, $arrang);

				imagefilledpolygon($this->img,array (
					$arrow['x1'], $arrow['y1'],
					$arrow['x2'], $arrow['y2'],
					$x2, $y2
				),3,$col);

				break;
			}
		}

		$sX = ImageSX($this->img);
		$sY = ImageSY($this->img);

		$font   = $this->getProp("font", realpath($this->font_name));
		$labels = $this->getProp("labelcolor", array(0,0,0,0));

		$label  = imagecolorallocatealpha($this->img, $labels[0], $labels[1], $labels[2], $labels[3]);

		$fX = $sX;
		$fY = $sY;
		$tB = 0;

		if ($this->getProp("title",false)!==false) {
			$title = $this->getProp("title");
			$tsize = imagettfbbox($this->getProp("titlesize",24),0,$font,$title);
			$tB = abs($tsize[5])+10;
			$fY += $tB + 5;
		}

		if ($this->getProp("xlabel",false)!==false || $this->getProp("xllabel",false)!==false || $this->getProp("xrlabel",false)!==false) {
			
			if($this->getProp("xlabel",false)!==false){
				$xlabel = $this->getProp("xlabel");
				$xsize = imagettfbbox($this->getProp("labelsize",14),0,$font,$xlabel);
			}
			
			if($this->getProp("xllabel",false)!==false){
				$xllabel = $this->getProp("xllabel");
				$xlsize = imagettfbbox($this->getProp("xllabelsize",10),0,$font,$xllabel);
			}
			
			if($this->getProp("xrlabel",false)!==false){
				$xrlabel = $this->getProp("xrlabel");
				$xrsize = imagettfbbox($this->getProp("xrlabelsize",10),0,$font,$xrlabel);
			}
			
			@$fY += max(array(abs($xsize[5]),abs($xlsize[5]),abs($xrsize[5])))+5+$this->getProp('xlabeloffset',5);
		}

		if ($this->getProp("ylabel",false)!==false) {
			$ylabel = $this->getProp("ylabel");
			$ysize = imagettfbbox($this->getProp("labelsize",14),90,$font,$ylabel);
			$fX += abs($ysize[2])+15;
		}

		if(!isset($xsize)) $xsize[5] = 30;
		
		if ($fX != $sX || $fY != $sY) {
			$timg = imagecreatetruecolor($fX,$fY);
			imagecopy($timg,$this->img,0,$tB,0,0,$sX,$sY);
			imagefill($timg,0,0,$back);

			if ($this->getProp("ylabel",false)!==false) {
				imagettftext($timg,$this->getProp("labelsize",14),90,$sX+10,round(($fY-$ysize[5])/2,0),$label,$font,$ylabel);
			}

			if ($this->getProp("xlabel",false)!==false) {
				imagettftext($timg,$this->getProp("labelsize",14),0,round(($fX-abs($xsize[2]))/2,0),$sY+$tB+$this->getProp('xlabeloffset',5),$label,$font,$xlabel);
			}
			
			if ($this->getProp("xllabel",false)!==false) {
				imagettftext($timg,$this->getProp("xllabelsize",10),0,$this->getProp('xrlabeloffset',10),floor($sY+$tB+(abs($xsize[5])-abs($xlsize[5]))/2)+$this->getProp('xlabeloffset',5),$label,$font,$xllabel);
			}
			
			if ($this->getProp("xrlabel",false)!==false) {
				imagettftext($timg,$this->getProp("xrlabelsize",10),0,round(($fX-abs($xrsize[2])-20),0)+$this->getProp('xllabeloffset',0),floor($sY+$tB+(abs($xsize[5])-abs($xrsize[5]))/2)+$this->getProp('xlabeloffset',5),$label,$font,$xrlabel);
			}

			if ($this->getProp("title",false)!==false) {
				imagettftext($timg,$this->getProp("titlesize",24),0,round(($fX-abs($tsize[2]))/2,0),abs($tsize[5]),$label,$font,$title);
			}

			$this->img = $timg;
		}

		if ($this->getProp("benchmark",false)) {
			echo (round(microtime(true) - $time,3)*1000)."ms";
		}

		if ($this->getProp("showkey",false)) {
			$acthei = $this->getProp("actheight",$this->height);
			$actwid = $this->getProp("actwidth",$this->width);
			$size   = $this->getProp("keysize", 10);
			$font   = $this->getProp("keyfont", realpath($this->keyfont_name));

			$indhei = 0;
			$indwid = 0;
			$type = $this->getProp("type",'line');

			if ($type!='line') {
				$keys = $this->getProp('key',false);
				$num  = $this->numPoints[0];
				$dis  = $this->getProp("keyinfo",0);

				if ($dis>3 || $dis<0) {
					$this->error("keyinfo not understood.");
				}

				if ($dis!=0) {
					$d = $this->dVars[0];
					$g = array_sum($d);

					foreach ($d as $i => $e) {
						$pr[] = ($e/$g)*100;
					}

					if (($fd=array_sum($pr))<1) {
						$pr[count($pr)-1] += 1-$fd;
					}

					foreach ($d as $i => $e) {
						$p[] = round($pr[$i],0).'%';
					}

					if ($dis==1 || $dis==3) {
						foreach ($keys as $i => $k) {
							$keys[$i] = $p[$i].' '.$k;
						}
					}

					if ($dis==2 || $dis==3) {
						foreach ($keys as $i => $k) {
							$keys[$i] = $d[$i].' '.$k;
						}
					}
				}
			} else {
				$num = $this->lines;
			}

			for($i=0;$i<$num;$i++) {
				if ($type=='line') {
					$keys[$i] = $this->getProp("key",false,$i);

					if ($keys[$i]==false) {
						$this->error("You must set the keys property for dataset $i.");
					}
				}

				$ksize[$i]['size'] = imagettfbbox($size, 0, $font, $keys[$i]);
				$indhei = max($indhei,$ksize[$i]['height'] = abs($ksize[$i]['size'][5]));
				$indwid = max($indwid,$ksize[$i]['width'] = abs($ksize[$i]['size'][2])+$indhei+5);
			}

			$indhei += 4;
			$indwid += 10;
			$wspc    = $this->getProp("keywidspc",10);
			$oldwid  = $actwid;
			$oldhei  = $acthei;
			if($num<12)
				$khei    = $indhei*$num+14;
			else
				$khei    = $indhei*($num+1)+14;
			$actwid  = $actwid+$indwid+$wspc;
			$acthei  = max($acthei,$khei);

			$timg = imagecreatetruecolor($actwid,$acthei);
			$backcolor = $this->getProp("backcolor");

			$back = imagecolorallocate($this->img,$backcolor[0],$backcolor[1],$backcolor[2]);

			imagefill($timg,0,0,$back);
			imagecopy($timg,$this->img,0,0,0,0,$oldwid,$oldhei);

			$this->img = $timg;

			$y = ($acthei-$khei)/2;
			$x = $oldwid+$wspc+4;

			imagerectangle($this->img,$x-4,$y,$actwid-1,$khei+($acthei-$khei)/2+4,imagecolorallocate($this->img,0,0,0));

			$black = imagecolorallocate($this->img,0,0,0);

			if ($this->getProp('type','line')=='bar') {
				$colorlist = $this->getProp("colorlist",false);

				if ($colorlist === true || $colorlist === 1 || $colorlist === "true") {
					$colorlist = array(array(255, 203, 3),array(220, 101, 29),array(189, 24, 51),array(214, 0, 127),array(98, 1, 96),array(0, 62, 136),array(0, 102, 179),array(0, 145, 195),array(0, 115, 106),array(178, 210, 52),array(137, 91, 74),array(82, 56, 47));
				}

				if ($colorlist) {
					$col = $colorlist[$i-1];
				} else {
					$this->error("colorlist must be set for keys to work.",E_USER_ERROR);
				}
			} else {
				$colorlist = $this->getProp('colorlist',array(array(125, 203, 3),array(220, 101, 29),array(189, 24, 51),array(34, 78, 120),array(120, 1, 60),array(0, 62, 136),array(0, 102, 179),array(0, 145, 195),array(0, 115, 106),array(178, 210, 52),array(137, 91, 74),array(82, 56, 47)));
			}

			foreach ($keys as $i => $k) {
		 		if ($type=='line') {
					$col = $this->getProp("color",array(0,0,255,0),$i);
				} else {
					$col = $colorlist[$i];
				}

				if (isset($col[3])) {
					$alpha = $col[3];
				} else {
					$alpha = 0;
				}

		 		$fco = imagecolorallocatealpha($this->img, $col[0], $col[1], $col[2], $alpha);

		 		imagefilledrectangle($this->img,$x,$y+=4,$x+$indhei-4,$y+=$indhei-2,$fco);

		 		$y-=$indhei-2+4;

		 		imagerectangle($this->img,$x,$y+=4,$x+$indhei-4,$y+=$indhei-2,$black);
		 		imagettftext($this->img,$size,0,$x+$indhei,$y-1,$black,$font,$k);
		 	}
		}

		$this->error(true);

		return true;
	}

	private function loadimg($file) {
		if (substr($file,-4)==".png") {
			$im = imagecreatefrompng($file);
		}elseif (substr($file,-4)==".gif") {
			$im = imagecreatefromgif ($file);
		}elseif (substr($file,-4)==".jpg" || substr($file,-5)==".jpeg") {
			$im = imagecreatefromjpeg($file);
		}elseif (substr($file,-4)==".bmp") {
			$im = imagecreatefromwbmp($file);
		} else {
			$this->error("Image format not understood",true);
		}

		return $im;
	}

	private function imageSmoothAlphaLine ($image, $x1, $y1, $x2, $y2, $r, $g, $b, $alpha=0) { //Thanks php.net poster
		if ($this->mode=='image') {
			if ($x2==$x1) {
				$x2++;
			}

			if ($y2==$y1) {
				$y2++;
			}

			$icr = $r;$icg = $g;$icb = $b;$dcol = imagecolorallocatealpha($image, $icr, $icg, $icb, $alpha);$m = ($y2 - $y1) / ($x2 - $x1);$b = $y1 - $m * $x1;

			if (abs ($m) <2) {
				$x = min($x1, $x2);$endx = max($x1, $x2) + 1;
				while ($x < $endx) {
					$y = $m * $x + $b;$ya = ($y == floor($y) ? 1: $y - floor($y));$yb = ceil($y) - $y;$trgb = ImageColorAt($image, $x, floor(abs($y)));$tcr = ($trgb >> 16) & 0xFF;$tcg = ($trgb >> 8) & 0xFF;$tcb = $trgb & 0xFF;imagesetpixel($image, $x, floor($y), imagecolorallocatealpha($image, ($tcr * $ya + $icr * $yb), ($tcg * $ya + $icg * $yb), ($tcb * $ya + $icb * $yb), $alpha));$trgb = ImageColorAt($image, $x, ceil(abs($y)));$tcr = ($trgb >> 16) & 0xFF;$tcg = ($trgb >> 8) & 0xFF;$tcb = $trgb & 0xFF;imagesetpixel($image, $x, ceil($y), imagecolorallocatealpha($image, ($tcr * $yb + $icr * $ya), ($tcg * $yb + $icg * $ya), ($tcb * $yb + $icb * $ya), $alpha));$x++;
				}
			} else {
				$y = min($y1, $y2);$endy = max($y1, $y2) + 1;

				while ($y < $endy) {
					@$x = ($y - $b) / $m;
					$xa = ($x == floor($x) ? 1: $x - floor($x));$xb = ceil($x) - $x;$trgb = ImageColorAt($image, floor(abs($x)), $y);$tcr = ($trgb >> 16) & 0xFF;$tcg = ($trgb >> 8) & 0xFF;$tcb = $trgb & 0xFF;

					imagesetpixel($image, floor($x), $y, imagecolorallocatealpha($image, ($tcr * $xa + $icr * $xb), ($tcg * $xa + $icg * $xb), ($tcb * $xa + $icb * $xb), $alpha));$trgb = ImageColorAt($image, ceil(abs($x)), $y);$tcr = ($trgb >> 16) & 0xFF;$tcg = ($trgb >> 8) & 0xFF;$tcb = $trgb & 0xFF;imagesetpixel ($image, ceil(abs($x)), $y, imagecolorallocatealpha($image, ($tcr * $xb + $icr * $xa), ($tcg * $xb + $icg * $xa), ($tcb * $xb + $icb * $xa), $alpha));$y++;
				}
			}
		} else {
			$this->div->setColor($r,$g,$b);
			$this->div->drawLine($x1,$y1,$x2,$y2);
		}
	}

	private function drawArrowheads ($x1, $y1, $x2, $y2, $arrhead, $arrang) { //Thanks php.net poster (tried to do the math myself, not fun :()
		$debug = false;

		if (($x2-$x1)==0) {
			if ($y1 == 0) {
				$slope = 0;
			} else {
				$slope = 'INFINITE';
			}
		} else {
			$slope = -($y2-$y1)/($x2-$x1);
		}

		if ($debug) {
			echo ("Values of xy.. before add/sub</br>");
			echo ("$x1, $y1   $x2, $y2</br>");
		}

		if ($slope == 'INFINITE') {
			$ang = 90;
		} else {
			$ang = atan ($slope);
			$ang = ($ang * 180)/pi();
		}

		$arrang1 = ($ang - $arrang);
		$arrang1 = ($arrang1*pi())/180;
		$arrang2 = ($ang + $arrang);
		$arrang2 = ($arrang2*pi())/180;

		$arx1 = (floor(cos($arrang1)*$arrhead));
		$ary1 = (floor(sin($arrang1)*$arrhead));
		$arx2 = (floor(cos($arrang2)*$arrhead));
		$ary2 = (floor(sin($arrang2)*$arrhead));

		if ($ang==0) {
			if ($x2>$x1) {
				$arx1=$x2-$arx1;$ary1=$y2-$ary1;$arx2=$x2-$arx2;$ary2=$y2-$ary2;
			}elseif ($x2<$x1) {
				$arx1=$x2+$arx1;$ary1=$y2-$ary1;$arx2=$x2+$arx2;$ary2=$y2-$ary2;
			}
		}

		if ($ang>0&&$ang<90) {
			if (($x2>$x1)&&($y2<$y1)) {
				$arx1=$x2-$arx1;$ary1=$y2+$ary1;$arx2=$x2-$arx2;$ary2=$y2+$ary2;
			}elseif (($x2<$x1)&&($y2>$y1)) {
				$arx1=$x2+$arx1;$ary1=$y2-$ary1;$arx2=$x2+$arx2;$ary2=$y2-$ary2;
			}
		}

		if ($ang==90) {
			if (($y2>$y1)) {
				$arx1=$x2-$arx1;$ary1=$y2-$ary1;$arx2=$x2-$arx2;$ary2=$y2-$ary2;
			}elseif (($y2<$y1)) {
				$arx1=$x2-$arx1;$ary1=$y2+$ary1;$arx2=$x2-$arx2;$ary2=$y2+$ary2;
			}
		}

		if ($ang>-90&&$ang<0) {
			if (($x2>$x1)&&($y2>$y1)) {
				$arx1=$x2-$arx1;$ary1=$y2+$ary1;$arx2=$x2-$arx2;$ary2=$y2+$ary2;
			}elseif (($x2<$x1)&&($y2<$y1)) {
				$arx1=$x2+$arx1;$ary1=$y2-$ary1;$arx2=$x2+$arx2;$ary2=$y2-$ary2;
			}
		}

		$array_arrows=array('x1'=>$arx1,'y1'=>$ary1,'x2'=>$arx2,'y2'=>$ary2);

		return $array_arrows;
	}

	public function showGraph($show = true) {
		if ($this->mode=='image') {
			if ($show === true) {
				if (!$this->getProp("noheader",false)) {
					header('Content-type: image/'.$this->getProp('imagetype','png'));
				}

				switch ($this->getProp("imagetype","png")){
				case 'png':
					imagepng($this->img);

					break;
				case 'jpeg':
					imagejpeg($this->img);

					break;
				case 'gif':
					imagegif ($this->img);

					break;
				default:
					$this->error('imagetype not understood.');
				}

				return true;
			}elseif ($show == "random" || $show === false) {
				$name = $this->getProp("imagepre","")."graphImage".($show=="random"?rand(1000000,10000000):"").'.'.$this->getProp('imagetype','png');

				switch ($this->getProp("imagetype","png")) {
				case 'png':
					imagepng($this->img,$name);

					break;
				case 'jpeg':
					imagejpeg($this->img,$name);

					break;
				case 'gif':
					imagegif ($this->img,$name);

					break;
				default:
					$this->error('imagetype not understood.');
				}

				return $name;
			} else {
				switch ($this->getProp("imagetype","png")){
				case 'png':
					imagepng($this->img,$show);

					break;
				case 'jpeg':
					imagejpeg($this->img,$show);

					break;
				case 'gif':
					imagegif ($this->img,$show);

					break;
				default:
					$this->error('imagetype not understood.');
				}

				return $show;
			}
		} else {
			$this->div->draw();
		}

		return true;
	}

	private static function trimfilter($a) {
		if (trim($a)=="") {
			return false;
		} else {
			return true;
		}
	}

	public static function createGraph($data,$w=400,$h=200,$prop="",$random=false) {
		self::$numGraphs++;

		$graph = new graph($w,$h);

		if (is_array($prop)) {
			$graph->storePropArr($prop);
		} else {
			$graph->setBulkProps($prop);
		}

		$data = explode(":",$data);
		$dt = $data[1];

		if (strtoupper($data[0])=="XML"){
			$graph->importXML($dt,(isset($data[2])?$data[2]:"i"),(isset($data[3])?$data[3]:"d"),(isset($data[4])?$data[4]:""),(isset($data[5])?$data[5]:0));
		}elseif (strtoupper($data[0])=="CSV") {
			$graph->importCSV($dt,(isset($data[2])?$data[2]:"d,i"),(isset($data[3])?$data[3]:0));
		}elseif (strtoupper($data[0])=="RAW") {
			$graph->addBulkPoints($dt,';');
		}elseif (strtoupper($data[0])=="FUN") {
			$graph->graphFunction($dt,(isset($data[2])?$data[2]:-1),(isset($data[2])?$data[2]:1));
		} else {
			$graph->error("Inline data format not understood. Read the readme.");
		}

		$graph->graph();
		$name = $graph->getProp("tempfolder","")."graph".substr(basename($_SERVER['PHP_SELF']),0,3).self::$numGraphs.($random==true?rand(1,100000):"").'.png';
		$graph->showGraph($name);

		if ($random && $graph->getProp("delold",true)) {
			$fname = $graph->getProp("tempfolder","")."imagehistory".basename($_SERVER['PHP_SELF']).".data";
			$f = @file($fname);

			if (count($f)>$graph->getProp("cachehistory",5)) {
				unlink($f[0]);
				unset($f[0]);
			}

			$f[]=$name;

			file_put_contents($fname,join(array_filter($f,self::trimfilter),"\n"));
		}

		unset($graph);

		return $name;
	}
}

class jgwrap { //Wrapper class for Walter Zorn's Vector Graphics Library, http://www.walterzorn.com
	public $str, $cc, $cs, $font, $size, $div, $num;

	public function __construct($jgo='', $font='DejaVuSans', $nu=0){
		$this->num = $nu;

		define('jgw','jg');

		$this->div = $jgo;

		echo '<script type="text/javascript" src="wz_jsgraphics.js"></script>';

		$this->str = "function divDraw".($this->num)."(){";
		$this->addDiv('setFont("'.$font.'", "12pt", Font.PLAIN)');
		$this->cc = NULL;
		$this->font = 'DejaVuSans';
		$this->size = 12;
	}

	private function addDiv($add){
		$this->str .= jgw.'.'.$add . ";";
	}

	public function setColor($r,$g,$b){
		$col = array($r,$g,$b);

		if ($this->cc!=$col) {
			$this->addDiv('setColor("#'.sprintf('%0-2X%0-2X%0-2X',$col[0],$col[1],$col[2]).'")');
		}

		$this->cc = $col;
	}

	public function setStyle($str){
		if ($this->cs!=$str) {
			if ($str=='dotted') {
				$this->addDiv('setStroke(Stroke.DOTTED)');
			} else {
				$this->addDiv('setStroke('.$str.')');
			}
		}

		$this->cs = $str;
	}

	public function drawLine($x1,$y1,$x2,$y2) {
		$this->addDiv('drawLine('.sprintf('%d,%d,%d,%d',$x1,$y1,$x2,$y2).')');
	}

	public function drawEllipse($x,$y,$w,$h) {
		$this->addDiv('drawEllipse('.sprintf('%d,%d,%d,%d',$x,$y,$w,$h).')');
	}

	public function drawRect($x,$y,$w,$h) {
		$this->addDiv('drawRect('.sprintf('%d,%d,%d,%d',$x,$y,$x+$w,$y+$h).')');
	}

	public function drawFilledEllipse($x,$y,$w,$h) {
		$this->addDiv('fillEllipse('.sprintf('%d,%d,%d,%d',$x,$y,$w,$h).')');
	}

	public function drawFilledRect($x,$y,$w,$h) {
		$this->addDiv('fillRect('.sprintf('%d,%d,%d,%d',$x,$y,$x+$w,$y+$h).')');
	}

	public function drawString($x,$y,$s) {
		$this->addDiv('drawString("'.$s.'",'.sprintf('%d,%d',$x,$y).')');
	}

	public function setFontSize($s) {
		if ($this->size!=$s) {
			$this->addDiv('setFont("'.$this->font.'","'.$this->size.'px",Font.PLAIN)');
		}

		$this->size = $s;
	}

	public function draw() {
		echo '<script>';
		$this->addDiv('paint()');
		echo $this->str;
		echo "} \n";
		echo "var jg = new jsGraphics('$this->div');\n";
		echo "divDraw".($this->num)."()";
		echo '</script>';
	}
}

class EvalMath { // By Miles Kaufmann
	private $suppress_errors = false;
	private $last_error = null;
	private $v = array('e'=>2.71,'pi'=>3.14);
	private $f = array();
	private $vb = array('e', 'pi');

	private $fb = array(
		'sin','sinh','arcsin','asin','arcsinh','asinh',
		'cos','cosh','arccos','acos','arccosh','acosh',
		'tan','tanh','arctan','atan','arctanh','atanh',
		'sqrt','abs','ln','log','pow');

	function EvalMath() {
		$this->v['pi'] = pi();
		$this->v['e'] = exp(1);
	}

	function e($expr) {
		return $this->evaluate($expr);
	}

	function evaluate($expr) {
		$this->last_error = null;
		$expr = trim($expr);

		if (substr($expr, -1, 1) == ';') {
			$expr = substr($expr, 0, strlen($expr)-1);
		}

		if (preg_match('/^\s*([a-z]\w*)\s*=\s*(.+)$/', $expr, $matches)) {
			if (in_array($matches[1], $this->vb)) {
				return $this->trigger("cannot assign to constant '$matches[1]'");
			}

			if (($tmp = $this->pfx($this->nfx($matches[2]))) === false) {
				return false;
			}

			$this->v[$matches[1]] = $tmp;

			return $this->v[$matches[1]];
		}elseif (preg_match('/^\s*([a-z]\w*)\s*\(\s*([a-z]\w*(?:\s*,\s*[a-z]\w*)*)\s*\)\s*=\s*(.+)$/', $expr, $matches)) {
			$fnn = $matches[1];

			if (in_array($matches[1], $this->fb)) {
				return $this->trigger("cannot redefine built-in function '$matches[1]()'");
			}

			$args = explode(",", preg_replace("/\s+/", "", $matches[2]));

			if (($stack = $this->nfx($matches[3])) === false) {
				return false;
			}

			for ($i = 0; $i<count($stack); $i++) {
				$token = $stack[$i];

				if (preg_match('/^[a-z]\w*$/', $token) and !in_array($token, $args)) {
					if (array_key_exists($token, $this->v)) {
						$stack[$i] = $this->v[$token];
					} else {
						return $this->trigger("undefined variable '$token' in function definition");
					}
				}
			}

			$this->f[$fnn] = array('args'=>$args, 'func'=>$stack, 'def'=>$matches[3]);

			return true;
		} else {
			return $this->pfx($this->nfx($expr));
		}
	}

	function vars() {
		$output = $this->v;
		unset($output['pi']);
		unset($output['e']);

		return $output;
	}

	function funcs() {
		$output = array();
		foreach ($this->f as $fnn=>$dat) {
			$output[$fnn . '(' . implode(',', $dat['args']) . ')'] = $dat['def'];
		}

		return $output;
	}

	function nfx($expr) {
		$index  = 0;
		$stack  = new EvalMathStack;
		$output = array();
		$expr   = trim(strtolower($expr));
		$ops    = array('+', '-', '*', '/', '^', '_');
		$ops_r  = array('+'=>0,'-'=>0,'*'=>0,'/'=>0,'^'=>1);
		$ops_p  = array('+'=>0,'-'=>0,'*'=>1,'/'=>1,'_'=>1,'^'=>2);

		$expecting_op = false;

		if (preg_match("/[^\w\s+*^\/()\.,-]/", $expr, $matches)) {
			return $this->trigger("illegal character '{$matches[0]}'");
		}

		while (1) {
			$op = substr($expr, $index, 1);
			$ex = preg_match('/^([a-z]\w*\(?|\d+(?:\.\d*)?|\.\d+|\()/', substr($expr, $index), $match);

			if ($op == '-' and !$expecting_op) {
				$stack->push('_');
				$index++;
			}elseif ($op == '_') {
				return $this->trigger("illegal character '_'");
			}elseif ((in_array($op, $ops) or $ex) and $expecting_op) {
				if ($ex) {
					$op = '*'; $index--;
				}

				while ($stack->count > 0 and ($o2 = $stack->last()) and in_array($o2, $ops) and ($ops_r[$op] ? $ops_p[$op] < $ops_p[$o2] : $ops_p[$op] <= $ops_p[$o2])) {
					$output[] = $stack->pop();
				}

				$stack->push($op);
				$index++;
				$expecting_op = false;
			}elseif ($op == ')' and $expecting_op) {
				while (($o2 = $stack->pop()) != '(') {
					if (is_null($o2)) {
						return $this->trigger("unexpected ')'");
					} else {
						$output[] = $o2;
					}
				}

				if (preg_match("/^([a-z]\w*)\($/", $stack->last(2), $matches)) {
					$fnn = $matches[1];
					$arg_count = $stack->pop();
					$output[] = $stack->pop();

					if (in_array($fnn, $this->fb)) {
						if ($arg_count > 1) {
							return $this->trigger("too many arguments ($arg_count given, 1 expected)");
						}
					}elseif (array_key_exists($fnn, $this->f)) {
						if ($arg_count != count($this->f[$fnn]['args'])) {
							return $this->trigger("wrong number of arguments ($arg_count given, " . count($this->f[$fnn]['args']) . " expected)");
						}
					} else {
						return $this->trigger("internal error");
					}
				}

				$index++;
			}elseif ($op == ',' and $expecting_op) {
				while (($o2 = $stack->pop()) != '(') {
					if (is_null($o2)) {
						return $this->trigger("unexpected ','");
					} else {
						$output[] = $o2;
					}
				}

				if (!preg_match("/^([a-z]\w*)\($/", $stack->last(2), $matches)) {
					return $this->trigger("unexpected ','");
				}

				$stack->push($stack->pop()+1);
				$stack->push('(');
				$index++;
				$expecting_op = false;
			}elseif ($op == '(' and !$expecting_op) {
				$stack->push('(');
				$index++;
				$allow_neg = true;
			}elseif ($ex and !$expecting_op) {
				$expecting_op = true;
				$val = $match[1];

				if (preg_match("/^([a-z]\w*)\($/", $val, $matches)) {
					if (in_array($matches[1], $this->fb) or array_key_exists($matches[1], $this->f)) {
						$stack->push($val);
						$stack->push(1);
						$stack->push('(');
						$expecting_op = false;
					} else {
						$val = $matches[1];
						$output[] = $val;
					}
				} else {
					$output[] = $val;
				}

				$index += strlen($val);
			}elseif ($op == ')') {
				return $this->trigger("unexpected ')'");
			}elseif (in_array($op, $ops) and !$expecting_op) {
				return $this->trigger("unexpected operator '$op'");
			} else {
				return $this->trigger("an unexpected error occured");
			}

			if ($index == strlen($expr)) {
				if (in_array($op, $ops)) {
					return $this->trigger("operator '$op' lacks operand");
				} else {
					break;
				}
			}

			while (substr($expr, $index, 1) == ' ') {
				$index++;
			}
		}

		while (!is_null($op = $stack->pop())) {
			if ($op == '(') {
				return $this->trigger("expecting ')'");
			}

			$output[] = $op;
		}

		return $output;
	}

	function pfx($tokens, $vars = array()) {
		if ($tokens == false) {
			return false;
		}

		$stack = new EvalMathStack;

		foreach ($tokens as $token) {
			if (in_array($token, array('+', '-', '*', '/', '^'))) {
				if (is_null($op2 = $stack->pop())) {
					return $this->trigger("internal error");
				}

				if (is_null($op1 = $stack->pop())) {
					return $this->trigger("internal error");
				}

				switch ($token) {
				case '+':
					$stack->push($op1+$op2);

					break;
				case '-':
					$stack->push($op1-$op2);

					break;
				case '*':
					$stack->push($op1*$op2);

					break;
				case '/':
					if ($op2 == 0) {
						return $this->trigger("division by zero");
					}

					$stack->push($op1/$op2);

					break;
				case '^':
					$stack->push(pow($op1, $op2));

					break;
				}
			}elseif ($token == "_") {
				$stack->push(-1*$stack->pop());
			}elseif (preg_match("/^([a-z]\w*)\($/", $token, $matches)) {
				$fnn = $matches[1];

				if (in_array($fnn, $this->fb)) {
					if (is_null($op1 = $stack->pop())) {
						return $this->trigger("internal error");
					}

					$fnn = preg_replace("/^arc/", "a", $fnn);

					if ($fnn == 'ln') {
						$fnn = 'log';
					}

					eval('$stack->push(' . $fnn . '($op1));');
				}elseif (array_key_exists($fnn, $this->f)) {
					$args = array();

					for ($i = count($this->f[$fnn]['args'])-1; $i >= 0; $i--) {
						if (is_null($args[$this->f[$fnn]['args'][$i]] = $stack->pop())) {
							return $this->trigger("internal error");
						}
					}

					$stack->push($this->pfx($this->f[$fnn]['func'], $args));
				}
			} else {
				if (is_numeric($token)) {
					$stack->push($token);
				}elseif (array_key_exists($token, $this->v)) {
					$stack->push($this->v[$token]);
				}elseif (array_key_exists($token, $vars)) {
					$stack->push($vars[$token]);
				} else {
					return $this->trigger("undefined variable '$token'");
				}
			}
		}

		if ($stack->count != 1) {
			return $this->trigger("internal error");
		}

		return $stack->pop();
	}

	function trigger($msg) {
		$this->last_error = $msg;

		if (!$this->suppress_errors) {
			trigger_error($msg, E_USER_WARNING);
		}

		return false;
	}
}

class EvalMathStack {
	public $stack = array();
	public $count = 0;

	function push($val) {
		$this->stack[$this->count] = $val;
		$this->count++;
	}

	function pop() {
		if ($this->count > 0) {
			$this->count--;

			return $this->stack[$this->count];
		}

		return null;
	}

	function last($n=1) {
		return $this->stack[$this->count-$n];
	}
}

function createGraph($data, $w=400, $h=200, $prop="", $random=false){ // for legacy support, safe to remove
	return graph::createGraph($data, $w, $h, $prop, $random);
}
?>
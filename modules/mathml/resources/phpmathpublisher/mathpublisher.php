<?php
/***************************************************************************
 *   copyright            : (C) 2005 by Pascal Brachet - France            *
 *   pbrachet_NOSPAM_xm1math.net (replace _NOSPAM_ by @)                   *
 *   http://www.xm1math.net/phpmathpublisher/                              *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 ***************************************************************************/

/********* HOW TO USE PHPMATHPUBLISHER ****************************
1) Fix the path to the fonts and the images directory (see PARAMETERS TO MODIFY below)
2) Include this script in your php page :
include("mathpublisher.php") ;
3) Just call the mathfilter($text,$size,$pathtoimg) function in your php page.
$text is the text with standard html tags and mathematical expressions (defined by the <m>...</m> tag).
$size is the size of the police used for the formulas.
$pathtoimg is the relative path between the html pages and the images directory.
With a simple "echo mathfilter($text,$size,$pathtoimg);", you can display text with mathematical formulas.
The mathfilter function will replace all the math tags (<m>formula</m>) in $text by <img src=the formula image >.
Example : 
mathfilter("A math formula : <m>f(x)=sqrt{x}</m>,12,"img/") will return :
"A math formula : <img src=\"img/math_988.5_903b2b36fc716cfb87ff76a65911a6f0.png\" style=\"vertical-align:-11.5px; display: inline-block ;\" alt=\"f(x)=sqrt{x}\" title=\"f(x)=sqrt{x}\">"
The image corresponding to a formula is created only once. Then the image is stocked into the image directories.
The first time that mathfilter is called, the images corresponding to the formulas are created, but the next times mathfilter will only return the html code.

NOTE : if the free latex fonts furnished with this script don't work well (very tiny formulas - that's could happened with some GD configurations), you should try to use the bakoma versions of these fonts (downloadable here : http://www.ctan.org/tex-archive/fonts/cm/ps-type1/bakoma/ttf/ )
*******************************************************************/

//********* PARAMETERS TO MODIFY *********************************
// The four global variables. Uncomment the line if you need it.
//global $dirfonts,$dirimg,$symboles,$fontesmath;

// choose the type of the declaration according to your server settings (some servers don't accept the dirname(__FILE__) command for security reasons).

// NEW in 0.3 version : no more / at the end of $dirfonts and $dirimg

/* 

NOTICE:

These two settings below have been disabled for integration with the Chisimba framework.
They are set in the MathImg Class in the MathMl module

@author: Tohir

*/

// absolute path to the fonts directory
//$GLOBALS['dirfonts']=dirname(__FILE__)."/phpmathpublisher/fonts";

//absolute path to the images directory
//$GLOBALS['dirimg']=dirname(__FILE__)."/phpmathpublisher/img";

//******************************************************************
$GLOBALS['symboles'] = array(
'~'=>' ',
'alpha'=>'&#174;',
'beta'=>'&#175;',
'gamma'=>'&#176;',
'delta'=>'&#177;',
'epsilon'=>'&#178;',
'varepsilon'=>'&#34;',
'zeta'=>'&#179;',
'eta'=>'&#180;',
'theta'=>'&#181;',
'vartheta'=>'&#35;',
'iota'=>'&#182;',
'kappa'=>'&#183;',
'lambda'=>'&#184;',
'mu'=>'&#185;',
'nu'=>'&#186;',
'xi'=>'&#187;',
'Pi'=>'&#188;',
'pi'=>'&#188;',
'varpi'=>'&#36;',
'rho'=>'&#189;',
'varrho'=>'&#37;',
'sigma'=>'&#190;',
'varsigma'=>'&#38;',
'tau'=>'&#191;',
'upsilon'=>'&#192;',
'phi'=>'&#193;',
'varphi'=>'&#39;',
'chi'=>'&#194;',
'psi'=>'&#195;',
'omega'=>'&#33;',
'Gamma'=>'&#161;',
'Lambda'=>'&#164;',
'Sigma'=>'&#167;',
'Psi'=>'&#170;',
'Delta'=>'&#162;',
'Xi'=>'&#165;',
'Upsilon'=>'&#168;',
'Omega'=>'&#173;',
'Theta'=>'&#163;',
'Pi'=>'&#166;',
'Phi'=>'&#169;',
'infty'=>'&#8734;',
'ne'=>'&#8800;',
'*'=>'&#215;',
'in'=>'&#8712;',
'notin'=>'&#8713;',
'forall'=>'&#8704;',
'exists'=>'&#8707;',
'notexists'=>'&#8708;',
'partial'=>'&#8706;',
'approx'=>'&#8776;',
'left'=>'&#8592;',
'right'=>'&#8594;',
'leftright'=>'&#8596;',
'doubleleft'=>'&#8656;',
'doubleright'=>'&#8658;',
'doubleleftright'=>'&#8660;',
'nearrow'=>'&#8599;',
'searrow'=>'&#8601;',
'pm'=>'&#177;',
'bbR'=>'&#8477;',
'bbN'=>'&#8469;',
'bbZ'=>'&#8484;',
'bbC'=>'&#8450;',
'inter'=>'&#8898;',
'union'=>'&#8899;',
'ortho'=>'&#8869;',
'parallel'=>'&#8741;',
'backslash'=>'&#92;',
'prime'=>'&#39;',
'wedge'=>'&#8896;',
'vert'=>'&#8741;',
'subset'=>'&#8834;',
'notsubset'=>'&#8836;',
'circ'=>'&#8728;',
'varnothing'=>'&#248;',
'cdots'=>'&#8943;',
'vdots'=>'&#8942;',
'ddots'=>'&#8945;',
//operateurs
'le'=>'&#54;',
'ge'=>'&#62;',
'<'=>'&#60;',
'>'=>'&#62;',
//parentheses
'('=>'&#179;',
')'=>'&#180;',
'['=>'&#104;',
']'=>'&#105;',
'lbrace'=>'&#40;',
'rbrace'=>'&#41;',
//autres
'_hat'=>'&#99;',
'_racine'=>'&#113;',
'_integrale'=>'&#82;',
'_dintegrale'=>'&#8748;',
'_tintegrale'=>'&#8749;',
'_ointegrale'=>'&#72;',
'_produit'=>'&#81;',
'_somme'=>'&#80;',
'_intersection'=>'&#84;',
'_reunion'=>'&#83;',
'_lim'=>'lim',
//fonctions
'arccos'=>'arccos',
'ker'=>'ker',
'arcsin'=>'arcsin',
'lg'=>'lg',
'arctan'=>'arctan',
'arg'=>'arg',
'cos'=>'cos',
'cosh'=>'cosh',
'ln'=>'ln',
'cot'=>'cot',
'log'=>'log',
'coth'=>'coth',
'max'=>'max',
'csc'=>'csc',
'min'=>'min',
'deg'=>'deg',
'det'=>'det',
'sec'=>'sec',
'dim'=>'dim',
'sin'=>'sin',
'exp'=>'exp',
'sinh'=>'sinh',
'gcd'=>'gcd',
'sup'=>'sup',
'hom'=>'hom',
'tan'=>'tan',
'inf'=>'inf',
'tanh'=>'tanh'
);
$GLOBALS['fontesmath'] = array(
'~'=>'FreeSerif',
'alpha'=>'cmmi10',
'beta'=>'cmmi10',
'gamma'=>'cmmi10',
'delta'=>'cmmi10',
'epsilon'=>'cmmi10',
'varepsilon'=>'cmmi10',
'zeta'=>'cmmi10',
'eta'=>'cmmi10',
'theta'=>'cmmi10',
'vartheta'=>'cmmi10',
'iota'=>'cmmi10',
'kappa'=>'cmmi10',
'lambda'=>'cmmi10',
'mu'=>'cmmi10',
'nu'=>'cmmi10',
'xi'=>'cmmi10',
'pi'=>'cmmi10',
'varpi'=>'cmmi10',
'rho'=>'cmmi10',
'varrho'=>'cmmi10',
'sigma'=>'cmmi10',
'varsigma'=>'cmmi10',
'tau'=>'cmmi10',
'upsilon'=>'cmmi10',
'phi'=>'cmmi10',
'varphi'=>'cmmi10',
'chi'=>'cmmi10',
'psi'=>'cmmi10',
'omega'=>'cmmi10',
'Gamma'=>'cmr10',
'Lambda'=>'cmr10',
'Sigma'=>'cmr10',
'Psi'=>'cmr10',
'Delta'=>'cmr10',
'Xi'=>'cmr10',
'Upsilon'=>'cmr10',
'Omega'=>'cmr10',
'Theta'=>'cmr10',
'Pi'=>'cmr10',
'Phi'=>'cmr10',
'infty'=>'FreeSerif',
'ne'=>'FreeSerif',
'*'=>'FreeSerif',
'in'=>'FreeSerif',
'notin'=>'FreeSerif',
'forall'=>'FreeSerif',
'exists'=>'FreeSerif',
'notexists'=>'FreeSerif',
'partial'=>'FreeSerif',
'approx'=>'FreeSerif',
'left'=>'FreeSerif',
'right'=>'FreeSerif',
'leftright'=>'FreeSerif',
'doubleleft'=>'FreeSerif',
'doubleright'=>'FreeSerif',
'doubleleftright'=>'FreeSerif',
'nearrow'=>'FreeSerif',
'searrow'=>'FreeSerif',
'pm'=>'FreeSerif',
'bbR'=>'FreeSerif',
'bbN'=>'FreeSerif',
'bbZ'=>'FreeSerif',
'bbC'=>'FreeSerif',
'inter'=>'FreeSerif',
'union'=>'FreeSerif',
'ortho'=>'FreeSerif',
'parallel'=>'FreeSerif',
'backslash'=>'FreeSerif',
'prime'=>'FreeSerif',
'wedge'=>'FreeSerif',
'vert'=>'FreeSerif',
'subset'=>'FreeSerif',
'notsubset'=>'FreeSerif',
'circ'=>'FreeSerif',
'varnothing'=>'FreeSerif',
'cdots'=>'FreeSerif',
'vdots'=>'FreeSerif',
'ddots'=>'FreeSerif',
//operateurs
'le'=>'msam10',
'ge'=>'msam10',
'<'=>'cmmi10',
'>'=>'cmmi10',
//parentheses
'('=>'cmex10',
')'=>'cmex10',
'['=>'cmex10',
']'=>'cmex10',
'lbrace'=>'cmex10',
'rbrace'=>'cmex10',
//autres
'_hat'=>'cmex10',
'_racine'=>'cmex10',
'_integrale'=>'cmex10',
'_dintegrale'=>'FreeSerif',
'_tintegrale'=>'FreeSerif',
'_ointegrale'=>'cmex10',
'_produit'=>'cmex10',
'_somme'=>'cmex10',
'_intersection'=>'cmex10',
'_reunion'=>'cmex10',
'_lim'=>'cmr10',
//fonctions
'arccos'=>'cmr10',
'ker'=>'cmr10',
'arcsin'=>'cmr10',
'lg'=>'cmr10',
'arctan'=>'cmr10',
'arg'=>'cmr10',
'cos'=>'cmr10',
'cosh'=>'cmr10',
'ln'=>'cmr10',
'cot'=>'cmr10',
'log'=>'cmr10',
'coth'=>'cmr10',
'max'=>'cmr10',
'csc'=>'cmr10',
'min'=>'cmr10',
'deg'=>'cmr10',
'det'=>'cmr10',
'sec'=>'cmr10',
'dim'=>'cmr10',
'sin'=>'cmr10',
'exp'=>'cmr10',
'sinh'=>'cmr10',
'gcd'=>'cmr10',
'sup'=>'cmr10',
'hom'=>'cmr10',
'tan'=>'cmr10',
'inf'=>'cmr10',
'tanh'=>'cmr10'
);

function est_nombre($str) 
{
return ereg("^[0-9]", $str);
}

function tableau_expression($expression)
{
$e = str_replace('_', ' _ ', $expression);
$e = str_replace('{(}', '{ }', $e);
$e = str_replace('{)}', '{ }', $e);
$t = token_get_all("<?php \$formula=$e ?".">");
$extraits = array();
$result=array();
//stupid code but token_get_all bug in some php versions
$d=0;
for($i = 0; $i < count($t); $i++)
	{
	if(is_array($t[$i])) $t[$i] = $t[$i][1];
	if (ereg("formula", $t[$i]))
		{
		$d=$i+2;
		break;
		}
	}
for($i = $d; $i < count($t) - 1; $i++)
	{
	if(is_array($t[$i])) $t[$i] = $t[$i][1];
	if($t[$i] == '<=') $t[$i] = 'le';
	elseif($t[$i] == '!=') $t[$i] = 'ne';
	elseif($t[$i] == '<>') $t[$i] = 'ne';
	elseif($t[$i] == '>=') $t[$i] = 'ge';
	elseif($t[$i] == '--')
		{
		$t[$i] = '-';
		$t[$i+1] = '-' . $t[$i+1];
		}
	elseif($t[$i] == '++') $t[$i] = '+';
	elseif($t[$i] == '-')
		{
		if($t[$i - 1] == '^' || $t[$i - 1] == '_' || $t[$i - 1] == '*' || $t[$i - 1] == '/' || $t[$i - 1] == '+' || $t[$i - 1] == '(')
			{
			$t[$i] = '';
			if(is_array($t[$i+1])) $t[$i+1][1] = '-' . $t[$i+1][1];
            		else $t[$i+1] = '-' . $t[$i+1];
			}
		}
	if(trim($t[$i]) != '') $extraits[] = $t[$i];
	}
for($i = 0; $i < count($extraits); $i++)
	{
	$result[]=new expression_texte($extraits[$i]);
	}
return $result;
}


// ugly hack, but GD is not very good with truetype fonts (especially with latex fonts)
function affiche_symbol($texte,$haut)
{
global $symboles, $fontesmath, $dirfonts;
$texte=trim(stripslashes($texte));
switch($texte)
	{
	case '':
	$img = ImageCreate(1, max($haut,1));
	$blanc=ImageColorAllocate($img,255,255,255);
	$blanc=imagecolortransparent($img,$blanc);
	ImageFilledRectangle($img,0,0,1,$haut,$blanc);
	break;
	case '~':
	$img = ImageCreate(1, max($haut,1));
	$blanc=ImageColorAllocate($img,255,255,255);
	$blanc=imagecolortransparent($img,$blanc);
	ImageFilledRectangle($img,0,0,1,$haut,$blanc);
	break;
	case 'vert':
	$img = ImageCreate(6, max($haut,1));
	$blanc=ImageColorAllocate($img,255,255,255);
	$blanc=imagecolortransparent($img,$blanc);
	$noir=ImageColorAllocate($img,0,0,0);
	ImageFilledRectangle($img,0,0,6,$haut,$blanc);
	ImageFilledRectangle($img,2,0,2,$haut,$noir);
	ImageFilledRectangle($img,4,0,4,$haut,$noir);
	break;
	case '|':
	$img = ImageCreate(5, max($haut,1));
	$blanc=ImageColorAllocate($img,255,255,255);
	$blanc=imagecolortransparent($img,$blanc);
	$noir=ImageColorAllocate($img,0,0,0);
	ImageFilledRectangle($img,0,0,5,$haut,$blanc);
	ImageFilledRectangle($img,2,0,2,$haut,$noir);
	break;
	case 'right':
	$font =$dirfonts."/".$fontesmath[$texte].".ttf";
	$t=16;
	$texte = $symboles[$texte];
	$tmp_dim = ImageTTFBBox($t, 0, $font , $texte);
	$tmp_largeur = abs($tmp_dim[2] - $tmp_dim[0])+2;
	$tmp_hauteur = abs($tmp_dim[3] - $tmp_dim[5])+2;
	$tmp_img = ImageCreate(max($tmp_largeur,1), max($tmp_hauteur,1));
	$tmp_noir=ImageColorAllocate($tmp_img,0,0,0);
	$tmp_blanc=ImageColorAllocate($tmp_img,255,255,255);
	$tmp_blanc=imagecolortransparent($tmp_img,$tmp_blanc);
	ImageFilledRectangle($tmp_img,0,0,$tmp_largeur,$tmp_hauteur,$tmp_blanc);
	ImageTTFText($tmp_img, $t, 0,0,$tmp_hauteur,$tmp_noir, $font,$texte);
	$toutblanc=true;
	$sx = $sy = $ex = $ey = -1;
	for ($y = 0; $y < $tmp_hauteur; $y++)
	{
		for ($x = 0; $x < $tmp_largeur; $x++)
		{
			$rgb = ImageColorAt($tmp_img, $x, $y);
			if ($rgb !=$tmp_blanc)
			{
				$toutblanc=false;
				if ($sy == -1) $sy = $y;
				else $ey = $y;
	
				if ($sx == -1) $sx = $x;
				else
				{
					if ($x < $sx) $sx = $x;
					else if ($x > $ex) $ex = $x;
				}
			}
		}
	}
	$nx = abs($ex - $sx);
	$ny = abs($ey - $sy);
	$img = ImageCreate(max($nx+4,1),max($ny+4,1));
	$blanc=ImageColorAllocate($img,255,255,255);
	$blanc=imagecolortransparent($img,$blanc);
	ImageFilledRectangle($img,0,0,$nx+4,$ny+4,$blanc);
	ImageCopy($img,$tmp_img,2,2,$sx,$sy,min($nx+2,$tmp_largeur-$sx),min($ny+2,$tmp_hauteur-$sy));
	break;
	case '_hat':
	$font =$dirfonts."/".$fontesmath[$texte].".ttf";
	$t=$haut;
	$texte = $symboles[$texte];
	$tmp_dim = ImageTTFBBox($t, 0, $font , $texte);
	$tmp_largeur = abs($tmp_dim[2] - $tmp_dim[0]);
	$tmp_hauteur = abs($tmp_dim[3] - $tmp_dim[5])*4;
	$tmp_img = ImageCreate(max($tmp_largeur,1), max($tmp_hauteur,1));
	$tmp_noir=ImageColorAllocate($tmp_img,0,0,0);
	$tmp_blanc=ImageColorAllocate($tmp_img,255,255,255);
	$tmp_blanc=imagecolortransparent($tmp_img,$tmp_blanc);
	ImageFilledRectangle($tmp_img,0,0,$tmp_largeur,$tmp_hauteur,$tmp_blanc);
	ImageTTFText($tmp_img, $t, 0,0,$tmp_hauteur,$tmp_noir, $font,$texte);
	$toutblanc=true;
	$img=$tmp_img;
	$sx = $sy = $ex = $ey = -1;
	for ($y = 0; $y < $tmp_hauteur; $y++)
	{
		for ($x = 0; $x < $tmp_largeur; $x++)
		{
			$rgb = ImageColorAt($tmp_img, $x, $y);
			if ($rgb !=$tmp_blanc)
			{
				$toutblanc=false;
				if ($sy == -1) $sy = $y;
				else $ey = $y;
	
				if ($sx == -1) $sx = $x;
				else
				{
					if ($x < $sx) $sx = $x;
					else if ($x > $ex) $ex = $x;
				}
			}
		}
	}
	$nx = abs($ex - $sx);
	$ny = abs($ey - $sy);
	$img = ImageCreate(max($nx+4,1),max($ny+4,1));
	$blanc=ImageColorAllocate($img,255,255,255);
	$blanc=imagecolortransparent($img,$blanc);
	ImageFilledRectangle($img,0,0,$nx+4,$ny+4,$blanc);
	ImageCopy($img,$tmp_img,2,2,$sx,$sy,min($nx+2,$tmp_largeur-$sx),min($ny+2,$tmp_hauteur-$sy));
	break;
	case '_dintegrale':
	case '_tintegrale':
	if(isset($fontesmath[$texte])) $font = $dirfonts."/".$fontesmath[$texte].".ttf";
	elseif (est_nombre($texte)) $font = $dirfonts."/cmr10.ttf";
	else $font = $dirfonts."/cmmi10.ttf";
	$t=6;
	if(isset($symboles[$texte])) $texte = $symboles[$texte];
	do
		{
		$tmp_dim = ImageTTFBBox($t, 0, $font , $texte);
		$t+=1;
		}
	while ((abs($tmp_dim[3] - $tmp_dim[5])<1.2*$haut));
	$tmp_largeur = abs($tmp_dim[2] - $tmp_dim[0])*2;
	$tmp_hauteur = abs($tmp_dim[3] - $tmp_dim[5])*2;
	$tmp_img = ImageCreate(max($tmp_largeur,1), max($tmp_hauteur,1));
	$tmp_noir=ImageColorAllocate($tmp_img,0,0,0);
	$tmp_blanc=ImageColorAllocate($tmp_img,255,255,255);
	$tmp_blanc=imagecolortransparent($tmp_img,$tmp_blanc);
	ImageFilledRectangle($tmp_img,0,0,$tmp_largeur,$tmp_hauteur,$tmp_blanc);
	ImageTTFText($tmp_img, $t,0,5,$tmp_hauteur/2,$tmp_noir, $font,$texte);
	$img=$tmp_img;
	$toutblanc=true;
	$sx = $sy = $ex = $ey = -1;
	for ($y = 0; $y < $tmp_hauteur; $y++)
	{
		for ($x = 0; $x < $tmp_largeur; $x++)
		{
			$rgb = ImageColorAt($tmp_img, $x, $y);
			if ($rgb !=$tmp_blanc)
			{
				$toutblanc=false;
				if ($sy == -1) $sy = $y;
				else $ey = $y;
	
				if ($sx == -1) $sx = $x;
				else
				{
					if ($x < $sx) $sx = $x;
					else if ($x > $ex) $ex = $x;
				}
			}
		}
	}
	$nx = abs($ex - $sx);
	$ny = abs($ey - $sy);
	if ($toutblanc)
		{
		$img = ImageCreate(1, max($haut,1));
		$blanc=ImageColorAllocate($img,255,255,255);
		$blanc=imagecolortransparent($img,$blanc);
		ImageFilledRectangle($img,0,0,1,$haut,$blanc);
		}
	else
		{
		$img = ImageCreate(max($nx+4,1),max($ny+4,1));
		$blanc=ImageColorAllocate($img,255,255,255);
		$blanc=imagecolortransparent($img,$blanc);
		ImageFilledRectangle($img,0,0,$nx+4,$ny+4,$blanc);
		ImageCopy($img,$tmp_img,2,2,$sx,$sy,min($nx+2,$tmp_largeur-$sx),min($ny+2,$tmp_hauteur-$sy));
		}
	break;
	default:
	if(isset($fontesmath[$texte])) $font = $dirfonts."/".$fontesmath[$texte].".ttf";
	elseif (est_nombre($texte)) $font = $dirfonts."/cmr10.ttf";
	else $font = $dirfonts."/cmmi10.ttf";
	$t=6;
	if(isset($symboles[$texte])) $texte = $symboles[$texte];
	do
		{
		$tmp_dim = ImageTTFBBox($t, 0, $font , $texte);
		$t+=1;
		}
	while ((abs($tmp_dim[3] - $tmp_dim[5])<$haut));
	$tmp_largeur = abs($tmp_dim[2] - $tmp_dim[0])*2;
	$tmp_hauteur = abs($tmp_dim[3] - $tmp_dim[5])*2;
	$tmp_img = ImageCreate(max($tmp_largeur,1), max($tmp_hauteur,1));
	$tmp_noir=ImageColorAllocate($tmp_img,0,0,0);
	$tmp_blanc=ImageColorAllocate($tmp_img,255,255,255);
	$tmp_blanc=imagecolortransparent($tmp_img,$tmp_blanc);
	ImageFilledRectangle($tmp_img,0,0,$tmp_largeur,$tmp_hauteur,$tmp_blanc);
	ImageTTFText($tmp_img, $t, 0,0,$tmp_hauteur/4,$tmp_noir, $font,$texte);
// 	ImageTTFText($tmp_img, $t, 0,5,5,$tmp_noir, $font,$texte);
//	$img=$tmp_img;
	$toutblanc=true;
	$sx = $sy = $ex = $ey = -1;
	for ($y = 0; $y < $tmp_hauteur; $y++)
	{
		for ($x = 0; $x < $tmp_largeur; $x++)
		{
			$rgb = ImageColorAt($tmp_img, $x, $y);
			if ($rgb !=$tmp_blanc)
			{
				$toutblanc=false;
				if ($sy == -1) $sy = $y;
				else $ey = $y;
	
				if ($sx == -1) $sx = $x;
				else
				{
					if ($x < $sx) $sx = $x;
					else if ($x > $ex) $ex = $x;
				}
			}
		}
	}
	$nx = abs($ex - $sx);
	$ny = abs($ey - $sy);
	if ($toutblanc)
		{
		$img = ImageCreate(1, max($haut,1));
		$blanc=ImageColorAllocate($img,255,255,255);
		$blanc=imagecolortransparent($img,$blanc);
		ImageFilledRectangle($img,0,0,1,$haut,$blanc);
		}
	else
		{
		$img = ImageCreate(max($nx+4,1),max($ny+4,1));
		$blanc=ImageColorAllocate($img,255,255,255);
		$blanc=imagecolortransparent($img,$blanc);
		ImageFilledRectangle($img,0,0,$nx+4,$ny+4,$blanc);
		ImageCopy($img,$tmp_img,2,2,$sx,$sy,min($nx+2,$tmp_largeur-$sx),min($ny+2,$tmp_hauteur-$sy));
		}
	break;
	}
//$rouge=ImageColorAllocate($img,255,0,0);
//ImageRectangle($img,0,0,ImageSX($img)-1,ImageSY($img)-1,$rouge);
return $img;
}

function affiche_texte($texte, $taille)
{
global $dirfonts;
$taille=max($taille,6);
$texte=stripslashes($texte);
$font = $dirfonts."/cmr10.ttf";
$htexte = 'dg'.$texte;
$hdim = ImageTTFBBox($taille, 0, $font, $htexte);
$wdim = ImageTTFBBox($taille, 0, $font, $texte); 
$dx = max($wdim[2], $wdim[4]) - min($wdim[0], $wdim[6])+ceil($taille /8);
$dy = max($hdim[1], $hdim[3]) - min($hdim[5], $hdim[7])+ ceil($taille /8);
$img = ImageCreate(max($dx,1), max($dy,1));
$noir=ImageColorAllocate($img,0,0,0);
$blanc=ImageColorAllocate($img,255,255,255);
$blanc=imagecolortransparent($img,$blanc);
ImageFilledRectangle($img,0,0,$dx,$dy,$blanc);
//ImageRectangle($img,0,0,$dx-1,$dy-1,$noir);
ImageTTFText($img, $taille, $angle, 0, -min($hdim[5], $hdim[7]), $noir, $font, $texte);
return $img;
}

function affiche_math($texte, $taille)
{
global $dirfonts;
$taille=max($taille,6);
global $symboles, $fontesmath;
$texte=stripslashes($texte);
if(isset($fontesmath[$texte])) $font = $dirfonts."/".$fontesmath[$texte].".ttf";
elseif (ereg("[a-zA-Z]", $texte)) $font = $dirfonts."/cmmi10.ttf";
else $font = $dirfonts."/cmr10.ttf";
if(isset($symboles[$texte])) $texte = $symboles[$texte];
$htexte = 'dg'.$texte;
$hdim = ImageTTFBBox($taille, 0, $font, $htexte);
$wdim = ImageTTFBBox($taille, 0, $font, $texte); 
$dx = max($wdim[2], $wdim[4]) - min($wdim[0], $wdim[6])+ceil($taille /8);
$dy = max($hdim[1], $hdim[3]) - min($hdim[5], $hdim[7])+ ceil($taille /8);
$img = ImageCreate(max($dx,1), max($dy,1));
$noir=ImageColorAllocate($img,0,0,0);
$blanc=ImageColorAllocate($img,255,255,255);
$blanc=imagecolortransparent($img,$blanc);
ImageFilledRectangle($img,0,0,$dx,$dy,$blanc);
//ImageRectangle($img,0,0,$dx-1,$dy-1,$noir);
ImageTTFText($img, $taille, 0, 0, -min($hdim[5], $hdim[7]), $noir, $font, $texte);
return $img;
}

function parenthese($hauteur, $style)
{
$image=affiche_symbol($style,$hauteur);
return $image;
}

function alignement2($image1,$base1,$image2,$base2)
{
$largeur1=imagesx($image1);
$hauteur1=imagesy($image1);
$largeur2=imagesx($image2);
$hauteur2=imagesy($image2);
$dessus=max($base1,$base2);
$dessous=max($hauteur1-$base1,$hauteur2-$base2);
$largeur=$largeur1+$largeur2;
$hauteur=$dessus+$dessous;
$result = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($result,0,0,0);
$blanc=ImageColorAllocate($result,255,255,255);
$blanc=imagecolortransparent($result,$blanc);
ImageFilledRectangle($result,0,0,$largeur-1,$hauteur-1,$blanc);
ImageCopy($result, $image1,0,$dessus - $base1, 0, 0,$largeur1,$hauteur1);
ImageCopy($result, $image2,$largeur1,$dessus - $base2, 0, 0,$largeur2,$hauteur2);
//ImageRectangle($result,0,0,$largeur-1,$hauteur-1,$noir);
return $result;
}

function alignement3($image1,$base1,$image2,$base2,$image3,$base3)
{
$largeur1=imagesx($image1);
$hauteur1=imagesy($image1);
$largeur2=imagesx($image2);
$hauteur2=imagesy($image2);
$largeur3=imagesx($image3);
$hauteur3=imagesy($image3);
$dessus=max($base1,$base2,$base3);
$dessous=max($hauteur1-$base1,$hauteur2-$base2,$hauteur3-$base3);
$largeur=$largeur1+$largeur2+$largeur3;
$hauteur=$dessus+$dessous;
$result = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($result,0,0,0);
$blanc=ImageColorAllocate($result,255,255,255);
$blanc=imagecolortransparent($result,$blanc);
ImageFilledRectangle($result,0,0,$largeur-1,$hauteur-1,$blanc);
ImageCopy($result, $image1,0,$dessus - $base1, 0, 0,$largeur1,$hauteur1);
ImageCopy($result, $image2,$largeur1,$dessus - $base2, 0, 0,$largeur2,$hauteur2);
ImageCopy($result, $image3,$largeur1+$largeur2,$dessus - $base3, 0, 0,$largeur3,$hauteur3);
//ImageRectangle($result,0,0,$largeur-1,$hauteur-1,$noir);
return $result;
}

//*****************************************************************
class expression
{
var $texte;
var $image;
var $base_verticale;
}
//*****************************************************************
class expression_texte extends  expression
{
function expression_texte($exp)
{
$this->texte = $exp;
}
function dessine($taille)
{
$this->image = affiche_math($this->texte,$taille);
$this->base_verticale = imagesy($this->image) / 2;
}
}
//*****************************************************************
class expression_math extends  expression
{
var $noeuds;
function expression_math($exp)
{
$this->texte = "&$";
$this->noeuds = $exp;
$this->noeuds = $this->parse();
}

function parse()
{
if(count($this->noeuds) <= 3) return $this->noeuds;
$ret = array();
$parentheses = array();
for($i = 0; $i < count($this->noeuds); $i++)
	{
	if($this->noeuds[$i]->texte == '(' || $this->noeuds[$i]->texte == '{') array_push($parentheses, $i);
	elseif($this->noeuds[$i]->texte == ')' || $this->noeuds[$i]->texte == '}')
		{
		$pos = array_pop($parentheses);
		if(count($parentheses) == 0)
			{
			$sub = array_slice($this->noeuds, $pos + 1, $i - $pos - 1);
			if($this->noeuds[$i]->texte == ')') 
				{
				$ret[] = new expression_math(array(new expression_texte("("), new expression_math($sub), new expression_texte(")")));
				}
			else $ret[] = new expression_math($sub);
			}
		}
	elseif(count($parentheses) == 0) $ret[] = $this->noeuds[$i];
	}
$ret = $this->traite_fonction($ret, 'sqrt', 1);
$ret = $this->traite_fonction($ret, 'vec', 1);
$ret = $this->traite_fonction($ret, 'overline', 1);
$ret = $this->traite_fonction($ret, 'underline', 1);
$ret = $this->traite_fonction($ret, 'hat', 1);
$ret = $this->traite_fonction($ret, 'int', 3);
$ret = $this->traite_fonction($ret, 'doubleint', 3);
$ret = $this->traite_fonction($ret, 'tripleint', 3);
$ret = $this->traite_fonction($ret, 'oint', 3);
$ret = $this->traite_fonction($ret, 'prod', 3);
$ret = $this->traite_fonction($ret, 'sum', 3);
$ret = $this->traite_fonction($ret, 'bigcup', 3);
$ret = $this->traite_fonction($ret, 'bigcap', 3);
$ret = $this->traite_fonction($ret, 'delim', 3);
$ret = $this->traite_fonction($ret, 'lim', 2);
$ret = $this->traite_fonction($ret, 'root', 2);
$ret = $this->traite_fonction($ret, 'matrix', 3);
$ret = $this->traite_fonction($ret, 'tabular', 3);

$ret = $this->traite_operation($ret, '^');
$ret = $this->traite_operation($ret, 'over');
$ret = $this->traite_operation($ret, '_');
$ret = $this->traite_operation($ret, 'under');
$ret = $this->traite_operation($ret, '*');
$ret = $this->traite_operation($ret, '/');
$ret = $this->traite_operation($ret, '+');
$ret = $this->traite_operation($ret, '-');
return $ret;
}

function traite_operation($noeuds, $operation)
{
do
	{
	$change = false;
	if(count($noeuds) <= 3) return $noeuds;
	$ret = array();
	for($i = 0; $i < count($noeuds); $i++)
		{
		if(!$change && $i < count($noeuds) - 2 && $noeuds[$i+1]->texte == $operation)
			{
			$ret[] = new expression_math(array($noeuds[$i], $noeuds[$i+1], $noeuds[$i+2]));
			$i += 2;
			$change = true;
			}
		else
		$ret[] = $noeuds[$i];
		}
	$noeuds = $ret;
	}
while($change);
return $ret;
}

function traite_fonction($noeuds, $fonction, $nbarg)
{
if(count($noeuds) <= $nbarg + 1) return $noeuds;
$ret = array();
for($i = 0; $i < count($noeuds); $i++)
{
if($i < count($noeuds) - $nbarg && $noeuds[$i]->texte == $fonction)
{
	$a = array();
	for($j = $i; $j <= $i + $nbarg; $j++)
	$a[] = $noeuds[$j];
	$ret[] = new expression_math($a);
	$i += $nbarg;
}
else
	$ret[] = $noeuds[$i];
}
return $ret;
}


function dessine($taille)
{
switch(count($this->noeuds))
{
case 1:
	$this->noeuds[0]->dessine($taille);
	$this->image = $this->noeuds[0]->image;
	$this->base_verticale = $this->noeuds[0]->base_verticale;
	break;
case 2:
	switch($this->noeuds[0]->texte)
		{
		case 'sqrt':
		$this->dessine_racine($taille);
		break;
		case 'vec':
		$this->dessine_vecteur($taille);
		break;
		case 'overline':
		$this->dessine_overline($taille);
		break;
		case 'underline':
		$this->dessine_underline($taille);
		break;
		case 'hat':
		$this->dessine_chapeau($taille);
		break;
		default:
		$this->dessine_expression($taille);
		break;
		}
	break;
case 3:
	if ($this->noeuds[0]->texte=="lim") 
		{
		$this->dessine_limite($taille);
		}
	elseif ($this->noeuds[0]->texte=="root") 
		{
		$this->dessine_root($taille);
		}
	else
	{
	switch($this->noeuds[1]->texte)
		{
		case '/':
		$this->dessine_fraction($taille);
		break;
		case '^':
		$this->dessine_exposant($taille);
		break;
		case 'over':
		$this->dessine_dessus($taille);
		break;
		case '_':
		$this->dessine_indice($taille);
		break;
		case 'under':
		$this->dessine_dessous($taille);
		break;
		default:
		$this->dessine_expression($taille);
		break;
		}
	}
	break;
case 4:
	switch($this->noeuds[0]->texte)
		{
		case 'int':
		$this->dessine_grandoperateur($taille,'_integrale');
		break;
		case 'doubleint':
		$this->dessine_grandoperateur($taille,'_dintegrale');
		break;
		case 'tripleint':
		$this->dessine_grandoperateur($taille,'_tintegrale');
		break;
		case 'oint':
		$this->dessine_grandoperateur($taille,'_ointegrale');
		break;
		case 'sum':
		$this->dessine_grandoperateur($taille,'_somme');
		break;
		case 'prod':
		$this->dessine_grandoperateur($taille,'_produit');
		break;
		case 'bigcap':
		$this->dessine_grandoperateur($taille,'_intersection');
		break;
		case 'bigcup':
		$this->dessine_grandoperateur($taille,'_reunion');
		break;
		case 'delim':
		$this->dessine_delimiteur($taille);
		break;
		case 'matrix':
		$this->dessine_matrice($taille);
		break;
		case 'tabular':
		$this->dessine_tableau($taille);
		break;
		default:
		$this->dessine_expression($taille);
		break;
		}
	break;
default:
	$this->dessine_expression($taille);
	break;
}
}

function dessine_expression($taille)
{
$largeur=1;
$hauteur=1;
$dessus=1;
$dessous=1;
for($i = 0; $i < count($this->noeuds); $i++)
	{
	if ($this->noeuds[$i]->texte != '(' && $this->noeuds[$i]->texte != ')')
		{
		$this->noeuds[$i]->dessine($taille);
		$img[$i] = $this->noeuds[$i]->image;
		$base[$i] = $this->noeuds[$i]->base_verticale;
		$dessus = max($base[$i], $dessus);
		$dessous = max(imagesy($img[$i]) - $base[$i], $dessous);
		}
	}
$hauteur=$dessus+$dessous;
$paro=parenthese(max($dessus,$dessous)*2,"(");
$parf=parenthese(max($dessus,$dessous)*2,")");
for($i = 0; $i < count($this->noeuds); $i++)
        {
	if(!isset($img[$i]))
		{
		if ($this->noeuds[$i]->texte == "(") $img[$i] = $paro;
		else $img[$i] = $parf;
		$dessus=max(imagesy($img[$i])/2,$dessus);
		$base[$i]=imagesy($img[$i])/2;
		$dessous = max(imagesy($img[$i]) - $base[$i], $dessous);
		$hauteur=max(imagesy($img[$i]),$hauteur);
		}
	$largeur+=imagesx($img[$i]);
       	}
$this->base_verticale = $dessus;
$result = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($result,0,0,0);
$blanc=ImageColorAllocate($result,255,255,255);
$blanc=imagecolortransparent($result,$blanc);
ImageFilledRectangle($result,0,0,$largeur-1,$hauteur-1,$blanc);
$pos = 0;
for($i = 0; $i < count($img); $i++)
	{
	if(isset($img[$i]))
		{
		ImageCopy($result, $img[$i],$pos,$dessus-$base[$i], 0, 0,imagesx($img[$i]),imagesy($img[$i]));
		$pos += imagesx($img[$i]);
		}
	}
$this->image=$result;
}

function dessine_fraction($taille) 
{
$this->noeuds[0]->dessine($taille*0.9);
$img1=$this->noeuds[0]->image;
$base1=$this->noeuds[0]->base_verticale;
$this->noeuds[2]->dessine($taille*0.9);
$img2=$this->noeuds[2]->image;
$base2=$this->noeuds[2]->base_verticale;
$hauteur1=imagesy($img1);
$hauteur2=imagesy($img2);
$largeur1=imagesx($img1);
$largeur2=imagesx($img2);
$largeur = max($largeur1,$largeur2);
$hauteur = $hauteur1+$hauteur2+4;
$result = ImageCreate(max($largeur+5,1), max($hauteur,1));
$noir=ImageColorAllocate($result,0,0,0);
$blanc=ImageColorAllocate($result,255,255,255);
$blanc=imagecolortransparent($result,$blanc);
$this->base_verticale=$hauteur1+2;
ImageFilledRectangle($result,0,0,$largeur+4,$hauteur-1,$blanc);
ImageCopy($result, $img1, ($largeur - $largeur1)/2, 0, 0, 0,$largeur1,$hauteur1);
imageline($result, 0,$this->base_verticale, $largeur,$this->base_verticale, $noir);
ImageCopy($result, $img2, ($largeur - $largeur2)/2,$hauteur1+4, 0, 0,$largeur2,$hauteur2);
$this->image=$result;
}

function dessine_exposant($taille) 
{
$this->noeuds[0]->dessine($taille);
$img1=$this->noeuds[0]->image;
$base1=$this->noeuds[0]->base_verticale;
$this->noeuds[2]->dessine($taille*0.8);
$img2=$this->noeuds[2]->image;
$base2=$this->noeuds[2]->base_verticale;
$hauteur1=imagesy($img1);
$hauteur2=imagesy($img2);
$largeur1=imagesx($img1);
$largeur2=imagesx($img2);
$largeur =$largeur1 + $largeur2;
if ($hauteur1 >= $hauteur2)
	{
	$hauteur = ceil($hauteur2/2+$hauteur1);
	$this->base_verticale=$hauteur2/2+ $base1;
	$result = ImageCreate(max($largeur,1), max($hauteur,1));
	$noir=ImageColorAllocate($result,0,0,0);
	$blanc=ImageColorAllocate($result,255,255,255);
	$blanc=imagecolortransparent($result,$blanc);
	ImageFilledRectangle($result,0,0,$largeur-1,$hauteur-1,$blanc);
	ImageCopy($result, $img1, 0, ceil($hauteur2/2), 0, 0, $largeur1, $hauteur1);
	ImageCopy($result, $img2, $largeur1, 0, 0, 0, $largeur2,$hauteur2);
	}
else 
	{
	$hauteur = ceil($hauteur1/2+$hauteur2);
	$this->base_verticale=$hauteur2-$base1+$hauteur1/2;
	$result = ImageCreate(max($largeur,1), max($hauteur,1));
	$noir=ImageColorAllocate($result,0,0,0);
	$blanc=ImageColorAllocate($result,255,255,255);
	$blanc=imagecolortransparent($result,$blanc);
	ImageFilledRectangle($result,0,0,$largeur-1,$hauteur-1,$blanc);
	ImageCopy($result, $img1, 0, ceil($hauteur2-$hauteur1/2), 0, 0, $largeur1, $hauteur1);
	ImageCopy($result, $img2, $largeur1, 0, 0, 0, $largeur2,$hauteur2);
	}
$this->image=$result;
}

function dessine_indice($taille) 
{
$this->noeuds[0]->dessine($taille);
$img1=$this->noeuds[0]->image;
$base1=$this->noeuds[0]->base_verticale;
$this->noeuds[2]->dessine($taille*0.8);
$img2=$this->noeuds[2]->image;
$base2=$this->noeuds[2]->base_verticale;
$hauteur1=imagesy($img1);
$hauteur2=imagesy($img2);
$largeur1=imagesx($img1);
$largeur2=imagesx($img2);
$largeur =$largeur1 + $largeur2;
if ($hauteur1 >= $hauteur2)
	{
	$hauteur = ceil($hauteur2/2+$hauteur1);
	$this->base_verticale=$base1;
	$result = ImageCreate(max($largeur,1), max($hauteur,1));
	$noir=ImageColorAllocate($result,0,0,0);
	$blanc=ImageColorAllocate($result,255,255,255);
	$blanc=imagecolortransparent($result,$blanc);
	ImageFilledRectangle($result,0,0,$largeur-1,$hauteur-1,$blanc);
	ImageCopy($result, $img1, 0, 0, 0, 0, $largeur1, $hauteur1);
	ImageCopy($result, $img2, $largeur1,ceil($hauteur1-$hauteur2/2) , 0, 0, $largeur2,$hauteur2);
	}
else 
	{
	$hauteur = ceil($hauteur1/2+$hauteur2);
	$this->base_verticale=$base1;
	$result = ImageCreate(max($largeur,1), max($hauteur,1));
	$noir=ImageColorAllocate($result,0,0,0);
	$blanc=ImageColorAllocate($result,255,255,255);
	$blanc=imagecolortransparent($result,$blanc);
	ImageFilledRectangle($result,0,0,$largeur-1,$hauteur-1,$blanc);
	ImageCopy($result, $img1, 0, 0, 0, 0, $largeur1, $hauteur1);
	ImageCopy($result, $img2, $largeur1,ceil($hauteur1/2), 0, 0, $largeur2,$hauteur2);
	}
$this->image=$result;
}

function dessine_racine($taille) 
{
$this->noeuds[1]->dessine($taille);
$imgexp=$this->noeuds[1]->image;
$baseexp=$this->noeuds[1]->base_verticale;
$largeurexp=imagesx($imgexp);
$hauteurexp=imagesy($imgexp);

$imgrac=affiche_symbol("_racine",$hauteurexp+2);
$largeurrac=imagesx($imgrac);
$hauteurrac=imagesy($imgrac);
$baserac=$hauteurrac/2;

$largeur=$largeurrac+$largeurexp;
$hauteur=max($hauteurexp,$hauteurrac);
$result = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($result,0,0,0);
$blanc=ImageColorAllocate($result,255,255,255);
$blanc=imagecolortransparent($result,$blanc);
ImageFilledRectangle($result,0,0,$largeur-1,$hauteur-1,$blanc);
ImageCopy($result, $imgrac,0,0, 0, 0,$largeurrac,$hauteurrac);
ImageCopy($result, $imgexp,$largeurrac,$hauteur-$hauteurexp, 0, 0,$largeurexp,$hauteurexp);
imagesetthickness($result,1);
imageline($result, $largeurrac-2,2, $largeurrac+$largeurexp+2,2, $noir);
$this->base_verticale=$hauteur-$hauteurexp+$baseexp;
$this->image=$result;	
}

function dessine_root($taille) 
{
$this->noeuds[1]->dessine($taille*0.6);
$imgroot=$this->noeuds[1]->image;
$baseroot=$this->noeuds[1]->base_verticale;
$largeurroot=imagesx($imgroot);
$hauteurroot=imagesy($imgroot);

$this->noeuds[2]->dessine($taille);
$imgexp=$this->noeuds[2]->image;
$baseexp=$this->noeuds[2]->base_verticale;
$largeurexp=imagesx($imgexp);
$hauteurexp=imagesy($imgexp);

$imgrac=affiche_symbol("_racine",$hauteurexp+2);
$largeurrac=imagesx($imgrac);
$hauteurrac=imagesy($imgrac);
$baserac=$hauteurrac/2;

$largeur=$largeurrac+$largeurexp;
$hauteur=max($hauteurexp,$hauteurrac);
$result = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($result,0,0,0);
$blanc=ImageColorAllocate($result,255,255,255);
$blanc=imagecolortransparent($result,$blanc);
ImageFilledRectangle($result,0,0,$largeur-1,$hauteur-1,$blanc);
ImageCopy($result, $imgrac,0,0, 0, 0,$largeurrac,$hauteurrac);
ImageCopy($result, $imgexp,$largeurrac,$hauteur-$hauteurexp, 0, 0,$largeurexp,$hauteurexp);
imagesetthickness($result,1);
imageline($result, $largeurrac-2,2, $largeurrac+$largeurexp+2,2, $noir);
ImageCopy($result, $imgroot,0,0, 0, 0,$largeurroot,$hauteurroot); 
$this->base_verticale=$hauteur-$hauteurexp+$baseexp;
$this->image=$result;	
}

function dessine_grandoperateur($taille,$caractere) 
{
$this->noeuds[1]->dessine($taille*0.8);
$img1=$this->noeuds[1]->image;
$base1=$this->noeuds[1]->base_verticale;
$this->noeuds[2]->dessine($taille*0.8);
$img2=$this->noeuds[2]->image;
$base2=$this->noeuds[2]->base_verticale;
$this->noeuds[3]->dessine($taille);
$imgexp=$this->noeuds[3]->image;
$baseexp=$this->noeuds[3]->base_verticale;
//borneinf
$largeur1=imagesx($img1);
$hauteur1=imagesy($img1);
//bornesup
$largeur2=imagesx($img2);
$hauteur2=imagesy($img2);
//expression
$hauteurexp=imagesy($imgexp);
$largeurexp=imagesx($imgexp);
//caractere
$imgsymbole=affiche_symbol($caractere,$baseexp*1.8);//max($baseexp,$hauteurexp-$baseexp)*2);
$largeursymbole=imagesx($imgsymbole);
$hauteursymbole=imagesy($imgsymbole);
$basesymbole=$hauteursymbole/2;

$hauteurgauche=$hauteursymbole+$hauteur1+$hauteur2;
$largeurgauche=max($largeursymbole,$largeur1,$largeur2);
$imggauche=ImageCreate(max($largeurgauche,1), max($hauteurgauche,1));
$noir=ImageColorAllocate($imggauche,0,0,0);
$blanc=ImageColorAllocate($imggauche,255,255,255);
$blanc=imagecolortransparent($imggauche,$blanc);
ImageFilledRectangle($imggauche,0,0,$largeurgauche-1,$hauteurgauche-1,$blanc);
ImageCopy($imggauche, $imgsymbole,($largeurgauche-$largeursymbole)/2, $hauteur2, 0, 0,$largeursymbole,$hauteursymbole);
ImageCopy($imggauche, $img2,($largeurgauche-$largeur2)/2,0, 0, 0,$largeur2,$hauteur2);
ImageCopy($imggauche, $img1,($largeurgauche-$largeur1)/2, $hauteur2+$hauteursymbole, 0, 0,$largeur1,$hauteur1);
$imgfin=alignement2($imggauche,$basesymbole+$hauteur2,$imgexp,$baseexp);
$this->image=$imgfin;
$this->base_verticale=max($basesymbole+$hauteur2,$baseexp+$hauteur2);
}

function dessine_dessus($taille) 
{
$this->noeuds[2]->dessine($taille*0.8);
$imgsup=$this->noeuds[2]->image;
$basesup=$this->noeuds[2]->base_verticale;
$this->noeuds[0]->dessine($taille);
$imgexp=$this->noeuds[0]->image;
$baseexp=$this->noeuds[0]->base_verticale;
//expression
$largeurexp=imagesx($imgexp);
$hauteurexp=imagesy($imgexp);
//bornesup
$largeursup=imagesx($imgsup);
$hauteursup=imagesy($imgsup);
//fin
$hauteur=$hauteurexp+$hauteursup;
$largeur=max($largeursup,$largeurexp)+ceil($taille/8);
$imgfin = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($imgfin,0,0,0);
$blanc=ImageColorAllocate($imgfin,255,255,255);
$blanc=imagecolortransparent($imgfin,$blanc);
ImageFilledRectangle($imgfin,0,0,$largeur-1,$hauteur-1,$blanc);
ImageCopy($imgfin, $imgsup,($largeur-$largeursup)/2, 0, 0, 0,$largeursup,$hauteursup);
ImageCopy($imgfin, $imgexp,($largeur-$largeurexp)/2, $hauteursup, 0, 0,$largeurexp,$hauteurexp);
$this->image=$imgfin;
$this->base_verticale=$baseexp+$hauteursup;
}

function dessine_dessous($taille) 
{
$this->noeuds[2]->dessine($taille*0.8);
$imginf=$this->noeuds[2]->image;
$baseinf=$this->noeuds[2]->base_verticale;
$this->noeuds[0]->dessine($taille);
$imgexp=$this->noeuds[0]->image;
$baseexp=$this->noeuds[0]->base_verticale;
//expression
$largeurexp=imagesx($imgexp);
$hauteurexp=imagesy($imgexp);
//borneinf
$largeurinf=imagesx($imginf);
$hauteurinf=imagesy($imginf);
//fin
$hauteur=$hauteurexp+$hauteurinf;
$largeur=max($largeurinf,$largeurexp)+ceil($taille/8);
$imgfin = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($imgfin,0,0,0);
$blanc=ImageColorAllocate($imgfin,255,255,255);
$blanc=imagecolortransparent($imgfin,$blanc);
ImageFilledRectangle($imgfin,0,0,$largeur-1,$hauteur-1,$blanc);
ImageCopy($imgfin, $imgexp,($largeur-$largeurexp)/2, 0, 0, 0,$largeurexp,$hauteurexp);
ImageCopy($imgfin, $imginf,($largeur-$largeurinf)/2, $hauteurexp, 0, 0,$largeurinf,$hauteurinf);
$this->image=$imgfin;
$this->base_verticale=$baseexp;
}

function dessine_matrice($taille) 
{
$padding=8;
$nbligne=$this->noeuds[1]->noeuds[0]->texte;
$nbcolonne=$this->noeuds[2]->noeuds[0]->texte;
$largeur_case=0;
$hauteur_case=0;

for($ligne = 0; $ligne <$nbligne; $ligne++)
	{
	$hauteur_ligne[$ligne]=0;
	$dessus_ligne[$ligne]=0;
	}
for($col = 0; $col <$nbcolonne; $col++)
	{
	$largeur_colonne[$col]=0;
	}
$i=0;
for($ligne = 0; $ligne <$nbligne; $ligne++)
	{
	for($col = 0; $col <$nbcolonne; $col++)
		{
		if ($i< count($this->noeuds[3]->noeuds))
			{
			$this->noeuds[3]->noeuds[$i]->dessine($taille*0.9);
			$img[$i]=$this->noeuds[3]->noeuds[$i]->image;
			$base[$i]=$this->noeuds[3]->noeuds[$i]->base_verticale;
			$dessus_ligne[$ligne] = max($base[$i],$dessus_ligne[$ligne]);
			$largeur[$i]=imagesx($img[$i]);
			$hauteur[$i]=imagesy($img[$i]);
			$hauteur_ligne[$ligne]=max($hauteur_ligne[$ligne],$hauteur[$i]);
			$largeur_colonne[$col]=max($largeur_colonne[$col],$largeur[$i]);
			}
		$i++;
		}
 	}

$hauteurfin=0;
$largeurfin=0;
for($ligne = 0; $ligne <$nbligne; $ligne++)
	{
	$hauteurfin+=$hauteur_ligne[$ligne]+$padding;
	}
for($col = 0; $col <$nbcolonne; $col++)
	{
	$largeurfin+=$largeur_colonne[$col]+$padding;
	}
$hauteurfin-=$padding;
$largeurfin-=$padding;
$imgfin = ImageCreate(max($largeurfin,1), max($hauteurfin,1));
$noir=ImageColorAllocate($imgfin,0,0,0);
$blanc=ImageColorAllocate($imgfin,255,255,255);
$blanc=imagecolortransparent($imgfin,$blanc);
ImageFilledRectangle($imgfin,0,0,$largeurfin-1,$hauteurfin-1,$blanc);
$i=0;
$h=$padding/2-1;
for($ligne = 0; $ligne <$nbligne; $ligne++)
	{
	$l=$padding/2-1;
	for($col = 0; $col <$nbcolonne; $col++)
		{
		if ($i< count($this->noeuds[3]->noeuds))
			{
			ImageCopy($imgfin,$img[$i],$l+ceil($largeur_colonne[$col]-$largeur[$i])/2,$h+$dessus_ligne[$ligne]-$base[$i], 0, 0,$largeur[$i],$hauteur[$i]);
			//ImageRectangle($imgfin,$l,$h,$l+$largeur_colonne[$col],$h+$hauteur_ligne[$ligne],$noir);
			}
		$l+=$largeur_colonne[$col]+$padding;
		$i++;
		}
	$h+=$hauteur_ligne[$ligne]+$padding;
 	}
//ImageRectangle($imgfin,0,0,$largeurfin-1,$hauteurfin-1,$noir);
$this->image=$imgfin;
$this->base_verticale=imagesy($imgfin)/2;
}

function dessine_tableau($taille) 
{
$padding=8;
$typeligne=$this->noeuds[1]->noeuds[0]->texte;
$typecolonne=$this->noeuds[2]->noeuds[0]->texte;
$nbligne=strlen($typeligne)-1;
$nbcolonne=strlen($typecolonne)-1;
$largeur_case=0;
$hauteur_case=0;

for($ligne = 0; $ligne <$nbligne; $ligne++)
	{
	$hauteur_ligne[$ligne]=0;
	$dessus_ligne[$ligne]=0;
	}
for($col = 0; $col <$nbcolonne; $col++)
	{
	$largeur_colonne[$col]=0;
	}
$i=0;
for($ligne = 0; $ligne <$nbligne; $ligne++)
	{
	for($col = 0; $col <$nbcolonne; $col++)
		{
		if ($i< count($this->noeuds[3]->noeuds))
			{
			$this->noeuds[3]->noeuds[$i]->dessine($taille*0.9);
			$img[$i]=$this->noeuds[3]->noeuds[$i]->image;
			$base[$i]=$this->noeuds[3]->noeuds[$i]->base_verticale;
			$dessus_ligne[$ligne] = max($base[$i],$dessus_ligne[$ligne]);
			$largeur[$i]=imagesx($img[$i]);
			$hauteur[$i]=imagesy($img[$i]);
			$hauteur_ligne[$ligne]=max($hauteur_ligne[$ligne],$hauteur[$i]);
			$largeur_colonne[$col]=max($largeur_colonne[$col],$largeur[$i]);
			}
		$i++;
		}
 	}

$hauteurfin=0;
$largeurfin=0;
for($ligne = 0; $ligne <$nbligne; $ligne++)
	{
	$hauteurfin+=$hauteur_ligne[$ligne]+$padding;
	}
for($col = 0; $col <$nbcolonne; $col++)
	{
	$largeurfin+=$largeur_colonne[$col]+$padding;
	}
$imgfin = ImageCreate(max($largeurfin,1), max($hauteurfin,1));
$noir=ImageColorAllocate($imgfin,0,0,0);
$blanc=ImageColorAllocate($imgfin,255,255,255);
$blanc=imagecolortransparent($imgfin,$blanc);
ImageFilledRectangle($imgfin,0,0,$largeurfin-1,$hauteurfin-1,$blanc);
$i=0;
$h=$padding/2-1;
if (substr($typeligne,0,1)=="1") ImageLine($imgfin,0,0,$largeurfin-1,0,$noir);
for($ligne = 0; $ligne <$nbligne; $ligne++)
	{
	$l=$padding/2-1;
	if (substr($typecolonne,0,1)=="1") ImageLine($imgfin,0,$h-$padding/2,0,$h+$hauteur_ligne[$ligne]+$padding/2,$noir);
	for($col = 0; $col <$nbcolonne; $col++)
		{
		if ($i< count($this->noeuds[3]->noeuds))
			{
			ImageCopy($imgfin,$img[$i],$l+ceil($largeur_colonne[$col]-$largeur[$i])/2,$h+$dessus_ligne[$ligne]-$base[$i], 0, 0,$largeur[$i],$hauteur[$i]);
			if (substr($typecolonne,$col+1,1)=="1") ImageLine($imgfin,$l+$largeur_colonne[$col]+$padding/2,$h-$padding/2,$l+$largeur_colonne[$col]+$padding/2,$h+$hauteur_ligne[$ligne]+$padding/2,$noir);
			}
		$l+=$largeur_colonne[$col]+$padding;
		$i++;
		}
	if (substr($typeligne,$ligne+1,1)=="1") ImageLine($imgfin,0,$h+$hauteur_ligne[$ligne]+$padding/2,$largeurfin-1,$h+$hauteur_ligne[$ligne]+$padding/2,$noir);
	$h+=$hauteur_ligne[$ligne]+$padding;
 	}
$this->image=$imgfin;
$this->base_verticale=imagesy($imgfin)/2;
}

function dessine_vecteur($taille) 
{
//expression
$this->noeuds[1]->dessine($taille);
$imgexp=$this->noeuds[1]->image;
$baseexp=$this->noeuds[1]->base_verticale;
$largeurexp=imagesx($imgexp);
$hauteurexp=imagesy($imgexp);
//fleche
$imgsup=affiche_symbol("right",16);
$largeursup=imagesx($imgsup);
$hauteursup=imagesy($imgsup);
//fin
$hauteur=$hauteurexp+$hauteursup;
$largeur=$largeurexp;
$imgfin = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($imgfin,0,0,0);
$blanc=ImageColorAllocate($imgfin,255,255,255);
$blanc=imagecolortransparent($imgfin,$blanc);
ImageFilledRectangle($imgfin,0,0,$largeur-1,$hauteur-1,$blanc);
ImageCopy($imgfin, $imgsup,$largeur-6, 0,$largeursup-6, 0,$largeursup,$hauteursup);
imagesetthickness($imgfin,1);
imageline($imgfin,0,6, $largeur-4,6, $noir);
ImageCopy($imgfin, $imgexp,($largeur-$largeurexp)/2, $hauteursup, 0, 0,$largeurexp,$hauteurexp);
$this->image=$imgfin;
$this->base_verticale=$baseexp+$hauteursup;
}

function dessine_overline($taille) 
{
//expression
$this->noeuds[1]->dessine($taille);
$imgexp=$this->noeuds[1]->image;
$baseexp=$this->noeuds[1]->base_verticale;
$largeurexp=imagesx($imgexp);
$hauteurexp=imagesy($imgexp);

$hauteur=$hauteurexp+2;
$largeur=$largeurexp;
$imgfin = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($imgfin,0,0,0);
$blanc=ImageColorAllocate($imgfin,255,255,255);
$blanc=imagecolortransparent($imgfin,$blanc);
ImageFilledRectangle($imgfin,0,0,$largeur-1,$hauteur-1,$blanc);
imagesetthickness($imgfin,1);
imageline($imgfin,0,1, $largeur,1, $noir);
ImageCopy($imgfin, $imgexp,0,2, 0, 0,$largeurexp,$hauteurexp);
$this->image=$imgfin;
$this->base_verticale=$baseexp+2;
}

function dessine_underline($taille) 
{
//expression
$this->noeuds[1]->dessine($taille);
$imgexp=$this->noeuds[1]->image;
$baseexp=$this->noeuds[1]->base_verticale;
$largeurexp=imagesx($imgexp);
$hauteurexp=imagesy($imgexp);

$hauteur=$hauteurexp+2;
$largeur=$largeurexp;
$imgfin = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($imgfin,0,0,0);
$blanc=ImageColorAllocate($imgfin,255,255,255);
$blanc=imagecolortransparent($imgfin,$blanc);
ImageFilledRectangle($imgfin,0,0,$largeur-1,$hauteur-1,$blanc);
imagesetthickness($imgfin,1);
imageline($imgfin,0,$hauteurexp+1, $largeur,$hauteurexp+1, $noir);
ImageCopy($imgfin, $imgexp,0,0, 0, 0,$largeurexp,$hauteurexp);
$this->image=$imgfin;
$this->base_verticale=$baseexp;
}

function dessine_chapeau($taille) 
{

$imgsup=affiche_symbol("_hat",$taille);

$this->noeuds[1]->dessine($taille);
$imgexp=$this->noeuds[1]->image;
$baseexp=$this->noeuds[1]->base_verticale;
//expression
$largeurexp=imagesx($imgexp);
$hauteurexp=imagesy($imgexp);
//bornesup
$largeursup=imagesx($imgsup);
$hauteursup=imagesy($imgsup);
//fin
$hauteur=$hauteurexp+$hauteursup;
$largeur=max($largeursup,$largeurexp)+ceil($taille/8);
$imgfin = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($imgfin,0,0,0);
$blanc=ImageColorAllocate($imgfin,255,255,255);
$blanc=imagecolortransparent($imgfin,$blanc);
ImageFilledRectangle($imgfin,0,0,$largeur-1,$hauteur-1,$blanc);
ImageCopy($imgfin, $imgsup,($largeur-$largeursup)/2, 0, 0, 0,$largeursup,$hauteursup);
ImageCopy($imgfin, $imgexp,($largeur-$largeurexp)/2, $hauteursup, 0, 0,$largeurexp,$hauteurexp);
$this->image=$imgfin;
$this->base_verticale=$baseexp+$hauteursup;
}

function dessine_limite($taille) 
{
$imglim=affiche_math("_lim",$taille);
$largeurlim=imagesx($imglim);
$hauteurlim=imagesy($imglim);
$baselim=$hauteurlim/2;

$this->noeuds[1]->dessine($taille*0.8);
$imginf=$this->noeuds[1]->image;
$baseinf=$this->noeuds[1]->base_verticale;
$largeurinf=imagesx($imginf);
$hauteurinf=imagesy($imginf);

$this->noeuds[2]->dessine($taille);
$imgexp=$this->noeuds[2]->image;
$baseexp=$this->noeuds[2]->base_verticale;
$largeurexp=imagesx($imgexp);
$hauteurexp=imagesy($imgexp);

$hauteur=$hauteurlim+$hauteurinf;
$largeur=max($largeurinf,$largeurlim)+ceil($taille/8);
$imgfin = ImageCreate(max($largeur,1), max($hauteur,1));
$noir=ImageColorAllocate($imgfin,0,0,0);
$blanc=ImageColorAllocate($imgfin,255,255,255);
$blanc=imagecolortransparent($imgfin,$blanc);
ImageFilledRectangle($imgfin,0,0,$largeur-1,$hauteur-1,$blanc);
ImageCopy($imgfin, $imglim,($largeur-$largeurlim)/2, 0, 0, 0,$largeurlim,$hauteurlim);
ImageCopy($imgfin, $imginf,($largeur-$largeurinf)/2, $hauteurlim, 0, 0,$largeurinf,$hauteurinf);

$this->image=alignement2($imgfin,$baselim,$imgexp,$baseexp);
$this->base_verticale=max($baselim,$baseexp);
}

function dessine_delimiteur($taille) 
{
$this->noeuds[2]->dessine($taille);
$imgexp=$this->noeuds[2]->image;
$baseexp=$this->noeuds[2]->base_verticale;
$hauteurexp=imagesy($imgexp);
if ($this->noeuds[1]->texte=="&$") $imggauche=parenthese($hauteurexp,$this->noeuds[1]->noeuds[0]->texte);
else $imggauche=parenthese($hauteurexp,$this->noeuds[1]->texte);
$basegauche=imagesy($imggauche)/2;
if ($this->noeuds[3]->texte=="&$") $imgdroit=parenthese($hauteurexp,$this->noeuds[3]->noeuds[0]->texte);
else $imgdroit=parenthese($hauteurexp,$this->noeuds[3]->texte);
$basedroit=imagesy($imgdroit)/2;
$this->image=alignement3($imggauche,$basegauche,$imgexp,$baseexp,$imgdroit,$basedroit);
$this->base_verticale=max($basegauche,$baseexp,$basedroit);
}
}
//******************************************************************************************

function detectimg($n)
{
/*
Detects if the formula image already exists in the $dirimg cache directory. 
In that case, the function returns a parameter (recorded in the name of the image file) which allows to align correctly the image with the text.
*/
global $dirimg;
$ret=0;
$handle=opendir($dirimg);
while ($fi = readdir($handle))
	{
	$info=pathinfo($fi);
	if ($fi!="." && $fi!=".." && $info["extension"]=="png" && ereg("^math",$fi)) 
		{
		list($math,$v,$name)=explode("_",$fi);
		if ($name==$n) 
			{
			$ret=$v;
			break;
			}
		}
	}
closedir($handle);
return $ret;
}

function mathimage($text,$size,$pathtoimg)
{
/*
Creates the formula image (if the image is not in the cache) and returns the <img src=...></img> html code.
*/
global $dirimg;
$nameimg = md5(trim($text).$size).'.png';
$v=detectimg($nameimg);
if ($v==0)
	{
	//the image doesn't exist in the cache directory. we create it.
	$formula=new expression_math(tableau_expression(trim($text)));
	$formula->dessine($size);
	$v=1000-imagesy($formula->image)+$formula->base_verticale+3;
	//1000+baseline ($v) is recorded in the name of the image
	ImagePNG($formula->image,$dirimg."/math_".$v."_".$nameimg);
	}
$valign=$v-1000;
return '<img src="'.$pathtoimg."math_".$v."_".$nameimg.'" style="vertical-align:'.$valign.'px;'.' display: inline-block ;" alt="'.htmlentities($text).'" title="'.htmlentities($text).'"/>';
}


function mathfilter($text,$size,$pathtoimg) 
{
/* THE MAIN FUNCTION
1) the content of the math tags (<m></m>) are extracted in the $t variable (you can replace <m></m> by your own tag).
2) the "mathimage" function replaces the $t code by <img src=...></img> according to this method :
- if the image corresponding to the formula doesn't exist in the $dirimg cache directory (detectimg($nameimg)=0), the script creates the image and returns the "<img src=...></img>" code.
- otherwise, the script returns only the <img src=...></img>" code.
To align correctly the formula image with the text, the "valign" parameter of the image is required.
That's why a parameter (1000+valign) is recorded in the name of the image file (the "detectimg" function returns this parameter if the image exists in the cache directory)
To be sure that the name of the image file is unique and to allow the script to retrieve the valign parameter without re-creating the image, the syntax of the image filename is :
math_(1000+valign)_md5(formulatext.size).png.
(1000+valign is used instead of valign directly to avoid a negative number)
*/
$text=stripslashes($text);
$size=max($size,10);
$size=min($size,24);
preg_match_all("|<m>(.*?)</m>|", $text, $regs, PREG_SET_ORDER);
foreach ($regs as $math) 
	{
	$t=str_replace('<m>','',$math[0]);
	$t=str_replace('</m>','',$t);
	$code=mathimage(trim($t),$size,$pathtoimg);
	$text = str_replace($math[0], $code, $text);
	}	
return $text;
}

?>

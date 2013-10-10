<?php
class homemanager extends object {
    function init() {
        $this->objAltConfig = $this->getObject('altconfig','config');

    }

    function show() {

        $siteRoot=$this->objAltConfig->getsiteRoot();
        $skinUri=$this->objAltConfig->getskinRoot();

        $imgPath=$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif';
        $img ='<img  width="1" height="1" border="0" alt="" src="'.$imgPath.'">';

        $table=$this->getObject('htmltable','htmlelements');

        $table->cellspacing = '0';
        $table->width='1024';
        $max=41;
        //row 1

        $table->startRow();
        for($i=0;$i<$max;$i++) {

            $table->addCell($img);
        }
        $table->endRow();

        //row 2
        $table->startRow();
        $imgPath=$siteRoot."/".$skinUri.'/vowfm/images/current_r1_c1.gif';
        $img ='<img name="current_r1_c1" width="1024" height="9" border="0" id="current_r1_c1" alt=""  src="'.$imgPath.'">';
        $table->addCell($img,null,null,null,null,'colspan="41"');
        $imgPath=$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif';
        $img ='<img width="1" height="9" border="0" alt=""   src="'.$imgPath.'">';
        $table->addCell($img);
        $table->endRow();


        //row 3
        /**
         *   <tr>
         <td rowspan="3" colspan="4"><img name="current_r2_c1" src="images/current_r2_c1.gif" width="183" height="212" border="0" id="current_r2_c1" alt="" /></td>
         <td colspan="7"><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu('MMMenuContainer0405165335_0', 'MMMenu0405165335_0',15,50,'current_r2_c5');MM_swapImage('current_r2_c5','','images/current_r2_c5_f2.gif',1);"><img name="current_r2_c5" src="images/current_r2_c5.gif" width="91" height="59" border="0" id="current_r2_c5" alt="" /></a></td>

         *
         *  <td colspan="5"><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_nbGroup('out');MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu('MMMenuContainer0405165335_1', 'MMMenu0405165335_1',0,50,'current_r2_c12');MM_nbGroup('over','current_r2_c12','images/current_r2_c12_f2.gif','images/current_r2_c12_f3.gif',1);MM_swapImage('current_r2_c12','','images/current_r2_c12_f2.gif',1);" onclick="MM_nbGroup('down','navbar1','current_r2_c12','images/current_r2_c12_f3.gif',1);"><img name="current_r2_c12" src="images/current_r2_c12_f3.gif" width="115" height="59" border="0" id="current_r2_c12" alt=""onload="MM_nbGroup('init','navbar1', 'current_r2_c12','images/current_r2_c12.gif',1)" /></a></td>
         <td colspan="5"><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu('MMMenuContainer0405165335_2', 'MMMenu0405165335_2',0,50,'current_r2_c17');MM_swapImage('current_r2_c17','','images/current_r2_c17_f2.gif',1);"><img name="current_r2_c17" src="images/current_r2_c17.gif" width="131" height="59" border="0" id="current_r2_c17" alt="" /></a></td>
         <td colspan="6"><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu('MMMenuContainer0405165335_3', 'MMMenu0405165335_3',0,50,'current_r2_c22');MM_swapImage('current_r2_c22','','images/current_r2_c22_f2.gif',1);"><img name="current_r2_c22" src="images/current_r2_c22.gif" width="133" height="59" border="0" id="current_r2_c22" alt="" /></a></td>
         <td colspan="4"><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('current_r2_c28','','images/current_r2_c28_f2.gif',1);"><img name="current_r2_c28" src="images/current_r2_c28.gif" width="79" height="59" border="0" id="current_r2_c28" alt="" /></a></td>
         <td colspan="5"><a href="advertise.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('current_r2_c32','','images/current_r2_c32_f2.gif',1);"><img name="current_r2_c32" src="images/current_r2_c32.gif" width="90" height="59" border="0" id="current_r2_c32" alt="" /></a></td>
         <td><a href="contactus.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('current_r2_c37','','images/current_r2_c37_f2.gif',1);"><img name="current_r2_c37" src="images/current_r2_c37.gif" width="92" height="59" border="0" id="current_r2_c37" alt="" /></a></td>
         <td colspan="2"><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu('MMMenuContainer0405165335_4', 'MMMenu0405165335_4',-200,50,'current_r2_c38');MM_swapImage('current_r2_c38','','images/current_r2_c38_f2.gif',1);"><img name="current_r2_c38" src="images/current_r2_c38.gif" width="69" height="59" border="0" id="current_r2_c38" alt="" /></a></td>
         <td rowspan="22" colspan="2"><img name="current_r2_c40" src="images/current_r2_c40.gif" width="41" height="644" border="0" id="current_r2_c40" usemap="#m_current_r2_c40" alt="" /></td>
         <td><img src="images/spacer.gif" width="1" height="59" border="0" alt="" /></td>
         </tr>
         */
        $table->startRow();
        $imgPath=$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c1.gif';
        $img ='<img name="current_r2_c1" width="183" height="212" border="0" id="current_r2_c1" alt=""   src="'.$imgPath.'">';
        $table->addCell($img,null,null,null,null,'rowspan="3" colspan="4"');
        $imgPath=$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif';
        
        $imgPath1=$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c5_f2.gif';
        $imgPath2=$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c5.gif';
        $imgPath3=$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f2.gif';
        $imgPath4=$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f3.gif';
        $imgPath5=$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f2.gif';
        $imgPath6=$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f3.gif';
        $imgPath7=$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f3.gif';
        $imgPath8=$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12.gif';

        $val="<a href=\"javascript:;\"
            onmouseout=\"MM_swapImgRestore();MM_menuStartTimeout(250);\"
            onmouseover=\"MM_menuShowMenu('MMMenuContainer0405165335_0', 'MMMenu0405165335_0',15,50,'current_r2_c5');MM_swapImage('current_r2_c5','','".$imgPath1."',1);\">
            <img name=\"current_r2_c5\" src=\"".$imgPath2."\" width=\"91\" height=\"59\" border=\"0\" id=\"current_r2_c5\" alt=\"\" />
            </a>";
        $table->addCell($val,null,null,null,null,'colspan="7"');
        $val="
         <a href=\"javascript:;\"
         onmouseout=\"MM_swapImgRestore();MM_nbGroup('out');MM_menuStartTimeout(250);\"
         onmouseover=\"MM_menuShowMenu('MMMenuContainer0405165335_1', 'MMMenu0405165335_1',0,50,'current_r2_c12');MM_nbGroup('over','current_r2_c12','".$imgPath3."','".$imgPath4."',1);MM_swapImage('current_r2_c12','','".$imgPath5."',1);\"
         onclick=\"MM_nbGroup('down','navbar1','current_r2_c12','".$imgPath6."',1);\">
         <img name=\"current_r2_c12\" src=\"".$imgPath7."\" width=\"115\" height=\"59\" border=\"0\" id=\"current_r2_c12\" alt=\"\"
         onload=\"MM_nbGroup('init','navbar1', 'current_r2_c12','".$imgPath8."',1)\" />
         </a>
           ";
        $table->addCell($val,null,null,null,null,'colspan="5"');

        $table->endRow();

        return $table->show();

    }

}
?>

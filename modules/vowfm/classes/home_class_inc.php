<?php
class home extends object {
    public  function  init() {
        $this->objAltConfig = $this->getObject('altconfig','config');
    }
    function show() {

        $siteRoot=$this->objAltConfig->getsiteRoot();
        $skinUri=$this->objAltConfig->getskinRoot();
        $imgPath=$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif';
        $objCategories=$this->getObject("dbnewscategories","news");
        $news=$this->getObject("dbnewsstories","news");
        $categories=$objCategories->getCategories();

        foreach ($categories as $cat){
            
            if($cat['categoryname'] == 'on air now'){
                  $onAirNowId=$cat['id'];
                  $onAirNowStories=$news->getCategoryStories($onAirNowId);
                  $currentShow=$onAirNowStories[0]['storytext'];
            }
        }
        
       $homePagePic= '<img name="current_r4_c9" src="'.$siteRoot."/".$skinUri.'/vowfm/images/cxurrent_r4_c9.gif" width="553" height="43" border="0" id="current_r4_c9" alt="" ';
        $content='


  <ul>
   
   <li><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu(\'MMMenuContainer0405165335_0\', \'MMMenu0405165335_0\',15,50,\'current_r2_c5\');MM_swapImage(\'current_r2_c5\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c5_f2.gif\',1);"><img name="current_r2_c5" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c5.gif" width="91" height="59" border="0" id="current_r2_c5" alt="" /></a></td>
   <li><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_nbGroup(\'out\');MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu(\'MMMenuContainer0405165335_1\', \'MMMenu0405165335_1\',0,50,\'current_r2_c12\');MM_nbGroup(\'over\',\'current_r2_c12\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f2.gif\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f3.gif\',1);MM_swapImage(\'current_r2_c12\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f2.gif\',1);" onclick="MM_nbGroup(\'down\',\'navbar1\',\'current_r2_c12\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f3.gif\',1);"><img name="current_r2_c12" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12_f3.gif" width="115" height="59" border="0" id="current_r2_c12" alt=""onload="MM_nbGroup(\'init\',\'navbar1\', \'current_r2_c12\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c12.gif\',1)" /></a></td>
   <li><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu(\'MMMenuContainer0405165335_2\', \'MMMenu0405165335_2\',0,50,\'current_r2_c17\');MM_swapImage(\'current_r2_c17\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c17_f2.gif\',1);"><img name="current_r2_c17" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c17.gif" width="131" height="59" border="0" id="current_r2_c17" alt="" /></a></td>
   <li><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu(\'MMMenuContainer0405165335_3\', \'MMMenu0405165335_3\',0,50,\'current_r2_c22\');MM_swapImage(\'current_r2_c22\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c22_f2.gif\',1);"><img name="current_r2_c22" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c22.gif" width="133" height="59" border="0" id="current_r2_c22" alt="" /></a></td>
   <li><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r2_c28\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c28_f2.gif\',1);"><img name="current_r2_c28" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c28.gif" width="79" height="59" border="0" id="current_r2_c28" alt="" /></a></td>
   <li><a href="advertise.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r2_c32\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c32_f2.gif\',1);"><img name="current_r2_c32" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c32.gif" width="90" height="59" border="0" id="current_r2_c32" alt="" /></a></td>
   <li><a href="contactus.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r2_c37\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c37_f2.gif\',1);"><img name="current_r2_c37" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c37.gif" width="92" height="59" border="0" id="current_r2_c37" alt="" /></a></td>
   <li><a href="javascript:;" onmouseout="MM_swapImgRestore();MM_menuStartTimeout(250);" onmouseover="MM_menuShowMenu(\'MMMenuContainer0405165335_4\', \'MMMenu0405165335_4\',-200,50,\'current_r2_c38\');MM_swapImage(\'current_r2_c38\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c38_f2.gif\',1);"><img name="current_r2_c38" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c38.gif" width="69" height="59" border="0" id="current_r2_c38" alt="" /></a></li>
   <li><img name="current_r2_c40" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r2_c40.gif" width="41" height="644" border="0" id="current_r2_c40" usemap="#m_current_r2_c40" alt="" /></</li>
  </ul>
';
$str='
<tr>
   <td colspan="4"><img name="current_r4_c5" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r4_c5.gif" width="12" height="3" border="0" id="current_r4_c5" alt="" /></td>
   <td rowspan="3" colspan="25"><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r4_c9\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r4_c9_f2.gif\',1);">'.$homePagePic.'/></a></td>
   <td rowspan="20" colspan="6"><img name="current_r4_c34" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r4_c34.gif" width="235" height="435" border="0" id="current_r4_c34" usemap="#m_current_r4_c34" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="3" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="8"><img name="competitionsleft4_r1_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/competitionsleft4_r1_c1.gif" width="1" height="190" border="0" id="competitionsleft4_r1_c1" alt="" /></td>
   <td colspan="2"><img name="current_r5_c2" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r5_c2.gif" width="181" height="2" border="0" id="current_r5_c2" alt="" /></td>
   <td rowspan="8" colspan="2"><img name="competitionsleft4_r1_c3" src="'.$siteRoot."/".$skinUri.'/vowfm/images/competitionsleft4_r1_c3.gif" width="7" height="190" border="0" id="competitionsleft4_r1_c3" alt="" /></td>
   <td rowspan="11" colspan="3"><img name="current_r5_c6" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r5_c6.gif" width="6" height="259" border="0" id="current_r5_c6" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="2" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="3" colspan="2"><a href="current.html" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'competitionsleft4_r1_c2\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/competitionsleft4_r1_c2_f2.gif\',1);"><img name="competitionsleft4_r1_c2" src="'.$siteRoot."/".$skinUri.'/vowfm/images/competitionsleft4_r1_c2.gif" width="181" height="43" border="0" id="competitionsleft4_r1_c2" alt="" /></a></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="38" border="0" alt="" /></td>
  </tr>
  <tr>
   
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="3" border="0" alt="" /></td>
  </tr>
  <tr>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="2" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="4" colspan="2"><img name="current_r9_c2" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r9_c2.gif" width="181" height="145" border="0" id="current_r9_c2" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="43" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="6" colspan="18"><img name="current_r10_c9" src="" width="446" height="171" border="0" id="current_r10_c9" alt="" /></td>
   <td colspan="3"><a href="enter.html" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r10_c27\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r10_c27_f2.gif\',1);"><img name="current_r10_c27" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r10_c27.gif" width="66" height="41" border="0" id="current_r10_c27" alt="" /></a></td>
   <td rowspan="11" colspan="4"><img name="current_r10_c30" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r10_c30.gif" width="41" height="284" border="0" id="current_r10_c30" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="41" border="0" alt="" /></td>
  </tr>
  <tr>
   <td colspan="3"><img name="current_r11_c27" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r11_c27.gif" width="66" height="35" border="0" id="current_r11_c27" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="35" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="3"><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r12_c27\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r12_c27_f2.gif\',1);"><img name="current_r12_c27" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r12_c27.gif" width="66" height="41" border="0" id="current_r12_c27" alt="" /></a></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="26" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="3" colspan="5"><img name="current_r13_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r13_c1.gif" width="189" height="69" border="0" id="current_r13_c1" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="15" border="0" alt="" /></td>
  </tr>
  <tr>
   <td colspan="3"><img name="current_r14_c27" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r14_c27.gif" width="66" height="34" border="0" id="current_r14_c27" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="34" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="3"><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r15_c27\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r15_c27_f2.gif\',1);"><img name="current_r15_c27" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r15_c27.gif" width="66" height="41" border="0" id="current_r15_c27" alt="" /></a></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="20" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="6"><a href="news.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'Twitternewsfeed_r1_c1\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/Twitternewsfeed_r1_c1_f2.gif\',1);"><img name="Twitternewsfeed_r1_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Twitternewsfeed_r1_c1.gif" width="190" height="43" border="0" id="Twitternewsfeed_r1_c1" alt="" /></a></td>
   <td rowspan="11" colspan="3"><img name="Twitternewsfeed_r1_c3" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Twitternewsfeed_r1_c3.gif" width="7" height="359" border="0" id="Twitternewsfeed_r1_c3" alt="" /></td>
   <td rowspan="5" colspan="17"><img name="current_r16_c10" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r16_c10.gif" width="444" height="113" border="0" id="current_r16_c10" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="21" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="3"><img name="current_r17_c27" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r17_c27.gif" width="66" height="35" border="0" id="current_r17_c27" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="22" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="9" colspan="6"><img name="Twitternewsfeed_r2_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Twitternewsfeed_r2_c1.gif" width="190" height="316" border="0" id="Twitternewsfeed_r2_c1" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="13" border="0" alt="" /></td>
  </tr>
  <tr>
   <td colspan="3"><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r19_c27\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r19_c27_f2.gif\',1);"><img name="current_r19_c27" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r19_c27.gif" width="66" height="41" border="0" id="current_r19_c27" alt="" /></a></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="41" border="0" alt="" /></td>
  </tr>
  <tr>
   <td colspan="3"><img name="current_r20_c27" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r20_c27.gif" width="66" height="16" border="0" id="current_r20_c27" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="16" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="4"><img name="current_r21_c10" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r21_c10.gif" width="100" height="59" border="0" id="current_r21_c10" alt="" /></td>
   <td><a href="rules.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r21_c14\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r21_c14_f2.gif\',1);"><img name="current_r21_c14" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r21_c14.gif" width="58" height="41" border="0" id="current_r21_c14" alt="" /></a></td>
   <td colspan="4"><a href="past.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r21_c15\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r21_c15_f2.gif\',1);"><img name="current_r21_c15" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r21_c15.gif" width="136" height="41" border="0" id="current_r21_c15" alt="" /></a></td>
   <td rowspan="2" colspan="6"><img name="current_r21_c19" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r21_c19.gif" width="102" height="59" border="0" id="current_r21_c19" alt="" /></td>
   <td colspan="6"><a href="upcoming.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'current_r21_c25\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/current_r21_c25_f2.gif\',1);"><img name="current_r21_c25" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r21_c25.gif" width="136" height="41" border="0" id="current_r21_c25" alt="" /></a></td>
   <td rowspan="3" colspan="3"><img name="current_r21_c31" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r21_c31.gif" width="19" height="60" border="0" id="current_r21_c31" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="41" border="0" alt="" /></td>
  </tr>
  <tr>
   <td colspan="5"><img name="current_r22_c14" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r22_c14.gif" width="194" height="18" border="0" id="current_r22_c14" alt="" /></td>
   <td rowspan="2" colspan="6"><img name="current_r22_c25" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r22_c25.gif" width="136" height="19" border="0" id="current_r22_c25" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="18" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="8"><img name="current_r23_c10" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r23_c10.gif" width="11" height="268" border="0" id="current_r23_c10" alt="" /></td>
   <td rowspan="2" colspan="10"><a href="newmusic.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'newmusicbox2_r1_c1\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/newmusicbox2_r1_c1_f2.gif\',1);"><img name="newmusicbox2_r1_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/newmusicbox2_r1_c1.gif" width="300" height="41" border="0" id="newmusicbox2_r1_c1" alt="" /></a></td>
   <td colspan="4"><img name="current_r23_c21" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r23_c21.gif" width="85" height="1" border="0" id="current_r23_c21" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="1" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="7" colspan="2"><img name="current_r24_c21" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r24_c21.gif" width="18" height="267" border="0" id="current_r24_c21" alt="" /></td>
   <td rowspan="2" colspan="12"><a href="top40.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'Chartsbox_r1_c1\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r1_c1_f2.gif\',1);"><img name="Chartsbox_r1_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r1_c1.gif" width="223" height="41" border="0" id="Chartsbox_r1_c1" alt="" /></a></td>
   <td rowspan="7"><img name="current_r24_c35" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r24_c35.gif" width="16" height="267" border="0" id="current_r24_c35" alt="" /></td>
   <td rowspan="2" colspan="5"><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'downloadsbox_r1_c1\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/downloadsbox_r1_c1_f2.gif\',1);"><img name="downloadsbox_r1_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/downloadsbox_r1_c1.gif" width="223" height="41" border="0" id="downloadsbox_r1_c1" alt="" /></a></td>
   <td rowspan="7"><img name="current_r24_c41" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r24_c41.gif" width="36" height="267" border="0" id="current_r24_c41" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="40" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="10"><img name="newmusicbox2_r2_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/newmusicbox2_r2_c1.gif" width="300" height="146" border="0" id="newmusicbox2_r2_c1" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="1" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="12"><img name="Chartsbox_r2_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r2_c1.gif" width="223" height="146" border="0" id="Chartsbox_r2_c1" alt="" /></td>
   <td rowspan="2" colspan="5"><img name="downloadsbox_r2_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/downloadsbox_r2_c1.gif" width="223" height="146" border="0" id="downloadsbox_r2_c1" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="145" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="2"><img name="Twitternewsfeed_r3_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Twitternewsfeed_r3_c1.gif" width="127" height="34" border="0" id="Twitternewsfeed_r3_c1" alt="" /></td>
   <td rowspan="2" colspan="5"><a href="news.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'Twitternewsfeed_r3_c2\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/Twitternewsfeed_r3_c2_f2.gif\',1);"><img name="Twitternewsfeed_r3_c2" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Twitternewsfeed_r3_c2.gif" width="66" height="34" border="0" id="Twitternewsfeed_r3_c2" alt="" /></a></td>
   <td rowspan="2" colspan="2"><img name="Twitternewsfeed_r3_c4" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Twitternewsfeed_r3_c4.gif" width="4" height="34" border="0" id="Twitternewsfeed_r3_c4" alt="" /></td>
   <td rowspan="3" colspan="2"><img name="newmusicbox2_r3_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/newmusicbox2_r3_c1.gif" width="87" height="38" border="0" id="newmusicbox2_r3_c1" alt="" /></td>
   <td rowspan="3" colspan="5"><a href="submitmusic.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'newmusicbox2_r3_c2\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/newmusicbox2_r3_c2_f2.gif\',1);"><img name="newmusicbox2_r3_c2" src="'.$siteRoot."/".$skinUri.'/vowfm/images/newmusicbox2_r3_c2.gif" width="136" height="38" border="0" id="newmusicbox2_r3_c2" alt="" /></a></td>
   <td rowspan="3" colspan="2"><a href="newmusic.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'newmusicbox2_r3_c4\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/newmusicbox2_r3_c4_f2.gif\',1);"><img name="newmusicbox2_r3_c4" src="'.$siteRoot."/".$skinUri.'/vowfm/images/newmusicbox2_r3_c4.gif" width="63" height="38" border="0" id="newmusicbox2_r3_c4" alt="" /></a></td>
   <td rowspan="3"><img name="newmusicbox2_r3_c5" src="'.$siteRoot."/".$skinUri.'/vowfm/images/newmusicbox2_r3_c5.gif" width="14" height="38" border="0" id="newmusicbox2_r3_c5" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="1" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2"><img name="Chartsbox_r3_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r3_c1.gif" width="15" height="37" border="0" id="Chartsbox_r3_c1" alt="" /></td>
   <td rowspan="2" colspan="2"><a href="top40.htm" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'Chartsbox_r3_c2\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r3_c2_f2.gif\',1);"><img name="Chartsbox_r3_c2" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r3_c2.gif" width="64" height="37" border="0" id="Chartsbox_r3_c2" alt="" /></a></td>
   <td rowspan="2" colspan="3"><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'Chartsbox_r3_c3\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r3_c3_f2.gif\',1);"><img name="Chartsbox_r3_c3" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r3_c3.gif" width="62" height="37" border="0" id="Chartsbox_r3_c3" alt="" /></a></td>
   <td rowspan="2" colspan="4"><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'Chartsbox_r3_c5\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r3_c5_f2.gif\',1);"><img name="Chartsbox_r3_c5" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r3_c5.gif" width="66" height="37" border="0" id="Chartsbox_r3_c5" alt="" /></a></td>
   <td rowspan="2" colspan="2"><img name="Chartsbox_r3_c7" src="'.$siteRoot."/".$skinUri.'/vowfm/images/Chartsbox_r3_c7.gif" width="16" height="37" border="0" id="Chartsbox_r3_c7" alt="" /></td>
   <td rowspan="2" colspan="3"><img name="downloadsbox_r3_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/downloadsbox_r3_c1.gif" width="157" height="37" border="0" id="downloadsbox_r3_c1" alt="" /></td>
   <td rowspan="2" colspan="2"><a href="javascript:;" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage(\'downloadsbox_r3_c2\',\'\',\''.$siteRoot."/".$skinUri.'/vowfm/images/downloadsbox_r3_c2_f2.gif\',1);"><img name="downloadsbox_r3_c2" src="'.$siteRoot."/".$skinUri.'/vowfm/images/downloadsbox_r3_c2.gif" width="66" height="37" border="0" id="downloadsbox_r3_c2" alt="" /></a></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="33" border="0" alt="" /></td>
  </tr>
  <tr>
   <td rowspan="2" colspan="9"><img name="current_r29_c1" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r29_c1.gif" width="197" height="47" border="0" id="current_r29_c1" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="4" border="0" alt="" /></td>
  </tr>
  <tr>
   <td colspan="10"><img name="current_r30_c11" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r30_c11.gif" width="300" height="43" border="0" id="current_r30_c11" alt="" /></td>
   <td colspan="12"><img name="current_r30_c23" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r30_c23.gif" width="223" height="43" border="0" id="current_r30_c23" alt="" /></td>
   <td colspan="5"><img name="current_r30_c36" src="'.$siteRoot."/".$skinUri.'/vowfm/images/current_r30_c36.gif" width="223" height="43" border="0" id="current_r30_c36" alt="" /></td>
   <td><img src="'.$siteRoot."/".$skinUri.'/vowfm/images/spacer.gif" width="1" height="43" border="0" alt="" /></td>
  </tr>
</table>
<map name="m_current_r2_c40" id="m_current_r2_c40">
<area shape="rect" coords="-223,213,7,623" href="jamesbond.htm" alt="" />
</map>
<map name="m_current_r4_c34" id="m_current_r4_c34">
<area shape="rect" coords="12,4,242,414" href="jamesbond.htm" alt="" />
</map>
<div id="MMMenuContainer0405165335_0">
	<div id="MMMenu0405165335_0" onmouseout="MM_menuStartTimeout(250);" onmouseover="MM_menuResetTimeout();">
		<a href="newmusic.htm" id="MMMenu0405165335_0_Item_0" class="MMMIFHStyleMMMenu0405165335_0" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_0\');">
			New&nbsp;Music
		</a>
		<a href="top40.htm" id="MMMenu0405165335_0_Item_1" class="MMMIHStyleMMMenu0405165335_0" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_0\');">
			Charts
		</a>
		<a href="submitmusic.htm" id="MMMenu0405165335_0_Item_2" class="MMMIHStyleMMMenu0405165335_0" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_0\');">
			Submissions
		</a>
		<a href="musicquery.htm" id="MMMenu0405165335_0_Item_3" class="MMMIHStyleMMMenu0405165335_0" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_0\');">
			Queries
		</a>
		<a href="playlist.htm" id="MMMenu0405165335_0_Item_4" class="MMMIHStyleMMMenu0405165335_0" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_0\');">
			Our&nbsp;Playlist
		</a>
	</div>
</div>
<div id="MMMenuContainer0405165335_1">
	<div id="MMMenu0405165335_1" onmouseout="MM_menuStartTimeout(250);" onmouseover="MM_menuResetTimeout();">
		<a href="showlineup.htm" id="MMMenu0405165335_1_Item_0" class="MMMIFHStyleMMMenu0405165335_1" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_1\');">
			Show&nbsp;Lineup
		</a>
		<a href="DJs.htm" id="MMMenu0405165335_1_Item_1" class="MMMIHStyleMMMenu0405165335_1" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_1\');">
			DJ&nbsp;Pages
		</a>
		<a href="djcontact.htm" id="MMMenu0405165335_1_Item_2" class="MMMIHStyleMMMenu0405165335_1" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_1\');">
			DJ&nbsp;Contact
		</a>
	</div>
</div>
<div id="MMMenuContainer0405165335_2">
	<div id="MMMenu0405165335_2" onmouseout="MM_menuStartTimeout(250);" onmouseover="MM_menuResetTimeout();">
		<a href="current.html" id="MMMenu0405165335_2_Item_0" class="MMMIFHStyleMMMenu0405165335_2" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_2\');">
			Current
		</a>
		<a href="past.htm" id="MMMenu0405165335_2_Item_1" class="MMMIHStyleMMMenu0405165335_2" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_2\');">
			Past
		</a>
		<a href="upcoming.htm" id="MMMenu0405165335_2_Item_2" class="MMMIHStyleMMMenu0405165335_2" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_2\');">
			Upcoming
		</a>
		<a href="rules.htm" id="MMMenu0405165335_2_Item_3" class="MMMIHStyleMMMenu0405165335_2" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_2\');">
			Rules
		</a>
	</div>
</div>
<div id="MMMenuContainer0405165335_3">
	<div id="MMMenu0405165335_3" onmouseout="MM_menuStartTimeout(250);" onmouseover="MM_menuResetTimeout();">
		<a href="upcomingevents.htm" id="MMMenu0405165335_3_Item_0" class="MMMIFHStyleMMMenu0405165335_3" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_3\');">
			Upcoming&nbsp;Events
		</a>
		<a href="eventguide.htm" id="MMMenu0405165335_3_Item_1" class="MMMIHStyleMMMenu0405165335_3" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_3\');">
			Event&nbsp;Guide
		</a>
		<a href="submitevent.htm" id="MMMenu0405165335_3_Item_2" class="MMMIHStyleMMMenu0405165335_3" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_3\');">
			Submit&nbsp;an&nbsp;Event
		</a>
		<a href="eventgalleries.htm" id="MMMenu0405165335_3_Item_3" class="MMMIHStyleMMMenu0405165335_3" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_3\');">
			Event&nbsp;Galleries
		</a>
	</div>
</div>
<div id="MMMenuContainer0405165335_4">
	<div id="MMMenu0405165335_4" onmouseout="MM_menuStartTimeout(250);" onmouseover="MM_menuResetTimeout();">
		<a href="vowjobs.htm" id="MMMenu0405165335_4_Item_0" class="MMMIFHStyleMMMenu0405165335_4" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_4\');">
			VOW
		</a>
		<a href="studentjobs.htm" id="MMMenu0405165335_4_Item_1" class="MMMIHStyleMMMenu0405165335_4" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_4\');">
			Student&nbsp;Jobs
		</a>
		<a href="services.htm" id="MMMenu0405165335_4_Item_2" class="MMMIHStyleMMMenu0405165335_4" onmouseover="MM_menuOverMenuItem(\'MMMenu0405165335_4\');">
			Services
		</a>
	</div>
</div>
</div>



';

        return "<center>$content</center>";
    }
}

?>

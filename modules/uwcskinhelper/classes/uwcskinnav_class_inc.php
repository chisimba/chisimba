<?php
/**
 * 
 * Provides functionality to help with the UWC skin design, content and layout.
 * 
 * The uwcskinghelper module provides functionality to help with the UWC skin design, content and layout. This includes additional navigation and content features, as well as faculty specific navigation items.
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   helloforms
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: uwcskinnav_class_inc.php 11931 2008-12-29 21:16:26Z charlvn $
 * @link      http://avoir.uwc.ac.za
 */
 
// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 * 
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *         
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
* 
* Controller class for Chisimba for the module uwcskinhelper
*
* @author Derek Keats
* @package uwcskinhelper
*
*/
class uwcskinnav extends dbtable
{
    
    /**
    * 
    * Intialiser for the uwcskinhelper controller
    * @access public
    * 
    */
    public function init()
    {
        
    }
    
    public function topNav()
    {
        return '
            <table border="0" cellspacing="0" cellpadding="0" width="745" height="15">
            <tr>
              <td background="../../_template/background/white.png" valign="top" align="right">
              <font color="black" size="1">
          &nbsp;<a href="../../public/portal_desktop/sitemap.htm"><font color="black" size="1">A-Z List</font></a>

          |&nbsp;<a href="../../public/portal_desktop/news.htm"><font color="black" size="1">News</font></a>
          |&nbsp;<a href="../../public/portal_desktop/map.htm"><font color="black" size="1">Map</font></a>
          |&nbsp;<a href="../../public/portal_desktop/calendar.htm"><font color="black" size="1">Calendar</font></a>
          |&nbsp;<a href="../../public/portal_desktop/online_services.htm"><font color="black" size="1">Online Services</font></a>
          |&nbsp;<a href="../../public/portal_desktop/email.htm"><font color="black" size="1">E-Mail</font></a>

          |&nbsp;<a href="../../public/portal_desktop/elearning.htm"><font color="black" size="1">E-Learning</font></a>
          |&nbsp;<a href="../../public/portal_desktop/download.htm"><font color="black" size="1">Download</font></a>
          |&nbsp;<a href="../../public/portal_desktop/contact.htm"><font color="black" size="1">Contact</font></a>
          |&nbsp;<a href="../../public/portal_desktop/search.htm"><font color="black" size="1">Search</font></a>
          |&nbsp;<a href="../../public/our_campus/library.htm"><font color="black" size="1">Library</font></a>

          |&nbsp;<a href="../../administration/human_resources/vacancy_list.htm"><font color="black" size="1">Vacancies</font></a>
          |&nbsp;<a href="../../public/portal_services/help.htm"><font color="maroon" size="1"><b>Help</b></font></a>&nbsp;
        </font>
      </td>
    </tr>
    </table>
        ';
    }
    
    
    public function bannerNav()
    {
    	return '<table><tr>
      <td valign="middle" align="left">
        <font color="black" size="1">

          &nbsp;&nbsp;<img src="../../_template/icon/contact_phone.gif">&nbsp;General Information +27 21 959 2911<br>
          &nbsp;&nbsp;<img src="../../_template/icon/contact_phone.gif">&nbsp;Technical Service Desk +27 21 959 2000<br>
          &nbsp;&nbsp;<img src="../../_template/icon/contact_physical_address.gif">&nbsp;Modderdam Road, Bellville, Cape Town 7535 South Africa<br>
          &nbsp;&nbsp;<img src="../../_template/icon/contact_postal_address.gif">&nbsp;Private Bag X17 Bellville 7535 Republic of South Africa<br>
          &nbsp;&nbsp;<img src="../../_template/icon/contact_email.gif">&nbsp;Support <a href="mailto:servicedesk@uwc.ac.za">servicedesk@uwc.ac.za</a><br>

          &nbsp;&nbsp;<img src="../../_template/icon/contact_info.gif">&nbsp;<a href="../../public/access_to_information/index.htm">Access to Information</a> | Last Updated June 13 2007<br>
        </font>
      </td>
      <td valign="top" align="left" height="5" width="168">
        <font color="black" size="1">
          <br>
          &nbsp;<img src="../../_template/icon/contact_person.gif">&nbsp;<a href="../../public/portal_desktop/contact_enquiries.htm">Academic Enquiry</a><br>

          &nbsp;<img src="../../_template/icon/contact_person.gif">&nbsp;<a href="../../public/portal_desktop/contact_student_administration.htm">Administration</a>
<br>
          &nbsp;<img src="../../_template/icon/contact_person.gif">&nbsp;<a href="../../public/our_campus/campus_services.htm">Campus Services</a><br>
          &nbsp;<img src="../../_template/icon/contact_person.gif">&nbsp;<a href="../../administration/development_public_affairs/index.htm">Communications & Media</a><br>
          &nbsp;<img src="../../_template/icon/contact_person.gif">&nbsp;<a href="../../public/portal_desktop/contact.htm">Contact Directory</a><br>
          &nbsp;<img src="../../_template/icon/contact_person.gif">&nbsp;<a href="../../public/portal_desktop/contact_enquiries.htm">General Enquiry</a><br>

        </font>
      </td>
    </tr>
    </table>';
    }
    
    
}
?>

<?php
/**
 * Class to handle statusbar elements.
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
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
 * @version    0.001
 * @package    schools
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to handle statusbar elements
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @version    0.001
 * @package    schools
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 */
class statusbarops extends object
{
    /**
     * 
     * Variable to hold the orientation of the status bar
     * 
     * @access public
     * @var string
     */
    public $orientaion;

    /**
     * 
     * Variable to hold the position of the status bar
     * 
     * @access public
     * @var string
     */
    public $position;
    
    /**
     * 
     * Variable to hold the display of the status bar
     * 
     * @access public
     * @var string
     */
    public $display;
    
    /**
     * 
     * Variable to hold the PKId
     * 
     * @access public
     * @var string
     */
    public $PKId;

    /**
     * 
     * Variable to hold the userId
     * 
     * @access public
     * @var string
     */
    public $userId;

    /**
     * 
     * Variable to hold the period of the alert
     * 
     * @access public
     * @var integer
     */
    public $alert;

    /**
     * 
     * Variable to hold the script string
     * 
     * @access public
     * @var string
     */
    public $script;

    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init()
    {
        try {
            // Load core system objects.
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->userId();
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            $this->objConfirm = $this->newObject('confirm', 'utilities');
            $this->objModules = $this->getObject('modules', 'modulecatalogue');

            // Load html elements.
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->objTable = $this->loadClass('htmltable', 'htmlelements');
            $this->objInput = $this->loadClass('textinput', 'htmlelements');
            $this->objText = $this->loadClass('textarea', 'htmlelements');
            $this->objDropdown = $this->loadClass('dropdown', 'htmlelements');
            $this->objForm = $this->loadClass('form', 'htmlelements');
            $this->objTab = $this->newObject('tabber', 'htmlelements');
            
            // Load db classes,
            $this->objDBsettings = $this->getObject('dbstatusbar_settings', 'statusbar');
            $this->objDBbridging = $this->getObject('dbstatusbar_bridging', 'statusbar');
            $this->getParams();
            
            //load module classes
            $this->objBookmarks = $this->getObject('bookmarksops', 'bookmarks');
            $this->objEmail = $this->newObject('dbrouting', 'internalmail');
            $this->objBuddies = $this->newObject('dbbuddies', 'buddies');
            $this->objMessaging = $this->newObject('dbmessaging', 'messaging');
            $this->objLoggedinUsers = $this->newObject('loggedinusers', 'security');
            $this->objCalendar = $this->newObject('dbcalendar', 'calendarbase');
            $this->objUserContext = $this->getObject('usercontext', 'context');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
     *
     * Method to generate an error string for display
     * 
     * @access private
     * @param string $errorText The error string
     * @return string $string The formated error string
     */
    private function error($errorText)
    {
        $error = $this->objLanguage->languageText('word_error', 'system', 'WORD: word_error, not found');
        
        $this->objIcon->title = $error;
        $this->objIcon->alt = $error;
        $this->objIcon->setIcon('exclamation', 'png');
        $errorIcon = $this->objIcon->show();
        
        $string = '<span style="color: red">' . $errorIcon . '&nbsp;<b>' . $errorText . '</b></span>';
        return $string;
    }
    
    /**
     *
     * Method to get the parameters
     * 
     * @access public
     * @return array $the array of parameters
     */
    public function getParams()
    {
        $settings = $this->objDBsettings->getSettings($this->userId);
        if (!empty($settings))
        {
            foreach ($settings as $setting)
            {
                switch ($setting['param'])
                {
                    case 'orientation':
                        $this->orientation = $setting['value'];
                        break;
                    case 'position':
                        $this->position = $setting['value'];
                        break;
                    case 'display':
                        $this->display = $setting['value'];
                        break;
                    case 'alert':
                        $this->alert = $setting['value'];
                        break;
                }
            }
        }
        else
        {
            $this->orientation = 'horizontal';
            $this->position = 'left top';
            $this->display = 'yes';
            $this->alert = 15;
        }                
    }
    
    /**
     *
     * Method to show the main status bar page
     * 
     * @access public
     * @param boolean $notAjax TRUE if this method is NOT called via ajax | FALSE if not
     * @return string $string The htlm string to display 
     */
    public function showMain($isAjax = FALSE)
    {
        $this->getParams();
        
        $onlineUsersLabel = $this->objLanguage->languageText('mod_statusbar_onlineusers', 'statusbar', 'ERROR: mod_statusbar_onlineusers');
        $emailLabel = $this->objLanguage->languageText('mod_statusbar_email', 'statusbar', 'ERROR: mod_statusbar_email');
        $messageLabel = $this->objLanguage->languageText('mod_statusbar_messaging', 'statusbar', 'ERROR: mod_statusbar_messaging');
        $alertLabel = $this->objLanguage->languageText('mod_statusbar_calendaralert', 'statusbar', 'ERROR: mod_statusbar_calendaralert');
        $documentLabel = $this->objLanguage->code2Txt('mod_statusbar_document', 'statusbar', NULL, 'ERROR: mod_statusbar_document');
        $pictureLabel = $this->objLanguage->languageText('mod_statusbar_picture', 'statusbar', 'ERROR: mod_statusbar_picture');
        $buddiesLabel = $this->objLanguage->languageText('mod_statusbar_buddies', 'statusbar', 'ERROR: mod_statusbar_buddies');
        $settingsLabel = $this->objLanguage->languageText('mod_statusbar_settings', 'statusbar', 'ERROR: mod_statusbar_settings');
        $showLabel = $this->objLanguage->languageText('mod_statusbar_showbar', 'statusbar', 'ERROR: mod_statusbar_showbar');
        $hideLabel = $this->objLanguage->languageText('mod_statusbar_hidebar', 'statusbar', 'ERROR: mod_statusbar_hidebar');
        $noBuddiesLabel = $this->objLanguage->languageText('mod_statusbar_nobuddies', 'statusbar', 'ERROR: mod_statusbar_nobuddies');
        $bookmarkLabel = $this->objLanguage->languageText('mod_statusbar_bookmark', 'statusbar', 'ERROR: mod_statusbar_bookmark');
        
        $onlineUsers = $this->ajaxGetOnlineUsers();
        $onlineLink = "<span title=\"$onlineUsersLabel\"><div class=\"statusbar_online_users\"><span class=\"statusbar_online_count\">$onlineUsers</span></div></span>";

        if ($this->objModules->checkIfRegistered('internalmail'))
        {
            $unreadMail = $this->ajaxGetUnreadEmailCount();
            $class = ($unreadMail > 0) ? 'statusbar_email_on' : 'statusbar_email_off';
            $unreadMail = ($unreadMail > 0) ? '<span class="statusbar_email_count">' . $unreadMail . '</span>' : '&nbsp;';
            $emailLink = "<span title=\"$emailLabel\"><div class=\"$class\">$unreadMail</div></span>";
        }
            
        if ($this->objModules->checkIfRegistered('messaging'))
        {
            $instantMessages = $this->ajaxGetUnreadInstantMessages();
            $class = (count($instantMessages) > 0) ? 'statusbar_messaging_on' : 'statusbar_messaging_off';
            $instantMessages = (count($instantMessages) > 0) ? '<span class="statusbar_message_count">' . count($instantMessages) . '</span>' : '&nbsp;';
            $messageLink = "<span title=\"$messageLabel\"><div class=\"$class\">$instantMessages</div></span>";
        }
            
        if ($this->objModules->checkIfRegistered('calendar'))
        {
            $events = $this->ajaxGetCalendarAlerts();
            $class = (count($events) > 0) ? 'statusbar_alert_on' : 'statusbar_alert_off';
            $alerts = (count($events) > 0) ? '<span class="statusbar_alert_count">' . count($events) . '</span>' : '&nbsp;';
            $alertLink = "<span title=\"$alertLabel\"><div class=\"$class\">$alerts</div></span>";
        }

        if ($this->objModules->checkIfRegistered('contextcontent'))
        {
            $events = $this->ajaxGetContentAlerts();
            $class = (count($events) > 0) ? 'statusbar_document_on' : 'statusbar_document_off';
            $alerts = (count($events) > 0) ? '<span class="statusbar_document_count">' . count($events) . '</span>' : '&nbsp;';
            $documentLink = "<span title=\"$documentLabel\"><div class=\"$class\">$alerts</div></span>";
        }
            
        $this->objIcon->setIcon('camera', 'png');
        $this->objIcon->title = $pictureLabel;
        $this->objIcon->alt = $pictureLabel;
        $pictureIcon = $this->objIcon->show();
        $pictureLink = '<a href="#" id="statusbar_picture" title="' . $pictureLabel . '"' . '>' . $pictureIcon . '</a>';

        if ($this->objModules->checkIfRegistered('buddies'))
        {
            $buddies = $this->ajaxGetBuddies();
            $this->objIcon->setIcon('buddies', 'png');
            if ($buddies['all'] == 0)
            {
                $this->objIcon->title = $buddiesLabel;
                $this->objIcon->alt = $buddiesLabel;
                $buddiesIcon = $this->objIcon->show();

                $uri = $this->uri(array(), 'buddies');
                $buddiesLink = '<a href="' . $uri . '" title="' . $buddiesLabel . '"' . '>' . $buddiesIcon . '</a>';
            }
            else
            {
                if (count($buddies['online']) > 0)
                {
                    $buddiesLink = "<span title=\"$buddiesLabel\"><div class=\"statusbar_buddies_on\"><span class=\"statusbar_buddies_count\">" . count($buddies['online']) . "</span></div></span>";
                }
                else
                {
                    $this->objIcon->title = $noBuddiesLabel;
                    $this->objIcon->alt = $noBuddiesLabel;
                    $buddiesIcon = $this->objIcon->show();
                    $buddiesLink = $buddiesIcon;
                }
            }
        }

        if ($this->objModules->checkIfRegistered('bookmarks'))
        {        
            $this->objIcon->setIcon('bookmark', 'png');
            $this->objIcon->title = $bookmarkLabel;
            $this->objIcon->alt = $bookmarkLabel;
            $bookmarkIcon = $this->objIcon->show();

            $bookmarkLink = '<div id="add_bookmark" style="cursor: pointer;" title="' . $bookmarkLabel . '"' . '>' . $bookmarkIcon . '</a>';
            $this->objBookmarks = $this->getObject('bookmarksops', 'bookmarks');
            $gotoLink = $this->objBookmarks->showGotoLink();
        }

        $this->objIcon->setIcon('cog', 'png');
        $this->objIcon->title = $settingsLabel;
        $this->objIcon->alt = $settingsLabel;
        $settingsIcon = $this->objIcon->show();
        $settingsLink = '<a href="#" id="statusbar_settings" title="' . $settingsLabel . '"' . '>' . $settingsIcon . '</a>';
        
        if ($this->display == 'yes')
        {
            $this->objIcon->setIcon('arrow_in', 'png');
            $this->objIcon->title = $hideLabel;
            $this->objIcon->alt = $hideLabel;
            $displayIcon = $this->objIcon->show();
            $displayLink = '<a href="#" id="statusbar_display_hide" title="' . $hideLabel . '"' . '>' . $displayIcon . '</a>';        
        }
        else
        {
            $this->objIcon->setIcon('arrow_out', 'png');
            $this->objIcon->title = $showLabel;
            $this->objIcon->alt = $showLabel;
            $displayIcon = $this->objIcon->show();
            $displayLink = '<a href="#" id="statusbar_display_show" title="' . $showLabel . '"' . '>' . $displayIcon . '</a>';        
        }

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        if ($this->display == 'yes')
        {
            if ($this->orientation == 'horizontal')
            {
                $objTable->startRow();
                $objTable->addCell($onlineLink, '25px', '', 'center', '', '', '');
                if ($this->objModules->checkIfRegistered('internalmail'))
                {
                    $objTable->addCell($emailLink, '25px', '', 'center', '', '', '');
                }
                if ($this->objModules->checkIfRegistered('messaging'))
                {
                    $objTable->addCell($messageLink, '25px', '', 'center', '', '', '');
                }
                if ($this->objModules->checkIfRegistered('calendar'))
                {                
                    $objTable->addCell($alertLink, '25px', '', 'center', '', '', '');
                }
                if ($this->objModules->checkIfRegistered('contextcontent'))
                {
                    $objTable->addCell($documentLink, '25px', '', 'center', '', '', '');
                }
                $objTable->addCell($pictureLink, '25px', '', 'center', '', '', '');
                if ($this->objModules->checkIfRegistered('buddies'))
                {
                    $objTable->addCell($buddiesLink, '25px', '', 'center', '', '', '');
                }
                if ($this->objModules->checkIfRegistered('bookmarks'))
                {
                    $objTable->addCell($bookmarkLink, '25px', '', 'center', '', '', '');
                    if (!empty($gotoLink))
                    {
                        $objTable->addCell($gotoLink, '25px', '', 'center', '', '', '');
                    }
                }
                $objTable->addCell($settingsLink, '25px', '', 'center', '', '', '');
                $objTable->addCell($displayLink, '25px', '', 'center', '', '', '');
                $objTable->endRow();        
            }
            else
            {
                $objTable->width = '25px';
                $objTable->startRow();
                $objTable->addCell($onlineLink, '', '', 'center', '', '', '');
                $objTable->endRow();        
                if ($this->objModules->checkIfRegistered('internalmail'))
                {
                    $objTable->startRow();
                    $objTable->addCell($emailLink, '', '', 'center', '', '', '');
                    $objTable->endRow();        
                }
                if ($this->objModules->checkIfRegistered('messaging'))
                {
                    $objTable->startRow();
                    $objTable->addCell($messageLink, '', '', 'center', '', '', '');
                    $objTable->endRow();        
                }
                if ($this->objModules->checkIfRegistered('calendar'))
                {
                    $objTable->startRow();
                    $objTable->addCell($alertLink, '', '', 'center', '', '', '');
                    $objTable->endRow();        
                }
                if ($this->objModules->checkIfRegistered('contextcontent'))
                {
                    $objTable->startRow();
                    $objTable->addCell($documentLink, '', '', 'center', '', '', '');
                    $objTable->endRow();        
                }
                $objTable->startRow();
                $objTable->addCell($pictureLink, '', '', 'center', '', '', '');
                $objTable->endRow();        
                if ($this->objModules->checkIfRegistered('buddies'))
                {
                    $objTable->startRow();
                    $objTable->addCell($buddiesLink, '', '', 'center', '', '', '');
                    $objTable->endRow();        
                }
                if ($this->objModules->checkIfRegistered('internalmail'))
                {
                    $objTable->startRow();
                    $objTable->addCell($bookmarkLink, '', '', 'center', '', '', '');
                    $objTable->endRow();        
                    if (!empty($gotoLink))
                    {
                        $objTable->startRow();
                        $objTable->addCell($gotoLink, '', '', 'center', '', '', '');
                        $objTable->endRow();        
                    }
                }
                $objTable->startRow();
                $objTable->addCell($settingsLink, '', '', 'center', '', '', '');
                $objTable->endRow();        
                $objTable->startRow();
                $objTable->addCell($displayLink, '', '', 'center', '', '', '');
                $objTable->endRow();        
            }
        }
        else
        {
            $objTable->width = '25px';
            $objTable->startRow();
            $objTable->addCell($displayLink, '', '', 'center', '', '', '');
            $objTable->endRow();        
        }
        $string = $objTable->show();
        
        if ($isAjax)
        {
            echo $string;
            die();
        }
        else
        {
            return $string;
        }
    }
    
    /**
     *
     * Method to create the status bar floating dialog
     * 
     * @access public
     * @return string $string The display string for the dialog 
     */
    public function showStatusbar()
    {
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $ajaxPollingInterval = $this->objSysConfig->getValue('AJAX_POLL', 'statusbar');
        $this->objSvars = $this->getObject('serializevars', 'utilities');
        $this->script = '';
        $this->script .= '<script type="text/javascript">var ajax_poll=' . $ajaxPollingInterval . '</script>';
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('statusbar');
        $dialog->setContent($this->showMain());
        $dialog->setOpen("jQuery('#statusbar').prev().hide();jQuery('#statusbar').removeAttr('class');jQuery('#statusbar').addClass('ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix');");
        if ($this->orientation == 'horizontal' || $this->display == 'no')
        {
            $dialog->setMinHeight('35');
        }
        $dialog->setPosition($this->position);
        $dialog->setDraggable(FALSE);
        $dialog->setResizable(FALSE);
        $dialog->setAutoOpen(TRUE);
        $dialog->setModal(FALSE);
        $dialog->setDialogClass('dialog_fixed');
        $dialog->unsetButtons();
        $string = $dialog->show();
        $this->script .= $dialog->script;
        
        if ($this->objModules->checkIfRegistered('bookmarks'));
        {
            $string .= $this->objBookmarks->showLink(FALSE);
        }
        return $string . $this->showSettingsDialog() . $this->showInstantMessaging() . $this->showInstantMessages() . $this->ajaxShowCalendarAlerts() . $this->ajaxShowContentAlerts();
    }
    
    /**
     * 
     * Method to show the left block
     * 
     * @access public
     * @return string $string The string for the left block
     */
    public function showLeft()
    {
        return $this->showStatusbar();
    }
    
    /**
     *
     * <Method to return the statusbar settings dialog
     * 
     * @access public 
     * @return string $string The dialog display string 
     */
    public function showSettingsDialog()
    {
        $titleLabel = $this->objLanguage->languageText('mod_statusbar_settings', 'statusbar', 'ERROR: mod_statusbar_settings');
        
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        
        $orientationDiv = '<div id="statusbar_orientation_div"></div>';
        $positionDiv = '<div id="statusbar_position_div"></div>';
        $intervalDiv = '<div id="statusbar_interval_div"></div>';
        
        $objButton = new button('save', $saveLabel);
        $objButton->setId('statusbar_settings_save');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('statusbar_settings_cancel');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($saveButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="7"', '');
        $objTable->endRow();
        $buttonsTable = $objTable->show();

        $objForm = new form('settings', $this->uri(array(
            'action' => 'savesettings',
        ), 'statusbar'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($orientationDiv . $positionDiv . $intervalDiv . $buttonsTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_statusbar_settings');
        $dialog->setTitle($titleLabel);
        $dialog->setContent($form);
        $dialog->setWidth(420);
        $dialog->unsetButtons();
        $string = $dialog->show();
        $this->script .= $dialog->script;

        return $string;        
    }
    
    /**
     *
     * Method to show the orientation options
     * 
     * @access public 
     * @return VOID
     */
    public function ajaxShowOrientation($isAjax = TRUE)
    {
        $orientationLabel = $this->objLanguage->languageText('mod_statusbar_orientation', 'statusbar', 'ERROR: mod_statusbar_orientation');
        $horizontalLabel = $this->objLanguage->languageText('mod_statusbar_horizontal', 'statusbar', 'ERROR: mod_statusbar_horizontal');
        $verticalLabel = $this->objLanguage->languageText('mod_statusbar_vertical', 'statusbar', 'ERROR: mod_statusbar_vertical');

        $objDrop = new dropdown('statusbar_orientation');
        $objDrop->addOption('horizontal', $horizontalLabel);
        $objDrop->addOption('vertical', $verticalLabel);
        $objDrop->setSelected($this->orientation);
        $orientationDrop = $objDrop->show();
  
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $orientationLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($orientationDrop, '', '', '', '', '', '');
        $objTable->endRow();
        $orientationTable = $objTable->show();
        
        if ($isAjax)
        {
            echo $orientationTable;
            die();
        }
        else
        {
            return $orientationTable;
        }
    }
    
    /**
     * Method to show the position options for the orientation
     * 
     * @access public
     * @return VOID
     */
    public function ajaxShowPosition()
    {
        $positionLabel = $this->objLanguage->languageText('mod_statusbar_position', 'statusbar', 'ERROR: mod_statusbar_position');
        $topLabel = $this->objLanguage->languageText('mod_statusbar_top', 'statusbar', 'ERROR: mod_statusbar_top');
        $bottomLabel = $this->objLanguage->languageText('mod_statusbar_bottom', 'statusbar', 'ERROR: mod_statusbar_bottom');
        $leftLabel = $this->objLanguage->languageText('mod_statusbar_left', 'statusbar', 'ERROR: mod_statusbar_left');
        $rightLabel = $this->objLanguage->languageText('mod_statusbar_right', 'statusbar', 'ERROR: mod_statusbar_right');
        $leftTopLabel = $this->objLanguage->languageText('mod_statusbar_lefttop', 'statusbar', 'ERROR: mod_statusbar_lefttop');
        $rightTopLabel = $this->objLanguage->languageText('mod_statusbar_righttop', 'statusbar', 'ERROR: mod_statusbar_righttop');
        $leftBottomLabel = $this->objLanguage->languageText('mod_statusbar_leftbottom', 'statusbar', 'ERROR: mod_statusbar_leftbottom');
        $rightBottomLabel = $this->objLanguage->languageText('mod_statusbar_rightbottom', 'statusbar', 'ERROR: mod_statusbar_rightbottom');
        
        $orientation = $this->getParam('orientation');

        $objDrop = new dropdown('statusbar_position');
        if ($orientation == 'horizontal')
        {
            $objDrop->addOption('left top', $leftTopLabel);
            $objDrop->addOption('top', $topLabel);
            $objDrop->addOption('right top', $rightTopLabel);
            $objDrop->addOption('left bottom', $leftBottomLabel);
            $objDrop->addOption('bottom', $bottomLabel);
            $objDrop->addOption('right bottom', $rightBottomLabel);
        }
        else
        {
            $objDrop->addOption('left top', $leftTopLabel);
            $objDrop->addOption('left', $leftLabel);
            $objDrop->addOption('left bottom', $leftBottomLabel);
            $objDrop->addOption('right top', $rightTopLabel);
            $objDrop->addOption('right', $rightLabel);
            $objDrop->addOption('right bottom', $rightBottomLabel);
        }
        $objDrop->setSelected($this->position);
        $positionDrop = $objDrop->show();
  
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $positionLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($positionDrop, '', '', '', '', '', '');
        $objTable->endRow();        
        $positionTable = $objTable->show();
        
        echo $positionTable;
        die();        
    }
    
    /**
     * Method to show the position options for the orientation
     * 
     * @access public
     * @return VOID
     */
    public function ajaxShowInterval($isAjax = TRUE)
    {
        $intervalLabel = $this->objLanguage->languageText('mod_statusbar_alertperiod', 'statusbar', 'ERROR: mod_statusbar_alertperiod');
        $minutesLabel = $this->objLanguage->languageText('word_minutes', 'system', 'ERROR: word_minutes');
        
        if ($this->objModules->checkIfRegistered('calendar'))
        {
            $objDrop = new dropdown('statusbar_alert');
            $objDrop->addOption('15', '15 ' . $minutesLabel);
            $objDrop->addOption('30', '30 ' . $minutesLabel);
            $objDrop->addOption('45', '45 ' . $minutesLabel);
            $objDrop->addOption('60', '60 ' . $minutesLabel);
            $objDrop->setSelected($this->alert);
            $intervalDrop = $objDrop->show();

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell('<b>' . $intervalLabel . ': </b>', '200px', '', '', '', '', '');
            $objTable->addCell($intervalDrop, '', '', '', '', '', '');
            $objTable->endRow();        
            $interval = $objTable->show();        
        }
        else
        {
            $objInput = new textinput('statusbar_alert', $this->alert, 'hidden');
            $interval = $objInput->show();
        }
  
        if ($isAjax)
        {
            echo $interval;
            die();        
        }
        else
        {
            return $interval;
        }
    }
    
    /**
     *
     * Method to get count of unread email
     * 
     * @access public
     * @return integer $count The count of unread emails 
     */
    public function ajaxGetUnreadEmailCount()
    {
        $unreadEmail = $this->objEmail->getUnreadEmailCount();

        return $unreadEmail;
    }
    
    /**
     *
     * Method to return the number of buddies the user has
     * 
     * @access public
     * @return array $count The number of buddies the user has
     */
    public function ajaxGetBuddies()
    {
        $allBuddies = $this->objBuddies->getBuddies($this->userId);
        $onlineBuddies = $this->objBuddies->getOnlineBuddies($this->userId);
        
        $count = array();
        $count['all'] = count($allBuddies);
        $count['online'] = $onlineBuddies;
        
        return $count;
    }
    
    /**
     *
     * Method to get online users
     * 
     * @access public
     * @return integer $count The number of online users 
     */
    public function ajaxGetOnlineUsers()
    {
        $loggedinUsers = $this->objLoggedinUsers->getActiveUserCount();
        
        return $loggedinUsers;
    }
    
    /**
     *
     * Method to display the instant messaging facility
     * 
     * @access public
     * @return string $string The im dialog 
     */
    public function showInstantMessaging()
    {
        $buddies = $this->ajaxGetBuddies();
        
        $toLabel = $this->objLanguage->languageText('word_to', 'system', 'ERROR: word_to');
        $selectLabel = $this->objLanguage->languageText('mod_statusbar_selectrecipient', 'statusbar', 'ERROR: mod_statusbar_selectrecipient');
        $sendMessageLabel = $this->objLanguage->languageText('mod_statusbar_sendmessage', 'statusbar', 'ERROR: mod_statusbar_sendmessage');
        $messageLabel = $this->objLanguage->languageText('word_message', 'system', 'ERROR: word_message');
        $sendLabel = $this->objLanguage->languageText('word_send', 'system', 'ERROR: word_send');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $confirmLabel = $this->objLanguage->languageText('mod_statusbar_confirmsend', 'statusbar', 'mod_statusbar_confirmsend');
        $successLabel = $this->objLanguage->languageText('word_success', 'system', 'ERROR: word_success');
        $recipientErrorLabel = $this->objLanguage->languageText('mod_statusbar_recipienterror', 'statusbar', 'ERROR: mod_statusbar_recipienterror');
        $messageErrorLabel = $this->objLanguage->languageText('mod_statusbar_messageerror', 'statusbar', 'ERROR: mod_statusbar_messageerror');
        
        $arrayVars = array();
        $arrayVars['no_recipient'] = $recipientErrorLabel;
        $arrayVars['no_message'] = $messageErrorLabel;
        
        // pass error to javascript.
        $this->objSvars->varsToJs($arrayVars);
        $objDrop = new dropdown('statusbar_to');
        $objDrop->addOption('', $selectLabel);
        $objDrop->addFromArray($buddies['online']);
        $toDrop = $objDrop->show();
        
        $objText = new textarea('statusbar_message', '');
        $messageText = $objText->show();

        $objButton = new button('send', $sendLabel);
        $objButton->setId('statusbar_message_send');
        $sendButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('statusbar_message_cancel');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $toLabel . ': </b>', '200px', '', '', '', '', '');
        $objTable->addCell($toDrop, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $messageLabel . ': </b>', '', 'top', '', '', '', '');
        $objTable->addCell($messageText, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($sendButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $messageTable = $objTable->show();

        $objForm = new form('message', $this->uri(array(
            'action' => 'sendmessage',
        ), 'statusbar'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($messageTable);
        $form = $objForm->show();
        
        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_statusbar_message');
        $dialog->setTitle($sendMessageLabel);
        $dialog->setContent($form);
        $dialog->setWidth(745);
        $dialog->unsetButtons();
        $string = $dialog->show();
        $this->script .= $dialog->script;

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<span class="success">' . $confirmLabel . '</span>', '200px', '', '', '', '', '');
        $objTable->endRow();
        $successTable = $objTable->show();

        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_statusbar_message_confirm');
        $dialog->setTitle($successLabel);
        $dialog->setContent($successTable);
        $dialog->setWidth(370);
        $string .= $dialog->show();
        $this->script .= $dialog->script;

        return $string;        
    }
    
    /**
     *
     * Method to get the unread chat messages count
     * 
     * @access public
     * @return integer $count The number of unread chat messages the user has 
     */
    public function ajaxGetUnreadInstantMessages()
    {
        $messages = $this->objMessaging->getInstantMessages();
        return $messages;
    }
    
    /**
     *
     * Method to display instant messages
     * 
     * @access public
     * @return string $string The instant messa display string  
     */
    public function showInstantMessages()
    {
        $fromLabel = $this->objLanguage->languageText('word_from', 'system', 'ERROR: word_from');
        $messageLabel = $this->objLanguage->languageText('word_message', 'system', 'ERROR: word_message');
        $instantMessageLabel = $this->objLanguage->languageText('mod_statusbar_instantmessage', 'statusbar', 'ERROR: mod_statusbar_instantmessage');
        
        $objInput = new textinput('statusbar_message_id', '', 'hidden', '');
        $idInput = $objInput->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $fromLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell('<div id="statusbar_message_from"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $messageLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell('<div id="statusbar_message_message"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $messageTable = $objTable->show();

        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_statusbar_message_show');
        $dialog->setTitle($instantMessageLabel);
        $dialog->setContent($idInput . $messageTable);
        $dialog->setBeforeClose('updateInstantMessages(this)');
        $dialog->setWidth(500);
        $string = $dialog->show();
        $this->script .= $dialog->script;

        return $string;        
    }
    
    /**
     *
     * Method to show upcoming calendar events
     * 
     * @access public
     * @return array $alerts The number of upcoming calendar events 
     */
    public function ajaxGetCalendarAlerts()
    {
        $timestamp = strtotime("+$this->alert minutes", time());
        $contexts = $this->objUserContext->getUserContext($this->userId);

        $alerts = array();
        
        $userEvents = $this->objCalendar->getAlerts($timestamp, $this->userId, NULL);
        if (count($userEvents) > 0)
        {
            foreach ($userEvents as $event)
            {
                $alerts[] = $event;
            }
        }
        
        if (count($contexts) > 0)
        {
            $contextEvents = array();
            foreach ($contexts as $contextCode)
            {
                $contextEvents = $this->objDBbridging->getContextCalendarAlerts($timestamp, $contextCode);
            }
            
            if (count($contextEvents) > 0)
            {
                foreach ($contextEvents as $event)
                {
                    $alerts[] = $event;
                }
            }
        }
        
        foreach ($alerts as $key => $alert)
        {
            $eventTime = strtotime($alert['eventdate'] . ' ' . $alert['timefrom']);
            if ($eventTime > $timestamp)
            {
                unset($alerts[$key]);
            }
        }

        return $alerts;
    }
    
    /**
     *
     * Method to show new document alerts
     * 
     * @access public
     * @return array $alerts The number of new documents events 
     */
    public function ajaxGetContentAlerts()
    {
        $timestamp = time();
        $contexts = $this->objUserContext->getUserContext($this->userId);

        $alerts = array();
        
        if (count($contexts) > 0)
        {
            $contextEvents = array();
            foreach ($contexts as $contextCode)
            {
                $contextEvents = $this->objDBbridging->getContextContentAlerts($timestamp, $contextCode);
            }
          
            if (count($contextEvents) > 0)
            {
                foreach ($contextEvents as $event)
                {
                    $alerts[] = $event;
                }
            }
        }
        
        foreach ($alerts as $key => $alert)
        {
            $eventTime = strtotime($alert['createdon']);
            if ($eventTime > $timestamp)
            {
                unset($alerts[$key]);
            }
        }

        return $alerts;
    }
    
    /**
     *
     * Method to display calendar alerts 
     * 
     * @access public
     * @return string $string The alert dialog
     */
    public function ajaxShowCalendarAlerts()
    {
        $titleLabel = $this->objLanguage->languageText('word_title', 'system', 'ERROR: word_title');
        $fromLabel = $this->objLanguage->languageText('word_from', 'system', 'ERROR: word_from');
        $alertLabel = $this->objLanguage->languageText('mod_statusbar_calendaralert', 'statusbar', 'ERROR: mod_statusbar_calendaralert');
        
        $objInput = new textinput('statusbar_calendar_id', '', 'hidden', '');
        $idInput = $objInput->show();
        
        $objInput = new textinput('statusbar_alert_type', '', 'hidden', '');
        $typeInput = $objInput->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $titleLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell('<div id="statusbar_alert_title"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $fromLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell('<div id="statusbar_alert_from"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $alertTable = $objTable->show();

        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_statusbar_calendar_alert_show');
        $dialog->setTitle($alertLabel);
        $dialog->setContent($idInput . $typeInput . $alertTable);
        $dialog->setBeforeClose('updateCalendarAlert(this)');
        $dialog->setWidth(500);
        $string = $dialog->show();
        $this->script .= $dialog->script;

        return $string;        
    }

    /**
     *
     * Method to display content alerts 
     * 
     * @access public
     * @return string $string The alert dialog
     */
    public function ajaxShowContentAlerts()
    {
        $descriptionLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $linkLabel = $this->objLanguage->languageText('word_link', 'system', 'ERROR: word_link');
        $alertLabel = $this->objLanguage->languageText('mod_statusbar_contentalert', 'statusbar', 'ERROR: mod_statusbar_contentalert');
        
        $objInput = new textinput('statusbar_content_id', '', 'hidden', '');
        $idInput = $objInput->show();
               
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<b>' . $descriptionLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell('<div id="statusbar_alert_description"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('<b>' . $linkLabel . ': </b>', '', '', '', '', '', '');
        $objTable->addCell('<div id="statusbar_alert_link"></div>', '', '', '', '', '', '');
        $objTable->endRow();
        $alertTable = $objTable->show();

        $dialog = $this->newObject('dialog', 'jquerycore');
        $dialog->setCssId('dialog_statusbar_content_alert_show');
        $dialog->setTitle($alertLabel);
        $dialog->setContent($idInput . $alertTable);
        $dialog->setBeforeClose('updateContentAlert(this)');
        $dialog->setWidth(500);
        $string = $dialog->show();
        $this->script .= $dialog->script;

        return $string;        
    }
    
    /**
     *
     * Method to return the statusbar params
     * 
     * @access public
     * @return array The array of params 
     */
    public function statusbarParams()
    {
        $headerParams = $this->getJavascriptFile('statusbar.js', 'statusbar');
        if ($this->objModules->checkIfRegistered('bookmarks'));
        {
            $params = $this->objBookmarks->bookmarkParams();
            $headerParams .= "\n" . $params['headerParams'];
        }
        $headerParams .= "\n" . $this->script;
        $array = array('headerParams' => $headerParams);
        return $array;
    }
}
?>
<?php
// +-----------------------------------------------------------------------+ 
// | Copyright (c) 2002 Richard Heyes                                      | 
// | All rights reserved.                                                  | 
// |                                                                       | 
// | Redistribution and use in source and binary forms, with or without    | 
// | modification, are permitted provided that the following conditions    | 
// | are met:                                                              | 
// |                                                                       | 
// | o Redistributions of source code must retain the above copyright      | 
// |   notice, this list of conditions and the following disclaimer.       | 
// | o Redistributions in binary form must reproduce the above copyright   | 
// |   notice, this list of conditions and the following disclaimer in the | 
// |   documentation and/or other materials provided with the distribution.| 
// | o The names of the authors may not be used to endorse or promote      | 
// |   products derived from this software without specific prior written  | 
// |   permission.                                                         | 
// |                                                                       | 
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   | 
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     | 
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR | 
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  | 
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, | 
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      | 
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, | 
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY | 
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   | 
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE | 
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  | 
// |                                                                       | 
// +-----------------------------------------------------------------------+ 
// | Author: Richard Heyes <richard@php.net>                               | 
// +-----------------------------------------------------------------------+ 
// 
// $Id$

/**
* Class to produce a HTML Select dropdown of UK Counties
* Probably not an entirely accurate list and no doubt contains
* some fluff. However it did come from Post Office data.
*
* @author  Richard Heyes <richard@php.net>
* @access  public
* @version 1.0
* @package HTML_Select
*/

class HTML_Select_Common_UKCounty
{
    /**
    * Constructor
    *
    * @access public
    */
    function HTML_Select_Common_UKCounty()
    {
        $this->_counties[] = 'Aberdeenshire';
        $this->_counties[] = 'Angus';
        $this->_counties[] = 'Argyll';
        $this->_counties[] = 'Avon';
        $this->_counties[] = 'Ayrshire';
        $this->_counties[] = 'Banffshire';
        $this->_counties[] = 'Bedfordshire';
        $this->_counties[] = 'Berkshire';
        $this->_counties[] = 'Berwickshire';
        $this->_counties[] = 'Buckinghamshire';
        $this->_counties[] = 'Caithness';
        $this->_counties[] = 'Cambridgeshire';
        $this->_counties[] = 'Cheshire';
        $this->_counties[] = 'Clackmannanshire';
        $this->_counties[] = 'Cleveland';
        $this->_counties[] = 'Clwyd';
        $this->_counties[] = 'Cornwall';
        $this->_counties[] = 'county';
        $this->_counties[] = 'County Antrim';
        $this->_counties[] = 'County Armagh';
        $this->_counties[] = 'County Down';
        $this->_counties[] = 'County Durham';
        $this->_counties[] = 'County Fermanagh';
        $this->_counties[] = 'County Londonderry';
        $this->_counties[] = 'County Tyrone';
        $this->_counties[] = 'Cumbria';
        $this->_counties[] = 'Derbyshire';
        $this->_counties[] = 'Devon';
        $this->_counties[] = 'Dorset';
        $this->_counties[] = 'Dumfriesshire';
        $this->_counties[] = 'Dunbartonshire';
        $this->_counties[] = 'Dyfed';
        $this->_counties[] = 'East Lothian';
        $this->_counties[] = 'East Sussex';
        $this->_counties[] = 'Essex';
        $this->_counties[] = 'Fife';
        $this->_counties[] = 'Gloucestershire';
        $this->_counties[] = 'Gwent';
        $this->_counties[] = 'Gwynedd';
        $this->_counties[] = 'Hampshire';
        $this->_counties[] = 'Herefordshire';
        $this->_counties[] = 'Hertfordshire';
        $this->_counties[] = 'Inverness-shire';
        $this->_counties[] = 'Isle of Arran';
        $this->_counties[] = 'Isle of Barra';
        $this->_counties[] = 'Isle of Bute';
        $this->_counties[] = 'Isle of Canna';
        $this->_counties[] = 'Isle of Coll';
        $this->_counties[] = 'Isle of Colonsay';
        $this->_counties[] = 'Isle of Cumbrae';
        $this->_counties[] = 'Isle of Eigg';
        $this->_counties[] = 'Isle of Gigha';
        $this->_counties[] = 'Isle of Harris';
        $this->_counties[] = 'Isle of Iona';
        $this->_counties[] = 'Isle of Islay';
        $this->_counties[] = 'Isle of Jura';
        $this->_counties[] = 'Isle of Lewis';
        $this->_counties[] = 'Isle of Mull';
        $this->_counties[] = 'Isle of North Uist';
        $this->_counties[] = 'Isle of Orkney';
        $this->_counties[] = 'Isle of Rhum';
        $this->_counties[] = 'Isle of Skye';
        $this->_counties[] = 'Isle of South Uist';
        $this->_counties[] = 'Isle of Tiree';
        $this->_counties[] = 'Isle of Wight';
        $this->_counties[] = 'Isles of Scilly';
        $this->_counties[] = 'Kent';
        $this->_counties[] = 'Kincardineshire';
        $this->_counties[] = 'Kirkcudbrightshire';
        $this->_counties[] = 'Lanarkshire';
        $this->_counties[] = 'Lancashire';
        $this->_counties[] = 'Leicestershire';
        $this->_counties[] = 'Lincolnshire';
        $this->_counties[] = 'Merseyside';
        $this->_counties[] = 'Middlesex';
        $this->_counties[] = 'Mid Glamorgan';
        $this->_counties[] = 'Midlothian';
        $this->_counties[] = 'Morayshire';
        $this->_counties[] = 'Norfolk';
        $this->_counties[] = 'Northamptonshire';
        $this->_counties[] = 'North Humberside';
        $this->_counties[] = 'Northumberland';
        $this->_counties[] = 'North Yorkshire';
        $this->_counties[] = 'Nottinghamshire';
        $this->_counties[] = 'Orkney';
        $this->_counties[] = 'Oxfordshire';
        $this->_counties[] = 'Peeblesshire';
        $this->_counties[] = 'Perthshire';
        $this->_counties[] = 'Powys';
        $this->_counties[] = 'Renfrewshire';
        $this->_counties[] = 'Ross-shire';
        $this->_counties[] = 'Roxburghshire';
        $this->_counties[] = 'Selkirkshire';
        $this->_counties[] = 'Shetland Islands';
        $this->_counties[] = 'Shropshire';
        $this->_counties[] = 'Somerset';
        $this->_counties[] = 'South Glamorgan';
        $this->_counties[] = 'South Humberside';
        $this->_counties[] = 'South Yorkshire';
        $this->_counties[] = 'Staffordshire';
        $this->_counties[] = 'Stirlingshire';
        $this->_counties[] = 'Suffolk';
        $this->_counties[] = 'Surrey';
        $this->_counties[] = 'Sutherland';
        $this->_counties[] = 'Tyne and Wear';
        $this->_counties[] = 'Warwickshire';
        $this->_counties[] = 'West Glamorgan';
        $this->_counties[] = 'West Lothian';
        $this->_counties[] = 'West Midlands';
        $this->_counties[] = 'West Sussex';
        $this->_counties[] = 'West Yorkshire';
        $this->_counties[] = 'Wigtownshire';
        $this->_counties[] = 'Wilts';
        $this->_counties[] = 'Wiltshire';
        $this->_counties[] = 'Worcestershire';
    }

    /**
    * Produces the HTML for the dropdown
    *
    * @param  string $name            The name="" attribute
    * @param  string $selectedOption  The option to be selected by default.
    *                                 Must match the county name exactly,
    *                                 (though it can be a different case).
    * @param  string $promoText       The text to appear as the first option
    * @param  string $extraAttributes Any extra attributes for the <select> tag
    * @return string                  The HTML for the <select>
    * @access public
    */
    function toHTML($name, $selectedOption = null, $promoText = 'Select a county...', $extraAttributes = '')
    {
        $options[]      = sprintf('<option value="">%s</option>', $promoText);
        $selectedOption = strtolower($selectedOption);

        foreach ($this->_counties as $county) {
            $county_lc = strtolower($county);
            $selected  = $selectedOption == $county_lc ? ' selected="selected"' : '';
            $options[] = '<option value="' . $county_lc . '"' . $selected . '>' . ucfirst($county) . '</option>';
        }
        
        return sprintf('<select name="%s" %s>%s</select>',
                       $name,
                       $extraAttributes,
                       implode("\r\n", $options));
    }
}

?>

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
* Class to produce a HTML Select dropdown of US States
*
* @author  Richard Heyes <richard@php.net>
* @access  public
* @version 1.0
* @package HTML_Select
*/

class HTML_Select_Common_USState
{
    /**
    * Constructor
    *
    * @access public
    */
    function HTML_Select_Common_USState()
    {
        $this->_states['ak'] = 'Alabama';
        $this->_states['al'] = 'Alaska';
        $this->_states['ar'] = 'Arizona';
        $this->_states['az'] = 'Arkansas';
        $this->_states['ca'] = 'California';
        $this->_states['co'] = 'Colorado';
        $this->_states['ct'] = 'Connecticut';
        $this->_states['de'] = 'Delaware';
        $this->_states['dc'] = 'District of Columbia';
        $this->_states['fl'] = 'Florida';
        $this->_states['ga'] = 'Georgia';
        $this->_states['hi'] = 'Hawaii';
        $this->_states['id'] = 'Idaho';
        $this->_states['il'] = 'Illinois';
        $this->_states['in'] = 'Indiana';
        $this->_states['ia'] = 'Iowa';
        $this->_states['ks'] = 'Kansas';
        $this->_states['ky'] = 'Kentucky';
        $this->_states['la'] = 'Louisiana';
        $this->_states['ma'] = 'Maine';
        $this->_states['md'] = 'Maryland';
        $this->_states['ma'] = 'Massachusetts';
        $this->_states['mi'] = 'Michigan';
        $this->_states['mn'] = 'Minnesota';
        $this->_states['ms'] = 'Mississippi';
        $this->_states['mo'] = 'Missouri';
        $this->_states['mt'] = 'Montana';
        $this->_states['ne'] = 'Nebraska';
        $this->_states['nv'] = 'Nevada';
        $this->_states['ne'] = 'New England';
        $this->_states['nh'] = 'New Hampshire';
        $this->_states['nj'] = 'New Jersey';
        $this->_states['nm'] = 'New Mexico';
        $this->_states['ny'] = 'New York';
        $this->_states['nc'] = 'North Carolina';
        $this->_states['nd'] = 'North Dakota';
        $this->_states['oh'] = 'Ohio';
        $this->_states['ok'] = 'Oklahoma';
        $this->_states['or'] = 'Oregon';
        $this->_states['pa'] = 'Pennsylvania';
        $this->_states['ri'] = 'Rhode Island';
        $this->_states['sc'] = 'South Carolina';
        $this->_states['sd'] = 'South Dakota';
        $this->_states['tn'] = 'Tennessee';
        $this->_states['tx'] = 'Texas';
        $this->_states['ut'] = 'Utah';
        $this->_states['vt'] = 'Vermont';
        $this->_states['va'] = 'Virginia';
        $this->_states['wa'] = 'Washington';
        $this->_states['wv'] = 'West Virginia';
        $this->_states['wi'] = 'Wisconsin';
        $this->_states['wy'] = 'Wyoming';
    }

    /**
    * Produces the HTML for the dropdown
    *
    * @param  string $name            The name="" attribute
    * @param  string $selectedOption  The option to be selected by default.
    *                                 Must match the state name exactly,
    *                                 (though it can be a different case).
    * @param  string $promoText       The text to appear as the first option
    * @param  string $extraAttributes Any extra attributes for the <select> tag
    * @return string                  The HTML for the <select>
    * @access public
    */
    function toHTML($name, $selectedOption = null, $promoText = 'Select a state...', $extraAttributes = '')
    {
        $options[]      = sprintf('<option value="">%s</option>', $promoText);
        $selectedOption = strtolower($selectedOption);

        foreach ($this->_states as $state) {
            $state_lc  = strtolower($state);
            $selected  = $selectedOption == $state_lc ? ' selected="selected"' : '';
            $options[] = '<option value="' . $state_lc . '"' . $selected . '>' . ucfirst($state) . '</option>';
        }
        
        return sprintf('<select name="%s" %s>%s</select>',
                       $name,
                       $extraAttributes,
                       implode("\r\n", $options));
    }

    /**
    * Returns an array with all states, indexed by shortcut
    *
    * @return array                   The array containing all state data
    * @access public
    */
    function getList()
    {
        return $this->_states;
    }
}

?>

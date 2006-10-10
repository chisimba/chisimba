<?php
// +-----------------------------------------------------------------------+ 
// | Copyright (c) 2002 Richard Heyes                                     | 
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
// |         Eric De Sousa <esc.z@wanadoo.fr>                               | 
// +-----------------------------------------------------------------------+ 
// 
// $Id$

/**
* Class to produce a HTML Select dropdown of FR Departements
*
* @author  Richard Heyes <richard@php.net>
* @author  Eric De Sousa <esc.z@wanadoo.fr>
* @access  public
* @version 1.0
* @package HTML_Select
*/

class HTML_Select_Common_FRDepartements
{
    /**
    * Constructor
    *
    * @access public
    */
    function HTML_Select_Common_FRDepartements()
    {
        $this->_departements['01'] = 'Ain';
        $this->_departements['02'] = 'Aisne';
        $this->_departements['03'] = 'Allier';
        $this->_departements['04'] = 'Alpes de-Htes Provence';
        $this->_departements['05'] = 'Hautes-Alpes';
        $this->_departements['06'] = 'Alpes-Maritimes';
        $this->_departements['07'] = 'Ardèche';
        $this->_departements['08'] = 'Ardennes';
        $this->_departements['09'] = 'Ariège';
        $this->_departements['10'] = 'Aube';
        $this->_departements['11'] = 'Aude';
        $this->_departements['12'] = 'Aveyron';
        $this->_departements['13'] = 'Bouches-du-Rhône';
        $this->_departements['14'] = 'Calvados';
        $this->_departements['15'] = 'Cantal';
        $this->_departements['16'] = 'Charente';
        $this->_departements['17'] = 'Charente-Maritime';
        $this->_departements['18'] = 'Cher';
        $this->_departements['19'] = 'Corrèze';
        $this->_departements['21'] = 'Côte d\'Or';
        $this->_departements['22'] = 'Côtes d\'Armor';
        $this->_departements['23'] = 'Creuse';
        $this->_departements['24'] = 'Dordogne';
        $this->_departements['25'] = 'Doubs';
        $this->_departements['26'] = 'Drôme';
        $this->_departements['27'] = 'Eure';
        $this->_departements['28'] = 'Eure-et-Loir';
        $this->_departements['29'] = 'Finistère';
        $this->_departements['2A'] = 'Corse du Sud';
        $this->_departements['2B'] = 'Hautes-Corse';
        $this->_departements['30'] = 'Gard';
        $this->_departements['31'] = 'Hautes-Garonne';
        $this->_departements['32'] = 'Gers';
        $this->_departements['33'] = 'Gironde';
        $this->_departements['34'] = 'Hérault';
        $this->_departements['35'] = 'Ille-et-Vilaine';
        $this->_departements['36'] = 'Indre';
        $this->_departements['37'] = 'Indre-et-Loire';
        $this->_departements['38'] = 'Isère';
        $this->_departements['39'] = 'Jura';
        $this->_departements['40'] = 'Landes';
        $this->_departements['41'] = 'Loir-et-Cher';
        $this->_departements['42'] = 'Loire';
        $this->_departements['43'] = 'Hautes-Loire';
        $this->_departements['44'] = 'Loire-Atlantique';
        $this->_departements['45'] = 'Loiret';
        $this->_departements['46'] = 'Lot';
        $this->_departements['47'] = 'Lot-et-Garonne';
        $this->_departements['48'] = 'Lozère';
        $this->_departements['49'] = 'Maine-et-Loire';
        $this->_departements['50'] = 'Manche';
        $this->_departements['51'] = 'Marne'; 
        $this->_departements['52'] = 'Haute-Marne';
        $this->_departements['53'] = 'Mayenne';
        $this->_departements['54'] = 'Meurthe-et-Moselle';
        $this->_departements['55'] = 'Meuse';
        $this->_departements['56'] = 'Morbihan';
        $this->_departements['57'] = 'Moselle';
        $this->_departements['58'] = 'Nièvre';
        $this->_departements['59'] = 'Nord';
        $this->_departements['60'] = 'Oise';
        $this->_departements['61'] = 'Orne';
        $this->_departements['62'] = 'Pas-de-Calais';
        $this->_departements['63'] = 'Puy-de-Dôme';
        $this->_departements['64'] = 'Pyrénées-Atlantiques';
        $this->_departements['65'] = 'Hautes-Pyrénées';
        $this->_departements['66'] = 'Pyrénées';
        $this->_departements['67'] = 'Bas-Rhin';
        $this->_departements['68'] = 'Haut-Rhin';
        $this->_departements['69'] = 'Rhône';
        $this->_departements['70'] = 'Hautes-Saône';
        $this->_departements['71'] = 'Saône-et-Loire';
        $this->_departements['72'] = 'Sarthe';
        $this->_departements['73'] = 'Savoie';
        $this->_departements['74'] = 'Hautes-Savoie';
        $this->_departements['75'] = 'Paris';
        $this->_departements['76'] = 'Seine-Maritime';
        $this->_departements['77'] = 'Sein-et-Marne';
        $this->_departements['78'] = 'Yvelines';
        $this->_departements['79'] = 'Deux-Sèvres';
        $this->_departements['80'] = 'Somme';
        $this->_departements['81'] = 'Tarn';
        $this->_departements['82'] = 'Tarn-et-Garonne';
        $this->_departements['83'] = 'Var';
        $this->_departements['84'] = 'Vaucluse';
        $this->_departements['85'] = 'Vendée';
        $this->_departements['86'] = 'Vienne';
        $this->_departements['87'] = 'Hautes-Vienne';
        $this->_departements['88'] = 'Vosges';
        $this->_departements['89'] = 'Yonne';
        $this->_departements['90'] = 'Territoire de Belfort';
        $this->_departements['91'] = 'Essonne';
        $this->_departements['92'] = 'Hauts-de-Seine';
        $this->_departements['93'] = 'Seine-St-Denis';
        $this->_departements['94'] = 'Val de Marne';
        $this->_departements['95'] = 'Val d\'Oise';
        $this->_departements['971'] = 'Guadeloupe';
        $this->_departements['972'] = 'Martinique';
        $this->_departements['973'] = 'Guyane';
        $this->_departements['974'] = 'Réunion';
    }

    /**
    * Produces the HTML for the dropdown
    *
    * @param  string $name            The name="" attribute
    * @param  string $selectedOption  The option to be selected by default.
    *                                 Must match the departement name exactly,
    *                                 OR departement code
    *                                 (though it can be a different case).
    * @param  string $promoText       The text to appear as the first option
    * @param  string $extraAttributes Any extra attributes for the <select> tag
    * @return string                  The HTML for the <select>
    * @access public
    */
    function toHTML($name, $selectedOption = null, $promoText = 'Selectionnez un departement...', $extraAttributes = '')
    {
        $options[]      = sprintf('<option value="">%s</option>', $promoText);
        $selectedOption = strtolower($selectedOption);

        foreach ($this->_departements as $code => $departement) {
            $code = strtolower($code);
            //$departements_lc  = explode(' - ',$departements_ld);
            $selected  = ( $selectedOption == $code || $selectedOption == strtolower($departement) ) ? ' selected="selected" ' : '';
            $options[] = '<option value="' . $code . '"' . $selected . '>' . $code . ' - ' . $departement . '</option>';
        }
        
        return sprintf('<select name="%s" %s>%s</select>',
                       $name,
                       $extraAttributes,
                       implode("\r\n", $options));
    }
}

?>
//
// ChemDoodle Web Components 3.5.0
//
// http://web.chemdoodle.com
//
// Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// As a special exception to the GPL, any HTML file which merely makes
// function calls to this code, and for that purpose includes it by
// reference, shall be deemed a separate work for copyright law purposes.
// If you modify this code, you may extend this exception to your version
// of the code, but you are not obligated to do so. If you do not wish to
// do so, delete this exception statement from your version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// Please contact iChemLabs <http://www.ichemlabs.com/contact> for
// alternate licensing options.
//
//
//  Copyright 2006-2010 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2794 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-13 12:12:28 -0400 (Fri, 13 Aug 2010) $
//

String.prototype.startsWith = function(str) {
	return this.match("^"+str)==str;
}
vec3.angleFrom = function(vec, vec2){
	var length1 = vec3.length(vec);
	var length2 = vec3.length(vec2);
	var dot = vec3.dot(vec, vec2);
	var cosine = dot/length1/length2;
	return Math.acos(cosine);
}
//
//  Copyright 2006-2010 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//

/* Creates a new Queue. A Queue is a first-in-first-out (FIFO) data structure.
 * Functions of the Queue object allow elements to be enqueued and dequeued, the
 * first element to be obtained without dequeuing, and for the current size of
 * the Queue and empty/non-empty status to be obtained.
 */
function Queue(){

  // the list of elements, initialised to the empty array
  var queue = [];

  // the amount of space at the front of the queue, initialised to zero
  var queueSpace = 0;

  /* Returns the size of this Queue. The size of a Queue is equal to the number
   * of elements that have been enqueued minus the number of elements that have
   * been dequeued.
   */
  this.getSize = function(){

    // return the number of elements in the queue
    return queue.length - queueSpace;

  }

  /* Returns true if this Queue is empty, and false otherwise. A Queue is empty
   * if the number of elements that have been enqueued equals the number of
   * elements that have been dequeued.
   */
  this.isEmpty = function(){

    // return true if the queue is empty, and false otherwise
    return (queue.length == 0);

  }

  /* Enqueues the specified element in this Queue. The parameter is:
   *
   * element - the element to enqueue
   */
  this.enqueue = function(element){
    queue.push(element);
  }

  /* Dequeues an element from this Queue. The oldest element in this Queue is
   * removed and returned. If this Queue is empty then undefined is returned.
   */
  this.dequeue = function(){

    // initialise the element to return to be undefined
    var element = undefined;

    // check whether the queue is empty
    if (queue.length){

      // fetch the oldest element in the queue
      element = queue[queueSpace];

      // update the amount of space and check whether a shift should occur
      if (++queueSpace * 2 >= queue.length){

        // set the queue equal to the non-empty portion of the queue
        queue = queue.slice(queueSpace);

        // reset the amount of space at the front of the queue
        queueSpace=0;

      }

    }

    // return the removed element
    return element;

  }

  /* Returns the oldest element in this Queue. If this Queue is empty then
   * undefined is returned. This function returns the same value as the dequeue
   * function, but does not remove the returned element from this Queue.
   */
  this.getOldestElement = function(){

    // initialise the element to return to be undefined
    var element = undefined;

    // if the queue is not element then fetch the oldest element in the queue
    if (queue.length) element = queue[queueSpace];

    // return the oldest element
    return element;

  }

}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2777 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:48:33 -0400 (Thu, 12 Aug 2010) $
//
//default canvas properties
var default_backgroundColor = '#FFFFFF';
var default_scale = 1;
var default_rotateAngle = 0;
var default_bondLength_2D = 20;
var default_angstromsPerBondLength = 1.25;
var default_lightDirection_3D = [-.1, -.1, -1];
var default_lightDiffuseColor_3D = '#FFFFFF';
var default_lightSpecularColor_3D = '#FFFFFF';
var default_projectionVerticalFieldOfView_3D = 45;
var default_projectionWidthHeightRatio_3D = 1;
var default_projectionFrontCulling_3D = .1;
var default_projectionBackCulling_3D = 10000;

//default atom properties
var default_atoms_display = true;
var default_atoms_color = '#000000';
var default_atoms_font_size_2D = 12;
var default_atoms_font_families_2D = ['Helvetica', 'Arial', 'Dialog'];
var default_atoms_circles_2D = false;
var default_atoms_circleDiameter_2D = 10;
var default_atoms_circleBorderWidth_2D = 1;
var default_atoms_useJMOLColors = false;
var default_atoms_resolution_3D = 60;
var default_atoms_sphereDiameter_3D = .8;
var default_atoms_useVDWDiameters_3D = false;
var default_atoms_materialAmbientColor_3D = '#000000';
var default_atoms_materialSpecularColor_3D = '#555555';
var default_atoms_materialShininess_3D = 32;
var default_atoms_implicitHydrogens_2D = true;
var default_atoms_displayTerminalCarbonLabels_2D = false;
var default_atoms_showHiddenCarbons_2D = true;

//default bond properties
var default_bonds_display = true;
var default_bonds_color = '#000000';
var default_bonds_width_2D = 1;
var default_bonds_saturationWidth_2D = .2;
var default_bonds_ends_2D = 'round';
var default_bonds_useJMOLColors = false;
var default_bonds_saturationAngle_2D = Math.PI / 3;
var default_bonds_symmetrical_2D = false;
var default_bonds_clearOverlaps_2D = false;
var default_bonds_overlapClearWidth_2D = .5;
var default_bonds_atomLabelBuffer_2D = .25;
var default_bonds_wedgeThickness_2D = .22;
var default_bonds_hashWidth_2D = 1;
var default_bonds_hashSpacing_2D = 2.5;
var default_bonds_resolution_3D = 60;
var default_bonds_cylinderDiameter_3D = .3;
var default_bonds_materialAmbientColor_3D = '#222222';
var default_bonds_materialSpecularColor_3D = '#555555';
var default_bonds_materialShininess_3D = 32;

//default spectrum properties
var default_plots_color = '#000000';
var default_plots_width = 1;
var default_plots_showIntegration = false;
var default_plots_integrationColor = '#c10000';
var default_plots_integrationLineWidth = 1;
var default_plots_showGrid = false;
var default_plots_gridColor = 'gray';
var default_plots_gridLineWidth = .5;
var default_plots_showYAxis = true;
var default_plots_flipXAxis = false;
var default_text_font_size = 12;
var default_text_font_families = ['Helvetica', 'Arial', 'Dialog'];
var default_text_color = '#000000';


function VisualSpecifications(){

    //canvas properties
    this.backgroundColor = default_backgroundColor;
    this.scale = default_scale;
    this.rotateAngle = default_rotateAngle;
    this.bondLength = default_bondLength_2D;
    this.angstromsPerBondLength = default_angstromsPerBondLength;
    this.lightDirection_3D = default_lightDirection_3D;
    this.lightDiffuseColor_3D = default_lightDiffuseColor_3D;
    this.lightSpecularColor_3D = default_lightSpecularColor_3D;
    this.projectionVerticalFieldOfView_3D = default_projectionVerticalFieldOfView_3D;
    this.projectionWidthHeightRatio_3D = default_projectionWidthHeightRatio_3D;
    this.projectionFrontCulling_3D = default_projectionFrontCulling_3D;
    this.projectionBackCulling_3D = default_projectionBackCulling_3D;
    
    //atom properties
    this.atoms_display = default_atoms_display;
    this.atoms_color = default_atoms_color;
    this.atoms_font_size_2D = default_atoms_font_size_2D;
    this.atoms_font_families_2D = [];
    for (var i = 0, ii = default_atoms_font_families_2D.length; i < ii; i++) {
        this.atoms_font_families_2D[i] = default_atoms_font_families_2D[i];
    }
    this.atoms_circles_2D = default_atoms_circles_2D;
    this.atoms_circleDiameter_2D = default_atoms_circleDiameter_2D;
    this.atoms_circleBorderWidth_2D = default_atoms_circleBorderWidth_2D;
    this.atoms_useJMOLColors = default_atoms_useJMOLColors;
    this.atoms_resolution_3D = default_atoms_resolution_3D;
    this.atoms_sphereDiameter_3D = default_atoms_sphereDiameter_3D;
    this.atoms_useVDWDiameters_3D = default_atoms_useVDWDiameters_3D;
    this.atoms_materialAmbientColor_3D = default_atoms_materialAmbientColor_3D;
    this.atoms_materialSpecularColor_3D = default_atoms_materialSpecularColor_3D;
    this.atoms_materialShininess_3D = default_atoms_materialShininess_3D;
    this.atoms_implicitHydrogens_2D = default_atoms_implicitHydrogens_2D;
    this.atoms_displayTerminalCarbonLabels_2D = default_atoms_displayTerminalCarbonLabels_2D;
    this.atoms_showHiddenCarbons_2D = default_atoms_showHiddenCarbons_2D;
    
    //bond properties
    this.bonds_display = default_bonds_display;
    this.bonds_color = default_bonds_color;
    this.bonds_width_2D = default_bonds_width_2D;
    this.bonds_saturationWidth_2D = default_bonds_saturationWidth_2D;
    this.bonds_ends_2D = default_bonds_ends_2D;
    this.bonds_useJMOLColors = default_bonds_useJMOLColors;
    this.bonds_saturationAngle_2D = default_bonds_saturationAngle_2D;
    this.bonds_symmetrical_2D = default_bonds_symmetrical_2D;
    this.bonds_clearOverlaps_2D = default_bonds_clearOverlaps_2D;
    this.bonds_overlapClearWidth_2D = default_bonds_overlapClearWidth_2D;
    this.bonds_atomLabelBuffer_2D = default_bonds_atomLabelBuffer_2D;
    this.bonds_wedgeThickness_2D = default_bonds_wedgeThickness_2D;
    this.bonds_hashWidth_2D = default_bonds_hashWidth_2D;
    this.bonds_hashSpacing_2D = default_bonds_hashSpacing_2D;
    this.bonds_resolution_3D = default_bonds_resolution_3D;
    this.bonds_cylinderDiameter_3D = default_bonds_cylinderDiameter_3D;
    this.bonds_materialAmbientColor_3D = default_bonds_materialAmbientColor_3D;
    this.bonds_materialSpecularColor_3D = default_bonds_materialSpecularColor_3D;
    this.bonds_materialShininess_3D = default_bonds_materialShininess_3D;
    
    //spectrum properties
    this.plots_color = default_plots_color;
    this.plots_width = default_plots_width;
    this.plots_showIntegration = default_plots_showIntegration;
    this.plots_integrationColor = default_plots_integrationColor;
    this.plots_integrationLineWidth = default_plots_integrationLineWidth;
    this.plots_showGrid = default_plots_showGrid;
    this.plots_gridColor = default_plots_gridColor;
    this.plots_gridLineWidth = default_plots_gridLineWidth;
    this.plots_showYAxis = default_plots_showYAxis;
    this.plots_flipXAxis = default_plots_flipXAxis;
    this.text_font_size = default_text_font_size;
    this.text_font_families = [];
    for (var i = 0, ii = default_text_font_families.length; i < ii; i++) {
        this.text_font_families[i] = default_text_font_families[i];
    }
    this.text_color = default_text_color;
}

VisualSpecifications.prototype.set3DRepresentation = function(representation){
    this.bonds_color = '#777777';
    if (representation == 'Ball and Stick') {
        this.atoms_display = true;
        this.bonds_display = true;
        this.atoms_useVDWDiameters_3D = false;
        this.atoms_useJMOLColors = true;
        this.bonds_useJMOLColors = true;
        this.bonds_cylinderDiameter_3D = .3;
        this.atoms_sphereDiameter_3D = 1;
    }
    else 
        if (representation == 'van der Waals Spheres') {
            this.atoms_display = true;
            this.bonds_display = false;
            this.atoms_useVDWDiameters_3D = true;
            this.atoms_useJMOLColors = true;
            this.bonds_useJMOLColors = true;
        }
        else 
            if (representation == 'Stick') {
                this.atoms_display = true;
                this.bonds_display = true;
                this.atoms_useVDWDiameters_3D = false;
                this.atoms_useJMOLColors = true;
                this.bonds_useJMOLColors = true;
                this.bonds_cylinderDiameter_3D = this.atoms_sphereDiameter_3D = .8;
                this.bonds_materialAmbientColor_3D = this.atoms_materialAmbientColor_3D;
                this.bonds_materialSpecularColor_3D = this.atoms_materialSpecularColor_3D;
                this.bonds_materialShininess_3D = this.atoms_materialShininess_3D;
            }
            else 
                if (representation == 'Wireframe') {
                    this.atoms_display = true;
                    this.bonds_display = true;
                    this.atoms_useVDWDiameters_3D = false;
                    this.atoms_useJMOLColors = true;
                    this.bonds_useJMOLColors = true;
                    this.bonds_cylinderDiameter_3D = .05;
                    this.atoms_sphereDiameter_3D = .15;
                }
                else {
                    alert('"' + representation + '" is not recognized. Use one of the following strings:\n\n' +
                    '1. Ball and Stick\n' +
                    '2. van der Waals Spheres\n' +
                    '3. Stick\n' +
                    '4. Wireframe\n');
                }
}
VisualSpecifications.prototype.getFontString = function(size, families){
    var sb = [size + 'px '];
    for (var i = 0, ii = families.length; i < ii; i++) {
        sb.push((i != 0 ? ',' : '') + families[i]);
    };
    return sb.join('');
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2798 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-15 18:11:12 -0400 (Sun, 15 Aug 2010) $
//
jQuery(document).ready(function(){
    //handles dragging beyond the canvas bounds
    $(document).mousemove(function(e){
        if (CANVAS_DRAGGING != null) {
            if (CANVAS_DRAGGING.drag) {
                CANVAS_DRAGGING.prehandleEvent(e);
                CANVAS_DRAGGING.drag(e);
            }
        }
    });
    $(document).mouseup(function(e){
        if (CANVAS_DRAGGING != null) {
            if (CANVAS_DRAGGING.mouseup) {
                CANVAS_DRAGGING.prehandleEvent(e);
                CANVAS_DRAGGING.mouseup(e);
            }
            CANVAS_DRAGGING = null;
        }
    });
    //handles modifier keys from a single keyboard
    $(document).keydown(function(e){
        SHIFT = e.shiftKey;
        ALT = e.altKey;
        var affecting = CANVAS_OVER;
        if (CANVAS_DRAGGING != null) {
            affecting = CANVAS_DRAGGING;
        }
        if (affecting != null) {
            if (affecting.keydown) {
                affecting.prehandleEvent(e);
                affecting.keydown(e);
            }
        }
    });
    $(document).keypress(function(e){
        var affecting = CANVAS_OVER;
        if (CANVAS_DRAGGING != null) {
            affecting = CANVAS_DRAGGING;
        }
        if (affecting != null) {
            if (affecting.keypress) {
                affecting.prehandleEvent(e);
                affecting.keypress(e);
            }
        }
    });
    $(document).keyup(function(e){
        SHIFT = e.shiftKey;
        ALT = e.altKey;
        var affecting = CANVAS_OVER;
        if (CANVAS_DRAGGING != null) {
            affecting = CANVAS_DRAGGING;
        }
        if (affecting != null) {
            if (affecting.keyup) {
                affecting.prehandleEvent(e);
                affecting.keyup(e);
            }
        }
    });
});

var CANVAS_DRAGGING = null;
var CANVAS_OVER = null;
var ALT = false;
var SHIFT = false;

function Canvas(){
    this.molecule = null;
    this.emptyMessage = null;
    this.image = null;
    this.inGesture = false;
    return true;
}

Canvas.prototype.repaint = function(){
    var canvas = document.getElementById(this.id);
    if (canvas.getContext) {
        var ctx = canvas.getContext('2d');
        if (this.image == null) {
            if (this.specs.backgroundColor != null) {
                ctx.fillStyle = this.specs.backgroundColor;
                ctx.fillRect(0, 0, this.width, this.height);
            }
        }
        else {
            ctx.drawImage(this.image, 0, 0);
        }
        if (this.molecule != null && this.molecule.atoms.length > 0) {
            ctx.save();
            ctx.translate(this.width / 2, this.height / 2);
            ctx.rotate(this.specs.rotateAngle);
            ctx.scale(this.specs.scale, this.specs.scale);
            ctx.translate(-this.width / 2, -this.height / 2);
            this.molecule.draw(ctx, this.specs);
            ctx.restore();
        }
        else 
            if (this.emptyMessage != null) {
                ctx.fillStyle = '#737683';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = '18px Helvetica, Verdana, Arial, Sans-serif';
                ctx.fillText(this.emptyMessage, this.width / 2, this.height / 2);
            }
        if (this.drawChildExtras) {
            this.drawChildExtras(ctx);
        }
    }
}
Canvas.prototype.setBackgroundImage = function(path){
    this.image = new Image(); // Create new Image object 
    var me = this;
    this.image.onload = function(){
        me.repaint();
    }
    this.image.src = path; // Set source path  
}
Canvas.prototype.loadMolecule = function(molecule){
    this.molecule = molecule;
    this.center();
    this.molecule.check();
    if (this.afterLoadMolecule) {
        this.afterLoadMolecule();
    }
    this.repaint();
}
Canvas.prototype.center = function(){
    var canvas = document.getElementById(this.id);
    var p = this.molecule.getCenter3D();
    var center = new Atom('C', this.width / 2, this.height / 2, 0);
    center.sub3D(p);
    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
        this.molecule.atoms[i].add3D(center);
    };
    var dim = this.molecule.getDimension();
    this.specs.scale = 1;
    if (dim.x > this.width || dim.y > this.height) {
        this.specs.scale = Math.min(this.width / dim.x, this.height / dim.y) * .85;
    }
}
Canvas.prototype.create = function(id, width, height){
    this.id = id;
    this.width = width;
    this.height = height;
    if (!supports_canvas_text() && $.browser.msie && $.browser.version >= '6') {
        // Install Google Chrome Frame
        document.writeln('<div style="border: 1px solid black;" width="' + width + '" height="' + height + '">Please install <a href="http://code.google.com/chrome/chromeframe/">Google Chrome Frame</a>, then restart Internet Explorer.</div>');
    }
    else {
        document.writeln('<canvas class="ChemDoodleWebComponent" id="' + id + '" width="' + width + '" height="' + height + '"></canvas>');
    }
    this.specs = new VisualSpecifications();
    //setup input events
    var me = this;
    //for iPhone OS and Android devices
    $('#' + id).bind('touchstart', function(e){
        if (me.touchstart) {
        	me.prehandleMobileEvent(e);
            me.touchstart(e);
        }
        else 
            if (me.mousedown) {
        		me.prehandleMobileEvent(e);
                me.mousedown(e);
            }
    });
    $('#' + id).bind('touchmove', function(e){
        if (!me.inGesture) {
			//must duplicate prehandleMobile event so that the default action is performed if not implemented
            ALT = e.originalEvent.changedTouches.length == 2;
            if (me.touchmove) {
            	me.prehandleMobileEvent(e);
                me.touchmove(e);
            }
            else 
                if (me.drag) {
            		me.prehandleMobileEvent(e);
                    me.drag(e);
                }
        }
    });
    $('#' + id).bind('touchend', function(e){
        if (me.touchend) {
        	me.prehandleMobileEvent(e);
            me.touchend(e);
        }
        else 
            if (me.mouseup) {
        		me.prehandleMobileEvent(e);
                me.mouseup(e);
            }
    });
    $('#' + id).bind('gesturestart', function(e){
        me.inGesture = true;
        if (me.gesturestart) {
            me.prehandleEvent(e);
            me.gesturestart(e);
        }
    });
    $('#' + id).bind('gesturechange', function(e){
        if (e.originalEvent.scale == 1) {
            me.inGesture = false;
        }
        else {
            if (me.gesturechange) {
                me.prehandleEvent(e);
                me.gesturechange(e);
            }
        }
    });
    $('#' + id).bind('gestureend', function(e){
        me.inGesture = false;
        if (me.gestureend) {
            me.prehandleEvent(e);
            me.gestureend(e);
        }
    });
    //normal events
    $('#' + id).click(function(e){
        switch (e.which) {
            case 1:
                //left mouse button pressed
                if (me.click) {
                    me.prehandleEvent(e);
                    me.click(e);
                }
                break;
            case 2:
                //middle mouse button pressed
                if (me.middleclick) {
                    me.prehandleEvent(e);
                    me.middleclick(e);
                }
                break;
            case 3:
                //right mouse button pressed
                if (me.rightclick) {
                    me.prehandleEvent(e);
                    me.rightclick(e);
                }
                break;
        }
    });
    $('#' + id).dblclick(function(e){
        if (me.dblclick) {
            me.prehandleEvent(e);
            me.dblclick(e);
        }
    });
    $('#' + id).mousedown(function(e){
        switch (e.which) {
            case 1:
                //left mouse button pressed
                CANVAS_DRAGGING = me;
                if (me.mousedown) {
                    me.prehandleEvent(e);
                    me.mousedown(e);
                }
                break;
            case 2:
                //middle mouse button pressed
                if (me.middlemousedown) {
                    me.prehandleEvent(e);
                    me.middlemousedown(e);
                }
                break;
            case 3:
                //right mouse button pressed
                if (me.rightmousedown) {
                    me.prehandleEvent(e);
                    me.rightmousedown(e);
                }
                break;
        }
    });
    $('#' + id).mousemove(function(e){
        if (CANVAS_DRAGGING == null && me.mousemove) {
            me.prehandleEvent(e);
            me.mousemove(e);
        }
    });
    $('#' + id).mouseout(function(e){
        CANVAS_OVER = null;
        if (me.mouseout) {
            me.prehandleEvent(e);
            me.mouseout(e);
        }
    });
    $('#' + id).mouseover(function(e){
        CANVAS_OVER = me;
        if (me.mouseover) {
            me.prehandleEvent(e);
            me.mouseover(e);
        }
    });
    $('#' + id).mouseup(function(e){
        switch (e.which) {
            case 1:
                //left mouse button pressed
                if (me.mouseup) {
                    me.prehandleEvent(e);
                    me.mouseup(e);
                }
                break;
            case 2:
                //middle mouse button pressed
                if (me.middlemouseup) {
                    me.prehandleEvent(e);
                    me.middlemouseup(e);
                }
                break;
            case 3:
                //right mouse button pressed
                if (me.rightmouseup) {
                    me.prehandleEvent(e);
                    me.rightmouseup(e);
                }
                break;
        }
    });
    $('#' + id).mousewheel(function(e, delta){
        if (me.mousewheel) {
            me.prehandleEvent(e);
            me.mousewheel(e, delta);
        }
    });
    if (this.subCreate) {
        this.subCreate();
    }
}
Canvas.prototype.getMolecule = function(){
    return this.molecule;
}
Canvas.prototype.prehandleEvent = function(e){
    e.preventDefault();
    var offset = $('#' + this.id).offset();
    e.p = new Point(e.pageX - offset.left, e.pageY - offset.top);
}
Canvas.prototype.prehandleMobileEvent = function(e){
    e.pageX = e.originalEvent.changedTouches[0].pageX;
    e.pageY = e.originalEvent.changedTouches[0].pageY;
    e.preventDefault();
    var offset = $('#' + this.id).offset();
    e.p = new Point(e.pageX - offset.left + window.pageXOffset, e.pageY - offset.top + window.pageYOffset);
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function AnimatorCanvas(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    this.handle = null;
    this.timeout = 50;
    return true;
}

AnimatorCanvas.prototype = new Canvas();

AnimatorCanvas.prototype.startAnimation = function(){
    this.stopAnimation();
    var me = this;
    if (this.nextFrame) {
        this.handle = setInterval(function(){
            me.nextFrame();
            me.repaint();
        }, this.timeout);
    }
}
AnimatorCanvas.prototype.stopAnimation = function(){
    if (this.handle != null) {
        clearInterval(this.handle);
        this.handle = null;
    }
}
AnimatorCanvas.prototype.isRunning = function(){
    return this.handle != null;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2777 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:48:33 -0400 (Thu, 12 Aug 2010) $
//
function DoodleCanvas(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    this.specs.atoms_useJMOLColors = true;
    this.specs.atoms_circleDiameter_2D = 7;
    this.specs.atoms_circleBorderWidth_2D = 0;
    this.isHelp = false;
    this.helpPos = new Point(this.width - 20, 20);
    this.tempAtom = null;
    var molecule = new Molecule();
    molecule.atoms[0] = new Atom('C', 0, 0, 0);
    this.loadMolecule(molecule);
    return true;
}

DoodleCanvas.prototype = new Canvas();

DoodleCanvas.prototype.drawChildExtras = function(ctx){
    if (this.tempAtom != null) {
        ctx.strokeStyle = '#00FF00';
        ctx.fillStyle = '#00FF00';
        ctx.lineWidth = 1.2;
        for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
            if (this.molecule.atoms[i].isSelected) {
                ctx.beginPath();
                ctx.moveTo(this.molecule.atoms[i].x, this.molecule.atoms[i].y);
                ctx.lineTo(this.tempAtom.x, this.tempAtom.y);
                ctx.stroke();
                ctx.beginPath();
                ctx.arc(this.tempAtom.x, this.tempAtom.y, 3, 0, Math.PI * 2, false);
                ctx.fill();
                if (this.tempAtom.isOverlap) {
                    ctx.strokeStyle = '#C10000';
                    ctx.lineWidth = 1.2;
                    ctx.beginPath();
                    ctx.arc(this.tempAtom.x, this.tempAtom.y, 7, 0, Math.PI * 2, false);
                    ctx.stroke();
                }
            }
        };
            }
    var radgrad = ctx.createRadialGradient(this.width - 20, 20, 10, this.width - 20, 20, 2);
    radgrad.addColorStop(0, '#00680F');
    radgrad.addColorStop(1, '#FFFFFF');
    ctx.fillStyle = radgrad;
    ctx.beginPath();
    ctx.arc(this.helpPos.x, this.helpPos.y, 10, 0, Math.PI * 2, false);
    ctx.fill();
    if (this.isHelp) {
        ctx.lineWidth = 2;
        ctx.strokeStyle = 'black';
        ctx.stroke();
    }
    ctx.fillStyle = this.isHelp ? 'red' : 'black';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.font = '14px sans-serif';
    ctx.fillText('?', this.helpPos.x, this.helpPos.y);
}
DoodleCanvas.prototype.drag = function(e){
    var changed = false;
    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
        if (this.molecule.atoms[i].isSelected) {
            changed = true;
            if (e.p.distance(this.molecule.atoms[i]) < 7) {
                var x = this.molecule.atoms[i].x;
                var y = this.molecule.atoms[i].y;
                var angles = this.molecule.getAngles(this.molecule.atoms[i]);
                if (angles.length == 0) {
                    x += this.specs.bondLength * Math.cos(-Math.PI / 6);
                    y += this.specs.bondLength * Math.sin(-Math.PI / 6);
                }
                else 
                    if (angles.length == 1) {
                        var radian = 0;
                        var b = null;
                        for (var j = 0, jj = this.molecule.bonds.length; j < jj; j++) {
                            if (this.molecule.bonds[j].contains(this.molecule.atoms[i])) {
                                b = this.molecule.bonds[j];
                            }
                        };
                        if (b.bondOrder >= 3) {
                            radian = angles[0] + Math.PI;
                        }
                        else {
                            var concerned = angles[0] % Math.PI * 2;
                            if (isBetween(concerned, 0, Math.PI / 2) || isBetween(concerned, Math.PI, 3 * Math.PI / 2)) {
                                radian = angles[0] + 2 * Math.PI / 3;
                            }
                            else {
                                radian = angles[0] - 2 * Math.PI / 3;
                            }
                        }
                        x += this.specs.bondLength * Math.cos(radian);
                        y -= this.specs.bondLength * Math.sin(radian);
                    }
                    else {
                        var use = angleBetweenLargest(angles);
                        x += this.specs.bondLength * Math.cos(use);
                        y -= this.specs.bondLength * Math.sin(use);
                    }
                this.tempAtom = new Atom('C', x, y, 0);
            }
            else {
                if (ALT && SHIFT) {
                    this.tempAtom = new Atom('C', e.p.x, e.p.y, 0);
                }
                else {
                    var angle = this.molecule.atoms[i].angle(e.p);
                    var length = this.molecule.atoms[i].distance(e.p);
                    if (!SHIFT) {
                        length = this.specs.bondLength;
                    }
                    if (!ALT) {
                        var increments = Math.floor((angle + Math.PI / 12) / (Math.PI / 6));
                        angle = increments * Math.PI / 6;
                    }
                    this.tempAtom = new Atom('C', this.molecule.atoms[i].x + length * Math.cos(angle), this.molecule.atoms[i].y - length * Math.sin(angle), 0);
                }
            }
            for (var j = 0, jj = this.molecule.atoms.length; j < jj; j++) {
                if (this.molecule.atoms[j].distance(this.tempAtom) < 5) {
                    this.tempAtom.x = this.molecule.atoms[j].x;
                    this.tempAtom.y = this.molecule.atoms[j].y;
                    this.tempAtom.isOverlap = true;
                }
            }
            break;
        }
    };
    if (!changed) {
        var dif = new Point(e.p.x, e.p.y);
        dif.sub(this.lastPoint);
        for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
            this.molecule.atoms[i].add(dif);
        }
        for (var i = 0, ii = this.molecule.rings.length; i < ii; i++) {
            this.molecule.rings[i].center = this.molecule.rings[i].getCenter();
        }
    }
    this.lastPoint = e.p;
    this.repaint();
}
DoodleCanvas.prototype.mousedown = function(e){
    this.lastPoint = e.p;
    if (this.isHelp) {
        this.isHelp = false;
        this.repaint();
        window.open('http://web.chemdoodle.com/DoodlerTutorial.html');
        return;
    }
    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
        if (this.molecule.atoms[i].isHover) {
            this.molecule.atoms[i].isHover = false;
            this.molecule.atoms[i].isSelected = true;
            this.drag(e);
            return;
        }
    };
    for (var i = 0, ii = this.molecule.bonds.length; i < ii; i++) {
        if (this.molecule.bonds[i].isHover) {
            this.molecule.bonds[i].isHover = false;
            this.molecule.bonds[i].bondOrder += (this.molecule.bonds[i].bondOrder % 1) + 1;
            if (this.molecule.bonds[i].bondOrder > 3) {
                this.molecule.bonds[i].bondOrder = 1;
            }
            this.molecule.check();
            this.repaint();
            return;
        }
    };
    }
DoodleCanvas.prototype.click = function(e){
    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
        if (this.tempAtom != null && this.molecule.atoms[i].isSelected) {
            if (this.tempAtom.isOverlap) {
                for (var j = 0, jj = this.molecule.atoms.length; j < jj; j++) {
                    if (this.molecule.atoms[j].distance(this.tempAtom) < 5) {
                        this.tempAtom = this.molecule.atoms[j];
                    }
                }
            }
            else {
                this.molecule.atoms[this.molecule.atoms.length] = this.tempAtom;
            }
            var found = false;
            for (var j = 0, jj = this.molecule.bonds.length; j < jj; j++) {
                if (this.molecule.bonds[j].contains(this.molecule.atoms[i]) && this.molecule.bonds[j].contains(this.tempAtom)) {
                    found = true;
                    this.molecule.bonds[j].bondOrder += (this.molecule.bonds[j].bondOrder % 1) + 1;
                    if (this.molecule.bonds[j].bondOrder > 3) {
                        this.molecule.bonds[j].bondOrder = 1;
                    }
                }
            }
            if (!found) {
                this.molecule.bonds[this.molecule.bonds.length] = new Bond(this.molecule.atoms[i], this.tempAtom, 1);
            }
            this.molecule.check();
        }
        this.molecule.atoms[i].isSelected = false;
    };
    this.tempAtom = null;
    this.mousemove(e);
}
DoodleCanvas.prototype.mousemove = function(e){
    if (this.tempAtom != null) {
        return;
    }
    var min = Infinity;
    var hovering = null;
    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
        this.molecule.atoms[i].isHover = false;
        var dist = e.p.distance(this.molecule.atoms[i]);
        if (dist < this.specs.bondLength && dist < min) {
            min = dist;
            hovering = this.molecule.atoms[i];
        }
    };
    for (var i = 0, ii = this.molecule.bonds.length; i < ii; i++) {
        this.molecule.bonds[i].isHover = false;
        var dist = e.p.distance(this.molecule.bonds[i].getCenter());
        if (dist < this.specs.bondLength && dist < min) {
            min = dist;
            hovering = this.molecule.bonds[i];
        }
    };
    if (hovering != null) {
        hovering.isHover = true;
    }
    this.isHelp = false;
    if (e.p.distance(this.helpPos) < 10) {
        this.isHelp = true;
    }
    this.repaint();
}
DoodleCanvas.prototype.keyup = function(e){
    if (CANVAS_DRAGGING == this) {
        if (this.lastPoint != null) {
            e.p = this.lastPoint;
            this.drag(e);
        }
    }
}
DoodleCanvas.prototype.keydown = function(e){
    if (CANVAS_DRAGGING == this) {
        if (this.lastPoint != null) {
            e.p = this.lastPoint;
            this.drag(e);
        }
    }
    else 
        if (e.keyCode >= 37 && e.keyCode <= 40) {
            var difx = 0;
            var dify = 0;
            switch (e.keyCode) {
                case 37:
                    difx = -10;
                    break;
                case 38:
                    dify = -10;
                    break;
                case 39:
                    difx = 10;
                    break;
                case 40:
                    dify = 10;
                    break;
            }
            for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
                this.molecule.atoms[i].x += difx;
                this.molecule.atoms[i].y += dify;
            }
            for (var i = 0, ii = this.molecule.rings.length; i < ii; i++) {
                this.molecule.rings[i].center = this.molecule.rings[i].getCenter();
            }
            this.repaint();
        }
        else 
            if (e.keyCode == 187 || e.keyCode == 189) {
                for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
                    if (this.molecule.atoms[i].isHover) {
                        this.molecule.atoms[i].charge += e.keyCode == 187 ? 1 : -1;
                        this.repaint();
                        break;
                    }
                }
            }
            else 
                if (e.keyCode == 8 || e.keyCode == 127) {
                    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
                        if (this.molecule.atoms[i].isHover) {
                            for (var j = 0, jj = this.molecule.atoms.length; j < jj; j++) {
                                this.molecule.atoms[j].visited = false;
                            }
                            var connectionsA = [];
                            var connectionsB = [];
                            this.molecule.atoms[i].visited = true;
                            for (var j = 0, jj = this.molecule.bonds.length; j < jj; j++) {
                                if (this.molecule.bonds[j].contains(this.molecule.atoms[i])) {
                                    var atoms = [];
                                    var bonds = [];
                                    var q = new Queue();
                                    q.enqueue(this.molecule.bonds[j].getNeighbor(this.molecule.atoms[i]));
                                    while (!q.isEmpty()) {
                                        var a = q.dequeue();
                                        if (!a.visited) {
                                            a.visited = true;
                                            atoms[atoms.length] = a;
                                            for (var k = 0, kk = this.molecule.bonds.length; k < kk; k++) {
                                                if (this.molecule.bonds[k].contains(a) && !this.molecule.bonds[k].getNeighbor(a).visited) {
                                                    q.enqueue(this.molecule.bonds[k].getNeighbor(a));
                                                    bonds[bonds.length] = this.molecule.bonds[k];
                                                }
                                            }
                                        }
                                    }
                                    connectionsA[connectionsA.length] = atoms;
                                    connectionsB[connectionsB.length] = bonds;
                                }
                            }
                            var largest = -1;
                            var index = -1;
                            for (var j = 0, jj = connectionsA.length; j < jj; j++) {
                                if (connectionsA[j].length > largest) {
                                    index = j;
                                    largest = connectionsA[j].length;
                                }
                            }
                            if (index > -1) {
                                this.molecule.atoms = connectionsA[index];
                                this.molecule.bonds = connectionsB[index];
                                this.molecule.check();
                            }
                            else {
                                var molecule = new Molecule();
                                molecule.atoms[0] = new Atom('C', 0, 0, 0);
                                this.loadMolecule(molecule);
                            }
                            this.repaint();
                            break;
                        }
                    }
                }
                else {
                    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
                        if (this.molecule.atoms[i].isHover) {
                            var check = String.fromCharCode(e.keyCode);
                            var firstMatch = null;
                            var firstAfterMatch = null;
                            var found = false;
                            for (var j = 0, jj = symbols.length; j < jj; j++) {
                                if (this.molecule.atoms[i].label.charAt(0) == check) {
                                    if (symbols[j] == this.molecule.atoms[i].label) {
                                        found = true;
                                    }
                                    else 
                                        if (symbols[j].charAt(0) == check) {
                                            if (found && firstAfterMatch == null) {
                                                firstAfterMatch = symbols[j];
                                            }
                                            else 
                                                if (firstMatch == null) {
                                                    firstMatch = symbols[j];
                                                }
                                        }
                                }
                                else {
                                    if (symbols[j].charAt(0) == check) {
                                        firstMatch = symbols[j];
                                        break;
                                    }
                                }
                            };
                            if (firstAfterMatch != null) {
                                this.molecule.atoms[i].label = firstAfterMatch;
                            }
                            else 
                                if (firstMatch != null) {
                                    this.molecule.atoms[i].label = firstMatch;
                                }
                            this.molecule.check();
                            this.repaint();
                            break;
                        }
                    }
                }
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2791 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-13 07:42:06 -0400 (Fri, 13 Aug 2010) $
//
function FileCanvas(id, width, height, action) {
    if (id) {
        this.create(id, width, height);
    }
    form = '<br><form name="FileForm" enctype="multipart/form-data" method="POST" action="'+action+'" target="HiddenFileFrame"><input type="file" name="f" /><input type="submit" name="submitbutton" value="Show File" /></form><iframe id="HFF-'+id+'" name="HiddenFileFrame" height="0" width="0" style="display:none;" onLoad="GetMolFromFrame(\'HFF-'+id+'\', '+id+')"></iframe>';
    document.writeln(form);
    this.emptyMessage = 'Click below to load file';
    this.repaint();
    return true;
}

FileCanvas.prototype = new Canvas();
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2792 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-13 09:24:30 -0400 (Fri, 13 Aug 2010) $
//
function HyperlinkCanvas(id, width, height, urlOrFunction, color, size){
    if (id) {
        this.create(id, width, height);
    }
    this.urlOrFunction = urlOrFunction;
    this.color = color ? color : 'blue';
    this.size = size ? size : 2;
    this.openInNewWindow = true;
    this.hoverImage = null;
    this.e = null;
    return true;
}

HyperlinkCanvas.prototype = new Canvas();

HyperlinkCanvas.prototype.drawChildExtras = function(ctx){
    if (this.e != null) {
        if (this.hoverImage == null) {
            ctx.strokeStyle = this.color;
            ctx.lineWidth = this.size * 2;
            ctx.strokeRect(0, 0, this.width, this.height);
        }
        else {
            ctx.drawImage(this.hoverImage, 0, 0);
        }
    }
}
HyperlinkCanvas.prototype.setHoverImage = function(url){
    this.hoverImage = new Image();
    this.hoverImage.src = url;
}
HyperlinkCanvas.prototype.click = function(p){
    this.e = null;
    this.repaint();
    if (this.urlOrFunction instanceof Function) {
        this.urlOrFunction();
    }
    else {
        if (this.openInNewWindow) {
            window.open(this.urlOrFunction);
        }
        else {
            location.href = this.urlOrFunction;
        }
    }
}
HyperlinkCanvas.prototype.mouseout = function(e){
    this.e = null;
    this.repaint();
}
HyperlinkCanvas.prototype.mouseover = function(e){
    this.e = e;
    this.repaint();
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2777 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:48:33 -0400 (Thu, 12 Aug 2010) $
//
function MolGrabberCanvas(id, width, height, action){
    if (id) {
        this.create(id, width, height);
    }
    form = '<form name="MolGrabberForm" method="POST" action="' + action + '" target="HiddenMolGrabberFrame" onSubmit="ValidateMolecule(MolGrabberForm); return false;"><input type="hidden" name="dim" value="2" /><input type="text" name="q" value="" /><input type="submit" name="submitbutton" value="Show Molecule" /></form><iframe id="HMGF-' + id + '" name="HiddenMolGrabberFrame" height="0" width="0" style="display:none;" onLoad="GetMolFromFrame(\'HMGF-' + id + '\', ' + id + ')"></iframe>';
    document.writeln(form);
    this.emptyMessage = 'Enter search term below';
    this.repaint();
    return true;
}

MolGrabberCanvas.prototype = new Canvas();

MolGrabberCanvas.prototype.setSearchTerm = function(term){
    document.MolGrabberForm.q.value = term;
    document.MolGrabberForm.submit();
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2792 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-13 09:24:30 -0400 (Fri, 13 Aug 2010) $
//
function RotatorCanvas(id, width, height, rotate3D){
    if (id) {
        this.create(id, width, height);
    }
    this.rotate3D = rotate3D;
    var increment = Math.PI / 360;
    this.xIncrement = increment;
    this.yIncrement = increment;
    this.zIncrement = increment;
    return true;
}

RotatorCanvas.prototype = new AnimatorCanvas();

RotatorCanvas.prototype.nextFrame = function(){
    if (this.molecule == null) {
        this.stopAnimation();
        return;
    }
    if (this.rotate3D) {
        var matrix = [];
        mat4.identity(matrix);
        mat4.rotate(matrix, this.xIncrement, [1, 0, 0]);
        mat4.rotate(matrix, this.yIncrement, [0, 1, 0]);
        mat4.rotate(matrix, this.zIncrement, [0, 0, 1]);
        for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
            var a = this.molecule.atoms[i];
            var p = [a.x - this.width / 2, a.y - this.height / 2, a.z];
            mat4.multiplyVec3(matrix, p);
            a.x = p[0] + this.width / 2;
            a.y = p[1] + this.height / 2;
            a.z = p[2];
        }
        for (var i = 0, ii = this.molecule.rings.length; i < ii; i++) {
            this.molecule.rings[i].center = this.molecule.rings[i].getCenter();
        }
        if (this.specs.atoms_display && this.specs.atoms_circles_2D) {
            this.molecule.sortAtomsByZ();
        }
        if (this.specs.bonds_display && this.specs.bonds_clearOverlaps_2D) {
            this.molecule.sortBondsByZ();
        }
    }
    else {
        this.specs.rotateAngle += this.zIncrement;
    }
}
RotatorCanvas.prototype.dblclick = function(e){
    if (this.isRunning()) {
        this.stopAnimation();
    }
    else {
        this.startAnimation();
    }
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function SlideshowCanvas(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    this.molecules = [];
    this.curIndex = 0;
    this.timeout = 5000;
    this.alpha = 0;
    this.innerHandle = null;
    this.phase = 0;
    return true;
}

SlideshowCanvas.prototype = new AnimatorCanvas();

SlideshowCanvas.prototype.drawChildExtras = function(ctx){
    ctx.fillStyle = 'rgba(' + parseInt(this.specs.backgroundColor.substring(1, 3), 16) + ', ' + parseInt(this.specs.backgroundColor.substring(3, 5), 16) + ', ' + parseInt(this.specs.backgroundColor.substring(5, 7), 16) + ', ' + this.alpha + ')';
    ctx.fillRect(0, 0, this.width, this.height);
}
SlideshowCanvas.prototype.nextFrame = function(){
    if (this.molecules.length == 0) {
        this.stopAnimation();
        return;
    }
    this.phase = 0;
    var me = this;
    var count = 1;
    this.innerHandle = setInterval(function(){
        me.alpha = count / 15;
        me.repaint();
        if (count == 15) {
            me.breakInnerHandle();
        }
        count++;
    }, 33);
}
SlideshowCanvas.prototype.breakInnerHandle = function(){
    if (this.innerHandle != null) {
        clearInterval(this.innerHandle);
        this.innerHandle = null;
    }
    if (this.phase == 0) {
        this.curIndex++;
        if (this.curIndex > this.molecules.length - 1) {
            this.curIndex = 0;
        }
        this.alpha = 1;
        this.loadMolecule(this.molecules[this.curIndex]);
        this.phase = 1;
        var me = this;
        var count = 1;
        this.innerHandle = setInterval(function(){
            me.alpha = (15 - count) / 15;
            me.repaint();
            if (count == 15) {
                me.breakInnerHandle();
            }
            count++;
        }, 33);
    }
    else 
        if (this.phase == 1) {
            this.alpha = 0;
            this.repaint();
        }
}
SlideshowCanvas.prototype.addMolecule = function(molecule){
    if (this.molecules.length == 0) {
        this.loadMolecule(molecule);
    }
    this.molecules[this.molecules.length] = molecule;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2797 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-14 09:12:18 -0400 (Sat, 14 Aug 2010) $
//
function TransformCanvas(id, width, height, rotate3D){
    if (id) {
        this.create(id, width, height);
    }
    this.lastPoint = null;
    this.rotate3D = rotate3D;
    this.rotationMultMod = 1.3;
    this.lastPinchScale = 1;
    return true;
}

TransformCanvas.prototype = new Canvas();

TransformCanvas.prototype.mousedown = function(e){
    this.lastPoint = e.p;
    this.lastPinchScale = 1;
}
TransformCanvas.prototype.rightmousedown = function(e){
    this.lastPoint = e.p;
}
TransformCanvas.prototype.drag = function(e){
    if (ALT) {
        var t = new Point(e.p.x, e.p.y);
        t.sub(this.lastPoint);
        for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
            this.molecule.atoms[i].add(t);
        };
        this.lastPoint = e.p;
        this.repaint();
    }
    else {
        if (this.rotate3D == true) {
            var diameter = Math.max(this.width / 4, this.height / 4);
            var difx = e.p.x - this.lastPoint.x;
            var dify = e.p.y - this.lastPoint.y;
            var yIncrement = difx / diameter * this.rotationMultMod;
            var xIncrement = -dify / diameter * this.rotationMultMod;
            var matrix = [];
            mat4.identity(matrix);
            mat4.rotate(matrix, xIncrement, [1, 0, 0]);
            mat4.rotate(matrix, yIncrement, [0, 1, 0]);
            for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
                var a = this.molecule.atoms[i];
                var p = [a.x - this.width / 2, a.y - this.height / 2, a.z];
                mat4.multiplyVec3(matrix, p);
                a.x = p[0] + this.width / 2;
                a.y = p[1] + this.height / 2;
                a.z = p[2];
            }
            for (var i = 0, ii = this.molecule.rings.length; i < ii; i++) {
                this.molecule.rings[i].center = this.molecule.rings[i].getCenter();
            }
            this.lastPoint = e.p;
            if (this.specs.atoms_display && this.specs.atoms_circles_2D) {
                this.molecule.sortAtomsByZ();
            }
            if (this.specs.bonds_display && this.specs.bonds_clearOverlaps_2D) {
                this.molecule.sortBondsByZ();
            }
            this.repaint();
        }
        else {
            var center = new Point(this.width / 2, this.height / 2);
            var before = center.angle(this.lastPoint);
            var after = center.angle(e.p);
            this.specs.rotateAngle -= (after - before);
            this.lastPoint = e.p;
            this.repaint();
        }
    }
}
TransformCanvas.prototype.mousewheel = function(e, delta){
    this.specs.scale += delta / 100;
    if (this.specs.scale < .01) {
        this.specs.scale = .01;
    }
    this.repaint();
}
TransformCanvas.prototype.gesturechange = function(e){
    this.specs.scale *= e.originalEvent.scale / this.lastPinchScale;
    if (this.specs.scale < .01) {
        this.specs.scale = .01;
    }
    this.lastPinchScale = e.originalEvent.scale;
    this.repaint();
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 1602 $
//  $Author: kevin $
//  $LastChangedDate: 2009-09-19 01:12:58 -0400 (Sat, 19 Sep 2009) $
//
function ViewerCanvas(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    return true;
}
ViewerCanvas.prototype = new Canvas();
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function SpectrumCanvas(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    this.spectrum = null;
    this.emptyMessage = 'No Spectrum Loaded or Recognized';
    return true;
}

SpectrumCanvas.prototype = new Canvas();

SpectrumCanvas.prototype.repaint = function(){
    var canvas = document.getElementById(this.id);
    if (canvas.getContext) {
        var ctx = canvas.getContext('2d');
        if (this.image == null) {
            if (this.specs.backgroundColor != null) {
                ctx.fillStyle = this.specs.backgroundColor;
                ctx.fillRect(0, 0, this.width, this.height);
            }
        }
        else {
            ctx.drawImage(this.image, 0, 0);
        }
        if (this.spectrum != null && this.spectrum.data.length > 0) {
            this.spectrum.draw(ctx, this.specs, this.width, this.height);
        }
        else 
            if (this.emptyMessage != null) {
                ctx.fillStyle = '#737683';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = '18px Helvetica, Verdana, Arial, Sans-serif';
                ctx.fillText(this.emptyMessage, this.width / 2, this.height / 2);
            }
        if (this.drawChildExtras) {
            this.drawChildExtras(ctx);
        }
    }
}
SpectrumCanvas.prototype.loadSpectrum = function(spectrum){
    this.spectrum = spectrum;
    this.repaint();
}
SpectrumCanvas.prototype.getSpectrum = function(){
    return this.spectrum;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function ObserverCanvas(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    return true;
}

ObserverCanvas.prototype = new SpectrumCanvas();
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2797 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-14 09:12:18 -0400 (Sat, 14 Aug 2010) $
//
function PerspectiveCanvas(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    this.dragRange = null;
    this.lastPinchScale = 1;
    return true;
}

PerspectiveCanvas.prototype = new SpectrumCanvas();

PerspectiveCanvas.prototype.mousedown = function(e){
    this.dragRange = new Point(e.p.x, e.p.x);
}
PerspectiveCanvas.prototype.mouseup = function(e){
    if (this.dragRange != null && this.dragRange.x != this.dragRange.y) {
        this.spectrum.zoom(this.dragRange.x, e.p.x, this.width);
        this.dragRange = null;
        this.repaint();
    }
}
PerspectiveCanvas.prototype.drag = function(e){
    this.dragRange.y = e.p.x;
    this.repaint();
}
PerspectiveCanvas.prototype.drawChildExtras = function(ctx){
    if (this.dragRange != null) {
        var xs = Math.min(this.dragRange.x, this.dragRange.y);
        var xe = Math.max(this.dragRange.x, this.dragRange.y);
        ctx.strokeStyle = 'gray';
        ctx.lineStyle = 1;
        ctx.beginPath();
        ctx.moveTo(xs, this.height / 2);
        for (var i = xs; i <= xe; i++) {
            if (i % 10 < 5) {
                ctx.lineTo(i, Math.round(this.height / 2));
            }
            else {
                ctx.moveTo(i, Math.round(this.height / 2));
            }
        }
        ctx.stroke();
    }
}
PerspectiveCanvas.prototype.mousewheel = function(e, delta){
    this.specs.scale += delta / 100;
    if (this.specs.scale < .01) {
        this.specs.scale = .01;
    }
    this.repaint();
}
PerspectiveCanvas.prototype.dblclick = function(e){
    this.spectrum.setup();
    this.specs.scale = 1;
    this.repaint();
}
PerspectiveCanvas.prototype.gesturechange = function(e){
    this.specs.scale *= e.originalEvent.scale / this.lastPinchScale;
    if (this.specs.scale < .01) {
        this.specs.scale = .01;
    }
    this.lastPinchScale = e.originalEvent.scale;
    this.repaint();
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2794 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-13 12:12:28 -0400 (Fri, 13 Aug 2010) $

var NO_WEBGL_WARNING = false;

function Canvas3D(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    this.rotationMatrix = mat4.identity([]);
    this.translationMatrix = mat4.identity([]);
    this.lastPoint = null;
    return true;
}

Canvas3D.prototype = new Canvas();

Canvas3D.prototype.afterLoadMolecule = function(){
    var d = this.molecule.getDimension();
	this.translationMatrix = mat4.translate(mat4.identity([]), [0, 0, -Math.max(d.x, d.y) - 10]);
    this.setupScene();
}
Canvas3D.prototype.setViewDistance = function(distance){
    this.translationMatrix = mat4.translate(mat4.identity([]), [0, 0, -distance]);
}
Canvas3D.prototype.repaint = function(){
    //ready the bits for rendering
    this.gl.clear(this.gl.COLOR_BUFFER_BIT | this.gl.DEPTH_BUFFER_BIT)
    
    //set up the model view matrix to the specified transformations
    this.gl.modelViewMatrix = mat4.multiply(this.translationMatrix, this.rotationMatrix, []);
    
    if (this.molecule != null) {
        //render molecule
        this.molecule.render(this.gl, this.specs);
    }
}
Canvas3D.prototype.center = function(){
    var canvas = document.getElementById(this.id);
    var p = this.molecule.getCenter3D();
    var center = new Atom('C', 0, 0, 0);
    center.sub3D(p);
    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
        this.molecule.atoms[i].add3D(center);
    };
    }
Canvas3D.prototype.subCreate = function(){
    //setup gl object
    try {
        this.gl = document.getElementById(this.id).getContext("experimental-webgl");
        this.gl.viewport(0, 0, this.width, this.height);
    } 
    catch (e) {
    }
    if (!this.gl) {
        if (!NO_WEBGL_WARNING) {
            alert("WebGL is not installed or enabled.");
        }
        return;
    }
    this.gl.program = this.gl.createProgram();
    //this is the shader
    this.gl.shader = new Shader();
    this.gl.shader.init(this.gl);
    this.setupScene();
}
Canvas3D.prototype.setupScene = function(){
    //clear the canvas
    this.gl.clearColor(parseInt(this.specs.backgroundColor.substring(1, 3), 16) / 255.0, parseInt(this.specs.backgroundColor.substring(3, 5), 16) / 255.0, parseInt(this.specs.backgroundColor.substring(5, 7), 16) / 255.0, 1.0);
    this.gl.clearDepth(1.0);
    this.gl.enable(this.gl.DEPTH_TEST);
    this.gl.depthFunc(this.gl.LEQUAL);
    //here is the sphere buffer to be drawn, make it once, then scale and translate to draw atoms
    this.gl.sphereBuffer = new Sphere();
    this.gl.sphereBuffer.generate(this.gl, 1, this.specs.atoms_resolution_3D, this.specs.atoms_resolution_3D);
    this.gl.cylinderBuffer = new Cylinder();
    this.gl.cylinderBuffer.generate(this.gl, 1, 1, this.specs.bonds_resolution_3D);
    //set up lighting
    this.gl.lighting = new Light(this.specs.lightDiffuseColor_3D, this.specs.lightSpecularColor_3D, this.specs.lightDirection_3D);
    this.gl.lighting.lightScene(this.gl);
    //set up material
    this.gl.material = new Material(this.specs.atoms_materialAmbientColor_3D, this.specs.atoms_color, this.specs.atoms_materialSpecularColor_3D, this.specs.atoms_materialShininess_3D);
    this.gl.material.setup(this.gl);
    //projection matrix
    //arg1: vertical field of view (degrees)
    //arg2: width to height ratio
    //arg3: front culling
    //arg4: back culling
	this.gl.projectionMatrix = mat4.perspective(this.specs.projectionVerticalFieldOfView_3D, this.specs.projectionWidthHeightRatio_3D, this.specs.projectionFrontCulling_3D, this.specs.projectionBackCulling_3D);
    //matrix setup functions
    this.gl.setMatrixUniforms = function(pMatrix, mvMatrix){
        //push the projection matrix to the graphics card
        var pUniform = this.getUniformLocation(this.program, "u_projection_matrix");
        this.uniformMatrix4fv(pUniform, false, new Float32Array(pMatrix));
        //push the model-view matrix to the graphics card
        var mvUniform = this.getUniformLocation(this.program, "u_model_view_matrix");
        this.uniformMatrix4fv(mvUniform, false, new Float32Array(mvMatrix));
        //create the normal matrix and push it to the graphics card
        var normalMatrix = mat4.transpose(mat4.inverse(mvMatrix, []));
        var nUniform = this.getUniformLocation(this.program, "u_normal_matrix");
        this.uniformMatrix4fv(nUniform, false, new Float32Array(normalMatrix));
    }
}
Canvas3D.prototype.mousedown = function(e){
    this.lastPoint = e.p;
}
Canvas3D.prototype.rightmousedown = function(e){
    this.lastPoint = e.p;
}
Canvas3D.prototype.drag = function(e){
    if (ALT) {
        var t = new Point(e.p.x, e.p.y);
        t.sub(this.lastPoint);
		mat4.translate(this.translationMatrix, [t.x / 20, -t.y / 20, 0]);
        this.lastPoint = e.p;
        this.repaint();
    }
    else {
        var diameter = Math.max(this.width / 4, this.height / 4);
        var difx = e.p.x - this.lastPoint.x;
        var dify = e.p.y - this.lastPoint.y;
		var rotation = mat4.rotate(mat4.identity([]), difx * Math.PI / 180.0, [0, 1, 0]);
		mat4.rotate(rotation, dify * Math.PI / 180.0, [1, 0, 0]);
		this.rotationMatrix = mat4.multiply(rotation, this.rotationMatrix);
        this.lastPoint = e.p;
        this.repaint();
    }
}
Canvas3D.prototype.mousewheel = function(e, delta){
	mat4.translate(this.translationMatrix, [0, 0, delta]);
    this.repaint();
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2793 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-13 10:42:31 -0400 (Fri, 13 Aug 2010) $
//
function MolGrabberCanvas3D(id, width, height, action){
    if (id) {
        this.create(id, width, height);
    }
    form = '<br><form name="MolGrabberForm3D" method="POST" action="' + action + '" target="HiddenMolGrabberFrame3D" onSubmit="ValidateMolecule(MolGrabberForm3D); return false;"><input type="hidden" name="dim" value="3" /><input type="text" name="q" value="" /><input type="submit" name="submitbutton" value="Show Molecule" /></form><iframe id="HMGF3D-' + id + '" name="HiddenMolGrabberFrame3D" height="0" width="0" style="display:none;" onLoad="Get3DMolFromFrame(\'HMGF3D-' + id + '\', ' + id + ')"></iframe>';
    document.writeln(form);
    return true;
}

MolGrabberCanvas3D.prototype = new Canvas3D();

MolGrabberCanvas3D.prototype.setSearchTerm = function(term){
    document.MolGrabberForm3D.q.value = term;
    document.MolGrabberForm3D.submit();
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2794 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-13 12:12:28 -0400 (Fri, 13 Aug 2010) $
//
function RotatorCanvas3D(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    this.handle = null;
    this.timeout = AnimatorCanvas.prototype.timeout;
    var increment = Math.PI / 360;
    this.xIncrement = increment;
    this.yIncrement = increment;
    this.zIncrement = increment;
    return true;
}

RotatorCanvas3D.prototype = new Canvas3D();

RotatorCanvas3D.prototype.startAnimation = AnimatorCanvas.prototype.startAnimation;
RotatorCanvas3D.prototype.stopAnimation = AnimatorCanvas.prototype.stopAnimation;
RotatorCanvas3D.prototype.isRunning = AnimatorCanvas.prototype.isRunning;
RotatorCanvas3D.prototype.dblclick = RotatorCanvas.prototype.dblclick;
RotatorCanvas3D.prototype.mousedown = null;
RotatorCanvas3D.prototype.rightmousedown = null;
RotatorCanvas3D.prototype.drag = null;
RotatorCanvas3D.prototype.mousewheel = null;
RotatorCanvas3D.prototype.nextFrame = function(){
    if (this.molecule == null) {
        this.stopAnimation();
        return;
    }
	var matrix = [];
    mat4.identity(matrix);
    mat4.rotate(matrix, this.xIncrement, [1, 0, 0]);
    mat4.rotate(matrix, this.yIncrement, [0, 1, 0]);
    mat4.rotate(matrix, this.zIncrement, [0, 0, 1]);
    mat4.multiply(this.rotationMatrix, matrix);
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function TransformCanvas3D(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    return true;
}

TransformCanvas3D.prototype = new Canvas3D();
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function ViewerCanvas3D(id, width, height){
    if (id) {
        this.create(id, width, height);
    }
    return true;
}

ViewerCanvas3D.prototype = new Canvas3D();

ViewerCanvas3D.prototype.mousedown = null;
ViewerCanvas3D.prototype.rightmousedown = null;
ViewerCanvas3D.prototype.drag = null;
ViewerCanvas3D.prototype.mousewheel = null;
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function Layout(){
    return true;
}

Layout.prototype.layout = function(){
    if (this.innerLayout) {
        this.innerLayout();
    }
}
Layout.prototype.create = function(name){
    this.name = name;
    this.specs = new VisualSpecifications();
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function SimpleReactionLayout(name){
    this.reactants = [];
    this.products = [];
    this.textAbove = null;
    this.textBelow = null;
    this.arrow = '&rarr;';
    this.plus = '+';
    this.create(name);
    return true;
}

SimpleReactionLayout.prototype = new Layout();

SimpleReactionLayout.prototype.addReactant = function(reactant){
    this.reactants[this.reactants.length] = reactant;
}
SimpleReactionLayout.prototype.addProduct = function(product){
    this.products[this.products.length] = product;
}
SimpleReactionLayout.prototype.innerLayout = function(){
    var glyphStyle = '<span style="font-size:25px;">';
    document.writeln("<table><tr>");
    //reactants
    for (var i = 0, ii = this.reactants.length; i < ii; i++) {
        if (i > 0) {
            document.writeln("<td>" + glyphStyle + this.plus + "</span></td>");
        }
        document.writeln("<td>");
        var dim = this.reactants[i].getDimension();
        var view = new ViewerCanvas(this.name + '_reactant' + i, dim.x + 60, dim.y + 60);
        if (this.specs.backgroundColor == null) {
            $('#' + this.name + '_reactant' + i).css("border", "0px");
        }
        view.specs = this.specs;
        view.loadMolecule(this.reactants[i]);
        document.writeln("</td>");
    }
    //arrow
    document.writeln("<td>");
    document.writeln("<table>");
    document.writeln("<tr><td>");
    if (this.textAbove != null) {
        document.writeln("<center>" + this.textAbove + "</center>");
    }
    else {
        document.writeln("&nbsp;");
    }
    document.writeln("</td></tr>");
    document.writeln("<tr><td><center>" + glyphStyle + this.arrow + "</span></center></td></tr>");
    document.writeln("<tr><td>");
    if (this.textBelow != null) {
        document.writeln("<center>" + this.textBelow + "</center>");
    }
    else {
        document.writeln("&nbsp;");
    }
    document.writeln("</td></tr>");
    document.writeln("</table>");
    document.writeln("</td>");
    //products
    for (var i = 0, ii = this.products.length; i < ii; i++) {
        if (i > 0) {
            document.writeln("<td>" + glyphStyle + this.plus + "</td>");
        }
        document.writeln("<td>");
        var dim = this.products[i].getDimension();
        var view = new ViewerCanvas(this.name + '_product' + i, dim.x + 60, dim.y + 60);
        if (this.specs.backgroundColor == null) {
            $('#' + this.name + '_product' + i).css("border", "0px");
        }
        view.specs = this.specs;
        view.loadMolecule(this.products[i]);
        document.writeln("</td>");
    }
    document.writeln("</tr></table>");
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
var LEEWAY = 1.1;

function getPointsPerAngstrom(){
    return default_bondLength_2D / default_angstromsPerBondLength;
}

function deduceCovalentBonds(molecule, customPointsPerAngstrom){
    var pointsPerAngstrom = getPointsPerAngstrom();
	if(customPointsPerAngstrom){
		pointsPerAngstrom = customPointsPerAngstrom;
	}
    for (var i = 0, ii=molecule.atoms.length; i < ii; i++) {
        for (var j = i + 1; j < ii; j++) {
            var first = molecule.atoms[i];
            var second = molecule.atoms[j];
            if (first.distance3D(second) < (ELEMENT[first.label].covalentRadius + ELEMENT[second.label].covalentRadius)*pointsPerAngstrom * LEEWAY) {
	          	molecule.bonds[molecule.bonds.length] = new Bond(first, second, 1);
            }
        }
    }
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//

function removeHydrogens(molecule){
	var atoms = [];
	var bonds = [];
    for (var i = 0, ii=molecule.bonds.length; i <ii; i++) {
		if(molecule.bonds[i].a1.label != 'H'&&molecule.bonds[i].a2.label != 'H'){
			bonds[bonds.length] = molecule.bonds[i];
		}
    }
    for (var i = 0, ii=molecule.atoms.length; i <ii; i++) {
		if(molecule.atoms[i].label != 'H'){
			atoms[atoms.length] = molecule.atoms[i];
		}
    }
	molecule.atoms = atoms;
	molecule.bonds = bonds;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//

function copy(molecule){
    for (var i = 0, ii=molecule.atoms.length; i < ii; i++) {
        molecule.atoms[i].metaID = i;
    }
    var newMol = new Molecule();
    for (var i = 0, ii=molecule.atoms.length; i < ii; i++) {
        newMol.atoms[i] = new Atom(molecule.atoms[i].label, molecule.atoms[i].x, molecule.atoms[i].y, molecule.atoms[i].z);
    }
    for (var i = 0, ii=molecule.bonds.length; i < ii; i++) {
        newMol.bonds[i] = new Bond(newMol.atoms[molecule.bonds[i].a1.metaID], newMol.atoms[molecule.bonds[i].a2.metaID], molecule.bonds[i].bondOrder);
    }
    return newMol;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function Counter(){
    this.value = 0;
    this.molecule = null;
    return true;
}

Counter.prototype.setMolecule = function(molecule){
    this.value = 0;
    this.molecule = molecule;
    if (this.innerCalculate) {
        this.innerCalculate();
    }
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function FrerejacqueNumberCounter(molecule){
    this.setMolecule(molecule);
    return true;
}

FrerejacqueNumberCounter.prototype = new Counter();

FrerejacqueNumberCounter.prototype.innerCalculate = function(){
    this.value = this.molecule.bonds.length - this.molecule.atoms.length + new NumberOfMoleculesCounter(this.molecule).value;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function NumberOfMoleculesCounter(molecule){
    this.setMolecule(molecule);
    return true;
}

NumberOfMoleculesCounter.prototype = new Counter()

NumberOfMoleculesCounter.prototype.innerCalculate = function(){
    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
        this.molecule.atoms[i].visited = false;
    }
    for (var i = 0, ii = this.molecule.atoms.length; i < ii; i++) {
        if (!this.molecule.atoms[i].visited) {
            this.value++;
            var q = new Queue();
            this.molecule.atoms[i].visited = true;
            q.enqueue(this.molecule.atoms[i]);
            while (!q.isEmpty()) {
                var atom = q.dequeue();
                for (var j = 0, jj = this.molecule.bonds.length; j < jj; j++) {
                    if (this.molecule.bonds[j].contains(atom)) {
                        var neigh = this.molecule.bonds[j].getNeighbor(atom);
                        if (!neigh.visited) {
                            neigh.visited = true;
                            q.enqueue(neigh);
                        }
                    }
                }
            }
        }
    }
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function RingFinder(){
    this.atoms = null;
    this.bonds = null;
    this.rings = null;
    return true;
}

RingFinder.prototype.reduce = function(molecule){
    for (var i = 0, ii = molecule.atoms.length; i < ii; i++) {
        molecule.atoms[i].visited = false;
    }
    for (var i = 0, ii = molecule.bonds.length; i < ii; i++) {
        molecule.bonds[i].visited = false;
    }
    var cont = true;
    while (cont) {
        cont = false;
        for (var i = 0, ii = molecule.atoms.length; i < ii; i++) {
            var count = 0;
            var bond = null;
            for (var j = 0, jj = molecule.bonds.length; j < jj; j++) {
                if (molecule.bonds[j].contains(molecule.atoms[i]) && !molecule.bonds[j].visited) {
                    count++;
                    if (count == 2) {
                        break;
                    }
                    bond = molecule.bonds[j];
                }
            }
            if (count == 1) {
                cont = true;
                bond.visited = true;
                molecule.atoms[i].visited = true;
            }
        }
    }
    for (var i = 0, ii = molecule.atoms.length; i < ii; i++) {
        if (!molecule.atoms[i].visited) {
            this.atoms[this.atoms.length] = molecule.atoms[i];
        }
    }
    for (var i = 0, ii = molecule.bonds.length; i < ii; i++) {
        if (!molecule.bonds[i].visited) {
            this.bonds[this.bonds.length] = molecule.bonds[i];
        }
    }
    if (this.bonds.length == 0 && this.atoms.length != 0) {
        this.atoms = [];
    }
}
RingFinder.prototype.setMolecule = function(molecule){
    this.atoms = [];
    this.bonds = [];
    this.rings = [];
    this.reduce(molecule);
    if (this.atoms.length > 2 && this.innerGetRings) {
        this.innerGetRings();
    }
}
RingFinder.prototype.fuse = function(){
    for (var i = 0, ii = this.rings.length; i < ii; i++) {
        for (var j = 0, jj = this.bonds.length; j < jj; j++) {
            if ($.inArray(this.bonds[j].a1, this.rings[i].atoms) != -1 && $.inArray(this.bonds[j].a2, this.rings[i].atoms) != -1) {
                this.rings[i].bonds[this.rings[i].bonds.length] = this.bonds[j];
            }
        }
    }
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function Finger(a, from){
    this.atoms = [];
    if (from) {
        for (var i = 0, ii = from.atoms.length; i < ii; i++) {
            this.atoms[i] = from.atoms[i];
        }
    }
    this.atoms[this.atoms.length] = a;
    return true;
}

Finger.prototype.grow = function(bonds, blockers){
    var last = this.atoms[this.atoms.length - 1];
    var neighs = [];
    for (var i = 0, ii = bonds.length; i < ii; i++) {
        if (bonds[i].contains(last)) {
            var neigh = bonds[i].getNeighbor(last);
            if ($.inArray(neigh, blockers) == -1) {
                neighs[neighs.length] = neigh;
            }
        }
    }
    var returning = [];
    for (var i = 0, ii = neighs.length; i < ii; i++) {
        returning[returning.length] = new Finger(neighs[i], this);
    }
    return returning;
}
Finger.prototype.check = function(bonds, finger, a){
    //check that they dont contain similar parts
    for (var i = 0, ii = finger.atoms.length - 1; i < ii; i++) {
        if ($.inArray(finger.atoms[i], this.atoms) != -1) {
            return null;
        }
    }
    var ring = null;
    //check if fingers meet at tips
    if (finger.atoms[finger.atoms.length - 1] == this.atoms[this.atoms.length - 1]) {
        ring = new Ring();
        ring.atoms[0] = a;
        for (var i = 0, ii = this.atoms.length; i < ii; i++) {
            ring.atoms[ring.atoms.length] = this.atoms[i];
        }
        for (var i = finger.atoms.length - 2; i >= 0; i--) {
            ring.atoms[ring.atoms.length] = finger.atoms[i];
        }
    }
    else {
        //check if fingers meet at bond
        var endbonds = [];
        for (var i = 0, ii = bonds.length; i < ii; i++) {
            if (bonds[i].contains(finger.atoms[finger.atoms.length - 1])) {
                endbonds[endbonds.length] = bonds[i];
            }
        }
        for (var i = 0, ii = endbonds.length; i < ii; i++) {
            if ((finger.atoms.length == 1 || !endbonds[i].contains(finger.atoms[finger.atoms.length - 2])) && endbonds[i].contains(this.atoms[this.atoms.length - 1])) {
                ring = new Ring();
                ring.atoms[0] = a;
                for (var j = 0, jj = this.atoms.length; j < jj; j++) {
                    ring.atoms[ring.atoms.length] = this.atoms[j];
                }
                for (var j = finger.atoms.length - 1; j >= 0; j--) {
                    ring.atoms[ring.atoms.length] = finger.atoms[j];
                }
                break;
            }
        }
    }
    return ring;
}

var EULER_FACET_FINGER_BREAK = 5;

function EulerFacetRingFinder(molecule){
    this.setMolecule(molecule);
    return true;
}

EulerFacetRingFinder.prototype = new RingFinder();

EulerFacetRingFinder.prototype.innerGetRings = function(){
    for (var i = 0, ii = this.atoms.length; i < ii; i++) {
        var neigh = [];
        for (var j = 0, jj = this.bonds.length; j < jj; j++) {
            if (this.bonds[j].contains(this.atoms[i])) {
                neigh[neigh.length] = this.bonds[j].getNeighbor(this.atoms[i]);
            }
        }
        for (var j = 0, jj = neigh.length; j < jj; j++) {
            //weird that i can't optimize this loop without breaking a test case...
            for (var k = j + 1; k < neigh.length; k++) {
                var fingers = [];
                fingers[0] = new Finger(neigh[j]);
                fingers[1] = new Finger(neigh[k]);
                var blockers = [];
                blockers[0] = this.atoms[i];
                for (var l = 0, ll = neigh.length; l < ll; l++) {
                    if (l != j && l != k) {
                        blockers[blockers.length] = neigh[l];
                    }
                }
                var found = [];
                //check for 3 membered ring
                var three = fingers[0].check(this.bonds, fingers[1], this.atoms[i]);
                if (three) {
                    found[0] = three;
                }
                while (found.length == 0 && fingers.length > 0 && fingers[0].atoms.length < EULER_FACET_FINGER_BREAK) {
                    var newfingers = [];
                    for (var l = 0, ll = fingers.length; l < ll; l++) {
                        var adding = fingers[l].grow(this.bonds, blockers);
                        for (var m = 0, mm = adding.length; m < mm; m++) {
                            newfingers[newfingers.length] = adding[m];
                        }
                    }
                    fingers = newfingers;
                    for (var l = 0, ll = fingers.length; l < ll; l++) {
                        for (var m = l + 1; m < ll; m++) {
                            var r = fingers[l].check(this.bonds, fingers[m], this.atoms[i]);
                            if (r) {
                                found[found.length] = r;
                            }
                        }
                    }
                    if (found.length == 0) {
                        var newBlockers = [];
                        for (var l = 0, ll = blockers.length; l < ll; l++) {
                            for (var m = 0, mm = this.bonds.length; m < mm; m++) {
                                if (this.bonds[m].contains(blockers[l])) {
                                    var neigh = this.bonds[m].getNeighbor(blockers[l]);
                                    if ($.inArray(neigh, blockers) == -1 && $.inArray(neigh, newBlockers) == -1) {
                                        newBlockers[newBlockers.length] = neigh;
                                    }
                                }
                            }
                        }
                        for (var l = 0, ll = newBlockers.length; l < ll; l++) {
                            blockers[blockers.length] = newBlockers[l];
                        }
                    }
                }
                if (found.length > 0) {
                    var use = null;
                    for (var l = 0, ll = found.length; l < ll; l++) {
                        if (!use || use.atoms.length > found[l].atoms.length) {
                            use = found[l];
                        }
                    }
                    var already = false;
                    for (var l = 0, ll = this.rings.length; l < ll; l++) {
                        var all = true;
                        for (var m = 0, mm = use.atoms.length; m < mm; m++) {
                            if ($.inArray(use.atoms[m], this.rings[l].atoms) == -1) {
                                all = false;
                                break;
                            }
                        }
                        if (all) {
                            already = true;
                            break;
                        }
                    }
                    if (!already) {
                        this.rings[this.rings.length] = use;
                    }
                }
            }
        }
    }
    this.fuse();
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2778 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:54:03 -0400 (Thu, 12 Aug 2010) $
//

function Link(data){
    this.data = data;
    this.next = null;
    return true;
}

Link.prototype.reverse = function(before){
    if (this.next != null) {
        this.next.reverse(this);
    }
    this.next = before;
}
Link.prototype.getDataArray = function(array){
    array[array.length] = this.data;
    if (this.next != null) {
        this.next.getDataArray(array);
    }
}
Link.prototype.count = function(){
    if (this.next == null) {
        return 1;
    }
    else {
        return 1 + this.next.count();
    }
}

function PGraphEdge(i1, i2){
    if (i1 != null) {
        this.head = new Link(i1);
        this.head.next = new Link(i2);
    }
    return true;
}

PGraphEdge.prototype.getLast = function(){
    var hold = this.head;
    while (hold.next != null) {
        hold = hold.next;
    }
    return hold;
}
PGraphEdge.prototype.getCopy = function(){
    var copy = new PGraphEdge();
    var hold = this.head;
    var copyHold = new Link(hold.data);
    copy.head = copyHold;
    while (hold.next != null) {
        hold = hold.next;
        copyHold.next = new Link(hold.data);
        copyHold = copyHold.next;
    }
    return copy;
}
PGraphEdge.prototype.merge = function(other){
    var newPGE = this.getCopy();
    var same = this.head.data;
    if (other.head.data != same && other.getLast().data != same) {
        same = this.getLast().data;
    }
    var otherBetweens = other.getCopy();
    if (newPGE.head.data == same) {
        newPGE.reverse();
    }
    if (other.head.data != same) {
        otherBetweens.reverse();
    }
    otherBetweens.head = otherBetweens.head.next;
    newPGE.getLast().next = otherBetweens.head;
    return newPGE;
}
PGraphEdge.prototype.connectsTo = function(index){
    return this.head.data == index || this.getLast().data == index;
}
PGraphEdge.prototype.isCycle = function(){
    return this.head.data == this.getLast().data;
}
PGraphEdge.prototype.size = function(){
    return this.head.count();
}
PGraphEdge.prototype.reverse = function(){
    var last = this.getLast();
    this.head.reverse(null);
    this.head = last;
}

function indexOf(array, item){
    for (var i = 0, ii = array.length; i < ii; i++) {
        if (array[i] == item) {
            return i;
        }
    };
    return -1;
}

function HanserRingFinder(molecule){
    this.setMolecule(molecule);
    return true;
}

HanserRingFinder.prototype = new RingFinder();

HanserRingFinder.prototype.innerGetRings = function(){
    var pGraphEdges = [];
    var pGraphRings = [];
    for (var i = 0, ii = this.bonds.length; i < ii; i++) {
        pGraphEdges[pGraphEdges.length] = new PGraphEdge(indexOf(this.atoms, this.bonds[i].a1), indexOf(this.atoms, this.bonds[i].a2));
    }
    while (pGraphEdges.length > 0) {
        var counts = [];
        for (var i = 0, ii = this.atoms.length; i < ii; i++) {
            counts[i] = 0;
        };
        for (var i = 0, ii = pGraphEdges.length; i < ii; i++) {
            counts[pGraphEdges[i].head.data]++;
            counts[pGraphEdges[i].getLast().data]++;
        };
        var pick = -1;
        var min = Infinity;
        for (var i = 0, ii = counts.length; i < ii; i++) {
            if (counts[i] > 0 && counts[i] < min) {
                min = counts[i];
                pick = i;
            }
        }
        var removing = [];
        var keep = [];
        for (var i = 0, ii = pGraphEdges.length; i < ii; i++) {
            if (pGraphEdges[i].connectsTo(pick)) {
                removing[removing.length] = pGraphEdges[i];
            }
            else {
                keep[keep.length] = pGraphEdges[i];
            }
        };
        pGraphEdges = keep;
        for (var i = 0, ii = removing.length; i < ii; i++) {
            for (var j = i + 1; j < ii; j++) {
                var one = removing[i];
                var two = removing[j];
                var newPGE = one.merge(two);
                var overlap = false;
                var hold = newPGE.head.next;
                while (!overlap && hold != null) {
                    var hold2 = hold.next;
                    while (!overlap && hold2 != null) {
                        if (hold2.data == hold.data) {
                            overlap = true;
                        }
                        hold2 = hold2.next;
                    }
                    hold = hold.next;
                }
                if (!overlap) {
                    if (newPGE.isCycle()) {
                        pGraphRings[pGraphRings.length] = newPGE;
                    }
                    else {
                        pGraphEdges[pGraphEdges.length] = newPGE;
                    }
                }
            }
        }
    }
    var ringsI = [];
    for (var i = 0, ii = pGraphRings.length; i < ii; i++) {
        ringsI[i] = [];
        pGraphRings[i].head.getDataArray(ringsI[i]);
    }
    //build rings from pGraphs
    for (var i = 0, ii = ringsI.length; i < ii; i++) {
        var ring = new Ring();
        for (var j = 0, jj = ringsI[i].length - 1; j < jj; j++) {
            ring.atoms[j] = this.atoms[ringsI[i][j]];
        }
        for (var j = 0, jj = ring.atoms.length - 1; j < jj; j++) {
            for (var k = 0, kk = this.bonds.length; k < kk; k++) {
                if (this.bonds[k].contains(ring.atoms[j]) && this.bonds[k].contains(ring.atoms[j + 1])) {
                    ring.bonds[ring.bonds.length] = this.bonds[k];
                    break;
                }
            }
        }
        for (var k = 0, kk = this.bonds.length; k < kk; k++) {
            if (this.bonds[k].contains(ring.atoms[0]) && this.bonds[k].contains(ring.atoms[ring.atoms.length - 1])) {
                ring.bonds[ring.bonds.length] = this.bonds[k];
                break;
            }
        }
        this.rings[i] = ring;
    }
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function SSSRFinder(molecule){
	this.rings = [];
	if (molecule.atoms.length > 0) {
		var frerejacqueNumber = new FrerejacqueNumberCounter(molecule).value;
		var all = new EulerFacetRingFinder(molecule).rings;
		all.sort(function(a, b){
			return a.atoms.length - b.atoms.length;
		});
		for (var i = 0,ii=molecule.bonds.length; i < ii; i++) {
			molecule.bonds[i].visited = false;
		}
		for (var i = 0,ii=all.length; i < ii; i++) {
			var use = false;
			for (var j = 0,jj=all[i].bonds.length; j < jj; j++) {
				if (!all[i].bonds[j].visited) {
					use = true;
					break;
				}
			}
			if (use) {
				for (var j = 0,jj=all[i].bonds.length; j < jj; j++) {
					all[i].bonds[j].visited = true;
				}
				this.rings[this.rings.length] = all[i];
			}
			if (this.rings.length == frerejacqueNumber) {
				break;
			}
		}
	}
	return true;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function Element(symbol, name, atomicNumber){
	this.symbol = symbol;
	this.name = name;
	this.atomicNumber = atomicNumber;
	return true;
}//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2777 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:48:33 -0400 (Thu, 12 Aug 2010) $
//

//the ELEMENT array
var ELEMENT = [];
ELEMENT['H'] = new Element('H', 'Hydrogen', 1);
ELEMENT['He'] = new Element('He', 'Helium', 2);
ELEMENT['Li'] = new Element('Li', 'Lithium', 3);
ELEMENT['Be'] = new Element('Be', 'Beryllium', 4);
ELEMENT['B'] = new Element('B', 'Boron', 5);
ELEMENT['C'] = new Element('C', 'Carbon', 6);
ELEMENT['N'] = new Element('N', 'Nitrogen', 7);
ELEMENT['O'] = new Element('O', 'Oxygen', 8);
ELEMENT['F'] = new Element('F', 'Fluorine', 9);
ELEMENT['Ne'] = new Element('Ne', 'Neon', 10);
ELEMENT['Na'] = new Element('Na', 'Sodium', 11);
ELEMENT['Mg'] = new Element('Mg', 'Magnesium', 12);
ELEMENT['Al'] = new Element('Al', 'Aluminum', 13);
ELEMENT['Si'] = new Element('Si', 'Silicon', 14);
ELEMENT['P'] = new Element('P', 'Phosphorus', 15);
ELEMENT['S'] = new Element('S', 'Sulfur', 16);
ELEMENT['Cl'] = new Element('Cl', 'Chlorine', 17);
ELEMENT['Ar'] = new Element('Ar', 'Argon', 18);
ELEMENT['K'] = new Element('K', 'Potassium', 19);
ELEMENT['Ca'] = new Element('Ca', 'Calcium', 20);
ELEMENT['Sc'] = new Element('Sc', 'Scandium', 21);
ELEMENT['Ti'] = new Element('Ti', 'Titanium', 22);
ELEMENT['V'] = new Element('V', 'Vanadium', 23);
ELEMENT['Cr'] = new Element('Cr', 'Chromium', 24);
ELEMENT['Mn'] = new Element('Mn', 'Manganese', 25);
ELEMENT['Fe'] = new Element('Fe', 'Iron', 26);
ELEMENT['Co'] = new Element('Co', 'Cobalt', 27);
ELEMENT['Ni'] = new Element('Ni', 'Nickel', 28);
ELEMENT['Cu'] = new Element('Cu', 'Copper', 29);
ELEMENT['Zn'] = new Element('Zn', 'Zinc', 30);
ELEMENT['Ga'] = new Element('Ga', 'Gallium', 31);
ELEMENT['Ge'] = new Element('Ge', 'Germanium', 32);
ELEMENT['As'] = new Element('As', 'Arsenic', 33);
ELEMENT['Se'] = new Element('Se', 'Selenium', 34);
ELEMENT['Br'] = new Element('Br', 'Bromine', 35);
ELEMENT['Kr'] = new Element('Kr', 'Krypton', 36);
ELEMENT['Rb'] = new Element('Rb', 'Rubidium', 37);
ELEMENT['Sr'] = new Element('Sr', 'Strontium', 38);
ELEMENT['Y'] = new Element('Y', 'Yttrium', 39);
ELEMENT['Zr'] = new Element('Zr', 'Zirconium', 40);
ELEMENT['Nb'] = new Element('Nb', 'Niobium', 41);
ELEMENT['Mo'] = new Element('Mo', 'Molybdenum', 42);
ELEMENT['Tc'] = new Element('Tc', 'Technetium', 43);
ELEMENT['Ru'] = new Element('Ru', 'Ruthenium', 44);
ELEMENT['Rh'] = new Element('Rh', 'Rhodium', 45);
ELEMENT['Pd'] = new Element('Pd', 'Palladium', 46);
ELEMENT['Ag'] = new Element('Ag', 'Silver', 47);
ELEMENT['Cd'] = new Element('Cd', 'Cadmium', 48);
ELEMENT['In'] = new Element('In', 'Indium', 49);
ELEMENT['Sn'] = new Element('Sn', 'Tin', 50);
ELEMENT['Sb'] = new Element('Sb', 'Antimony', 51);
ELEMENT['Te'] = new Element('Te', 'Tellurium', 52);
ELEMENT['I'] = new Element('I', 'Iodine', 53);
ELEMENT['Xe'] = new Element('Xe', 'Xenon', 54);
ELEMENT['Cs'] = new Element('Cs', 'Cesium', 55);
ELEMENT['Ba'] = new Element('Ba', 'Barium', 56);
ELEMENT['La'] = new Element('La', 'Lanthanum', 57);
ELEMENT['Ce'] = new Element('Ce', 'Cerium', 58);
ELEMENT['Pr'] = new Element('Pr', 'Praseodymium', 59);
ELEMENT['Nd'] = new Element('Nd', 'Neodymium', 60);
ELEMENT['Pm'] = new Element('Pm', 'Promethium', 61);
ELEMENT['Sm'] = new Element('Sm', 'Samarium', 62);
ELEMENT['Eu'] = new Element('Eu', 'Europium', 63);
ELEMENT['Gd'] = new Element('Gd', 'Gadolinium', 64);
ELEMENT['Tb'] = new Element('Tb', 'Terbium', 65);
ELEMENT['Dy'] = new Element('Dy', 'Dysprosium', 66);
ELEMENT['Ho'] = new Element('Ho', 'Holmium', 67);
ELEMENT['Er'] = new Element('Er', 'Erbium', 68);
ELEMENT['Tm'] = new Element('Tm', 'Thulium', 69);
ELEMENT['Yb'] = new Element('Yb', 'Ytterbium', 70);
ELEMENT['Lu'] = new Element('Lu', 'Lutetium', 71);
ELEMENT['Hf'] = new Element('Hf', 'Hafnium', 72);
ELEMENT['Ta'] = new Element('Ta', 'Tantalum', 73);
ELEMENT['W'] = new Element('W', 'Tungsten', 74);
ELEMENT['Re'] = new Element('Re', 'Rhenium', 75);
ELEMENT['Os'] = new Element('Os', 'Osmium', 76);
ELEMENT['Ir'] = new Element('Ir', 'Iridium', 77);
ELEMENT['Pt'] = new Element('Pt', 'Platinum', 78);
ELEMENT['Au'] = new Element('Au', 'Gold', 79);
ELEMENT['Hg'] = new Element('Hg', 'Mercury', 80);
ELEMENT['Tl'] = new Element('Tl', 'Thallium', 81);
ELEMENT['Pb'] = new Element('Pb', 'Lead', 82);
ELEMENT['Bi'] = new Element('Bi', 'Bismuth', 83);
ELEMENT['Po'] = new Element('Po', 'Polonium', 84);
ELEMENT['At'] = new Element('At', 'Astatine', 85);
ELEMENT['Rn'] = new Element('Rn', 'Radon', 86);
ELEMENT['Fr'] = new Element('Fr', 'Francium', 87);
ELEMENT['Ra'] = new Element('Ra', 'Radium', 88);
ELEMENT['Ac'] = new Element('Ac', 'Actinium', 89);
ELEMENT['Th'] = new Element('Th', 'Thorium', 90);
ELEMENT['Pa'] = new Element('Pa', 'Protactinium', 91);
ELEMENT['U'] = new Element('U', 'Uranium', 92);
ELEMENT['Np'] = new Element('Np', 'Neptunium', 93);
ELEMENT['Pu'] = new Element('Pu', 'Plutonium', 94);
ELEMENT['Am'] = new Element('Am', 'Americium', 95);
ELEMENT['Cm'] = new Element('Cm', 'Curium', 96);
ELEMENT['Bk'] = new Element('Bk', 'Berkelium', 97);
ELEMENT['Cf'] = new Element('Cf', 'Californium', 98);
ELEMENT['Es'] = new Element('Es', 'Einsteinium', 99);
ELEMENT['Fm'] = new Element('Fm', 'Fermium', 100);
ELEMENT['Md'] = new Element('Md', 'Mendelevium', 101);
ELEMENT['No'] = new Element('No', 'Nobelium', 102);
ELEMENT['Lr'] = new Element('Lr', 'Lawrencium', 103);
ELEMENT['Rf'] = new Element('Rf', 'Rutherfordium', 104);
ELEMENT['Db'] = new Element('Db', 'Dubnium', 105);
ELEMENT['Sg'] = new Element('Sg', 'Seaborgium', 106);
ELEMENT['Bh'] = new Element('Bh', 'Bohrium', 107);
ELEMENT['Hs'] = new Element('Hs', 'Hassium', 108);
ELEMENT['Mt'] = new Element('Mt', 'Meitnerium', 109);
ELEMENT['Ds'] = new Element('Ds', 'Darmstadtium', 110);
ELEMENT['Rg'] = new Element('Rg', 'Roentgenium', 111);
ELEMENT['Cn'] = new Element('Cn', 'Copernicium', 112);
ELEMENT['Uut'] = new Element('Uut', 'Ununtrium', 113);
ELEMENT['Uuq'] = new Element('Uuq', 'Ununquadium', 114);
ELEMENT['Uup'] = new Element('Uup', 'Ununpentium', 115);
ELEMENT['Uuh'] = new Element('Uuh', 'Ununhexium', 116);
ELEMENT['Uus'] = new Element('Uus', 'Ununseptium', 117);
ELEMENT['Uuo'] = new Element('Uuo', 'Ununoctium', 118);

//all symbols
var symbols = ['H','He','Li','Be','B','C','N','O','F','Ne','Na','Mg','Al','Si','P','S','Cl','Ar','K','Ca','Sc','Ti','V','Cr','Mn','Fe','Co','Ni','Cu','Zn','Ga','Ge','As','Se','Br','Kr','Rb','Sr','Y','Zr','Nb','Mo','Tc','Ru','Rh','Pd','Ag','Cd','In','Sn','Sb','Te','I','Xe','Cs','Ba','La','Ce','Pr','Nd','Pm','Sm','Eu','Gd','Tb','Dy','Ho','Er','Tm','Yb','Lu','Hf','Ta','W','Re','Os','Ir','Pt','Au','Hg','Tl','Pb','Bi','Po','At','Rn','Fr','Ra','Ac','Th','Pa','U','Np','Pu','Am','Cm','Bk','Cf','Es','Fm','Md','No','Lr','Rf','Db','Sg','Bh','Hs','Mt','Ds','Rg','Cn','Uut','Uuq','Uup','Uuh','Uus','Uuo'];

//set up jmol colors
ELEMENT['H'].jmolColor = '#FFFFFF';
ELEMENT['He'].jmolColor = '#D9FFFF';
ELEMENT['Li'].jmolColor = '#CC80FF';
ELEMENT['Be'].jmolColor = '#C2FF00';
ELEMENT['B'].jmolColor = '#FFB5B5';
ELEMENT['C'].jmolColor = '#909090';
ELEMENT['N'].jmolColor = '#3050F8';
ELEMENT['O'].jmolColor = '#FF0D0D';
ELEMENT['F'].jmolColor = '#90E050';
ELEMENT['Ne'].jmolColor = '#B3E3F5';
ELEMENT['Na'].jmolColor = '#AB5CF2';
ELEMENT['Mg'].jmolColor = '#8AFF00';
ELEMENT['Al'].jmolColor = '#BFA6A6';
ELEMENT['Si'].jmolColor = '#F0C8A0';
ELEMENT['P'].jmolColor = '#FF8000';
ELEMENT['S'].jmolColor = '#FFFF30';
ELEMENT['Cl'].jmolColor = '#1FF01F';
ELEMENT['Ar'].jmolColor = '#80D1E3';
ELEMENT['K'].jmolColor = '#8F40D4';
ELEMENT['Ca'].jmolColor = '#3DFF00';
ELEMENT['Sc'].jmolColor = '#E6E6E6';
ELEMENT['Ti'].jmolColor = '#BFC2C7';
ELEMENT['V'].jmolColor = '#A6A6AB';
ELEMENT['Cr'].jmolColor = '#8A99C7';
ELEMENT['Mn'].jmolColor = '#9C7AC7';
ELEMENT['Fe'].jmolColor = '#E06633';
ELEMENT['Co'].jmolColor = '#F090A0';
ELEMENT['Ni'].jmolColor = '#50D050';
ELEMENT['Cu'].jmolColor = '#C88033';
ELEMENT['Zn'].jmolColor = '#7D80B0';
ELEMENT['Ga'].jmolColor = '#C28F8F';
ELEMENT['Ge'].jmolColor = '#668F8F';
ELEMENT['As'].jmolColor = '#BD80E3';
ELEMENT['Se'].jmolColor = '#FFA100';
ELEMENT['Br'].jmolColor = '#A62929';
ELEMENT['Kr'].jmolColor = '#5CB8D1';
ELEMENT['Rb'].jmolColor = '#702EB0';
ELEMENT['Sr'].jmolColor = '#00FF00';
ELEMENT['Y'].jmolColor = '#94FFFF';
ELEMENT['Zr'].jmolColor = '#94E0E0';
ELEMENT['Nb'].jmolColor = '#73C2C9';
ELEMENT['Mo'].jmolColor = '#54B5B5';
ELEMENT['Tc'].jmolColor = '#3B9E9E';
ELEMENT['Ru'].jmolColor = '#248F8F';
ELEMENT['Rh'].jmolColor = '#0A7D8C';
ELEMENT['Pd'].jmolColor = '#006985';
ELEMENT['Ag'].jmolColor = '#C0C0C0';
ELEMENT['Cd'].jmolColor = '#FFD98F';
ELEMENT['In'].jmolColor = '#A67573';
ELEMENT['Sn'].jmolColor = '#668080';
ELEMENT['Sb'].jmolColor = '#9E63B5';
ELEMENT['Te'].jmolColor = '#D47A00';
ELEMENT['I'].jmolColor = '#940094';
ELEMENT['Xe'].jmolColor = '#429EB0';
ELEMENT['Cs'].jmolColor = '#57178F';
ELEMENT['Ba'].jmolColor = '#00C900';
ELEMENT['La'].jmolColor = '#70D4FF';
ELEMENT['Ce'].jmolColor = '#FFFFC7';
ELEMENT['Pr'].jmolColor = '#D9FFC7';
ELEMENT['Nd'].jmolColor = '#C7FFC7';
ELEMENT['Pm'].jmolColor = '#A3FFC7';
ELEMENT['Sm'].jmolColor = '#8FFFC7';
ELEMENT['Eu'].jmolColor = '#61FFC7';
ELEMENT['Gd'].jmolColor = '#45FFC7';
ELEMENT['Tb'].jmolColor = '#30FFC7';
ELEMENT['Dy'].jmolColor = '#1FFFC7';
ELEMENT['Ho'].jmolColor = '#00FF9C';
ELEMENT['Er'].jmolColor = '#00E675';
ELEMENT['Tm'].jmolColor = '#00D452';
ELEMENT['Yb'].jmolColor = '#00BF38';
ELEMENT['Lu'].jmolColor = '#00AB24';
ELEMENT['Hf'].jmolColor = '#4DC2FF';
ELEMENT['Ta'].jmolColor = '#4DA6FF';
ELEMENT['W'].jmolColor = '#2194D6';
ELEMENT['Re'].jmolColor = '#267DAB';
ELEMENT['Os'].jmolColor = '#266696';
ELEMENT['Ir'].jmolColor = '#175487';
ELEMENT['Pt'].jmolColor = '#D0D0E0';
ELEMENT['Au'].jmolColor = '#FFD123';
ELEMENT['Hg'].jmolColor = '#B8B8D0';
ELEMENT['Tl'].jmolColor = '#A6544D';
ELEMENT['Pb'].jmolColor = '#575961';
ELEMENT['Bi'].jmolColor = '#9E4FB5';
ELEMENT['Po'].jmolColor = '#AB5C00';
ELEMENT['At'].jmolColor = '#754F45';
ELEMENT['Rn'].jmolColor = '#428296';
ELEMENT['Fr'].jmolColor = '#420066';
ELEMENT['Ra'].jmolColor = '#007D00';
ELEMENT['Ac'].jmolColor = '#70ABFA';
ELEMENT['Th'].jmolColor = '#00BAFF';
ELEMENT['Pa'].jmolColor = '#00A1FF';
ELEMENT['U'].jmolColor = '#008FFF';
ELEMENT['Np'].jmolColor = '#0080FF';
ELEMENT['Pu'].jmolColor = '#006BFF';
ELEMENT['Am'].jmolColor = '#545CF2';
ELEMENT['Cm'].jmolColor = '#785CE3';
ELEMENT['Bk'].jmolColor = '#8A4FE3';
ELEMENT['Cf'].jmolColor = '#A136D4';
ELEMENT['Es'].jmolColor = '#B31FD4';
ELEMENT['Fm'].jmolColor = '#B31FBA';
ELEMENT['Md'].jmolColor = '#B30DA6';
ELEMENT['No'].jmolColor = '#BD0D87';
ELEMENT['Lr'].jmolColor = '#C70066';
ELEMENT['Rf'].jmolColor = '#CC0059';
ELEMENT['Db'].jmolColor = '#D1004F';
ELEMENT['Sg'].jmolColor = '#D90045';
ELEMENT['Bh'].jmolColor = '#E00038';
ELEMENT['Hs'].jmolColor = '#E6002E';
ELEMENT['Mt'].jmolColor = '#EB0026';
ELEMENT['Ds'].jmolColor = '#000000';
ELEMENT['Rg'].jmolColor = '#000000';
ELEMENT['Cn'].jmolColor = '#000000';
ELEMENT['Uut'].jmolColor = '#000000';
ELEMENT['Uuq'].jmolColor = '#000000';
ELEMENT['Uup'].jmolColor = '#000000';
ELEMENT['Uuh'].jmolColor = '#000000';
ELEMENT['Uus'].jmolColor = '#000000';
ELEMENT['Uuo'].jmolColor = '#000000';

//set up covalent radii
ELEMENT['H'].covalentRadius = 0.31;
ELEMENT['He'].covalentRadius = 0.28;
ELEMENT['Li'].covalentRadius = 1.28;
ELEMENT['Be'].covalentRadius = 0.96;
ELEMENT['B'].covalentRadius = 0.84;
ELEMENT['C'].covalentRadius = 0.76;
ELEMENT['N'].covalentRadius = 0.71;
ELEMENT['O'].covalentRadius = 0.66;
ELEMENT['F'].covalentRadius = 0.57;
ELEMENT['Ne'].covalentRadius = 0.58;
ELEMENT['Na'].covalentRadius = 1.66;
ELEMENT['Mg'].covalentRadius = 1.41;
ELEMENT['Al'].covalentRadius = 1.21;
ELEMENT['Si'].covalentRadius = 1.11;
ELEMENT['P'].covalentRadius = 1.07;
ELEMENT['S'].covalentRadius = 1.05;
ELEMENT['Cl'].covalentRadius = 1.02;
ELEMENT['Ar'].covalentRadius = 1.06;
ELEMENT['K'].covalentRadius = 2.03;
ELEMENT['Ca'].covalentRadius = 1.76;
ELEMENT['Sc'].covalentRadius = 1.7;
ELEMENT['Ti'].covalentRadius = 1.6;
ELEMENT['V'].covalentRadius = 1.53;
ELEMENT['Cr'].covalentRadius = 1.39;
ELEMENT['Mn'].covalentRadius = 1.39;
ELEMENT['Fe'].covalentRadius = 1.32;
ELEMENT['Co'].covalentRadius = 1.26;
ELEMENT['Ni'].covalentRadius = 1.24;
ELEMENT['Cu'].covalentRadius = 1.32;
ELEMENT['Zn'].covalentRadius = 1.22;
ELEMENT['Ga'].covalentRadius = 1.22;
ELEMENT['Ge'].covalentRadius = 1.2;
ELEMENT['As'].covalentRadius = 1.19;
ELEMENT['Se'].covalentRadius = 1.2;
ELEMENT['Br'].covalentRadius = 1.2;
ELEMENT['Kr'].covalentRadius = 1.16;
ELEMENT['Rb'].covalentRadius = 2.2;
ELEMENT['Sr'].covalentRadius = 1.95;
ELEMENT['Y'].covalentRadius = 1.9;
ELEMENT['Zr'].covalentRadius = 1.75;
ELEMENT['Nb'].covalentRadius = 1.64;
ELEMENT['Mo'].covalentRadius = 1.54;
ELEMENT['Tc'].covalentRadius = 1.47;
ELEMENT['Ru'].covalentRadius = 1.46;
ELEMENT['Rh'].covalentRadius = 1.42;
ELEMENT['Pd'].covalentRadius = 1.39;
ELEMENT['Ag'].covalentRadius = 1.45;
ELEMENT['Cd'].covalentRadius = 1.44;
ELEMENT['In'].covalentRadius = 1.42;
ELEMENT['Sn'].covalentRadius = 1.39;
ELEMENT['Sb'].covalentRadius = 1.39;
ELEMENT['Te'].covalentRadius = 1.38;
ELEMENT['I'].covalentRadius = 1.39;
ELEMENT['Xe'].covalentRadius = 1.4;
ELEMENT['Cs'].covalentRadius = 2.44;
ELEMENT['Ba'].covalentRadius = 2.15;
ELEMENT['La'].covalentRadius = 2.07;
ELEMENT['Ce'].covalentRadius = 2.04;
ELEMENT['Pr'].covalentRadius = 2.03;
ELEMENT['Nd'].covalentRadius = 2.01;
ELEMENT['Pm'].covalentRadius = 1.99;
ELEMENT['Sm'].covalentRadius = 1.98;
ELEMENT['Eu'].covalentRadius = 1.98;
ELEMENT['Gd'].covalentRadius = 1.96;
ELEMENT['Tb'].covalentRadius = 1.94;
ELEMENT['Dy'].covalentRadius = 1.92;
ELEMENT['Ho'].covalentRadius = 1.92;
ELEMENT['Er'].covalentRadius = 1.89;
ELEMENT['Tm'].covalentRadius = 1.9;
ELEMENT['Yb'].covalentRadius = 1.87;
ELEMENT['Lu'].covalentRadius = 1.87;
ELEMENT['Hf'].covalentRadius = 1.75;
ELEMENT['Ta'].covalentRadius = 1.7;
ELEMENT['W'].covalentRadius = 1.62;
ELEMENT['Re'].covalentRadius = 1.51;
ELEMENT['Os'].covalentRadius = 1.44;
ELEMENT['Ir'].covalentRadius = 1.41;
ELEMENT['Pt'].covalentRadius = 1.36;
ELEMENT['Au'].covalentRadius = 1.36;
ELEMENT['Hg'].covalentRadius = 1.32;
ELEMENT['Tl'].covalentRadius = 1.45;
ELEMENT['Pb'].covalentRadius = 1.46;
ELEMENT['Bi'].covalentRadius = 1.48;
ELEMENT['Po'].covalentRadius = 1.4;
ELEMENT['At'].covalentRadius = 1.5;
ELEMENT['Rn'].covalentRadius = 1.5;
ELEMENT['Fr'].covalentRadius = 2.6;
ELEMENT['Ra'].covalentRadius = 2.21;
ELEMENT['Ac'].covalentRadius = 2.15;
ELEMENT['Th'].covalentRadius = 2.06;
ELEMENT['Pa'].covalentRadius = 2.0;
ELEMENT['U'].covalentRadius = 1.96;
ELEMENT['Np'].covalentRadius = 1.9;
ELEMENT['Pu'].covalentRadius = 1.87;
ELEMENT['Am'].covalentRadius = 1.8;
ELEMENT['Cm'].covalentRadius = 1.69;
ELEMENT['Bk'].covalentRadius = 0.0;
ELEMENT['Cf'].covalentRadius = 0.0;
ELEMENT['Es'].covalentRadius = 0.0;
ELEMENT['Fm'].covalentRadius = 0.0;
ELEMENT['Md'].covalentRadius = 0.0;
ELEMENT['No'].covalentRadius = 0.0;
ELEMENT['Lr'].covalentRadius = 0.0;
ELEMENT['Rf'].covalentRadius = 0.0;
ELEMENT['Db'].covalentRadius = 0.0;
ELEMENT['Sg'].covalentRadius = 0.0;
ELEMENT['Bh'].covalentRadius = 0.0;
ELEMENT['Hs'].covalentRadius = 0.0;
ELEMENT['Mt'].covalentRadius = 0.0;
ELEMENT['Ds'].covalentRadius = 0.0;
ELEMENT['Rg'].covalentRadius = 0.0;
ELEMENT['Cn'].covalentRadius = 0.0;
ELEMENT['Uut'].covalentRadius = 0.0;
ELEMENT['Uuq'].covalentRadius = 0.0;
ELEMENT['Uup'].covalentRadius = 0.0;
ELEMENT['Uuh'].covalentRadius = 0.0;
ELEMENT['Uus'].covalentRadius = 0.0;
ELEMENT['Uuo'].covalentRadius = 0.0;

//set up vdW radii
ELEMENT['H'].vdWRadius = 1.2;
ELEMENT['He'].vdWRadius = 1.4;
ELEMENT['Li'].vdWRadius = 1.82;
ELEMENT['Be'].vdWRadius = 0.0;
ELEMENT['B'].vdWRadius = 0.0;
ELEMENT['C'].vdWRadius = 1.7;
ELEMENT['N'].vdWRadius = 1.55;
ELEMENT['O'].vdWRadius = 1.52;
ELEMENT['F'].vdWRadius = 1.47;
ELEMENT['Ne'].vdWRadius = 1.54;
ELEMENT['Na'].vdWRadius = 2.27;
ELEMENT['Mg'].vdWRadius = 1.73;
ELEMENT['Al'].vdWRadius = 0.0;
ELEMENT['Si'].vdWRadius = 2.1;
ELEMENT['P'].vdWRadius = 1.8;
ELEMENT['S'].vdWRadius = 1.8;
ELEMENT['Cl'].vdWRadius = 1.75;
ELEMENT['Ar'].vdWRadius = 1.88;
ELEMENT['K'].vdWRadius = 2.75;
ELEMENT['Ca'].vdWRadius = 0.0;
ELEMENT['Sc'].vdWRadius = 0.0;
ELEMENT['Ti'].vdWRadius = 0.0;
ELEMENT['V'].vdWRadius = 0.0;
ELEMENT['Cr'].vdWRadius = 0.0;
ELEMENT['Mn'].vdWRadius = 0.0;
ELEMENT['Fe'].vdWRadius = 0.0;
ELEMENT['Co'].vdWRadius = 0.0;
ELEMENT['Ni'].vdWRadius = 1.63;
ELEMENT['Cu'].vdWRadius = 1.4;
ELEMENT['Zn'].vdWRadius = 1.39;
ELEMENT['Ga'].vdWRadius = 1.87;
ELEMENT['Ge'].vdWRadius = 0.0;
ELEMENT['As'].vdWRadius = 1.85;
ELEMENT['Se'].vdWRadius = 1.9;
ELEMENT['Br'].vdWRadius = 1.85;
ELEMENT['Kr'].vdWRadius = 2.02;
ELEMENT['Rb'].vdWRadius = 0.0;
ELEMENT['Sr'].vdWRadius = 0.0;
ELEMENT['Y'].vdWRadius = 0.0;
ELEMENT['Zr'].vdWRadius = 0.0;
ELEMENT['Nb'].vdWRadius = 0.0;
ELEMENT['Mo'].vdWRadius = 0.0;
ELEMENT['Tc'].vdWRadius = 0.0;
ELEMENT['Ru'].vdWRadius = 0.0;
ELEMENT['Rh'].vdWRadius = 0.0;
ELEMENT['Pd'].vdWRadius = 1.63;
ELEMENT['Ag'].vdWRadius = 1.72;
ELEMENT['Cd'].vdWRadius = 1.58;
ELEMENT['In'].vdWRadius = 1.93;
ELEMENT['Sn'].vdWRadius = 2.17;
ELEMENT['Sb'].vdWRadius = 0.0;
ELEMENT['Te'].vdWRadius = 2.06;
ELEMENT['I'].vdWRadius = 1.98;
ELEMENT['Xe'].vdWRadius = 2.16;
ELEMENT['Cs'].vdWRadius = 0.0;
ELEMENT['Ba'].vdWRadius = 0.0;
ELEMENT['La'].vdWRadius = 0.0;
ELEMENT['Ce'].vdWRadius = 0.0;
ELEMENT['Pr'].vdWRadius = 0.0;
ELEMENT['Nd'].vdWRadius = 0.0;
ELEMENT['Pm'].vdWRadius = 0.0;
ELEMENT['Sm'].vdWRadius = 0.0;
ELEMENT['Eu'].vdWRadius = 0.0;
ELEMENT['Gd'].vdWRadius = 0.0;
ELEMENT['Tb'].vdWRadius = 0.0;
ELEMENT['Dy'].vdWRadius = 0.0;
ELEMENT['Ho'].vdWRadius = 0.0;
ELEMENT['Er'].vdWRadius = 0.0;
ELEMENT['Tm'].vdWRadius = 0.0;
ELEMENT['Yb'].vdWRadius = 0.0;
ELEMENT['Lu'].vdWRadius = 0.0;
ELEMENT['Hf'].vdWRadius = 0.0;
ELEMENT['Ta'].vdWRadius = 0.0;
ELEMENT['W'].vdWRadius = 0.0;
ELEMENT['Re'].vdWRadius = 0.0;
ELEMENT['Os'].vdWRadius = 0.0;
ELEMENT['Ir'].vdWRadius = 0.0;
ELEMENT['Pt'].vdWRadius = 1.75;
ELEMENT['Au'].vdWRadius = 1.66;
ELEMENT['Hg'].vdWRadius = 1.55;
ELEMENT['Tl'].vdWRadius = 1.96;
ELEMENT['Pb'].vdWRadius = 2.02;
ELEMENT['Bi'].vdWRadius = 0.0;
ELEMENT['Po'].vdWRadius = 0.0;
ELEMENT['At'].vdWRadius = 0.0;
ELEMENT['Rn'].vdWRadius = 0.0;
ELEMENT['Fr'].vdWRadius = 0.0;
ELEMENT['Ra'].vdWRadius = 0.0;
ELEMENT['Ac'].vdWRadius = 0.0;
ELEMENT['Th'].vdWRadius = 0.0;
ELEMENT['Pa'].vdWRadius = 0.0;
ELEMENT['U'].vdWRadius = 1.86;
ELEMENT['Np'].vdWRadius = 0.0;
ELEMENT['Pu'].vdWRadius = 0.0;
ELEMENT['Am'].vdWRadius = 0.0;
ELEMENT['Cm'].vdWRadius = 0.0;
ELEMENT['Bk'].vdWRadius = 0.0;
ELEMENT['Cf'].vdWRadius = 0.0;
ELEMENT['Es'].vdWRadius = 0.0;
ELEMENT['Fm'].vdWRadius = 0.0;
ELEMENT['Md'].vdWRadius = 0.0;
ELEMENT['No'].vdWRadius = 0.0;
ELEMENT['Lr'].vdWRadius = 0.0;
ELEMENT['Rf'].vdWRadius = 0.0;
ELEMENT['Db'].vdWRadius = 0.0;
ELEMENT['Sg'].vdWRadius = 0.0;
ELEMENT['Bh'].vdWRadius = 0.0;
ELEMENT['Hs'].vdWRadius = 0.0;
ELEMENT['Mt'].vdWRadius = 0.0;
ELEMENT['Ds'].vdWRadius = 0.0;
ELEMENT['Rg'].vdWRadius = 0.0;
ELEMENT['Cn'].vdWRadius = 0.0;
ELEMENT['Uut'].vdWRadius = 0.0;
ELEMENT['Uuq'].vdWRadius = 0.0;
ELEMENT['Uup'].vdWRadius = 0.0;
ELEMENT['Uuh'].vdWRadius = 0.0;
ELEMENT['Uus'].vdWRadius = 0.0;
ELEMENT['Uuo'].vdWRadius = 0.0;

ELEMENT['H'].valency = 1;
ELEMENT['He'].valency = 0;
ELEMENT['Li'].valency = 1;
ELEMENT['Be'].valency = 2;
ELEMENT['B'].valency = 3;
ELEMENT['C'].valency = 4;
ELEMENT['N'].valency = 3;
ELEMENT['O'].valency = 2;
ELEMENT['F'].valency = 1;
ELEMENT['Ne'].valency = 0;
ELEMENT['Na'].valency = 1;
ELEMENT['Mg'].valency = 0;
ELEMENT['Al'].valency = 0;
ELEMENT['Si'].valency = 4;
ELEMENT['P'].valency = 3;
ELEMENT['S'].valency = 2;
ELEMENT['Cl'].valency = 1;
ELEMENT['Ar'].valency = 0;
ELEMENT['K'].valency = 0;
ELEMENT['Ca'].valency = 0;
ELEMENT['Sc'].valency = 0;
ELEMENT['Ti'].valency = 1;
ELEMENT['V'].valency = 1;
ELEMENT['Cr'].valency = 2;
ELEMENT['Mn'].valency = 3;
ELEMENT['Fe'].valency = 2;
ELEMENT['Co'].valency = 1;
ELEMENT['Ni'].valency = 1;
ELEMENT['Cu'].valency = 0;
ELEMENT['Zn'].valency = 0;
ELEMENT['Ga'].valency = 0;
ELEMENT['Ge'].valency = 4;
ELEMENT['As'].valency = 3;
ELEMENT['Se'].valency = 2;
ELEMENT['Br'].valency = 1;
ELEMENT['Kr'].valency = 0;
ELEMENT['Rb'].valency = 0;
ELEMENT['Sr'].valency = 0;
ELEMENT['Y'].valency = 0;
ELEMENT['Zr'].valency = 0;
ELEMENT['Nb'].valency = 1;
ELEMENT['Mo'].valency = 2;
ELEMENT['Tc'].valency = 3;
ELEMENT['Ru'].valency = 2;
ELEMENT['Rh'].valency = 1;
ELEMENT['Pd'].valency = 0;
ELEMENT['Ag'].valency = 0;
ELEMENT['Cd'].valency = 0;
ELEMENT['In'].valency = 0;
ELEMENT['Sn'].valency = 4;
ELEMENT['Sb'].valency = 3;
ELEMENT['Te'].valency = 2;
ELEMENT['I'].valency = 1;
ELEMENT['Xe'].valency = 0;
ELEMENT['Cs'].valency = 0;
ELEMENT['Ba'].valency = 0;
ELEMENT['La'].valency = 0;
ELEMENT['Ce'].valency = 0;
ELEMENT['Pr'].valency = 0;
ELEMENT['Nd'].valency = 0;
ELEMENT['Pm'].valency = 0;
ELEMENT['Sm'].valency = 0;
ELEMENT['Eu'].valency = 0;
ELEMENT['Gd'].valency = 0;
ELEMENT['Tb'].valency = 0;
ELEMENT['Dy'].valency = 0;
ELEMENT['Ho'].valency = 0;
ELEMENT['Er'].valency = 0;
ELEMENT['Tm'].valency = 0;
ELEMENT['Yb'].valency = 0;
ELEMENT['Lu'].valency = 0;
ELEMENT['Hf'].valency = 0;
ELEMENT['Ta'].valency = 1;
ELEMENT['W'].valency = 2;
ELEMENT['Re'].valency = 3;
ELEMENT['Os'].valency = 2;
ELEMENT['Ir'].valency = 3;
ELEMENT['Pt'].valency = 0;
ELEMENT['Au'].valency = 1;
ELEMENT['Hg'].valency = 0;
ELEMENT['Tl'].valency = 0;
ELEMENT['Pb'].valency = 4;
ELEMENT['Bi'].valency = 3;
ELEMENT['Po'].valency = 2;
ELEMENT['At'].valency = 1;
ELEMENT['Rn'].valency = 0;
ELEMENT['Fr'].valency = 0;
ELEMENT['Ra'].valency = 0;
ELEMENT['Ac'].valency = 0;
ELEMENT['Th'].valency = 0;
ELEMENT['Pa'].valency = 0;
ELEMENT['U'].valency = 0;
ELEMENT['Np'].valency = 0;
ELEMENT['Pu'].valency = 0;
ELEMENT['Am'].valency = 0;
ELEMENT['Cm'].valency = 0;
ELEMENT['Bk'].valency = 0;
ELEMENT['Cf'].valency = 0;
ELEMENT['Es'].valency = 0;
ELEMENT['Fm'].valency = 0;
ELEMENT['Md'].valency = 0;
ELEMENT['No'].valency = 0;
ELEMENT['Lr'].valency = 0;
ELEMENT['Rf'].valency = 0;
ELEMENT['Db'].valency = 0;
ELEMENT['Sg'].valency = 0;
ELEMENT['Bh'].valency = 0;
ELEMENT['Hs'].valency = 0;
ELEMENT['Mt'].valency = 0;
ELEMENT['Ds'].valency = 0;
ELEMENT['Rg'].valency = 0;
ELEMENT['Cn'].valency = 0;
ELEMENT['Uut'].valency = 0;
ELEMENT['Uuq'].valency = 0;
ELEMENT['Uup'].valency = 0;
ELEMENT['Uuh'].valency = 0;
ELEMENT['Uus'].valency = 0;
ELEMENT['Uuo'].valency = 0;

//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2777 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:48:33 -0400 (Thu, 12 Aug 2010) $
//
function Point(x, y){
    this.x = x ? x : 0;
    this.y = y ? y : 0;
    return true;
}

Point.prototype.sub = function(p){
    this.x -= p.x;
    this.y -= p.y;
}
Point.prototype.add = function(p){
    this.x += p.x;
    this.y += p.y;
}
Point.prototype.distance = function(p){
    return Math.sqrt(Math.pow(p.x - this.x, 2) + Math.pow(p.y - this.y, 2));
}
Point.prototype.angleForStupidCanvasArcs = function(p){
    var dx = p.x - this.x;
    var dy = p.y - this.y;
    var angle = 0;
    // Calculate angle
    if (dx == 0) {
        if (dy == 0) {
            angle = 0;
        }
        else 
            if (dy > 0) {
                angle = Math.PI / 2;
            }
            else {
                angle = 3 * Math.PI / 2;
            }
    }
    else 
        if (dy == 0) {
            if (dx > 0) {
                angle = 0;
            }
            else {
                angle = Math.PI;
            }
        }
        else {
            if (dx < 0) {
                angle = Math.atan(dy / dx) + Math.PI;
            }
            else 
                if (dy < 0) {
                    angle = Math.atan(dy / dx) + 2 * Math.PI;
                }
                else {
                    angle = Math.atan(dy / dx);
                }
        }
    while (angle < 0) {
        angle += Math.PI * 2;
    }
    angle = angle % (Math.PI * 2);
    return angle;
}
Point.prototype.angle = function(p){
    //y is upside down to account for inverted canvas
    var dx = p.x - this.x;
    var dy = this.y - p.y;
    var angle = 0;
    // Calculate angle
    if (dx == 0) {
        if (dy == 0) {
            angle = 0;
        }
        else 
            if (dy > 0) {
                angle = Math.PI / 2;
            }
            else {
                angle = 3 * Math.PI / 2;
            }
    }
    else 
        if (dy == 0) {
            if (dx > 0) {
                angle = 0;
            }
            else {
                angle = Math.PI;
            }
        }
        else {
            if (dx < 0) {
                angle = Math.atan(dy / dx) + Math.PI;
            }
            else 
                if (dy < 0) {
                    angle = Math.atan(dy / dx) + 2 * Math.PI;
                }
                else {
                    angle = Math.atan(dy / dx);
                }
        }
    while (angle < 0) {
        angle += Math.PI * 2;
    }
    angle = angle % (Math.PI * 2);
    return angle;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2777 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:48:33 -0400 (Thu, 12 Aug 2010) $
//
function Ring(){
    this.atoms = [];
    this.bonds = [];
    this.center = null;
	return true;
}

Ring.prototype.setupBonds = function(){
    for (var i = 0, ii = this.bonds.length; i < ii; i++) {
        this.bonds[i].ring = this;
    };
    this.center = this.getCenter();
}
Ring.prototype.getCenter = function(){
    var minX = minY = Infinity;
    var maxX = maxY = -Infinity;
    for (var i = 0, ii = this.atoms.length; i < ii; i++) {
        minX = Math.min(this.atoms[i].x, minX);
        minY = Math.min(this.atoms[i].y, minY);
        maxX = Math.max(this.atoms[i].x, maxX);
        maxY = Math.max(this.atoms[i].y, maxY);
    };
    return new Point((maxX + minX) / 2, (maxY + minY) / 2);
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2794 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-13 12:12:28 -0400 (Fri, 13 Aug 2010) $
//
function Atom(label, x, y, z){
    this.x = x ? x : 0;
    this.y = y ? y : 0;
    this.z = z ? z : 0;
    this.charge = 0;
    this.coordinationNumber = 0;
    this.bondNumber = 0;
    this.angleOfLeastInterference = 0;
    this.isHidden = false;
    this.label = label ? label.replace(/\s/g, '') : 'C';
    if (!ELEMENT[this.label]) {
        this.label = 'C';
    }
    this.isLone = false;
    this.isHover = false;
    this.isSelected = false;
    this.isOverlap = false;
    return true;
}

Atom.prototype = new Point(0, 0);

Atom.prototype.add3D = function(p){
    this.x += p.x;
    this.y += p.y;
    this.z += p.z;
}
Atom.prototype.sub3D = function(p){
    this.x -= p.x;
    this.y -= p.y;
    this.z -= p.z;
}
Atom.prototype.distance3D = function(p){
    return Math.sqrt(Math.pow(p.x - this.x, 2) + Math.pow(p.y - this.y, 2) + Math.pow(p.z - this.z, 2));
}
Atom.prototype.draw = function(ctx, specs){
    var font = specs.getFontString(specs.atoms_font_size_2D, specs.atoms_font_families_2D);
    ctx.font = font;
    ctx.fillStyle = specs.atoms_color;
    if (specs.atoms_useJMOLColors) {
        ctx.fillStyle = ELEMENT[this.label].jmolColor;
    }
    if (this.isLone || specs.atoms_circles_2D) {
        ctx.beginPath();
        ctx.arc(this.x, this.y, specs.atoms_circleDiameter_2D / 2, 0, Math.PI * 2, false);
        ctx.fill();
        if (specs.atoms_circleBorderWidth_2D > 0) {
            ctx.lineWidth = specs.atoms_circleBorderWidth_2D;
            ctx.strokeStyle = 'black';
            ctx.stroke(this.x, this.y, 0, Math.PI * 2, specs.atoms_circleDiameter_2D / 2);
        }
    }
    else 
        if (this.isLabelVisible(specs)) {
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(this.label, this.x, this.y);
            if (specs.atoms_implicitHydrogens_2D) {
                //implicit hydrogens
                var numHs = ELEMENT[this.label].valency - this.coordinationNumber - Math.abs(this.charge);
                if (numHs > 0) {
                    var symbolWidth = ctx.measureText(this.label).width;
                    var hWidth = ctx.measureText('H').width;
                    if (numHs > 1) {
                        var xoffset = symbolWidth / 2 + hWidth / 2;
                        var yoffset = 0;
                        var subFont = specs.getFontString(specs.atoms_font_size_2D * .8, specs.atoms_font_families_2D);
                        ctx.font = subFont;
                        var numWidth = ctx.measureText(numHs).width;
                        if (this.bondNumber == 1) {
                            if (this.angleOfLeastInterference > Math.PI / 2 && this.angleOfLeastInterference < 3 * Math.PI / 2) {
                                xoffset = -symbolWidth / 2 - numWidth - hWidth / 2;
                            }
                        }
                        else {
                            if (this.angleOfLeastInterference <= Math.PI / 4) {
                            //default
                            }
                            else 
                                if (this.angleOfLeastInterference < 3 * Math.PI / 4) {
                                    xoffset = 0;
                                    yoffset = -specs.atoms_font_size_2D * .9;
                                }
                                else 
                                    if (this.angleOfLeastInterference <= 5 * Math.PI / 4) {
                                        xoffset = -symbolWidth / 2 - numWidth - hWidth / 2;
                                    }
                                    else 
                                        if (this.angleOfLeastInterference < 7 * Math.PI / 4) {
                                            xoffset = 0;
                                            yoffset = specs.atoms_font_size_2D * .9;
                                        }
                        }
                        ctx.font = font;
                        ctx.fillText('H', this.x + xoffset, this.y + yoffset);
                        ctx.font = subFont;
                        ctx.fillText(numHs, this.x + xoffset + hWidth / 2 + numWidth / 2, this.y + yoffset + specs.atoms_font_size_2D * .3);
                    }
                    else {
                        var xoffset = symbolWidth / 2 + hWidth / 2;
                        var yoffset = 0;
                        if (this.bondNumber == 1) {
                            if (this.angleOfLeastInterference > Math.PI / 2 && this.angleOfLeastInterference < 3 * Math.PI / 2) {
                                xoffset = -symbolWidth / 2 - hWidth / 2;
                            }
                        }
                        else {
                            if (this.angleOfLeastInterference <= Math.PI / 4) {
                            //default
                            }
                            else 
                                if (this.angleOfLeastInterference < 3 * Math.PI / 4) {
                                    xoffset = 0;
                                    yoffset = -specs.atoms_font_size_2D * .9;
                                }
                                else 
                                    if (this.angleOfLeastInterference <= 5 * Math.PI / 4) {
                                        xoffset = -symbolWidth / 2 - hWidth / 2;
                                    }
                                    else 
                                        if (this.angleOfLeastInterference < 7 * Math.PI / 4) {
                                            xoffset = 0;
                                            yoffset = specs.atoms_font_size_2D * .9;
                                        }
                        }
                        ctx.fillText('H', this.x + xoffset, this.y + yoffset);
                    }
                }
            }
        }
    if (this.charge != null && this.charge != 0) {
        var s = this.charge.toFixed(0);
        if (s == '1') {
            s = '+';
        }
        else 
            if (s == '-1') {
                s = '\u2013';
            }
            else 
                if (s.startsWith('-')) {
                    s = s.substring(1) + '\u2013';
                }
                else {
                    s += '+';
                }
        ctx.fillText(s, this.x + specs.atoms_font_size_2D * Math.cos(this.angleOfLeastInterference + Math.PI / 4), this.y - specs.atoms_font_size_2D * Math.sin(this.angleOfLeastInterference + Math.PI / 4));
    }
    if (this.isHover || this.isSelected || this.isOverlap) {
        ctx.strokeStyle = this.isHover ? '#885110' : '#0060B2';
        ctx.lineWidth = 1.2;
        ctx.beginPath();
        ctx.arc(this.x, this.y, 7, 0, Math.PI * 2, false);
        ctx.stroke();
    }
}
Atom.prototype.render = function(gl, specs){
	var transform = mat4.translate(gl.modelViewMatrix, [this.x, this.y, this.z], []);
    var radius = specs.atoms_useVDWDiameters_3D ? ELEMENT[this.label].vdWRadius : specs.atoms_sphereDiameter_3D / 2;
    mat4.scale(transform, [radius, radius, radius]);
    //positions
    gl.bindBuffer(gl.ARRAY_BUFFER, gl.sphereBuffer.vertexPositionBuffer);
    gl.vertexAttribPointer(gl.shader.vertexPositionAttribute, gl.sphereBuffer.vertexPositionBuffer.itemSize, gl.FLOAT, false, 0, 0);
    //colors
    var color = specs.atoms_useJMOLColors ? ELEMENT[this.label].jmolColor : specs.atoms_color;
    gl.material.setTempColors(gl, specs.atoms_materialAmbientColor_3D, color, specs.atoms_materialSpecularColor_3D, specs.atoms_materialShininess_3D);
    //normals
    gl.bindBuffer(gl.ARRAY_BUFFER, gl.sphereBuffer.vertexNormalBuffer);
    gl.vertexAttribPointer(gl.shader.vertexNormalAttribute, gl.sphereBuffer.vertexNormalBuffer.itemSize, gl.FLOAT, false, 0, 0);
    //render
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, gl.sphereBuffer.vertexIndexBuffer);
    gl.setMatrixUniforms(gl.projectionMatrix, transform);
    gl.drawElements(gl.TRIANGLES, gl.sphereBuffer.vertexIndexBuffer.numItems, gl.UNSIGNED_SHORT, 0);
}
Atom.prototype.isLabelVisible = function(specs){
    return this.label != 'C' || (this.isHidden && specs.atoms_showHiddenCarbons_2D) || (specs.atoms_displayTerminalCarbonLabels_2D && this.bondNumber == 1);
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2794 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-13 12:12:28 -0400 (Fri, 13 Aug 2010) $
//

var BOND_STEREO_NONE = 'BOND_STEREO_NONE';
var BOND_STEREO_PROTRUDING = 'BOND_STEREO_PROTRUDING';
var BOND_STEREO_RECESSED = 'BOND_STEREO_RECESSED';
var BOND_STEREO_AMBIGUOUS = 'BOND_STEREO_AMBIGUOUS';

function Bond(a1, a2, bondOrder){
    this.a1 = a1;
    this.a2 = a2;
    this.bondOrder = bondOrder ? bondOrder : 1;
    this.stereo = BOND_STEREO_NONE;
    this.isHover = false;
    this.ring = null;
    return true;
}

Bond.prototype.getCenter = function(){
    return new Point((this.a1.x + this.a2.x) / 2, (this.a1.y + this.a2.y) / 2);
}
Bond.prototype.getLength = function(){
    return this.a1.distance(this.a2);
}
Bond.prototype.getLength3D = function(){
    return this.a1.distance3D(this.a2);
}
Bond.prototype.contains = function(a){
    return a == this.a1 || a == this.a2;
}
Bond.prototype.getNeighbor = function(a){
    if (a == this.a1) {
        return this.a2;
    }
    else 
        if (a == this.a2) {
            return this.a1;
        }
    return null;
}
Bond.prototype.draw = function(ctx, specs){
    var x1 = this.a1.x;
    var x2 = this.a2.x;
    var y1 = this.a1.y;
    var y2 = this.a2.y;
    var difX = x2 - x1;
    var difY = y2 - y1;
    if (specs.atoms_display && !specs.atoms_circles_2D && this.a1.isLabelVisible(specs)) {
        x1 += difX * specs.bonds_atomLabelBuffer_2D;
        y1 += difY * specs.bonds_atomLabelBuffer_2D;
    }
    if (specs.atoms_display && !specs.atoms_circles_2D && this.a2.isLabelVisible(specs)) {
        x2 -= difX * specs.bonds_atomLabelBuffer_2D;
        y2 -= difY * specs.bonds_atomLabelBuffer_2D;
    }
    if (specs.bonds_clearOverlaps_2D) {
        var xs = x1 + difX * .15;
        var ys = y1 + difY * .15;
        var xf = x2 - difX * .15;
        var yf = y2 - difY * .15;
        ctx.strokeStyle = specs.backgroundColor;
        ctx.lineWidth = specs.bonds_width_2D + specs.bonds_overlapClearWidth_2D * 2;
        ctx.lineCap = 'round';
        ctx.beginPath();
        ctx.moveTo(xs, ys);
        ctx.lineTo(xf, yf);
        ctx.closePath();
        ctx.stroke();
    }
    ctx.strokeStyle = specs.bonds_color;
    ctx.fillStyle = specs.bonds_color;
    ctx.lineWidth = specs.bonds_width_2D;
    ctx.lineCap = specs.bonds_ends_2D;
    if (specs.bonds_useJMOLColors) {
        var linearGradient = ctx.createLinearGradient(x1, y1, x2, y2);
        linearGradient.addColorStop(0, ELEMENT[this.a1.label].jmolColor);
        linearGradient.addColorStop(1, ELEMENT[this.a2.label].jmolColor);
        ctx.strokeStyle = linearGradient;
    }
    switch (this.bondOrder) {
        case 1:
            if (this.stereo == BOND_STEREO_PROTRUDING || this.stereo == BOND_STEREO_RECESSED) {
                var useDist = this.a1.distance(this.a2) * specs.bonds_wedgeThickness_2D / 2;
                var perpendicular = this.a1.angle(this.a2) + Math.PI / 2;
                var cx3 = x2 + Math.cos(perpendicular) * useDist;
                var cy3 = y2 - Math.sin(perpendicular) * useDist;
                var cx4 = x2 - Math.cos(perpendicular) * useDist;
                var cy4 = y2 + Math.sin(perpendicular) * useDist;
                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(cx3, cy3);
                ctx.lineTo(cx4, cy4);
                ctx.closePath();
                if (this.stereo == BOND_STEREO_PROTRUDING) {
                    ctx.fill();
                }
                else {
                    ctx.save();
                    ctx.clip();
                    ctx.beginPath();
                    ctx.moveTo(x1, y1);
                    ctx.lineWidth = useDist * 2;
                    ctx.lineCap = 'butt';
                    var travelled = 0;
                    var dist = this.a1.distance(this.a2);
                    var space = false;
                    var lastX = x1;
                    var lastY = y1;
                    while (travelled < dist) {
                        if (space) {
                            var percent = specs.bonds_hashSpacing_2D / dist;
                            lastX += percent * difX;
                            lastY += percent * difY;
                            ctx.moveTo(lastX, lastY);
                            travelled += specs.bonds_hashSpacing_2D;
                        }
                        else {
                            var percent = specs.bonds_hashWidth_2D / dist;
                            lastX += percent * difX;
                            lastY += percent * difY;
                            ctx.lineTo(lastX, lastY);
                            travelled += specs.bonds_hashWidth_2D;
                        }
                        space = !space;
                    }
                    ctx.stroke();
                    ctx.restore();
                }
            }
            else {
                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                ctx.stroke();
            }
            break;
        case 1.5:
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
            break;
        case 2:
            if (this.stereo == BOND_STEREO_AMBIGUOUS) {
                var useDist = this.a1.distance(this.a2) * specs.bonds_saturationWidth_2D / 2;
                var perpendicular = this.a1.angle(this.a2) + Math.PI / 2;
                var cx1 = x1 - Math.cos(perpendicular) * useDist;
                var cy1 = y1 + Math.sin(perpendicular) * useDist;
                var cx2 = x1 + Math.cos(perpendicular) * useDist;
                var cy2 = y1 - Math.sin(perpendicular) * useDist;
                var cx3 = x2 + Math.cos(perpendicular) * useDist;
                var cy3 = y2 - Math.sin(perpendicular) * useDist;
                var cx4 = x2 - Math.cos(perpendicular) * useDist;
                var cy4 = y2 + Math.sin(perpendicular) * useDist;
                ctx.beginPath();
                ctx.moveTo(cx1, cy1);
                ctx.lineTo(cx3, cy3);
                ctx.moveTo(cx2, cy2);
                ctx.lineTo(cx4, cy4);
                ctx.stroke();
            }
            else 
                if (!specs.bonds_symmetrical_2D && (this.ring != null || this.a1.label == 'C' && this.a2.label == 'C')) {
                    ctx.beginPath();
                    ctx.moveTo(x1, y1);
                    ctx.lineTo(x2, y2);
                    var clip = 0;
                    var dist = this.a1.distance(this.a2);
                    var angle = this.a1.angle(this.a2);
                    var perpendicular = angle + Math.PI / 2;
                    var useDist = dist * specs.bonds_saturationWidth_2D;
                    var clipAngle = specs.bonds_saturationAngle_2D;
                    if (clipAngle < Math.PI / 2) {
                        clip = -(useDist / Math.tan(clipAngle));
                    }
                    if (Math.abs(clip) < dist / 2) {
                        var xuse1 = x1 - Math.cos(angle) * clip;
                        var xuse2 = x2 + Math.cos(angle) * clip;
                        var yuse1 = y1 + Math.sin(angle) * clip;
                        var yuse2 = y2 - Math.sin(angle) * clip;
                        var cx1 = xuse1 - Math.cos(perpendicular) * useDist;
                        var cy1 = yuse1 + Math.sin(perpendicular) * useDist;
                        var cx2 = xuse1 + Math.cos(perpendicular) * useDist;
                        var cy2 = yuse1 - Math.sin(perpendicular) * useDist;
                        var cx3 = xuse2 - Math.cos(perpendicular) * useDist;
                        var cy3 = yuse2 + Math.sin(perpendicular) * useDist;
                        var cx4 = xuse2 + Math.cos(perpendicular) * useDist;
                        var cy4 = yuse2 - Math.sin(perpendicular) * useDist;
                        var flip = this.ring == null || (this.ring.center.angle(this.a1) > this.ring.center.angle(this.a2) && !(this.ring.center.angle(this.a1) - this.ring.center.angle(this.a2) > Math.PI) || (this.ring.center.angle(this.a1) - this.ring.center.angle(this.a2) < -Math.PI));
                        if (flip) {
                            ctx.moveTo(cx1, cy1);
                            ctx.lineTo(cx3, cy3);
                        }
                        else {
                            ctx.moveTo(cx2, cy2);
                            ctx.lineTo(cx4, cy4);
                        }
                        ctx.stroke();
                    }
                }
                else {
                    var useDist = this.a1.distance(this.a2) * specs.bonds_saturationWidth_2D / 2;
                    var perpendicular = this.a1.angle(this.a2) + Math.PI / 2;
                    var cx1 = x1 - Math.cos(perpendicular) * useDist;
                    var cy1 = y1 + Math.sin(perpendicular) * useDist;
                    var cx2 = x1 + Math.cos(perpendicular) * useDist;
                    var cy2 = y1 - Math.sin(perpendicular) * useDist;
                    var cx3 = x2 + Math.cos(perpendicular) * useDist;
                    var cy3 = y2 - Math.sin(perpendicular) * useDist;
                    var cx4 = x2 - Math.cos(perpendicular) * useDist;
                    var cy4 = y2 + Math.sin(perpendicular) * useDist;
                    ctx.beginPath();
                    ctx.moveTo(cx1, cy1);
                    ctx.lineTo(cx4, cy4);
                    ctx.moveTo(cx2, cy2);
                    ctx.lineTo(cx3, cy3);
                    ctx.stroke();
                }
            break;
        case 3:
            var useDist = this.a1.distance(this.a2) * specs.bonds_saturationWidth_2D;
            var perpendicular = this.a1.angle(this.a2) + Math.PI / 2;
            var cx1 = x1 - Math.cos(perpendicular) * useDist;
            var cy1 = y1 + Math.sin(perpendicular) * useDist;
            var cx2 = x1 + Math.cos(perpendicular) * useDist;
            var cy2 = y1 - Math.sin(perpendicular) * useDist;
            var cx3 = x2 + Math.cos(perpendicular) * useDist;
            var cy3 = y2 - Math.sin(perpendicular) * useDist;
            var cx4 = x2 - Math.cos(perpendicular) * useDist;
            var cy4 = y2 + Math.sin(perpendicular) * useDist;
            ctx.beginPath();
            ctx.moveTo(cx1, cy1);
            ctx.lineTo(cx4, cy4);
            ctx.moveTo(cx2, cy2);
            ctx.lineTo(cx3, cy3);
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
            break;
    }
    if (this.isHover) {
		var pi2 = 2*Math.PI;
        var angle = (this.a1.angleForStupidCanvasArcs(this.a2) + Math.PI / 2)%pi2;
        ctx.strokeStyle = '#885110';
        ctx.lineWidth = 1.2;
        ctx.beginPath();
        var angleTo = (angle + Math.PI)%pi2;
        angleTo = angleTo % (Math.PI * 2);
        ctx.arc(this.a1.x, this.a1.y, 6, angle, angleTo, false);
        ctx.stroke();
        ctx.beginPath();
        angle += Math.PI;
        angleTo = (angle + Math.PI)%pi2;
        ctx.arc(this.a2.x, this.a2.y, 7, angle, angleTo, false);
        ctx.stroke();
    }
}
Bond.prototype.render = function(gl, specs){
	var transform = mat4.translate(gl.modelViewMatrix, [this.a1.x, this.a1.y, this.a1.z], []);
    //align bond
    var a2b = [this.a2.x - this.a1.x, this.a2.y - this.a1.y, this.a2.z - this.a1.z];
    if (specs.bonds_useJMOLColors) {
        vec3.scale(a2b, .5);
    }
    if (this.a1.x == this.a2.x && this.a1.z == this.a2.z) {
        if (this.a2.y < this.a1.y) {
			mat4.rotate(transform, Math.PI, [0,0,1]);
        }
    }
    else {
	    var mult = [0, 1, 0];
		mat4.rotate(transform, vec3.angleFrom(mult, a2b), vec3.cross(mult, a2b, []));
    }
    var height = specs.bonds_useJMOLColors ? this.a1.distance3D(this.a2) / 2 : this.a1.distance3D(this.a2);
    if (height == 0) {
        return false;
    }
	mat4.scale(transform, [specs.bonds_cylinderDiameter_3D / 2, height, specs.bonds_cylinderDiameter_3D / 2]);
    //colors
    var color = specs.bonds_useJMOLColors ? ELEMENT[this.a1.label].jmolColor : specs.bonds_color;
    gl.material.setTempColors(gl, specs.bonds_materialAmbientColor_3D, color, specs.bonds_materialSpecularColor_3D, specs.bonds_materialShininess_3D);
    //normals
    gl.bindBuffer(gl.ARRAY_BUFFER, gl.cylinderBuffer.vertexNormalBuffer);
    gl.vertexAttribPointer(gl.shader.vertexNormalAttribute, gl.cylinderBuffer.vertexNormalBuffer.itemSize, gl.FLOAT, false, 0, 0);
    //positions
    gl.bindBuffer(gl.ARRAY_BUFFER, gl.cylinderBuffer.vertexPositionBuffer);
    gl.vertexAttribPointer(gl.shader.vertexPositionAttribute, gl.cylinderBuffer.vertexPositionBuffer.itemSize, gl.FLOAT, false, 0, 0);
    //render
    gl.setMatrixUniforms(gl.projectionMatrix, transform);
    gl.drawArrays(gl.TRIANGLE_STRIP, 0, gl.cylinderBuffer.vertexPositionBuffer.numItems);
    if (specs.bonds_useJMOLColors) {
		transform = mat4.translate(gl.modelViewMatrix, [this.a2.x, this.a2.y, this.a2.z], []);
        //align bond
		vec3.scale(a2b, -1);
        if (this.a1.x == this.a2.x && this.a1.z == this.a2.z) {
            if (this.a2.y > this.a1.y) {
				mat4.rotate(transform, Math.PI, [0,0,1]);
            }
        }
        else {
	        var mult = [0, 1, 0];
			mat4.rotate(transform, vec3.angleFrom(mult, a2b), vec3.cross(mult, a2b, []));
        }
		mat4.scale(transform, [specs.bonds_cylinderDiameter_3D / 2, height* 1.001, specs.bonds_cylinderDiameter_3D / 2]);
        //colors
        gl.material.setTempColors(gl, specs.bonds_materialAmbientColor_3D, ELEMENT[this.a2.label].jmolColor, specs.bonds_materialSpecularColor_3D, specs.bonds_materialShininess_3D);
        //normals
        gl.bindBuffer(gl.ARRAY_BUFFER, gl.cylinderBuffer.vertexNormalBuffer);
        gl.vertexAttribPointer(gl.shader.vertexNormalAttribute, gl.cylinderBuffer.vertexNormalBuffer.itemSize, gl.FLOAT, false, 0, 0);
        //positions
        gl.bindBuffer(gl.ARRAY_BUFFER, gl.cylinderBuffer.vertexPositionBuffer);
        gl.vertexAttribPointer(gl.shader.vertexPositionAttribute, gl.cylinderBuffer.vertexPositionBuffer.itemSize, gl.FLOAT, false, 0, 0);
        //render
        gl.setMatrixUniforms(gl.projectionMatrix, transform);
        gl.drawArrays(gl.TRIANGLE_STRIP, 0, gl.cylinderBuffer.vertexPositionBuffer.numItems);
    }
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2777 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:48:33 -0400 (Thu, 12 Aug 2010) $
//
function Molecule(){
    this.atoms = [];
    this.bonds = [];
    this.rings = [];
    //this can be an extensive algorithm for large molecules, you may want to turn this off
    this.findRings = true;
    return true;
}

Molecule.prototype.draw = function(ctx, specs){
    //draw
    if (specs.bonds_display == true) {
        for (var i = 0, ii = this.bonds.length; i < ii; i++) {
            this.bonds[i].draw(ctx, specs);
        };
            }
    if (specs.atoms_display == true) {
        for (var i = 0, ii = this.atoms.length; i < ii; i++) {
            this.atoms[i].draw(ctx, specs);
        };
            }
}
Molecule.prototype.render = function(gl, specs){
    if (specs.bonds_display == true) {
        for (var i = 0, ii = this.bonds.length; i < ii; i++) {
            this.bonds[i].render(gl, specs);
        };
            }
    if (specs.atoms_display == true) {
        for (var i = 0, ii = this.atoms.length; i < ii; i++) {
            this.atoms[i].render(gl, specs);
        };
            }
}
Molecule.prototype.getCenter3D = function(){
    if (this.atoms.length == 1) {
        return new Atom('C', this.atoms[0].x, this.atoms[0].y, this.atoms[0].z);
    }
    var minX = minY = minZ = Infinity;
    var maxX = maxY = maxZ = -Infinity;
    for (var i = 0, ii = this.atoms.length; i < ii; i++) {
        minX = Math.min(this.atoms[i].x, minX);
        minY = Math.min(this.atoms[i].y, minY);
        minZ = Math.min(this.atoms[i].z, minZ);
        maxX = Math.max(this.atoms[i].x, maxX);
        maxY = Math.max(this.atoms[i].y, maxY);
        maxZ = Math.max(this.atoms[i].z, maxZ);
    };
    return new Atom('C', (maxX + minX) / 2, (maxY + minY) / 2, (maxZ + minZ) / 2);
}
Molecule.prototype.getCenter = function(){
    if (this.atoms.length == 1) {
        return new Point(this.atoms[0].x, this.atoms[0].y);
    }
    var minX = minY = Infinity;
    var maxX = maxY = -Infinity;
    for (var i = 0, ii = this.atoms.length; i < ii; i++) {
        minX = Math.min(this.atoms[i].x, minX);
        minY = Math.min(this.atoms[i].y, minY);
        maxX = Math.max(this.atoms[i].x, maxX);
        maxY = Math.max(this.atoms[i].y, maxY);
    };
    return new Point((maxX + minX) / 2, (maxY + minY) / 2);
}
Molecule.prototype.getDimension = function(){
    if (this.atoms.length == 1) {
        return new Point(0, 0);
    }
    var minX = minY = Infinity;
    var maxX = maxY = -Infinity;
    for (var i = 0, ii = this.atoms.length; i < ii; i++) {
        minX = Math.min(this.atoms[i].x, minX);
        minY = Math.min(this.atoms[i].y, minY);
        maxX = Math.max(this.atoms[i].x, maxX);
        maxY = Math.max(this.atoms[i].y, maxY);
    };
    return new Point(maxX - minX, maxY - minY);
}
Molecule.prototype.check = function(){
    //find lones
    for (var i = 0, ii = this.atoms.length; i < ii; i++) {
        this.atoms[i].isLone = false;
        if (this.atoms[i].label == 'C') {
            var counter = 0;
            for (var j = 0, jj = this.bonds.length; j < jj; j++) {
                if (this.bonds[j].a1 == this.atoms[i] || this.bonds[j].a2 == this.atoms[i]) {
                    counter++;
                }
            };
            if (counter == 0) {
                this.atoms[i].isLone = true;
            }
        }
    };
    if (this.findRings) {
        //find rings
        this.rings = new SSSRFinder(this).rings;
        for (var i = 0, ii = this.rings.length; i < ii; i++) {
            this.rings[i].setupBonds();
        };
            }
    //sort
    this.sortAtomsByZ();
    this.sortBondsByZ();
    //setup metadata
    this.setupMetaData();
}
Molecule.prototype.getAngles = function(a){
    var angles = [];
    for (var i = 0, ii = this.bonds.length; i < ii; i++) {
        if (this.bonds[i].contains(a)) {
            angles[angles.length] = a.angle(this.bonds[i].getNeighbor(a));
        }
    };
    angles.sort();
    return angles;
}
Molecule.prototype.getCoordinationNumber = function(bs){
    var coordinationNumber = 0;
    for (var i = 0, ii = bs.length; i < ii; i++) {
        coordinationNumber += bs[i].bondOrder;
    };
    return coordinationNumber;
}
Molecule.prototype.getBonds = function(a){
    var bonds = [];
    for (var i = 0, ii = this.bonds.length; i < ii; i++) {
        if (this.bonds[i].contains(a)) {
            bonds[bonds.length] = this.bonds[i];
        }
    };
    return bonds;
}
Molecule.prototype.sortAtomsByZ = function(){
    for (var i = 1, ii = this.atoms.length; i < ii; i++) {
        var index = i;
        while (index > 0 && this.atoms[index].z < this.atoms[index - 1].z) {
            var hold = this.atoms[index];
            this.atoms[index] = this.atoms[index - 1];
            this.atoms[index - 1] = hold;
            index--;
        }
    }
}
Molecule.prototype.sortBondsByZ = function(){
    for (var i = 1, ii = this.bonds.length; i < ii; i++) {
        var index = i;
        while (index > 0 && (this.bonds[index].a1.z + this.bonds[index].a2.z) < (this.bonds[index - 1].a1.z + this.bonds[index - 1].a2.z)) {
            var hold = this.bonds[index];
            this.bonds[index] = this.bonds[index - 1];
            this.bonds[index - 1] = hold;
            index--;
        }
    }
}
Molecule.prototype.setupMetaData = function(){
    for (var i = 0, ii = this.atoms.length; i < ii; i++) {
        var a = this.atoms[i];
        var bonds = this.getBonds(a);
        var angles = this.getAngles(a);
        a.isHidden = bonds.length == 2 && Math.abs(Math.abs(angles[1] - angles[0]) - Math.PI) < Math.PI / 30 && bonds[0].bondOrder == bonds[1].bondOrder;
        a.angleOfLeastInterference = angleBetweenLargest(angles) % (Math.PI * 2);
        a.coordinationNumber = this.getCoordinationNumber(bonds);
        a.bondNumber = bonds.length;
    };
    }
Molecule.prototype.scaleToAverageBondLength = function(length){
    var avBondLength = this.getAverageBondLength();
    if (avBondLength != 0) {
        var scale = length / avBondLength;
        for (var i = 0, ii = this.atoms.length; i < ii; i++) {
            this.atoms[i].x *= scale;
            this.atoms[i].y *= scale;
        };
            }
}
Molecule.prototype.getAverageBondLength = function(){
    if (this.bonds.length == 0) {
        return 0;
    }
    var tot = 0;
    for (var i = 0, ii = this.bonds.length; i < ii; i++) {
        tot += this.bonds[i].getLength();
    };
    tot /= this.bonds.length;
    return tot;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function Spectrum(){
    this.minX;
    this.maxX;
    this.data = [];
    this.title = null;
    this.xUnit = null;
    this.yUnit = null;
    this.continuous = true;
    var memoryOffsetLeft = 0;
    var memoryFlipXAxis = false;
    return true;
}

Spectrum.prototype.draw = function(ctx, specs, width, height){
    var offsetTop = 5;
    var offsetLeft = 0;
    var offsetBottom = 0;
    //draw decorations
    ctx.fillStyle = specs.text_color;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'alphabetic';
    ctx.font = specs.getFontString(specs.text_font_size, specs.text_font_families);
    if (this.xUnit) {
        offsetBottom += specs.text_font_size;
        ctx.fillText(this.xUnit, width / 2, height - 2);
    }
    if (this.yUnit && specs.plots_showYAxis) {
        offsetLeft += specs.text_font_size;
        ctx.save();
        ctx.translate(specs.text_font_size, height / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.fillText(this.yUnit, 0, 0);
        ctx.restore();
    }
    if (this.title != null) {
        offsetTop += specs.text_font_size;
        ctx.fillText(this.title, width / 2, specs.text_font_size);
    }
    //draw ticks
    offsetBottom += 5 + specs.text_font_size;
    if (specs.plots_showYAxis) {
        offsetLeft += 5 + ctx.measureText('0.0').width;
    }
    if (specs.plots_showGrid) {
        ctx.strokeStyle = specs.plots_gridColor;
        ctx.lineWidth = specs.plots_gridLineWidth;
        ctx.strokeRect(offsetLeft, offsetTop, width - offsetLeft, height - offsetBottom - offsetTop);
    }
    ctx.textAlign = 'center';
    ctx.textBaseline = 'top';
    var t = (this.maxX - this.minX) / 10;
    var major = .001;
    while (major < t) {
        major *= 10;
    }
    var counter = 0;
    for (var i = 0; i <= this.maxX; i += major / 2) {
        var x = this.getTransformedX(i, specs, width, offsetLeft);
        if (x > offsetLeft) {
            ctx.strokeStyle = 'black';
            ctx.lineWidth = 1;
            if (counter % 2 == 0) {
                ctx.beginPath();
                ctx.moveTo(x, height - offsetBottom);
                ctx.lineTo(x, height - offsetBottom + 2);
                ctx.stroke();
                var s = i.toFixed(5);
                while (s.charAt(s.length - 1) == '0') {
                    s = s.substring(0, s.length - 1);
                }
                if (s.charAt(s.length - 1) == '.') {
                    s = s.substring(0, s.length - 1);
                }
                ctx.fillText(s, x, height - offsetBottom + 2);
                if (specs.plots_showGrid) {
                    ctx.strokeStyle = specs.plots_gridColor;
                    ctx.lineWidth = specs.plots_gridLineWidth;
                    ctx.beginPath();
                    ctx.moveTo(x, height - offsetBottom);
                    ctx.lineTo(x, offsetTop);
                    ctx.stroke();
                }
            }
            else {
                ctx.beginPath();
                ctx.moveTo(x, height - offsetBottom);
                ctx.lineTo(x, height - offsetBottom + 2);
                ctx.stroke();
            }
        }
        counter++;
    }
    if (specs.plots_showYAxis) {
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';
        for (var i = 0; i <= 1; i += .1) {
            var y = offsetTop + (height - offsetBottom - offsetTop) * (1 - i * specs.scale);
            if (y > offsetTop) {
                ctx.strokeStyle = 'black';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(offsetLeft, y);
                ctx.lineTo(offsetLeft - 3, y);
                ctx.stroke();
                if (specs.plots_showGrid) {
                    ctx.strokeStyle = specs.plots_gridColor;
                    ctx.lineWidth = specs.plots_gridLineWidth;
                    ctx.beginPath();
                    ctx.moveTo(offsetLeft, y);
                    ctx.lineTo(width, y);
                    ctx.stroke();
                }
                ctx.fillText(i.toFixed(1), offsetLeft - 3, y);
            }
        }
    }
    //draw axes
    ctx.strokeStyle = 'black';
    ctx.lineWidth = 1;
    ctx.beginPath();
    //draw x axis
    ctx.moveTo(width, height - offsetBottom);
    ctx.lineTo(offsetLeft, height - offsetBottom);
    //draw y axis
    if (specs.plots_showYAxis) {
        ctx.lineTo(offsetLeft, offsetTop);
    }
    ctx.stroke();
    //draw plot
    ctx.strokeStyle = specs.plots_color;
    ctx.lineWidth = specs.plots_width;
    var integration = [];
    ctx.beginPath();
    if (this.continuous) {
        var started = false;
        for (var i = 0, ii = this.data.length; i < ii; i++) {
            var x = this.getTransformedX(this.data[i].x, specs, width, offsetLeft);
            if (x >= offsetLeft && x < width) {
                var y = this.getTransformedY(this.data[i].y, specs, height, offsetBottom, offsetTop);
                if (specs.plots_showIntegration) {
                    integration[integration.length] = new Point(this.data[i].x, this.data[i].y);
                }
                if (!started) {
                    ctx.moveTo(x, y);
                    started = true;
                }
                ctx.lineTo(x, y);
            }
            else 
                if (started) {
                    break;
                }
        }
    }
    else {
        for (var i = 0, ii = this.data.length; i < ii; i++) {
            var x = this.getTransformedX(this.data[i].x, specs, width, offsetLeft);
            if (x >= offsetLeft && x < width) {
                ctx.moveTo(x, height - offsetBottom);
                ctx.lineTo(x, this.getTransformedY(this.data[i].y, specs, height, offsetBottom, offsetTop));
            }
        }
    }
    ctx.stroke();
    if (specs.plots_showIntegration) {
        ctx.strokeStyle = specs.plots_integrationColor;
        ctx.lineWidth = specs.plots_integrationLineWidth;
        ctx.beginPath();
        var ascending = integration[1].x > integration[0].x;
        var max;
        if (this.flipXAxis && !ascending || !this.flipXAxis && ascending) {
            for (var i = integration.length - 2; i >= 0; i--) {
                integration[i].y = integration[i].y + integration[i + 1].y;
            }
            max = integration[0].y;
        }
        else {
            for (var i = 1, ii = integration.length; i < ii; i++) {
                integration[i].y = integration[i].y + integration[i - 1].y;
            }
            max = integration[integration.length - 1].y;
        }
        for (var i = 0, ii = integration.length; i < ii; i++) {
            var x = this.getTransformedX(integration[i].x, specs, width, offsetLeft);
            var y = this.getTransformedY(integration[i].y / max, specs, height, offsetBottom, offsetTop);
            if (i == 0) {
                ctx.moveTo(x, y);
            }
            else {
                ctx.lineTo(x, y);
            }
        }
        ctx.stroke();
    }
    memoryOffsetLeft = offsetLeft;
    memoryFlipXAxis = specs.plots_flipXAxis;
}
Spectrum.prototype.getTransformedY = function(y, specs, height, offsetBottom, offsetTop){
    return offsetTop + (height - offsetBottom - offsetTop) * (1 - y * specs.scale);
}
Spectrum.prototype.getTransformedX = function(x, specs, width, offsetLeft){
    var returning = offsetLeft + (x - this.minX) / (this.maxX - this.minX) * (width - offsetLeft);
    if (specs.plots_flipXAxis) {
        returning = width + offsetLeft - returning;
    }
    return returning;
}
Spectrum.prototype.getInverseTransformedX = function(x, width, offsetLeft){
    if (memoryFlipXAxis) {
        x = width + offsetLeft - x;
    }
    return ((x - offsetLeft) * (this.maxX - this.minX) / (width - offsetLeft)) + this.minX;
}
Spectrum.prototype.setup = function(){
    var xmin = Number.MAX_VALUE;
    var xmax = Number.MIN_VALUE;
    var ymax = Number.MIN_VALUE;
    for (var i = 0, ii = this.data.length; i < ii; i++) {
        xmin = Math.min(xmin, this.data[i].x);
        xmax = Math.max(xmax, this.data[i].x);
        ymax = Math.max(ymax, this.data[i].y);
    }
    if (this.continuous) {
        this.minX = xmin;
        this.maxX = xmax;
    }
    else {
        this.minX = xmin - 1;
        this.maxX = xmax + 1;
    }
    for (var i = 0, ii = this.data.length; i < ii; i++) {
        this.data[i].y /= ymax;
    }
}
Spectrum.prototype.zoom = function(pixel1, pixel2, width){
    var p1 = this.getInverseTransformedX(pixel1, width, memoryOffsetLeft);
    var p2 = this.getInverseTransformedX(pixel2, width, memoryOffsetLeft);
    this.minX = Math.min(p1, p2);
    this.maxX = Math.max(p1, p2);
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2789 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 20:13:50 -0400 (Thu, 12 Aug 2010) $
//

function Cube(){
    this.vertexPositionBuffer;
    this.vertexNormalBuffer;
    this.vertexColorBuffer;
    this.vertexIndexBuffer;
}

Cube.prototype.generate = function(gl){
    this.vertexPositionBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexPositionBuffer);
    vertices = [ // Front face
-1.0, -1.0, 1.0, 1.0, -1.0, 1.0, 1.0, 1.0, 1.0, -1.0, 1.0, 1.0, // Back face
 -1.0, -1.0, -1.0, -1.0, 1.0, -1.0, 1.0, 1.0, -1.0, 1.0, -1.0, -1.0, // Top face
 -1.0, 1.0, -1.0, -1.0, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0, -1.0, // Bottom face
 -1.0, -1.0, -1.0, 1.0, -1.0, -1.0, 1.0, -1.0, 1.0, -1.0, -1.0, 1.0, // Right face
 1.0, -1.0, -1.0, 1.0, 1.0, -1.0, 1.0, 1.0, 1.0, 1.0, -1.0, 1.0, // Left face
 -1.0, -1.0, -1.0, -1.0, -1.0, 1.0, -1.0, 1.0, 1.0, -1.0, 1.0, -1.0];
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertices), gl.STATIC_DRAW);
    this.vertexPositionBuffer.itemSize = 3;
    this.vertexPositionBuffer.numItems = 24;
    
    this.vertexColorBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexColorBuffer);
    var colors = [[1.0, 0.0, 0.0, 1.0], // Front face
 [1.0, 1.0, 0.0, 1.0], // Back face
 [0.0, 1.0, 0.0, 1.0], // Top face
 [1.0, 0.5, 0.5, 1.0], // Bottom face
 [1.0, 0.0, 1.0, 1.0], // Right face
 [0.0, 0.0, 1.0, 1.0] // Left face
 ];
    var unpackedColors = []
    for (var i in colors) {
        var color = colors[i];
        for (var j = 0; j < 4; j++) {
            unpackedColors = unpackedColors.concat(color);
        }
    }
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(unpackedColors), gl.STATIC_DRAW);
    this.vertexColorBuffer.itemSize = 4;
    this.vertexColorBuffer.numItems = 24;
    
    this.vertexNormalBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexNormalBuffer);
    var vertexNormals = [ // Front face
0.0, 0.0, 1.0, 0.0, 0.0, 1.0, 0.0, 0.0, 1.0, 0.0, 0.0, 1.0, // Back face
 0.0, 0.0, -1.0, 0.0, 0.0, -1.0, 0.0, 0.0, -1.0, 0.0, 0.0, -1.0, // Top face
 0.0, 1.0, 0.0, 0.0, 1.0, 0.0, 0.0, 1.0, 0.0, 0.0, 1.0, 0.0, // Bottom face
 0.0, -1.0, 0.0, 0.0, -1.0, 0.0, 0.0, -1.0, 0.0, 0.0, -1.0, 0.0, // Right face
 1.0, 0.0, 0.0, 1.0, 0.0, 0.0, 1.0, 0.0, 0.0, 1.0, 0.0, 0.0, // Left face
 -1.0, 0.0, 0.0, -1.0, 0.0, 0.0, -1.0, 0.0, 0.0, -1.0, 0.0, 0.0 ];
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertexNormals), gl.STATIC_DRAW);
    this.vertexNormalBuffer.itemSize = 3;
    this.vertexNormalBuffer.numItems = 24;
    
    this.vertexIndexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.vertexIndexBuffer);
    var cubeVertexIndices = [0, 1, 2, 0, 2, 3, // Front face
 4, 5, 6, 4, 6, 7, // Back face
 8, 9, 10, 8, 10, 11, // Top face
 12, 13, 14, 12, 14, 15, // Bottom face
 16, 17, 18, 16, 18, 19, // Right face
 20, 21, 22, 20, 22, 23 // Left face
]
    gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array(cubeVertexIndices), gl.STATIC_DRAW);
    this.vertexIndexBuffer.itemSize = 1;
    this.vertexIndexBuffer.numItems = 36;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//

function Cylinder(){
    this.vertexNormalBuffer;
    this.vertexPositionBuffer;
    this.vertexIndexBuffer;
}

Cylinder.prototype.generate = function(gl, radius, height, bands){
    var vertexPositionData = [];
    var normalData = [];
    for (var i = 0; i < bands; i++) {
        var theta = i * 2 * Math.PI / bands;
        var cosTheta = Math.cos(theta);
        var sinTheta = Math.sin(theta);
        normalData.push(cosTheta, 0, sinTheta);
        vertexPositionData.push(radius * cosTheta, 0, radius * sinTheta);
        normalData.push(cosTheta, 0, sinTheta);
        vertexPositionData.push(radius * cosTheta, height, radius * sinTheta);
    }
    normalData.push(1, 0, 0);
    vertexPositionData.push(radius, 0, 0);
    normalData.push(1, 0, 0);
    vertexPositionData.push(radius, height, 0);
    
    this.vertexNormalBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexNormalBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(normalData), gl.STATIC_DRAW);
    this.vertexNormalBuffer.itemSize = 3;
    this.vertexNormalBuffer.numItems = normalData.length / 3;
    
    this.vertexPositionBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexPositionBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertexPositionData), gl.STATIC_DRAW);
    this.vertexPositionBuffer.itemSize = 3;
    this.vertexPositionBuffer.numItems = vertexPositionData.length / 3;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2794 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-13 12:12:28 -0400 (Fri, 13 Aug 2010) $
//

function Light(diffuseColor, specularColor, direction){
    this.diffuseRGB = [parseInt(diffuseColor.substring(1, 3), 16) / 255.0, parseInt(diffuseColor.substring(3, 5), 16) / 255.0, parseInt(diffuseColor.substring(5, 7), 16) / 255.0];
    this.specularRGB = [parseInt(specularColor.substring(1, 3), 16) / 255.0, parseInt(specularColor.substring(3, 5), 16) / 255.0, parseInt(specularColor.substring(5, 7), 16) / 255.0];
    this.direction = direction;
}

Light.prototype.lightScene = function(gl){
    var prefix = 'u_light.';
    gl.uniform3f(gl.getUniformLocation(gl.program, prefix + 'diffuse_color'), this.diffuseRGB[0], this.diffuseRGB[1], this.diffuseRGB[2]);
    gl.uniform3f(gl.getUniformLocation(gl.program, prefix + 'specular_color'), this.specularRGB[0], this.specularRGB[1], this.specularRGB[2]);
    
    var lightingDirection = vec3.create(this.direction);
	vec3.normalize(lightingDirection);
	vec3.negate(lightingDirection);
    gl.uniform3f(gl.getUniformLocation(gl.program, prefix + 'direction'), lightingDirection[0], lightingDirection[1], lightingDirection[2]);
    
    // compute the half vector
    var eyeVector = [0, 0, 0]
    var halfVector = [eyeVector[0] + lightingDirection[0], eyeVector[1] + lightingDirection[1], eyeVector[2] + lightingDirection[2]];
    var length = vec3.length(halfVector);
    if (length == 0) 
        halfVector = [0, 0, 1];
    else {
		vec3.scale(1/length);
    }
    gl.uniform3f(gl.getUniformLocation(gl.program, prefix + "half_vector"), halfVector[0], halfVector[1], halfVector[2]);
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//

function Material(ambientColor, diffuseColor, specularColor, shininess){
    this.ambientColor = ambientColor;
    this.diffuseColor = diffuseColor;
    this.specularColor = specularColor;
    this.shininess = shininess;
}

Material.prototype.setup = function(gl){
    this.setTempColors(gl, this.ambientColor, this.diffuseColor, this.specularColor, this.shininess);
}
Material.prototype.setTempColors = function(gl, ambientColor, diffuseColor, specularColor, shininess){
    var prefix = 'u_material.';
    gl.uniform3f(gl.getUniformLocation(gl.program, prefix + 'ambient_color'), parseInt(ambientColor.substring(1, 3), 16) / 255.0, parseInt(ambientColor.substring(3, 5), 16) / 255.0, parseInt(ambientColor.substring(5, 7), 16) / 255.0);
    gl.uniform3f(gl.getUniformLocation(gl.program, prefix + 'diffuse_color'), parseInt(diffuseColor.substring(1, 3), 16) / 255.0, parseInt(diffuseColor.substring(3, 5), 16) / 255.0, parseInt(diffuseColor.substring(5, 7), 16) / 255.0);
    gl.uniform3f(gl.getUniformLocation(gl.program, prefix + 'specular_color'), parseInt(specularColor.substring(1, 3), 16) / 255.0, parseInt(specularColor.substring(3, 5), 16) / 255.0, parseInt(specularColor.substring(5, 7), 16) / 255.0);
    gl.uniform1f(gl.getUniformLocation(gl.program, prefix + 'shininess'), shininess);
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//

function Shader(){
    this.vertexPositionAttribute;
    this.vertexNormalAttribute;
    this.vertexColorAttribute;
}

Shader.prototype.init = function(gl){
    var vertexShader = this.getShader(gl, "vertex-shader");
    if (vertexShader == null) {
        vertexShader = this.loadDefaultVertexShader(gl);
    }
    var fragmentShader = this.getShader(gl, "fragment-shader");
    if (fragmentShader == null) {
        fragmentShader = this.loadDefaultFragmentShader(gl);
    }
    
    gl.attachShader(gl.program, vertexShader);
    gl.attachShader(gl.program, fragmentShader);
    gl.linkProgram(gl.program);
    
    if (!gl.getProgramParameter(gl.program, gl.LINK_STATUS)) {
        alert("Could not initialize shaders!");
    }
    
    gl.useProgram(gl.program);
    
    this.vertexPositionAttribute = gl.getAttribLocation(gl.program, "a_vertex_position");
    gl.enableVertexAttribArray(this.vertexPositionAttribute);
    
    this.vertexNormalAttribute = gl.getAttribLocation(gl.program, "a_vertex_normal");
    gl.enableVertexAttribArray(this.vertexNormalAttribute);
}
Shader.prototype.getShader = function(gl, id){
    var shaderScript = document.getElementById(id);
    if (!shaderScript) {
        return null;
    }
    var sb = [];
    var k = shaderScript.firstChild;
    while (k) {
        if (k.nodeType == 3) 
            sb.push(k.textContent);
        k = k.nextSibling;
    }
    var shader;
    if (shaderScript.type == "x-shader/x-fragment") {
        shader = gl.createShader(gl.FRAGMENT_SHADER);
    }
    else 
        if (shaderScript.type == "x-shader/x-vertex") {
            shader = gl.createShader(gl.VERTEX_SHADER);
        }
        else {
            return null;
        }
    gl.shaderSource(shader, sb.join(''));
    gl.compileShader(shader);
    if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
        alert(gl.getShaderInfoLog(shader));
        return null;
    }
    return shader;
}
Shader.prototype.loadDefaultVertexShader = function(gl){
    var sb = [];
    //set precision
    sb.push("#ifdef GL_ES\n");
    sb.push("precision highp float;\n");
    sb.push("#endif\n");
    //phong shader
    sb.push("struct Light");
    sb.push("{");
    sb.push("vec3 diffuse_color;");
    sb.push("vec3 specular_color;");
    sb.push("vec3 direction;");
    sb.push("vec3 half_vector;");
    sb.push("};");
    sb.push("struct Material");
    sb.push("{");
    sb.push("vec3 ambient_color;");
    sb.push("vec3 diffuse_color;");
    sb.push("vec3 specular_color;");
    sb.push("float shininess;");
    sb.push("};");
    //attributes set when rendering objects
    sb.push("attribute vec3 a_vertex_position;");
    sb.push("attribute vec3 a_vertex_normal;");
    //scene structs
    sb.push("uniform Light u_light;");
    sb.push("uniform Material u_material;");
    //matrices set by gl.setMatrixUniforms
    sb.push("uniform mat4 u_model_view_matrix;");
    sb.push("uniform mat4 u_projection_matrix;");
    sb.push("uniform mat4 u_normal_matrix;");
    //sent to the fragment shader
    sb.push("varying vec4 v_diffuse;");
    sb.push("varying vec4 v_ambient;");
    sb.push("varying vec3 v_normal;");
    sb.push("varying vec3 v_light_direction;");
    sb.push("void main(void)");
    sb.push("{");
    sb.push("v_normal = normalize((u_normal_matrix * vec4(a_vertex_normal, 1.0)).xyz);");
    
    sb.push("vec4 diffuse = vec4(u_light.diffuse_color, 1.0);");
    sb.push("v_light_direction = u_light.direction;");
    
    sb.push("v_ambient = vec4(u_material.ambient_color, 1.0);");
    sb.push("v_diffuse = vec4(u_material.diffuse_color, 1.0) * diffuse;");
    
    sb.push("gl_Position = u_projection_matrix * u_model_view_matrix * vec4(a_vertex_position, 1.0);");
    sb.push("}");
    var shader = gl.createShader(gl.VERTEX_SHADER);
    gl.shaderSource(shader, sb.join(''));
    gl.compileShader(shader);
    if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
        alert(gl.getShaderInfoLog(shader));
        return null;
    }
    return shader;
}
Shader.prototype.loadDefaultFragmentShader = function(gl){
    var sb = [];
    //set precision
    sb.push("#ifdef GL_ES\n");
    sb.push("precision highp float;\n");
    sb.push("#endif\n");
    sb.push("struct Light");
    sb.push("{");
    sb.push("vec3 diffuse_color;");
    sb.push("vec3 specular_color;");
    sb.push("vec3 direction;");
    sb.push("vec3 half_vector;");
    sb.push("};");
    sb.push("struct Material");
    sb.push("{");
    sb.push("vec3 ambient_color;");
    sb.push("vec3 diffuse_color;");
    sb.push("vec3 specular_color;");
    sb.push("float shininess;");
    sb.push("};");
    //scene structs
    sb.push("uniform Light u_light;");
    sb.push("uniform Material u_material;");
    //from the vertex shader
    sb.push("varying vec4 v_diffuse;");
    sb.push("varying vec4 v_ambient;");
    sb.push("varying vec3 v_normal;");
    sb.push("varying vec3 v_light_direction;");
    sb.push("void main(void)");
    sb.push("{");
    sb.push("float nDotL = max(dot(v_normal, v_light_direction), 0.0);");
    sb.push("vec4 color = vec4(v_diffuse.rgb*nDotL, v_diffuse.a);");
    sb.push("float nDotHV = max(dot(v_normal, u_light.half_vector), 0.0);");
    sb.push("vec4 specular = vec4(u_material.specular_color * u_light.specular_color, 1.0);");
    sb.push("color+=vec4(specular.rgb * pow(nDotHV, u_material.shininess), specular.a);");
    sb.push("gl_FragColor = color+v_ambient;");
    sb.push("}");
    var shader = gl.createShader(gl.FRAGMENT_SHADER);
    gl.shaderSource(shader, sb.join(''));
    gl.compileShader(shader);
    if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
        alert(gl.getShaderInfoLog(shader));
        return null;
    }
    return shader;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//

function Sphere(){
    this.vertexNormalBuffer;
    this.vertexPositionBuffer;
    this.vertexIndexBuffer;
}

Sphere.prototype.generate = function(gl, radius, latitudeBands, longitudeBands){
    var vertexPositionData = [];
    var normalData = [];
    for (var latNumber = 0; latNumber <= latitudeBands; latNumber++) {
        var theta = latNumber * Math.PI / latitudeBands;
        var sinTheta = Math.sin(theta);
        var cosTheta = Math.cos(theta);
        
        for (var longNumber = 0; longNumber <= longitudeBands; longNumber++) {
            var phi = longNumber * 2 * Math.PI / longitudeBands;
            var sinPhi = Math.sin(phi);
            var cosPhi = Math.cos(phi);
            
            var x = cosPhi * sinTheta;
            var y = cosTheta;
            var z = sinPhi * sinTheta;
            
            normalData.push(x);
            normalData.push(y);
            normalData.push(z);
            vertexPositionData.push(radius * x);
            vertexPositionData.push(radius * y);
            vertexPositionData.push(radius * z);
        }
    }
    
    var indexData = [];
    longitudeBands += 1;
    for (var latNumber = 0; latNumber < latitudeBands; latNumber++) {
        for (var longNumber = 0; longNumber < longitudeBands; longNumber++) {
            var first = (latNumber * longitudeBands) + (longNumber % longitudeBands);
            var second = first + longitudeBands;
            indexData.push(first);
            indexData.push(second);
            indexData.push(first + 1);
            
            if (longNumber < longitudeBands - 1) {
                indexData.push(second);
                indexData.push(second + 1);
                indexData.push(first + 1);
            }
        }
    }
    
    this.vertexNormalBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexNormalBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(normalData), gl.STATIC_DRAW);
    this.vertexNormalBuffer.itemSize = 3;
    this.vertexNormalBuffer.numItems = normalData.length / 3;
    
    this.vertexPositionBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, this.vertexPositionBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertexPositionData), gl.STATIC_DRAW);
    this.vertexPositionBuffer.itemSize = 3;
    this.vertexPositionBuffer.numItems = vertexPositionData.length / 3;
    
    this.vertexIndexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.vertexIndexBuffer);
    gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array(indexData), gl.STREAM_DRAW);
    this.vertexIndexBuffer.itemSize = 1;
    this.vertexIndexBuffer.numItems = indexData.length;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2791 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-13 07:42:06 -0400 (Fri, 13 Aug 2010) $
//
function ValidateMolecule(form) {
  if (form.q.value.match(/^$|^ +$/))
  {
    alert("You must enter a molecule value.");
    return false;
  }

  form.submit();
  return true;
}

function GetMolFromFrame(frameId, canvas){
  if (!canvas) {
    return;
  }
  var mol = document.getElementById(frameId).contentDocument.body.innerHTML;
  if (mol == "") {
    return;
  }
  if (mol.match('^ChemDoodle Web Components Query Error.|^File Error.')) {
    alert(mol);
  }
  else {
    try {
		  var mol = readMOL(mol);
		  if(mol==null||mol.atoms.length==0){
		  	alert('Invalid data found. Please input a valid MOL or SDF file.');
		  }
		  removeHydrogens(mol);
		  canvas.loadMolecule(mol);
    } catch(err) {
       alert('Invalid data found. Please input a valid MOL or SDF file.');
    }
  }
}

function Get3DMolFromFrame(frameId, canvas){
  if (!canvas) {
    return;
  }
  var mol = document.getElementById(frameId).contentDocument.body.innerHTML;
  if (mol.match('^ChemDoodle Web Components Query Error.|^File Error.')) {
    alert(mol);
  }
  else {
    var mol = readMOL(mol, 1);
    canvas.loadMolecule(mol);
  }
}

function GetPdbFromFrameDEMO(frameId, canvas){
  if (!canvas)
  {
    return;
  }
  var mol = document.getElementById(frameId).contentDocument.body.innerHTML;
  if (mol.match('^ChemDoodle Web Components Query Error.'))
  {
    alert(mol);
  }
  else
  {
    canvas.loadMolecule(readPDB(mol));
  }
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2777 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:48:33 -0400 (Thu, 12 Aug 2010) $
//
function readMOL(content, multiplier){
	var save = default_bondLength_2D;
	if(multiplier){
		default_bondLength_2D = multiplier;
	}
    var molecule = new Molecule();
    if (content == null || content.length == 0) {
        return molecule;
    }
    var currentTagTokens = content.split("\n");
    
    var counts = currentTagTokens[3];
    var numAtoms = parseInt(parseInt(counts.substring(0, 3)));
    var numBonds = parseInt(parseInt(counts.substring(3, 6)));
    
    for (var i = 0; i < numAtoms; i++) {
        var line = currentTagTokens[4 + i];
        molecule.atoms[i] = new Atom(line.substring(31, 34), parseFloat(line.substring(0, 10)) * default_bondLength_2D, -parseFloat(line.substring(10, 20)) * default_bondLength_2D, parseFloat(line.substring(20, 30)) * default_bondLength_2D);
		switch (parseInt(line.substring(36, 39))) {
			case 1:
				molecule.atoms[i].charge = 3;
				break;
			case 2:
				molecule.atoms[i].charge = 2;
				break;
			case 3:
				molecule.atoms[i].charge = 1;
				break;
			case 5:
				molecule.atoms[i].charge = -1;
				break;
			case 6:
				molecule.atoms[i].charge = -2;
				break;
			case 7:
				molecule.atoms[i].charge = -3;
				break;
			}
    }
    for (var i = 0; i < numBonds; i++) {
        var line = currentTagTokens[4 + numAtoms + i];
        var bondOrder = parseInt(line.substring(6, 9));
        var stereo = parseInt(line.substring(9, 12));
        if (bondOrder > 3) {
            switch (bondOrder) {
                case 4:
                    bondOrder = 1.5;
                    break;
                default:
                    bondOrder = 1;
                    break;
            }
        }
		var b = new Bond(molecule.atoms[parseInt(line.substring(0, 3)) - 1], molecule.atoms[parseInt(line.substring(3, 6)) - 1], bondOrder);
        switch (stereo) {
            case 3:
                b.stereo = BOND_STEREO_AMBIGUOUS;
                break;
            case 1:
                b.stereo = BOND_STEREO_PROTRUDING;
                break;
            case 6:
                b.stereo = BOND_STEREO_RECESSED;
                break;
        }
		molecule.bonds[i] = b;
    }
	if(multiplier){
		default_bondLength_2D = save;
	}
    return molecule;
}

function writeMOL(molecule){
    var content = 'Molecule from ChemDoodle Web Components\n\nhttp://www.ichemlabs.com\n';
    content = content + fit(molecule.atoms.length.toString(), 3) + fit(molecule.bonds.length.toString(), 3) + '  0  0  0  0            999 v2000\n';
    var p = molecule.getCenter();
    for (var i = 0, ii=molecule.atoms.length; i < ii; i++) {
        var a = molecule.atoms[i];
		var charge = '  0';
		if (a.charge != 0) {
			switch (a.charge) {
				case 3:
					charge = '  1';
					break;
				case 2:
					charge = '  2';
					break;
				case 1:
					charge = '  3';
					break;
				case -1:
					charge = '  5';
					break;
				case -2:
					charge = '  6';
					break;
				case -3:
					charge = '  7';
					break;
			}
		}
        content = content + fit(((a.x - p.x) / default_bondLength_2D).toFixed(4), 10) + fit((-(a.y - p.y) / default_bondLength_2D).toFixed(4), 10) + fit((a.z / default_bondLength_2D).toFixed(4), 10) + ' ' + fit(a.label, 3) + ' 0'+charge+'  0  0  0  0\n';
    };
    for (var i = 0, ii=molecule.bonds.length; i < ii; i++) {
        var b = molecule.bonds[i];
		var stereo = 0;
		if(b.stereo==BOND_STEREO_AMBIGUOUS){
			stereo = 3;
		}else if(b.stereo==BOND_STEREO_PROTRUDING){
			stereo = 1;
		}else if(b.stereo==BOND_STEREO_RECESSED){
			stereo = 6;
		}
        content = content + fit((indexOf(molecule.atoms, b.a1) + 1).toString(), 3) + fit((indexOf(molecule.atoms, b.a2) + 1).toString(), 3) + fit(b.bondOrder.toString(), 3) + '  '+stereo+'     0  0\n';
    };
    content = content + 'M  END';
    return content;
}

function fit(data, length){
    var size = data.length;
    var padding = '';
    for (var i = 0; i < length - size; i++) {
        padding = padding + ' ';
    };
    return padding + data;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
function readPDB(content, multiplier){
    var molecule = new Molecule();
	if(content==null || content.length==0){
		return molecule;
	}
    var currentTagTokens = content.split("\n");
    var pointsPerAngstrom = getPointsPerAngstrom();
	if(multiplier){
		pointsPerAngstrom = multiplier;
	}
    for (var i = 0, ii=currentTagTokens.length; i < ii; i++) {
        var line = currentTagTokens[i];
		if(line.indexOf("ATOM")==0 || line.indexOf("HETATM")==0){
			molecule.atoms[molecule.atoms.length] = new Atom(line.substring(76,78), parseFloat(line.substring(30, 38))*pointsPerAngstrom, -parseFloat(line.substring(46,54))*pointsPerAngstrom, parseFloat(line.substring(38, 46)*pointsPerAngstrom));
		}
    }
	if (multiplier) {
		deduceCovalentBonds(molecule, multiplier);
	}
	else {
		deduceCovalentBonds(molecule);
	}
    return molecule;
}
//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//
var SQZ_HASH = {
	'@': 0,
	'A': 1,
	'B': 2,
	'C': 3,
	'D': 4,
	'E': 5,
	'F': 6,
	'G': 7,
	'H': 8,
	'I': 9,
	'a': -1,
	'b': -2,
	'c': -3,
	'd': -4,
	'e': -5,
	'f': -6,
	'g': -7,
	'h': -8,
	'i': -9
};
var DIF_HASH = {
	'%': 0,
	'J': 1,
	'K': 2,
	'L': 3,
	'M': 4,
	'N': 5,
	'O': 6,
	'P': 7,
	'Q': 8,
	'R': 9,
	'j': -1,
	'k': -2,
	'l': -3,
	'm': -4,
	'n': -5,
	'o': -6,
	'p': -7,
	'q': -8,
	'r': -9
};
var DUP_HASH = {
	'S': 1,
	'T': 2,
	'U': 3,
	'V': 4,
	'W': 5,
	'X': 6,
	'Y': 7,
	'Z': 8,
	's': 9
};

function readJCAMP(content){
	this.isBreak = function(c){
		return SQZ_HASH[c]!=null||DIF_HASH[c]!=null||DUP_HASH[c]!=null||c==' '||c=='-'||c=='+';
	}
	this.getValue = function(decipher, lastDif){
		var first = decipher.charAt(0);
		var rest = decipher.substring(1);
		if(SQZ_HASH[first]!=null){
			return parseFloat(SQZ_HASH[first] + rest);
		}else if(DIF_HASH[first]!=null){
			return parseFloat(DIF_HASH[first] + rest)+lastDif;
		}
		return parseFloat(rest);
	}
	var spectrum = new Spectrum();
	if (content == null || content.length == 0) {
        return spectrum;
    }
    var lines = content.split('\n');
	var sb = [];
	var xLast, xFirst, yFirst, nPoints, xFactor=1, yFactor=1;
	for(var i = 0, ii=lines.length; i<ii; i++){
		var use = $.trim(lines[i]);
		if(use.indexOf('$$')!=-1){
			use = use.substring(0, use.indexOf('$$'));
		}
		if(sb.length==0 || !lines[i].startsWith('##')){
			if(sb.length!=0){
				sb.push('\n');
			}
			sb.push($.trim(use));
		}else{
			var currentRecord = sb.join('');
			sb = [use];
			if(currentRecord.startsWith("##TITLE=")){
				spectrum.title = $.trim(currentRecord.substring(8));
			}else if(currentRecord.startsWith("##XUNITS=")){
				spectrum.xUnit = $.trim(currentRecord.substring(9));
			}else if(currentRecord.startsWith("##YUNITS=")){
				spectrum.yUnit = $.trim(currentRecord.substring(9));
			}else if(currentRecord.startsWith("##XYPAIRS=")){
				//spectrum.yUnit = $.trim(currentRecord.substring(9));
			}else if(currentRecord.startsWith("##FIRSTX=")){
				xFirst = parseFloat($.trim(currentRecord.substring(9)));
			}else if(currentRecord.startsWith("##LASTX=")){
				xLast = parseFloat($.trim(currentRecord.substring(8)));
			}else if(currentRecord.startsWith("##FIRSTY=")){
				yFirst = parseFloat($.trim(currentRecord.substring(9)));
			}else if(currentRecord.startsWith("##NPOINTS=")){
				nPoints = parseFloat($.trim(currentRecord.substring(10)));
			}else if(currentRecord.startsWith("##XFACTOR=")){
				xFactor = parseFloat($.trim(currentRecord.substring(10)));
			}else if(currentRecord.startsWith("##YFACTOR=")){
				yFactor = parseFloat($.trim(currentRecord.substring(10)));
			}else if(currentRecord.startsWith("##XYDATA=")){
				var innerLines = currentRecord.split('\n');
				var abscissaSpacing = (xLast - xFirst) / (nPoints - 1);
				var lastX = xFirst-abscissaSpacing;
				var lastY = yFirst;
				var lastDif = 0;
				var lastOrdinate;
				for(var j = 1, jj=innerLines.length; j<jj; j++){
					var data = [];
					var read = $.trim(innerLines[j]);
					var sb = [];
					for(var k = 0, kk=read.length; k<kk; k++){
						if(this.isBreak(read.charAt(k))){
							if (sb.length>0 && !(sb.length==1&&sb[0]==' ')) {
								data[data.length] = sb.join('');
							}
							sb=[read.charAt(k)];
						}else{
							sb.push(read.charAt(k));
						}
					}
					data[data.length] = sb.join('');
					for(var k = 1, kk=data.length; k<kk; k++){
						var decipher = data[k];
						if(DUP_HASH[decipher.charAt(0)]!=null){
							var dup = parseInt(DUP_HASH[decipher.charAt(0)] + decipher.substring(1))-1;
							for(var l = 0; l<dup; l++){
								lastX += abscissaSpacing;
								lastDif = this.getValue(lastOrdinate, lastDif);
								lastY = lastDif * yFactor;
								spectrum.data[spectrum.data.length-1] = new Point(lastX, lastY);
							}
						}else{
							lastOrdinate = decipher;
							lastX+=abscissaSpacing;
							lastDif = this.getValue(decipher, lastDif);
							lastY=lastDif*yFactor;
							spectrum.data[spectrum.data.length] = new Point(lastX, lastY);
						}
					}
				}
			}else if(currentRecord.startsWith("##PEAK TABLE=")){
				spectrum.continuous = false;
				var innerLines = currentRecord.split('\n');
				for(var j = 1, jj=innerLines.length; j<jj; j++){
					var items = innerLines[j].split(',');
					spectrum.data[spectrum.data.length] = new Point(parseInt($.trim(items[0])), parseInt($.trim(items[1])));
				}
			}
		}
	}
	spectrum.setup();
    return spectrum;
}

//
//  Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2777 $
//  $Author: kevin $
//  $LastChangedDate: 2010-08-12 16:48:33 -0400 (Thu, 12 Aug 2010) $
//
function angleBetweenLargest(angles){
    if (angles.length == 0) {
        return 0;
    }
    if (angles.length == 1) {
        return angles[0] + Math.PI;
    }
    var largest = 0;
    var angle = 0;
    var index = -1;
    for (var i = 0, ii=angles.length - 1; i < ii; i++) {
        var dif = angles[i + 1] - angles[i];
        if (dif > largest) {
            largest = dif;
            angle = (angles[i + 1] + angles[i]) / 2;
            index = i;
        }
    }
    var last = angles[0] + Math.PI * 2 - angles[angles.length - 1];
    if (last > largest) {
        angle = angles[0] - last / 2;
        largest = last;
        if (angle < 0) {
            angle += Math.PI * 2;
        }
        index = angles.length - 1;
    }
    return angle;
}

function isBetween(x, left, right){
    return x >= left && x <= right;
}

function getRGB(hex, multiplier){
    return [parseInt(hex.substring(1, 3), 16) / 255.0 * multiplier, parseInt(hex.substring(3, 5), 16) / 255.0 * multiplier, parseInt(hex.substring(5, 7), 16) / 255.0 * multiplier];
}
//
//  Copyright 2006-2010 iChemLabs, LLC.  All rights reserved.
//
//  $Revision: 2786 $
//  $Author: jat $
//  $LastChangedDate: 2010-08-12 19:50:43 -0400 (Thu, 12 Aug 2010) $
//

function supports_canvas() {
  return !!document.createElement('canvas').getContext;
}

function supports_canvas_text() {
  if (!supports_canvas()) { return false; }
  var dummy_canvas = document.createElement('canvas');
  var context = dummy_canvas.getContext('2d');
  return typeof context.fillText == 'function';
}

function alertBrowserIncompatibility(){
	if (!supports_canvas_text()) {
		if ($.browser.msie && $.browser.version >= '6') {
			good = true;
			alert('ChemDoodle Web Components require Google Chrome Frame to run in Internet Explorer. Please install Google Chrome Frame and then restart your browser.\n\nhttp://code.google.com/chrome/chromeframe/');
		}
		if (!good) {
			alert('ChemDoodle Web Components are best viewed in the following browsers with minimum versions listed. Please use one of the following or update your browser for the best experience.\n\nGoogle Chrome 2+ (Windows)\nApple Safari 4+ (Windows, Mac)\nMozilla Firefox 3.5+ (Windows, Mac, Linux)\nOpera 10.5+ (Windows, Mac, Linux)\nInternet Explorer 6+ (Windows)');
		}
	}
}


/* 
 * Javascript to support product filtering
 *
 * @author David Wafula
 *
 *
 */


jQuery(function() {
    // Things to do on loading the page.
    jQuery(document).ready(function() {
        
        });
    
    
    jQuery("#form_productfilter").submit(function(e) {
        jQuery("#searchProductButton").attr("disabled", "disabled");
        jQuery("#save_results").html(please_wait);//'<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
        
    });
    
});
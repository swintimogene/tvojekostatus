jQuery(document).ready(function() {
    "use strict";
    jQuery("#woo_instagram_limit_images").keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });
    jQuery("#woo_instagram_limit_images").keyup(function() {
        var value = jQuery(this).val();
        value = value.replace(/^(0*)/, "");
        jQuery(this).val(value);
    });
});

jQuery(function(){
    jQuery('#the-list').on('click', '.editinline', function(){
    
        /**
         * Extract metadata and put it as the value for the custom field form
         */
        inlineEditPost.revert();
    
        var post_id = jQuery(this).closest('tr').attr('id');
    
        post_id = post_id.replace("post-", "");
    
        var $cfd_inline_data = jQuery('#custom_field_demo_inline_' + post_id),
            $wc_inline_data = jQuery('#woocommerce_inline_' + post_id );
    
        jQuery('input[name="_custom_field"]', '.inline-edit-row').val($cfd_inline_data.find("#_custom_field_demo").text());
    
        /**
         * Only show custom field for appropriate types of products (simple)
         */
        var product_type = $wc_inline_data.find('.product_type').text();
        
        

        if (product_type =='simple' || product_type=='variable') {
            jQuery('.custom_field_demo', '.inline-edit-row').show();
        } else {
            jQuery('.custom_field_demo', '.inline-edit-row').css("display", "none");
            jQuery('.onoff-bulk-discoun-br', '.inline-edit-row').css("display", "none");
        }
    
    });
});
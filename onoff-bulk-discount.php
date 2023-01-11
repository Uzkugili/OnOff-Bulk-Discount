<?php
/**
 * Plugin Name:       OnOff Bulk Discount
 * Plugin URI:        https://onoff.si/
 * Description:       Handle the basics with this plugin.
 * Version:           1.0.0
 * Author:            Urban Puhek
 * Author URI:        https://www.linkedin.com/in/urban-puhek-2227261b7/
 */


 add_action( 'woocommerce_product_quick_edit_start', 'bbloomer_show_custom_field_quick_edit' );
 
 function bbloomer_show_custom_field_quick_edit() {
	global $post;
	?>
	<label>
	   <span class="title">Custom field</span>
	   <span class="input-text-wrap">
		  <input type="text" name="_custom_field" class="text" value="<?php esc_html( get_post_meta( $post->ID, '_custom_field', true ) );?>">
	   </span>
	</label>
	<br class="clear" />
	<?php
 }
  
 add_action( 'manage_product_posts_custom_column', 'bbloomer_show_custom_field_quick_edit_data', 9999, 2 );
  
 function bbloomer_show_custom_field_quick_edit_data( $column, $post_id ){
	 if ( 'name' !== $column ) return;
	 echo '<div>Custom field: <span id="cf_' . $post_id . '">' . esc_html( get_post_meta( $post_id, '_custom_field', true ) ) . '</span></div>';
	wc_enqueue_js( "
	   $('#the-list').on('click', '.editinline', function() {
		  var post_id = $(this).closest('tr').attr('id');
		  post_id = post_id.replace('post-', '');
		  var custom_field = $('#cf_' + post_id).text();
		  $('input[name=\'_custom_field\']', '.inline-edit-row').val(custom_field);
		 });
	" );
 }
  
 add_action( 'woocommerce_product_quick_edit_save', 'bbloomer_save_custom_field_quick_edit' );
  
 function bbloomer_save_custom_field_quick_edit( $product ) {
	 $post_id = $product->get_id();
	 if ( isset( $_REQUEST['_custom_field'] ) ) {
		 $custom_field = $_REQUEST['_custom_field'];
		 update_post_meta( $post_id, '_custom_field', wc_clean( $custom_field ) );
	 }
 }
 
<?php
/**
 * Plugin Name:       OnOff - Bulk Discount
 * Plugin URI:        https://onoff.si/
 * Description:       Handle the basics with this plugin.
 * Version:           1.0.0
 * Author:            Urban Puhek
 * Author URI:        https://www.linkedin.com/in/urban-puhek-2227261b7/
 */

 add_action( 'woocommerce_product_quick_edit_start', function(){
	global $post;
	/*?>
	<label>
	   <span class="title">Custom field</span>
	   <span class="input-text-wrap">
		  <input id="onoff-discount-input" type="text" name="_custom_field" class="text" value="<?php echo esc_html( get_post_meta( $post->ID, '_custom_field', true ) );?>">
	   </span>
	</label>
	<br class="clear" />
	<?php*/

	woocommerce_wp_text_input( 
		array( 
			'id'          => '_custom_field', 
			'label'       => __( 'Custom field', 'woocommerce' ),
			'value'       => get_post_meta( $post->ID, '_custom_field', true ),		
		)
	);
 });
  
 add_action( 'manage_product_posts_custom_column', function ( $column, $post_id ){
	 if ( 'name' !== $column ) return;
	 /*echo '<div>Custom field: <span id="cf_' . $post_id . '">' . esc_html( get_post_meta( $post_id, '_custom_field', true ) ) . '</span></div>';
	wc_enqueue_js( "
	   $('#the-list').on('click', '.editinline', function() {
		  var post_id = $(this).closest('tr').attr('id');
		  post_id = post_id.replace('post-', '');
		  var custom_field = $('#cf_' + post_id).text();
		  $('input[name=\'_custom_field\']', '.inline-edit-row').val(custom_field);
		 });
	" );*/
 });
  
 add_action( 'woocommerce_product_quick_edit_save', function ( $product ) {
	 $product_id = $product->get_id();

	 if ( isset( $_REQUEST['_custom_field'] ) ) {
		 $custom_field = $_REQUEST['_custom_field'];
		 update_post_meta( $product_id, '_custom_field', esc_attr( $custom_field) );

		 if($product->get_type() == 'simple'){
			$current_product_price = $product->get_regular_price();
			$new_price = ((100-$custom_field)*$current_product_price)/100;
			$product->set_sale_price($new_price);
			$product->save();
		}else if($product->get_type() == 'variable'){
	
		}
	 }
 });
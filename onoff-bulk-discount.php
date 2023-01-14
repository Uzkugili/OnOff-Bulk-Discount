<?php
/**
 * Plugin Name:       OnOff - Bulk Discount
 * Plugin URI:        https://onoff.si/
 * Description:       This plugin adds % discount field to Quick edit and Bulk edit for SIMPLE and VARIABLE products.
 * Version:           1.0.0
 * Author:            Urban Puhek
 * Author URI:        https://www.linkedin.com/in/urban-puhek-2227261b7/
 */

add_action('admin_enqueue_scripts', function (){
	wp_enqueue_script('main-onoff-bulk-discount-js', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js' , array('jquery'), '1.0');
});

//Add Action for Bulk Edit
add_action( 'woocommerce_product_bulk_edit_start', 'onoff_bulk_discount_field_input' );

//Add action for Quick Edit
add_action( 'woocommerce_product_quick_edit_start', 'onoff_bulk_discount_field_input');


function onoff_bulk_discount_field_input(){
	global $post;
	?>
	<label class="custom_field_demo">
		<span class="title">Custom field</span>
		<span class="input-text-wrap">
			<input type="text" name="_custom_field" class="text" value="">
		</span>
	</label>
	<br class="clear onoff-bulk-discoun-br" />
	<?php
}

add_action( 'woocommerce_product_quick_edit_save', 'onoff_bulk_discount_field_save' , 10, 1);

function onoff_bulk_discount_field_save( $product ) {
	$product_id = $product->get_id();

	if ( isset( $_REQUEST['_custom_field'] ) ) {
		$custom_field = $_REQUEST['_custom_field'];
		update_post_meta( $product_id, '_custom_field', wc_clean( $custom_field) );

		if($product->is_type('simple')){
			$current_product_price = $product->get_regular_price();
			$new_price = ((100-$custom_field)*$current_product_price)/100;
			$product->set_sale_price($new_price);
			$product->save();
		}else if($product->is_type('variable')){
		$variations = $product->get_children();
		foreach($variations as $variation){
			$varible_product = wc_get_product($variation);
			$current_varible_product = $varible_product->get_regular_price();
			$new_variable_price = ((100-$custom_field)*$current_varible_product)/100;
			$varible_product->set_sale_price($new_variable_price);
			$varible_product->save();
		}
		}
	}
}
  
 add_action( 'manage_product_posts_custom_column', function ( $column, $post_id ){
	 if ( 'name' !== $column ) return;

	?>
	<div class="hidden custom_field_demo_inline" id="custom_field_demo_inline_<?php echo $post_id; ?>">
		<div id="_custom_field_demo"><?php echo get_post_meta($post_id,'_custom_field',true); ?></div>
 	</div>

	<?php
 }, 99, 2);
  
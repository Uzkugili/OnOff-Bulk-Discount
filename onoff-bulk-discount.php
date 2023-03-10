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
	?>
	<label class="onoff-bulk-discoun">
	<span class="title">Discount (%)</span>
		<span class="input-text-wrap">
			<input type="text" name="_discount_field" class="text" value="">
		</span>
	</label>
	<br class="clear onoff-bulk-discoun-br" />
	<?php
}

//Add Action for Bulk Edit
add_action( 'woocommerce_product_bulk_edit_save', 'onoff_bulk_discount_field_save', 10, 1 );

//Add action for Quick Edit
//add_action( 'woocommerce_product_quick_edit_save', 'onoff_bulk_discount_field_save' , 10, 2);

function onoff_bulk_discount_field_save( $product) {
	//echo '<script>alert("saved");</script>';
	//wp_die(var_dump($_REQUEST['_discount_field'] ));
	$product_id = $product->get_id();

	if($_REQUEST['_discount_field'] != NULL){
		//wp_die($_REQUEST['_discount_field']);
		//if ( isset( $_REQUEST['_discount_field'] ) ) {
		$discount_field = $_REQUEST['_discount_field'];
		if($discount_field <= 100 && $discount_field >= 0){
			update_post_meta( $product_id, '_discount_field', wc_clean( $discount_field) );
		}else{
			wp_die('Discount can not be higher than 100% or smaller than 0%!');
		}
		//}
	}else{
		//wp_die('imamo error');
		$discount_field = 0;
		update_post_meta( $product_id, '_discount_field', wc_clean( $discount_field) );
	}

	if($product->is_type('simple')){
		//wp_die($product->get_regular_price());
		if($product->get_regular_price() != NULL){
			$current_product_price = $product->get_regular_price();
			$new_price = ((100-$discount_field)*$current_product_price)/100;
			$product->set_sale_price($new_price);
			$product->save();
		}
	}else if($product->is_type('variable')){
		$variations = $product->get_children();
		foreach($variations as $variation){
			$varible_product = wc_get_product($variation);
			if($varible_product->get_regular_price() != NULL){
				$current_varible_product = $varible_product->get_regular_price();
				$new_variable_price = ((100-$discount_field)*$current_varible_product)/100;
				$varible_product->set_sale_price($new_variable_price);
				$varible_product->save();
			}
		}
	}
}
  
add_action( 'manage_product_posts_custom_column', function ( $column, $post_id ){
	if ( 'name' !== $column ) return;

	?>
	<div class="hidden onoff-bulk-discoun-inline" id="onoff-bulk-discoun-inline_<?php echo $post_id; ?>">
		<div id="onoff-bulk-discoun"><?php echo get_post_meta($post_id,'_discount_field',true); ?></div>
	</div>

<?php
}, 99, 2);
  

add_action( 'woocommerce_product_object_updated_props', 'onoff_bulk_discount_sale_updated', 1, 2);

function onoff_bulk_discount_sale_updated($product, $updated_props){
	//echo '<script>alert("updated");</script>';
	//wp_die($product->get_sale_price());
	wp_die(var_dump($updated_props));
	if(in_array('sale_price', $updated_props, true)){
		//wp_die('sale price');
		//echo '<script>alert("updated");</script>';

		$product_id = $product->get_id();
		//wp_die($product_id);
		$product_sale_price = $product->get_sale_price();
		//wp_die($product_sale_price);
		if($product_sale_price == NULL || $product_sale_price == 0){
			//wp_die('tukaj sem');
			//wp_die(get_post_meta($product_id,'_discount_field',true));
			update_post_meta( $product_id, '_discount_field', 0 );
			//wp_die(get_post_meta($product_id,'_discount_field',true));
		}else{
			$discount_field = get_post_meta($product_id, '_discount_field', true);
			$product_regular_price = $product->get_regular_price();
			$discount_field_price = ((100-$discount_field)*$product_regular_price)/100;

			if($product_sale_price != $discount_field_price){
				$new_discount_field = 100 - ((100*$product_sale_price)/$product_regular_price);
				//wp_die($new_discount_field);
				update_post_meta( $product_id, '_discount_field', wc_clean($new_discount_field));
				//wp_die(get_post_meta($product_id,'_discount_field',true));
			}
		}
	}
}

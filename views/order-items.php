<?php 
	$order = new WC_Order( $_GET['order'] );
	echo get_option('pr_form_message');
 ?>
<form method="POST" action="">
	<p class="form-row" data-priority="10">
		<label for="acc_number" class=""><?php _e('Bank account number','pr'); ?> <abbr class="required" title="pole wymagane">*</abbr></label>
		<input id="acc_number" type="text" class="input-text" name="acc_number" required="">
	</p>
	<p class="form-row" data-priority="10">
		<label for="return_message" class=""><?php _e('Additional message','pr'); ?> <abbr class="required" title="pole wymagane">*</abbr></label>
		<textarea id="return_message" class="pr-textarea input-text" name="return_message" required=""></textarea>
		
	</p>
	<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
				<th class="woocommerce-orders-table__header">
					<span class="nobr">&nbsp;</span>
				</th>
				<th class="woocommerce-orders-table__header">
					<span class="nobr"><?php _e('Product name','pr'); ?></span>
				</th>
				<th class="woocommerce-orders-table__header">
					<span class="nobr"><?php _e('Price','pr'); ?></span>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($order->get_items() as $key => $order_item) { ?>
			<?php $product = new WC_Product($order_item['product_id']); ?>
			<tr class="woocommerce-orders-table__row order">
				<td><input type="checkbox" name="items[]" value="<?php echo $product->get_id(); ?>"></td>
				<td><?php echo $product->get_name(); ?></td>
				<td><?php echo $product->get_price_html(); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<button type="submit" name="return_pdf"><?php _e('Generate document','pr'); ?></button>
</form>
<?php
/*
Plugin Name: Product Return PDF
Plugin URI: #
Description: This plugin utilizes MPDF 6.1 to generate simple document for product returns
Version: 1.0
Author: FWP
Author URI: #
Text Domain: pr
*/

if( !defined('ABSPATH') ) {
	exit();
}

define( 'PR_DIR', plugin_dir_path( __FILE__ ) );
define( 'PR_URI', plugin_dir_url( __FILE__ ) );

function pr_template($slug,$name=''){
	if(!empty($name)){
		$name = '-'.$name;
	}
	if ( $overridden_template = locate_template( 'woocommerce/'.$slug.$name.'.php',false,false ) ) {
		$load = $overridden_template;
	} else {
		$load = PR_DIR . 'views/'.$slug.$name.'.php';
	}
	if( file_exists($load) ) {
		load_template( $load, false );
	} else {
		echo 'Template not found '. $load;
	}
}

function pr_get_items(){
	$items = array();
	if( isset( $_POST['return_pdf'] )  ) {
		if( isset( $_POST['items'] ) && !empty( $_POST['items'] ) ) {
			$items = $_POST['items'];
		}
	}
	return $items;
}

function pr_pdf_content(){
	global $pr_return;
	
	$message = get_option('pr_message','');
	$items = pr_get_items();
	if( !empty($items) ) {
		$itemsHtml = '<table class="pdf-table" border="1" border-col>';
		$itemsHtml .= '<tr>';
			$itemsHtml .= '<th>'. __('Name','pr') .'</th>';
			$itemsHtml .= '<th>'. __('Price','pr') .'</th>';
		$itemsHtml .= '</tr>';
		foreach ($items as $key => $item) {
			$product = new WC_Product($item);
			$itemsHtml .= '<tr>';
				$itemsHtml .= '<td>'.$product->get_name().'</td>';
				$itemsHtml .= '<td>'.$product->get_price_html().'</td>';
			$itemsHtml .= '</tr>';
		}
		$itemsHtml .= '</table>';
	}
	$message = str_replace('[items]', $itemsHtml, $message);
	$message = str_replace('[acc_number]', $_POST['acc_number'], $message);
	$message = str_replace('[user_message]', $_POST['return_message'], $message);
	return $message;
}

include_once PR_DIR . 'classes/class-settings.php';
include_once PR_DIR . 'classes/class-return.php';



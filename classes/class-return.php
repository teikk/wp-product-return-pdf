<?php 

class PR_Return {
	public $tags = array();
	function __construct() {
		$this->tags = array(
			'items' => __('shows list of returned products','pr'),
			'acc_number' => __('shows client bank account number','pr'),
			'user_message' => __('shows user message','pr'),
		);
		add_action('wp',array($this, 'pdf_file'),30);
		add_filter( 'woocommerce_locate_template', array($this,'locate_template'), 20, 3 );
		add_filter( 'woocommerce_my_account_my_orders_actions', array($this,'add_action'), 20, 2 );
		add_action( 'pr/message/help', array($this,'message_tags'), 10 );
	}

	public function pdf_file(){
		if( isset($_POST['return_pdf']) ) {
			require_once( PR_DIR . 'includes/mpdf2/vendor/autoload.php' );
			$config = array(
				'mode' => '',
				'format' => get_option('pr_pdf_size','A5'),
				'default_font_size' => 11,
				'default_font' => '',
				'margin_left' => 5,
				'margin_right' => 5,
				'margin_top' => 0,
				'margin_bottom' => 0,
				'margin_header' => 2,
				'margin_footer' => 2,
				'orientation' => 'L'
				);
			extract($config);
			$mpdf = new mPDF(
				$mode,
				$format,
				$default_font_size,
				$default_font,
				$margin_left,
				$margin_right,
				$margin_top,
				$margin_bottom,
				$margin_header,
				$margin_footer,
				$orientation
				);
			ob_start();
			pr_template('pdf-content');
			$html = ob_get_clean();
			$header = get_option('pr_pdf_header');
			$footer = get_option('pr_pdf_footer');
			$mpdf->SetHTMLHeader($header);
			$mpdf->SetHTMLFooter($footer);
			$mpdf->WriteHTML($html);
			$mpdf->Output();
			exit();
		}
	}

	public function locate_template($template, $name, $template_path) {
		if( $name == 'myaccount/orders.php' && isset($_GET['return']) ) {
			global $wp_query;
			if( isset($_GET['order']) && !empty($_GET['order']) ) {
				$load = PR_DIR . 'views/order-items.php';
				if( file_exists($load) ) {
					$template = $load;
				}		
			}
		}		
		return $template;
	}

	public function add_action($actions, $order) {
		global $woocommerce;
		if( $order->get_status() == 'completed' ) {
			$orders_endpoint = $woocommerce->query->query_vars['orders'];
			$link_args = array(
				'return' => true,
				'order' => $order->get_id()
				);
			$link = add_query_arg($link_args, wc_get_endpoint_url( $orders_endpoint ));
			$actions['return'] = array(
					'url' => $link,
					'name' => __('Return','pr'),
				);
		}
		return $actions;
	}

	public function message_tags(){
		foreach ($this->tags as $tag => $msg) {
			echo '<p><kbd>['.$tag.']</kbd> - '.$msg.'</p>';
		}
	}
}

$pr_return = new PR_Return;
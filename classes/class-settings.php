<?php 
class PR_Settings {
	function __construct(){
		add_action( 'admin_menu', array($this,'add_options_page') );
		add_action( 'admin_init', array($this,'register_settings') );

		add_filter( 'option_pr_message',array($this,'get_option'), 10, 1 );
		add_filter( 'option_pr_form_message',array($this,'get_option'), 10, 1 );
		add_filter( 'option_pr_pdf_header',array($this,'get_option'), 10, 1 );
		add_filter( 'option_pr_pdf_footer',array($this,'get_option'), 10, 1 );

		add_filter( 'pre_update_option_pr_message',array($this,'sanitize_wysiwyg'), 10, 1 );
		add_filter( 'pre_update_option_pr_form_message',array($this,'sanitize_wysiwyg'), 10, 1 );
		add_filter( 'pre_update_option_pr_pdf_header',array($this,'sanitize_wysiwyg'), 10, 1 );
		add_filter( 'pre_update_option_pr_pdf_footer',array($this,'sanitize_wysiwyg'), 10, 1 );
	}

	public function sanitize_wysiwyg($value){
		return esc_html( $value );
	}

	public function add_options_page(){
		add_options_page(
			__('Returns Settings','fwp'),
			__('Returns Settings','fwp'),
			'manage_options',
			'pr-settings',
			array($this,'options_page')
		);
	}

	public function options_page(){
		echo '<div class="wrap">';
		echo '<h1>'.__('Returns Settings','fwp').'</h1>';
		echo '<form method="POST" action="options.php">';
		settings_fields( 'pr-options' );
		do_settings_sections( 'pr-settings' );
		submit_button();
		echo '</form>';
		echo '</div>';
	}

	public function register_settings(){
		add_settings_section(
			'pr_message_section',
			__('Text in the PDF file','fwp'),
			'',
			'pr-settings'
		);
		register_setting( 'pr-options', 'pr_form_message' );
	 	add_settings_field(
			'pr_form_message',
			__('Text above return form','fwp'),
			array($this,'form_message_field'),
			'pr-settings',
			'pr_message_section',
			array(
				'label_for' => 'pr_form_message'
				)
		);

		register_setting( 'pr-options', 'pr_message' );				
	 	add_settings_field(
			'pr_message',
			__('PDF Content','fwp'),
			array($this,'message_field'),
			'pr-settings',
			'pr_message_section',
			array(
				'label_for' => 'pr_message'
				)
		);

	 	register_setting( 'pr-options', 'pr_pdf_header' );
	 	register_setting( 'pr-options', 'pr_pdf_footer' );
	 	add_settings_field(
			'pr_pdf_settings',
			__('Ustawienia nagłówka i stopki','fwp'),
			array($this,'pdf_settings'),
			'pr-settings',
			'pr_message_section',
			array(
				'label_for' => 'pr_pdf_settings'
				)
		);
	 	register_setting( 'pr-options', 'pr_pdf_size' );
 	 	add_settings_field(
 			'pr_pdf_size',
 			__('PDF Size','fwp'),
 			array($this,'pdf_size'),
 			'pr-settings',
 			'pr_message_section',
 			array(
 				'label_for' => 'pr_pdf_size'
 				)
 		);
	}

	public function sanitize($value) {
		return htmlspecialchars($value);
	}

	public function get_option($value) {
		return html_entity_decode($value);
	}

	public function form_message_field( $data ){
		$option = get_option('pr_form_message');
		wp_editor( $option, 'pr_form_message', array(
			'wpautop' => false
			) );
	}

	public function message_field( $data ){
		$option = get_option('pr_message');
		do_action('pr/message/help');
		wp_editor( $option, 'pr_message', array(
			'wpautop' => false
			) );
	}

	public function pdf_settings( $data ){
		$header = get_option('pr_pdf_header');
		$footer = get_option('pr_pdf_footer');
		echo '<h4>'.__('PDF Header','pr').'</h4>';
		echo '<h5>'.__('Use <code>{PAGENO}</code> to insert page number','pr').'</h5>';
		wp_editor( $header, 'pr_pdf_header', array(
			'wpautop' => false
			) );

		echo '<h4>'.__('PDF Footer','pr').'</h4>';
		echo '<h5>'.__('Use <code>{PAGENO}</code> to insert page number','pr').'</h5>';
		wp_editor( $footer, 'pr_pdf_footer', array(
			'wpautop' => false
			) );
	}

	public function pdf_size( $data ){
		$option = get_option('pr_pdf_size','A4');
		$sizes = array(
			'A6'=>'A6',
			'A5'=>'A5',
			'A5-L'=>'A5 Letter',
			'A4'=>'A4',
			'A4-L'=>'A4 Letter',
			'A3'=>'A3',
			);
		/**
		 * Key must fulfill MPDF size name i.e 'A4-L'
		 * @see https://mpdf.github.io/reference/mpdf-functions/mpdf.html
		 */
		$sizes = apply_filters( 'pr/pdf/sizes', $sizes );
		echo '<select name="pr_pdf_size">';
		foreach ($sizes as $key => $size) {
			$selected = ($option == $key) ? selected : '';
			echo '<option value="'.$key.'"'.$selected.'>'.$size.'</option>';
		}
		echo '</select>';
	}
}

new PR_Settings;
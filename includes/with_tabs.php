<?php
class WOP_Page_With_Tabs extends WP_Options_Page {

	private static $instance = null;

	public static function instance () {
		if ( ! self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	public function __construct () {
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'wp_head', [ $this, 'with_tabs_use_admin_styles' ] ); 
		
	}

	public function init () {
		$this->id = 'wop_with_tabs';
		$this->menu_title = \esc_html__('Theme Control', 'tsw-options-page');
		$this->menu_position = 61;

		// declare the "page_tabs" feature
		$this->supports['page_tabs'] = [
			// declare my tabs
			'branding' => \esc_html__('Branding', 'tsw-options-page'),
			'styles' => \esc_html__('Styles', 'tsw-options-page'),
			'help' => \esc_html__('Help', 'tsw-options-page'),
		];
        // see addons/rich_text_field.php
		//$this->supports[] = 'rich_text_field';
		//$this->supports[] = 'code_editor_field';


		parent::init();
	}

	public function get_fields () {
		$prvlnk = wp_kses_post('<a href="'. \site_url().'" target="_blank">'. esc_html__('Home (new tab)', 'tsw-options-page') .'</a>');
		return [
			[
				'title' => 'Branding, Colors/Logo',
				'description' => 'Change colors and add logo.',
				'type' => 'title',
				'tab' => 'branding', // set the field tab
			],
			[
				'id' => 'text_1',
				'title' => 'Public E-mail',
				'type' => 'text',
				'tab' => 'branding', // set the field tab
			],
			[
				'id' => 'color_1',
				'title' => 'Content Links Color',
				'type' => 'color',
				'tab' => 'branding', // set the field tab
			],
			[
				'id' => 'code_editor_page_css',
				'title' => 'Additional CSS',
				'type' => 'code_editor',
				'description' => esc_html__('Mobile-ready view will change when you save settings ', 'tsw-options-page') . $prvlnk,
				'tab' => 'styles',
                
			],
			[
				'id' => 'preview_1',
				'title' => 'Theme Styles',
				'type' => 'iframe',
				'scr' => \site_url(),
				'tab' => 'styles',
			],
			[
				'title' => 'Support & Instructions',
				'description' => 'General Help for this theme.',
				'type' => 'title',
				'tab' => 'help', // set the field tab
			],
           
		];
	}

	/** #A1
	 * Put scripts in the head.
	 * @since 1.0.0
	 * @param wp_unslash   Remove slashes from a string or array of strings.
	 */
	
	public function with_tabs_use_admin_styles()
	{ 
		
		$css_new = \get_option('wop_with_tabs_options')['code_editor_page_css'];
		$css_clr = \get_option('wop_with_tabs_options')['color_1'];

		$css_toget = ( empty( $css_new  ) ) ? '' : $css_new;
		$clr_toget = ( empty( $css_clr  ) ) ? '' : '.inner_content a{color:'. esc_attr($css_clr).';}';
		// 1 = use these styles. 0 = do not use.
		$opt_styles = '1';
		$output     = '';
		if( $opt_styles == 1 ) {
			$output .= '<style type="text/css" id="thememod-styles">';
		if( $opt_styles == "1" ) : 
			$output .= wp_unslash( $css_toget . $clr_toget );
		endif;
			$output .= '</style> ';
		} 
		
		print( $output );

	}
}

// start your class
WOP_Page_With_Tabs::instance();
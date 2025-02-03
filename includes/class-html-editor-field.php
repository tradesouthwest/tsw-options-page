<?php
class WOP_Addon_Html_Editor_Field {

	/**
	 * Code editor settings.
	 *
	 * @see wp_enqueue_code_editor()
	 * @since 4.9.0
	 * @var array|false
	 */
	public $editor_settings = array();
	const FEATURE = 'code_editor_field';
	const FIELD_TYPE = 'code_editor';
	private static $instance = null;

	public static function instance () {
		if ( ! self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	public function __construct () {
		add_action( 'wp_options_page_init', [ $this, 'setup' ] );
add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * @param \WP_Options_Page $page
	 * @return void
	 */
	public function setup ( $page ) {
		if ( ! $page->supports( self::FEATURE ) ) return;

		// hooks
       
		$page->add_action( 'render_field_' . self::FIELD_TYPE, [ $this, 'render_field' ], 10, 2 );
		$page->add_action( 'prepare_field_' . self::FIELD_TYPE, [ $this, 'prepare_field' ], 10, 2 );
	}

	/**
	 * @param array $field
	 * @param \WP_Options_Page $page
	 * @return void
	 */
	public function render_field ( $field, $page ) {
		$value = $page->get_field_value( $field );
		$name = $field['name'];
		$desc = $field['description'];
		$args = $field['editor_settings'] ?? [];

		$args['textarea_rows'] = $args['textarea_rows'] ?? 5;
		$args['wpautop'] = $args['wpautop'] ?? true;

		apply_filters( 'wp_code_editor_settings', $value, $name, $args );

		$page->do_action( 'after_field_input', $field, $this );

		if ( $desc ) : ?>
		<p class="description"><?php echo $desc ?></p>
		<?php endif;
    }

	public function prepare_field ( $field, $page ) {
		$field['@sanitize'] = 'wp_kses_post';
		return $field;
	}


	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 4.9.0
	 */
	public function enqueue() {
		$this->editor_settings = wp_enqueue_code_editor(
			array_merge(
				array(
					'type'       => '',
					'codemirror' => array(
						'indentUnit' => 2,
						'tabSize'    => 2,
					),
				),
				$this->editor_settings
			)
		);
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 * @since 4.9.0
	 */
	public function render_content() {}

	/**
	 * Render a JS template for control display.
	 *
	 * @since 4.9.0
	 */
	public function content_template() {
		?>
		<# var elementIdPrefix = 'el' + String( Math.random() ); #>
		<# if ( data.label ) { #>
			<label for="{{ elementIdPrefix }}_editor" class="customize-control-title">
				{{ data.label }}
			</label>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<div class="customize-control-notifications-container"></div>
		<textarea id="{{ elementIdPrefix }}_editor"
			<# _.each( _.extend( { 'class': 'code' }, data.input_attrs ), function( value, key ) { #>
				{{{ key }}}="{{ value }}"
			<# }); #>
			></textarea>
		<?php
	}
}
WOP_Addon_Html_Editor_Field::instance();
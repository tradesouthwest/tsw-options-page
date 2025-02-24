<?php

class WOP_Addon_Tabs {

	const FEATURE = 'page_tabs';
	private static $instance = null;

	public static function instance () {
		if ( ! self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	public function __construct () {
		add_action( 'wp_options_page_init', [ $this, 'setup' ] );
	}

	public function setup ( $page ) {
		// check this feature
		if ( ! $page->supports( self::FEATURE ) ) return;

		$tabs = $page->apply_filters( self::FEATURE, $page->supports[ self::FEATURE ] ?? [] );
		if ( count( $tabs ) > 0 ) {
			// set the firt tab as default tab
			$tabs['__default'] = \array_keys( $tabs )[0];
		}
		$page->supports[ self::FEATURE ] = $tabs;

		// hooks
		$page->add_action( 'before_render_form', [ $this, 'render_tabs' ], 5 );
		$page->add_filter( 'get_fields', [ $this, 'get_fields_by_tab' ], 10, 2 );

		// disable page title
		$page->insert_title = false;
	}

	public function render_tabs ( $page ) {
		$current = $this->get_current_tab( $page );
		$tabs = $page->supports[ self::FEATURE ];
		unset( $tabs['__default'] );
		?>

		<nav class="nav-tab-wrapper" style="margin: 1.5rem 0;">

		<?php foreach ( $tabs as $tab_id => $tab_name ) : ?>
			<a href="<?php echo esc_attr( \add_query_arg( 'tab', $tab_id, $page->get_url() ) ); ?>" class="nav-tab <?php echo $current === $tab_id ? 'nav-tab-active' : '' ?>"><?php echo $tab_name; ?></a>
		<?php endforeach; ?>

		</nav>

		<?php
	}

	public function get_fields_by_tab ( $fields, $page ) {
		$result = [];
		$current = $this->get_current_tab( $page );

		foreach ( $fields as $field ) {
			$field_tab = $field['tab'] ?? '';
			if ( $current === $field_tab ) {
				$result[] = $field;
			}
		}

		return $result;
	}

	protected function get_current_tab ( $page ) {
		return $_REQUEST['tab'] ?? $page->supports[ self::FEATURE ]['__default'];
	}
}

WOP_Addon_Tabs::instance();
<?php

/**
 * Creates a standalone replica of the WordPress Customizer's Additional CSS editor.
 * Saves theme mods as wp options.
 */

class Customizer_CSS_Editor {

    private $option_name = 'custom_css_options'; // Option name to store CSS

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_init', array( $this, 'save_custom_css' ) );
    }

    public function add_menu_page() {
        add_submenu_page(
            'themes.php', // Parent page (can be changed)
            'Custom CSS Editor', // Page title
            'Custom CSS', // Menu title
            'manage_options', // Capability required
            'custom-css-editor', // Menu slug
            array( $this, 'render_editor_page' )
        );
    }

    public function render_editor_page() {
        $custom_css = get_option( $this->option_name, '' );
        ?>
        <div class="wrap">
            <h1>Custom CSS Editor</h1>
            <form method="post">
                <textarea name="custom_css" rows="20" cols="80" style="width: 95%;"><?php echo esc_textarea( $custom_css ); ?></textarea>
                <?php submit_button( 'Save Custom CSS' ); ?>
                <?php wp_nonce_field( 'custom_css_nonce' ); ?>
            </form>

            <h2>Preview (Styling may not be exact in the editor)</h2>
            <div id="custom-css-preview" style="border: 1px solid #ddd; padding: 10px; margin-top: 20px;">
               <p>This is a preview area.  Changes will be reflected after saving and refreshing the front end.</p>
            </div>

        </div>

        <style>
            #custom-css-preview {
                /* Basic preview styling - customize as needed */
                background-color: #f9f9f9; /* Example background color */
            }
            /* Add any other necessary styling */
        </style>
        <?php
    }

    public function save_custom_css() {
        if ( isset( $_POST['submit'] ) && isset( $_POST['custom_css'] ) && wp_verify_nonce( $_POST['custom_css_nonce'], 'custom_css_nonce' ) ) {
            $custom_css = wp_kses_post( $_POST['custom_css'] ); // Sanitize CSS
            update_option( $this->option_name, $custom_css );

            // Optional: Add a notice to confirm save
            add_action( 'admin_notices', function() {
                echo '<div class="updated"><p>Custom CSS saved.</p></div>';
            } );
        }
    }


    // Function to output the saved CSS in the header
    public function output_custom_css() {
        $custom_css = get_option( $this->option_name, '' );
        if ( !empty( $custom_css ) ) {
            echo '<style type="text/css">' . $custom_css . '</style>';
        }
    }
}

// Initialize the class
$custom_css_editor = new Customizer_CSS_Editor();

// Add action to output the CSS in the header (important!)
add_action( 'wp_head', array( $custom_css_editor, 'output_custom_css' ) );

?>
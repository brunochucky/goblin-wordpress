<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.goblincompute.com/
 * @since      1.0.0
 *
 * @package    Goblin
 * @subpackage Goblin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Goblin
 * @subpackage Goblin/public
 * @author     Goblin <bsousaf@gmail.com>
 */
class Goblin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Goblin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Goblin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/goblin-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Goblin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Goblin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/goblin-public.js', array( 'jquery' ), $this->version, false );

	}


	public function activate() {
		set_transient( 'vpt_flush', 1, 60 );
	}


	public function rewrite() {
		add_rewrite_rule( '^goblin-secret/([^/]*)/?', 'index.php?goblin=$matches[1]', 'top' );

		if(get_transient( 'vpt_flush' )) {
			delete_transient( 'vpt_flush' );
			flush_rewrite_rules();
		}
	}

	public function query_vars($vars) {
		$vars[] = 'goblin';

		return $vars;
	}

	public function change_template( $template ) {

		if( get_query_var( 'goblin', false ) !== false ) {
			//Check plugin directory next
			$newTemplate = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/template-embed.php';
			if( file_exists( $newTemplate ) )
				return $newTemplate;
		}


		//Fall back to original template
		return $template;

	}

	public function filter_the_content_in_the_main_loop( $content ) {
		$options = get_option( 'goblin_settings' ); 
		$hasquery = get_query_var( 'goblin', false ) === false;
		
		if( $hasquery && $options['goblin_enabled'] == true && (is_page() || is_single()) ) {
	 		$options = get_option( 'goblin_settings' );
	 		$newcontent = '<div class="goblin-widget" data-token="'.$options['goblin_token'].'" data-path="'.get_the_ID().'/"></div>';
			$newcontent .= '<script async src="https://cdn.goblincompute.com/v0/widget.js"></script>';
		 
		    return $newcontent;
		}
		elseif( (is_archive() || is_front_page() || is_home() || is_search()) && $hasquery) {
			$excerpt = $content;
			$excerpt = mb_strimwidth(wp_strip_all_tags($excerpt), 0, 50, '[...]');
			$excerpt = '<p>'.$excerpt.'</p>';

			return $excerpt;
		}
		else {
			return $content;
		}
	}

}

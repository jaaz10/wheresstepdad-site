<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       sonaar.io
 * @since      1.0.0
 *
 * @package    Sonaar_Music
 * @subpackage Sonaar_Music/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sonaar_Music
 * @subpackage Sonaar_Music/includes
 * @author     Edouard Duplessis <eduplessis@gmail.com>
 */


class Sonaar_Music {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sonaar_Music_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->version = SRMP3_VERSION;
		$this->plugin_name = 'sonaar-music';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sonaar_Music_Loader. Orchestrates the hooks of the plugin.
	 * - Sonaar_Music_i18n. Defines internationalization functionality.
	 * - Sonaar_Music_Admin. Defines all hooks for the admin area.
	 * - Sonaar_Music_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sonaar-music-elementor.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sonaar-music-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sonaar-music-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sonaar-music-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sonaar-music-review.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sonaar-music-public.php';

		/* Add RSS importer */
		if ( is_admin() ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sonaar-podcast-rss-import.php';
		}

		$this->loader = new Sonaar_Music_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sonaar_Music_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sonaar_Music_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		
		$plugin_admin = new Sonaar_Music_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action('shortcode_button_load', $plugin_admin, 'initCPT', 9998);
		$this->loader->add_action( 'elementor/editor/before_enqueue_scripts', $plugin_admin, 'editor_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'init_options' );
		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'init_postField' );
		$this->loader->add_action( 'widgets_init', $plugin_admin, 'srmp3_create_postType' );
		$this->loader->add_action( 'init', $plugin_admin, 'srmp3_add_shortcode' );
		$this->loader->add_action( 'init', $plugin_admin, 'checkAlbumVersion' );
		$this->loader->add_action( 'widgets_init', $plugin_admin, 'register_widget' );
		$this->loader->add_action( 'shortcode_button_load', $plugin_admin, 'init_my_shortcode_button', 9999 );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'srp_add_go_pro_submenu', 9999);
		$this->loader->add_action('manage_sr_playlist_posts_custom_column', $plugin_admin , 'manage_album_custom_column', 10, 2);
		$this->loader->add_filter('manage_sr_playlist_posts_columns', $plugin_admin , 'manage_album_columns');
		$this->loader->add_filter( 'submenu_file', $plugin_admin, 'yourprefix_remove_submenus' );
		//add_filter( 'submenu_file', Sonaar_Music_Admin::yourprefix_remove_submenus() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin ,'prefix_admin_scripts'  );	
	}



	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Sonaar_Music_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'elementor/frontend/before_enqueue_scripts', $plugin_public, 'editor_enqueue_scripts' );
		
		if (get_template()== 'onair2'){
			$this->loader->add_filter( 'body_class', $plugin_public , 'srbodyclass' );	
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sonaar_Music_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	public static function get_option($id, $option_name = null, $default = null){
		
		$option_name = ( !empty( $option_name ) && get_option($option_name) != false ) ? get_option($option_name) : get_option('iron_music_player');
		
		if ($id == 'allOptions') {
			$srmp3_settings_general = Sonaar_Music::convertSomeOptionValue(( is_array( get_option('srmp3_settings_general') ) ) ?  get_option('srmp3_settings_general') : array());
			$srmp3_settings_widget_player = Sonaar_Music::convertSomeOptionValue(( is_array( get_option('srmp3_settings_widget_player') ) ) ?  get_option('srmp3_settings_widget_player') : array());
			$srmp3_settings_sticky_player = Sonaar_Music::convertSomeOptionValue(( is_array( get_option('srmp3_settings_sticky_player') ) ) ?  get_option('srmp3_settings_sticky_player') : array());
			$srmp3_settings_woocommerce = Sonaar_Music::convertSomeOptionValue(( is_array( get_option('srmp3_settings_woocommerce') ) ) ?  get_option('srmp3_settings_woocommerce') : array());
			$srmp3_settings_popup = Sonaar_Music::convertSomeOptionValue(( is_array( get_option('srmp3_settings_popup') ) ) ?  get_option('srmp3_settings_popup') : array());

			$result = array_merge($srmp3_settings_general, $srmp3_settings_widget_player, $srmp3_settings_sticky_player, $srmp3_settings_woocommerce, $srmp3_settings_popup);

			return ( is_array( $result ) )? $result : array();
		}

		$value = ( ( is_array( $option_name ) && array_key_exists( $id,  $option_name ) ) )? $option_name[$id] : $default;

		return $value;
		
	}

	/*Do some modifications in the get option return*/
	public static function convertSomeOptionValue($value){
		/*covert URL sting from the 'sr_prevent_continuous_url' option To post ID array */
		if( is_array( $value ) && array_key_exists( 'sr_prevent_continuous_url',  $value ) ){
			$pageListToAvoid = explode(PHP_EOL, $value['sr_prevent_continuous_url']);
			$idListToAvoid = [];
			foreach ( $pageListToAvoid as &$url) {
				if( substr($url, -1) == '*' || substr($url, -2, 1) == '*' ){ // We can add a "*" to the url  to avoid all children post
					if( url_to_postid( str_replace('*', '', $url) ) === 0){ //If the page url doesnt load a post (with post ID), that mean it is probably a archive page. 
						$urlBroken = explode('/', $url);
						$slugNameFromUrl =  $urlBroken[ count($urlBroken) - 2] ;
						foreach ( get_post_types(array(), 'objects') as &$postType) {
							if( isset( $postType->rewrite) && isset( $postType->rewrite['slug'] ) ){ 
								if( $postType->rewrite['slug'] == $slugNameFromUrl){
									$posts = get_posts([
										'fields'    => 'ids', // Only get post IDs
										'post_type' => $postType->name,
										'post_status' => 'publish',
										'numberposts' => -1
									  ]);
									$idListToAvoid =  array_merge( $posts, $idListToAvoid ); 
									break;  
								}
							}
						}
					}else{
						$newIds = array_keys(get_children(url_to_postid( mb_substr($url, 0, -1) )));
						$idListToAvoid =  array_merge( $newIds, $idListToAvoid );
					}
				}else{
					array_push( $idListToAvoid, url_to_postid($url));
				}
			}
			if( $idListToAvoid == null){
				$value['sr_prevent_continuous_url'] = [];
			}else{
				$value['sr_prevent_continuous_url'] = array_map(function($idInt) { return strval($idInt); }, $idListToAvoid);
			}
		}
		return $value;
	}



	/**
	 * Check if tracks are found in the post
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function srmp3_check_if_audio($post = null, $wooclass = false) {
		
		if($wooclass){
			global $product;
			if ( ! $post ) {
				$post = $product;
			}
			if ( is_numeric( $post ) ) {
				$post = wc_get_product( $post );
			}
			$album_tracks = get_post_meta( $post->get_id(), 'alb_tracklist', true );
		} else {
			$album_tracks = get_post_meta( $post->ID, 'alb_tracklist', true );
		}
		
		$trackSet = false;

		if(!is_array($album_tracks)){
			return false;
		}

		$fileOrStream =  $album_tracks[0]['FileOrStream'];
		
		switch ($fileOrStream) {
			case 'mp3':
				if ( isset( $album_tracks[0]["track_mp3"] ) ) {
					$trackSet = true;
				}
				break;

			case 'stream':
				if ( isset( $album_tracks[0]["stream_link"] ) ) {
					$trackSet = true;
				}
				break;
		}

		if (!$trackSet)
		return false;

        return true; 
	}

	public static function array_insert ( $array, $pairs, $key, $position = 'after' ){
		$key_pos = array_search( $key, array_keys($array) );

		if ( 'after' == $position )
			$key_pos++;

		if ( false !== $key_pos ) {
			$result = array_slice( $array, 0, $key_pos );
			$result = array_merge( $result, $pairs );
			$result = array_merge( $result, array_slice( $array, $key_pos ) );
		}
		else {
			$result = array_merge( $array, $pairs );
		}

		return $result;
	}

}

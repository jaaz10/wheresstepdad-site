<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Controls_Media;
use Elementor\Group_Control_Base;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;

use Sonaar_Music_Admin;
use Sonaar_Music;
/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */

class SR_Audio_Player extends Widget_Base {

	public function get_name() {
		return 'music-player';
	}

	public function get_title() {
		return esc_html__( 'MP3 Audio Player', 'sonaar-music' );
	}

	public function get_icon() {
		return 'sricons-logo sonaar-badge';
	}

	public function get_help_url() {
		return 'https://support.sonaar.io';
	}

	public function get_categories() {
		return [ 'elementor-sonaar' ];
	}

	public function get_defaultLayout() {
		return Sonaar_Music::get_option('player_widget_type', 'srmp3_settings_general') ;
	}
	public function get_srmp3_option_label($option_id, $option_tab){
		if( 'true' === Sonaar_Music::get_option( $option_id, $option_tab ) ){
			return esc_html__( 'Use global setting (Yes)', 'sonaar-music' );
		}else{
			return esc_html__( 'Use global setting (No)', 'sonaar-music' );
		}
	}
	public function srp_promo_message(){
		return '<div class="sr_gopro elementor-nerd-box sonaar-gopro">' .
		'<i class="sricons-logo" aria-hidden="true"></i>
			<div class="elementor-nerd-box-title">' .
				__( 'Business plan is required', 'sonaar-music' ) .
			'</div>
			<div class="elementor-nerd-box-message">' .
				__( 'This feature starts with the Business Plan which includes:', 'sonaar-music' ) .
			'</div>
			<ul>
				<li><i class="eicon-check"></i>Search Bar Widget</li>
				<li><i class="eicon-check"></i>Filter Dropdown Widget</li>
				<li><i class="eicon-check"></i>Chips & Tags Widget</li>
				<li><i class="eicon-check"></i>Tracklist Custom Fields</li>
				<li><i class="eicon-check"></i>Tracklist Pagination</li>
				<li><i class="eicon-check"></i>Support for ACF, JetEngine, etc</li>
				<li><i class="eicon-check"></i>Full WooCommerce support</li>
			</ul>
			<a class="elementor-nerd-box-link elementor-button elementor-button-default elementor-go-pro" href="https://sonaar.io/mp3-audio-player-pro/pricing/?utm_source=Sonaar+Music+Free+Plugin&utm_medium=plugin" target="_blank">' .
			__( 'Upgrade your plan', 'elementor' ) .
			'</a>
		</div>';
	}
	public function get_keywords() {
		return [ 'mp3', 'player', 'audio', 'sonaar', 'podcast', 'music', 'beat', 'sermon', 'episode', 'radio' ,'stream', 'sonar', 'sonnar', 'sonnaar', 'music player', 'podcast player'];
	}

	public function get_script_depends() {
		return [ 'elementor-sonaar' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' 							=> esc_html__( 'Audio Player Settings', 'sonaar-music' ),
				'tab'   							=> Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'album_img',
			[
				'label' => esc_html__( 'Image Cover (Optional)', 'sonaar-music' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'dynamic' => [ 'active' => true,],
				'separator' => 'after',
				'conditions'                    => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'player_layout', 
							'operator' => '==',
							'value' => 'skin_float_tracklist'
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_boxed_tracklist'
								],
								[
									'name' => 'playlist_show_soundwave', 
									'operator' => '!=',
									'value' => 'yes'
								]
							]
						]
					]
				]
			]
		);
		$this->add_control(
				'playlist_source',
				[
					'label'					=> esc_html__( 'Source', 'sonaar-music' ),
					'type' 					=> Controls_Manager::SELECT,
					'label_block'			=> true,
					'options' 				=> [
						'from_cpt' 			=> 'Selected Post(s)',
						'from_cat'			=> 'All Posts',
						'from_elementor' 	=> 'This Widget',
						'from_current_post' => 'Current Post',
					],
					'default' 				=> 'from_cpt',
				]
		);
		$this->add_control(
			'playlist_list',
				[
					'label' => sprintf( esc_html__( 'Select %1$s Post(s)', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('playlist')) ),
					'label_block' => true,
					'description' => sprintf( __('To create new %1$s %2$s Leave blank if you want to display your latest published %1$s', 'sonaar-music'), Sonaar_Music_Admin::sr_GetString('playlist'), __('<a href="' . esc_url(get_admin_url( null, 'post-new.php?post_type=' . SR_PLAYLIST_CPT )) . '" target="_blank">click here</a><br>','sonaar-music')),
					'type' 							=> \Elementor\Controls_Manager::SELECT2,
					'multiple' 						=> true,
					'options'               		=> sr_plugin_elementor_select_playlist(),   
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '==',
					            'value' => 'from_cpt'
					        ],
					    ]
					]   
				]
		);
		if ( !function_exists( 'run_sonaar_music_pro' ) ){
			$this->add_control(
				'playlist_list_cat_srpro',
					[
						'label'                 		=> esc_html__( 'All Posts', 'sonaar-music' ),
						'label_block'					=> true,
						'classes' 						=> 'sr-pro-only',
						'type' 							=> \Elementor\Controls_Manager::SELECT2,
						'multiple' 						=> true,
						'options'               		=> srp_elementor_select_category(),   
						'conditions' 					=> [
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'playlist_source',
									'operator' => '==',
									'value' => 'from_cat'
								],
							]
						]   
					]
			);
		}
		if ( function_exists( 'run_sonaar_music_pro' ) ){
				$this->add_control(
					'audio_meta_field',
					[
						'label' 						=> esc_html__( 'Audio Source Metakey ID', 'sonaar-music' ),
						'description' => esc_html__( 'Leave blank to fetch your current post tracklist. For dynamic custom fields, enter your custom meta field key or ID.', 'sonaar-music' ),
						'type' 							=> Controls_Manager::TEXT,
						'dynamic' 						=> [
							'active' 					=> true,
						],
						'default' 						=> '',
						'label_block' 					=> true,
						'conditions' 					=> [
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'playlist_source',
									'operator' => '==',
									'value' => 'from_current_post'
								],
							]
						]   
					]
				);
				$this->add_control(
					'repeater_meta_field',
					[
						'label' 						=> esc_html__( 'Repeater Group Metakey ID', 'sonaar-music' ),
						'description' => esc_html__( 'Leave blank if you are not using dynamic repeater or enter metakey ID for your Repeater Group', 'sonaar-music' ),
						'type' 							=> Controls_Manager::TEXT,
						'dynamic' 						=> [
							'active' 					=> true,
						],
						'default' 						=> '',
						'label_block' 					=> true,
						'conditions' 					=> [
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'playlist_source',
									'operator' => '==',
									'value' => 'from_current_post'
								],
							]
						]   
					]
			);
			$this->add_control(
				'playlist_list_cat',
					[
						'label'                 		=> esc_html__( 'From specific category(s)', 'sonaar-music' ),
						'label_block'					=> true,
						'type' 							=> \Elementor\Controls_Manager::SELECT2,
						'multiple' 						=> true,
						'options'               		=> srp_elementor_select_category(),   
						'conditions' 					=> [
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'playlist_source',
									'operator' => '==',
									'value' => 'from_cat'
								],
							]
						]   
					]
			);
			$this->add_control(
				'show_cat_description',
				[
					'label' 						=> esc_html__( 'Display category description', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0',
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '==',
					            'value' => 'from_cat'
					        ],
					    ]
					]
				]
			);
			$this->add_control(
				'posts_per_page',
				[
					'label' => esc_html__( 'Max number of posts to load', 'sonaar-music' ),
					'description' => esc_html__( 'Leave blank for all posts', 'sonaar-music' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 10000,
					'step' => 1,
					'default' => 99,
					'conditions'                    => [
						'relation' => 'or',
							'terms' => [
								[
									'name' => 'playlist_source', 
									'operator' => '==',
									'value' => 'from_cat'
								]
							]
					]
				]
			);
		}
		$this->add_control(
			'playlist_title', [
				'label' => esc_html__( 'Playlist Title', 'sonaar-music' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [ 'active' => true,],
				'conditions' 					=> [
				    'relation' => 'and',
				    'terms' => [
				        [
				            'name' => 'playlist_source',
				            'operator' => '==',
				            'value' => 'from_elementor'
				        ],
				    ]
				] 
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'feed_source',
			[
				'label' => esc_html__( 'Source', 'sonaar-music' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'media_file',
				'options' => [
					'media_file' => esc_html__( 'Media File', 'sonaar-music' ),
					'external_url' => esc_html__( 'External URL', 'sonaar-music' ),
				],
				'frontend_available' => true,
			]
		);
		$repeater->add_control(
			'feed_source_external_url',
			[
				'label' => esc_html__( 'External URL', 'sonaar-music' ),
				'type' => Controls_Manager::URL,
				'condition' => [
					'feed_source' => 'external_url',
				],
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter your URL', 'sonaar-music' ),
				'frontend_available' => true,
			]
		);
		$repeater->add_control(
			'feed_source_file',
			[
				'label' => esc_html__( 'Upload MP3 File', 'sonaar-music' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'audio',
				'frontend_available' => true,
				'condition' => [
					'feed_source' => 'media_file',
				],
			]
		);
		$repeater->add_control(
			'feed_track_title', [
				'label' => sprintf( esc_html__( '%1$s Title', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [ 'active' => true,],
				'condition' => [
					'feed_source' => 'external_url',
				],
			]
		);
		$repeater->add_control(
			'feed_track_img',
			[
				'label' => sprintf( esc_html__( '%1$s Cover (Optional)', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'description' => sprintf( esc_html__(  'Setting a %1$s cover image will override the main cover image. Recommended: JPG file 500x500px', 'sonaar-music' ), Sonaar_Music_Admin::sr_GetString('track') ),
				'dynamic' => [ 'active' => true,],
			]
		);

		$this->add_control(
			'feed_repeater',
			[
				'label' => sprintf( esc_html__( 'Add %1$s(s)', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ feed_source_file["url"] || feed_source_external_url["url"] }}}',
				'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'play_current_id',
					            'operator' => '==',
					            'value' => ''
					        ],
					        [
					            'name' => 'playlist_source',
					            'operator' => '==',
					            'value' => 'from_elementor'
					        ],
					    ]
				] 
			]
		);
		$this->add_control(
			'hr_storelinks',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
				'conditions' 					=> [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'playlist_source',
							'operator' => '==',
							'value' => 'from_elementor'
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout',
											'operator' => '!=',
											'value' => 'skin_button'
										],
										[	
											'name' => 'playlist_show_album_market',
											'operator' => '==',
											'value' => 'yes'
										]
									]
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout',
											'operator' => '==',
											'value' => 'skin_button'
										],
										[	
											'name' => 'playlist_show_album_market_skin_button',
											'operator' => '==',
											'value' => 'yes'
										]
									]
								],
							]
						],
					]
				]
			]
		);
		$store_repeater = new \Elementor\Repeater();
		$store_repeater->add_control(
			'store_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);
		$store_repeater->add_control(
			'store_name', [
				'label' => esc_html__( 'Link Title', 'sonaar-music' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [ 'active' => true,],
			]
		);
		$store_repeater->add_control(
			'store_link', [
				'label' => esc_html__( 'Link URL', 'sonaar-music' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [ 'active' => true,],
			]
		);

		$this->add_control(
			'storelist_repeater',
			[
				'label' => esc_html__( 'External Link Buttons', 'sonaar-music' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'prevent_empty' => false,
				'fields' => $store_repeater->get_controls(),
				'title_field' => '{{{ store_name || store_link["url"] }}}',
				'conditions' 					=> [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'playlist_source',
							'operator' => '==',
							'value' => 'from_elementor'
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout',
											'operator' => '!=',
											'value' => 'skin_button'
										],
										[	
											'name' => 'playlist_show_album_market',
											'operator' => '==',
											'value' => 'yes'
										]
									]
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout',
											'operator' => '==',
											'value' => 'skin_button'
										],
										[	
											'name' => 'playlist_show_album_market_skin_button',
											'operator' => '==',
											'value' => 'yes'
										]
									]
								],
							]
						],
					]
				]
			]
		);
		
		$this->add_control(
			'hr_2',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'player_layout',
			[
				'label'					=> esc_html__( 'Player Design Layout', 'sonaar-music' ),
				'type' 					=> Controls_Manager::SELECT,
				'label_block'			=> true,
				'options' 				=> [
					'skin_float_tracklist'         =>  esc_html__('Floated', 'sonaar-music'),
					'skin_boxed_tracklist'    =>  esc_html__('Boxed', 'sonaar-music'),
					'skin_button'    =>  esc_html__('Button', 'sonaar-music'),
				],
				'default' 				=> 'skin_float_tracklist',
			]
		);
		$this->add_control(
			'player_layout_hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		if ( function_exists( 'run_sonaar_music_pro' ) ){
			$this->add_control(
				'enable_sticky_player',
				[
					'label' 						=> esc_html__( 'Sticky Audio Player', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '1', 
				]
			);
			$this->add_control(
				'enable_shuffle',
				[
					'label' 						=> esc_html__( 'Enable Shuffle', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '', 
				]
			);
			$this->add_control(
				'no_track_skip',
				[
					'label' => sprintf( esc_html__( 'Stop when  %1$s ends', 'sonaar-music' ), Sonaar_Music_Admin::sr_GetString('track') ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> 'yes',
					'default' 						=> '', 
				]
			);
			$this->add_control(
				'no_loop_tracklist',
				[
					'label' => sprintf( esc_html__( 'Do not loop %1$s list', 'sonaar-music' ), Sonaar_Music_Admin::sr_GetString('track') ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> 'yes',
					'default' 						=> '', 
				]
			);
		}
		$this->add_control(
			'playlist_show_playlist',
			[
				'label' 							=> esc_html__( 'Show Tracklist', 'sonaar-music' ),
				'type' 								=> \Elementor\Controls_Manager::SWITCHER,
				'separator'							=> 'before',
				'label_on' 							=> esc_html__( 'Show', 'sonaar-music' ),
				'label_off' 						=> esc_html__( 'Hide', 'sonaar-music' ),
				'return_value' 						=> 'yes',
				'default' 							=> 'yes',
				'condition' => [
					'player_layout!' 	=> 'skin_button'
				],
			]
		);
		$this->add_control(
			'playlist_show_playlist_skin_button',
			[
				'label' 							=> esc_html__( 'Show Tracklist', 'sonaar-music' ),
				'type' 								=> \Elementor\Controls_Manager::SWITCHER,
				'separator'							=> 'before',
				'label_on' 							=> esc_html__( 'Show', 'sonaar-music' ),
				'label_off' 						=> esc_html__( 'Hide', 'sonaar-music' ),
				'return_value' 						=> 'yes',
				'default' 							=> '',
				'condition' => [
					'player_layout' 	=> 'skin_button'
				],
			]
		);
		if ( function_exists( 'run_sonaar_music_pro' ) ){
			$this->add_control(
				'reverse_tracklist',
				[
					'label' 							=> esc_html__( 'Reverse Tracklist', 'sonaar-music' ),
					'type' 								=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 							=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 						=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 						=> 'yes',
					'default' 							=> '',
					'condition' 					=> [
						'playlist_source!' 	=> 'from_elementor',
					],
				]
			);
		}
		$this->add_control(
			'hr_playlist_show_album_market',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'playlist_show_album_market',
			[
				'label' 							=> esc_html__( 'External Links', 'sonaar-music' ),
				'type' 								=> \Elementor\Controls_Manager::SWITCHER,
				'label_on' 							=> esc_html__( 'Show', 'sonaar-music' ),
				'label_off' 						=> esc_html__( 'Hide', 'sonaar-music' ),
				'return_value' 						=> 'yes',
				'default' 							=> 'yes',
				'condition' => [
					'player_layout!' 	=> 'skin_button'
				],
			]
		);
		$this->add_control(
			'playlist_show_album_market_skin_button',
			[
				'label' 							=> esc_html__( 'External Links', 'sonaar-music' ),
				'type' 								=> \Elementor\Controls_Manager::SWITCHER,
				'label_on' 							=> esc_html__( 'Show', 'sonaar-music' ),
				'label_off' 						=> esc_html__( 'Hide', 'sonaar-music' ),
				'return_value' 						=> 'yes',
				'default' 							=> '',
				'condition' => [
					'player_layout' 	=> 'skin_button'
				],
			]
		);
		
		$this->add_control(
			'playlist_hide_artwork',
			[
				'label' 							=> esc_html__( 'Hide Image Cover', 'sonaar-music' ),
				'type' 								=> \Elementor\Controls_Manager::SWITCHER,
				'label_on' 							=> esc_html__( 'Hide', 'sonaar-music' ),
				'label_off' 						=> esc_html__( 'Show', 'sonaar-music' ),
				'return_value' 						=> 'yes',
				'default' 							=> '',
				'conditions'                    => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'player_layout', 
							'operator' => '==',
							'value' => 'skin_float_tracklist'
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_boxed_tracklist'
								],
								[
									'name' => 'playlist_show_soundwave', 
									'operator' => '!=',
									'value' => 'yes'
								]
							]
						]
					]
				]
			]
		);
		$this->add_control(
			'sr_player_on_artwork',
			[
				'label' 						=> esc_html__( 'Show Controls over Image Cover', 'sonaar-music' ),
				'type' 							=> Controls_Manager::SWITCHER,
				'default' 						=> '',
				'return_value' 					=> 'yes',
				'conditions'                    => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_float_tracklist'
								],
								[
									'name' => 'playlist_hide_artwork', 
									'operator' => '!=',
									'value' => 'yes'
								]
							]
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_boxed_tracklist'
								],
								[
									'name' => 'playlist_show_soundwave', 
									'operator' => '!=',
									'value' => 'yes'
								],
								[
									'name' => 'playlist_hide_artwork', 
									'operator' => '!=',
									'value' => 'yes'
								]
							]
						]
					]
				]
			]
		);
		$this->add_control(
			'playlist_show_soundwave',
			[
				'label' 							=> esc_html__( 'Hide Mini Player / Soundwave', 'sonaar-music' ),
				'type' 								=> \Elementor\Controls_Manager::SWITCHER,
				'label_on' 							=> esc_html__( 'Hide', 'sonaar-music' ),
				'label_off' 						=> esc_html__( 'Show', 'sonaar-music' ),
				'return_value' 						=> 'yes',
				'default' 							=> '',
				'condition' => [
					'player_layout!' 	=> 'skin_button'
				],
			]
		);

// Deprecated control: play_current_id. It's always hidden except for old installation. This has been replaced by playlist_source = from_current_post
		$this->add_control(
			'play_current_id',
			[
				'label'							 	=> esc_html__( 'Play its own Post ID track', 'sonaar-music' ),
				'description' 						=> esc_html__( 'Check this case if this player is intended to be displayed on its own single post', 'sonaar-music' ),
				'type' 								=> \Elementor\Controls_Manager::SWITCHER,
				'yes' 								=> esc_html__( 'Yes', 'sonaar-music' ),
				'no' 								=> esc_html__( 'No', 'sonaar-music' ),
				'return_value' 						=> 'yes',
				'default' 							=> '',
				'conditions' 					=> [
				    'relation' => 'and',
				    'terms' => [
				        [
				            'name' => 'playlist_source',
				            'operator' => '!=',
				            'value' => 'from_elementor'
				        ],
						[
							'name' => 'play_current_id',
							'operator' => '!=',
							'value' => ''
						],				       
				    ]
				]
			]
		);
		if ( !function_exists( 'run_sonaar_music_pro' ) ){
			$this->add_control(
				'hr_enable_sticky_player_pro-only',
				[
					'type' => \Elementor\Controls_Manager::DIVIDER,
				]
			);
			$this->add_control(
				'enable_sticky_player_pro-only',
				[
					'label' 						=> esc_html__( 'Enable Sticky Player', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'description' 					=> esc_html__( 'This option allows you to display a sticky footer player bar on this page', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_searchbox_pro-only',
				[
					'label' 						=> esc_html__( 'Enable Search Box', 'sonaar-music' ),
					'description' 					=> esc_html__( 'Add search box to search any keywords within the tracklist', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_custom_fields_pro-only',
				[
					'label' 						=> esc_html__( 'Enable Custom Field Columns', 'sonaar-music' ),
					'description' 					=> esc_html__( 'Add any custom fields in the tracklist', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_addtocart_pro-only',
				[
					'label' 						=> esc_html__( 'Add Buy Now button', 'sonaar-music' ),
					'description' 					=> esc_html__( 'Add Buy Now and Add-to-cart buttons on each tracks. We also support audio/music licensing.', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_scrollbar_pro-only',
				[
					'label' 						=> esc_html__( 'Enable Scrollbar', 'sonaar-music' ),
					'description' 					=> esc_html__( 'Enable a scrollbar for long tracklist', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_thumbnails_pro-only',
				[
					'label' 						=> esc_html__( 'Display Image Cover beside each tracks', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'description' 					=> esc_html__( 'Add image covers for each tracks in the tracklist', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_volume_pro-only',
				[
					'label' 						=> esc_html__( 'Display Volume Control', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_playlistduration_pro-only',
				[
					'label' 						=> esc_html__( 'Display Playlist Duration', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			
			$this->add_control(
				'enable_publishdate_pro-only',
				[
					'label' 						=> esc_html__( 'Display Publish Date', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_numbersoftrack_pro-only',
				[
					'label' 						=> sprintf( esc_html__( 'Display Total Numbers of %1$ss', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_skipbt_pro-only',
				[
					'label' 						=> esc_html__( 'Display Skip 15/30 seconds button', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			$this->add_control(
				'enable_speedrate_pro-only',
				[
					'label' 						=> esc_html__( 'Display Speed Rate button', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
			
			$this->add_control(
				'enable_shuffle_pro-only',
				[
					'label' 						=> esc_html__( 'Display Shuffle/Random button', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '0', 
					'classes' 						=> 'sr-pro-only',
				]
			);
		}

		/*}*/
		$this->end_controls_section();
		if ( !function_exists( 'run_sonaar_music_pro' ) ){
			$this->start_controls_section(
				'go_pro_content',
				[
					'label' 						=> esc_html__( 'Go Pro', 'sonaar-music' ),
					'tab'   						=> Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_control(
				'sonaar_go_pro',
				[
					'type' 							=> \Elementor\Controls_Manager::RAW_HTML,
					'raw' 							=> 	'<div class="sr_gopro elementor-nerd-box sonaar-gopro">' .
														'<i class="sricons-logo" aria-hidden="true"></i>
															<div class="elementor-nerd-box-title">' .
																__( 'Meet the MP3 Audio Player PRO', 'sonaar-music' ) .
															'</div>
															<div class="elementor-nerd-box-message">' .
																__( 'Our PRO version lets you use Elementor\'s & Gutenberg Editor to customize the look and feel of the player in real-time! Over 70+ options available!', 'sonaar-music' ) .
															'</div>
															<div class="srp_promo_plan_heading"><i class="sricons-logo" aria-hidden="true"></i>MP3 Player Pro - Starter</div>
															<ul>
																<li><i class="eicon-check"></i>Sticky Player with Soundwave</li>
																<li><i class="eicon-check"></i>Elementor Real-Time Style Editor</li>
																<li><i class="eicon-check"></i>Display thumbnail beside each tracks</li>
																<li><i class="eicon-check"></i>Input feed URL directly in the widget</li>
																<li><i class="eicon-check"></i>Volume Control</li>
																<li><i class="eicon-check"></i>Shuffle Tracks</li>
																<li><i class="eicon-check"></i>Build dynamic playlist</li>
																<li><i class="eicon-check"></i>Tracklist View</li>
																<li><i class="eicon-check"></i>Karaoke! Add Live Lyrics to each tracks.</li>
																<li><i class="eicon-check"></i>Tool to import/bulk create playlists</li>
																<li><i class="eicon-check"></i>Statistic Reports</li>
																<li><i class="eicon-check"></i>1 year of support via live chat</li>
																<li><i class="eicon-check"></i>1 year of plugin updates</li>
																<li><i class="eicon-check"></i>1 website usage</li>
															</ul>
															<div class="srp_promo_plan_heading"><i class="sricons-logo" aria-hidden="true"></i>MP3 Player Pro - Business</div>
															<ul>
																<li><i class="eicon-check"></i>Everything in the Starter plan, plus:</li>
																<li><i class="eicon-check"></i>Full WooCommerce Support</li>
																<li><i class="eicon-check"></i>Search Bar Elementor Widget</li>
																<li><i class="eicon-check"></i>Filter Dropdown Elementor Widget</li>
																<li><i class="eicon-check"></i>Chips & Tags Elementor Widget</li>
																<li><i class="eicon-check"></i>Tracklist Custom Fields</li>
																<li><i class="eicon-check"></i>Tracklist Pagination</li>
																<li><i class="eicon-check"></i>Support for ACF, JetEngine, etc</li>
															</ul>
															<div class="srp_promo_plan_heading"><i class="sricons-logo" aria-hidden="true"></i>MP3 Player Pro - Unlimited</div>
															<ul>
																<li><i class="eicon-check"></i>Everything in the Business plan, plus:</li>
																<li><i class="eicon-check"></i>Unlimited website usage</li>
																<li><i class="eicon-check"></i>100+ Elementor Templates Addon kit</li>
																<li><i class="eicon-check"></i>Pre-designed Audio Player Elementor Templates</li>
															</ul>
															<div class="srp_promo_plan_heading"><i class="sricons-logo" aria-hidden="true"></i>MP3 Player Pro - Lifetime</div>
															<ul>
																<li><i class="eicon-check"></i>Everything in the Unlimited plan, plus:</li>
																<li><i class="eicon-check"></i>Lifetime plugin updates</li>
																<li><i class="eicon-check"></i>Lifetime support</li>
																<li><i class="eicon-check"></i>Lifetime Fun!</li>
															</ul>
															<a class="elementor-nerd-box-link elementor-button elementor-button-default elementor-go-pro" href="https://sonaar.io/mp3-audio-player-pro/pricing/?utm_source=Sonaar+Music+Free+Plugin&utm_medium=plugin" target="_blank">' .
															__( 'Go Pro', 'elementor' ) .
															'</a>
														</div>',
				]
			);
		$this->end_controls_section();
		}

		/**
         * STYLE: ARTWORK
         * -------------------------------------------------
         */
		if ( function_exists( 'run_sonaar_music_pro' ) ){
			$this->start_controls_section(
	            'artwork_style',
	            [
	                'label'                 		=> esc_html__( 'Image Cover', 'sonaar-music' ),
					'tab'                   		=> Controls_Manager::TAB_STYLE,
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_float_tracklist'
									],
									[
										'name' => 'playlist_hide_artwork', 
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_boxed_tracklist'
									],
									[
										'name' => 'playlist_show_soundwave', 
										'operator' => '!=',
										'value' => 'yes'
									],
									[
										'name' => 'playlist_hide_artwork', 
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							]
						]
					]
	            ]
			);
			$this->add_responsive_control(
				'artwork_width',
				[
					'label' 						=> esc_html__( 'Image Width', 'sonaar-music' ) . ' (px)',
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'min' 					=> 1,
							'max' 					=> 450,
						],
					],
					'default' 						=> [
							'unit' => 'px',
							'size' => 300,
							],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer[data-playertemplate="skin_float_tracklist"] .album .album-art' => 'width: {{SIZE}}px;',
					],
					'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist',
					],
				]
			);
			$this->add_responsive_control(
				'boxed_artwork_width',
				[
					'label' 						=> esc_html__( 'Image Width', 'sonaar-music' ) . ' (px)',
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'min' 					=> 1,
							'max' 					=> 450,
						],
					],
					'default' 						=> [
							'unit' => 'px',
							'size' => 160,
							],
					'selectors' 					=> [
						'{{WRAPPER}} .iron-audioplayer:not(.sonaar-no-artwork) .srp_player_grid' => 'grid-template-columns: {{SIZE}}px 1fr;',
						'{{WRAPPER}} .srp_player_boxed .album-art' => 'width: {{SIZE}}px; max-width: {{SIZE}}px;',
						'{{WRAPPER}} .srp_player_boxed .sonaar-Artwort-box' => 'min-width: {{SIZE}}px;'
					],	
					'condition' 					=> [
						'player_layout' 	=> 'skin_boxed_tracklist',
					],
				]
			);
			$this->add_responsive_control(
				'artwork_padding',
				[
					'label' 						=> esc_html__( 'Image Padding', 'sonaar-music' ),
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .sonaar-grid .album' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist',
					],
				]
			);
			$this->add_responsive_control(
				'artwork_radius',
				[
					'label' 						=> esc_html__( 'Image Radius', 'elementor' ),
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 300,
						],
					],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .album .album-art img' => 'border-radius: {{SIZE}}px;',
					],
				]
			);
			$this->add_control(
				'artwork_vertical_align',
				[
					'label' 					=> esc_html__( 'Center the Image vertically with the Tracklist', 'sonaar-music' ),
					'type' 						=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 					=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 				=> esc_html__( 'No', 'sonaar-music' ),
					'default' 					=> '',
					'return_value' 				=> 'yes',
					'condition' 					=> [
						'playlist_show_playlist!' 	=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
					'selectors' 				=> [
												'{{WRAPPER}} .sonaar-grid' => 'align-items: center;',
						 
				 ],
				]
			);
			$this->add_control(
				'audio_player_artwork_controls_color',
				[
					'label'                 		=> esc_html__( 'Audio Player Controls over Image', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'separator'						=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer.sr_player_on_artwork .sonaar-Artwort-box .control [class*="sricon-"]' => 'color: {{VALUE}};',
													'{{WRAPPER}} .iron-audioplayer.sr_player_on_artwork .sonaar-Artwort-box .control .play' => 'border-color:{{VALUE}};'
					],
					'condition' 					=> [
						'sr_player_on_artwork' 	=> 'yes',
					],
				]
			);
			$this->add_responsive_control(
				'audio_player_artwork_controls_scale',
				[
					
					'label' 						=> esc_html__( 'Control Size Scale', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::NUMBER,
					'min' 							=> 0,
					'max' 							=> 10,
					'step' 							=> 0.1,
					'default' 						=> 1,
					'condition' 					=> [
						'sr_player_on_artwork' 		=> 'yes',
					],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer.sr_player_on_artwork .sonaar-Artwort-box .control' => 'transform:scale({{SIZE}});',
					],
				]
			);
			$this->add_control(
				'show_control_on_hover',
				[
					
					'label' 						=> esc_html__( 'Show Control On Hover', 'sonaar-music' ),
					'type' 						=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 					=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 				=> esc_html__( 'No', 'sonaar-music' ),
					'default' 					=> '',
					'return_value' 				=> 'yes',
					'condition' 					=> [
						'sr_player_on_artwork' 		=> 'yes',
					]
				]
			);
			$this->add_control(
				'image_overlay_on_hover',
				[
					'label'                		 	=> esc_html__( 'Image Overlay On Hover', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '#6666667a',
					'condition' 					=> [
						'sr_player_on_artwork' 		=> 'yes',
						'show_control_on_hover' 	=> 'yes',
					],
					'selectors'             		=> [
													'{{WRAPPER}} .srp_show_ctr_hover .album-art:before ' => 'background: {{VALUE}}',
					],
				]
			);
			$this->end_controls_section();



			/**
	         * STYLE: SOUNDWAVE 
	         * -------------------------------------------------
	         */
			
			$this->start_controls_section(
	            'player',
	            [
	                'label'							=> esc_html__( 'Mini Player & Soundwave', 'sonaar-music' ),
					'tab'							=> Controls_Manager::TAB_STYLE,
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'name' => 'player_layout', 
								'operator' => 'in',
								'value' => ['skin_boxed_tracklist', 'skin_button']
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_float_tracklist'
									],
									[
										'name' => 'playlist_show_soundwave', 
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							]
						]
					]
	            ]
			);
			$this->add_control(
				'playlist_title_soundwave_show',
				[
					'label' 						=> sprintf( esc_html__( 'Hide %1$s Title', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('playlist/podcast')) ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default'						=> '',
					'return_value' 					=> 'yes',
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'name' => 'player_layout', 
								'operator' => '==',
								'value' => 'skin_boxed_tracklist'
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_float_tracklist'
									],
									[
										'name' => 'playlist_show_playlist', 
										'operator' => '==',
										'value' => ''
									]
								]
							]
						]
					],
				]
			);
			$this->add_control(
				'playlist_title_html_tag_soundwave',
				[
					'label' => sprintf( esc_html__( 'HTML %1$s Title Tag', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('playlist/podcast')) ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
						'div' => 'div',
						'span' => 'span',
						'p' => 'p',
					],
					'default' => 'div',
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'name' => 'player_layout', 
								'operator' => '==',
								'value' => 'skin_boxed_tracklist'
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_float_tracklist'
									],
									[
										'name' => 'playlist_title_soundwave_show', 
										'operator' => '==',
										'value' => ''
									],
									[
										'name' => 'playlist_show_playlist', 
										'operator' => '==',
										'value' => ''
									]
								]
							]
						]
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'playlist_title_soundwave_typography',
					'label' => sprintf( esc_html__( '%1$s Title Typography', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('playlist/podcast')) ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'name' => 'player_layout', 
								'operator' => '==',
								'value' => 'skin_boxed_tracklist'
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_float_tracklist'
									],
									[
										'name' => 'playlist_title_soundwave_show', 
										'operator' => '==',
										'value' => ''
									],
									[
										'name' => 'playlist_show_playlist', 
										'operator' => '==',
										'value' => ''
									]
								]
							]
						]
					],
					'selector' 						=> '{{WRAPPER}} .iron-audioplayer .track-title, {{WRAPPER}} .iron-audioplayer .album-title',
				]
			);
			$this->add_control(
				'playlist_title_soundwave_color',
				[
					'label' => sprintf( esc_html__( '%1$s Title Color', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('playlist/podcast')) ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'name' => 'player_layout', 
								'operator' => '==',
								'value' => 'skin_boxed_tracklist'
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_float_tracklist'
									],
									[
										'name' => 'playlist_title_soundwave_show', 
										'operator' => '==',
										'value' => ''
									],
									[
										'name' => 'playlist_show_playlist', 
										'operator' => '==',
										'value' => ''
									]
								]
							]
						]
					],
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .track-title, {{WRAPPER}} .iron-audioplayer .srp_player_boxed .track-title, {{WRAPPER}} .iron-audioplayer .player, {{WRAPPER}} .iron-audioplayer .album-title' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'hr7',
				[
					'type' 							=> \Elementor\Controls_Manager::DIVIDER,
					'style' 						=> 'thick',
					'condition' 					=> [
						'playlist_show_playlist' 	=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
				]
			);
			$this->add_control(
				'player_subtitle_btshow',
				[
					'label' 						=> esc_html__( 'Hide Subtitle', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'none',
					'selectors' 					=> [
													'{{WRAPPER}} .srp_player_boxed .srp_subtitle' => 'display:{{VALUE}}!important;',
					],
					'condition' 					=> [
						'player_layout' 	=> 'skin_boxed_tracklist',
					],
					'separator'						=> 'before',
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'player_subtitle_typography',
					'label' 						=> esc_html__( 'Subtitle Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'condition' 					=> [
						'player_subtitle_btshow' 			=> '',
						'player_layout' 	=> 'skin_boxed_tracklist',
					],
					'selector' 						=> '{{WRAPPER}} .srp_player_boxed .srp_subtitle',
				]
			);
			$this->add_control(
				'player_subtitle-color',
				[
					'label'                		 	=> esc_html__( 'Subtitle Color', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'condition' 					=> [
						'player_subtitle_btshow' 			=> '',
						'player_layout' 	=> 'skin_boxed_tracklist',
					],
					'selectors'             		=> [
													'{{WRAPPER}} .srp_player_boxed .srp_subtitle' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'title_soundwave_show',
				[
					'label' 						=> sprintf( esc_html__( 'Hide %1$s Title', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default'						=> '',
					'return_value' 					=> 'yes',
					'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist',
					],
					/*'selectors' 					=> [
							 						'{{WRAPPER}} .iron-audioplayer .track-title' => 'display:{{VALUE}};',
					 ],*/
				]
			);
			$this->add_control(
				'title_html_tag_soundwave',
				[
					'label' => sprintf( esc_html__( 'HTML %1$s Title Tag', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
						'div' => 'div',
						'span' => 'span',
						'p' => 'p',
					],
					'default' => 'div',
					'condition' 					=> [
						'title_soundwave_show' 		=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'title_soundwave_typography',
					'label' 						=> sprintf( esc_html__( '%1$s Title Typography', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'condition' 					=> [
						'title_soundwave_show' 		=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
					'selector' 						=> '{{WRAPPER}} div.iron-audioplayer .track-title',
				]
			);
			$this->add_control(
				'title_soundwave_color',
				[
					'label' 						=> sprintf( esc_html__( '%1$s Title Color', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'condition' 					=> [
						'title_soundwave_show' 		=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
					'selectors'             		=> [
													'{{WRAPPER}} div.iron-audioplayer .track-title, {{WRAPPER}} .iron-audioplayer .player' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'cat_description_typo',
					'label' 						=> esc_html__( 'Description/About Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'separator'						=> 'before',
					'condition' 					=> [
						'show_cat_description' 	=> '1',
					],
					'selector' 						=> '{{WRAPPER}} .iron-audioplayer .srp_podcast_rss_description',
				]
			);
			$this->add_control(
				'cat_description_color',
				[
					'label'                 		=> esc_html__( 'Description/About  Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'condition' 					=> [
						'show_cat_description' 	=> '1',
					],
					'selectors'            			=> [
													'{{WRAPPER}} .iron-audioplayer .srp_podcast_rss_description' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'hr8',
				[
					'type' 							=> \Elementor\Controls_Manager::DIVIDER,
					'style' 						=> 'thick',
					'condition' 					=> [
						'player_layout!' 	=> 'skin_button',
					]
				]
			);


			$this->add_control(
				'soundwave_show',
				[
					'label' 						=> esc_html__( 'Hide SoundWave', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'yes',
					'condition' 					=> [
						'player_layout!' 	=> 'skin_button',
					],
				]
			);
			$this->add_responsive_control(
				'player_align',
				[
					'label'						 	=> esc_html__( 'Alignment', 'sonaar-music' ),
					'type' 							=> Controls_Manager::CHOOSE,
					'options' 						=> [
						'flex-start'    					=> [
							'title' 				=> esc_html__( 'Left', 'elementor' ),
							'icon' 					=> 'eicon-h-align-left',
						],
						'center' 					=> [
							'title' 				=> esc_html__( 'Center', 'elementor' ),
							'icon' 					=> 'eicon-h-align-center',
						],
						'flex-end' 					=> [
							'title' 				=> esc_html__( 'Right', 'elementor' ),
							'icon' 					=> 'eicon-h-align-right',
						],
					],
					'default' 						=> '',
					'selectors' 					=> [
													'{{WRAPPER}} .album-player' => 'display: flex; justify-content: {{VALUE}};',
					],
					'condition' 					=> [
						'player_layout' 	=> 'skin_button',
						'soundwave_show_skin_button' => ''
					],
				]
			);
			$this->add_control(
				'soundwave_show_skin_button',
				[
					'label' 						=> esc_html__( 'Show SoundWave', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'yes',
					'condition' 					=> [
						'player_layout' 	=> 'skin_button',
					],
				]
			);
			$this->add_control(
				'soundWave_progress_bar_color',
				[
					'label'                 		=> esc_html__( 'SoundWave Progress Bar Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'             		=> [
						'{{WRAPPER}} .sonaar_wave_cut rect' => 'fill: {{VALUE}}',
						'{{WRAPPER}} .sr_waveform_simplebar .sonaar_wave_cut' => 'background-color: {{VALUE}}',
					],
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '!=',
										'value' => 'skin_button'
									],
									[
										'name' => 'soundwave_show', 
										'operator' => '==',
										'value' => ''
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_button'
									],
									[
										'name' => 'soundwave_show_skin_button', 
										'operator' => '!=',
										'value' => ''
									]
								]
							]
						]
					],
					'render_type' => 'template',
					
				]
			);
			$this->add_control(
				'soundWave_bg_bar_color',
				[
					'label'                 		=> esc_html__( 'SoundWave Background Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'             		=> [
						'{{WRAPPER}} .sonaar_wave_base rect' => 'fill: {{VALUE}}',
						'{{WRAPPER}} .sr_waveform_simplebar .sonaar_wave_base' => 'background-color: {{VALUE}}',
					],			
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '!=',
										'value' => 'skin_button'
									],
									[
										'name' => 'soundwave_show', 
										'operator' => '==',
										'value' => ''
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_button'
									],
									[
										'name' => 'soundwave_show_skin_button', 
										'operator' => '!=',
										'value' => ''
									]
								]
							]
						]
					],
					'render_type' => 'template',
				]
			);
			if(Sonaar_Music::get_option('waveformType', 'srmp3_settings_general') === 'simplebar'){
				$this->add_responsive_control(
					'simple_bar_height',
					[
						
						'label' 						=> esc_html__( 'Progress Bar Height', 'sonaar-music' ),
						'type' 							=> Controls_Manager::SLIDER,
						'range' 						=> [
						'px' 						=> [
							'min'					=> 1,
							'max' 					=> 50,
						],
						],
						'selectors' 					=> [
							'{{WRAPPER}} .sr_waveform_simplebar .sonaar_fake_wave .sonaar_wave_base, {{WRAPPER}} .sr_waveform_simplebar .sonaar_fake_wave .sonaar_wave_cut' => 'height: {{SIZE}}px !important;',
						],
						'conditions'                    => [
							'relation' => 'or',
							'terms' => [
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout', 
											'operator' => '!=',
											'value' => 'skin_button'
										],
										[
											'name' => 'soundwave_show', 
											'operator' => '==',
											'value' => ''
										]
									]
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout', 
											'operator' => '==',
											'value' => 'skin_button'
										],
										[
											'name' => 'soundwave_show_skin_button', 
											'operator' => '!=',
											'value' => ''
										]
									]
								]
							]
						],
					]
				);
				$this->add_responsive_control(
					'simple_bar_radius',
					[
						'label' 						=> esc_html__( 'Progress Bar Radius', 'elementor' ),
						'type' 							=> Controls_Manager::SLIDER,
						
						'range' 						=> [
							'px' 						=> [
								'max' 					=> 20,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 0,
						],
						'selectors' 					=> [
														'{{WRAPPER}} .sr_waveform_simplebar .sonaar_fake_wave .sonaar_wave_base, {{WRAPPER}} .sr_waveform_simplebar .sonaar_fake_wave .sonaar_wave_cut' => 'border-radius: {{SIZE}}px;',
						],
					]
				);
			}
			$this->add_control(
				'progressbar_inline',
				[
					'label' 						=> esc_html__( 'Inline Progress Bar', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'yes',
					'condition' 					=> [
						'soundwave_show' 			=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
				]
			);
			$this->add_control(
				'duration_soundwave_show',
				[
					'label' 						=> esc_html__( 'Hide Time Durations', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'yes',
					'separator'						=> 'before',
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '!=',
										'value' => 'skin_button'
									],
									[
										'name' => 'soundwave_show', 
										'operator' => '==',
										'value' => ''
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_button'
									],
									[
										'name' => 'soundwave_show_skin_button', 
										'operator' => '!=',
										'value' => ''
									]
								]
							]
						]
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'duration_soundwave_typography',
					'label' 						=> esc_html__( 'Time Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'conditions'                    => [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'duration_soundwave_show', 
								'operator' => '=',
								'value' => ''
							],
							[
								'relation' => 'or',
								'terms' => [
									[
										'relation' => 'and',
										'terms' => [
											[
												'name' => 'player_layout', 
												'operator' => '!=',
												'value' => 'skin_button'
											],
											[
												'name' => 'soundwave_show', 
												'operator' => '==',
												'value' => ''
											]
										]
									],
									[
										'relation' => 'and',
										'terms' => [
											[
												'name' => 'player_layout', 
												'operator' => '==',
												'value' => 'skin_button'
											],
											[
												'name' => 'soundwave_show_skin_button', 
												'operator' => '!=',
												'value' => ''
											]
										]
									]
								]
							]
						]
					],
					'selector' 						=> '{{WRAPPER}} .iron-audioplayer .player',
				]
			);
			$this->add_control(
				'duration_soundwave_color',
				[
					'label'                 		=> esc_html__( 'Time Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'conditions'                    => [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'duration_soundwave_show', 
								'operator' => '=',
								'value' => ''
							],
							[
								'relation' => 'or',
								'terms' => [
									[
										'relation' => 'and',
										'terms' => [
											[
												'name' => 'player_layout', 
												'operator' => '!=',
												'value' => 'skin_button'
											],
											[
												'name' => 'soundwave_show', 
												'operator' => '==',
												'value' => ''
											]
										]
									],
									[
										'relation' => 'and',
										'terms' => [
											[
												'name' => 'player_layout', 
												'operator' => '==',
												'value' => 'skin_button'
											],
											[
												'name' => 'soundwave_show_skin_button', 
												'operator' => '!=',
												'value' => ''
											]
										]
									]
								]
							]
						]
					],
					'selectors'            			=> [
													'{{WRAPPER}} .iron-audioplayer .currentTime, {{WRAPPER}} .iron-audioplayer .totalTime' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'hr9',
				[
					'type' 							=> \Elementor\Controls_Manager::DIVIDER,
					'style' 						=> 'thick',
				]
			);

			$this->start_controls_tabs( 'tabs_play_button_style' );
			$this->start_controls_tab(
				'tab_play_button_normal',
				[
					'label' 						=> esc_html__( 'Normal', 'elementor' ),
				]
			);
			$this->add_control(
				'audio_player_controls_color',
				[
					'label'                 		=> esc_html__( 'Audio Player Controls Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .control .sricon-play, {{WRAPPER}} .srp-play-button .sricon-play' => 'color: {{VALUE}}',
													'{{WRAPPER}} .iron-audioplayer .control .sr_speedRate div' => 'color: {{VALUE}}; border-color: {{VALUE}} ',
													'{{WRAPPER}} .iron-audioplayer .control' => 'color: {{VALUE}};',
													'{{WRAPPER}} .iron-audioplayer .srp-play-circle' => 'border-color: {{VALUE}};',
													'{{WRAPPER}} .iron-audioplayer .srp-play-button-label-container' => 'background: {{VALUE}};',	
													'{{WRAPPER}} .iron-audioplayer .sonaar-Artwort-box .control [class*="sricon-"]' => 'color: {{VALUE}};',
													'{{WRAPPER}} .iron-audioplayer .sonaar-Artwort-box .control .play' => 'border-color:{{VALUE}};'			
					],
				]
			);
			$this->add_control(
				'audio_player_play_text_color',
				[
					'label'                 		=> esc_html__( 'Play/Pause Text Color ', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .srp-play-button-label-container' => 'color: {{VALUE}};',
					],
					'condition' 					=> [
						'player_layout' 	=> ['skin_boxed_tracklist', 'skin_button']
					],
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_play_button_hover',
				[
					'label' 						=> esc_html__( 'Hover', 'elementor' ),
				]
			);
			$this->add_control(
				'audio_player_controls_color_hover',
				[
					'label'                 		=> esc_html__( 'Audio Player Controls Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .control .sricon-play:hover, {{WRAPPER}} .srp-play-button:hover .sricon-play' => 'color: {{VALUE}}',
													'{{WRAPPER}} .iron-audioplayer .control .sr_speedRate:hover div' => 'color: {{VALUE}}; border-color: {{VALUE}} ',
													'{{WRAPPER}} .iron-audioplayer .control .sr_skipBackward:hover, {{WRAPPER}} .iron-audioplayer .control .sr_skipForward:hover, {{WRAPPER}} .iron-audioplayer .control .sr_shuffle:hover, {{WRAPPER}} .iron-audioplayer .control .previous:hover, {{WRAPPER}} .iron-audioplayer .control .next:hover, {{WRAPPER}} .iron-audioplayer .control .volume:hover .sricon-volume' => 'color: {{VALUE}};',
													'{{WRAPPER}} .iron-audioplayer .srp-play-button:hover .srp-play-circle' => 'border-color: {{VALUE}};',
													'{{WRAPPER}} .iron-audioplayer .srp-play-button-label-container:hover' => 'background: {{VALUE}};',
					],
				]
			);
			$this->add_control(
				'audio_player_play_text_color_hover',
				[
					'label'                 		=> esc_html__( 'Play/Pause Text Color ', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .srp-play-button-label-container:hover' => 'color: {{VALUE}};',
					],
					'condition' 					=> [
						'player_layout' 	=> ['skin_boxed_tracklist', 'skin_button']
					],
				]
			);
			$this->add_control(
				'button_border_color_hover',
				[
					'label'                 		=> esc_html__( 'Border Color ', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .srp-play-button-label-container:hover' => 'border-color: {{VALUE}};',
					],
					'condition' 					=> [
						'player_layout' 	=> 'skin_button',
						'use_play_label_skin_button' => 'yes'
					],
				]
			);
			$this->add_control(
				'button_hover_animation',
				[
					'label' => __( 'Hover Animation', 'elementor-sonaar' ),
					'type'  => Controls_Manager::HOVER_ANIMATION,
					'condition' 					=> [
						'player_layout' 	=> 'skin_button'
					],

				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();

			$this->add_control(
				'hr10',
				[
					'type' 							=> \Elementor\Controls_Manager::DIVIDER,
					'style' 						=> 'thick',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 							=> 'button_label_border',
					'selector' 						=> '{{WRAPPER}} .srp-play-button-label-container',
					'separator' 					=> 'after',
					'condition' 					=> [
						'player_layout' 	=> 'skin_button',
						'use_play_label_skin_button' => 'yes'
					],
				]
			);
			$this->add_control(
				'use_play_label',
				[
					'label' 		=> esc_html__( 'Show Text instead of Play Icon', 'sonaar-music' ),
					'type' 			=> Controls_Manager::SELECT,
					'options' 		=> [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_use_play_label', 'srmp3_settings_widget_player') ),
						'true' 		=> esc_html__( 'Yes', 'sonaar-music' ),
						'false' 	=> esc_html__( 'No', 'sonaar-music' ),
					],
					'default' 		=> 'default',
					'condition' 	=> [
					'player_layout' => 'skin_boxed_tracklist',
					],
				]
			);
			if ( function_exists( 'run_sonaar_music_pro' ) ){
				$this->add_control(
					'play_text',
					[
						'label' 						=> esc_html__( 'Play text', 'sonaar-music' ),
						'type' 							=> Controls_Manager::TEXT,
						'dynamic' 						=> [
							'active' 					=> true,
						],
						'default' 						=> esc_html__( Sonaar_Music::get_option('labelPlayTxt', 'srmp3_settings_widget_player') ),
						'conditions'                    => [
							'relation' => 'or',
							'terms' => [
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout', 
											'operator' => '==',
											'value' => 'skin_button'
										],
										[
											'name' => 'use_play_label_skin_button', 
											'operator' => '==',
											'value' => 'yes'
										]
									]
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout', 
											'operator' => '==',
											'value' => 'skin_boxed_tracklist'
										],
										[
											'name' => 'use_play_label', 
											'operator' => '==',
											'value' => 'true'
										]
									]
								]
							]
						],
						'label_block' 					=> false,
					]
				);
				$this->add_control(
					'pause_text',
					[
						'label' 						=> esc_html__( 'Pause text', 'sonaar-music' ),
						'type' 							=> Controls_Manager::TEXT,
						'dynamic' 						=> [
							'active' 					=> true,
						],
						'default' 						=> esc_html__( Sonaar_Music::get_option('labelPauseTxt', 'srmp3_settings_widget_player') ),
						'conditions'                    => [
							'relation' => 'or',
							'terms' => [
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout', 
											'operator' => '==',
											'value' => 'skin_button'
										],
										[
											'name' => 'use_play_label_skin_button', 
											'operator' => '==',
											'value' => 'yes'
										]
									]
								],
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'player_layout', 
											'operator' => '==',
											'value' => 'skin_boxed_tracklist'
										],
										[
											'name' => 'use_play_label', 
											'operator' => '==',
											'value' => 'true'
										]
									]
								]
							]
						],
						'label_block' 					=> false,
					]
				);
			}
			$this->add_control(
				'play_btn_align_wave',
				[
					'label' 		=> esc_html__( 'Align the Play/Pause Button to the SoundWave', 'sonaar-music' ),
					'type' 								=> \Elementor\Controls_Manager::SWITCHER,
					'return_value' 						=> 'yes',
					'condition' 	=> [
						'player_layout' => 'skin_boxed_tracklist',
						'use_play_label' => 'false'
						],
				]
			);
			$this->add_control(
				'use_play_label_skin_button',
				[
					'label' 							=> esc_html__( 'Show Play Label', 'sonaar-music' ),
					'type' 								=> \Elementor\Controls_Manager::SWITCHER,
					'return_value' 						=> 'yes',
					'default' 							=> 'yes',
					'condition' 	=> [
						'player_layout' => 'skin_button',
						],
				]
			);
			
		$this->add_control(
			'use_play_label_with_icon',
			[
				'label' 							=> esc_html__( 'Play Icon', 'sonaar-music' ),
				'type' 								=> \Elementor\Controls_Manager::SWITCHER,
				'return_value' 						=> 'yes',
				'default' 							=> 'yes',
				'condition' 	=> [
					'player_layout' => 'skin_button',
					'use_play_label_skin_button' => 'yes'
					],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 							=> 'play_label_typography',
				'label' 						=> esc_html__( 'Play Label Typography', 'sonaar-music' ),
				'scheme' 						=> Typography::TYPOGRAPHY_1,
				'conditions'                    => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_button'
								],
								[
									'name' => 'use_play_label_skin_button', 
									'operator' => '==',
									'value' => 'yes'
								]
							]
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_boxed_tracklist'
								],
								[
									'name' => 'use_play_label', 
									'operator' => '==',
									'value' => 'true'
								]
							]
						]
					]
				],
				'selector' 						=> '{{WRAPPER}} .srp-play-button-label-container',
			]
		);
		$this->add_responsive_control(
			'play_label_padding',
			[
				'label' 						=> esc_html__( 'Play Label Padding', 'sonaar-music' ),
				'type' 							=> Controls_Manager::DIMENSIONS,
				'size_units' 					=> [ 'px', 'em', '%' ],
				'selectors' 					=> [
												'{{WRAPPER}} .srp-play-button-label-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions'                    => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_button'
								],
								[
									'name' => 'use_play_label_skin_button', 
									'operator' => '==',
									'value' => 'yes'
								]
							]
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_boxed_tracklist'
								],
								[
									'name' => 'use_play_label', 
									'operator' => '==',
									'value' => 'true'
								]
							]
						]
					]
				],
			]
		);
		$this->add_control(
			'play_button_radius',
			[
				'label' 						=> esc_html__( 'Play Button Radius', 'elementor' ),
				'type' 							=> Controls_Manager::SLIDER,
				'range' 						=> [
					'px' 						=> [
						'max' 					=> 100,
					],
				],
				'conditions'                    => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_button'
								],
								[
									'name' => 'use_play_label_skin_button', 
									'operator' => '==',
									'value' => 'yes'
								]
							]
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'player_layout', 
									'operator' => '==',
									'value' => 'skin_boxed_tracklist'
								],
								[
									'name' => 'use_play_label', 
									'operator' => '==',
									'value' => 'true'
								]
							]
						]
					]
				],
				'selectors' 					=> [
												'{{WRAPPER}} .srp-play-button-label-container' => 'border-radius: {{SIZE}}px;',
				],
			]
		);
		$this->add_responsive_control(
			'play-size',
			[
				'label' 					=> esc_html__( 'Play/Pause size', 'sonaar-music' ) . ' (px)',
				'type' 						=> Controls_Manager::SLIDER,
				'range' 					=> [
					'px' 					=> [
						'min'				=> 0,
						'max' 				=> 100,
					],
				],
				'selectors' 				=> [
							'{{WRAPPER}} .srp-play-button .sricon-play' => 'font-size: {{SIZE}}px;',
				],
				'condition' 				=> [
					'player_layout' => 'skin_boxed_tracklist',
					'use_play_label!' => 'true'
				],
			]
		);
		$this->add_responsive_control(
			'play-circle-size',
			[
				'label' 					=> esc_html__( 'Play/Pause Circle size', 'sonaar-music' ) . ' (px)',
				'type' 						=> Controls_Manager::SLIDER,
				'range' 					=> [
					'px' 					=> [
						'min'				=> 10,
						'max' 				=> 150,
					],
				],
				'selectors' 				=> [
							'{{WRAPPER}} .srp-play-circle' => 'height: {{SIZE}}px; width: {{SIZE}}px; border-radius: {{SIZE}}px;',
				],
				'condition' 				=> [
					'player_layout' => 'skin_boxed_tracklist',
					'use_play_label!' => 'true'
				],
			]
		);
		$this->add_responsive_control(
			'play-circle-width',
			[
				'label' 					=> esc_html__( 'Play/Pause Circle width', 'sonaar-music' ) . ' (px)',
				'type' 						=> Controls_Manager::SLIDER,
				'range' 					=> [
					'px' 					=> [
						'min'				=> 0,
						'max' 				=> 30,
					],
				],
				'selectors' 				=> [
							'{{WRAPPER}} .srp-play-circle' => 'border-width: {{SIZE}}px;',
				],
				'condition' 				=> [
					'player_layout' => 'skin_boxed_tracklist',
					'use_play_label!' => 'true'
				],
			]
		);
			$this->add_responsive_control(
				'audio_player_controls_spacebefore',
				[
					'label' 					=> esc_html__( 'Add Space Before Player Control', 'sonaar-music' ) . ' (px)',
					'type' 						=> Controls_Manager::SLIDER,
					'range' 					=> [
						'px' 					=> [
							'min'				=> -500,
							'max' 				=> 100,
						],
					],
					'selectors' 				=> [
								'{{WRAPPER}} .iron-audioplayer .album-player .control' => 'top: {{SIZE}}px;position:relative;',
					],
					'separator'		=> 'before',
					'condition' 				=> [
					'progressbar_inline'		=> '',
					'player_layout!' => 'skin_button',
					],
				]
			);
			$this->add_control(
				'show_skip_bt',
				[
					'label' 		=> esc_html__( 'Show Skip 15/30 Seconds button', 'sonaar-music' ),
					'type' 			=> Controls_Manager::SELECT,
					'options' 		=> [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_show_skip_bt', 'srmp3_settings_widget_player') ),
						'true' 		=> esc_html__( 'Yes', 'sonaar-music' ),
						'false' 	=> esc_html__( 'No', 'sonaar-music' ),
					],
					'default' 		=> 'default',
					'condition' 					=> [
						'player_layout!' 	=> 'skin_button',
					],
				]
			);
			$this->add_control(
				'show_skip_bt_skin_button',
				[
					'label' 		=> esc_html__( 'Show Skip 15/30 Seconds button', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'separator'		=> 'before',
					'return_value' 	=> 'yes',
					'default' 		=> '',
					'condition' 					=> [
						'player_layout' 	=> 'skin_button',
					],
				]
			);
			$this->add_control(
				'show_shuffle_bt',
				[
					'label' 		=> esc_html__( 'Show Shuffle button', 'sonaar-music' ),
					'type' 			=> Controls_Manager::SELECT,
					'options' 		=> [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_show_shuffle_bt', 'srmp3_settings_widget_player') ),
						'true' 		=> esc_html__( 'Yes', 'sonaar-music' ),
						'false' 	=> esc_html__( 'No', 'sonaar-music' ),
					],
					'default' 		=> 'default',
					'condition' 					=> [
						'player_layout!' 	=> 'skin_button',
					],
				]
			);
			$this->add_control(
				'show_shuffle_bt_skin_button',
				[
					'label' 		=> esc_html__( 'Show Shuffle button', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'return_value' 	=> 'yes',
					'default' 		=> '',
					'condition' 					=> [
						'player_layout' 	=> 'skin_button',
					],
				]
			);

			$this->add_control(
				'show_speed_bt',
				[
					'label' 		=> esc_html__( 'Show Playback Speed button', 'sonaar-music' ),
					'type' 			=> Controls_Manager::SELECT,
					'options' 		=> [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_show_speed_bt', 'srmp3_settings_widget_player') ),
						'true' 		=> esc_html__( 'Yes', 'sonaar-music' ),
						'false' 	=> esc_html__( 'No', 'sonaar-music' ),
					],
					'default' 		=> 'default',
					'condition' 					=> [
						'player_layout!' 	=> 'skin_button',
					],
				]
			);
			$this->add_control(
				'show_speed_bt_skin_button',
				[
					'label' 		=> esc_html__( 'Show Playback Speed button', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'return_value' 	=> 'yes',
					'default' 		=> '',
					'condition' 					=> [
						'player_layout' 	=> 'skin_button',
					],
				]
			);

			$this->add_control(
				'show_volume_bt',
				[
					'label' 		=> esc_html__( 'Show Volume Control', 'sonaar-music' ),
					'type' 			=> Controls_Manager::SELECT,
					'options' 		=> [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_show_volume_bt', 'srmp3_settings_widget_player') ),
						'true'		=> esc_html__( 'Yes', 'sonaar-music' ),
						'false' 	=> esc_html__( 'No', 'sonaar-music' ),
					],
					'default' 		=> 'default',
					'condition' 					=> [
						'player_layout!' 	=> 'skin_button',
					],
				]
			);
			$this->add_control(
				'show_volume_bt_skin_button',
				[
					'label' 		=> esc_html__( 'Show Volume Control', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'return_value' 	=> 'yes',
					'default' 		=> '',
					'condition' 					=> [
						'player_layout' 	=> 'skin_button',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'player_background',
					'label' => esc_html__( 'Background', 'elementor-sonaar' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .iron-audioplayer .srp_player_boxed, {{WRAPPER}} .iron-audioplayer[data-playertemplate="skin_float_tracklist"] .album-player',
					'separator' 				=> 'before',
					'condition' 					=> [
						'player_layout!' 	=> 'skin_button',
					],
				]
			);
			
			$this->add_responsive_control(
				'artwork_boxed_vertical_align',
				[
					'label' 						=> esc_html__( 'Vertical Alignment with the Image Cover', 'sonaar-music' ),
					'type' 							=> Controls_Manager::CHOOSE,
					'options' 						=> [
						'flex-start'    					=> [
							'title' 				=> esc_html__( 'Top', 'elementor' ),
							'icon' 					=> 'eicon-v-align-top',
						],
						'center' 					=> [
							'title' 				=> esc_html__( 'Center', 'elementor' ),
							'icon' 					=> 'eicon-v-align-middle',
						],
						'flex-end' 					=> [
							'title' 				=> esc_html__( 'Bottom', 'elementor' ),
							'icon' 					=> 'eicon-v-align-bottom',
						],
					],
					'default' 						=> '',
					'separator'					=> 'after',
					'condition' 					=> [
						'player_layout' 	=> 'skin_boxed_tracklist',
					],
					'selectors' 					=> [
														'{{WRAPPER}} .iron-audioplayer .srp_player_grid' => 'align-items:{{VALUE}}',
					],
				]
			);
			$this->end_controls_section();


			/**
			* STYLE: CUSTOM COLUMNS & SEARCH BAR
			* -------------------------------------------------
			*/
			
			$this->start_controls_section(
				'searchbar',
				[
					'label'                 		=> esc_html__( 'Search Bar', 'sonaar-music' ),
					'tab'                   		=> Controls_Manager::TAB_STYLE,
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '!=',
										'value' => 'skin_button'
									],
									[
										'name' => 'playlist_show_playlist', 
										'operator' => '==',
										'value' => 'yes'
									]
								]
							],
						]
					],
				]
			);
			if (get_site_option('SRMP3_ecommerce') != '1'){
				$this->add_control(
					'sonaar_go_pro',
					[
						'type' 							=> \Elementor\Controls_Manager::RAW_HTML,
						'raw' 							=> 	$this->srp_promo_message(),
					]
				);
				$this->end_controls_section();
			}else if (get_site_option('SRMP3_ecommerce') == '1'){
			$this->add_control(
				'searchbar_searchheading',
				[
					'label' 						=> esc_html__( 'Search Bar', 'sonaar-music' ),
					'type' 							=> Controls_Manager::HEADING,
				]
			);
			$this->add_control(
				'enable_searchbar_keyword',
				[
					'label' 							=> esc_html__( 'Enable Tracklist Search', 'sonaar-music' ),
					'type' 								=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 							=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 						=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 						=> 'true',
					'default' 							=> '',
				]
			);
			$this->add_control(
				'search_placeholder',
				[
					'label' 						=> esc_html__( 'Placeholder Text', 'sonaar-music' ),
					'type' 							=> Controls_Manager::TEXT,
					'default' 						=> '',
					'placeholder' 					=> esc_html__( 'Enter any keyword', 'sonaar-music' ),
					'separator' 					=> 'after',
					'dynamic' 						=> [
						'active' 					=> true,
					],
				]
			);
			$this->add_control(
				'searchbar_color',
				[
					'label'                		 	=> esc_html__( 'Keyword Color', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'selectors'             		=> [
						'{{WRAPPER}} .srp_search_container .srp_search, {{WRAPPER}} .srp_search_container .fa-search' => 'color: {{VALUE}}',
					],
					'condition' 					=> [
						'enable_searchbar_keyword' 	=> 'true',
					],
				]
			);
			$this->add_control(
				'reset_color',
				[
					'label'                		 	=> esc_html__( 'Reset Color', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'selectors'             		=> [
						'{{WRAPPER}} .srp_search_container .srp_reset_search' => 'color: {{VALUE}}',
					],
					'condition' 					=> [
						'enable_searchbar_keyword' 	=> 'true',
					],
				]
			);
			$this->add_control(
				'searchbar_placeholdercolor',
				[
					'label'                		 	=> esc_html__( 'Placeholder Color', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'selectors'             		=> [
						'{{WRAPPER}} .srp_search_container .srp_search::placeholder' => 'color: {{VALUE}}',
					],
					'condition' 					=> [
						'enable_searchbar_keyword' 	=> 'true',
					],
				]
			);
			$this->add_control(
				'searchbar_bg',
				[
					'label'                		 	=> esc_html__( 'Background Color', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'selectors'             		=> [
						'{{WRAPPER}} .srp_search_container .srp_search' => 'background: {{VALUE}}',
					],
					'condition' 					=> [
						'enable_searchbar_keyword' 	=> 'true',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'searchbar_typo',
					'label' 						=> esc_html__( 'Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'selector' 						=> '{{WRAPPER}} .srp_search_container .srp_search',
					'separator' 					=> 'after',
					'condition' 					=> [
						'enable_searchbar_keyword' 	=> 'true',
					],
				]
			);
			$this->add_responsive_control(
				'searchbar_padding',
				[
					'label' 						=> esc_html__( 'Search Bar Padding', 'sonaar-music' ),
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .srp_search_container .srp_search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 					=> [
						'enable_searchbar_keyword' 	=> 'true',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' 							=> 'searchbar_container_bg',
					'label' 						=> esc_html__( 'Search Bar Container Background', 'elementor-sonaar' ),
					'types' 						=> [ 'classic', 'gradient'],
					'selector' 						=> '{{WRAPPER}} .srp_search_main',
					'condition' 					=> [
						'enable_searchbar_keyword' 	=> 'true',
					],
				]
			);
			$this->add_responsive_control(
				'searchbar_container_padding',
				[
					'label' 						=> esc_html__( 'Search Bar Container Padding', 'sonaar-music' ),
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .srp_search_main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 					=> [
						'enable_searchbar_keyword' 	=> 'true',
					],
				]
			);
			$this->end_controls_section();
		}
		
			$this->start_controls_section(
				'searchbar_style',
				[
					'label'                 		=> esc_html__( 'Custom Fields Columns', 'sonaar-music' ),
					'tab'                   		=> Controls_Manager::TAB_STYLE,
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '!=',
										'value' => 'skin_button'
									],
									[
										'name' => 'playlist_show_playlist', 
										'operator' => '==',
										'value' => 'yes'
									]
								]
							],
						]
					],
				]
			);
		if (get_site_option('SRMP3_ecommerce') != '1'){
			$this->add_control(
				'sonaar_go_pro_cf',
				[
					'type' 							=> \Elementor\Controls_Manager::RAW_HTML,
					'raw' 							=> 	$this->srp_promo_message(),
				]
			);
			$this->end_controls_section();
		}else if (get_site_option('SRMP3_ecommerce') == '1'){
			$this->add_control(
				'searchbar_cf_heading',
				[
					'label' 						=> esc_html__( 'Columns', 'elementor' ),
					'type' 							=> Controls_Manager::HEADING,
					'separator' 					=> 'before',
					'condition' 			=> [
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_responsive_control(
				'cf_track_title_width',
				[
					'label' 						=> esc_html__( 'Track Title Column Width', 'sonaar-music' ) . ' (px)',
					'type'							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 600,
						],
					],
					'size_units' 					=> [ 'px', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer.srp_has_customfields .audio-track' => 'flex: 1 1 {{SIZE}}{{UNIT}};',
													'{{WRAPPER}} .iron-audioplayer.srp_has_customfields .sr-playlist-cf-container' => 'flex: 0 1 calc(100% -  {{SIZE}}{{UNIT}});',
					],
					'condition' 			=> [
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_responsive_control(
				'cf_cta_width',
				[
					'label' 						=> esc_html__( 'CTA Column Width', 'sonaar-music' ) . ' (px)',
					'type'							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 600,
						],
					],
					'size_units' 					=> [ 'px', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer.srp_has_customfields .sr-playlist-item .store-list' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
													'{{WRAPPER}} .iron-audioplayer.srp_has_customfields .playlist .store-list .song-store-list-menu' => 'width: {{SIZE}}{{UNIT}};',
													'{{WRAPPER}} .iron-audioplayer.srp_has_customfields .sr-playlist-item:not(.srp_extended) .song-store-list-menu' => 'max-width: {{SIZE}}{{UNIT}};',
													'{{WRAPPER}} .iron-audioplayer.srp_has_customfields .srp_responsive .sr-playlist-item .store-list' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
													'{{WRAPPER}} .iron-audioplayer.srp_has_customfields .srp_responsive .playlist .store-list .song-store-list-menu' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' 			=> [
						'trackList_layout!' => 'grid',
					],
				]
			);
			$column_repeater = new \Elementor\Repeater();
			$column_repeater->add_control(
				'column_name',
				[
					'label'     => __( 'Heading Title', 'sonaar-music' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic' 						=> [
						'active' 					=> true,
					],
					'default' 						=> '',
					'label_block' 					=> true,
				]
			);
			$column_repeater->add_control(
				'custom_field_plugin',
				[
					'label'					=> esc_html__( 'Source ', 'sonaar-music' ),
					'type' 					=> Controls_Manager::SELECT,
					'label_block'			=> false,
					'options'				=> $this->check_column_plugin_activated(),
					'default' 				=> 'object',
				]
			);
			$column_repeater->add_control(
				'column_fields_acf',
				[
					'label'     => __( 'ACF Field', 'sonaar-music' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'default'   => '',
					'groups'    => $this->get_fields_goups( 'fields' ),
					'condition' => [
						'custom_field_plugin' => 'acf',
					],
				]
			);
			
			if (function_exists('jet_engine')){
				$meta_fields = $this->get_meta_fields_for_post_type();
				if ( ! empty( $meta_fields ) ) {
					$column_repeater->add_control(
						'column_fields_jetengine',
						[
							'label'     => __( 'Meta Field', 'sonaar-music' ),
							'type'      => \Elementor\Controls_Manager::SELECT,
							'default'   => '',
							'groups'    => $meta_fields,
							'condition' => [
								'custom_field_plugin' => 'jetengine',
							],
						]
					);
				}
			}
			$column_repeater->add_control(
				'column_fields_object',
				[
					'label'     => __( 'Object Field', 'sonaar-music' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'default'   => '',
					'groups'    => $this->get_object_fields(),
					'condition' => [
						'custom_field_plugin' => 'object',
					],
				]
			);
			$column_repeater->add_control(
				'column_fields_artwork_size',
				[
					'label' 						=> esc_html__( 'Thumbnail Width', 'sonaar-music' ) . ' (px)',
					'type'							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 50,
						],
					],
					'default' 						=> [
						'unit' => 'px',
						'size' => 50,
						],
				
					'size_units' 					=> [ 'px'],
					'selectors' 					=> [
													'{{WRAPPER}} .sr_cf_track_cover' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' 					=> [
						'column_fields_object' 		=> 'srmp3_cf_album_img',
					],
				]
			);
			$column_repeater->add_control(
				'custom_field_key', [
					'label'     => __( 'Custom Meta Key', 'sonaar-music' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'condition' => [
						'custom_field_plugin' => 'customkey',
					],
				]
			);
			$column_repeater->add_responsive_control(
				'column_width',
				[
					'label' 						=> esc_html__( 'Column Width', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 1,
						],
					],
					'default' 						=> [
							'unit' => 'px',
							'size' => 100,
							],
				]
			);
			$this->add_control(
				'cf_repeater',
				[
					'label' => esc_html__( 'Add New Column', 'sonaar-music' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'prevent_empty' => false,
					'fields' => $column_repeater->get_controls(),
					'title_field' => '{{{ column_name }}}  <# if ( "object" == custom_field_plugin ) { #> :: {{{ column_fields_object }}} <# } #> <# if ( "acf" == custom_field_plugin ) { #> :: {{{ column_fields_acf }}} <# } #> <# if ( "jetengine" == custom_field_plugin ) { #> :: {{{ column_fields_jetengine }}} <# } #> <# if ( "customkey" == custom_field_plugin ) { #> :: {{{ custom_field_key }}} <# } #>',
					/*'condition' 					=> [
						'show_searchbar' 	=> 'true',
					],*/
				]
			);
			$this->add_control(
				'column_notice',
				[
					'label' => esc_html__( 'Important:', 'sonaar-music' ),
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => esc_html__( 'Make sure tracklist is wide enough to contains all your columns. We automatically hide columns (starting with the last one) if tracklist has not enough space.', 'sonaar-music' ),
				]
			);
			$this->add_responsive_control(
				'column_justify',
				[
				'label' 		=> esc_html_x( 'Justify Content', 'Flex Container Control', 'elementor' ),
				'type' 			=> Controls_Manager::CHOOSE,
				'label_block' 	=> true,
				'separator' 	=> 'before',
				'default' 		=> '',
				'options' 		=> [
					'flex-start' => [
						'title' => esc_html_x( 'Start', 'Flex Container Control', 'elementor' ),
						'icon' => 'eicon-flex eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html_x( 'Center', 'Flex Container Control', 'elementor' ),
						'icon' => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end' => [
						'title' => esc_html_x( 'End', 'Flex Container Control', 'elementor' ),
						'icon' => 'eicon-flex eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html_x( 'Space Between', 'Flex Container Control', 'elementor' ),
						'icon' => 'eicon-flex eicon-justify-space-between-h',
					],
					'space-around' => [
						'title' => esc_html_x( 'Space Around', 'Flex Container Control', 'elementor' ),
						'icon' => 'eicon-flex eicon-justify-space-around-h',
					],
					'space-evenly' => [
						'title' => esc_html_x( 'Space Evenly', 'Flex Container Control', 'elementor' ),
						'icon' => 'eicon-flex eicon-justify-space-evenly-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sr-playlist-cf-container' => 'justify-content: {{VALUE}};',
				],
				]
			);
			$this->add_responsive_control(
				'column_align',
				[
					'label' 						=> esc_html__( 'Text Alignment', 'sonaar-music' ),
					'type' 							=> Controls_Manager::CHOOSE,
					'options' 						=> [
						'left'    					=> [
							'title' 				=> esc_html__( 'Left', 'elementor' ),
							'icon' 					=> 'eicon-h-align-left',
						],
						'center' 					=> [
							'title' 				=> esc_html__( 'Center', 'elementor' ),
							'icon' 					=> 'eicon-h-align-center',
						],
						'right' 					=> [
							'title' 				=> esc_html__( 'Right', 'elementor' ),
							'icon' 					=> 'eicon-h-align-right',
						],
					],
					'default' 						=> '',
					'selectors' 					=> [
													'{{WRAPPER}} .sr-playlist-cf-container .sr-playlist-cf-child' => 'text-align: {{VALUE}};',
													'{{WRAPPER}} .sr-playlist-heading-child:not(.sr-playlist-cf--title)' => 'text-align: {{VALUE}}!important;',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'searchbar_cf_typo',
					'label' 						=> esc_html__( 'Column Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'selector' 						=> '{{WRAPPER}} .sr-playlist-cf-container',
				]
			);
			$this->add_control(
				'searchbar_cf_colum_color',
				[
					'label'                		 	=> esc_html__( 'Column Text Color', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'selectors'             		=> [
						'{{WRAPPER}} div.sr-playlist-cf-container' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'cf_white_space',
				[
					'label' 					=> esc_html__( 'White space no wrap', 'sonaar-music' ),
					'type' 						=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 					=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 				=> esc_html__( 'No', 'sonaar-music' ),
					'default' 					=> '',
					'return_value' 				=> 'yes',
					'selectors' 				=> [
												'{{WRAPPER}} .sr-playlist-cf-container .sr-playlist-cf-child' => 'white-space: nowrap;',
						 
				 ],
				]
			);
			$this->add_control(
				'searchbar_cf_colheading_title',
				[
					'label' 						=> esc_html__( 'Column Heading', 'elementor' ),
					'type' 							=> Controls_Manager::HEADING,
					'separator' 					=> 'before',
					'condition' 			=> [
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_control(
				'searchbar_cf_heading_show',
				[
					'label' 							=> esc_html__( 'Show Column Headings', 'sonaar-music' ),
					'type' 								=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 							=> esc_html__( 'Show', 'sonaar-music' ),
					'label_off' 						=> esc_html__( 'Hide', 'sonaar-music' ),
					'return_value' 						=> 'true',
					'default' 							=> 'true',
					'condition' 			=> [
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'searchbar_cf_heading_typo',
					'label' 						=> esc_html__( 'Heading Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'selector' 						=> '{{WRAPPER}} .iron-audioplayer.srp_has_customfields .sr-cf-heading .sr-playlist-heading-child',
					'separator' 					=> 'after',
					'condition' 					=> [
						'searchbar_cf_heading_show' 	=> 'true',
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_control(
				'searchbar_cf_heading_color',
				[
					'label'                		 	=> esc_html__( 'Color', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'selectors'             		=> [
						'{{WRAPPER}} .sr-playlist-heading-child' => 'color: {{VALUE}}',
					],
					'condition' 					=> [
						'searchbar_cf_heading_show' 	=> 'true',
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_control(
				'searchbar_cf_heading_color_hover',
				[
					'label'                		 	=> esc_html__( 'Color Hover', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'selectors'             		=> [
						'{{WRAPPER}} .sr-playlist-heading-child:hover' => 'color: {{VALUE}}',
					],
					'condition' 					=> [
						'searchbar_cf_heading_show' 	=> 'true',
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_control(
				'cf_heading_bottom_border',
				[
					'label'                		 	=> esc_html__( 'Heading Separator', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'selectors'             		=> [
						'{{WRAPPER}} .sr-cf-heading' => 'border-color: {{VALUE}}',
					],
					'condition' 					=> [
						'searchbar_cf_heading_show' 	=> 'true',
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' 							=> 'searchbar_cf_heading_bg',
					'label' 						=> esc_html__( 'Background', 'elementor-sonaar' ),
					'types' 						=> [ 'classic', 'gradient'],
					'exclude' 						=> [ 'image' ],
					'selector' 						=> '{{WRAPPER}} .sr-cf-heading',
					'condition' 					=> [
						'searchbar_cf_heading_show' 	=> 'true',
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_responsive_control(
				'searchbar_cf_heading_pad',
				[
					'label' 						=> esc_html__( 'Heading Padding', 'sonaar-music' ),
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .sr-cf-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 					=> [
						'searchbar_cf_heading_show' 	=> 'true',
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->end_controls_section();
		}

	        /**
	         * STYLE: PLAYLIST
	         * -------------------------------------------------
	         */
				
			$this->start_controls_section(
	            'playlist_style',
	            [
	                'label'                			=> esc_html__( 'Tracklist', 'sonaar-music' ),
					'tab'                   		=> Controls_Manager::TAB_STYLE,
					'conditions' 					=> [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout',
										'operator' => '==',
										'value' => 'skin_button'
									],
									[
										'name' => 'playlist_show_playlist_skin_button',
										'operator' => '!=',
										'value' => ''
									]
								]	
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout',
										'operator' => '!=',
										'value' => 'skin_button'
									],
									[
										'name' => 'playlist_show_playlist',
										'operator' => '!=',
										'value' => ''
									]
								]	
							],
						]
					],
				]
			);
			$this->add_control(
				'trackList_layout',
				[
					'label' 						=> esc_html__( 'Tracklist Layout', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SELECT,
					'options' 				=> [
						'list' 				=> 'List',
						'grid'				=> 'Grid',
					],
					'default'						=> 'list',
				]
			);
			$this->add_responsive_control(
					'grid_column_number',
					[
						'label'					=> esc_html__( 'Columns', 'sonaar-music' ),
						'type' 					=> Controls_Manager::SELECT,
						'options' 				=> [
							'1' 				=> '1',
							'2'					=> '2',
							'3' 				=> '3',
							'4' 				=> '4',
							'5' 				=> '5',
							'6' 				=> '6',
						],
						'default' 				=> '4',
						'tablet_default' 		=> '3',
						'mobile_default' 		=> '2',
						'condition' 			=> [
							'trackList_layout' => 'grid',
						],
						'selectors'             => [
							'{{WRAPPER}} .srp_tracklist_grid .srp_tracklist > ul' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
						],
					]
			);
			$this->add_control(
				'hr12',
				[
					'type' 							=> \Elementor\Controls_Manager::DIVIDER,
					'style' 						=> 'thick',
				]
			);
			$this->add_control(
					'move_playlist_below_artwork',
					[
						'label' 					=> esc_html__( 'Move Tracklist Below Artwork', 'sonaar-music' ),
						'type' 						=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 					=> esc_html__( 'Yes', 'sonaar-music' ),
						'label_off' 				=> esc_html__( 'No', 'sonaar-music' ),
						'return_value' 				=> 'auto',
						'separator'					=> 'after',
						'default' 					=> '',
						'prefix_class'				=> 'sr_playlist_below_artwork_',
						'condition' 				=> [
							'playlist_hide_artwork!' => 'yes',
							'player_layout' 	=> 'skin_float_tracklist',
						],
						'selectors' 				=> [
													'{{WRAPPER}} .sonaar-grid' => 'flex-direction: column;',
													
													//'{{WRAPPER}} .sonaar-Artwort-box' => 'justify-self:center;',
													//'{{WRAPPER}} .sonaar-grid' => 'justify-content:center!important;grid-template-columns:{{VALUE}}!important;',
							 
					 ],
					]
			);
			
			$this->add_control(
				'track_artwork_heading',
				[
					'label' 						=> sprintf( esc_html__( '%1$s Image', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> Controls_Manager::HEADING,
					//'separator' 					=> 'before',
				]
			);
			$this->add_control(
				'track_artwork_show',
				[
					'label' 						=> sprintf( esc_html__( 'Show Thumbnail for Each %1$s', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default'						=> '',
					'return_value' 					=> 'yes',
					'condition' 					=> [
						'trackList_layout!' 			=> 'grid',
					],
				]
			);
			$this->add_control(
				'grid_track_artwork_show',
				[
					'label' 						=> sprintf( esc_html__( 'Show Thumbnail for Each %1$s', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default'						=> 'yes',
					'return_value' 					=> 'yes',
					'condition' 					=> [
						'trackList_layout' 			=> 'grid',
					],
				]
			);
			$this->add_control(
				'track_artwork_play_button',
				[
					'label' 						=> sprintf( esc_html__( 'Play Icon overlay Image', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default'						=> '',
					'return_value' 					=> 'yes',
					'condition' 					=> [
						'trackList_layout!' 		=> 'grid',
						'track_artwork_show' 		=> 'yes'
					],
				]
			);
			$this->add_control(
				'grid_track_artwork_play_button',
				[
					'label' 						=> sprintf( esc_html__( 'Play Icon overlay Image', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default'						=> 'yes',
					'return_value' 					=> 'yes',
					'condition' 					=> [
						'trackList_layout' 			=> 'grid',
						'grid_track_artwork_show' 		=> 'yes'
					],
				]
			);
			$this->add_control(
				'track_artwork_play_on_hover',
				[
					
					'label' 						=> esc_html__( 'Show Play Icon On Hover only', 'sonaar-music' ),
					'type' 						=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 					=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 				=> esc_html__( 'No', 'sonaar-music' ),
					'default' 					=> '',
					'return_value' 				=> 'yes',
					'condition' 					=> [
						'trackList_layout!' 			=> 'grid',
						'track_artwork_play_button' => 'yes',
						'track_artwork_show' 		=> 'yes'
					],
				]
			);
			$this->add_control(
				'grid_track_artwork_play_on_hover',
				[
					
					'label' 						=> esc_html__( 'Show Play Icon On Hover only', 'sonaar-music' ),
					'type' 						=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 					=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 				=> esc_html__( 'No', 'sonaar-music' ),
					'default' 					=> 'yes',
					'return_value' 				=> 'yes',
					'condition' 					=> [
						'trackList_layout' 			=> 'grid',
						'grid_track_artwork_play_button' => 'yes',
						'grid_track_artwork_show' 		=> 'yes'
					],
				]
			);
			$this->add_control(
				'track_artwork_overlay',
				[
					'label'                 		=> esc_html__( 'Image Overlay', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'separator'						=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .srp_tracklist_play_cover .sr_track_cover:after' => 'background: {{VALUE}};',
					],
					'conditions' 					=> [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '!=',
										'value' => 'grid'
									],
									[
										'name' => 'track_artwork_show',
										'operator' => '==',
										'value' => 'yes'
									],
									[
										'name' => 'track_artwork_play_button',
										'operator' => '==',
										'value' => 'yes'
									],
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '==',
										'value' => 'grid'
									],
									[
										'name' => 'grid_track_artwork_show',
										'operator' => '==',
										'value' => 'yes'
									],
									[
										'name' => 'grid_track_artwork_play_button',
										'operator' => '==',
										'value' => 'yes'
									],
								]
							],
						]
					],
				]
			);
			$this->add_control(
				'track_artwork_play_button_color',
				[
					'label'                 		=> esc_html__( 'Play Icon Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'separator'						=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .sr_track_cover .srp_play .sricon-play' => 'color: {{VALUE}};',
													'{{WRAPPER}} .iron-audioplayer .sr_track_cover .srp_play' => 'border-color:{{VALUE}};'
					],
					'conditions' 					=> [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '!=',
										'value' => 'grid'
									],
									[
										'name' => 'track_artwork_show',
										'operator' => '==',
										'value' => 'yes'
									],
									[
										'name' => 'track_artwork_play_button',
										'operator' => '==',
										'value' => 'yes'
									],
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '==',
										'value' => 'grid'
									],
									[
										'name' => 'grid_track_artwork_show',
										'operator' => '==',
										'value' => 'yes'
									],
									[
										'name' => 'grid_track_artwork_play_button',
										'operator' => '==',
										'value' => 'yes'
									],
								]
							],
						]
					],
				]
			);
			$this->add_responsive_control(
				'track_artwork_play_button_scale',
				[
					
					'label' 						=> esc_html__( 'Control Size Scale', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::NUMBER,
					'min' 							=> 0,
					'max' 							=> 10,
					'step' 							=> 0.1,
					'conditions' 					=> [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '!=',
										'value' => 'grid'
									],
									[
										'name' => 'track_artwork_show',
										'operator' => '==',
										'value' => 'yes'
									],
									[
										'name' => 'track_artwork_play_button',
										'operator' => '==',
										'value' => 'yes'
									],
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '==',
										'value' => 'grid'
									],
									[
										'name' => 'grid_track_artwork_show',
										'operator' => '==',
										'value' => 'yes'
									],
									[
										'name' => 'grid_track_artwork_play_button',
										'operator' => '==',
										'value' => 'yes'
									],
								]
							],
						]
					],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .sr_track_cover .srp_play' => 'transform:scale({{SIZE}});',
													'{{WRAPPER}} .srp_tracklist_play_cover_hover .sr-playlist-item:not(:hover):not(.current) .sr_track_cover .srp_play'  => 'transform:scale({{SIZE}}) translateY(30%);',
													'(mobile){{WRAPPER}} .srp_tracklist_play_cover_hover .sr-playlist-item:not(:hover):not(.current) .sr_track_cover .srp_play'  => 'transform:scale({{SIZE}});',
					],
				]
			);
			$this->add_responsive_control(
				'track_artwork_size',
				[
					'label' 						=> esc_html__( 'Thumbnail Width', 'sonaar-music' ) . ' (px)',
					'type'							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 500,
						],
					],
					'size_units' 					=> [ 'px', '%' ],
					'selectors' 					=> [
													//'{{WRAPPER}} .iron-audioplayer .sonaar-grid-2' => 'grid-template-columns: auto {{SIZE}}{{UNIT}};',
													'{{WRAPPER}} .iron-audioplayer .playlist li .sr_track_cover' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' 					=> [
						'track_artwork_show' 		=> 'yes',
						'trackList_layout!' 		=> 'grid',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 							=> 'grid_track_artwork_format', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
					'default' 						=> 'large',
					'separator' 					=> 'none',
					'condition' 					=> [
						'grid_track_artwork_show' 	=> 'yes',
						'trackList_layout' 			=> 'grid',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 							=> 'list_track_artwork_format', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
					'default' 						=> 'thumbnail',
					'separator' 					=> 'none',
					'condition' 					=> [
						'track_artwork_show' 		=> 'yes',
						'trackList_layout!' 		=> 'grid',
					],
				]
			);
			$this->add_control(
				'track_artwork_radius',
				[
					'label' 						=> esc_html__( 'Image Radius', 'elementor' ),
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 200,
						],
					],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .playlist li .sr_track_cover, {{WRAPPER}} .srp_tracklist_play_cover .sr_track_cover:after' => 'border-radius: {{SIZE}}px;',
					],
					'conditions' 					=> [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '!=',
										'value' => 'grid'
									],
									[
										'name' => 'track_artwork_show',
										'operator' => '==',
										'value' => 'yes'
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '==',
										'value' => 'grid'
									],
									[
										'name' => 'grid_track_artwork_show',
										'operator' => '==',
										'value' => 'yes'
									]
								]
							],
						]
					],
				]
			);
			$this->add_control(
				'alignment_options',
				[
					'label' 						=> esc_html__( 'Tracklist Alignments', 'sonaar-music' ),
					'type' 							=> Controls_Manager::HEADING,
					'separator' 					=> 'before',
					'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist',
					],
				]
			);
			$this->add_responsive_control(
				'playlist_justify',
				[
					'label' 						=> esc_html__( 'Tracklist Alignment', 'sonaar-music' ),
					'type' 							=> Controls_Manager::CHOOSE,
					'options' 						=> [
						'flex-start'    					=> [
							'title' 				=> esc_html__( 'Left', 'elementor' ),
							'icon' 					=> 'eicon-h-align-left',
						],
						'center' 					=> [
							'title' 				=> esc_html__( 'Center', 'elementor' ),
							'icon' 					=> 'eicon-h-align-center',
						],
						'flex-end' 					=> [
							'title' 				=> esc_html__( 'Right', 'elementor' ),
							'icon' 					=> 'eicon-h-align-right',
						],
					],
					'default' 						=> 'center',
					'selectors' 					=> [
														'{{WRAPPER}} .iron-audioplayer .sonaar-grid' => 'justify-content: {{VALUE}};',
														'{{WRAPPER}}.sr_playlist_below_artwork_auto .iron-audioplayer .sonaar-grid' => 'align-items:{{VALUE}}',
					],
					'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist'
					],
				]
			);
			$this->add_responsive_control(
				'artwork_align',
				[
					'label' 						=> esc_html__( 'Image Alignment', 'sonaar-music' ),
					'type' 							=> Controls_Manager::CHOOSE,
					'options' 						=> [
						'flex-start'    					=> [
							'title' 				=> esc_html__( 'Left', 'elementor' ),
							'icon' 					=> 'eicon-h-align-left',
						],
						'center' 					=> [
							'title' 				=> esc_html__( 'Center', 'elementor' ),
							'icon' 					=> 'eicon-h-align-center',
						],
						'flex-end' 					=> [
							'title' 				=> esc_html__( 'Right', 'elementor' ),
							'icon' 					=> 'eicon-h-align-right',
						],
					],
					'default' 						=> '',
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .sonaar-Artwort-box' => 'justify-content: {{VALUE}};',
													//'{{WRAPPER}} .iron-audioplayer .sonaar-Artwort-box' => 'justify-self: {{VALUE}}!important; text-align: {{VALUE}};',
					],
					'conditions' 					=> [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'sr_player_on_artwork',
								'operator' => '==',
								'value' => ''
							],
							[
								'name' => 'playlist_hide_artwork',
								'operator' => '==',
								'value' => ''
							],
							[
								'name' => 'playlist_show_playlist',
								'operator' => '!=',
								'value' => ''
							],
							[
								'name' => 'move_playlist_below_artwork',
								'operator' => '!=',
								'value' => ''
							],
							[
								'name' => 'player_layout',
								'operator' => '!=',
								'value' => 'skin_button'
							],
						]
					],
				]
			);
			$this->add_responsive_control(
				'playlist_width',
				[
					'label' 						=> esc_html__( 'Tracklist Width', 'sonaar-music' ) . ' (px)',
					'type'							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 2000,
						],
					],
					'size_units' 					=> [ 'px', 'vw', '%' ],
					'selectors' 					=> [
													//'{{WRAPPER}} .iron-audioplayer .sonaar-grid-2' => 'grid-template-columns: auto {{SIZE}}{{UNIT}};',
													'{{WRAPPER}} .iron-audioplayer .playlist, {{WRAPPER}} .iron-audioplayer .sonaar-Artwort-box, {{WRAPPER}} .iron-audioplayer .buttons-block' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist'
					],
					'render_type'					=> 'template',
				]
			);
			$this->add_control(
				'title_options',
				[
					'label' 						=> sprintf( esc_html__( '%1$s Title', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('playlist/podcast')) ),
					'type' 							=> Controls_Manager::HEADING,
					'separator' 					=> 'before',
					'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist',
					],
				]
			);
			$this->add_control(
				'title_html_tag_playlist',
				[
					'label' => esc_html__( 'HTML Title Tag', 'sonaar-music' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
						'div' => 'div',
						'span' => 'span',
						'p' => 'p',
					],
					'default' => 'h3',
					'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist'
					],
				]
			);
			$this->add_control(
				'title_btshow',
				[
					'label' 						=> esc_html__( 'Hide Title', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'none',
					'selectors' 					=> [
						 							'{{WRAPPER}} .playlist .sr_it-playlist-title' => 'display:{{VALUE}};',
					 ],
					 'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist'
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'title_typography',
					'label' 						=> esc_html__( 'Title Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'condition' 					=> [
						'title_btshow' 				=> '',
						'player_layout' 	=> 'skin_float_tracklist'
					],
					'selector' 						=> '{{WRAPPER}} .iron-audioplayer .sr_it-playlist-title',
				]
			);
			$this->add_control(
				'title_color',
				[
					'label'                			=> esc_html__( 'Title Color', 'sonaar-music' ),
					'type'                 			=> Controls_Manager::COLOR,
					'default'               		=> '',
					'condition' 					=> [
						'title_btshow'				=> '',
						'player_layout' 	=> 'skin_float_tracklist'
					],
					'selectors'             		=> [
													'{{WRAPPER}} .playlist .sr_it-playlist-title, {{WRAPPER}} .srp_player_meta' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_responsive_control(
				'title_align',
				[
					'label' 						=> esc_html__( 'Title Alignment', 'sonaar-music' ),
					'type' 							=> Controls_Manager::CHOOSE,
					'options' 						=> [
						'left'    					=> [
							'title' 				=> esc_html__( 'Left', 'elementor' ),
							'icon' 					=> 'eicon-h-align-left',
						],
						'center' 					=> [
							'title' 				=> esc_html__( 'Center', 'elementor' ),
							'icon' 					=> 'eicon-h-align-center',
						],
						'right' 					=> [
							'title' 				=> esc_html__( 'Right', 'elementor' ),
							'icon' 					=> 'eicon-h-align-right',
						],
					],
					'default' 						=> '',
					'condition' 					=> [
						'title_btshow'				=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
					'selectors' 					=> [
													'{{WRAPPER}} .sr_it-playlist-title, {{WRAPPER}} .sr_it-playlist-artists, {{WRAPPER}} .srp_subtitle' => 'text-align: {{VALUE}}!important;',
													'{{WRAPPER}} .iron-audioplayer .srp_player_meta' => 'justify-content: {{VALUE}};',
					],
				]
			);
			$this->add_responsive_control(
				'title_indent',
				[
					
					'label' 						=> esc_html__( 'Title Indent', 'sonaar-music' ) . ' (px)',
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'min' 					=> -500,
						],
					],
					'condition' 					=> [
						'title_btshow' 				=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
					'selectors' 					=> [
													'{{WRAPPER}} .sr_it-playlist-title' => 'margin-left: {{SIZE}}px;',
													'{{WRAPPER}} .sr_it-playlist-artists' => 'margin-left: {{SIZE}}px;',
													'{{WRAPPER}} .srp_subtitle' => 'margin-left: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'subtitle_options',
				[
					'label' 						=> sprintf( esc_html__( '%1$s Subtitle', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('playlist/podcast')) ),
					'type' 							=> Controls_Manager::HEADING,
					'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist',
					],
					'separator' 					=> 'before',
				]
			);
			$this->add_control(
				'subtitle_btshow',
				[
					'label' 						=> esc_html__( 'Hide Subtitle', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'none',
					'selectors' 					=> [
							 						'{{WRAPPER}} .playlist .srp_subtitle' => 'display:{{VALUE}}!important;',
					 ],
					 'condition' 					=> [
						'player_layout' 	=> 'skin_float_tracklist',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'subtitle_typography',
					'label' 						=> esc_html__( 'Subtitle Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'condition' 					=> [
						'subtitle_btshow' 			=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
					'selector' 						=> '{{WRAPPER}} .playlist .srp_subtitle',
					
				]
			);
			$this->add_control(
				'subtitle-color',
				[
					'label'                		 	=> esc_html__( 'Subtitle Color', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'condition' 					=> [
						'subtitle_btshow' 			=> '',
						'player_layout' 	=> 'skin_float_tracklist',
					],
					'selectors'             		=> [
													'{{WRAPPER}} .playlist .srp_subtitle' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'track_options',
				[
					'label' 						=> esc_html__( 'Tracklist', 'elementor' ),
					'type' 							=> Controls_Manager::HEADING,
					'separator' 					=> 'before',
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'track_title_typography',
					'label' 						=> sprintf( esc_html__( '%1$s Title Typography', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'selector' 						=> '{{WRAPPER}} .iron-audioplayer .playlist .audio-track, {{WRAPPER}} .iron-audioplayer .playlist .track-number, {{WRAPPER}} .iron-audioplayer .playlist',
				]
			);
			$this->add_control(
				'tracktitle_white_space',
				[
					'label' 					=> esc_html__( 'Wrap title on multiple lines', 'sonaar-music' ),
					'type' 						=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 					=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 				=> esc_html__( 'No', 'sonaar-music' ),
					'default' 					=> '',
					'return_value' 				=> 'yes',
					'selectors' 				=> [
												'{{WRAPPER}} .playlist .tracklist-item-title' => 'white-space: normal;overflow:visible;text-overflow:initial;',
						 
				 ],
				]
			);
			$this->start_controls_tabs( 'tabs_tracktitle_style' );
			$this->start_controls_tab(
				'tab_tracktitle_normal',
				[
					'label' 						=> esc_html__( 'Normal', 'elementor' ),
				]
			);
			$this->add_control(
				'track_title_color',
				[
					'label' 						=> sprintf( esc_html__( '%1$s Title Color', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .iron-audioplayer .playlist .audio-track, {{WRAPPER}} .iron-audioplayer .playlist .track-number,  {{WRAPPER}} .iron-audioplayer .player, {{WRAPPER}} .iron-audioplayer .sr-playlist-item .srp_noteButton, {{WRAPPER}} .srp_track_description, {{WRAPPER}} .sr-playlist-cf-container, {{WRAPPER}} .srp_notfound' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'track_bgcolor',
				[
					'label'                			=> esc_html__( 'Tracklist Item Background', 'sonaar-music' ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .sr-playlist-item'=> 'background: {{VALUE}}',
					],
					'condition' 					=> [
						'player_layout' 	=> 'skin_boxed_tracklist'
					],
				]
			);
			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_tracktitle_hover',
				[
					'label' 						=> esc_html__( 'Hover', 'elementor' ),
				]
			);
				$this->add_control(
					'tracklist_hover_color',
					[
						'label' 						=> sprintf( esc_html__( '%1$s Title Color', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
						'type'                  		=> Controls_Manager::COLOR,
						'default'               		=> '',
						'selectors'             		=> [
														'{{WRAPPER}} .iron-audioplayer .playlist .audio-track:hover, {{WRAPPER}} .iron-audioplayer .playlist .audio-track:hover .track-number, {{WRAPPER}} .iron-audioplayer .playlist a.song-store:not(.sr_store_wc_round_bt):hover, {{WRAPPER}} .iron-audioplayer .playlist .current a.song-store:not(.sr_store_wc_round_bt):hover' => 'color: {{VALUE}}',
													],
					]
				);
				$this->add_control(
					'track_bgcolor_hover',
					[
						'label'                			=> esc_html__( 'Tracklist Item Background', 'sonaar-music' ),
						'type'                 		 	=> Controls_Manager::COLOR,
						'default'               		=> '',
						'selectors'            		 	=> [
														'{{WRAPPER}} .sr-playlist-item:hover'=> 'background-color: {{VALUE}};',
						],
					]
				);
				
				$this->add_control(
					"track_translate_popover_hover",
					[
						'label' => esc_html__( 'Transform FX', 'elementor' ),
						'type' => Controls_Manager::POPOVER_TOGGLE,
					
					]
				);
				$this->start_popover();
				$this->add_responsive_control(
					'track_scale_hover',
					[
						
						'label' 						=> esc_html__( 'Scale', 'sonaar-music' ),
						'type' 							=> \Elementor\Controls_Manager::NUMBER,
						'min' 							=> 0,
						'max' 							=> 10,
						'step' 							=> 0.01,
						'default' 						=> 1,
						'condition' => [
							"track_translate_popover_hover!" => '',
						],
						'selectors' 					=> [
														'{{WRAPPER}} .sr-playlist-item:hover' => '--srp-trackhover-scale:{{SIZE}};',
						],
					]
				);
				$this->add_responsive_control(
					"track_translateX_hover",
					[
						'label' => esc_html__( 'Offset X', 'elementor' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => ['px' ],
						'range' => [
							
							'px' => [
								'min' => -200,
								'max' => 200,
							],
						],
						'condition' => [
							"track_translate_popover_hover!" => '',
						],
						'selectors' => [
							"{{WRAPPER}} .sr-playlist-item:hover" => '--srp-trackhover-translateX: {{SIZE}}{{UNIT}};',
						],
						'frontend_available' => true,
					]
				);
	
				$this->add_responsive_control(
					"track_translateY_hover",
					[
						'label' => esc_html__( 'Offset Y', 'elementor' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => ['px' ],
						'range' => [
							
							'px' => [
								'min' => -20,
								'max' => 20,
							],
						],
						'condition' => [
							"track_translate_popover_hover!" => '',
						],
						'selectors' => [
							"{{WRAPPER}} .sr-playlist-item:hover" => '--srp-trackhover-translateY: {{SIZE}}{{UNIT}};',
						],
						'frontend_available' => true,
					]
				);
				
			$this->end_popover();
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'track_box_shadow_hover',
					'selector' 						=> '{{WRAPPER}}  .sr-playlist-item:hover',
				]
			);
				
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_tracktitle_active',
				[
					'label' 						=> esc_html__( 'Active', 'elementor' ),
				]
			);
				$this->add_control(
					'tracklist_active_color',
					[
						'label' 						=> sprintf( esc_html__( '%1$s Title Color', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
						'type'                 			=> Controls_Manager::COLOR,
						'default'              			=> '',
						'selectors'             		=> [
														'{{WRAPPER}} .iron-audioplayer.audio-playing .playlist .current .audio-track, {{WRAPPER}} .iron-audioplayer.audio-playing .playlist .current .audio-track .track-number, {{WRAPPER}} .iron-audioplayer.audio-playing .playlist .current .audio-track .srp_trackartist' => 'color: {{VALUE}}',
						],
					]
				);
				$this->add_control(
					'track_bgcolor_active',
					[
						'label'                			=> esc_html__( 'Tracklist Item Background', 'sonaar-music' ),
						'type'                 		 	=> Controls_Manager::COLOR,
						'default'               		=> '',
						'selectors'            		 	=> [
														'{{WRAPPER}} .audio-playing .current.sr-playlist-item'=> 'background-color: {{VALUE}};',
						],
					]
				);
				$this->add_control(
					"track_translate_popover_active",
					[
						'label' => esc_html__( 'Transform FX', 'elementor' ),
						'type' => Controls_Manager::POPOVER_TOGGLE,
					
					]
				);
				$this->start_popover();
					$this->add_responsive_control(
						'track_scale_active',
						[
							
							'label' 						=> esc_html__( 'Scale', 'sonaar-music' ),
							'type' 							=> \Elementor\Controls_Manager::NUMBER,
							'min' 							=> 0,
							'max' 							=> 10,
							'step' 							=> 0.01,
							'default' 						=> 1,
							'condition' => [
								"track_translate_popover_active!" => '',
							],
							'selectors' 					=> [
															'{{WRAPPER}} .audio-playing .current.sr-playlist-item' => '--srp-trackactive-scale:{{SIZE}};',
							],
						]
					);
					$this->add_responsive_control(
						"track_translateX_active",
						[
							'label' => esc_html__( 'Offset X', 'elementor' ),
							'type' => Controls_Manager::SLIDER,
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => -200,
									'max' => 200,
								],
							],
							'condition' => [
								"track_translate_popover_active!" => '',
							],
							'selectors' => [
								"{{WRAPPER}} .audio-playing .current.sr-playlist-item" => '--srp-trackactive-translateX: {{SIZE}}{{UNIT}};',
							],
							'frontend_available' => true,
						]
					);
		
					$this->add_responsive_control(
						"track_translateY_active",
						[
							'label' => esc_html__( 'Offset Y', 'elementor' ),
							'type' => Controls_Manager::SLIDER,
							'size_units' => ['px' ],
							'range' => [
								'px' => [
									'min' => -20,
									'max' => 20,
								],
							],
							'condition' => [
								"track_translate_popover_active!" => '',
							],
							'selectors' => [
								"{{WRAPPER}} .audio-playing .current.sr-playlist-item" => '--srp-trackactive-translateY: {{SIZE}}{{UNIT}};',
							],
							'frontend_available' => true,
						]
					);
				$this->end_popover();
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'track_box_shadow',
						'selector' 						=> '{{WRAPPER}} .audio-playing .current.sr-playlist-item',
					]
				);
				$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->add_control(
				'tracklist_transition',
				[
					'label' => esc_html__( 'Transition Duration (ms)', 'sonaar-music' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 50,
							'max' => 2000,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .iron-audioplayer .playlist li' => 'transition: all {{SIZE}}ms',
					],
				]
			);
			if( Sonaar_Music::get_option('show_artist_name', 'srmp3_settings_general') ){
				$this->add_control(
					'artist_hide',
					[
						'label' 					=> esc_html__( 'Hide Artist Name', 'sonaar-music' ),
						'type' 						=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 					=> esc_html__( 'Hide', 'sonaar-music' ),
						'label_off' 				=> esc_html__( 'Show', 'sonaar-music' ),
						'return_value' 				=> 'true',
						'selectors' => [
							'{{WRAPPER}} .iron-audioplayer .srp_trackartist' => 'display:none;',
						],
						'separator' 				=> 'before',
						'default'					=> '',
					]
				);
				$this->add_control(
					'artist_wrap',
					[
						'label' 					=> esc_html__( 'Display Artist Name below Track Title', 'sonaar-music' ),
						'type' 						=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 					=> esc_html__( 'Yes', 'sonaar-music' ),
						'label_off' 				=> esc_html__( 'No', 'sonaar-music' ),
						'return_value' 				=> 'true',
						'default'					=> '',
						'condition' 			=> [
							'artist_hide!' => 'true',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' 							=> 'artist_typography',
						'label' 						=> esc_html__( 'Artist Name Typography', 'sonaar-music' ),
						'scheme' 						=> Typography::TYPOGRAPHY_1,
						'selector' 						=> '{{WRAPPER}} .iron-audioplayer .srp_trackartist',
						'condition' 			=> [
							'artist_hide!' => 'true',
						],
					]
				);
				$this->add_control(
					'artist_color',
					[
						'label'                			=> esc_html__( 'Artist Name Color', 'sonaar-music' ),
						'type'                 			=> Controls_Manager::COLOR,
						'default'               		=> '',
						'selectors'             		=> [
														'{{WRAPPER}} .iron-audioplayer .srp_trackartist' => 'color: {{VALUE}}',
						],
						'condition' 			=> [
							'artist_hide!' => 'true',
						],
					]
				);
			}
			$this->add_control(
				'track_separator_color',
				[
					'label' 						=> sprintf( esc_html__( '%1$s Separator Color', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> Controls_Manager::COLOR,
					'separator' 					=> 'before',
					'default' 						=> '',
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .playlist ul.srp_list > li' => 'border-bottom: solid 1px {{VALUE}};',
					],
					'condition' 			=> [
						'trackList_layout!' => 'grid',
					],
				]
			);
			$this->add_control(
				'track_list_linked',
				[
					'label' 						=> sprintf( esc_html__( 'Link title to the %1$s page', 'sonaar-music' ), Sonaar_Music_Admin::sr_GetString('playlist') ),
					'type' 						=> Controls_Manager::SELECT,
					'options' => [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_post_link', 'srmp3_settings_widget_player') ),
						'true' => esc_html__( 'Yes', 'sonaar-music' ),
						'false' => esc_html__( 'No', 'sonaar-music' ),
					],
					'default'					=> 'default',
					'conditions' 					=> [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'playlist_source',
								'operator' => '!=',
								'value' => 'from_elementor'
							],
							[
								'relation' => 'or',
								'terms' => [
									[
										'relation' => 'and',
										'terms' => [
											[
												'name' => 'playlist_show_playlist',
												'operator' => '!=',
												'value' => ''
											],
											[
												'name' => 'player_layout',
												'operator' => '!=',
												'value' => 'skin_button'
											]
										]
									],
									[
										'relation' => 'and',
										'terms' => [
											[
												'name' => 'playlist_show_playlist_skin_button',
												'operator' => '!=',
												'value' => ''
											],
											[
												'name' => 'player_layout',
												'operator' => '==',
												'value' => 'skin_button'
											]
										]
									],
								]
							]
						]
					],

				]
			);
			$this->add_responsive_control(
				'tracklist_spacing',
				[
					'label' 						=> sprintf( esc_html__( '%1$s Spacing', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 50,
						],
					],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer[data-playertemplate="skin_float_tracklist"]:not(.srp_tracklist_grid) .playlist .sr-playlist-item' => 'padding-top: {{SIZE}}px;padding-bottom: {{SIZE}}px;',
													'{{WRAPPER}} .iron-audioplayer[data-playertemplate="skin_boxed_tracklist"]:not(.srp_tracklist_grid) .sr-playlist-item + .sr-playlist-item' => 'margin-top: {{SIZE}}px;',
													'{{WRAPPER}} .iron-audioplayer[data-playertemplate="skin_boxed_tracklist"] .sr-cf-heading' => 'margin-bottom: {{SIZE}}px;',
													'{{WRAPPER}} .srp_tracklist_grid .srp_tracklist > ul' => 'gap: {{SIZE}}px;',
					],
				]
			);
			$this->add_control(
				'track_radius',
				[
					'label' 						=> esc_html__( 'Tracklist Item Radius', 'elementor' ),
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 200,
						],
					],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .playlist li.sr-playlist-item' => 'border-radius: {{SIZE}}px;',
					],
				]
			);
			$this->add_responsive_control(
				'track_padding',
				[
					'label' 						=> esc_html__( 'Tracklist Item Padding', 'sonaar-music' ) . ' (px)', 
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer[data-playertemplate="skin_boxed_tracklist"] .playlist li.sr-playlist-item' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
													'{{WRAPPER}} .iron-audioplayer[data-playertemplate="skin_boxed_tracklist"].srp_tracklist_grid .sr_track_cover' => 'width:calc(100% + {{RIGHT}}{{UNIT}} + {{LEFT}}{{UNIT}})!important; margin-left: calc(-1 * {{LEFT}}{{UNIT}});  margin-top: calc(-1 * {{TOP}}{{UNIT}}); margin-bottom: {{TOP}}{{UNIT}};',
												],
					'condition' 					=> [
						'player_layout' 	=> 'skin_boxed_tracklist'
					],
				]
			);
			$this->add_control(
				'play_pause_bt_show',
				[
					'label' 						=> esc_html__( 'Hide Play/Pause Button', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default'						=> '',
					'return_value' 					=> 'yes',
					'separator' 					=> 'before',
					'selectors' => [
						'{{WRAPPER}} .sr-playlist-item .sricon-play' => 'display:none;',
						'{{WRAPPER}} .iron-audioplayer .track-number' => 'padding-left: 0 !important;',
					],
					'conditions' 					=> [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '!=',
										'value' => 'grid'
									],
									[
										'name' => 'track_artwork_play_button',
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '==',
										'value' => 'grid'
									],
									[
										'name' => 'grid_track_artwork_play_button',
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							],
						]
					],
				]
			);
			$this->add_control(
				'tracklist_controls_color',
				[
					'label'                			=> esc_html__( 'Play/Pause Button Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'              		 	=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .sr-playlist-item .sricon-play' => 'color: {{VALUE}}',
					],
					'conditions' 					=> [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '!=',
										'value' => 'grid'
									],
									[
										'name' => 'play_pause_bt_show',
										'operator' => '==',
										'value' => ''
									],
									[
										'name' => 'track_artwork_play_button',
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '==',
										'value' => 'grid'
									],
									[
										'name' => 'play_pause_bt_show',
										'operator' => '==',
										'value' => ''
									],
									[
										'name' => 'grid_track_artwork_play_button',
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							],
						]
					],
				]
			);
			$this->add_responsive_control(
				'tracklist_controls_size',
				[
					'label' => esc_html__( 'Play/Pause Button Size', 'sonaar-music' ) . ' (px)',
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .sr-playlist-item .track-number .sricon-play:before' => 'font-size: {{SIZE}}px;',
						'{{WRAPPER}} .iron-audioplayer .track-number' => 'padding-left: calc({{SIZE}}px + 12px);',
						'{{MOBILE}}{{WRAPPER}} .iron-audioplayer .srp_tracklist-item-date' => 'padding-left: calc({{SIZE}}px + 12px);',
					],
					'conditions' 					=> [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '!=',
										'value' => 'grid'
									],
									[
										'name' => 'play_pause_bt_show',
										'operator' => '==',
										'value' => ''
									],
									[
										'name' => 'track_artwork_play_button',
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'trackList_layout',
										'operator' => '==',
										'value' => 'grid'
									],
									[
										'name' => 'play_pause_bt_show',
										'operator' => '==',
										'value' => ''
									],
									[
										'name' => 'grid_track_artwork_play_button',
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							],
						]
					],
				]
			);
			if( Sonaar_Music::get_option('hide_track_number', 'srmp3_settings_widget_player') != 'true') {
				$this->add_control(
					'hide_number_btshow',
					[
						'label' 						=> sprintf( esc_html__( 'Hide %1$s Number', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
						'type' 							=> Controls_Manager::SWITCHER,
						'default' 						=> '',
						'separator' 					=> 'before',
						'return_value' 					=> 'none',
						'selectors' 					=> [
														'{{WRAPPER}} .iron-audioplayer .track-number .number' => 'display:{{VALUE}};',
														'{{WRAPPER}} .iron-audioplayer .track-number' => 'padding-right:0;',
						],
						'condition' 			=> [
							'trackList_layout!' => 'grid',
						],
					]
				);
				$this->add_control(
					'hide_number_btshow_grid',
					[
						'label' 						=> sprintf( esc_html__( 'Hide %1$s Number', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
						'type' 							=> Controls_Manager::SWITCHER,
						'default' 						=> '1',
						'separator' 					=> 'before',
						'return_value' 					=> '1',
						'selectors' 					=> [
														'{{WRAPPER}} .iron-audioplayer .track-number .number' => 'display:none;',
														'{{WRAPPER}} .iron-audioplayer .track-number' => 'padding-right:0;',
						],
						'condition' 			=> [
							'trackList_layout' => 'grid',
						],
					]
				);
			}
			$this->add_control(
					'hide_time_duration',
					[
						'label' 					=> sprintf( esc_html__( 'Hide %1$s Duration', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
						'type' 						=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 					=> esc_html__( 'Yes', 'sonaar-music' ),
						'label_off' 				=> esc_html__( 'No', 'sonaar-music' ),
						'return_value' 				=> 'none',
						'separator' 				=> 'before',
						'default'					=> '',
						'selectors' 				=> [
							 							'{{WRAPPER}} .iron-audioplayer .tracklist-item-time' => 'display:{{VALUE}};'
					 ],
					]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'duration_typography',
					'label' 						=> esc_html__( 'Duration Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'condition' 					=> [
						'hide_time_duration' 		=> '',
					],
					'selector' 						=> '{{WRAPPER}} .iron-audioplayer .tracklist-item-time',
				]
			);
			$this->add_control(
				'duration_color',
				[
					'label'                			=> esc_html__( 'Duration Color', 'sonaar-music' ),
					'type'                 			=> Controls_Manager::COLOR,
					'default'               		=> '',
					'condition' 					=> [
						'hide_time_duration' 		=> '',
					],
					'selectors'             		=> [
													'{{WRAPPER}} .tracklist-item-time' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'show_track_publish_date',
				[
					'label' 					=> esc_html__( 'Show Publish Date', 'sonaar-music' ),
					'type' 						=> Controls_Manager::SELECT,
					'options' => [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_show_track_publish_date', 'srmp3_settings_widget_player') ),
						'true' => esc_html__( 'Yes', 'sonaar-music' ),
						'false' => esc_html__( 'No', 'sonaar-music' ),
					],
					'separator' 				=> 'before',
					'default'					=> 'default',
					'condition' 					=> [
						'playlist_show_playlist!' 	=> '',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'date_typography',
					'label' 						=> esc_html__( 'Publish Date Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'selector' 						=> '{{WRAPPER}} .iron-audioplayer .srp_tracklist-item-date',
					'condition' 					=> [
						'playlist_show_playlist!' 	=> '',
					],
				]
			);
			$this->add_control(
				'date_color',
				[
					'label'                			=> esc_html__( 'Publish Date Color', 'sonaar-music' ),
					'type'                 			=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .srp_tracklist-item-date' => 'color: {{VALUE}}',
					],
					'condition' 					=> [
						'playlist_show_playlist!' 	=> '',
					],
				]
			);
			$this->add_control(
				'hide_trackdesc',
				[
					'label' 					=> sprintf( esc_html__( 'Hide %1$s Description', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 						=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' => esc_html__( 'No', 'sonaar-music' ),
					'return_value' => '1',
					'default' => '0',
					'separator' 				=> 'before',
					
				]
			);
			$this->add_control(
				'track_desc_postcontent',
				[
					'label' 						=> esc_html__( 'Use Post Content', 'sonaar-music' ),
					'description' 					=> esc_html__( 'We will use the post content for the description instead of the track description field', 'sonaar-music' ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> 'true',
					'default'						=> '',
					/*'condition'						=> [
						'hide_trackdesc!' 			=> '1',
					],*/
					
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'track_desc_typography',
					'label' 						=> esc_html__( 'Description Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'selector' 						=> '{{WRAPPER}} div.srp_track_description',
					'condition' => [
						'hide_trackdesc!' => '1',
					],
				]
			);
			$this->add_control(
				'track_desc_color',
				[
					'label'                			=> esc_html__( 'Description Color', 'sonaar-music' ),
					'type'                 			=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'             		=> [
													'{{WRAPPER}} div.srp_track_description' => 'color: {{VALUE}}',
					],
					'condition' => [
						'hide_trackdesc!' => '1',
					],
				]
			);
			$this->add_control(
				'track_desc_lenght',
				[
					'label' => esc_html__( 'Description Lenght', 'sonaar-music' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 100000,
					'step' => 1,
					'default' => 55,
					'condition' => [
						'hide_trackdesc!' => '1',
					],
				]
			);
			$this->add_control(
				'strip_html_track_desc',
				[
					'label' => esc_html__( 'Strip HTML', 'sonaar-music' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' => esc_html__( 'No', 'sonaar-music' ),
					'return_value' => '1',
					'default' => '1',
					'condition' => [
						'hide_trackdesc!' => '1',
					],
				]
			);
			$this->add_control(
				'cta_icon_options',
				[
					'label' 						=> esc_html__( 'Call-to-Action Buttons', 'elementor' ),
					'type' 							=> Controls_Manager::HEADING,
					'separator' 					=> 'before',
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '!=',
					            'value' => 'from_elementor'
					        ],
					    ]
					] 
				]
			);
			$this->add_control(
				'hide_track_market',
				[
					'label'							=> sprintf( esc_html__( 'Hide %1$s\'s Call-to-Action(s)', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value'					=> 'yes',
					'default' 						=> '',
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '!=',
					            'value' => 'from_elementor'
					        ],
					    ]
					] 
				]
			);
			$this->add_control(
				'view_icons_alltime',
				[
					'label' 						=> esc_html__( 'Display Icons without Popover', 'sonaar-music' ),
					'description' 					=> 'Turn off if you have a lot of icons',
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> 'yes',
					'default' 						=> 'yes',
					'prefix_class'					=> 'sr_track_inline_cta_bt__',
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '!=',
					            'value' => 'from_elementor'
					        ],
					        [
					            'name' => 'hide_track_market',
					            'operator' => '==',
					            'value' => ''
					        ],
					    ]
					],
					
				]
			);
			$this->add_control(
				'popover_icons_store',
				[
					'label' 						=> esc_html__( 'Popover Icon Color', 'sonaar-music' ),
					'type'							=> Controls_Manager::COLOR,
					'default' 						=> '',
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '!=',
					            'value' => 'from_elementor'
					        ],
					        [
					            'name' => 'hide_track_market',
					            'operator' => '==',
					            'value' => ''
					        ],
					    ]
					],
					'selectors'             		=> [
							'{{WRAPPER}} .iron-audioplayer .playlist .song-store-list-menu .fa-ellipsis-v, {{WRAPPER}} .iron-audioplayer .store-list .srp_ellipsis' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'popover_icons_bg_store',
				[
					'label' 						=> esc_html__( 'Popover Background Color', 'sonaar-music' ),
					'type'							=> Controls_Manager::COLOR,
					'default' 						=> '',
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '!=',
					            'value' => 'from_elementor'
					        ],
					        [
					            'name' => 'hide_track_market',
					            'operator' => '==',
					            'value' => ''
					        ],
					    ]
					],
					'selectors'             		=> [
							'{{WRAPPER}} .srp_extended .song-store-list-menu' => 'background-color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'cta_track_show_label',
				[
					'label' 						=> esc_html__( 'Show Text label', 'sonaar-music' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('show_label', 'srmp3_settings_widget_player') ),
						'true' => esc_html__( 'Yes', 'sonaar-music' ),
						'false' => esc_html__( 'No', 'sonaar-music' ),
					],
					'default' => 'default',
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'tracklist_label_typography',
					'label' 						=> esc_html__( 'Button Label Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '!=',
					            'value' => 'from_elementor'
					        ],
					        [
					            'name' => 'hide_track_market',
					            'operator' => '==',
					            'value' => ''
					        ],
					    ]
					],
					'selector' 						=> '{{WRAPPER}} .iron-audioplayer .song-store-list-container a.song-store',
				]
			);
			$this->add_control(
				'tracklist_icons_color',
				[
					'label'                 		=> esc_html__( 'Icons Color When No Label Present', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '!=',
					            'value' => 'from_elementor'
					        ],
					        [
					            'name' => 'hide_track_market',
					            'operator' => '==',
					            'value' => ''
					        ],
					    ]
					],
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .playlist a.song-store:not(.sr_store_wc_round_bt)' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'wc_icons_color',
				[
					'label'                 		=> esc_html__( 'Label Button Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'conditions' 					=> [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'playlist_source',
								'operator' => '!=',
								'value' => 'from_elementor'
							],
							[
								'name' => 'hide_track_market',
								'operator' => '==',
								'value' => ''
							],
						]
					],
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .song-store-list-container a.song-store.sr_store_wc_round_bt' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'wc_icons_bg_color',
				[
					'label'                 		=> esc_html__( 'Label Button Background Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'conditions' 					=> [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'playlist_source',
								'operator' => '!=',
								'value' => 'from_elementor'
							],
							[
								'name' => 'hide_track_market',
								'operator' => '==',
								'value' => ''
							],
						]
					],
					'selectors'             		=> [
													'{{WRAPPER}} .iron-audioplayer .song-store-list-container a.song-store.sr_store_wc_round_bt' => 'background-color: {{VALUE}}',
					],
				]
			);
			$this->add_responsive_control(
				'tracklist_icons_spacing',
				[
					'label' 						=> esc_html__( 'Button Spacing', 'elementor' ) . ' (px)',
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 50,
						],
					],
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '!=',
					            'value' => 'from_elementor'
					        ],
					        [
					            'name' => 'hide_track_market',
					            'operator' => '==',
					            'value' => ''
					        ],
					    ]
					],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .song-store-list-container' => 'column-gap: {{SIZE}}px;',
					],
				]
			);
			$this->add_responsive_control(
				'tracklist_icons_size',
				[
					'label' 						=> esc_html__( 'Icon Button Size (when no label is present)', 'sonaar-music' ) . ' (px)', 
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 50,
						],
					],
					'conditions' 					=> [
					    'relation' => 'and',
					    'terms' => [
					        [
					            'name' => 'playlist_source',
					            'operator' => '!=',
					            'value' => 'from_elementor'
					        ],
					        [
					            'name' => 'hide_track_market',
					            'operator' => '==',
					            'value' => ''
					        ],
					    ]
					],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .playlist .store-list .song-store .fab, {{WRAPPER}} .iron-audioplayer .playlist .store-list .song-store .fas' => 'font-size: {{SIZE}}px;',
					],
				]
			);
			$this->add_control(
				'cta_playlist_options',
				[
					'label' 						=> esc_html__( 'Tracklist Container', 'elementor' ),
					'type' 							=> Controls_Manager::HEADING,
					'separator' 					=> 'before',
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'playlist_bgcolor',
					'label' => esc_html__( 'Background', 'elementor-sonaar' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .iron-audioplayer[data-playertemplate="skin_boxed_tracklist"] .playlist, {{WRAPPER}} .iron-audioplayer[data-playertemplate="skin_float_tracklist"] .sonaar-grid',
				]
			);
			
			$this->add_responsive_control(
				'playlist_margin',
				[
					'label' 						=> esc_html__( 'Container Margin', 'sonaar-music' ) . ' (px)', 
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .playlist' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 					=> [
						'player_layout' 	=> ['skin_float_tracklist', 'skin_button']
					],
				]
			);
			$this->add_responsive_control(
				'playlist_padding',
				[
					'label' 						=> esc_html__( 'Container Padding', 'sonaar-music' ) . ' (px)', 
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .playlist' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 					=> [
						'player_layout' 	=> 'skin_boxed_tracklist'
					],
				]
			);
			$this->add_responsive_control(
				'tracklist_margin',
				[
					'label' 						=> esc_html__( 'Tracklist Margin', 'sonaar-music' ) . ' (px)', 
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .srp_tracklist' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'player_layout' 	=> ['skin_float_tracklist', 'skin_button']
					],
				]
			);
			$this->end_controls_section();
			$this->start_controls_section(
				'pagination_style',
				[
					'label'                 		=> esc_html__( 'Pagination', 'sonaar-music' ),
					'tab'                   		=> Controls_Manager::TAB_STYLE,
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '!=',
										'value' => 'skin_button'
									],
									[
										'name' => 'playlist_show_playlist', 
										'operator' => '==',
										'value' => 'yes'
									]
								]
							],
						]
					],
				]
			);
			
			$this->add_control(
				'scrollbar_options',
				[
					'label' 						=> esc_html__( 'Scrollbar', 'elementor' ),
					'type' 							=> Controls_Manager::HEADING,
				]
			);
			$this->add_control(
				'scrollbar',
				[
					'label' 						=> esc_html__( 'Enable Scrollbar', 'sonaar-music' ),
					'description' 					=> 'Enable a vertical scrollbar on your tracklist',
					'type' 							=> \Elementor\Controls_Manager::SWITCHER,
					'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
					'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
					'return_value' 					=> '1',
					'default' 						=> '',
				]
			);
			$this->add_responsive_control(
				'playlist_height',
				[
					'label' 						=> esc_html__( 'Scrollbar Height', 'sonaar-music' ) . ' (px)',
					'type'							=> Controls_Manager::SLIDER,
					'condition' 					=> [
													'scrollbar' => '1',
					],
					'default'						=> [
						'unit' 						=> 'px',
						'size' 						=> 215,
					],
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 2000,
						],
					],
					'size_units' 					=> [ 'px', 'vh', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer .playlist ul.srp_list' => 'height: {{SIZE}}{{UNIT}}; overflow-y:hidden; overflow-x:hidden;',
					],
				]
			);
				$this->add_control(
					'pagination_heading',
					[
						'label' 						=> esc_html__( 'Pagination', 'elementor' ),
						'type' 							=> Controls_Manager::HEADING,
						'separator' 					=> 'before',
					]
				);
				$this->add_control(
					'pagination',
					[
						'label' 						=> esc_html__( 'Enable Pagination', 'sonaar-music' ),
						'type' 							=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 						=> esc_html__( 'Yes', 'sonaar-music' ),
						'label_off' 					=> esc_html__( 'No', 'sonaar-music' ),
						'return_value' 					=> 'true',
						'default' 						=> '',
					]
				);
				if (get_site_option('SRMP3_ecommerce') != '1'){
					$this->add_control(
						'sonaar_go_pro_pagination',
						[
							'type' 							=> \Elementor\Controls_Manager::RAW_HTML,
							'raw' 							=> 	$this->srp_promo_message(),
							'condition' 					=> [
								'pagination'					 => 'true',
							],
						]
					);
					$this->end_controls_section();
				}else if ( get_site_option('SRMP3_ecommerce') == '1'){
				$this->add_control(
					'tracks_per_page',
					[
						'label' 						=> esc_html__( 'Number of tracks per pagination', 'sonaar-music' ),
						'description' 					=> esc_html__( 'This improve the overall performance if you have many tracks. Leave blank for all', 'sonaar-music' ),
						'type' 							=> \Elementor\Controls_Manager::NUMBER,
						'min'							=> 0,
						'max' 							=> 1000,
						'step' 							=> 1,
						'default'						=> 10,
						'condition' 					=> [
							'pagination'					 => 'true',
						],
					]
				);
				$this->add_control(
					'pagination_numbers_hide',
					[
						'label' 						=> esc_html__( 'Hide Numbers, Keep Arrows', 'sonaar-music' ),
						'type' 							=> Controls_Manager::SWITCHER,
						'default' 						=> '',
						'return_value' 					=> 'none',
						'selectors' 					=> [
							'{{WRAPPER}} .srp_pagination' => 'display:{{VALUE}};',
						],
						'condition' 					=> [
							'pagination'					=> 'true',
						],
					]
				);
			$this->start_controls_tabs( 'pagination_controls' );
			$this->start_controls_tab(
				'tab_page_normal',
				[
					'label' 						=> esc_html__( 'Normal', 'elementor' ),
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->add_control(
				'page_color',
				[
					'label' 						=> esc_html__( 'Color', 'sonaar-music' ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .srp_pagination_container .srp_pagination span' => 'color: {{VALUE}}',
					],
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->add_control(
				'page_bgcolor',
				[
					'label'                			=> esc_html__( 'Background', 'sonaar-music' ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .srp_pagination_container .srp_pagination span' => 'background-color: {{VALUE}}',
					],
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->add_control(
				'page_arrows_color',
				[
					'label' 						=> esc_html__( 'Arrows Color', 'sonaar-music' ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .srp_pagination_container .srp_pagination_arrows' => 'color: {{VALUE}};border-color:{{VALUE}};',
					],
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_page_hover',
				[
					'label' 						=> esc_html__( 'Hover', 'elementor' ),
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->add_control(
				'page_color_hover',
				[
					'label' 						=> esc_html__( 'Color', 'sonaar-music' ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .srp_pagination_container .srp_pagination span:hover' => 'color: {{VALUE}}',
					],
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->add_control(
				'page_bgcolor_hover',
				[
					'label'                			=> esc_html__( 'Background', 'sonaar-music' ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .srp_pagination_container .srp_pagination span:hover' => 'background-color: {{VALUE}}',
					],
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->add_control(
				'page_arrows_color_hover',
				[
					'label' 						=> esc_html__( 'Arrows Color', 'sonaar-music' ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .srp_pagination_container .srp_pagination_arrows:hover' => 'color: {{VALUE}};border-color:{{VALUE}};',
					],
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_page_active',
				[
					'label' 						=> esc_html__( 'Active', 'elementor' ),
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->add_control(
				'page_color_active',
				[
					'label' 						=> esc_html__( 'Color', 'sonaar-music' ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .srp_pagination_container .srp_pagination .active span' => 'color: {{VALUE}}',
					],
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->add_control(
				'page_bgcolor_active',
				[
					'label'                			=> esc_html__( 'Background', 'sonaar-music' ),
					'type'                 		 	=> Controls_Manager::COLOR,
					'default'               		=> '',
					'selectors'            		 	=> [
													'{{WRAPPER}} .srp_pagination_container .srp_pagination .active span' => 'background-color: {{VALUE}}',
					],
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();		
			$this->add_control(
				'pagination_radius',
				[
					'label' 						=> esc_html__( 'Page Radius', 'elementor' ),
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 40,
						],
					],
					'selectors' 					=> [
													'{{WRAPPER}} .srp_pagination_container .srp_pagination span' => 'border-radius: {{SIZE}}px;',
													'{{WRAPPER}} .srp_pagination_container .srp_pagination_arrows' => 'border-radius: {{SIZE}}px;'
					],
					'condition' 					=> [
						'pagination'					=> 'true',
					],
				]
			);
			
			$this->end_controls_section();
		}
			/**
	         * STYLE: METADATA
	         * -------------------------------------------------
	         */
				
			$this->start_controls_section(
	            'metadata_style',
	            [
	                'label'                			=> esc_html__( 'Metadata', 'sonaar-music' ),
					'tab'                   		=> Controls_Manager::TAB_STYLE,
					'conditions'                    => [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_float_tracklist'
									],
									[
										'name' => 'playlist_show_playlist', 
										'operator' => '!=',
										'value' => ''
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout', 
										'operator' => '==',
										'value' => 'skin_boxed_tracklist'
									],
									[
										'name' => 'playlist_show_soundwave', 
										'operator' => '!=',
										'value' => 'yes'
									]
								]
							]
						]
					]
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'metadata_typography',
					'label' 						=> esc_html__( 'Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'selector' 						=> '{{WRAPPER}} .sr_it-playlist-publish-date, {{WRAPPER}} .srp_playlist_duration, {{WRAPPER}} .srp_trackCount',
				]
			);	
			$this->add_control(
				'metadata_color',
				[
					'label'                		 	=> esc_html__( 'Color', 'sonaar-music' ),
					'type'                		 	=> Controls_Manager::COLOR,
					'default'            		    => '',
					'selectors'             		=> [
						'{{WRAPPER}} .sr_it-playlist-publish-date, {{WRAPPER}} .srp_playlist_duration, {{WRAPPER}} .srp_trackCount' => 'color: {{VALUE}}',
					],
				]
			);	
			$this->add_control(
				'publishdate_btshow',
				[
					'label' 						=> esc_html__( 'Show Date in the Mini-Player', 'sonaar-music' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_show_publish_date', 'srmp3_settings_widget_player') ),
						'true' => esc_html__( 'Yes', 'sonaar-music' ),
						'false' => esc_html__( 'No', 'sonaar-music' ),
					],
					'default' => 'default',
					'condition' => [
						'playlist_source!' => 'from_elementor',
					],
				]
			);
			$this->add_control(
				'nb_of_track_btshow',
				[
					'label' 						=> sprintf( esc_html__( 'Show Total Number of %1$ss', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_show_tracks_count', 'srmp3_settings_widget_player') ),
						'true' => esc_html__( 'Yes', 'sonaar-music' ),
						'false' => esc_html__( 'No', 'sonaar-music' ),
					],
					'default' => 'default',
				]
			);
			$this->add_control(
				'playlist_duration_btshow',
				[
					'label' 						=> esc_html__( 'Show Total Playlist Time Duration', 'sonaar-music' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'default' 	=> esc_html__( $this->get_srmp3_option_label('player_show_meta_duration', 'srmp3_settings_widget_player') ),
						'true' => esc_html__( 'Yes', 'sonaar-music' ),
						'false' => esc_html__( 'No', 'sonaar-music' ),
					],
					'default' => 'default',
				]
			);			
			$this->end_controls_section();






			/**
	         * STYLE: External Links Buttons
	         * -------------------------------------------------
	         */
			
			$this->start_controls_section(
	            'album_stores',
	            [
	                'label'                			=> esc_html__( 'External Links Buttons', 'sonaar-music' ),
					'tab'                   		=> Controls_Manager::TAB_STYLE,
					'conditions' 					=> [
						'relation' => 'or',
						'terms' => [
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout',
										'operator' => '!=',
										'value' => 'skin_button'
									],
									[	
										'name' => 'playlist_show_album_market',
										'operator' => '==',
										'value' => 'yes'
									]
								]
							],
							[
								'relation' => 'and',
								'terms' => [
									[
										'name' => 'player_layout',
										'operator' => '==',
										'value' => 'skin_button'
									],
									[	
										'name' => 'playlist_show_album_market_skin_button',
										'operator' => '==',
										'value' => 'yes'
									]
								]
							],
						]
					]
	            ]
			);
			$this->add_control(
				'album_store_position',
				[
					'label' 						=> esc_html__( 'Move Links below soundwave', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'top',
					'condition' 					=> [
						'player_layout' 	=> 'skin_boxed_tracklist',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'storelinks_background',
					'label' => esc_html__( 'Background', 'elementor-sonaar' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .iron-audioplayer .album-store',
				]
			);
			$this->add_control(
				'store_heading_options',
				[
					'label' 						=> esc_html__( 'Heading Style', 'elementor' ),
					'type' 							=> Controls_Manager::HEADING,
					'separator' 					=> 'before',
				]
			);
			$this->add_control(
				'store_title_btshow',
				[
					'label' 						=> esc_html__( 'Hide Heading', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'none',
					'selectors' 					=> [
							 						'{{WRAPPER}} .available-now' => 'display:{{VALUE}};',
					 ],
				]
			);
			$this->add_control(
				'store_title_text',
				[
					'label' 						=> esc_html__( 'Heading text', 'sonaar-music' ),
					'type' 							=> Controls_Manager::TEXT,
					'dynamic' 						=> [
						'active' 					=> true,
					],
					'default' 						=> '',
					'condition' 					=> [
						'store_title_btshow' 		=> '',
					],
					'label_block' 					=> false,
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'store_title_typography',
					'label' 						=> esc_html__( 'Heading Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'condition' 					=> [
						'store_title_btshow' 		=> '',
					],
					'selector' 						=> '{{WRAPPER}} .available-now',
				]
			);
			$this->add_control(
				'store_title_color',
				[
					'label'                 		=> esc_html__( 'Heading Color', 'sonaar-music' ),
					'type'                  		=> Controls_Manager::COLOR,
					'default'               		=> '',
					'condition' 					=> [
						'store_title_btshow' 		=> '',
					],
					'selectors'             		=> [
						'{{WRAPPER}} .available-now' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_responsive_control(
				'store_title_align',
				[
					'label' 						=> esc_html__( 'Heading Alignment', 'sonaar-music' ),
					'type' 							=> Controls_Manager::CHOOSE,
					'options' 						=> [
						'flex-start'    					=> [
							'title' 				=> esc_html__( 'Left', 'elementor' ),
							'icon' 					=> 'eicon-h-align-left',
						],
						'center' 					=> [
							'title' 				=> esc_html__( 'Center', 'elementor' ),
							'icon' 					=> 'eicon-h-align-center',
						],
						'flex-end' 					=> [
							'title' 				=> esc_html__( 'Right', 'elementor' ),
							'icon' 					=> 'eicon-h-align-right',
						],
					],
					'default' 						=> '',
					'condition' 					=> [
						'store_title_btshow' 		=> '',
					],
					'selectors' 					=> [
														'{{WRAPPER}} .ctnButton-block' => 'justify-content: {{VALUE}};align-items:{{VALUE}}',
													//'{{WRAPPER}} .available-now' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'store_links_options',
				[
					'label' 						=> esc_html__( 'Button Style', 'elementor' ),
					'type' 							=> Controls_Manager::HEADING,
					'separator' 					=> 'before',
				]
			);
			$this->add_responsive_control(
				'album_stores_align',
				[
					'label'						 	=> esc_html__( 'Links Alignment', 'sonaar-music' ),
					'type' 							=> Controls_Manager::CHOOSE,
					'options' 						=> [
						'flex-start'    					=> [
							'title' 				=> esc_html__( 'Left', 'elementor' ),
							'icon' 					=> 'eicon-h-align-left',
						],
						'center' 					=> [
							'title' 				=> esc_html__( 'Center', 'elementor' ),
							'icon' 					=> 'eicon-h-align-center',
						],
						'flex-end' 					=> [
							'title' 				=> esc_html__( 'Right', 'elementor' ),
							'icon' 					=> 'eicon-h-align-right',
						],
					],
					'default' 						=> '',
					'selectors' 					=> [
													'{{WRAPPER}} .buttons-block' => 'justify-content: {{VALUE}};align-items: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 							=> 'store_button_typography',
					'label'						 	=> esc_html__( 'Button Typography', 'sonaar-music' ),
					'scheme' 						=> Typography::TYPOGRAPHY_1,
					'selector' 						=> '{{WRAPPER}} a.button',
				]
			);

			$this->start_controls_tabs( 'tabs_button_style' );

			$this->start_controls_tab(
				'tab_button_normal',
				[
					'label' 						=> esc_html__( 'Normal', 'elementor' ),
				]
			);

			$this->add_control(
				'button_text_color',
				[
					'label' 						=> esc_html__( 'Text Color', 'sonaar-music' ),
					'type' 							=> Controls_Manager::COLOR,
					'default' 						=> '',
					'selectors' 					=> [
													'{{WRAPPER}} a.button' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'background_color',
				[
					'label' 						=> esc_html__( 'Button Color', 'sonaar-music' ),
					'type' 							=> Controls_Manager::COLOR,
					'selectors' 					=> [
													'{{WRAPPER}} a.button' => 'background: {{VALUE}}',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_button_hover',
				[
					'label' 						=> esc_html__( 'Hover', 'elementor' ),
				]
			);

			$this->add_control(
				'button_hover_color',
				[
					'label' 						=> esc_html__( 'Text Color', 'sonaar-music' ),
					'type' 							=> Controls_Manager::COLOR,
					'selectors' 					=> [
													'{{WRAPPER}} a.button:hover' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'button_background_hover_color',
				[
					'label' 						=> esc_html__( 'Button Color', 'sonaar-music' ),
					'type' 							=> Controls_Manager::COLOR,
					'selectors'					 	=> [
													'{{WRAPPER}} a.button:hover' => 'background-color: {{VALUE}};',
					],
				]
			);
			$this->add_control(
				'button_hover_border_color',
				[
					'label' 						=> esc_html__( 'Button Border Color', 'sonaar-music' ),
					'type' 							=> Controls_Manager::COLOR,
					'condition' 					=> [
						'border_border!' 			=> '',
					],
					'selectors' 					=> [
													'{{WRAPPER}} a.button:hover' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 							=> 'border',
					'selector' 						=> '{{WRAPPER}} .buttons-block .store-list li .button',
					'separator' 					=> 'before',
				]
			);
			$this->add_control(
				'button_border_radius',
				[
					'label' 						=> esc_html__( 'Button Radius', 'elementor' ),
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 20,
						],
					],
					'selectors' 					=> [
													'{{WRAPPER}} .store-list .button' => 'border-radius: {{SIZE}}px;',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 							=> 'button_box_shadow',
					'selector' 						=> '{{WRAPPER}} .store-list .button',
				]
			);
			$this->add_responsive_control(
				'button_text_padding',
				[
					'label' 						=> esc_html__( 'Button Padding', 'sonaar-music' ),
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .iron_widget_radio .store-list .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' 					=> 'before',
				]
			);
			$this->add_responsive_control(
				'space_between_store_button',
				[
					'label' 						=> esc_html__( 'Buttons Space', 'sonaar-music' ) . ' (px)',
					'type' 							=> Controls_Manager::SLIDER,
					'range' 						=> [
						'px' 						=> [
							'max' 					=> 50,
						],
					],
					'selectors' 					=> [
													'{{WRAPPER}} .buttons-block .store-list' => 'column-gap: {{SIZE}}px;', 
					],
				]
			);
			$this->add_control(
				'hr6',
				[
					'type' 							=> \Elementor\Controls_Manager::DIVIDER,
					'style' 						=> 'thick',
				]
			);
			$this->add_control(
				'store_icon_show',
				[
					'label' 						=> esc_html__( 'Hide Icon', 'sonaar-music' ),
					'type' 							=> Controls_Manager::SWITCHER,
					'default' 						=> '',
					'return_value' 					=> 'none',
					'selectors' 					=> [
							 						'{{WRAPPER}} .store-list .button i' => 'display:{{VALUE}};',
					 ],
				]
			);
			$this->add_responsive_control(
				'icon-font-size',
				[
					'label'							=> esc_html__( 'Icon Font Size', 'sonaar-music' ) . ' (px)',
					'type' 							=> Controls_Manager::SLIDER,
					'condition' 					=> [
						'store_icon_show'			=> '',
					],
					'range' 						=> [
						'px' 						=> [
						'max' 						=> 100,
						],
					],
					'selectors'						=> [
													'{{WRAPPER}} .buttons-block .store-list i' => 'font-size: {{SIZE}}px;', 
					],
				]
			);
			$this->add_responsive_control(
				'icon_indent',
				[
					'label' 						=> esc_html__( 'Icon Spacing', 'elementor' ) . ' (px)',
					'type' 							=> Controls_Manager::SLIDER,
					'condition' 					=> [
						'store_icon_show' 			=> '',
					],
					'range' 						=> [
						'px' 						=> [
						'max' 						=> 50,
						],
					],
					'selectors' 					=> [
													'{{WRAPPER}} .buttons-block .store-list i' => 'margin-right: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'hr11',
				[
					'type' 							=> \Elementor\Controls_Manager::DIVIDER,
					'style' 						=> 'thick',
				]
			);
			$this->add_responsive_control(
				'album_stores_padding',
				[
					'label' 						=> esc_html__( 'Link Buttons Margin', 'sonaar-music' ),
					'type' 							=> Controls_Manager::DIMENSIONS,
					'size_units' 					=> [ 'px', 'em', '%' ],
					'selectors' 					=> [
													'{{WRAPPER}} .iron-audioplayer.show-playlist .ctnButton-block' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

		// end if function exist
		}
		//
	
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$playlist_show_album_market = ( ( $settings['player_layout'] != 'skin_button' && $settings['playlist_show_album_market']=="yes" )  || ( $settings['player_layout'] == 'skin_button' && $settings['playlist_show_album_market_skin_button']=="yes" ) ) ? 'true' : 'false';
		$playlist_show_playlist = ( ( $settings['player_layout'] != 'skin_button' && $settings['playlist_show_playlist']=="yes" )  || ( $settings['player_layout'] == 'skin_button' && $settings['playlist_show_playlist_skin_button']=="yes" ) ) ? 'true' : 'false';
		$playlist_show_soundwave = (($settings['playlist_show_soundwave']=="yes") ? 'true' : 'false');
		$playlist_playlist_hide_artwork = (($settings['playlist_hide_artwork']=="yes") ? 'true' : 'false');
		$show_control_on_hover = (isset($settings['show_control_on_hover']) && $settings['show_control_on_hover']=="yes" ? 'true' : 'false');
		$playlist_reverse_tracklist = (function_exists( 'run_sonaar_music_pro' ) && isset($settings['reverse_tracklist']) && $settings['reverse_tracklist'] == "yes") ? true : false;
		$searchbar_show_keyword = '';
		$searchbar_placeholder = '';
		$tracks_per_page = '';
		if ( function_exists( 'run_sonaar_music_pro' ) ){
			$sticky_player = $settings['enable_sticky_player'];
			$shuffle = $settings['enable_shuffle'];
			$wave_color = $settings['soundWave_bg_bar_color'];
			$wave_progress_color = $settings['soundWave_progress_bar_color'];
			if (get_site_option('SRMP3_ecommerce') == '1'){
				$searchbar_show_keyword = (isset($settings['enable_searchbar_keyword'])) ? $settings['enable_searchbar_keyword'] : $searchbar_show_keyword;
				$searchbar_placeholder = (isset($settings['search_placeholder'])) ? $settings['search_placeholder'] : $searchbar_placeholder;
				$tracks_per_page = ( $settings['pagination'] == 'true' ) ? $settings['tracks_per_page'] : '';
			}
		}else{
			$sticky_player = false;
			$shuffle = false;
			$wave_color = false;
			$wave_progress_color = false;
			$settings['title_html_tag_soundwave'] = 'div';
			$settings['playlist_title_html_tag_soundwave'] = 'div';
			$settings['title_html_tag_playlist'] = 'h3';
		}
		
		$shortcode = '[sonaar_audioplayer elementor="true" tracks_per_page="' . $tracks_per_page . '" titletag_soundwave="'. $settings['playlist_title_html_tag_soundwave'] .'" track_titletag_soundwave="'. $settings['title_html_tag_soundwave'] .'" titletag_playlist="'. $settings['title_html_tag_playlist'] .'" hide_artwork="' . $playlist_playlist_hide_artwork .'" show_control_on_hover="' . $show_control_on_hover .'" show_playlist="' . $playlist_show_playlist .'" reverse_tracklist="' . $playlist_reverse_tracklist .'" show_album_market="' . $playlist_show_album_market .'" hide_timeline="' . $playlist_show_soundwave .'" sticky_player="' . $sticky_player .'" wave_color="' . $wave_color .'" wave_progress_color="' . $wave_progress_color .'" shuffle="' . $shuffle .'" searchbar="' . $searchbar_show_keyword .'" searchbar_placeholder="' . $searchbar_placeholder .'" ';
		
		if (isset($settings['show_cat_description'])){
			$shortcode .='show_cat_description="'. $settings['show_cat_description']  .'" ';
		}
		if (isset($settings['player_layout'])){
			$shortcode .= 'player_layout="' . $settings['player_layout'] . '" ';
		}

		if ( isset($settings['player_layout']) && $settings['player_layout'] == 'skin_button'){

			if (isset($settings['show_skip_bt_skin_button']) && $settings['show_skip_bt_skin_button'] == 'yes' ){
				$shortcode .= 'show_skip_bt="true" ';
			}

			if (isset($settings['show_speed_bt_skin_button']) && $settings['show_speed_bt_skin_button'] == 'yes' ){
				$shortcode .= 'show_speed_bt="true" ';
			}

			if (isset($settings['show_volume_bt_skin_button']) && $settings['show_volume_bt_skin_button'] == 'yes' ){
				$shortcode .= 'show_volume_bt="true" ';
			}

			if (isset($settings['show_shuffle_bt_skin_button']) && $settings['show_shuffle_bt_skin_button'] == 'yes' ){
				$shortcode .= 'show_shuffle_bt="true" ';
			}

		}else{

			if (isset($settings['show_skip_bt'])){
				$shortcode .= 'show_skip_bt="'. $settings['show_skip_bt'] .'" ';
			}

			if (isset($settings['show_speed_bt'])){
				$shortcode .= 'show_speed_bt="'. $settings['show_speed_bt'] .'" ';
			}

			if (isset($settings['show_volume_bt'])){
				$shortcode .= 'show_volume_bt="'. $settings['show_volume_bt'] .'" ';
			}

			if (isset($settings['show_shuffle_bt'])){
				$shortcode .= 'show_shuffle_bt="'. $settings['show_shuffle_bt'] .'" ';
			}
			
		}

		if( $settings['playlist_title'] ){
			$shortcode .= 'playlist_title="'. $settings['playlist_title'] . '" ';
		}
		
		if( isset($settings['publishdate_btshow']) && $settings['publishdate_btshow'] != ''){
			$shortcode .= 'show_publish_date="'. $settings['publishdate_btshow'] . '" ';
		}

		if( isset($settings['cta_track_show_label']) && $settings['cta_track_show_label'] != ''){
			$shortcode .= 'cta_track_show_label="'. $settings['cta_track_show_label'] . '" ';
		}

		if( isset($settings['playlist_duration_btshow']) && $settings['playlist_duration_btshow'] != ''){
			$shortcode .= 'show_meta_duration="'. $settings['playlist_duration_btshow'] . '" ';
		}
		if( isset($settings['nb_of_track_btshow']) && $settings['nb_of_track_btshow'] != ''){
			$shortcode .= 'show_tracks_count="'. $settings['nb_of_track_btshow'] . '" ';
		}
		if ( $settings['playlist_source'] == 'from_elementor' && !$settings['playlist_list']) {	
				
			$feed = '1';
			$shortcode .= 'feed=1 ';
			$shortcode .= 'el_widget_id="' . $this->get_id() .'" ';

			update_post_meta( get_the_ID(), 'srmp3_elementor_tracks', $settings['feed_repeater'] ); // update post meta to retrieve data in json later
			update_post_meta( get_the_ID(), 'alb_store_list', $settings['storelist_repeater'] ); // update post meta store list
		
		}

		if (isset($settings['hide_track_market']) && function_exists( 'run_sonaar_music_pro' )){
			$playlist_hide_track_market = (($settings['hide_track_market']=="yes") ? 'false' : 'true');
			$shortcode .= 'show_track_market="' . $playlist_hide_track_market . '" ';
		}else{
			$shortcode .= 'show_track_market="true" ';
		}

		if (isset($settings['trackList_layout'])  && $settings['trackList_layout']=='grid' ){
			if ( isset( $settings[ 'grid_track_artwork_format_size' ] ) ){
				$shortcode .= 'track_artwork_format="'. $settings['grid_track_artwork_format_size'] .'" ';
			}
			if (isset($settings['grid_track_artwork_show']) && $settings['grid_track_artwork_show'] == 'yes'){
				$shortcode .= 'track_artwork="true" ';
			}
			if (isset($settings['grid_track_artwork_play_button']) && $settings['grid_track_artwork_play_button'] == 'yes'){
				$shortcode .= 'track_artwork_play_button="true" ';
			}
			if (isset($settings['grid_track_artwork_play_on_hover']) && $settings['grid_track_artwork_play_on_hover'] == 'yes'){
				$shortcode .= 'track_artwork_play_on_hover="true" ';
			}
		}else{
			if ( isset( $settings[ 'list_track_artwork_format_size' ] ) ){
				$shortcode .= 'track_artwork_format="'. $settings['list_track_artwork_format_size'] .'" ';
			}
			if (isset($settings['track_artwork_show']) && $settings['track_artwork_show'] == 'yes'){
				$shortcode .= 'track_artwork="true" ';
			}
			if (isset($settings['track_artwork_play_button']) && $settings['track_artwork_play_button'] == 'yes'){
				$shortcode .= 'track_artwork_play_button="true" ';
			}
			if (isset($settings['track_artwork_play_on_hover']) && $settings['track_artwork_play_on_hover'] == 'yes'){
				$shortcode .= 'track_artwork_play_on_hover="true" ';
			}
		}
		
		if ( isset($settings['use_play_label']) ){
			$shortcode .= 'use_play_label="'. $settings['use_play_label'] .'" ';
		}
		if(isset($settings['player_layout']) && $settings['player_layout'] == 'skin_button'){
			if (isset($settings['use_play_label_skin_button']) && $settings['use_play_label_skin_button'] == ''){
				$shortcode .= 'use_play_label="false" ';
			}
		}else{
			if (isset($settings['use_play_label'])){
				$shortcode .= 'use_play_label="'. $settings['use_play_label'] .'" ';
			}
		}
		if (isset($settings['soundwave_show_skin_button']) && isset($settings['player_layout']) && $settings['player_layout'] == 'skin_button' && $settings['soundwave_show_skin_button']=='yes'){
			$shortcode .= 'show_progressbar="true" ';
		}
		if (isset($settings['play_btn_align_wave']) && $settings['play_btn_align_wave'] == 'yes' ){
			$shortcode .= 'play_btn_align_wave="true" ';
		}
		if ($settings['sr_player_on_artwork']){
			$shortcode .= 'display_control_artwork="true" ';
		}
		if (isset($settings['hide_trackdesc']) && $settings['hide_trackdesc'] == '1'){
			$shortcode .= 'hide_trackdesc="'. true .'" ';
		}
		
		if(function_exists( 'run_sonaar_music_pro' )){
			
			if (isset($settings['track_desc_postcontent']) && $settings['track_desc_postcontent'] == 'true'){
				$shortcode .= 'track_desc_postcontent="true" ';
			}
			if (isset($settings['artist_wrap']) && $settings['artist_wrap'] === 'true'){
				$shortcode .= 'artist_wrap="true" ';
			}
			if (isset($settings['scrollbar']) && $settings['scrollbar'] == '1'){
				$shortcode .= 'scrollbar="true" ';
			}
			if (isset($settings['title_soundwave_show']) && $settings['title_soundwave_show']=='yes'){
				$shortcode .= 'hide_track_title="true" ';
			}
			if (isset($settings['playlist_title_soundwave_show']) && $settings['playlist_title_soundwave_show']=='yes'){
				$shortcode .= 'hide_player_title="true" ';
			}
			if (isset($settings['duration_soundwave_show']) && $settings['duration_soundwave_show']=='yes'){
				$shortcode .= 'hide_times="true" ';
			}
			if (isset($settings['use_play_label_with_icon']) && $settings['use_play_label_with_icon']==''){
				$shortcode .= 'use_play_label_with_icon="false" ';
			}
			if (isset($settings['soundwave_show']) && $settings['soundwave_show']=='yes'){
				$shortcode .= 'hide_progressbar="true" ';
			}
			if (isset($settings['progressbar_inline']) && $settings['progressbar_inline']=='yes'){
				$shortcode .= 'progressbar_inline="true" ';
			}
			if (isset($settings['store_title_text'])){
				$shortcode .= 'store_title_text="' . $settings['store_title_text'] . '" ';
			}
			if (isset($settings['play_text'])){
				$shortcode .= 'play_text="' . $settings['play_text'] . '" ';
			}
			if (isset($settings['pause_text'])){
				$shortcode .= 'pause_text="' . $settings['pause_text'] . '" ';
			}
			if (isset($settings['album_store_position'])){
				$shortcode .= 'album_store_position="' . $settings['album_store_position'] . '" ';
			}
			if (isset($settings['no_track_skip']) && $settings['no_track_skip']=='yes'){
				$shortcode .= 'notrackskip="true" ';
			}
			if (isset($settings['no_loop_tracklist']) && $settings['no_loop_tracklist']=='yes'){
				$shortcode .= 'no_loop_tracklist="true" ';
			}
			if (isset($settings['strip_html_track_desc'])){
				$shortcode .= 'strip_html_track_desc="'. $settings['strip_html_track_desc'] .'" ';
			}
			if (isset($settings['track_desc_lenght'])){
				$shortcode .= 'track_desc_lenght="'. $settings['track_desc_lenght'] .'" ';
			}
			if (isset($settings['show_track_publish_date'])){
				$shortcode .= 'show_track_publish_date="'. $settings['show_track_publish_date'] .'" ';
			}
			if (isset($settings['track_list_linked'])){
				$shortcode .= 'post_link="'. $settings['track_list_linked'] .'" ';
			}
			if (isset($settings['button_hover_animation'])){
				$shortcode .= 'button_animation="'. $settings['button_hover_animation'] .'" ';
			}
			if (isset($settings['show_name_filter'])){
				$shortcode .= 'show_name_filter="'. $settings['show_name_filter'] .'" ';
			}
			if (isset($settings['show_date_filter'])){
				$shortcode .= 'show_date_filter="'. $settings['show_date_filter'] .'" ';
			}
			if (isset($settings['show_duration_filter'])){
				$shortcode .= 'show_duration_filter="'. $settings['show_duration_filter'] .'" ';
			}
			if (isset($settings['trackList_layout'])){
				$shortcode .= 'tracklist_layout="'. $settings['trackList_layout'] .'" ';

				if (isset($settings['grid_column_number']) && $settings['trackList_layout']=='grid'){
					$desktopValue = $settings['grid_column_number'];
					$tabetValue = ( isset($settings['grid_column_number_tablet']) && $settings['grid_column_number_tablet'] != '')? $settings['grid_column_number_tablet'] : $desktopValue;
					$mobileValue = ( isset($settings['grid_column_number_mobile']) && $settings['grid_column_number_mobile'] != '')? $settings['grid_column_number_mobile'] : $tabetValue;
					$shortcode .= 'grid_column_number="'. $desktopValue . ',' . $tabetValue . ',' . $mobileValue .'" ';
				}
			}

		}
		
		if ($settings['album_img']){
			//WIP test this.
			$attachImg = wp_get_attachment_image_src( $settings['album_img']['id'], 'large' );
			$album_img = (is_array($attachImg)) ? $attachImg[0] : '';
			$shortcode .= 'artwork="' .  $album_img . '" ';
			update_post_meta( get_the_ID(), 'srmp3_elementor_artwork', $album_img); // update post meta to retrieve data in json later
		}
		if ($settings['play_current_id']=='yes' || $settings['playlist_source']=='from_current_post'){ //If "Play its own Post ID track" option is enable
			$postid = get_the_ID();
			$shortcode .= 'albums="' . $postid . '" ';
			if (isset($settings['audio_meta_field']) && $settings['audio_meta_field'] !=''){ // Use the audio_meta_field field
				$shortcode .= 'audio_meta_field="' . $settings['audio_meta_field'] . '" ';
				if(isset($settings['repeater_meta_field']) && $settings['repeater_meta_field'] !=''){
					$shortcode .= 'repeater_meta_field="' . $settings['repeater_meta_field'] . '" ';	
				}
			}
		}else{
			$display_playlist_ar = $settings['playlist_list'];
			$display_playlist_cat_ar = (isset($settings['playlist_list_cat'])) ? $settings['playlist_list_cat'] : null;
			if(is_array($display_playlist_ar)){
				$display_playlist_ar = implode(", ", $display_playlist_ar); 
			}
			if(is_array($display_playlist_cat_ar)){
				$display_playlist_cat_ar = implode(", ", $display_playlist_cat_ar); 
			}
			if(!$display_playlist_cat_ar && $settings['playlist_source'] == 'from_cat'){
				$shortcode .= 'category="all" ';
				$shortcode .= (isset($settings['posts_per_page'])) ? 'posts_per_page="' . $settings['posts_per_page'] . '" ' : '';
			}elseif($display_playlist_cat_ar && $settings['playlist_source'] == 'from_cat'){
				$shortcode .= 'category="'. $display_playlist_cat_ar . '" ';
				$shortcode .= (isset($settings['posts_per_page'])) ? 'posts_per_page="' . $settings['posts_per_page'] . '" ' : '';
			}
	
			if (!$display_playlist_ar) { //If no playlist is selected, play the latest playlist
				if($settings['playlist_source'] == 'from_cpt' ){
					$shortcode .= 'play-latest="true" ';
				}
				if ($settings['playlist_source'] == 'from_elementor'){
					if ( isset( \Elementor\Plugin::$instance->documents ) ) {
						$post_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
						$shortcode .= 'albums="' . $post_id . '" ';
					}
					if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
						if (!$settings['feed_repeater']){
							echo esc_html__('Add tracks in the widget settings.', 'sonaar-music');
						}
					}
				}
				
			}else{
				$shortcode .= 'albums="' . $display_playlist_ar . '" ';
			}
		
		}
		if ( function_exists( 'run_sonaar_music_pro' ) &&  get_site_option('SRMP3_ecommerce') == '1' && !empty($settings['cf_repeater'])) {
			$shortcode .=( $settings['searchbar_cf_heading_show'] && $settings['searchbar_cf_heading_show']==true) ? 'custom_fields_heading="true" ' : '';
			$cf_repeater_ar = array();

			foreach ($settings['cf_repeater'] as $key => $value) {
				$value['column_name'] = ($value['column_name']) ? $value['column_name'] : '';
				$value['column_width']['size'] = ($value['column_width']['size']) ? $value['column_width']['size'] : '100';
				$value['column_width']['unit'] = ($value['column_width']['unit']) ? $value['column_width']['unit'] : 'px';
				$fieldKey = '';
				if($value['custom_field_key'] != '' ){
					$value[$fieldKey] = $value['custom_field_key'];
				}else if($value['custom_field_plugin'] == 'customkey' && $value['custom_field_key'] == '' ){
					$value[$fieldKey] = 'null';
				}else{
					$fieldKey = 'column_fields_' .  $value['custom_field_plugin'];
				}

				array_push( $cf_repeater_ar, $value['column_name'] . '::' . $value[$fieldKey].'::'.$value['column_width']['size'].$value['column_width']['unit'] );
			}
			
			$cf_repeater_ar = (isset($cf_repeater_ar) && is_array($cf_repeater_ar)) ? implode(";", $cf_repeater_ar):'';
			
			$shortcode .= ($cf_repeater_ar != '') ? 'custom_fields_columns="' . $cf_repeater_ar . '" ':'';
		}
		
		$shortcode .= ']';
		
		//Attention: double brackets are required if using var_dump to display a shortcode otherwise it will render it!
		//print_r("Shortcode = [" . $shortcode . "]");
		echo do_shortcode( $shortcode );



	}
	private function check_column_plugin_activated(){
		$source = array(
			'object' => __( 'Post/Term/User/Object Data', 'sonaar-music' ),
		);
		
		if (function_exists( 'acf_get_fields' )){
			$source['acf'] = 'ACF';
		}
		if (function_exists( 'jet_engine' )){
			$source['jetengine'] = 'Jet Engine';
		}
		$source['customkey'] = 'Custom Meta Key';
		return $source;
	}
	
		/**
		 * Retuns current object fields array
		 * @return [type] [description]
		 */
		public function get_object_fields( $where = 'elementor', $blocks_values_key = 'values' ) {

			$groups = array(
				array(
					'label'  => __( 'Player', 'sonaar-music' ),
					'options' => array(
						''	        	=> __( 'Select...', 'sonaar-music' ),
						'srmp3_cf_album_title'  => sprintf( esc_html__( '%1$s Title', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('album/podcast')) ),//__( 'Audio Image', 'sonaar-music' ),__( 'Album Title', 'sonaar-music' ),
						'srmp3_cf_album_img'  	=> sprintf( esc_html__( '%1$s Image', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),//__( 'Audio Image', 'sonaar-music' ),
						'srmp3_cf_audio_title'  => sprintf( esc_html__( '%1$s Title', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),//__( 'Audio Image', 'sonaar-music' ),
						'srmp3_cf_length'       => sprintf( esc_html__( '%1$s Duration', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),//__( 'Audio Image', 'sonaar-music' ),
						'srmp3_cf_description'  => sprintf( esc_html__( '%1$s Description', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('track')) ),//__( 'Audio Image', 'sonaar-music' ),
						'playlist-category'  => sprintf( esc_html__( '%1$s Category', 'sonaar-music' ), ucfirst(Sonaar_Music_Admin::sr_GetString('playlist')) ),//__( 'Audio Image', 'sonaar-music' ),
						'srmp3_cf_artist'  		=> __( 'Artist Name', 'sonaar-music' ),
						'podcast-show'        	=> __( 'Podcast Show', 'sonaar-music' )
					)
				),
				array(
					'label'  => __( 'Post', 'sonaar-music' ),
					'options' => array(
						'post_id'       => __( 'Post ID', 'sonaar-music' ),
						'post_title'    => __( 'Post Title', 'sonaar-music' ),
						'post_date'     => __( 'Post Date', 'sonaar-music' ),
						'post_modified' => __( 'Post Date Modified', 'sonaar-music' ),
					)
				),
				array(
					'label'  => __( 'Category', 'sonaar-music' ),
				),
			) ;
			/*$my_cat_ar = array(
				//'playlist_cat'        	=> __( 'Post Category', 'sonaar-music' ),
				'post_tags'        		=> __( 'Product Tags', 'sonaar-music' ),

			);*/
			/*if ( Sonaar_Music::get_option('player_type', 'srmp3_settings_general') == 'podcast' ){
				$my_cat_ar += [
					'podcast_show'        => __( 'Podcast Show', 'sonaar-music' )
				]; 
			}*/
			if (defined( 'WC_VERSION' )){
				$groups[] = array(
					'label'  => __( 'WooCommerce', 'sonaar-music' ),
					'options' => array(
						'product_cat'       	=> __( 'Product Categories', 'sonaar-music' ),
						'product_tag'       	=> __( 'Product Tags', 'sonaar-music' ),
						'_price'			=> __( 'Price', 'sonaar-music' ),
						'_sale_price'		=> __( 'Sale Price', 'sonaar-music' ),
						'_sku'				=> __( 'SKU', 'sonaar-musice' ),
						'total_sales'		=> __( 'Total Sales', 'sonaar-music' ),
						'_wc_average_rating'=> __( 'Average Rating', 'sonaar-music' ),
						'_stock_status'		=> __( 'Stock Status', 'sonaar-music' ),
					)
				);

				$wcAttributes = wc_get_attribute_taxonomy_labels();
				$wcAttributes = (is_array($wcAttributes)) ? array_combine(
					array_keys($wcAttributes), array_map(function($k){ return 'Attribute: '.$k; }, $wcAttributes)
				) : '';
				$wcAttributes = (is_array($wcAttributes)) ? array_combine(
					array_map(function($k){ return 'pa_'.$k; }, array_keys($wcAttributes)), $wcAttributes
				) : '';
				$groups[3]['options'] += $wcAttributes;
			}
			//$groups[2]['options'] = $my_cat_ar;
			return $groups;

		}
	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_meta_fields_for_post_type() {

		if ( jet_engine()->meta_boxes ) {
			return jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
		} else {
			return array();
		}

	}
	public function get_fields_goups( $group = 'fields' ) {
		$cb = array(
			'fields'   => 'map_fields',
			/*'images'   => 'map_images',
			'links'    => 'map_links',
			'repeater' => 'map_repeater',*/
		);

		$groups = (null !== $this->get_raw_goups()) ? $this->get_raw_goups() : '';
		
		$result = array(
			''        	=> __( 'Select...', 'sonaar-music' )
		);

		if ( empty( $groups ) ) {
			return $result;
		}

		foreach ( $groups as $data ) {

			$fields = array_filter( array_map( array( $this, $cb[ $group ] ), $data['options'] ) );

			if ( ! empty( $fields ) ) {
				$result[] = array(
					'label'   => $data['label'],
					'options' => $fields,
				);
			}

		}
		return $result;

	}
/**
		 * Map fields callback
		 *
		 * @param  [type] $field [description]
		 * @return [type]        [description]
		 */
		public function map_fields( $field ) {

			$whitelisted = $this->whitelisted_fields();
			$type        = $field['type'];

			if ( ! in_array( 'field', $whitelisted[ $type ] ) ) {
				return false;
			} else {
				return $field['label'];
			}
		}
		/**
		 * Fields groups
		 *
		 * @return array
		 */
		public function get_raw_goups() {
			
			if ( isset($this->fields_groups) && null !== $this->fields_groups ) {
				return $this->fields_groups;
			}

			// ACF >= 5.0.0
			if ( function_exists( 'acf_get_field_groups' ) ) {
				$groups = acf_get_field_groups();
			} else {
				$groups = apply_filters( 'acf/get_field_groups', [] );
			}

			$options_page_groups_ids = array();

			if ( function_exists( 'acf_options_page' ) ) {
				$pages = acf_options_page()->get_pages();

				foreach ( $pages as $slug => $page ) {
					$options_page_groups = acf_get_field_groups( array(
						'options_page' => $slug,
					) );

					foreach ( $options_page_groups as $options_page_group ) {
						$options_page_groups_ids[] = $options_page_group['ID'];
					}
				}
			}

			$result      = array();
			$whitelisted = $this->whitelisted_fields();

			foreach ( $groups as $group ) {

				// ACF >= 5.0.0
				if ( function_exists( 'acf_get_fields' ) ) {
					$fields = acf_get_fields( $group['ID'] );
				} else {
					$fields = apply_filters( 'acf/field_group/get_fields', [], $group['id'] );
				}

				$options = [];

				if ( ! is_array( $fields ) ) {
					continue;
				}

				$has_option_page_location = in_array( $group['ID'], $options_page_groups_ids, true );
				$is_only_options_page = $has_option_page_location && 1 === count( $group['location'] );

				foreach ( $fields as $field ) {

					if ( ! isset( $whitelisted[ $field['type'] ] ) ) {
						continue;
					}

					if ( $has_option_page_location ) {
						$key = 'options::' . $field['name'];

						$options[ $key ] = array(
							'type'  => $field['type'],
							'label' => __( 'Options', 'sonaar-music' ) . ':' . $field['label'],
						);

						if ( $is_only_options_page ) {
							continue;
						}
					}

					$key = $field['name'];
					$options[ $key ] = array(
						'type'  => $field['type'],
						'label' => $field['label']
					);
				}

				if ( empty( $options ) ) {
					continue;
				}

				$result[] = array(
					'label'   => $group['title'],
					'options' => $options,
				);
			}

			$this->fields_groups = $result;
			return $this->fields_groups;

		}
/**
		 * Returns whitelisted fields
		 *
		 * @return [type] [description]
		 */
		public function whitelisted_fields() {

			return array(
				'text'             => array( 'field', 'link' ),
				'textarea'         => array( 'field' ),
				'number'           => array( 'field' ),
				'range'            => array( 'field' ),
				'email'            => array( 'field', 'link' ),
				'url'              => array( 'field', 'link' ),
				'wysiwyg'          => array( 'field' ),
				'image'            => array( 'link', 'image' ),
				'file'             => array( 'field', 'link' ),
				'gallery'          => array( 'field' ),
				'select'           => array( 'field' ),
				'radio'            => array( 'field' ),
				'checkbox'         => array( 'field' ),
				'button_group'     => array( 'field' ),
				'true_false'       => array( 'field' ),
				'page_link'        => array( 'field', 'link' ),
				'post_object'      => array( 'field', 'link' ),
				'relationship'     => array( 'field', 'link' ),
				'taxonomy'         => array( 'field', 'link' ),
				'date_picker'      => array( 'field', 'link' ),
				'date_time_picker' => array( 'field' ),
				'time_picker'      => array( 'field' ),
				'repeater'         => array( 'repeater' ),
				'oembed'           => array( 'field' ),
			);

		}
	public function render_plain_content() {
		$settings = $this->get_settings_for_display();
		$playlist_show_album_market = ( ( $settings['player_layout'] != 'skin_button' && $settings['playlist_show_album_market']=="yes" )  || ( $settings['player_layout'] == 'skin_button' && $settings['playlist_show_album_market_skin_button']=="yes" ) ) ? 'true' : 'false';
		$playlist_reverse_tracklist = (function_exists( 'run_sonaar_music_pro' ) && isset($settings['reverse_tracklist']) && $settings['reverse_tracklist'] == "yes") ? true : false;
		if ( function_exists( 'run_sonaar_music_pro' ) ){
			$sticky_player = $settings['enable_sticky_player'];
			$shuffle = $settings['enable_shuffle'];
			$wave_color = $settings['soundWave_bg_bar_color'];
			$wave_progress_color = $settings['soundWave_progress_bar_color'];
		}else{
			$sticky_player = false;
			$shuffle = false;
			$wave_color = false;
			$wave_progress_color = false;
		}
		
		$shortcode = '[sonaar_audioplayer titletag_soundwave="'. isset($settings['title_html_tag_soundwave']) .'" titletag_playlist="'. isset($settings['title_html_tag_playlist']) .'" store_title_text="' . isset($settings['store_title_text']) .'" hide_artwork="' . isset($playlist_playlist_hide_artwork) .'" show_playlist="' . isset($playlist_show_playlist) .'" reverse_tracklist="' . $playlist_reverse_tracklist .'" show_track_market="' . isset($playlist_hide_track_market) .'" show_album_market="' . isset($playlist_show_album_market) .'" hide_timeline="' . isset($playlist_show_soundwave) .'" sticky_player="' . isset($sticky_player) .'" wave_color="' . isset($wave_color) .'" wave_progress_color="' . isset($wave_progress_color) .'" shuffle="' . isset($shuffle) .'" ';
		
		if ($settings['play_current_id']=='yes' || $settings['playlist_source']=='from_current_posts'){
			$postid = get_the_ID();
			$shortcode .= 'albums="' . $postid . '" ';
		}else{
			$display_playlist_ar = $settings['playlist_list'];

			if(is_array($display_playlist_ar)){
				$display_playlist_ar = implode(", ", $display_playlist_ar); 
			}
			if (!$display_playlist_ar) { //If no playlist is selected, play the latest playlist
				
				if($settings['playlist_source'] == 'from_cpt' ){
					$shortcode .= 'play-latest="true" ';
				}
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					if ($settings['playlist_source'] == 'from_elementor'  && !$settings['feed_repeater'] ){
						echo esc_html__('Add tracks in the widget settings.', 'sonaar-music');
					}
				}
			}else{
				$shortcode .= 'albums="' . $display_playlist_ar . '" ';
			}
		
		}
		$shortcode .= ']';
		echo do_shortcode( $shortcode );
	}
}
Plugin::instance()->widgets_manager->register( new SR_Audio_Player() );
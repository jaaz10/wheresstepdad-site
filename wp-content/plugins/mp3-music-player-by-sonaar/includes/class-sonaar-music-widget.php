<?php
/**
* Radio Widget Class
*
* @since 1.6.0
* @todo  - Add options
*/

class Sonaar_Music_Widget extends WP_Widget{
    /**
    * Widget Defaults
    */
    
    public static $widget_defaults;
    
    /**
    * Register widget with WordPress.
    */
    
    function __construct (){
        
        if ( function_exists( 'WC' ) && WC()->session ) {
            register_taxonomy( 'product_cat', array('product'), array() ); // taxonomy must be registered for the json file.
        }
        
        $widget_ops = array(
        'classname'   => 'sonaar_music_widget',
        'description' => esc_html_x('A simple radio that plays a list of songs from selected albums.', 'Widget', 'sonaar-music')
        );
        
        self::$widget_defaults = array(
            'title'        => '',
            'store_title_text' => '',
            'albums'     	 => array(),
            'hide_artwork' => false,
            'sticky_player' => 0,
            'show_album_market' => 0,
            'show_track_market' => 0,
            //'remove_player' => 0, // deprecated and replaced by hide_timeline
            'hide_timeline' =>0,
            
            
            );
            
            if ( isset($_GET['load']) && $_GET['load'] == 'playlist.json' ) {
                $this->print_playlist_json();
        }
        
        parent::__construct('sonaar-music', esc_html_x('Sonaar: Music Player', 'Widget', 'sonaar-music'), $widget_ops);
        
    }
    
    /**
    * Front-end display of widget.
    */
    public function widget ( $args, $instance ){
            $instance = wp_parse_args( (array) $instance, self::$widget_defaults );
            
            $widget_id = (isset($instance['id']))? $instance['id']: $args["widget_id"];
            $elementor_widget = (bool)( isset( $instance['elementor'] ) )? true: false; //Return true if the widget is set in the elementor editor 
            $args['before_title'] = "<span class='heading-t3'></span>".$args['before_title'];
            $args['before_title'] = str_replace('h2','h3',$args['before_title']);
            $args['after_title'] = str_replace('h2','h3',$args['after_title']);
            $feed = ( isset( $instance['feed'] ) )? $instance['feed']: '';
            $feed_title =  ( isset( $instance['feed_title'] ) )? $instance['feed_title']: '';
            $feed_img =  ( isset( $instance['feed_img'] ) )? $instance['feed_img']: '';
            $el_widget_id = ( isset( $instance['el_widget_id'] ) )? $instance['el_widget_id']: '';
            $single_playlist = (is_single()) ? true : false;
            $playlatestalbum = ( isset( $instance['play-latest'] ) ) ? true : false;
            $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
            $albums = $instance['albums'];
            $terms = ( isset( $instance['category'] ) ) ? $instance['category'] : false;
            $posts_per_pages = (isset($instance['posts_per_page']) && $instance['posts_per_page'] !== '') ? (int)$instance['posts_per_page'] : -1;
            $audio_meta_field =  ( function_exists( 'run_sonaar_music_pro' ) &&  isset( $instance['audio_meta_field'] ) ) ? $instance['audio_meta_field'] : '';
            $repeater_meta_field =  ( function_exists( 'run_sonaar_music_pro' ) && isset( $instance['repeater_meta_field'] ) ) ? $instance['repeater_meta_field'] : '';
            $tracklistGrid = ( isset( $instance['tracklist_layout'] ) && $instance['tracklist_layout'] == 'grid') ? true : false;
            $data_column_params = '';
            if($tracklistGrid ){
                $tracklistGrid_col = ( isset( $instance['grid_column_number'] ) ) ? $instance['grid_column_number']: '4,3,2';
                $tracklistGrid_col = explode(',', $tracklistGrid_col);
                $tracklistGrid_col_desktop = $tracklistGrid_col[0];
                $tracklistGrid_col_tablet = ( isset( $tracklistGrid_col[1] ) && isset( $tracklistGrid_col[2] ) && $tracklistGrid_col[0] != $tracklistGrid_col[1] )? $tracklistGrid_col[1] : false;
                if( isset( $tracklistGrid_col[1] ) && isset( $tracklistGrid_col[2] ) && $tracklistGrid_col[1] != $tracklistGrid_col[2]  ){
                    $tracklistGrid_col_mobile = $tracklistGrid_col[2];
                }else if( isset( $tracklistGrid_col[1] ) && ! isset( $tracklistGrid_col[2] ) && $tracklistGrid_col[0] != $tracklistGrid_col[1] ){
                    $tracklistGrid_col_mobile = $tracklistGrid_col[1];
                }else{
                    $tracklistGrid_col_mobile = false;
                }
                $data_column_params .= ' data-col="' . $tracklistGrid_col_desktop . '"';
                $data_column_params .= ($tracklistGrid_col_tablet !== false )?' data-col-tablet="' . $tracklistGrid_col_tablet . '"' : '';
                $data_column_params .= ($tracklistGrid_col_mobile !== false )?' data-col-mobile="' . $tracklistGrid_col_mobile . '"' : '';
            }
          

            $cf_data_formatted = '';
            if( $albums == 'all' ){
                $albums = array();
                $query_args = array(
                    'post_status' => 'publish',
                    'posts_per_page' => (int)$posts_per_pages,
                    'post_type' => SR_PLAYLIST_CPT,
                );
                $i = 0;
                $r = new WP_Query( $query_args );
                if ( $r->have_posts() ){
                  
                    while ( $r->have_posts() ) : $r->the_post();
                        array_push($albums, $r->posts[$i]->ID);
                        $i++;
                    endwhile;
                    $albums = implode(",", $albums);
                    wp_reset_query();
                }else{
                    echo '<div>' . esc_html__("No Playlist Post found ", 'sonaar-music') . '</div>';
                    return;
                }
            }
            if (isset($terms) && $terms !=='' && $terms != false){
                $albums = array();
                
                $first_post_ids = get_posts( array(
                    'fields'         => 'ids', // only return post ID´s
                    'posts_per_page' => '-1',
                    'post_type'      => SR_PLAYLIST_CPT,
                ));
                $second_post_ids = get_posts( array(
                    'fields'         => 'ids', // only return post ID´s
                    'posts_per_page' => '-1',
                    'post_type'      => array('product'),
                ));
                $merged_post_ids = array_merge( $first_post_ids, $second_post_ids);

                $query_args = array(
                    'post_status' => 'publish',
                    'posts_per_page' => $posts_per_pages,
                    'post_type' => 'any', // any post type
                    'post__in'  => $merged_post_ids, // our merged queries
                );
                if( $this->getOptionValue('reverse_tracklist', $instance) ){
                    $query_args['order'] = 'ASC';
                }
                if($terms != 'all'){
                    $terms = explode(", ", $terms); 
                    $query_args += [
                        'tax_query' => array(
                            'relation' => 'OR',
                            array(
                            'taxonomy' => 'playlist-category',
                            'field'    => 'id',
                            'terms'    => $terms
                            ),
                            array(
                            'taxonomy' => 'podcast-show',
                            'field'    => 'id',
                            'terms'    => $terms
                            ),
                            array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'id',
                            'terms'    => $terms
                            ),
                    )];
                }

                $i = 0;
                $r = new WP_Query( $query_args );
                if ( $r->have_posts() ){
                  
                    while ( $r->have_posts() ) : $r->the_post();
                        array_push($albums, $r->posts[$i]->ID);
                        $i++;
                    endwhile;
                    $albums = implode(",", $albums);
                    wp_reset_query();
                }else{
                    echo '<div>' . esc_html__("Oops! No post found.", 'sonaar-music') . '</div>';
                    return;
                }
            }
            
            if ( is_array($albums)) {
                $albums = implode(',', $albums);
            }
            if ( FALSE === get_post_status( $albums ) || get_post_status ( $albums ) == 'trash') {
                // if album is set by is deleted afterward, let fallback on the latest album post.
                $playlatestalbum = true;
            }
            
            if($playlatestalbum && $terms == false){
                $recent_posts = wp_get_recent_posts(array('post_type'=>SR_PLAYLIST_CPT, 'post_status' => 'publish', 'numberposts' => 1));
                if (!empty($recent_posts)){
                    $albums = $recent_posts[0]["ID"];
                }
            }
            
            if( empty($albums) ) {
                // SHORTCODE IS DISPLAYED BUT NO ALBUMS ID ARE SET. EITHER GET INFO FROM CURRENT POST OR RETURN NO PLAYLIST SELECTED
                $trackSet = '';
                $albums = get_the_ID();
               
                $album_tracks =  get_post_meta( $albums, 'alb_tracklist', true);

                if (is_array($album_tracks)){
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
                }                
                if (isset($feed) && strlen($feed) > 1 ){
                     $trackSet = true;

                }
    
                if(isset($audio_meta_field) && $audio_meta_field !==''){
                    $trackSet = true;
                }

                if ( ($album_tracks == 0 || !$trackSet) && (!isset($feed) && strlen($feed) < 1 )){
                    echo esc_html__("No playlist selected", 'sonaar-music');
                    return;
                }
                if (!$feed && !$trackSet){
                    return;
                }
            }


            $iron_widget_newClass = ''; 

            /* TRACKLIST GRID LAYOUT: Default value */
            if ( isset( $instance['tracklist_layout'] ) && $instance['tracklist_layout'] == 'grid' && !$elementor_widget){
                $instance['player_layout'] =( isset( $instance['player_layout'] ) )? $instance['player_layout'] : 'skin_boxed_tracklist';
                $instance['show_playlist'] =( isset( $instance['show_playlist'] ) )? $instance['show_playlist'] : 'true';
                $instance['track_artwork'] =( isset( $instance['track_artwork'] ) )? $instance['track_artwork'] : 'true';
                $instance['track_artwork_play_button'] = ( isset( $instance['track_artwork_play_button'] ) )? $instance['track_artwork_play_button'] : 'true';
                $instance['track_artwork_play_on_hover'] = ( isset( $instance['track_artwork_play_on_hover'] ) )? $instance['track_artwork_play_on_hover'] : 'true';
            }

            /* SKIN BUTTON LAYOUT */
            if( isset($instance['player_layout'] ) && $instance['player_layout'] == 'skin_button'){
                $iron_widget_newClass .= ' srp_player_button'; 
                $ironAudioClass = ' srp_player_button';
                $instance['player_layout'] = 'skin_boxed_tracklist';
                $instance['hide_artwork'] ='true'; 
                $instance['hide_album_title'] = 'true'; 
                $instance['hide_album_subtitle'] = 'true';
                $instance['hide_player_title'] ='true'; 
                $instance['hide_track_title'] ='true';  
                $instance['show_publish_date'] = 'false';
                $instance['show_skip_bt'] = (isset($instance['show_skip_bt']))? $instance['show_skip_bt']:'false';
                $instance['show_volume_bt'] = (isset($instance['show_volume_bt']))? $instance['show_volume_bt']:'false';
                $instance['show_speed_bt'] = (isset($instance['show_speed_bt']))? $instance['show_speed_bt']:'false';
                $instance['show_shuffle_bt'] = (isset($instance['show_shuffle_bt']))? $instance['show_shuffle_bt']:'false';
                $instance['use_play_label'] = (isset($instance['use_play_label']))? $instance['use_play_label']:'true';
                $instance['use_play_label_with_icon'] = (isset($instance['use_play_label_with_icon']) && function_exists( 'run_sonaar_music_pro' ) )? $instance['use_play_label_with_icon']:'true';
                $instance['progressbar_inline'] = 'true';

                if( !isset($instance['hide_progressbar']) ){
                    $instance['hide_progressbar'] = 'true';
                }
                if($instance['hide_progressbar'] == 'false'){
                    $instance['inline'] = 'false'; // Always disable inline when progressbar is shown
                }
            }else{
                if( !function_exists( 'run_sonaar_music_pro' ) ){
                    $instance['use_play_label_with_icon'] = 'false';
                }
            }

            
            

            $scrollbar = ( isset( $instance['scrollbar'] ) )? $instance['scrollbar']: false;
            $show_album_market = (bool) ( isset( $instance['show_album_market'] ) )? $instance['show_album_market']: 0;
            $show_track_market = (bool) ( isset( $instance['show_track_market'] ) )? $instance['show_track_market']: 0;
            $store_title_text = $instance['store_title_text'];
            $hide_artwork = (bool)( isset( $instance['hide_artwork'] ) )? $instance['hide_artwork']: false;
            $displayControlArtwork = (bool)( isset( $instance['display_control_artwork'] ) )? $instance['display_control_artwork']: false;
            $hide_control_under = (bool)( isset( $instance['hide_control_under'] ) )? $instance['hide_control_under']: false;
            $hide_track_title = (bool)( isset( $instance['hide_track_title'] ) )? $instance['hide_track_title']: false;
            $hide_player_title = (bool)( isset( $instance['hide_player_title'] ) )? $instance['hide_player_title']: false;
            $hide_times = (bool)( isset( $instance['hide_times'] ) )? $instance['hide_times']: false;
            $artwork= (bool)( isset( $instance['artwork'] ) )? $instance['artwork']: false;
            $track_artwork = (bool)( isset( $instance['track_artwork'] ) )? $instance['track_artwork']: false;
            $remove_player = (bool) ( isset( $instance['remove_player'] ) )? $instance['remove_player']: false; // deprecated and replaced by hide_timeline. keep it for fallbacks
            $hide_timeline = (bool) ( isset( $instance['hide_timeline'] ) )? $instance['hide_timeline']: false;
            $noLoopTracklist = (bool) ( isset( $instance['no_loop_tracklist'] ) && function_exists( 'run_sonaar_music_pro' ))? $instance['no_loop_tracklist']: false;
            $notrackskip = (bool) ( isset( $instance['notrackskip'] ) )? $instance['notrackskip']: false;
            $progressbar_inline = (bool) ( isset( $instance['progressbar_inline'] ) )? $instance['progressbar_inline']: false;
            $sticky_player = (bool)( isset( $instance['sticky_player'] ) )? $instance['sticky_player']: false;
            $shuffle = (bool)( isset( $instance['shuffle'] ) )? $instance['shuffle']: false;
            $wave_color = (bool)( isset( $instance['wave_color'] ) )? $instance['wave_color']: false;
            $wave_progress_color = (bool)( isset( $instance['wave_progress_color'] ) )? $instance['wave_progress_color']: false;
            $show_playlist = (bool)( isset( $instance['show_playlist'] ) )? $instance['show_playlist']: false;
            $title_html_tag_playlist = ( isset( $instance['titletag_playlist'] ) )? $instance['titletag_playlist']: 'h3';
            $title_html_tag_soundwave = ( isset( $instance['titletag_soundwave'] ) )? $instance['titletag_soundwave']: 'div';
            $track_title_html_tag_soundwave = ( isset( $instance['track_titletag_soundwave'] ) && $instance['track_titletag_soundwave'] != '' )? $instance['track_titletag_soundwave']: $title_html_tag_soundwave;
            $title_html_tag_playlist = ($title_html_tag_playlist == '') ? 'div' : $title_html_tag_playlist;
            $hide_album_title = (bool)( isset( $instance['hide_album_title'] ) )? $instance['hide_album_title']: false;
            $hide_album_subtitle = (bool)( isset( $instance['hide_album_subtitle'] ) )? $instance['hide_album_subtitle']: false;
            $playlist_title = ( isset( $instance['playlist_title'] ) )? $instance['playlist_title']: false;   
            $artistWrap = ( isset( $instance['artist_wrap'] ) &&  $instance['artist_wrap'] === "true" )? true : false;   
            $hide_trackdesc = ( isset( $instance['hide_trackdesc'] ) &&  $instance['hide_trackdesc'] == true ) ? true : false;
            $track_desc_postcontent = ( isset( $instance['track_desc_postcontent'] ) &&  $instance['track_desc_postcontent'] == true ) ? true : false;
            $track_desc_lenght = ( isset( $instance['track_desc_lenght'] ) )? $instance['track_desc_lenght']: 55;
            $strip_html_track_desc = ( isset( $instance['strip_html_track_desc'] ) && ( $instance['strip_html_track_desc'] == 'false' || $instance['strip_html_track_desc'] === false || $instance['strip_html_track_desc'] === '' ) )?  false : true;
            $albumStorePosition = ( isset( $instance['album_store_position'] ) ) ? $instance['album_store_position'] : '' ;
            $showPublishDate = ( $this->getOptionValue('show_publish_date', $instance) && !$feed)? true : false;
            $dateFormat = (Sonaar_Music::get_option('player_date_format', 'srmp3_settings_widget_player') && Sonaar_Music::get_option('player_date_format', 'srmp3_settings_widget_player') != '' ) ? Sonaar_Music::get_option('player_date_format', 'srmp3_settings_widget_player') : '';
            $labelPlayTxt = (Sonaar_Music::get_option('labelPlayTxt', 'srmp3_settings_widget_player')) ? Sonaar_Music::get_option('labelPlayTxt', 'srmp3_settings_widget_player') : 'Play';
            $labelPlayTxt = ( function_exists('run_sonaar_music_pro') && isset($instance['play_text']) && $instance['play_text'] != '') ? $instance['play_text'] : $labelPlayTxt; 
            $labelPauseTxt = (Sonaar_Music::get_option('labelPauseTxt', 'srmp3_settings_widget_player')) ? Sonaar_Music::get_option('labelPauseTxt', 'srmp3_settings_widget_player') : 'Pause'; 
            $labelPauseTxt = (function_exists('run_sonaar_music_pro') && isset($instance['pause_text']) && $instance['pause_text'] != '') ? $instance['pause_text'] : $labelPauseTxt;
            $usePlayLabel = ( $this->getOptionValue( 'use_play_label', $instance, false ) || ( $this->getOptionValue( 'use_play_label_with_icon', $instance, false ) && ! $instance['use_play_label'] && $instance['use_play_label'] != 'false' ) || isset($instance['play_text']) || isset($instance['pause_text']) )? true : false;
            $labelTitleColumn = (Sonaar_Music::get_option('tracklist_column_title_label', 'srmp3_settings_widget_player')) ? Sonaar_Music::get_option('tracklist_column_title_label', 'srmp3_settings_widget_player') : esc_html__('Title', 'sonaar-music'); 
            $labelSearch = (Sonaar_Music::get_option('tracklist_search_label', 'srmp3_settings_widget_player')) ? Sonaar_Music::get_option('tracklist_search_label', 'srmp3_settings_widget_player') : esc_html__('Search', 'sonaar-music'); 
            $labelSearchPlaceHolder = (Sonaar_Music::get_option('tracklist_search_placeholder', 'srmp3_settings_widget_player')) ? Sonaar_Music::get_option('tracklist_search_placeholder', 'srmp3_settings_widget_player') : esc_html__('Enter any keyword', 'sonaar-music'); 
            $labelNoResult1 = (Sonaar_Music::get_option('tracklist_no_result_1_label', 'srmp3_settings_widget_player')) ? Sonaar_Music::get_option('tracklist_no_result_1_label', 'srmp3_settings_widget_player') : esc_html__('Sorry, no results.', 'sonaar-music'); 
            $labelNoResult2 = (Sonaar_Music::get_option('tracklist_no_result_2_label', 'srmp3_settings_widget_player')) ? Sonaar_Music::get_option('tracklist_no_result_2_label', 'srmp3_settings_widget_player') : esc_html__('Please try another keyword', 'sonaar-music'); 
            $show_cf_headings = false;
            $tracks_per_page = ( !empty( $instance['tracks_per_page'] ) )? $instance['tracks_per_page']: null;
            $sr_cf_heading = '';
            if(!function_exists( 'run_sonaar_music_pro' )){
                $hide_trackdesc = true;
            }else{
                $notrackskip = apply_filters( 'srp_track_skip_attribute', $notrackskip);
            }
            if ( isset($instance['show_progressbar']) ){
                if ( $instance['show_progressbar'] == 'true' ){
                    $instance['hide_progressbar'] = 'false'; // Always set "hide_progressbar" to false when "show_progressbar" is to true. We have created the "show_progressbar" parameter for the "skin_button" layout
                }else if( $instance['show_progressbar'] == 'false' ){
                    $instance['hide_progressbar'] = 'true'; // Always set "hide_progressbar" to true when "show_progressbar" is to false. We have created the "show_progressbar" parameter for the "skin_button" layout
                }
            }

            $hide_progressbar = filter_var(( isset( $instance['hide_progressbar'] ) )? $instance['hide_progressbar']: false, FILTER_VALIDATE_BOOLEAN);

            $showControlOnHover = ( isset( $instance['show_control_on_hover'] ) && $instance['show_control_on_hover'] == 'true' ) ?  true : false ;
            
            $hasMetaData = ($this->getOptionValue('show_shuffle_bt', $instance) || $this->getOptionValue('show_speed_bt', $instance) || $this->getOptionValue('show_volume_bt', $instance) || $this->getOptionValue('show_skip_bt', $instance));
            
            //Field validation
            $sr_html_allowed_tags = array('h1', 'h2', 'h3', 'h4','h5','h6','div','span', 'p');
            if (!in_array($title_html_tag_playlist, $sr_html_allowed_tags, true)) {
                $title_html_tag_playlist = 'h3';
            }
            if (!in_array($title_html_tag_soundwave, $sr_html_allowed_tags, true)) {
                $title_html_tag_soundwave = 'div';
            }
            if (!in_array($track_title_html_tag_soundwave, $sr_html_allowed_tags, true)) {
                $track_title_html_tag_soundwave = 'div';
            }
      
            if($sticky_player){
                if ( function_exists( 'run_sonaar_music_pro' )){
                    $sticky_player = ($instance['sticky_player']=="true" || $instance['sticky_player']==1) ? : false;
                }else{
                    $sticky_player = false;
                }
            }
            if($show_playlist){
                $show_playlist = ($instance['show_playlist']=="true" || $instance['show_playlist']==1) ? : false;      
            }
            if($hide_track_title){
                $hide_track_title = ($instance['hide_track_title']=="true" || $instance['hide_track_title']==1) ? : false;      
            }
            if($show_track_market){
                $show_track_market = ($instance['show_track_market']=="true" || $instance['show_track_market']==1) ? : false;      
            }
            if($show_album_market){
                $show_album_market = ($instance['show_album_market']=="true" || $instance['show_album_market']==1) ? : false;      
            }
            if($hide_artwork){
                $hide_artwork = ($instance['hide_artwork']=="true" || $instance['hide_artwork']==1) ? : false;      
            }
            if($track_artwork){
                if ( function_exists( 'run_sonaar_music_pro' )){
                    $track_artwork = ($instance['track_artwork']=="true" || $instance['track_artwork']==1) ? : false;      
                }else{
                    $track_artwork = false;
                }
            }
            if($displayControlArtwork){
                $displayControlArtwork = ($instance['display_control_artwork']=="true" || $instance['display_control_artwork']==1) ? : false;      
            }
            if($hide_control_under){
                $hide_control_under = ($instance['hide_control_under']=="true") ? true : false;      
            }
            if($hide_player_title){
                $hide_player_title = ($instance['hide_player_title']=="true") ? true : false;      
            }
            if($hide_album_title){
                $hide_album_title = ($instance['hide_album_title']=="true") ? true : false;      
            }
            if($hide_album_subtitle){
                $hide_album_subtitle = ($instance['hide_album_subtitle']=="true") ? true : false;      
            }
            if($progressbar_inline){
                $progressbar_inline = ($instance['progressbar_inline']=="true" || $instance['progressbar_inline']==1) ? true : false;      
            }
            if($hide_times){
                $hide_times = ($instance['hide_times']=="true" || $instance['hide_times']==1) ? true : false;      
            }
            if($noLoopTracklist && isset($instance['no_loop_tracklist'])){
                $noLoopTracklist = ($instance['no_loop_tracklist']=="true" || $instance['no_loop_tracklist']==1) ? 'on' : false;      
            }
            if($notrackskip && isset($instance['notrackskip'])){
                $notrackskip = ($instance['notrackskip']=="true" || $instance['notrackskip']==1) ? 'on' : false;      
            }
            if($remove_player){
                $remove_player = ($instance['remove_player']=="true" || $instance['remove_player']==1) ? true : false;      
            }

            if($hide_timeline){
                $hide_timeline = ($instance['hide_timeline']=="true" || $instance['hide_timeline']==1) ? true : false;      
            }
           
            $store_buttons = array();
            $all_category = (isset($instance['category']) && $instance['category']=='all') ? true : false;
           
            $playlist = $this->get_playlist($albums, $title, $feed_title, $feed, $feed_img, $el_widget_id, $artwork, $posts_per_pages, $all_category, $single_playlist, $this->getOptionValue('reverse_tracklist', $instance), $audio_meta_field, $repeater_meta_field, 'widget', $track_desc_postcontent);

            if (isset($playlist['tracks'][0]['poster']) =="" || !$playlist['tracks'][0]['poster'] && !$artwork ){
                $hide_artwork = true;
            }

            if ( isset($playlist['tracks']) && ! empty($playlist['tracks']) )
                $player_message = esc_html_x('Loading tracks...', 'Widget', 'sonaar-music');
            else
                $player_message = esc_html_x('No tracks founds...', 'Widget', 'sonaar-music');
            
            /***/
            
            if ( ! $playlist )
                return;   
            
            if($show_playlist) { 
                $iron_widget_newClass .= ' playlist_enabled'; 
            } 

            if($this->getOptionValue('track_market_inline', $instance) || ( isset($instance['custom_fields_columns']) && $instance['custom_fields_columns'] ) || $tracklistGrid) { 
                $iron_widget_newClass .= ' sr_track_inline_cta_bt__yes'; 
            } 

            if($this->getOptionValue('inline', $instance, false)) { 
                $iron_widget_newClass .= ' srp_inline'; 
            } 
            $args['before_widget'] = str_replace('class="iron_widget_radio"', 'id="'. $widget_id .'" class="iron_widget_radio'. $iron_widget_newClass .'"', $args['before_widget'] );    
        
		/* Enqueue Sonaar Music related CSS and Js file */
		wp_enqueue_style( 'sonaar-music' );
		wp_enqueue_style( 'sonaar-music-pro' );
		wp_enqueue_script( 'sonaar-music-mp3player' );
		wp_enqueue_script( 'sonaar-music-pro-mp3player' );
		wp_enqueue_script( 'sonaar_player' );
		if ( function_exists('sonaar_player') ) {
			add_action('wp_footer','sonaar_player', 12);
		}
        
        if ( ( $this->getOptionValue('show_name_filter', $instance) || 
            $this->getOptionValue('show_date_filter', $instance) ||
            $this->getOptionValue('show_duration_filter', $instance)  ) && 
            $this->getOptionValue('searchbar_show_filters', $instance) &&
            function_exists( 'run_sonaar_music_pro' ) 
        ){
            $searchbarShowFilters = true;
        }else{
            $searchbarShowFilters = false;
        }
        if( isset( $instance['custom_fields_columns']) && function_exists( 'run_sonaar_music_pro' ) &&  get_site_option('SRMP3_ecommerce') == '1' ){
        
            $show_cf_headings =   ( isset( $instance['custom_fields_heading'] ) && $instance['custom_fields_heading'] == 'true')? true : false;
            $sr_cf_heading_html_ar = array();
            $sr_cf_heading_html_ar[] = '<div class="srp_sort sr-playlist-heading-child sr-playlist-cf--title" data-sort="tracklist-item-title" title="Title">' . esc_html($labelTitleColumn) . '</div>';
            $custom_fields_columns = $instance['custom_fields_columns'];
            $custom_fields_columns_ar = explode(';', $custom_fields_columns);
            $cf_input_formatted_ar =  array();
            $headingLabel='';
            $headingID ='';
            foreach ($custom_fields_columns_ar as $key => $valueString) {
                $value = explode('::', $valueString);
                $headingLabel = $value[0];
                $headingID= ( isset($value[1]) ) ? $value[1] : '';
                $cf_columnWidth = ( isset($value[2]) ) ? $value[2] :'100px';
                $sr_cf_heading_html = '<div class="srp_sort sr-playlist-heading-child" data-sort="sr-playlist-cf--' . esc_attr($headingID) . '"style="flex: 0 0 ' . esc_attr($cf_columnWidth) . ';" title="' .  esc_html($headingLabel) . '">' .  esc_html($headingLabel) . '</div>';
                $sr_cf_heading_html_ar[] =  $sr_cf_heading_html;
            }
        }else{
            $custom_fields_columns = false;
        }

        if( ( $searchbarShowFilters || $this->getOptionValue('searchbar', $instance) || $custom_fields_columns || $tracks_per_page ) && function_exists( 'run_sonaar_music_pro' ) ){
            wp_enqueue_script( 'sonaar-list' );
        }

        echo $args['before_widget'];
        
        if ( ! empty( $title ) )
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
    
        $firstAlbum = explode(',', $albums);
        $firstAlbum = $firstAlbum[0];
        
        $albums_ar = explode(',', $albums);
       
        if( isset( $instance['player_layout'])){  
            $playerWidgetTemplate = ($instance['player_layout'] == 'skin_boxed_tracklist' )? 'skin_boxed_tracklist' :'skin_float_tracklist'; //if player_layout parameter is set in the shortcode
        }else{  
            if(get_post_meta($firstAlbum, 'post_player_type', true)=='default') {
                $playerWidgetTemplate = ( Sonaar_Music::get_option('player_widget_type', 'srmp3_settings_general')  == 'skin_boxed_tracklist' )? 'skin_boxed_tracklist' :'skin_float_tracklist'; //if player_layout is not set or set to default through the post setting
            }else{
                $playerWidgetTemplate = ( get_post_meta($firstAlbum, 'post_player_type', true)  == 'skin_boxed_tracklist' )? 'skin_boxed_tracklist' :'skin_float_tracklist'; //Get the player_layout from the plugin settings
            };
        }
        
        $ironAudioClass = '';
        $ironAudioClass .= ( $show_playlist ) ? ' show-playlist' :'';
        $ironAudioClass .= ( $hide_artwork == "true" ) ? ' sonaar-no-artwork' :'';
        $ironAudioClass .= ' sr_waveform_' . Sonaar_Music::get_option('waveformType', 'srmp3_settings_general');
        $ironAudioClass .= ($displayControlArtwork) ? ' sr_player_on_artwork' : '';
        $ironAudioClass .= ( $remove_player || $hide_timeline )? ' srp_hide_player': '' ;
        $ironAudioClass .= ( $hide_progressbar )? ' srp_hide_progressbar': '' ;
        $ironAudioClass .= ( $hide_times )? ' srp_hide_time': '' ;
        $ironAudioClass .= ( $single_playlist )? ' srp_post_player': '' ;
        $ironAudioClass .= ( $hasMetaData )? ' srp_has_metadata': '' ;
        $ironAudioClass .= ( $this->getOptionValue('hide_track_number', $instance) && $show_playlist )? ' srp_hide_tracknumber': '' ;
        $ironAudioClass .= ( $custom_fields_columns ) ? ' srp_has_customfields': '' ;
        $ironAudioClass .= ( $noLoopTracklist == 'on' )? ' srp_noLoopTracklist': '' ;

        $album_ids_with_show_market = ( $show_album_market )? $albums : 0 ;
        
        $format_playlist ='';

        if(Sonaar_Music::get_option('show_artist_name', 'srmp3_settings_general') ){
            $artistSeparator = (Sonaar_Music::get_option('artist_separator', 'srmp3_settings_general') && Sonaar_Music::get_option('artist_separator', 'srmp3_settings_general') != '' && Sonaar_Music::get_option('artist_separator', 'srmp3_settings_general') != 'by')?Sonaar_Music::get_option('artist_separator', 'srmp3_settings_general'):  esc_html__('by', 'sonaar-music');
            $artistSeparator = ' ' . $artistSeparator . ' ';
        }else{
            $artistSeparator = '';
        }
        $storeButtonPosition = [];//$storeButtonPosition[ {track index} , {store index} ] , so $storeButtonPosition[ 0, 1 ] refers to the second(1) store button from the first(0) track
        $trackIndexRelatedToItsPost = 0; //variable required to set the data-store-id. Data-store-id is used to popup the right content.
        $currentTrackId = ''; //Used to set the $trackIndexRelatedToItsPost
        $trackNumber = 0; // Dont Count Relataded track
        $trackCountFromPlaylist = 0; //Count tracks from same playlist
        $playlistID = '';
        $excerptTrimmed = '[...]';
        $playlist_has_ctas = false;
        foreach( $playlist['tracks'] as $key1 => $track){
            $allAlbums = explode(', ', $albums);
            if( $playlistID == $track['sourcePostID'] ){
                $trackCountFromPlaylist++;
            }else{
                $playlistID = $track['sourcePostID'];
                $trackCountFromPlaylist = 0;
                if( $this->getOptionValue('reverse_tracklist', $instance) ){ //If reverse track list order is enable, start to count (the incrementation) from the number of track the playlist post has (in negative) rather than 0
                    $i = $key1 + 1;
                    while (  $i < (count( $playlist['tracks'] )) && $playlist['tracks'][$i]['sourcePostID'] == $playlistID ) {
                    $i++;
                    $trackCountFromPlaylist--;
                    }
                }
            }

            $relatedTrack = ( Sonaar_Music::get_option('sticky_show_related-post', 'srmp3_settings_sticky_player') != 'true' || $terms || in_array($track['sourcePostID'], $allAlbums) || $feed || $instance['albums'] == 'all' || !$single_playlist)? false : true; //True when the track is related to the selected playlist post as episode podcast from same category           
            $storeButtonPosition[$key1] = [];
            $trackdescEscapedValue = null;
            $trackUrl = $track['mp3'] ;
            $showLoading = $track['loading'] ;
            $song_store_list = '<span class="store-list">';
            if($currentTrackId != $track['sourcePostID']){ //Reset $trackIndexRelatedToItsPost counting. It is incremented at the end of the foreach.
                $currentTrackId = $track['sourcePostID'];
                $trackIndexRelatedToItsPost = 0; 
            }

            if( 
                ( get_post_meta( $currentTrackId, 'reverse_post_tracklist', true) || $this->getOptionValue('reverse_tracklist', $instance) ) &&  // If Reverse tracklist is set through the shortcode or throught the post settings, reverse the popup CTA odrer 
                !(get_post_meta( $currentTrackId, 'reverse_post_tracklist', true) && $this->getOptionValue('reverse_tracklist', $instance) )  //But if Reverse tracklist is set twice, dont reverse the popup CTA odrer
            ){
                $countTrackFromSamePlaylist = array_count_values( array_column($playlist['tracks'], 'sourcePostID') )[$currentTrackId];
                $trackIndex =  $countTrackFromSamePlaylist - 1 - $trackIndexRelatedToItsPost;
            }else{
                $trackIndex =  $trackIndexRelatedToItsPost;
            }
            
            if(isset($track['album_store_list'][0])){
                $track['song_store_list'] = ( isset($track['song_store_list'][0]) ) ? array_merge($track['song_store_list'], $track['album_store_list']) : $track['album_store_list'];
                $track['has_song_store'] = true;
            }
            if ( $show_track_market && is_array($track['song_store_list']) ){
 
                if ($track['has_song_store']){
                    $song_store_list_content = '';
                    foreach( $track['song_store_list'] as $key2 => $store ){
                        $storeButtonPosition[$key1][$key2]=[];
                        if(isset($store['link-option']) && $store['link-option'] == 'popup'){
                            if( array_key_exists('store-content', $store) ){
                                array_push ($storeButtonPosition[$key1][$key2], $store['store-content']);
                            }
                        }
                        if(!isset($store['store-icon'])){
                            $store['store-icon']='';
                        }
                        if(!isset($store['store-name'])){
                            $store['store-name']='';
                        }

                        $classes = 'song-store';
                        $extraAttributes = '';

                        
                        if(!isset($store['store-link'])){
                            $store['store-link']='';
                        }
                        $href = 'href="' . esc_url($store['store-link']) . '"';
                        $download="";
                        $label = '';

                        if($store['store-icon'] == "fas fa-download" && strpos($store['store-link'], '#') !== 0){
                            $download = ' download';
                        }

                        if(!isset($store['store-target'])){
                            $store['store-target']='_blank';
                        }
                        

                        if(isset($store['link-option']) && $store['link-option'] == 'popup'){ //if Popup content
                        $classes .= ' sr-store-popup';
                        $store['store-target'] = '_self';
                        $href = '';
                        }

                        if( isset($store['has-variation'])  && ! $store['has-variation'] && Sonaar_Music::get_option('wc_enable_ajax_addtocart', 'srmp3_settings_woocommerce') == 'true' ){ 
                            $classes .= ' add_to_cart_button ajax_add_to_cart';
                            $extraAttributes .= ' data-product_id="' . esc_attr($track['sourcePostID']) . '"';
                        }

                        if( function_exists( 'run_sonaar_music_pro' ) ){ 
                            $displayLabel = false;
                            if(Sonaar_Music::get_option('show_label', 'srmp3_settings_widget_player') != null){ //Display CTA Label: plugin settings
                                $displayLabel = filter_var(Sonaar_Music::get_option('show_label', 'srmp3_settings_widget_player'), FILTER_VALIDATE_BOOLEAN);
                            }
                            if( isset($instance['cta_track_show_label']) && $instance['cta_track_show_label'] != 'default') { //Display CTA Label: shortcode (second priority)
                                $displayLabel = filter_var($instance['cta_track_show_label'], FILTER_VALIDATE_BOOLEAN);
                            }
                            if(isset($store['show-label']) && $store['show-label'] != 'default'){
                                $displayLabel = filter_var($store['show-label'], FILTER_VALIDATE_BOOLEAN); //Display CTA Label: post setting (first priority)
                            }
                            if($displayLabel){
                                $classes .= ' sr_store_wc_round_bt';
                                $label = '<span class="srp_cta_label">' . esc_attr($store['store-name']) . '</span>';
                            }
                            if ( isset($store['has-variation']) && array_key_exists('sourcePostID', $track) && $this->ifProductHasVariation($track['sourcePostID']) && Sonaar_Music::get_option('wc_variation_lb', 'srmp3_settings_woocommerce') !='false'){
                                $classes .= ' srp_wc_variation_button';
                            }
                        }
                        
                        $song_store_list_content .= '<a ' . $href .  esc_html($download) . ' class="' . esc_attr($classes) . '" target="' .  esc_attr($store['store-target']) . '" title="' . esc_attr($store['store-name']) . '" data-source-post-id="' . esc_attr($track['sourcePostID']) . '" data-store-id="' . esc_attr($trackIndex . '-' . $key2) .'"'.$extraAttributes.' tabindex="1"><i class="' . esc_html($store['store-icon']) . '"></i>' . $label . '</a>';
                        $playlist_has_ctas = true;
                    }
                    $song_store_list .= '<div class="song-store-list-menu"><i class="fas fa-ellipsis-v"></i><div class="song-store-list-container">' . $song_store_list_content;
                    $song_store_list .= '</div></div>';
                }
            }
            $song_store_list .= '</span>';
           
            if (!$hide_trackdesc && isset($track['description']) && $track['description'] !==false) {
                $trackdesc_allowed_html = [
                    'a'      => [
                        'href'  => [],
                        'title' => [],
                    ],
                    'br'     => [],
                    'em'     => [],
                    'strong' => [],
                    'b' => [],
                    'p' => [],
                ];
                if( $strip_html_track_desc ){
                        $trackdescEscapedValue =  force_balance_tags( wp_trim_words( strip_shortcodes( $track['description'] ) , esc_attr($track_desc_lenght), $excerptTrimmed )) ;
                }else{
                        $trackdescEscapedValue =  force_balance_tags( html_entity_decode( wp_trim_words( htmlentities( strip_shortcodes( $track['description']   )), esc_attr($track_desc_lenght), $excerptTrimmed ) ));
                }
            }

            $playlistTrackDesc = (isset($trackdescEscapedValue)) ? '</div><div class="srp_track_description">'. wp_kses( $trackdescEscapedValue, $trackdesc_allowed_html ) .'</div>' : '</div>';
            $store_buttons = ( !empty($track["track_store"]) ) ? '<a class="button" target="_blank" href="'. esc_url( $track['track_store'] ) .'">'. esc_textarea( $track['track_buy_label'] ).'</a>' : '' ;
            $artistSeparator_string = ($track['track_artist']) ? $artistSeparator : '';//remove separator if no track doesnt have artist
            $artistSeparator_string = ($artistSeparator_string && $artistWrap) ? substr_replace($artistSeparator_string, '<br>', 0, 0) : $artistSeparator_string;
            $imageFormat = ( isset( $instance['track_artwork_format'] ) )? $instance['track_artwork_format'] : 'thumbnail' ;
            $track_image_url = (($track_artwork && isset($track['track_image_id'])) && ($track['track_image_id'] != 0)) ? wp_get_attachment_image_src($track['track_image_id'], $imageFormat, true)[0] : $track['poster'] ;
            $track_image_url_srcset = ( $track_artwork && isset($track['track_image_id']) &&  wp_get_attachment_image_srcset($track['track_image_id'], $imageFormat) ) ? ' srcset="' . esc_html( wp_get_attachment_image_srcset($track['track_image_id'], $imageFormat) ) . '" '  : '' ;
            $track_image_url_srcset .= ' sizes="(max-width: 480px) 300px"';
            $coverSpacer = ($custom_fields_columns && $track_artwork)? '<span class="sr_track_cover srp_spacer"></span>': '';

            $track_artwork_container = ( $this->getOptionValue('track_artwork_play_button', $instance) ) ? '<div class="sr_track_cover"><div class="srp_play"><i class="sricon-play"></i></div><img src=' . esc_url( $track_image_url ) . $track_image_url_srcset . ' /></div>' : '<img src=' . esc_url( $track_image_url ) . $track_image_url_srcset . ' class="sr_track_cover" />' ;
            $track_artwork_value = ($track_artwork && $track_image_url) ? $track_artwork_container : $coverSpacer ;
            $track_date = (isset($track['sourcePostID']) ) ? get_the_date( 'Y/m/d', $track['sourcePostID'] ) : false;
            $trackLinkedToPost = ( isset( $track['sourcePostID'] ) && $this->getOptionValue('post_link', $instance) && get_post_type() != 'product' ) ? get_permalink($track['sourcePostID']) : false;
            $trackTitle = esc_html($track['track_title']);
            $trackTitle .= ( Sonaar_Music::get_option('show_artist_name', 'srmp3_settings_general') )?  '<span class="srp_trackartist">' . esc_html($artistSeparator_string) . esc_html($track['track_artist']) .'</span>': '';
            $noteButton =  $this->addNoteButton($track['sourcePostID'], abs($trackCountFromPlaylist), $trackTitle, $trackdescEscapedValue, $excerptTrimmed, $track_desc_postcontent ); // We are using abs() here, because when the "reverse order" option is enable, the "$trackCountFromPlaylist" variable has a negative value 
            $playlistItemClass = (isset($trackdescEscapedValue) || $noteButton != null ) ? 'sr-playlist-item' : 'sr-playlist-item sr-playlist-item-flex';
            if($trackLinkedToPost && ! $this->getOptionValue('track_artwork_play_button', $instance) && ! $tracklistGrid ){
                $track_artwork_value = '<a href="' . $trackLinkedToPost . '" target="_self">' . $track_artwork_value;
                $track_artwork_value .= '</a>';
            }
            $format_playlist .= '<li 
            class="'. esc_attr($playlistItemClass) .'" 
            data-audiopath="' . esc_url( $trackUrl ) . '"
            data-showloading="' . esc_html($showLoading) .'"
            data-albumTitle="' . esc_attr( $track['album_title'] ) . '"
            data-albumArt="' . esc_url( $track['poster'] ) . '"
            data-releasedate="' . esc_attr( $track['release_date'] ) . '"
            data-date="' . esc_attr( $track_date ) . '"
            data-show-date="' . esc_attr($this->getOptionValue('show_track_publish_date', $instance)) . '"
            data-trackTitle="' . esc_html($trackTitle) . '"
            data-trackID="' . esc_html($track['id']) . '"
            data-trackTime="' . esc_html($track['lenght']) . '"
            data-relatedTrack="'. esc_html($relatedTrack) . '"
            data-post-url="'. esc_html($trackLinkedToPost) . '"
            data-post-id="'. esc_html($track['sourcePostID']) . '"
            data-track-pos='. $trackIndex . '
            data-track-lyric="'. esc_html((isset($track['has_lyric'])) ? $track['has_lyric'] : '') . '"
            >';

            $cf_input_formatted_ar=array();
            $cf_input_styling_ar=array();

            $postid = $track['sourcePostID'];

            /*
            ----------------------------------
            // INSERT CF INTO DOM FOR FILTERS 
            ----------------------------------
            */  
            if(function_exists( 'run_sonaar_music_pro' ) &&  get_site_option('SRMP3_ecommerce') == '1' ){
                $cf_data_formatted = '<!--START CF DATA--><div class="srp_cf_output" style="display:none;">';

                $cf_data_formatted .= $this->getTermsForFilters($postid, 'playlist-category');
                $cf_data_formatted .= $this->getTermsForFilters($postid, 'podcast-show');

                if (defined( 'WC_VERSION' )){
                    if(wc_get_product($postid)){

                        $cf_data_formatted .= $this->getTermsForFilters($postid, 'product_cat');
                        $cf_data_formatted .= $this->getTermsForFilters($postid, 'product_tag');

                        $product = wc_get_product($postid);
                        $attributes = $product->get_attributes();

                        if ( $attributes ) {
                            foreach ( $attributes as $attribute ) {
                                $display_result = '';
                                $name = $attribute->get_name();
                                if ( $attribute->is_taxonomy() ) {
                                    $terms = wp_get_post_terms( $product->get_id(), $name, 'all' );
                                    $cwtax = $terms[0]->taxonomy;
                                    $cw_object_taxonomy = get_taxonomy($cwtax);
                                    if ( isset ($cw_object_taxonomy->labels->singular_name) ) {
                                        $tax_label = $cw_object_taxonomy->labels->singular_name;
                                    } elseif ( isset( $cw_object_taxonomy->label ) ) {
                                        $tax_label = $cw_object_taxonomy->label;
                                        if ( 0 === strpos( $tax_label, 'Product ' ) ) {
                                            $tax_label = substr( $tax_label, 8 );
                                        }
                                    }
                                    $tax_terms = array();
                                    foreach ( $terms as $term ) {
                                        $single_term = esc_html( $term->name );
                                        array_push( $tax_terms, $single_term );
                                    }
                                    $display_result .= implode(', ', $tax_terms);
                                } else {
                                    // If custom attribute are used. but its useless for filtering.
                                    //$display_result .= esc_html( implode( ', ', $attribute->get_options() ) );
                                }
                            $cf_data_formatted .= '<div class="srp_cf_data sr-playlist-cf--'. esc_attr($name) .'">' . sanitize_text_field($display_result) . '</div>';  
                            }
                        }
                    }
                }
                if(function_exists('acf')){
                    if(is_array(get_fields($postid, true))){
                        foreach (get_fields($postid, true) as $key => $value) {
                            if(is_array($value) && (isset($value[0]) && !is_array($value[0]))){ // Prevent array values
                                $value = implode(', ', $value );
                            }
                            if(is_string($value) ){
                                $cf_data_formatted .= '<div class="srp_cf_data sr-playlist-cf--'. esc_attr($key) .'">' . sanitize_text_field($value) . '</div>';  
                            }
                        }
                    }
                }
                if ( function_exists('jet_engine') && jet_engine()->meta_boxes ) {
                    $metaboxes = jet_engine()->meta_boxes->get_registered_fields();
                    foreach ($metaboxes as $metabox) {
                        foreach($metabox as $themetabox){
                            if(isset($themetabox["object_type"])){ // make sure the object has a complete metabox structure
                                $metakey = isset($themetabox['name']) ? $themetabox['name'] : '' ;
                                $metakey_value = get_post_meta( $postid,  $metakey, true );
                                if(is_array($metakey_value)){
                                    $metakey_value = implode(', ', $metakey_value);
                                }
                                $cf_data_formatted .= '<div class="srp_cf_data sr-playlist-cf--'. esc_attr($metakey) .'">' . sanitize_text_field($metakey_value) . '</div>';  
                            }
                        }
                    }
                }
                $cf_data_formatted .= '</div><!--END CF DATA-->';
            }
            /*
            ----------------------------------
            // END OF CF INTO DOM FOR FILTERS 
            ----------------------------------
            */

            /*
            ----------------------------------
            // START OF CF DISPLAY INTO COLUMNS.
            ----------------------------------
            */
            if($custom_fields_columns != false && function_exists( 'run_sonaar_music_pro' ) &&  get_site_option('SRMP3_ecommerce') == '1' ){
                $cf_object = array();
                $cf_value =''; 

               // var_dump($cf_data_formatted);
                foreach ($custom_fields_columns_ar as $key => $value) {
                    $value = explode('::', $value);
                    if(!isset($value[1]) || $value[1] == ''){
                        break;
                    }else{
                        $valuekey = $value[1];
                    }
                    
                    $cf_object['name'] = $valuekey;

                    if( $valuekey != '' ){
                        if('pa_' == substr($valuekey, 0, 3)){
                            if (defined( 'WC_VERSION' )){
                                if(wc_get_product($postid)){
                                    $product = wc_get_product($postid);
                                    $cf_value = $product->get_attribute($valuekey);
                               // $productAttributes = get_post_meta( $postid, '_product_attributes', true );
                                //var_dump( $attributes);
                                }
                            }
                        }else{
                            $cf_value = get_post_meta( $postid, $valuekey, true );
                        }
                    

                    }else{
                        break;
                    }
                    if(function_exists('acf')){
                       //try to check if field_key present. underscore _postmeta shall contains the field key.
                        $cf_value_temp = get_post_meta( $postid, '_'.$valuekey, true );
                        if($cf_value_temp && !is_array($cf_value_temp) && 'field_' == substr($cf_value_temp, 0, 6)){
                            $cf_obj = get_field_object($cf_value_temp, $postid);
                            if($cf_obj){
                                $cf_value = $cf_obj['value'];
                            }
                        }
                    }
                    //$cf_value = get_post_meta( $postid, $value[1], true );
                    // timestamps example: $cf_value = "[sonaar_ts post_id='". $postid ."']" . $track['lenght'] . "[/sonaar_ts]";
                    if (!$cf_value){
                        switch ($valuekey) {
                            case 'srmp3_cf_album_img':
                                $cf_value = '<img src="'. esc_html($track['poster']) .'" class="sr_cf_track_cover">';
                                break;
                            case 'srmp3_cf_length':
                                $cf_value = ($track['lenght']) ? $track['lenght'] : '';
                                $cfValue_class = ' srp-hide-track-time';
                                break;
                            case 'srmp3_cf_album_title':
                                $cf_value = $track['album_title'];
                                break;
                            case 'srmp3_cf_audio_title':
                                $cf_value = $trackTitle;
                                break;
                            case 'srmp3_cf_artist':
                                $cf_value = $track['track_artist'];
                                break;
                            case 'srmp3_cf_description':
                                $cf_value = force_balance_tags( wp_trim_words( strip_shortcodes( $track['description'] ) , esc_attr($track_desc_lenght), $excerptTrimmed ));
                                break;
                            case 'post_title':
                                $cf_value = get_the_title( $postid );
                                break;
                            case 'post_id':
                                $cf_value = $postid;
                                break;
                            case 'post_date':
                                $cf_value = get_the_date('', $postid);
                                break;
                            case 'post_modified':
                                $cf_value = get_the_modified_date('', $postid);
                                break;
                            case 'playlist-category':
                                $cf_value = (get_the_terms($postid,'playlist-category')) ? get_the_terms($postid,'playlist-category'): '';
                                break;
                            case 'podcast-show':
                                $cf_value = (get_the_terms($postid,'podcast-show')) ? get_the_terms($postid,'podcast-show'):'';
                                break;
                            case 'product_cat':
                                $cf_value = (get_the_terms($postid,'product_cat')) ? get_the_terms($postid,'product_cat'): '';
                                break;
                            case 'product_tag':
                                $cf_value = (get_the_terms($postid,'product_tag')) ? get_the_terms($postid,'product_tag'): '';
                                break;
                            case 'post_tags':
                                // we dont currently support Playlist tags
                                $prod_terms='';
                                $tags=array();
                                $taxonomy = get_post_taxonomies( $postid );
                                foreach ($taxonomy as $key => $tax) {
                                    $taxonomy = ($tax == 'product_tag') ? $tax : $taxonomy; 
                                }
                                if ($taxonomy == 'product_tag'){
                                    $prod_terms = wp_get_post_terms($postid, $taxonomy );
                                    if ( count( $prod_terms ) > 0 ) {
                                        foreach ($prod_terms as $key => $prod_term) {
                                            $term_name = $prod_term->name;
                                            $tags[] = $term_name;
                                        }
                                        $tags = implode( ', ', $tags );
                                        $cf_value = $tags ;
                                    }
                                }

                                break;
                        } 
                    }
                   
                    
                    if ($cf_value == 'true' || $cf_value == 'false'){  
                        $cf_value = filter_var($cf_value, FILTER_VALIDATE_BOOLEAN);
                    }
                    if (is_bool($cf_value) === true) {
                        $cf_value = ($cf_value) ? esc_html__("Yes", 'sonaar-music') : esc_html__("No", 'sonaar-music');
                    }else if(is_array($cf_value)){
                        
                        $cf_value_ar =  array();
                        foreach ($cf_value as $keyx => $valuex[0]) {
                            
                            if (is_object($valuex[0])){
                                $cf_value_ar[]= $valuex[0]->name;
                                //array_push($cf_value_ar, $valuex[0]->name);
                            }else{
                                $cf_value_ar[]= $valuex[0];
                                //array_push($cf_value_ar, $valuex[0]);
                            }
                        }
                        $cf_value = join(', ', $cf_value_ar);
                    }
                    if ( is_wp_error( $cf_value ) ){
                        $cf_value = '';
                    }
                    $column_width = (isset($value[2]) && $value[2] !='' ) ? $value[2] : '100px';
                    $cf_input_styling = (isset( $cf_object['name'] )) ? '[data-id="' . esc_attr($widget_id) . '"] .sr-playlist-cf--' . $cf_object['name'] .'{
                                            flex: 0 0 ' . esc_attr($column_width) . '
                                        }':'';
                    $wpkses_value = array('img' => array('src' => array(), 'class'=>array()), 'strong' => array(), 'a' => array('href' => array(), 'title' => array(), 'target' => array()));
                   // $cf_data_formatted = (isset( $cf_object['name'] )) ? '<div class="sr-playlist-cf-child sr-playlist-cf--' . esc_attr($cf_object['name']) . '" data-id="sr-playlist-cf--' . esc_attr($cf_object['name']) . '" ' . $dataHide . '>' . wp_kses($cf_value, $wpkses_value) . '</div>' : '';
                   // var_dump($cf_data_formatted);
                    $cf_input_formatted =  (isset( $cf_object['name'] )) ? '<div class="sr-playlist-cf-child sr-playlist-cf--' . esc_attr($cf_object['name']) . '" data-id="sr-playlist-cf--' . esc_attr($cf_object['name']) . '">' . wp_kses($cf_value, $wpkses_value) . '</div>' : '';
                    $cf_input_styling_ar[] = $cf_input_styling;
                    $cf_input_formatted_ar[] = $cf_input_formatted;
                }
               
            }
            $show_cf_headings_class = ($show_cf_headings) ? '' : 'srmp3-heading--hide';
            $sr_cf_heading = ($custom_fields_columns != false ) ? '<div class="sr-cf-heading ' . $show_cf_headings_class . '"><style>' . join(' ', $cf_input_styling_ar) . '</style>' . join(' ', $sr_cf_heading_html_ar) . '</div>' : '';
            $custom_fields = ($custom_fields_columns != false && (isset($cf_input_formatted_ar[0]) && $cf_input_formatted_ar[0] != '')) ? '<div class="sr-playlist-cf-container">' . join(' ', $cf_input_formatted_ar) . '</div>' :'';
            $format_playlist .= ( isset($trackdescEscapedValue) || $noteButton != null ) ? '<div class="sr-playlist-item-flex">' : '';
           // $song_store_list = ($show_track_market && $playlist_has_ctas) ?  $song_store_list : '';
            //var_dump( $cf_data_formatted);
            $format_playlist .= $track_artwork_value . $custom_fields . $song_store_list . $cf_data_formatted ;
            $format_playlist .= ($noteButton != null)? $noteButton : '';
    
            $format_playlist .= (isset($trackdescEscapedValue)) ? $playlistTrackDesc : '';
            
            $format_playlist .= '</li>';

            if(!$relatedTrack){
                $trackNumber++; //Count visible track in the tracklist (All related tracks are hidden)
            }
            $trackIndexRelatedToItsPost++;//$trackIndexRelatedToItsPost is required to set the data-store-id. Data-store-id is used to popup the right content.
        }

        if( Sonaar_Music::get_option('waveformType', 'srmp3_settings_general') === 'wavesurfer' ) {
            $fakeWave = '';
        }else{
            $barHeight =(Sonaar_Music::get_option('sr_soundwave_height', 'srmp3_settings_general')) ? Sonaar_Music::get_option('sr_soundwave_height', 'srmp3_settings_general') : 70;
            $mediaElementStyle = (Sonaar_Music::get_option('waveformType', 'srmp3_settings_general') === 'mediaElement') ? 'style="height:'.esc_attr($barHeight).'px"' : '';
            $fakeWave = '
            <div class="sonaar_fake_wave" '.$mediaElementStyle.'>
                <audio src="" class="sonaar_media_element"></audio>
                <div class="sonaar_wave_base">
                    <canvas id=' . esc_attr($widget_id) . '-container' . ' class="" height="'.esc_attr($barHeight).'" width="2540"></canvas>
                    <svg></svg>
                </div>
                <div class="sonaar_wave_cut">
                    <canvas id=' . esc_attr($widget_id) . '-progress' . ' class="" height="'.esc_attr($barHeight).'" width="2540"></canvas>
                    <svg></svg>
                </div>
            </div>';
        }
        $feedurl = ($feed) ? '1' : '0';

        $hide_times_current = (!$hide_times) ? '
            <div class="currentTime"></div>
        ' : '' ;
        $hide_times_total = (!$hide_times) ? '
            <div class="totalTime"></div>
        ' : '' ;

        $wave_margin = ($hide_times) ? 'style="margin-left:0px;margin-right:0px;"': ''; // remove margin needed for the current/total time

        $progressbar = '';
        $player_style = ($hide_progressbar && $playerWidgetTemplate == 'skin_float_tracklist') ? 'style="height:33px;"': '';
        if (!$hide_progressbar){
            $progressbar = '
                ' . $hide_times_current . ' 
                <div id="'.esc_attr($widget_id). '-' . bin2hex(random_bytes(5)) . '-wave" class="wave" ' . esc_attr($wave_margin) . '>
                ' . $fakeWave . ' 
                </div>
                ' . $hide_times_total . ' 
            ';
         }else{
             // hide the progress bar
             $progressbar = '
                <div id="'.esc_attr($widget_id). '-' . bin2hex(random_bytes(5)) . '-wave" class="wave">
                ' . $fakeWave . '
                </div>
                
            ';
         }
        
         if(
            $playerWidgetTemplate == 'skin_float_tracklist' &&
            !$this->getOptionValue('show_shuffle_bt', $instance) &&
            !$this->getOptionValue('show_speed_bt', $instance) &&
            !$this->getOptionValue('show_volume_bt', $instance)
         ){ 
             $main_control_xtraClass = ' srp_oneColumn';
        }else{
            $main_control_xtraClass = '';
        }

        $widgetPart_control = ($playerWidgetTemplate == 'skin_float_tracklist' || ! $show_playlist )?'<div class="srp_main_control'. $main_control_xtraClass .'">':'';
        $widgetPart_control .= '<div class="control">';
        if ( $this->getOptionValue('show_skip_bt', $instance) ){
            $widgetPart_control .=
            '<div class="sr_skipBackward sricon-15s"></div>';
        }
        $prev_play_next_Controls = '';
        if(count($playlist['tracks']) > 1 ){
            $prev_play_next_Controls .= 
            '<div class="previous sricon-back" style="opacity:0;"></div>';
        }
            $prev_play_next_Controls .=
            '<div class="play" style="opacity:0;">
                <i class="sricon-play"></i>
            </div>';
        if(count($playlist['tracks']) > 1 ){
            $prev_play_next_Controls .=
            '<div class="next sricon-forward" style="opacity:0;"></div>';
        };
        $widgetPart_control .= $prev_play_next_Controls;
       
        if ( $this->getOptionValue('show_skip_bt', $instance) ){
                $widgetPart_control .= 
                '<div class="sr_skipForward sricon-30s"></div>';
            }
            $widgetPart_control .= ( $playerWidgetTemplate == 'skin_float_tracklist' )?'</div><div class="control">':'';
            if ( $this->getOptionValue('show_shuffle_bt', $instance) ){
                $widgetPart_control .= '<div class="sr_shuffle sricon-shuffle"></div>';
            }
        if ( $this->getOptionValue('show_speed_bt', $instance) ){
                $widgetPart_control .= '<div class="sr_speedRate"><div>1X</div></div>';
        }
        if ( $this->getOptionValue('show_volume_bt', $instance) ){
                $widgetPart_control .= '<div class="volume">
                <div class="sricon-volume">
                    <div class="slider-container">
                    <div class="slide"></div>
                </div>
                </div>
                </div>';
            }
        
        $widgetPart_control .= ($playerWidgetTemplate == 'skin_boxed_tracklist' &&  ! $show_playlist )? '<div class="srp_track_cta"></div>': '';
        $widgetPart_control .= '</div>'; //End DIV .control
        $widgetPart_control .= ($playerWidgetTemplate == 'skin_boxed_tracklist' && ! $show_playlist )? $this->addNoteButton( $albums, '0', $trackTitle) :'';
        $widgetPart_control .= ($playerWidgetTemplate == 'skin_float_tracklist' ||  ! $show_playlist )?'</div>':''; //End DIV .srp_main_control
        
        $class_player ='player ';
        $class_player .=($progressbar_inline) ? 'sr_player__inline ' : '';
        $controlArtwork = ($displayControlArtwork) ? $prev_play_next_Controls : '';
        $displayControlUnder = ($hide_control_under || $playerWidgetTemplate == 'skin_boxed_tracklist') ? '' : $widgetPart_control;
        $noLoopTracklist = ($noLoopTracklist == false) ? get_post_meta($albums, 'no_loop_tracklist', true) : $noLoopTracklist;
        $notrackskip = ($notrackskip == false) ? get_post_meta($albums, 'no_track_skip', true) : $notrackskip;
        $showControlOnHoverClass = ($showControlOnHover)? 'srp_show_ctr_hover' : '';
        $widgetPart_artwork = (!$hide_artwork || $hide_artwork!="true" ?
                '<div class="sonaar-Artwort-box ' . $showControlOnHoverClass . '">
                <div class="control">
                    ' . $controlArtwork . '
                </div>
                    <div class="album">
                        <div class="album-art">
                            <img alt="album-art">
                        </div>
                    </div>
                </div>'
            : '');
        
        $widgetPart_title =  '<'.esc_attr($title_html_tag_playlist).' class="sr_it-playlist-title">'. esc_attr($playlist_title) .'</'.esc_attr($title_html_tag_playlist).'>';

        
        $widgetPart_subtitle =  '<div class="srp_subtitle">'. ( ( get_post_meta( $firstAlbum, 'alb_release_date', true ) )? esc_html(get_post_meta($firstAlbum, 'alb_release_date', true )) : '' ) . '</div>'; //'alb_release_date' field is now used for the subtitle

        $wpkses_arr = array( 'br' => array(), 'p' => array(), 'strong' => array(), 'a' => array('href' => array(), 'title' => array()));
        $widgetPart_cat_description =  ( $this->getOptionValue('show_cat_description', $instance) && $terms) ? '<div class="srp_podcast_rss_description">' . wp_kses(category_description((int)$terms[0]),$wpkses_arr) . '</div>' : '';

        $widgetPart_meta = '<div class="srp_player_meta">';
        $widgetPart_meta .= ($showPublishDate)?'<div class="sr_it-playlist-publish-date">'. esc_html(get_the_date( $dateFormat, $albums )) .'</div>':'';
        $widgetPart_meta .= ($this->getOptionValue('show_tracks_count', $instance)  && $trackNumber > 1 )?'<div class="srp_trackCount">'. esc_attr($trackNumber) . ' ' . esc_html(Sonaar_Music::get_option('player_show_tracks_count_label', 'srmp3_settings_widget_player')) .'</div>':'';
        $widgetPart_meta .= ($this->getOptionValue('show_meta_duration', $instance))?'<div class="srp_playlist_duration" data-hours-label="'. esc_html(Sonaar_Music::get_option('player_hours_label', 'srmp3_settings_widget_player')) .'" data-minutes-label="'. esc_html(Sonaar_Music::get_option('player_minutes_label', 'srmp3_settings_widget_player')) .'"></div>':'';
        $widgetPart_meta .= '</div>';

        $widgetPart_tracklist = ($playerWidgetTemplate == 'skin_boxed_tracklist' && $trackNumber <= 1 && isset($instance['one_track_boxed_hide_tracklist']) && $instance['one_track_boxed_hide_tracklist'] == "true") ? '<div class="playlist" id="playlist_'. $widget_id .'" style="display:none;">' : '<div class="playlist" id="playlist_'. $widget_id .'">';
        $widgetPart_tracklist .= (!$hide_album_title && $playerWidgetTemplate == 'skin_float_tracklist') ? $widgetPart_title : '' ;
        $widgetPart_tracklist .= ($hide_album_subtitle || $playerWidgetTemplate == 'skin_boxed_tracklist') ? '' : $widgetPart_subtitle;
        $widgetPart_tracklist .= ( ($showPublishDate || $this->getOptionValue('show_meta_duration', $instance) || $this->getOptionValue('show_tracks_count', $instance)) && $playerWidgetTemplate == 'skin_float_tracklist') ? $widgetPart_meta : '';
        $widgetPart_tracklist .= ( $playerWidgetTemplate == 'skin_float_tracklist' ) ? $widgetPart_cat_description : '';
        
        if(function_exists( 'run_sonaar_music_pro' ) &&  get_site_option('SRMP3_ecommerce') == '1'){
            $labelSearchPlaceHolder = (isset( $instance['searchbar_placeholder'] ) && $instance['searchbar_placeholder'] != '' ) ? $instance['searchbar_placeholder'] : $labelSearchPlaceHolder;
            $searchbar_show_keyword_displayClass = ($this->getOptionValue('searchbar', $instance) !== false ) ? 'display:flex;' : 'display:none;';
            $searchbar_show_keyword = '<div class="srp_search_container" style="' . $searchbar_show_keyword_displayClass . '" data-metakey="search" data-label="' . esc_html($labelSearch) .'"><i class="fas fa-search"></i><input class="srp_search" enterkeyhint="done" placeholder="' .  esc_html($labelSearchPlaceHolder) . '" \><i class="srp_reset_search sricon-close-circle" style="display:none;"></i></div>';
            $searchbar_container =  ( $this->getOptionValue('searchbar', $instance) ) ? '<div class="srp_search_main">' . $searchbar_show_keyword . '</div>' : '';
            
            $pagination = ($tracks_per_page) ? '<div class="srp_pagination_container"><div class="srp_pagination_arrows srp_pagination--prev sricon-back"></div><ul class="srp_pagination"></ul><div class="srp_pagination_arrows srp_pagination--next sricon-forward"></div></div>' : '' ;
            $widgetPart_tracklist .= $searchbar_container . $sr_cf_heading . '<div class="srp_tracklist"><div class="srp_notfound"><div class="srp_notfound--title">'. esc_html($labelNoResult1) .'</div><div class="srp_notfound--subtitle">'. esc_html($labelNoResult2) .'</div></div><ul class="srp_list">' . $format_playlist . '</ul>' . $pagination . '</div></div>';
        }else{
            $widgetPart_tracklist .= '<div class="srp_tracklist"><ul class="srp_list">' . $format_playlist . '</ul></div></div>';
        }
        $widgetPart_albumStore = '<div class="album-store">' . $this->get_market( $store_title_text, $album_ids_with_show_market, $feedurl, $el_widget_id, $terms) . '</div>';
        
        if($displayControlArtwork){
            $widgetPart_playButton = '';
        }else{
            $extraClass = ( isset( $instance['button_animation'] ) )?' srp-elementor-animation elementor-animation-' . $instance['button_animation'] :'';
            $extraClassForlabelOnly = ( $this->getOptionValue( 'use_play_label_with_icon', $instance, false ) )?' sricon-play':''; 

            $extraStyle = ''; 
            $extraStyle .= ( isset( $instance['play_bt_bg_color'] ) )?' background:' . $instance['play_bt_bg_color'] . ';':''; 
            $extraStyle .= ( isset( $instance['play_bt_text_color'] ) )?' color:' . $instance['play_bt_text_color'] . ';':''; 
          
    
            $widgetPart_playButton = ( $usePlayLabel ) ? '
            <div class="srp-play-button play srp-play-button-label-container' . $extraClass . $extraClassForlabelOnly . '" href="#" style="' . esc_attr( $extraStyle ) . '">
                <div class="srp-play-button-label">' . esc_html($labelPlayTxt) .'</div>
                <div class="srp-pause-button-label">' . esc_html($labelPauseTxt) .'</div>
            </div>'
            :'
            <div class="srp-play-button play' . $extraClass . '" href="#">
                <i class="sricon-play"></i>
                <div class="srp-play-circle"></div>
            </div>';
        }
        $extraClass = ( function_exists( 'run_sonaar_music_pro' ) && $progressbar_inline )? ' srp_progressbar_inline':'';
        $ironAudioClass .= ($playlist_has_ctas) ? '' : ' playlist_has_no_ctas';
        $ironAudioClass .= ( isset($cfValue_class) ) ? $cfValue_class : '' ;
        $ironAudioClass .= ( $tracklistGrid ) ? ' srp_tracklist_grid' : '' ;
        $ironAudioClass .= ( $this->getOptionValue('track_artwork_play_button', $instance) ) ? ' srp_tracklist_play_cover' : '' ;
        $ironAudioClass .= ( $this->getOptionValue('track_artwork_play_on_hover', $instance) ) ? ' srp_tracklist_play_cover_hover' : '' ;
        $widgetPart_main = '<div class="album-player">';
        $widgetPart_main .= ( ( !$show_playlist && $playerWidgetTemplate == 'skin_boxed_tracklist' ) || ($show_playlist && $playerWidgetTemplate == 'skin_float_tracklist') || $hide_player_title || (!$show_playlist && $hide_album_title && $playerWidgetTemplate == 'skin_float_tracklist'))?'':'<'. esc_attr($title_html_tag_soundwave) .' class="album-title"></'. esc_attr($title_html_tag_soundwave) .'>'; 
        $widgetPart_main .= ( !$hide_track_title && $playerWidgetTemplate == 'skin_float_tracklist' || (!$hide_player_title && $playerWidgetTemplate == 'skin_boxed_tracklist' && !$show_playlist ))? '<'. esc_attr($track_title_html_tag_soundwave).' class="track-title"></'. esc_attr($track_title_html_tag_soundwave).'>' : '';
        $widgetPart_main .= ( !$hide_album_subtitle && $playerWidgetTemplate == 'skin_boxed_tracklist') ? $widgetPart_subtitle : '';
        $widgetPart_main .= ( $playerWidgetTemplate == 'skin_boxed_tracklist' )? $widgetPart_meta . '<div class="srp_control_box">'. $widgetPart_playButton .'<div class="srp_wave_box' . $extraClass . '">' : '';
        $widgetPart_main .= ' <div class="' . esc_attr($class_player) . '" ' . esc_attr($player_style) . '><div class="sr_progressbar">' . $progressbar . ' </div>' . $displayControlUnder . '</div>';
        if($playerWidgetTemplate == 'skin_boxed_tracklist'){
            $widgetPart_main .= ( ($usePlayLabel || $this->getOptionValue('play_btn_align_wave', $instance) ) && !$progressbar_inline)?  '</div></div>'. $widgetPart_control :   $widgetPart_control . '</div></div>';
        }
        $albums = str_replace(' ', '', $albums);
        $widgetData = ($artwork)?'data-albumart="' . $artwork. '"' : '';
        $output = '<div class="iron-audioplayer ' . esc_attr($ironAudioClass) . '" id="'. esc_attr( $widget_id ) .'-' . bin2hex(random_bytes(5)) . '" data-id="' . esc_attr($widget_id) .'" data-albums="'. esc_attr( $albums) .'"data-url-playlist="' . esc_url(home_url('?load=playlist.json&amp;title='.$title.'&amp;albums='.$albums.'&amp;feed_title='.$feed_title.'&amp;feed='.$feed.'&amp;feed_img='.$feed_img.'&amp;el_widget_id='.$el_widget_id.'&amp;artwork='.$artwork .'&amp;posts_per_pages='.$posts_per_pages .'&amp;all_category='.$all_category .'&amp;single_playlist='.$single_playlist .'&amp;reverse_tracklist='. $this->getOptionValue('reverse_tracklist', $instance) .'&amp;audio_meta_field='.$audio_meta_field .'&amp;repeater_meta_field='.$repeater_meta_field )) . '" data-sticky-player="'. esc_attr($sticky_player) . '" data-shuffle="'. esc_attr($shuffle) . '" data-playlist_title="'. esc_html($playlist_title) . '" data-scrollbar="'. esc_attr($scrollbar) . '" data-wave-color="'. esc_attr($wave_color) .'" data-wave-progress-color="'. esc_attr($wave_progress_color) . '" data-no-wave="'. esc_attr($hide_timeline) . '" data-hide-progressbar="'. esc_attr($hide_progressbar) . '" data-feedurl="'. esc_attr($feedurl) .'" data-notrackskip="'. esc_attr($notrackskip) .'" data-no-loop-tracklist="'. esc_attr($noLoopTracklist) .'" data-playertemplate ="'. esc_attr($playerWidgetTemplate) .'" data-hide-artwork ="'. esc_attr($hide_artwork) .'" data-speedrate="1" '. $widgetData .'" data-tracks-per-page="'. esc_attr($tracks_per_page) .'"' . $data_column_params . ' style="opacity:0;">';
        if($playerWidgetTemplate == 'skin_boxed_tracklist'){ // Boxed skin
            $output .= ($widgetPart_cat_description == '')?'<div class="srp_player_boxed srp_player_grid">':'<div class="srp_player_boxed"><div class="srp_player_grid">';
            $output .= $widgetPart_artwork . $widgetPart_main;// . $widgetPart_albumStore .'</div></div>';
            $output .= ( isset ($albumStorePosition) && $albumStorePosition == 'top') ? $widgetPart_albumStore : '';
            $output .= '</div></div>';
            $output .= ($widgetPart_cat_description == '')?'': $widgetPart_cat_description  . '</div>';
            $output .= $widgetPart_tracklist;
            $output .= ( isset ($albumStorePosition) && $albumStorePosition !== 'top') ? $widgetPart_albumStore : '';
        }else{ // Floated skin
            $inlineSyle = ($widgetPart_artwork == '' &&  !$show_playlist)? 'style="display:none;"':''; //hide sonaar-grid and its background if it is empty
            $output .= '<div class="sonaar-grid" '. esc_html($inlineSyle) . '>'. $widgetPart_artwork . $widgetPart_tracklist . '</div>' . $widgetPart_main . '</div>' . $widgetPart_albumStore;
        }
        $output .= '</div>';
        $output .= '<script>if(typeof setIronAudioplayers !== "undefined"){ setIronAudioplayers("'.$widget_id.'"); }</script>'; 
        if ( function_exists( 'wc_print_notices' ) && WC()->session ) {
			wc_print_notices(); // Print Woocommerce message. Eq: Feedback after Add to Cart
		}
        
        echo $output;
        echo $args['after_widget'];
    }

    private function getTermsForFilters($postid, $termname){
       
        $termObj = get_the_terms($postid, $termname);
           
        
        if(!$termObj || is_wp_error($termObj) ) return;
        
        $term_ar =  array();
        foreach ($termObj as $term) {
            $term_cat_ar[]= $term->name;
        }
        $term_cat_formatted = join(', ', $term_cat_ar);
        return '<div class="srp_cf_data sr-playlist-cf--' .  $termname . '">' . $term_cat_formatted . '</div>';
    }

    /* Return the notebutton HTML or NULL */
    private function addNoteButton($postID, $trackPosition, $trackTitle, $trackdescEscapedValue = null, $excerptTrimmed = null, $track_desc_postcontent = null){
        /*parameters:
        -$postID: playlist post ID
        -$trackPosition: track position in the playlist post, not in the track list.
        -$trackTitle: The track title: Required to display it in the Note content
        -$trackdescEscapedValue: (OPTIONAL) The Excerpt content. We have to check if the "note" is cuted by the "$excerptTrimmed"("[...]").
        -$excerptTrimmed: (OPTIONAL) [...]
        */
        $returnValue = null;
        if( function_exists( 'run_sonaar_music_pro' ) ){
            if($track_desc_postcontent){
                $post = get_post($postID); 
                $trackFields = $post->post_content;
            }else{
                $postObject = get_post_meta($postID, 'alb_tracklist', true );
                $trackFields = (isset($postObject[$trackPosition]['track_description']))?$postObject[$trackPosition]['track_description'] : '';

            }
            if( isset($trackFields) && $trackFields != ''){
                if ( ($trackdescEscapedValue && substr(strip_tags($trackdescEscapedValue), -1 * (strlen($excerptTrimmed))) == $excerptTrimmed) || $trackdescEscapedValue == null ){ // Check if the Excerpt display the whole description or if it is cuted/ended by the $excerptTrimmed[...].
                    $returnValue = '<div class="srp_noteButton"><i class="sricon-info"  data-source-post-id="' . esc_attr( $postID ) . '" data-track-position="' . esc_attr( $trackPosition ) . '" data-track-title="' . esc_attr( $trackTitle ) . '" data-track-use-postcontent="' . esc_attr( $track_desc_postcontent ) . '"></i></div>';
                }
            }
        }
        return $returnValue;
    }
    
    /*E.g. Return the value from "show_skip_bt" (shortcode) or "player_show_skip_bt" (plugin settings) */
    private function getOptionValue($optionID, $instance, $proRequired = true, $defaultValue = false){
        /*parameters:
        -$optionID: the option id from the plugins settings has to have the prefix "player_" add to the shortcode id (E.g. "player_show_skip_bt" for "show_skip_bt" )
        -$instance: The $instance variable
        -$proRequired: (OPTIONAL) We have to set this false if the option is available with the free plugin
        -$defaultValue: (OPTIONAL) If the setting is not saved return this value.
        */
        if($proRequired && !function_exists( 'run_sonaar_music_pro' ) ){ 
            return false;
        }
        if( isset($instance[$optionID]) && $instance[$optionID] != 'default') {
            return filter_var($instance[$optionID], FILTER_VALIDATE_BOOLEAN); //get value from the shortcode
        }else if(Sonaar_Music::get_option('player_' . $optionID, 'srmp3_settings_widget_player') != null){
            return filter_var(Sonaar_Music::get_option('player_' . $optionID, 'srmp3_settings_widget_player'), FILTER_VALIDATE_BOOLEAN); //get value from the plugin settings
        }else{
            return $defaultValue;
        }
    }
    
    private function wordpress_get_full_path_of_url( $url ) {
        // Make "get_home_path()" function callable on frontend
        if( ! is_admin() ) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        // Get the document root path
        $root_path = get_home_path();

        // Get the path from URL
        $src = parse_url( $url );	
        $url_path = $src['path'];

        // Get only WordPress subdirectory if exist
        $subdirectory = site_url( '', 'relative' );

        $url_part_1 = str_replace( $subdirectory, '', $root_path );
        $url_part_2 = $url_path;

        // Return the full path
        return untrailingslashit( $url_part_1 ) . $url_part_2; 
    }

    private function wordpress_audio_meta( $audio_url ) {
        $meta = '';
        
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        
        if( function_exists( 'wp_read_audio_metadata' ) ) {
            $file_path = $this->wordpress_get_full_path_of_url( $audio_url );
            $meta = wp_read_audio_metadata( $file_path );
        }
        
        return $meta;
    }
    
    private function wc_add_to_cart($id = null){
       
        if ( $id == null || ( !defined( 'WC_VERSION' ) && get_site_option('SRMP3_ecommerce') != '1' ) ){
            return false;
        }

        return get_post_meta($id, 'wc_add_to_cart', true);
    }
    private function wc_buynow_bt($id = null){
        if ($id == null || ( !defined( 'WC_VERSION' ) && get_site_option('SRMP3_ecommerce') != '1' )){
            return false;
        }

        return get_post_meta($id, 'wc_buynow_bt', true);
    }
    private function get_market($store_title_text, $album_id = 0, $feedurl = 0, $el_widget_id = null, $terms = null){
        
        if( $album_id == 0 && !$feedurl)
        return;

        if (!$feedurl){ // source if from albumid
            $firstAlbum = explode(',', $album_id);
            $firstAlbum = $firstAlbum[0];
            $storeList = get_post_meta($firstAlbum, 'alb_store_list', true);

            $wc_add_to_cart =  $this->wc_add_to_cart($firstAlbum);
            $wc_buynow_bt =  $this->wc_buynow_bt($firstAlbum);
            $is_variable_product = ($wc_add_to_cart == 'true' || $wc_buynow_bt == 'true' ) ? $this->is_variable_product($firstAlbum) : '';
            
            //check to add woocommerce icons for external links
            $album_store_list = ($wc_add_to_cart == 'true' || $wc_buynow_bt == 'true') ? $this->push_woocart_in_storelist(get_post($firstAlbum), $is_variable_product, $wc_add_to_cart, $wc_buynow_bt) : false;
          
            if ( is_singular( SR_PLAYLIST_CPT ) && Sonaar_Music::get_option('player_type', 'srmp3_settings_general') == 'podcast' ) {
                if ($terms == null) {
                    //no terms variable is passed manually. So check if post has terms 
                    $terms = get_the_terms(  get_the_ID(), 'podcast-show' ); 
                    $terms = ($terms == false) ? null : $terms[0]->term_id;
                }
            }

            //check to add category icons for external links
            $album_cat_store_list = ($terms) ? $this->push_caticons_in_storelist( get_post($firstAlbum), $terms ) : null;
           
            // merge arrays temporary
            $album_store_list = (isset($album_store_list) && is_array($album_store_list) && count($album_store_list) > 0 && is_array($album_cat_store_list)) ? array_merge($album_store_list,  $album_cat_store_list ) : $album_cat_store_list;
        
        } else if($feedurl = 1) {
             // source if from elementor widget
            if (!$el_widget_id)
            return;

            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                //__A. WE ARE IN EDITOR SO USE CURRENT POST META SOURCE TO UPDATE THE WIDGET LIVE OTHERWISE IT WONT UPDATE WITH LIVE DATA
                $storeList =  get_post_meta( $album_id, 'alb_store_list', true);
                if($storeList == ''){
                    return;
                }   
            }else{
                //__B. WE ARE IN FRONT-END SO USE SAVED POST META SOURCE
                $elementorData = get_post_meta( $album_id, '_elementor_data', true);
                $elementorData = json_decode($elementorData, true);
                $id = $el_widget_id;
                $results=[];

                if($elementorData){
                   $this->findData( $elementorData, $id, $results );
                   $storeList = (!empty($results['settings']['storelist_repeater'])) ? $results['settings']['storelist_repeater'] : '';
                }else{
                    return;
                } 
            }
        }
        if(isset($album_store_list) && is_array($album_store_list) && count($album_store_list) > 0){

            $storeList = (is_array($storeList)) ? array_merge($storeList,$album_store_list ): $album_store_list;
        }
            if ( is_array($storeList) && $storeList ){
                $output = '
                <div class="buttons-block">
                    <div class="ctnButton-block">
                        <div class="available-now">';
                            $output .= ( $store_title_text == NULL ) ? esc_html__("Available now on:", 'sonaar-music') : esc_html__($store_title_text);
                            $output .=  '
                        </div>
                        <ul class="store-list">';
                        if ($feedurl){
                            foreach ($storeList as $store ) {
                                if(!isset($store['store_name'])){
                                    $store['store_name']="";
                                }
                                if(!isset($store['store_link'])){
                                    $store['store_link']="";
                                }

                                if(array_key_exists ( 'store_icon' , $store )){
                                    $icon = ( $store['store_icon']['value'] )? '<i class="' . esc_html($store['store_icon']['value']) . '"></i>': '';
                                }else{
                                    $icon ='';
                                }
                                $output .= '<li><a class="button" href="' . esc_url( $store['store_link'] ) . '" target="_blank">'. $icon . $store['store_name'] . '</a></li>';
                            }
                        }else{
                            foreach ($storeList as $key => $store ) {
                                if(!isset($store['store-name'])){
                                    $store['store-name']="";
                                }
                                if(!isset($store['store-link'])){
                                    $store['store-link']="";
                                }
                                if(!isset($store['store-target'])){
                                    $store['store-target']='_blank';
                                }

                                if(array_key_exists ( 'store-icon' , $store )){
                                    $icon = ( $store['store-icon'] )? '<i class="' . esc_html($store['store-icon']) . '"></i>': '';
                                }else{
                                    $icon ='';
                                }
                                $classes = 'button';

                                $href = 'href="' . esc_url($store['store-link']) . '"';
                                if(isset($store['link-option']) && $store['link-option'] == 'popup'){ 
                                    $classes .= ' sr-store-popup';
                                    $store['store-target'] = '_self';
                                    $href = '';
                                }
                                $output .= '<li><a class="'. esc_attr($classes) .'" data-source-post-id="' . esc_attr($firstAlbum) .'" data-store-id="a-'. esc_attr($key) .'" '. $href .' target="' . $store['store-target'] . '">'. $icon . $store['store-name'] . '</a></li>';
                            }
                        }

                        $output .= '
                        </ul>
                    </div>
                </div>';
                
                return $output;
            }        
    }

    /**
    * Back-end widget form.
    */
    
    public function form ( $instance ){
        $instance = wp_parse_args( (array) $instance, self::$widget_defaults );
            
            $title = esc_attr( $instance['title'] );
            $albums = $instance['albums'];
            $show_playlist = (bool)$instance['show_playlist'];
            $sticky_player = (bool)$instance['sticky_player'];
            $hide_artwork = (bool)$instance['hide_artwork'];
            $show_album_market = (bool)$instance['show_album_market'];
            $show_track_market = (bool)$instance['show_track_market'];
            //$remove_player = (bool)$instance['remove_player']; // deprecated and replaced by hide_timeline
            $hide_timeline = (bool)$instance['hide_timeline'];
            
            $all_albums = get_posts(array(
            'post_type' => SR_PLAYLIST_CPT
            , 'posts_per_page' => -1
            , 'no_found_rows'  => true
            ));
            
            if ( !empty( $all_albums ) ) :?>

  <p>
    <label for="<?php echo esc_html($this->get_field_id('title')); ?>">
      <?php _ex('Title:', 'Widget', 'sonaar-music'); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo esc_html($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" placeholder="<?php _e('Popular Songs', 'sonaar-music'); ?>" />
  </p>
  <p>
    <label for="<?php echo esc_html($this->get_field_id('albums')); ?>">
      <?php esc_html_e('Album:', 'Widget', 'sonaar-music'); ?>
    </label>
    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('albums')); ?>" name="<?php echo esc_attr($this->get_field_name('albums')); ?>[]" multiple="multiple">
      <?php foreach($all_albums as $a): ?>

        <option value="<?php echo esc_attr($a->ID); ?>" <?php echo ( is_array($albums) && in_array($a->ID, $albums) ? ' selected="selected"' : ''); ?>>
          <?php echo esc_attr($a->post_title); ?>
        </option>

        <?php endforeach; ?>
    </select>
  </p>
<?php if ( function_exists( 'run_sonaar_music_pro' ) ): ?>
  <p>
    <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('sticky_player')); ?>" name="<?php echo esc_attr($this->get_field_name('sticky_player')); ?>" <?php checked( esc_attr($sticky_player) ); ?> />
    <label for="<?php echo esc_attr($this->get_field_id('sticky_player')); ?>">
      <?php esc_html_e( 'Enable Sticky Audio Player', 'sonaar-music'); ?>
    </label>
    <br />
  </p>
<?php endif ?>
  <p>
    <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_playlist')); ?>" name="<?php echo esc_attr($this->get_field_name('show_playlist')); ?>" <?php checked( esc_attr($show_playlist) ); ?> />
    <label for="<?php echo esc_attr($this->get_field_id('show_playlist')); ?>">
      <?php esc_html_e( 'Show Playlist', 'sonaar-music'); ?>
    </label>
    <br />
  </p>

  <p>
    <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_album_market')); ?>" name="<?php echo esc_attr($this->get_field_name('show_album_market')); ?>" <?php checked( esc_attr($show_album_market) ); ?> />
    <label for="<?php echo esc_attr($this->get_field_id('show_album_market')); ?>">
      <?php esc_html_e( 'Show Album store', 'sonaar-music'); ?>
    </label>
    <br />
  </p>
  <p>
    <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_artwork')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_artwork')); ?>" <?php checked( esc_attr($hide_artwork )); ?> />
    <label for="<?php echo esc_attr($this->get_field_id('hide_artwork')); ?>">
      <?php esc_html_e( 'Hide Album Cover', 'sonaar-music'); ?>
    </label>
    <br />
  </p>
  <p>
    <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_track_market')); ?>" name="<?php echo esc_attr($this->get_field_name('show_track_market')); ?>" <?php checked( esc_attr($show_track_market )); ?> />
    <label for="<?php echo esc_attr($this->get_field_id('show_track_market')); ?>">
      <?php esc_html_e( 'Show Track store', 'sonaar-music'); ?>
    </label>
    <br />
  </p>
  </p>
  <p>
    <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_timeline')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_timeline')); ?>" <?php checked( esc_attr($hide_timeline )); ?> />
    <label for="<?php echo esc_attr($this->get_field_id('hide_timeline')); ?>">
      <?php esc_html_e( 'Remove Visual Timeline', 'sonaar-music'); ?>
    </label>
    <br />
  </p>

  <?php
            else:
                
            echo wp_kses_post( '<p>'. sprintf( _x('No albums have been created yet. <a href="%s">Create some</a>.', 'Widget', 'sonaar-music'), esc_url(admin_url('edit.php?post_type=' . SR_PLAYLIST_CPT)) ) .'</p>' );
            
            endif;
    }
    
    
    
    
    
    
    /**
    * Sanitize widget form values as they are saved.
    */
    
    public function update ( $new_instance, $old_instance )
    {
        $instance = wp_parse_args( $old_instance, self::$widget_defaults );
            
            $instance['title'] = strip_tags( stripslashes($new_instance['title']) );
            $instance['albums'] = $new_instance['albums'];
            $instance['show_playlist']  = (bool)$new_instance['show_playlist'];
            $instance['hide_artwork']  = (bool)$new_instance['hide_artwork'];
            $instance['sticky_player']  = (bool)$new_instance['sticky_player'];
            $instance['show_album_market']  = (bool)$new_instance['show_album_market'];
            $instance['show_track_market']  = (bool)$new_instance['show_track_market'];
            //$instance['remove_player']  = (bool)$new_instance['remove_player']; deprecated and replaced by hide_timeline
            $instance['hide_timeline']  = (bool)$new_instance['hide_timeline'];
            
            return $instance;
    }
    
    
    private function print_playlist_json() {
        $jsonData = array();

        if ( ! empty($_GET["albums"]) ){
            $re = '/^\d+(?:,\d+)*$/';
            if ( preg_match($re, $_GET["albums"]) )
                $albums = sanitize_text_field($_GET["albums"]);
            else
                $albums = array();
        }else{
            $albums = array();
        }
       
        if(!empty($_GET["el_widget_id"]) && ctype_alnum($_GET["el_widget_id"])){
            $el_widget_id = sanitize_text_field($_GET["el_widget_id"]);
        }else{
            $el_widget_id = null;
        }

        $single_playlist = !empty($_GET["single_playlist"]) ? rest_sanitize_boolean($_GET["single_playlist"]) : false;
        $title = !empty($_GET["title"]) ? sanitize_text_field($_GET["title"]) : null;
        $feed_title = !empty($_GET["feed_title"]) ? sanitize_text_field($_GET["feed_title"]) : null;
        $feed = !empty($_GET["feed"]) ? sanitize_text_field($_GET["feed"]) : null; 
        $feed_img = !empty($_GET["feed_img"]) ? sanitize_url($_GET["feed_img"]) : null;
        $artwork =  !empty($_GET["artwork"]) ? sanitize_url($_GET["artwork"]) : null;
        $posts_per_pages = !empty($_GET["posts_per_pages"]) ? intval($_GET["posts_per_pages"]) : null;
        $all_category = !empty($_GET["all_category"]) ? true : null;
        $reverse_tracklist = !empty($_GET["reverse_tracklist"]) ? true : false;
        $audio_meta_field = !empty($_GET["audio_meta_field"]) ? $_GET["audio_meta_field"] : null;
        $repeater_meta_field = !empty($_GET["repeater_meta_field"]) ? $_GET["repeater_meta_field"] : null;
        $track_desc_postcontent = (isset($track_desc_postcontent)) ? $track_desc_postcontent : null;
        $playlist = $this->get_playlist($albums, $title, $feed_title, $feed, $feed_img, $el_widget_id, $artwork, $posts_per_pages, $all_category, $single_playlist, $reverse_tracklist, $audio_meta_field, $repeater_meta_field, 'sticky', $track_desc_postcontent);
        if(!is_array($playlist) || empty($playlist['tracks']))
        return;
        
        wp_send_json($playlist);
        
    }
    private function findData($arr, $id, &$results = []){
        foreach ($arr as $data) {           
            if ( is_array($data) ){
                if (array_key_exists('id', $data)) {
                    if($data['id'] == $id){
                        $results = $data;
                    }
                }
                $this->findData( $data, $id, $results);     
            }
        }
        return false ;
    }
    private function get_wc_price($id){
        if ( !defined( 'WC_VERSION' ) ){
            return;
        }
       
        $currency = get_woocommerce_currency_symbol();
        $currency_pos = get_option('woocommerce_currency_pos');
       
        $product_price = get_post_meta( $id, '_price', true );
        
        if ($product_price == ''){
            $product_price = esc_html__("Free", 'sonaar-music');
            return $product_price;
        }
        
        if ($product_price != ''){
            if ( $currency_pos == 'left' ){
                return html_entity_decode($currency . $product_price);
            } else if ( $currency_pos == 'left_space' ) {
                return html_entity_decode($currency . ' ' . $product_price);
            }else if( $currency_pos == 'right' ){
                return html_entity_decode($product_price . $currency);
            }else{
                return html_entity_decode($product_price . ' ' . $currency);
            }
        }
    }
    private function is_variable_product($id){
        $product_attributes = get_post_meta( $id, '_product_attributes', false );

        if (!is_array($product_attributes) || count($product_attributes) ==0 ){
            return false;
        }
        $prod_has_attributes = array_column($product_attributes[0], 'is_variation');
        foreach($prod_has_attributes as $a){
            if ($a == 1){
                return true;
            }
        }
        return false;
    }
private function push_caticons_in_storelist($post, $terms = null){
    $terms = (is_array($terms)) ? $terms[0] : $terms;
    $store_list =  array();
    $post_id = $post->ID;

    $default_args = array(
        'post_type'           => SR_PLAYLIST_CPT,
        'post_status'         => 'publish',
        'orderby'             => 'date',
        'posts_per_page'      => -1,
        'ignore_sticky_posts' => true,
    );   
    
    $default_args['tax_query'] = array(
            array(
                'taxonomy' => 'podcast-show',
                'field'    => 'term_id',
                'terms'    => $terms
            )
    );
    
    $query_args = apply_filters( 'sonaar_podcast_feed_query_args', $default_args );
    $qry = new WP_Query( $query_args );
    $options = Sonaar_Music_Admin::getPodcastPlatforms();
    $stores = get_term_meta($terms, 'podcast_rss_url', true);
    
    if (isset($stores) && is_array($stores)){
       
        foreach ( $stores as $store ) {
            if ( isset($store['srpodcast_url'] )){
                //var_dump(array_key_exists('srpodcast_name', $store));
               // die();$options[ $store['srpodcast_url_icon'] ]
                if ( array_key_exists('srpodcast_name', $store) && $store['srpodcast_name'] !== '' ){
                    $store['name'] = $store['srpodcast_name'];
                }else if( isset($options[$store['srpodcast_url_icon']] )){
                    $store['name'] = $options[$store['srpodcast_url_icon']];
                }else{
                    $store['name'] = '';
                }

                //$store['name'] = ($store['srpodcast_name'] !== '') ? $store['srpodcast_name'] : ( isset($options[$store['srpodcast_url_icon']] ) ) ? $options[$store['srpodcast_url_icon']] : '';
                array_push($store_list, [
                    'store-icon'    => $store['srpodcast_url_icon'],
                    'store-link'    => $store['srpodcast_url'],
                    'store-name'    => $store['name'],
                    'store-target'  => '_blank',
                    'show-label'    => true
                ]);
            }
        }
    }    
    return $store_list;

}
    private function push_woocart_in_storelist($post, $is_variable_product = null, $wc_add_to_cart = false, $wc_buynow_bt = false){
        if (  !defined( 'WC_VERSION' ) || ( defined( 'WC_VERSION' ) && !function_exists( 'run_sonaar_music_pro' ) && get_site_option('SRMP3_ecommerce') != '1' ) ){
            return false;
		}

        $wc_bt_type = Sonaar_Music::get_option('wc_bt_type', 'srmp3_settings_woocommerce');
        $store_list =  array();
        
        if ( $wc_bt_type == 'wc_bt_type_inactive' ){
            return $store_list;
        }
        if(!isset($post->ID)){
            $post = get_post($post);
        }
        
        $post_id = $post->ID;
        $slug = $post->post_name;
    
        $homeurl = esc_url( home_url() );
        $product_permalink = get_option('woocommerce_permalinks')['product_base'];
        $product_slug = $slug;
        $checkout_url = ( defined( 'WC_VERSION' ) ) ? wc_get_checkout_url() : '';
        $product_price = ( $wc_bt_type !='wc_bt_type_label' ) ? $this->get_wc_price($post_id) : '';
    
        if( $wc_add_to_cart == 'true' ){
            $label = (Sonaar_Music::get_option('wc_add_to_cart_text', 'srmp3_settings_woocommerce') && Sonaar_Music::get_option('wc_add_to_cart_text', 'srmp3_settings_woocommerce') != '' && Sonaar_Music::get_option('wc_add_to_cart_text', 'srmp3_settings_woocommerce') != 'Add to Cart') ? Sonaar_Music::get_option('wc_add_to_cart_text', 'srmp3_settings_woocommerce') : esc_html__('Add to Cart', 'sonaar-music');
            $label = ($wc_bt_type == 'wc_bt_type_price') ? '' : $label . ' '; 
            $url_if_variation = get_permalink( $post_id ); //no add to cart since its a variation and user must choose variation from the single page
            $url_if_no_variation = get_permalink(get_the_ID()) . '?add-to-cart=' . $post_id;
            $storeicon = ( Sonaar_Music::get_option('wc_bt_show_icon', 'srmp3_settings_woocommerce') =='true' ) ? 'fas fa-cart-plus' : '';
            $pageUrl = ($is_variable_product == 1) ? $url_if_variation : $url_if_no_variation ;

            $storeListArgs = [
                'store-icon'    => $storeicon,
                'store-link'    => $pageUrl,
                'store-name'    => $label . $product_price,
                'store-target'  => '_self',
                'show-label'    => true,
                'has-variation' => $is_variable_product == 1,
                'product-id'    =>$post_id
            ];

            array_push($store_list, $storeListArgs);
        }
       
        if( $wc_buynow_bt == 'true' ){
            $label = (Sonaar_Music::get_option('wc_buynow_text', 'srmp3_settings_woocommerce') && Sonaar_Music::get_option('wc_buynow_text', 'srmp3_settings_woocommerce') != '' && Sonaar_Music::get_option('wc_buynow_text', 'srmp3_settings_woocommerce') != 'Buy Now' ) ? Sonaar_Music::get_option('wc_buynow_text', 'srmp3_settings_woocommerce') : esc_html__('Buy Now', 'sonaar-music');
            $label = ($wc_bt_type == 'wc_bt_type_price') ? '' : $label . ' '; 
            $url_if_variation = $homeurl . $product_permalink . '/' . $product_slug; //no add to cart since its a variation and user must choose variation from the single page;
            $url_if_no_variation = $checkout_url . '?add-to-cart=' . $post_id;
            $storeicon = ( Sonaar_Music::get_option('wc_bt_show_icon', 'srmp3_settings_woocommerce') == 'true' ) ? 'fas fa-shopping-cart' : '';
            $pageUrl = ($is_variable_product == 1) ? $url_if_variation : $url_if_no_variation ;

            array_push($store_list, [
                'store-icon'    => $storeicon,
                'store-link'    => $pageUrl,
                'store-name'    =>  $label . $product_price,
                'store-target'  => '_self',
                'show-label'    => true
            ]);
        }
        return $store_list;
    }
    private function ifProductHasVariation($post_id){ 
        if(get_post_type( $post_id ) == 'product'){
            $product = wc_get_product($post_id);
            if($product->is_type( 'variable' )){
                $variations = $product->get_available_variations();
                $variations_id = wp_list_pluck( $variations, 'variation_id' ); 
                if( count($variations_id) > 0){
                    return true;
                }
            }
        }
        return false;
    }
    private function checkACF($field, $ids, $loop = true){
        if (substr( $field, 0, 3 ) === "acf") { 
            if (!function_exists('get_field')) return $field;
            if (empty($ids[0])){
                // make sure to get current post id if no album id has been specified so we can run the checkACF function.
                $ids[0] = get_post(get_the_ID());
            }
            $strings = '';
            foreach ( $ids as $a ) {
                if (!$loop){
                    $strings .= get_field( $field,  $a->ID );
                    return $strings;
                }
                $separator = ($a != end($ids)) ? " || " : '';
                $strings .= get_field( $field,  $a->ID ) . $separator;
            }
            return $strings;
        }
        return $field;
    }

    private function get_playlist($album_ids = array(), $title = null, $feed_title = null, $feed = null, $feed_img = null, $el_widget_id = null, $artwork = null, $posts_per_pages = null, $all_category = null, $single_playlist = false, $reverse_tracklist = false, $audio_meta_field = null, $repeater_meta_field = null, $player = 'widget', $track_desc_postcontent  = null) {
        global $post;
        $playlist = array();
        $tracks = array();
        $albums = '';

        if(!is_array($album_ids)) {
            $album_ids = explode(",", $album_ids);
        }

        if( function_exists( 'run_sonaar_music_pro' ) && Sonaar_Music::get_option('sticky_show_related-post', 'srmp3_settings_sticky_player') == 'true' && !$all_category && $single_playlist){            
            $args =  array(
                'post_status'=> 'publish',
                'order' => 'DESC',
                'orderby' => 'date',
                'post_type'=> ( Sonaar_Music::get_option('srmp3_posttypes', 'srmp3_settings_general') != null ) ? Sonaar_Music::get_option('srmp3_posttypes', 'srmp3_settings_general') : SR_PLAYLIST_CPT,
                'posts_per_page' => $posts_per_pages,
                'lang' => ''
            ); 
            $get_podcastshow_terms = [];
            $get_playlistcat_terms = [];
            $get_product_terms = [];
           
            foreach ($album_ids as $value) {
                if( is_array( get_the_terms( $value, 'playlist-category' ) ) && get_the_terms( $value, 'playlist-category') ){
                    if (!in_array(get_the_terms( $value, 'playlist-category')[0]->term_id, $get_playlistcat_terms)){
                        array_push( $get_playlistcat_terms, get_the_terms( $value, 'playlist-category')[0]->term_id);
                    }
                    
                }

                if( is_array( get_the_terms( $value, 'podcast-show' ) ) && get_the_terms( $value, 'podcast-show') ){
                    if (!in_array(get_the_terms( $value, 'podcast-show')[0]->term_id, $get_podcastshow_terms)){
                        array_push( $get_podcastshow_terms, get_the_terms( $value, 'podcast-show')[0]->term_id);
                    }
                }

                if( is_array( get_the_terms( $value, 'product_cat' ) ) && get_the_terms( $value, 'product_cat') ){
                    if (!in_array(get_the_terms( $value, 'product_cat')[0]->term_id, $get_product_terms)){
                        array_push( $get_product_terms, get_the_terms( $value, 'product_cat')[0]->term_id);
                    }                
                }
            }
            if($get_podcastshow_terms || $get_playlistcat_terms || $get_product_terms){
                $args['tax_query']= array();
                if( ($get_podcastshow_terms && $get_playlistcat_terms) || ($get_podcastshow_terms && $get_product_terms) || ($get_playlistcat_terms && $get_product_terms) ){
                    $args['tax_query'] = array('relation' => 'OR');
                }
                if($get_podcastshow_terms){
                    array_push($args['tax_query'] , array(
                        array(
                        'taxonomy' => 'podcast-show',
                        'field'    => 'id',
                        'terms'    =>  $get_podcastshow_terms
                        ),
                    ));
                }
                if($get_playlistcat_terms){
                    array_push($args['tax_query'], array(
                        array(
                        'taxonomy' => 'playlist-category',
                        'field'    => 'id',
                        'terms'    =>  $get_playlistcat_terms
                        ),
                    ));
                }
                if($get_product_terms){
                    array_push($args['tax_query'], array(
                        array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'id',
                        'terms'    =>  $get_product_terms
                        ),
                    ));
                }
            }else{
                $args['post__in'] = $album_ids;
            }
        }else{
            $args = array(
                'posts_per_page' => $posts_per_pages,
                'post_type' => ( Sonaar_Music::get_option('srmp3_posttypes', 'srmp3_settings_general') != null ) ? Sonaar_Music::get_option('srmp3_posttypes', 'srmp3_settings_general') : SR_PLAYLIST_CPT,//array(SR_PLAYLIST_CPT, 'post', 'product'),
                'post__in' => $album_ids,
                'lang' => ''
            );
        }
        $albums = get_posts($args);
       

        if(Sonaar_Music::get_option('show_artist_name', 'srmp3_settings_general') ){
            $artistSeparator = (Sonaar_Music::get_option('artist_separator', 'srmp3_settings_general') && Sonaar_Music::get_option('artist_separator', 'srmp3_settings_general') != '' && Sonaar_Music::get_option('artist_separator', 'srmp3_settings_general') != 'by' )?Sonaar_Music::get_option('artist_separator', 'srmp3_settings_general'): esc_html__('by', 'sonaar-music');
            $artistSeparator = ' ' . $artistSeparator . ' ';
        }else{
            $artistSeparator = '';
        }
       
        if( $feed == '1' ){
            //001. FEED = 1 MEANS ITS A FEED BUILT WITH ELEMENTOR AND USE TRACKS UPLOAD. IF A PREDEFINED PLAYLIST IS SET, GO TO 003. FEED = 1 VALUE IS SET IN THE SR-MUSIC-PLAYER.PHP
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                //__A. WE ARE IN EDITOR SO USE CURRENT POST META SOURCE TO UPDATE THE WIDGET LIVE OTHERWISE IT WONT UPDATE WITH LIVE DATA
                $album_tracks =  get_post_meta( $album_ids[0], 'srmp3_elementor_tracks', true);
                if($album_tracks == ''){
                    return;
                }   
            }else{
                //__B. WE ARE IN FRONT-END SO USE SAVED POST META SOURCE
                $elementorData = get_post_meta( $album_ids[0], '_elementor_data', true);
                $elementorData = (is_string($elementorData)) ? json_decode($elementorData, true) : ''; // make sure json_decode is parsing a string
                if(empty($elementorData)){
                    return;
                }
                
                $id = $el_widget_id;
                $results=[];

                $this->findData( $elementorData, $id, $results );

                $album_tracks = $results['settings']['feed_repeater'];

                $artwork = ( isset($results['settings']['album_img']['id'] ) && !empty($results['settings']['album_img']['id'] )) ? wp_get_attachment_image_src( $results['settings']['album_img']['id'], 'large' )[0] : '';
            }
        
            $num = 1;
            for($i = 0 ; $i < count($album_tracks) ; $i++) {

                $track_title = ( isset($album_tracks[$i]['feed_track_title'] )) ? $album_tracks[$i]['feed_track_title'] : false;
                $track_length = false;
                $album_title = false;
                $artworkImageSize = ( $player == 'sticky' )? 'medium' : 'large';
                if ( isset( $album_tracks[$i]['feed_track_img']['id'] ) && $album_tracks[$i]['feed_track_img']['id'] != ''){
                    $thumb_url = wp_get_attachment_image_src( $album_tracks[$i]['feed_track_img']['id'], $artworkImageSize )[0];
                    $thumb_id = $album_tracks[$i]['feed_track_img']['id'];
                }else{
                   $thumb_url = $artwork;
                }
                

                if( isset( $album_tracks[$i]['feed_source_file']['url'] ) ){
                    // TRACK SOURCE IS FROM MEDIA LIBRARY
                    $audioSrc = $album_tracks[$i]['feed_source_file']['url'];
                    $mp3_id = $album_tracks[$i]['feed_source_file']['id'];
                    $mp3_metadata = wp_get_attachment_metadata( $mp3_id );
                    $track_length = ( isset( $mp3_metadata['length_formatted'] ) && $mp3_metadata['length_formatted'] !== '' )? $mp3_metadata['length_formatted'] : false;
                    $album_title = ( isset( $mp3_metadata['album'] ) && $mp3_metadata['album'] !== '' )? $mp3_metadata['album'] : false;
                    $track_artist = ( isset( $mp3_metadata['artist'] ) && $mp3_metadata['artist'] !== '' )? $mp3_metadata['artist'] : false;
                    $track_title = ( isset( $mp3_metadata["title"] ) && $mp3_metadata["title"] !== '' )? $mp3_metadata["title"] : false ;
                    //todo description below
                    if ( function_exists( 'run_sonaar_music_pro' ) ){
                        $media_post = get_post( $mp3_id );
                        $track_description = ( isset( $media_post->post_content ) && $media_post->post_content !== '' )? $media_post->post_content : false ;
                    }else{
                        $track_description = '';
                    }
                    $track_title = ( get_the_title( $mp3_id ) !== '' && $track_title !== get_the_title( $mp3_id ) ) ? get_the_title( $mp3_id ) : $track_title;
                    $track_title = html_entity_decode( $track_title, ENT_COMPAT, 'UTF-8' );


                }else if( isset( $album_tracks[$i]['feed_source_external_url']['url'] ) ){
                     // TRACK SOURCE IS AN EXTERNAL LINK
                    $audioSrc = $album_tracks[$i]['feed_source_external_url']['url'];
                }else{
                    $audioSrc = '';
                }
                $showLoading = true;

                ////////
                
                $album_tracks[$i] = array();
                $album_tracks[$i]["id"] = '';
                $album_tracks[$i]["mp3"] = $audioSrc;
                $album_tracks[$i]["loading"] = $showLoading;
                $album_tracks[$i]["track_title"] = ( $track_title )? $track_title : "Track ". $num;
                $album_tracks[$i]["track_artist"] = ( isset( $track_artist ) && $track_artist != '' )? $track_artist : '';
                $album_tracks[$i]["lenght"] = $track_length;
                $album_tracks[$i]["album_title"] = ( $album_title )? $album_title : '';
                $album_tracks[$i]["poster"] = ( $thumb_url )? urldecode($thumb_url) : null;
                if(isset($thumb_id)){
                    $album_tracks[$i]["track_image_id"] = $thumb_id;    
                }
                
                $album_tracks[$i]["release_date"] = false;
                $album_tracks[$i]["song_store_list"] ='';
                $album_tracks[$i]["has_song_store"] = false;
                $album_tracks[$i]['sourcePostID'] = null;
                $album_tracks[$i]['description'] = (isset($track_description)) ? $track_description : null;
                $thumb_id = null;
                $num++;
            }
                $tracks = array_merge($tracks, $album_tracks);

        }else if ( $feed && $feed != '1'){           
            // 002. FEED MEANS SOURCE IS USED DIRECTLY IN THE SHORTCODE ATTRIBUTE
            $feed = $this->checkACF($feed, $albums);
            $feed_title = $this->checkACF($feed_title, $albums);
            $feed_img = $this->checkACF($feed_img, $albums);
            $artwork = $this->checkACF($artwork, $albums, false); 

            $thealbum = array();

            $feed_ar = explode('||', $feed);
            $feed_title_ar = explode('||', $feed_title);
            $feed_img_ar = explode('||', $feed_img);

            $thealbum = [$feed_ar];
            
            foreach($thealbum as $a) {
                $album_tracks = $feed_ar;
                $num = 1;
                for($i = 0 ; $i < count($feed_ar) ; $i++) {
                    $track_title = ( isset( $feed_title_ar[$i] )) ? $feed_title_ar[$i] : false;

                    if ( isset($feed_img_ar[$i]) ){
                        $thumb_url = $feed_img_ar[$i];
                    }else{
                       $thumb_url = $artwork;
                    }
                    
                    ////////
                    $audioSrc = $feed_ar[$i];
                    $showLoading = true;
                    ////////
                    $album_tracks[$i] = array();
                    $album_tracks[$i]["id"] = '';
                    $album_tracks[$i]["mp3"] = $audioSrc;
                    $album_tracks[$i]["loading"] = $showLoading;
                    $album_tracks[$i]["track_title"] = ( $track_title )? $track_title : "Track ". $num;
                    $album_tracks[$i]["track_artist"] = ( isset( $track_artist ) && $track_artist != '' )? $track_artist : '';
                    $album_tracks[$i]["lenght"] = false;
                    $album_tracks[$i]["album_title"] = '';
                    $album_tracks[$i]["poster"] = ( $thumb_url )? urldecode($thumb_url) : $artwork;
                    $album_tracks[$i]["release_date"] = false;
                    $album_tracks[$i]["song_store_list"] ='';
                    $album_tracks[$i]["has_song_store"] = false;
                    $album_tracks[$i]['sourcePostID'] = null;
                    $num++;
                }

                $tracks = array_merge($tracks, $album_tracks);
            }     
        }else if ( isset($audio_meta_field) && $audio_meta_field != ''){
            // 003. FEED SOURCE IS METAKEY

            if(is_numeric($audio_meta_field) ){
                $meta_key_type = 'id';
            }else if(strpos($audio_meta_field, "http") === 0){
                $meta_key_type = 'url';
            }else if(is_array($audio_meta_field)){
                $meta_key_type = 'array';
            }else{
                $meta_key_type = 'meta';
            }
           
            foreach ( $albums as $a ) {
                $album_tracks = array();
                
                if($meta_key_type == 'meta' && $repeater_meta_field != '' && is_array(get_post_meta( $a->ID, $repeater_meta_field, true))){
                   //REPEATER IS SET BY JETENGINE
                    foreach ( get_post_meta( $a->ID, $repeater_meta_field, true ) as $audio_track ) {
                        array_push($album_tracks, $audio_track);
                    }
                }else if( $meta_key_type == 'meta' && $repeater_meta_field != '' && is_array(get_post_meta( $a->ID, $repeater_meta_field )) ){
                    //REPEATER IS SET BY ACF
                    $numbers_of_tracks = (isset(get_post_meta( $a->ID, $repeater_meta_field )[0])) ? get_post_meta( $a->ID, $repeater_meta_field )[0] : '';
                    for ($i = 0; $i < $numbers_of_tracks; $i++) {
                        
                        $audio_track = $repeater_meta_field . '_' . $i . '_' . $audio_meta_field;
                        $audio_track = get_post_meta( $a->ID, $audio_track )[0];
                        array_push($album_tracks, $audio_track);
                    }
                }else{
                    array_push($album_tracks, $audio_meta_field);
                }
                
                $wc_add_to_cart = $this->wc_add_to_cart($a->ID);
                $wc_buynow_bt =  $this->wc_buynow_bt($a->ID);
                $is_variable_product = ($wc_add_to_cart == 'true' || $wc_buynow_bt == 'true' ) ? $this->is_variable_product($a->ID) : '';
              
                if ( get_post_meta( $a->ID, 'reverse_post_tracklist', true) ){
                    $album_tracks = array_reverse($album_tracks); //reverse tracklist order POST option
                }
                
                if ($album_tracks!=''){ 
                    for($i = 0 ; $i < count($album_tracks) ; $i++) {
                       
                        $fileOrStream =  'mp3';
                        $thumb_id = get_post_thumbnail_id($a->ID);
                        if(isset($album_tracks[$i]["track_image_id"]) && $album_tracks[$i]["track_image_id"] != ''){
                            $thumb_id = $album_tracks[$i]["track_image_id"];
                        }
                        $artworkImageSize = ( $player == 'sticky' )? 'medium' : Sonaar_Music::get_option('music_player_coverSize', 'srmp3_settings_widget_player');
                        $thumb_url = ( $thumb_id )? wp_get_attachment_image_src($thumb_id, $artworkImageSize , true)[0] : false ;
                        if ($artwork){ //means artwork is set in the shortcode so prioritize this image instead of the the post featured image.
                            $thumb_url = $artwork;
                        }
                        $track_title = false;
                        $album_title = false;
                        $mp3_id = false;
                        $mp3_metadata = '';
                        $track_description = false;
                        $showLoading = false;
                        $track_length = false;
                        $audioSrc = '';
                        $song_store_list = isset($album_tracks[$i]["song_store_list"]) ? $album_tracks[$i]["song_store_list"] : '' ;
                        $album_store_list = ($wc_add_to_cart == 'true' || $wc_buynow_bt == 'true') ? $this->push_woocart_in_storelist($a->ID, $is_variable_product, $wc_add_to_cart, $wc_buynow_bt) : false;
                        $has_song_store =false;
                        if (isset($song_store_list[0])){
                            $has_song_store = true; 
                        }

                        switch ($fileOrStream) {
                            
                            case 'mp3':
                                switch($meta_key_type){
                                    case 'id':
                                        $mp3_id = $audio_meta_field;
                                        $mp3_metadata = wp_get_attachment_metadata( $mp3_id );
                                    break;
                                    
                                    case 'url':
                                        $audioSrc = $audio_meta_field;
                                        $mp3_metadata = $this->wordpress_audio_meta( $audioSrc );
                                    break;
                                   case 'meta':
                                        if(is_array(get_post_meta( $a->ID, $audio_meta_field)) && is_numeric( get_post_meta( $a->ID, $audio_meta_field, true) )){
                                            //this is an array that contains an media ID.
                                            $mp3_id = get_post_meta( $a->ID, $audio_meta_field, true);
                                            $mp3_metadata = wp_get_attachment_metadata( $mp3_id );
                                            
                                        }else if( $repeater_meta_field !='' ){
                                            // Repeater SET
                                            if(is_numeric( $album_tracks[$i] )){
                                                // ACF is used
                                                $mp3_id = $album_tracks[$i];
                                                $mp3_metadata = wp_get_attachment_metadata( $mp3_id );
                                            }else{
                                                // JETENGINE is used
                                                $audioSrc = (isset($album_tracks[$i][$audio_meta_field])) ? $album_tracks[$i][$audio_meta_field] : '';
                                                $mp3_metadata = $this->wordpress_audio_meta( $audioSrc );
                                            }
                                            
                                            
                                        }else{
                                            $audioSrc = (is_array( get_post_meta( $a->ID, $audio_meta_field, true ) ) ) ? $album_tracks[$i] : get_post_meta( $a->ID, $audio_meta_field, true);
                                            $mp3_metadata = $this->wordpress_audio_meta( $audioSrc );
                                        }
                                    break;
                                }
                                    $track_title = ( isset( $mp3_metadata["title"] ) && $mp3_metadata["title"] !== '' )? $mp3_metadata["title"] : '' ;
                                    $track_title = ($track_title == '') ? get_the_title($a) : $track_title;
                                    $track_title = html_entity_decode($track_title, ENT_COMPAT, 'UTF-8');
                                    $track_artist = ( isset( $mp3_metadata['artist'] ) && $mp3_metadata['artist'] !== '' )? $mp3_metadata['artist'] : false;
                                    $album_title = ( isset( $mp3_metadata['album'] ) && $mp3_metadata['album'] !== '' )? $mp3_metadata['album'] : get_the_title($a->ID);
                                    $track_length = ( isset( $mp3_metadata['length_formatted'] ) && $mp3_metadata['length_formatted'] !== '' )? $mp3_metadata['length_formatted'] : false;
                                    $audioSrc = ($audioSrc == '') ? wp_get_attachment_url($mp3_id) : $audioSrc ;
                                    $showLoading = true;
                                break;
                        }

                        $num = 1;
                        $album_tracks[$i] = array();
                        $album_tracks[$i]["id"] = ( $mp3_id )? $mp3_id : '' ;
                        $album_tracks[$i]["mp3"] = $audioSrc;
                        $album_tracks[$i]["loading"] = $showLoading;
                        $album_tracks[$i]["track_title"] = ( $track_title ) ? $track_title : "Track ". $num++;
                        $album_tracks[$i]["track_artist"] = ( isset( $track_artist ) && $track_artist != '' )? $track_artist : '';
                        $album_tracks[$i]["lenght"] = $track_length;
                        $album_tracks[$i]["album_title"] = ( $album_title )? $album_title :'';
                        $album_tracks[$i]["poster"] = urldecode($thumb_url);
                        if(isset($thumb_id)){
                            $album_tracks[$i]["track_image_id"] = $thumb_id;    
                        }
                        $album_tracks[$i]["release_date"] = get_post_meta($a->ID, 'alb_release_date', true);
                        $album_tracks[$i]["song_store_list"] = $song_store_list;
                        $album_tracks[$i]["has_song_store"] = $has_song_store;
                        $album_tracks[$i]["album_store_list"] = $album_store_list;
                        $album_tracks[$i]['sourcePostID'] = $a->ID;
                        $thumb_id = null;
                        
                    }
                
                    $tracks = array_merge($tracks, $album_tracks);
                }
            }
        } else {
            // 004. FEED SOURCE IS A POSTID -> ALB_TRACKLIST POST META
            foreach ( $albums as $a ) {
                $album_tracks =  get_post_meta( $a->ID, 'alb_tracklist', true);
                $wc_add_to_cart = $this->wc_add_to_cart($a->ID);
                $wc_buynow_bt =  $this->wc_buynow_bt($a->ID);
                $is_variable_product = ($wc_add_to_cart == 'true' || $wc_buynow_bt == 'true' ) ? $this->is_variable_product($a->ID) : '';
              
                if ( get_post_meta( $a->ID, 'reverse_post_tracklist', true) ){
                    $album_tracks = array_reverse($album_tracks); //reverse tracklist order POST option
                }
                
                if ($album_tracks!=''){ 
                    for($i = 0 ; $i < count($album_tracks) ; $i++) {
                        $track_artist = ''; // reset artist value.
                        $fileOrStream =  $album_tracks[$i]['FileOrStream'];
                        $thumb_id = get_post_thumbnail_id($a->ID);
                        if(isset($album_tracks[$i]["track_image_id"]) && $album_tracks[$i]["track_image_id"] != ''){
                            $thumb_id = $album_tracks[$i]["track_image_id"];
                        }
                        
                        $artworkImageSize = ( $player == 'sticky' )? 'medium' : Sonaar_Music::get_option('music_player_coverSize', 'srmp3_settings_widget_player');

                        $thumb_url = ( $thumb_id )? wp_get_attachment_image_src($thumb_id, $artworkImageSize, true)[0] : false ;
                        if ($artwork){ //means artwork is set in the shortcode so prioritize this image instead of the the post featured image.
                           // $thumb_url = $artwork;
                        }
                        //$store_array = array();
                        $track_title = false;
                        $album_title = false;
                        $mp3_id = false;
                        $media_post = false;
                        $track_description = false;
                        $audioSrc = '';
                        $song_store_list = isset($album_tracks[$i]["song_store_list"]) ? $album_tracks[$i]["song_store_list"] : '' ;
                        $album_store_list = ($wc_add_to_cart == 'true' || $wc_buynow_bt == 'true') ? $this->push_woocart_in_storelist($a, $is_variable_product, $wc_add_to_cart, $wc_buynow_bt) : false;
                        $has_song_store =false;
                        if (isset($song_store_list[0])){
                            $has_song_store = true; 
                        }
                        
                        $showLoading = false;
                        $track_length = false;
                        $has_lyric = (isset($album_tracks[$i]['track_lyrics']) && $album_tracks[$i]['track_lyrics'] != false)? true : false;

                        switch ($fileOrStream) {
                            case 'mp3':
                                if ( isset( $album_tracks[$i]["track_mp3"] ) ) {
                                    $mp3_id = $album_tracks[$i]["track_mp3_id"];
                                    $mp3_metadata = wp_get_attachment_metadata( $mp3_id );
                                    $track_title = ( isset( $mp3_metadata["title"] ) && $mp3_metadata["title"] !== '' )? $mp3_metadata["title"] : false ;
                                    $track_title = ( get_the_title($mp3_id) !== '' && $track_title !== get_the_title($mp3_id))? get_the_title($mp3_id): $track_title;
                                    $track_title = html_entity_decode($track_title, ENT_COMPAT, 'UTF-8');
                                    $track_artist = ( isset( $mp3_metadata['artist'] ) && $mp3_metadata['artist'] !== '' )? $mp3_metadata['artist'] : false;
                                    $album_title = ( isset( $mp3_metadata['album'] ) && $mp3_metadata['album'] !== '' )? $mp3_metadata['album'] : false;
                                    $track_length = ( isset( $mp3_metadata['length_formatted'] ) && $mp3_metadata['length_formatted'] !== '' )? $mp3_metadata['length_formatted'] : false;
                                    $media_post = get_post( $mp3_id );
                                    $track_description = ( isset ($album_tracks[$i]["track_description"]) && $album_tracks[$i]["track_description"] !== '' )? $album_tracks[$i]["track_description"] : false;
                                    $audioSrc = wp_get_attachment_url($mp3_id);
                                    $showLoading = true;
                                }
                                break;

                            case 'stream':
                                
                                $audioSrc = ( array_key_exists ( "stream_link" , $album_tracks[$i] ) && $album_tracks[$i]["stream_link"] !== '' )? $album_tracks[$i]["stream_link"] : false;
                                $track_title = (  array_key_exists ( 'stream_title' , $album_tracks[$i] ) && $album_tracks[$i]["stream_title"] !== '' )? $album_tracks[$i]["stream_title"] : false;
                                $album_title = ( isset ($album_tracks[$i]["stream_album"]) && $album_tracks[$i]["stream_album"] !== '' )? $album_tracks[$i]["stream_album"] : false;
                                $showLoading = true;
                                $track_description = ( isset ($album_tracks[$i]["track_description"]) && $album_tracks[$i]["track_description"] !== '' )? $album_tracks[$i]["track_description"] : false;
                                $track_length = ( isset( $album_tracks[$i]["stream_lenght"] ) && $album_tracks[$i]["stream_lenght"] !== '' ) ? $album_tracks[$i]["stream_lenght"] : false;
                                break;
                            
                            default:
                                $album_tracks[$i] = array();
                                break;
                        }
                
                        $num = 1;
                       
                        $album_tracks[$i] = array();
                        $album_tracks[$i]["id"] = ( $mp3_id )? $mp3_id : '' ;
                        $album_tracks[$i]["mp3"] = $audioSrc;
                        $album_tracks[$i]["loading"] = $showLoading;
                        $album_tracks[$i]["track_title"] = ( $track_title )? $track_title : "Track ". $num++;
                        $album_tracks[$i]["track_artist"] = ( isset( $track_artist ) && $track_artist != '' )? $track_artist : '';
                        $album_tracks[$i]["lenght"] = $track_length;
                        $album_tracks[$i]["album_title"] = ( $album_title )? $album_title : $a->post_title;
                        $album_tracks[$i]["poster"] = urldecode($thumb_url);
                        if(isset($thumb_id)){
                            $album_tracks[$i]["track_image_id"] = $thumb_id;    
                        }
                        $album_tracks[$i]["track_pos"] = ( get_post_meta( $a->ID, 'reverse_post_tracklist', true) )? count($album_tracks) - ($i + 1) : $i ;
                        $album_tracks[$i]["release_date"] = get_post_meta($a->ID, 'alb_release_date', true);
                        $album_tracks[$i]["song_store_list"] = $song_store_list;
                        $album_tracks[$i]["has_song_store"] = $has_song_store;
                        $album_tracks[$i]["album_store_list"] = $album_store_list;
                        $album_tracks[$i]['sourcePostID'] = $a->ID;
                        $album_tracks[$i]['has_lyric'] = $has_lyric;
                        $track_description = ( $track_desc_postcontent ) ? $a->post_content : $track_description;
                        $album_tracks[$i]['description'] = (isset($track_description)) ? $track_description : null;
                        $thumb_id = null;
                    
                    }
            
                    $tracks = array_merge($tracks, $album_tracks);

                }

            }
            if( $reverse_tracklist ){
                $tracks = array_reverse($tracks); //reverse tracklist order option
            }
        }
        $playlist['playlist_name'] = $title;
        if ( empty($playlist['playlist_name']) ) $playlist['playlist_name'] = "";

        $playlist['artist_separator'] = $artistSeparator;

        $playlist['tracks'] = $tracks;
        if ( empty($playlist['tracks']) ) $playlist['tracks'] = array();
        return $playlist;

    }

}
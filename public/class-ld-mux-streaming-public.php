<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    LD_Mux_Streaming
 * @subpackage LD_Mux_Streaming/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    LD_Mux_Streaming
 * @subpackage LD_Mux_Streaming/public
 * @author     Your Name <email@example.com>
 */
class LD_Mux_Streaming_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ld_mux_streaming    The ID of this plugin.
	 */
	private $ld_mux_streaming;

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
	 * @param      string    $ld_mux_streaming       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $ld_mux_streaming, $version ) {

		$this->ld_mux_streaming = $ld_mux_streaming;
		$this->version = $version;

		add_shortcode('mux_streaming_react_app', array($this, 'render_react_app'));
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
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->ld_mux_streaming, plugin_dir_url( __FILE__ ) . 'css/ld-mux-streaming-public.css', array(), $this->version, 'all' );

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
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

	}

	public function get_mux_playback_id( $upload_id ) {
        if ( empty( $upload_id ) ) {
            return null;
        }

        $token  = get_option('ld_mux_token_id');
        $secret = get_option('ld_mux_token_secret');

        if ( empty($token) || empty($secret) ) {
            return null;
        }

        // Step 1: Get asset_id from upload
        $upload_response = wp_remote_get( "https://api.mux.com/video/v1/uploads/{$upload_id}", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode("{$token}:{$secret}"),
                'Content-Type'  => 'application/json',
            ]
        ]);

        if ( is_wp_error($upload_response) ) {
            return null;
        }

        $upload_body = json_decode( wp_remote_retrieve_body($upload_response), true );
        $asset_id = $upload_body['data']['asset_id'] ?? '';

        if ( empty($asset_id) ) {
            return null;
        }

        // Step 2: Get playback_id from asset
        $asset_response = wp_remote_get( "https://api.mux.com/video/v1/assets/{$asset_id}", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode("{$token}:{$secret}"),
                'Content-Type'  => 'application/json',
            ]
        ]);

        if ( is_wp_error($asset_response) ) {
            return null;
        }

        $asset_body = json_decode( wp_remote_retrieve_body($asset_response), true );

        if ( isset($asset_body['data']['status']) && $asset_body['data']['status'] === 'ready' ) {
            return $asset_body['data']['playback_ids'][0]['id'] ?? null;
        }

        return null;
    }

	public function append_the_iframe_if_enabled($content) {
		if (is_singular('sfwd-lessons')) {
			global $post;
			global $wpdb;

			$enabled = get_post_meta($post->ID, '_mux_streaming_key', true);
			$mux_video_file = get_post_meta( $post->ID, '_mux_video_file', true );
			$current_user = wp_get_current_user();
			$lesson_id = $post->ID;
		
			if ( 
				$enabled === 'yes' 
				&& !learndash_is_lesson_complete($current_user->ID, $lesson_id) 
			) {

				$html = '';
				$playback_id = '';
				
            	if ( !empty( $mux_video_file ) ) $playback_id = $this->get_mux_playback_id($mux_video_file);

				
				if ( !empty($playback_id) ) {
					$html = '<iframe src="https://player.mux.com/' . esc_attr($playback_id) . '" style="width:100%; border:none; aspect-ratio:16/9;" allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen></iframe>';
				}
				
				$content .= $html;
			}
		}

		return $content;
	}
}
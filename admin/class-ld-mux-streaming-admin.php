<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    LD_Mux_Streaming
 * @subpackage LD_Mux_Streaming/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    LD_Mux_Streaming
 * @subpackage LD_Mux_Streaming/admin
 * @author     Your Name <email@example.com>
 */
class LD_Mux_Streaming_Admin {

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
	 * @param      string    $ld_mux_streaming       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	private $token_id;
    private $token_secret;

	public function __construct( $ld_mux_streaming, $version ) {

		$this->ld_mux_streaming = $ld_mux_streaming;
		$this->version = $version;

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		$this->token_id = get_option('ld_mux_token_id');
        $this->token_secret = get_option('ld_mux_token_secret');
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->ld_mux_streaming, plugin_dir_url( __FILE__ ) . 'css/ld-mux-streaming-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

        wp_enqueue_script(
            'ld-mux-streaming-admin',
            plugin_dir_url(__FILE__) . 'js/ld-mux-streaming-admin.js',
            ['jquery'],
            $this->version,
            true
        );

        wp_localize_script('ld-mux-streaming-admin', 'ldMuxAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('ld_mux_upload_video_nonce')
        ]);

	}

	/**
     * Add submenu under LearnDash
     */
    public function add_plugin_admin_menu() {
        add_submenu_page(
            'learndash-lms',                           // Parent slug (LearnDash menu)
            __( 'LD Mux Streaming', 'ld-mux-streaming' ), // Page title
            __( 'LD Mux Streaming', 'ld-mux-streaming' ), // Menu title
            'manage_options',                          // Capability
            $this->plugin_name,                        // Menu slug
            array( $this, 'display_plugin_admin_page' ) // Callback
        );
    }

    /**
     * Render settings form
     */
    public function display_plugin_admin_page() {
        ?>
            <!-- <div class="wrap">
                <h1><?php //esc_html_e( 'LD Mux Streaming Settings', 'ld-mux-streaming' ); ?></h1>
                <form method="post" action="options.php">
                    <?php
                        // settings_fields( 'ld_mux_options' );
                        // do_settings_sections( 'ld-mux-streaming' );
                        // submit_button();
                    ?>
                </form>
            </div> -->

            <div class="wrap">
                <h1><?php esc_html_e( 'LD Mux Streaming Settings & Upload', 'ld-mux-streaming' ); ?></h1>

                <!-- Credentials -->
                <!-- <h2><?php //esc_html_e( 'Mux API Credentials', 'ld-mux-streaming' ); ?></h2> -->
                <form method="post" action="options.php">
                    <?php
                        settings_fields( 'ld_mux_options' );
                        do_settings_sections( 'ld-mux-streaming' );
                        submit_button( __( 'Save Credentials', 'ld-mux-streaming' ) );
                    ?>
                </form>

                <hr>

                <!-- <form id="ld-mux-upload-form" enctype="multipart/form-data" method="post"> -->
                    <!-- <input type="hidden" name="action" value="ld_mux_upload_video"> -->
                    <!-- <input type="hidden" id="video_nonce_field" name="ld_mux_upload_video_nonce_field" value="<?php //echo wp_create_nonce('ld_mux_upload_video_nonce'); ?>"> -->

                    <!-- <table class="form-table">
                        <tr>
                            <th><label for="ld_mux_video_title"><?php //esc_html_e( 'Title', 'ld-mux-streaming' ); ?></label></th>
                            <td><input id="video_title" type="text" name="ld_mux_video_title" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="ld_mux_video_desc"><?php //esc_html_e( 'Description', 'ld-mux-streaming' ); ?></label></th>
                            <td><textarea id="video_description" name="ld_mux_video_desc" rows="4" cols="50"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="ld_mux_video_file"><?php //esc_html_e( 'Video File', 'ld-mux-streaming' ); ?></label></th>
                            <td><input id="video_file" type="file" name="ld_mux_video_file" accept="video/*" ></td>
                        </tr>
                    </table> -->

                    <!-- <button type="submit" class="button button-primary"><?php //esc_html_e( 'Upload to Mux', 'ld-mux-streaming' ); ?></button> -->
                <!-- </form> -->

                <!-- <div id="ld-mux-upload-response"></div> -->


            </div>
        <?php
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting( 'ld_mux_options', 'ld_mux_token_id' );
        register_setting( 'ld_mux_options', 'ld_mux_token_secret' );

        add_settings_section(
            'ld_mux_main_section',
            __( 'Mux API Credentials', 'ld-mux-streaming' ),
            null,
            'ld-mux-streaming'
        );

        add_settings_field(
            'ld_mux_token_id',
            __( 'Mux Token ID', 'ld-mux-streaming' ),
            array( $this, 'field_token_id' ),
            'ld-mux-streaming',
            'ld_mux_main_section'
        );

        add_settings_field(
            'ld_mux_token_secret',
            __( 'Mux Token Secret', 'ld-mux-streaming' ),
            array( $this, 'field_token_secret' ),
            'ld-mux-streaming',
            'ld_mux_main_section'
        );
    }

    public function field_token_id() {
        printf(
            '<input type="text" name="ld_mux_token_id" value="%s" class="regular-text" />',
            esc_attr( get_option( 'ld_mux_token_id' ) )
        );
    }

    public function field_token_secret() {
        printf(
            '<input type="text" name="ld_mux_token_secret" value="%s" class="regular-text" />',
            esc_attr( get_option( 'ld_mux_token_secret' ) )
        );
    }

	private function auth_header() {
        return 'Basic ' . base64_encode($this->token_id . ':' . $this->token_secret);
    }

    public function upload_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $options = get_option( $this->option_name );
        ?>
            <div class="wrap">
                <h1>Upload Video to Mux</h1>

                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" enctype="multipart/form-data">
                    <?php
                        settings_fields( 'ldMuxSettings' );
                        do_settings_sections( 'ld_mux_upload' );
                        submit_button( 'Save Credentials' );
                    ?>

                </form>

                <hr>

                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="ld_mux_upload_video">
                    <?php wp_nonce_field( 'ld_mux_upload_video_nonce', 'ld_mux_upload_video_nonce_field' ); ?>

                    <table class="form-table">
                        <tr>
                            <th><label for="ld_mux_video_file">Video File</label></th>
                            <td><input type="file" name="ld_mux_video_file" required accept="video/*"></td>
                        </tr>
                        <tr>
                            <th><label for="ld_mux_video_title">Title</label></th>
                            <td><input type="text" name="ld_mux_video_title" required></td>
                        </tr>
                        <tr>
                            <th><label for="ld_mux_video_desc">Description</label></th>
                            <td><textarea name="ld_mux_video_desc" rows="4" cols="50"></textarea></td>
                        </tr>
                    </table>

                    <?php submit_button( 'Upload to Mux' ); ?>
                </form>
            </div>
        <?php
    }

    public function handle_upload() {
        try {
            
            // 🔒 Security check
            check_ajax_referer('ld_mux_upload_video_nonce', 'ld_mux_upload_video_nonce_field');

            if (!current_user_can('manage_options')) {
                throw new Exception('Unauthorized user');
            }
            

            // ✅ Validate file
            if (empty($_FILES['ld_mux_video_file']['tmp_name'])) {
                throw new Exception('No file uploaded');
            }

            $file  = $_FILES['ld_mux_video_file'];
            $title = sanitize_text_field($_POST['ld_mux_video_title'] ?? '');
            $desc  = sanitize_textarea_field($_POST['ld_mux_video_desc'] ?? '');

            // 🔑 Get Mux credentials
            $token  = get_option('ld_mux_token_id');
            $secret = get_option('ld_mux_token_secret');

            if (empty($token) || empty($secret)) {
                throw new Exception('Mux credentials not set.');
            }

            // 1️⃣ Request a new Direct Upload
            $url  = 'https://api.mux.com/video/v1/uploads';
            $args = [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$token}:{$secret}"),
                    'Content-Type'  => 'application/json',
                ],
                'body' => wp_json_encode([
                    'new_asset_settings' => [
                        'playback_policy' => ['public'],
                        'passthrough'     => $title,
                        'description'     => $desc,
                    ],
                    'cors_origin' => '*',
                ]),
            ];

            $response = wp_remote_post($url, $args);

            if (is_wp_error($response)) {
                throw new Exception('Mux error: ' . $response->get_error_message());
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);

            if (empty($body['data']['url'])) {
                throw new Exception('Mux did not return upload URL');
            }

            $upload_url = $body['data']['url'];

            // 2️⃣ Upload the actual video to Mux
            $filedata    = file_get_contents($file['tmp_name']);
            $contentType = !empty($file['type']) ? $file['type'] : 'application/octet-stream';

            $upload_response = wp_remote_request($upload_url, [
                'method'  => 'PUT',
                'headers' => [
                    'Content-Type' => $contentType,
                ],
                'body'    => $filedata,
                'timeout' => 600,
            ]);

            if (is_wp_error($upload_response)) {
                throw new Exception('Upload failed: ' . $upload_response->get_error_message());
            }

            $status_code = wp_remote_retrieve_response_code($upload_response);
            if ($status_code !== 200) {
                throw new Exception('Mux upload failed. HTTP ' . $status_code);
            }

            // ✅ Success
            wp_send_json_success([
                'message'   => 'Video uploaded successfully to Mux!',
                'upload_id' => $body['data']['id'] ?? '',
                'assetData' => $body['data'],
            ]);

        } catch (Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage(),
            ]);
        }
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

    public function handle_custom_upload($title = null, $desc = null, $file = null, $existingFileID = null) {
        try {
            // ✅ Validate file
            if (empty($file)) {
                throw new Exception('No file uploaded');
            }

            // 🔑 Get Mux credentials
            $token  = get_option('ld_mux_token_id');
            $secret = get_option('ld_mux_token_secret');

            if (empty($token) || empty($secret)) {
                throw new Exception('Mux credentials not set.');
            }

            if (!empty($existingFileID)) {
                $upload_url = "https://api.mux.com/video/v1/uploads/{$existingFileID}";
                $upload_response = wp_remote_get($upload_url, [
                    'headers' => [
                        'Authorization' => 'Basic ' . base64_encode("{$token}:{$secret}"),
                        'Content-Type'  => 'application/json',
                    ],
                    'timeout' => 30,
                ]);

                if (!is_wp_error($upload_response)) {
                    $status_code = wp_remote_retrieve_response_code($upload_response);
                    $body = json_decode(wp_remote_retrieve_body($upload_response), true);

                    if ($status_code === 200 && !empty($body['data']['asset_id'])) {
                        $asset_id = $body['data']['asset_id'];

                        $delete_url = "https://api.mux.com/video/v1/assets/{$asset_id}";
                        $delete_response = wp_remote_request($delete_url, [
                            'method'  => 'DELETE',
                            'headers' => [
                                'Authorization' => 'Basic ' . base64_encode("{$token}:{$secret}"),
                                'Content-Type'  => 'application/json',
                            ],
                            'timeout' => 30,
                        ]);

                        // if (is_wp_error($delete_response)) {
                        //     throw new Exception('Failed to delete existing Mux video: ' . $delete_response->get_error_message());
                        // }

                        // $delete_status = wp_remote_retrieve_response_code($delete_response);
                        // if ($delete_status !== 204) { // Mux returns 204 No Content on successful delete
                        //     throw new Exception('Failed to delete existing Mux video. HTTP ' . $delete_status);
                        // }
                    }
                    // else: Direct Upload exists but asset not yet ready, ignore
                }
                // else: Direct Upload not found, ignore
            }

            // 1️⃣ Request a new Direct Upload
            $url  = 'https://api.mux.com/video/v1/uploads';
            $args = [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$token}:{$secret}"),
                    'Content-Type'  => 'application/json',
                ],
                'body' => wp_json_encode([
                    'new_asset_settings' => [
                        'playback_policy' => ['public'],
                        'passthrough'     => $title,
                        'description'     => $desc,
                    ],
                    'cors_origin' => '*',
                ]),
            ];

            $response = wp_remote_post($url, $args);

            if (is_wp_error($response)) {
                throw new Exception('Mux error: ' . $response->get_error_message());
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);

            if (empty($body['data']['url'])) {
                throw new Exception('Mux did not return upload URL');
            }

            $upload_url = $body['data']['url'];

            // 2️⃣ Upload the actual video to Mux
            $filedata    = file_get_contents($file['tmp_name']);
            $contentType = !empty($file['type']) ? $file['type'] : 'application/octet-stream';

            $upload_response = wp_remote_request($upload_url, [
                'method'  => 'PUT',
                'headers' => [
                    'Content-Type' => $contentType,
                ],
                'body'    => $filedata,
                'timeout' => 600,
            ]);

            if (is_wp_error($upload_response)) {
                throw new Exception('Upload failed: ' . $upload_response->get_error_message());
            }

            $status_code = wp_remote_retrieve_response_code($upload_response);
            if ($status_code !== 200) {
                throw new Exception('Mux upload failed. HTTP ' . $status_code);
            }

            $id = $body['data']['id'] ?? '';
            $data = $body['data'];

            $response = [
                'message'   => 'Video uploaded successfully to Mux!',
                'id' => $id,
                'data' => $data
            ];

            return $response;

            // wp_send_json_success($response);
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function add_custom_field( $setting_option_fields = array(), $settings_metabox_key = '' ) {
        if ( 'learndash-lesson-display-content-settings' === $settings_metabox_key ) {
            $post_id = get_the_ID();
            if ( !$post_id ) return $setting_option_fields;

            $mux_streaming_checkbox_value = get_post_meta( $post_id, '_mux_streaming_key', true );
            if ( empty( $mux_streaming_checkbox_value ) ) {
                $mux_streaming_checkbox_value = '';
            }

            $mux_video_title = !empty(get_post_meta( $post_id, '_mux_video_title', true )) ? get_post_meta( $post_id, '_mux_video_title', true ) : '';
            $mux_video_desc = !empty(get_post_meta( $post_id, '_mux_video_desc', true )) ? get_post_meta( $post_id, '_mux_video_desc', true ) : '';
            $mux_video_file = !empty(get_post_meta( $post_id, '_mux_video_file', true )) ? get_post_meta( $post_id, '_mux_video_file', true ) : '';
            $playback_id = !empty(get_post_meta( $post_id, '_mux_video_playback_id', true )) ? get_post_meta( $post_id, '_mux_video_playback_id', true ) : '';

            // $playback_id = '';
            // if ( !empty( $mux_video_file ) ) $playback_id = $this->get_mux_playback_id($mux_video_file);

            $setting_option_fields['mux-streaming-switch'] = array(
                'name'      => 'mux-streaming-switch',
                'label'     => esc_html__( 'Mux Streaming', 'learndash' ),
                'type'      => 'checkbox-switch',
                'value'     => $mux_streaming_checkbox_value === 'yes' ? 'yes' : '',
                'default'   => '',
                'options'   => array(
                    'yes' => '',
                ),
                'help_text' => esc_html__( 'Enable this option to force the module timer.', 'learndash' ),
            );

            $setting_option_fields['ld_mux_video_title'] = array(
                'name'      => 'ld_mux_video_title',
                'label'     => esc_html__( 'Mux Video Title', 'learndash' ),
                'type'      => 'text',
                'value'     => $mux_video_title,
                'default'   => '',
                'help_text' => esc_html__( 'Enter the title of the video.', 'learndash' ),
            );

            $setting_option_fields['ld_mux_video_desc'] = array(
                'name'      => 'ld_mux_video_desc',
                'label'     => esc_html__( 'Mux Video Description', 'learndash' ),
                'type'      => 'textarea',
                'value'     => $mux_video_desc,
                'default'   => '',
                'help_text' => esc_html__( 'Enter a short description of the video.', 'learndash' ),
            );

            $iframe_html = '';
            if ( !empty($playback_id) ) {
                $iframe_html = '<iframe src="https://player.mux.com/' . esc_attr($playback_id) . '" style="width:100%; border:none; aspect-ratio:16/9;" allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen></iframe>';
            }

            $setting_option_fields['ld_mux_video_file'] = array(
                'name'      => 'ld_mux_video_file',
                'label'     => esc_html__( 'Mux Video File', 'learndash' ),
                'type'      => 'html',
                'value'     => '<input type="file" name="ld_mux_video_file" accept="video/*"><br/>' . $iframe_html,
                'default'   => '',
                'help_text' => esc_html__( 'Upload or select a video file from the Media Library.', 'learndash' ),
            );
        }

        return $setting_option_fields;
    }

	public function save_my_custom_meta( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if(isset($_POST['learndash-lesson-display-content-settings']['nonce'])) {
            $title = null;
            $desc = null;
            $file = null;
            $movefile = null;

			if (isset($_POST['learndash-lesson-display-content-settings']['mux-streaming-switch'])) {
				update_post_meta( $post_id, '_mux_streaming_key', 'yes' );
			}
			else {
				update_post_meta( $post_id, '_mux_streaming_key', '' );
			}

            if (isset($_POST['learndash-lesson-display-content-settings']['ld_mux_video_title'])) {
                $title = sanitize_text_field($_POST['learndash-lesson-display-content-settings']['ld_mux_video_title']);
                update_post_meta( $post_id, '_mux_video_title', $title );
            }

            if (isset($_POST['learndash-lesson-display-content-settings']['ld_mux_video_desc'])) {
                $desc = sanitize_textarea_field($_POST['learndash-lesson-display-content-settings']['ld_mux_video_desc']);
                update_post_meta( $post_id, '_mux_video_desc', $desc );
            }

            if (isset($_FILES['ld_mux_video_file']) && $_FILES['ld_mux_video_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['ld_mux_video_file'];

                $existingFileID = get_post_meta( $post_id, '_mux_video_file', true );
                $response = $this->handle_custom_upload($title, $desc, $file, $existingFileID);

                if($response && $response['id']) {
                    update_post_meta( $post_id, '_mux_video_file', $response['id'] );

                    $playback_id = $this->get_mux_playback_id($response['id']);
                    if($playback_id) update_post_meta( $post_id, '_mux_video_playback_id', $playback_id );
                }

                wp_send_json_success([
                    'message'   => 'Video uploaded successfully!',
                    'resetField' => true
                ]);
            }

            else wp_send_json_success(['message'   => 'Invalid Video!', 'data' => $file]);
		}
	}
}
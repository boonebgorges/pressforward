<?php
if ( empty( $GLOBALS['wp_press_this'] ) ) {
	include( ABSPATH . 'wp-admin/includes/class-wp-press-this.php' );
}

class Nominate_This extends WP_Press_This  {

	function __construct(){

	}

	function get_readable_content( $data ){

		if ( ! empty( $data['s'] ) ) {
			// Let the default behavior take over if the user has
			// highlighted something, on the assumption that's what
			// they actually want.
			return false;
		}
		$url = $this->get_canonical_link( $data );
		//$source_content = $this->fetch_source_html( $url, $data );
		//var_dump($source_content); die();
		$content = pressforward()->pf_feed_items->get_content_through_aggregator($url);

		if ( false == $content ) {
			return false;
		}

		$content .= $this->get_attribution_string( $data, $url );

		return $content;

	}

	function get_attribution_string( $data, $url ){
		$title = $this->get_suggested_title( $data );
		if ( ! $title ) {
			$title = $this->get_source_site_name( $data );
		}
		if ( $url && $title ) {
			$default_source = '<p>' . _x( 'Source:', 'Used in Press This to indicate where the content comes from.' ) .
				' <em><a href="%1$s">%2$s</a></em></p>';
			$content = sprintf( $default_source, $url, $title );
		}
		return $content;
	}

	function set_featured_img_via_pf(){
		if (isset($_POST['item_link']) && !empty($_POST['item_link']) && ($_POST['item_link']) != ''){
			//Gets OG image
			$itemFeatImg = pressforward()->pf_feed_items->get_ext_og_img($_POST['item_link']);
		}

		if (!empty($_POST['item_link']) && ($_POST['item_link']) != ''){
			pressforward()->pf_feed_items->set_ext_as_featured($post_ID, $itemFeatImg);
		}
	}

	public function html() {
		global $wp_locale, $wp_version;
		// Get data, new (POST) and old (GET).
		$data = $this->merge_or_fetch_data();
		$post_title = $this->get_suggested_title( $data );
		if ( empty( $title ) ) {
			$title = __( 'New Nomination' );
		}

		$post_content = $this->get_readable_content( $data );

		if ( !$post_content ) {
			$post_content = $this->get_suggested_content( $data );
		}
		// Get site settings array/data.
		$site_settings = $this->site_settings();
		// Pass the images and embeds
		$images = $this->get_images( $data );
		$embeds = $this->get_embeds( $data );
		$site_data = array(
			'v' => ! empty( $data['v'] ) ? $data['v'] : '',
			'u' => ! empty( $data['u'] ) ? $data['u'] : '',
			'hasData' => ! empty( $data ),
		);
		if ( ! empty( $images ) ) {
			$site_data['_images'] = $images;
		}
		if ( ! empty( $embeds ) ) {
			$site_data['_embeds'] = $embeds;
		}
		// Add press-this-editor.css and remove theme's editor-style.css, if any.
		remove_editor_styles();
		add_filter( 'mce_css', array( $this, 'add_editor_style' ) );
		if ( ! empty( $GLOBALS['is_IE'] ) ) {
			@header( 'X-UA-Compatible: IE=edge' );
		}
		@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
		?>
		<!DOCTYPE html>
		<!--[if IE 7]>         <html class="lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
		<!--[if IE 8]>         <html class="lt-ie9" <?php language_attributes(); ?>> <![endif]-->
		<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
		<head>
			<meta http-equiv="Content-Type" content="<?php echo esc_attr( get_bloginfo( 'html_type' ) ); ?>; charset=<?php echo esc_attr( get_option( 'blog_charset' ) ); ?>" />
			<meta name="viewport" content="width=device-width">
			<title><?php esc_html_e( 'Press This!' ) ?></title>

			<script>
				window.wpPressThisData   = <?php echo wp_json_encode( $site_data ); ?>;
				window.wpPressThisConfig = <?php echo wp_json_encode( $site_settings ); ?>;
			</script>

			<script type="text/javascript">
				var ajaxurl = '<?php echo esc_js( admin_url( 'admin-ajax.php', 'relative' ) ); ?>',
					pagenow = 'press-this',
					typenow = 'post',
					adminpage = 'press-this-php',
					thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
					decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
					isRtl = <?php echo (int) is_rtl(); ?>;
			</script>

			<?php
			/*
			 * $post->ID is needed for the embed shortcode so we can show oEmbed previews in the editor.
			 * Maybe find a way without it.
			 */
			$post = get_default_post_to_edit( 'post', true );
			$post_ID = (int) $post->ID;
			wp_enqueue_media( array( 'post' => $post_ID ) );
			wp_enqueue_style( 'press-this' );
			wp_enqueue_script( 'press-this' );
			wp_enqueue_script( 'json2' );
			wp_enqueue_script( 'editor' );
			$supports_formats = false;
			$post_format      = 0;
			if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) ) {
				$supports_formats = true;
				if ( ! ( $post_format = get_post_format( $post_ID ) ) ) {
					$post_format = 0;
				}
			}
			/** This action is documented in wp-admin/admin-header.php */
			do_action( 'admin_enqueue_scripts', 'press-this.php' );
			/** This action is documented in wp-admin/admin-header.php */
			do_action( 'admin_print_styles-press-this.php' );
			/** This action is documented in wp-admin/admin-header.php */
			do_action( 'admin_print_styles' );
			/** This action is documented in wp-admin/admin-header.php */
			do_action( 'admin_print_scripts-press-this.php' );
			/** This action is documented in wp-admin/admin-header.php */
			do_action( 'admin_print_scripts' );
			/** This action is documented in wp-admin/admin-header.php */
			do_action( 'admin_head-press-this.php' );
			do_action( 'admin_head-nominate-this.php' );
			/** This action is documented in wp-admin/admin-header.php */
			do_action( 'admin_head' );
		?>
		</head>
		<?php
		$admin_body_class  = 'press-this nominate-this';
		$admin_body_class .= ( is_rtl() ) ? ' rtl' : '';
		$admin_body_class .= ' branch-' . str_replace( array( '.', ',' ), '-', floatval( $wp_version ) );
		$admin_body_class .= ' version-' . str_replace( '.', '-', preg_replace( '/^([.0-9]+).*/', '$1', $wp_version ) );
		$admin_body_class .= ' admin-color-' . sanitize_html_class( get_user_option( 'admin_color' ), 'fresh' );
		$admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );
		/** This filter is documented in wp-admin/admin-header.php */
		$admin_body_classes = apply_filters( 'admin_body_class', '' );
		?>
<body class="wp-admin wp-core-ui <?php echo $admin_body_classes . ' ' . $admin_body_class; ?>">
	<div id="adminbar" class="adminbar">
		<h1 id="current-site" class="current-site">
			<a class="current-site-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="home">
				<span class="dashicons dashicons-wordpress"></span>
				<span class="current-site-name"><?php bloginfo( 'name' ); ?></span>
			</a>
		</h1>
		<button type="button" class="options button-subtle closed">
			<span class="dashicons dashicons-tag on-closed"></span>
			<span class="screen-reader-text on-closed"><?php _e( 'Show post options' ); ?></span>
			<span aria-hidden="true" class="on-open"><?php _e( 'Done' ); ?></span>
			<span class="screen-reader-text on-open"><?php _e( 'Hide post options' ); ?></span>
		</button>
	</div>

	<div id="scanbar" class="scan">
		<form method="GET">
			<label for="url-scan" class="screen-reader-text"><?php _e( 'Scan site for content' ); ?></label>
			<input type="url" name="u" id="url-scan" class="scan-url" value="" placeholder="<?php esc_attr_e( 'Enter a URL to scan' ) ?>" />
			<input type="submit" name="url-scan-submit" id="url-scan-submit" class="scan-submit" value="<?php esc_attr_e( 'Scan' ) ?>" />
		</form>
	</div>

	<form id="pressthis-form" method="post" action="post.php" autocomplete="off">
		<input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post_ID; ?>" />
		<input type="hidden" name="action" value="press-this-save-post" />
		<input type="hidden" name="post_status" id="post_status" value="draft" />
		<input type="hidden" name="wp-preview" id="wp-preview" value="" />
		<input type="hidden" name="post_title" id="post_title" value="" />
		<?php
		wp_nonce_field( 'update-post_' . $post_ID, '_wpnonce', false );
		wp_nonce_field( 'add-category', '_ajax_nonce-add-category', false );
		?>

	<div class="wrapper">
		<div class="editor-wrapper">
			<div class="alerts" role="alert" aria-live="assertive" aria-relevant="all" aria-atomic="true">
				<?php
				if ( isset( $data['v'] ) && $this->version > $data['v'] ) {
					//var_dump(plugin_dir_path( __FILE__ )); die();
					?>
					<p class="alert is-notice">
						<?php printf( __( 'You should upgrade <a href="%s" target="_blank">your bookmarklet</a> to the latest version!' ), admin_url( 'admin.php?page=pf-tools' ) ); ?>
					</p>
					<?php
				}
				?>
			</div>

			<div id="app-container" class="editor">
				<span id="title-container-label" class="post-title-placeholder" aria-hidden="true"><?php _e( 'Post title' ); ?></span>
				<h2 id="title-container" class="post-title" contenteditable="true" spellcheck="true" aria-label="<?php esc_attr_e( 'Post title' ); ?>" tabindex="0"><?php echo esc_html( $post_title ); ?></h2>

				<div class="media-list-container">
					<div class="media-list-inner-container">
						<h2 class="screen-reader-text"><?php _e( 'Suggested media' ); ?></h2>
						<ul class="media-list"></ul>
					</div>
				</div>

				<?php
				wp_editor( $post_content, 'pressthis', array(
					'drag_drop_upload' => true,
					'editor_height'    => 600,
					'media_buttons'    => false,
					'textarea_name'    => 'post_content',
					'teeny'            => false,
					'tinymce'          => array(
						'resize'                => true,
						'wordpress_adv_hidden'  => true,
						'add_unload_trigger'    => false,
						'statusbar'             => false,
						'autoresize_min_height' => 600,
						'wp_autoresize_on'      => true,
						'plugins'               => 'lists,media,paste,tabfocus,fullscreen,wordpress,wpautoresize,wpeditimage,wpgallery,wplink,wpview',
						'toolbar1'              => 'bold,italic,bullist,numlist,blockquote,link,unlink',
						'toolbar2'              => 'undo,redo',
					),
					'quicktags' => true,
				) );
				?>
			</div>
		</div>

		<div class="options-panel-back is-hidden" tabindex="-1"></div>
		<div class="options-panel is-off-screen is-hidden" tabindex="-1">
			<div class="post-options">

				<?php if ( $supports_formats ) : ?>
					<button type="button" class="button-reset post-option">
						<span class="dashicons dashicons-admin-post"></span>
						<span class="post-option-title"><?php _ex( 'Format', 'post format' ); ?></span>
						<span class="post-option-contents" id="post-option-post-format"><?php echo esc_html( get_post_format_string( $post_format ) ); ?></span>
						<span class="dashicons post-option-forward"></span>
					</button>
				<?php endif; ?>

				<button type="button" class="button-reset post-option">
					<span class="dashicons dashicons-category"></span>
					<span class="post-option-title"><?php _e( 'Categories' ); ?></span>
					<span class="dashicons post-option-forward"></span>
				</button>

				<button type="button" class="button-reset post-option">
					<span class="dashicons dashicons-tag"></span>
					<span class="post-option-title"><?php _e( 'Tags' ); ?></span>
					<span class="dashicons post-option-forward"></span>
				</button>
			</div>

			<?php if ( $supports_formats ) : ?>
				<div class="setting-modal is-off-screen is-hidden">
					<button type="button" class="button-reset modal-close">
						<span class="dashicons post-option-back"></span>
						<span class="setting-title" aria-hidden="true"><?php _ex( 'Format', 'post format' ); ?></span>
						<span class="screen-reader-text"><?php _e( 'Back to post options' ) ?></span>
					</button>
					<?php $this->post_formats_html( $post ); ?>
				</div>
			<?php endif; ?>

			<div class="setting-modal is-off-screen is-hidden">
				<button type="button" class="button-reset modal-close">
					<span class="dashicons post-option-back"></span>
					<span class="setting-title" aria-hidden="true"><?php _e( 'Categories' ); ?></span>
					<span class="screen-reader-text"><?php _e( 'Back to post options' ) ?></span>
				</button>
				<?php $this->categories_html( $post ); ?>
			</div>

			<div class="setting-modal tags is-off-screen is-hidden">
				<button type="button" class="button-reset modal-close">
					<span class="dashicons post-option-back"></span>
					<span class="setting-title" aria-hidden="true"><?php _e( 'Tags' ); ?></span>
					<span class="screen-reader-text"><?php _e( 'Back to post options' ) ?></span>
				</button>
				<?php $this->tags_html( $post ); ?>
			</div>
		</div><!-- .options-panel -->
	</div><!-- .wrapper -->

	<div class="press-this-actions">
		<div class="pressthis-media-buttons">
			<button type="button" class="insert-media button-subtle" data-editor="pressthis">
				<span class="dashicons dashicons-admin-media"></span>
				<span class="screen-reader-text"><?php _e( 'Add Media' ); ?></span>
			</button>
		</div>
		<div class="post-actions">
			<span class="spinner">&nbsp;</span>
			<button type="button" class="button-subtle draft-button" aria-live="polite">
				<span class="save-draft"><?php _e( 'Save Draft' ); ?></span>
				<span class="saving-draft"><?php _e( 'Saving...' ); ?></span>
			</button>
			<a href="<?php echo esc_url( get_edit_post_link( $post_ID ) ); ?>" class="edit-post-link" style="display: none;" target="_blank"><?php _e( 'Standard Editor' ); ?></a>
			<button type="button" class="button-subtle preview-button"><?php _e( 'Preview' ); ?></button>
			<button type="button" class="button-primary publish-button"><?php echo ( current_user_can( 'publish_posts' ) ) ? __( 'Publish' ) : __( 'Submit for Review' ); ?></button>
		</div>
	</div>
	</form>

	<?php
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_footer' );
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts' );
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_footer-press-this.php' );
	do_action( 'admin_footer-nominate-this.php' );
	?>
</body>
</html>
<?php
		die();
	}

}

if ( empty( $GLOBALS['pf_nom_this'] ) ) {
	$GLOBALS['pf_nom_this'] = new Nominate_This;
}
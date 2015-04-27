<?php
//Press This generation code from wp-admin\tools.php
if ( current_user_can('edit_posts') ) : ?>
<div class="card pressthis nominatethis">
	<h3><?php _e('Nominate This') ?></h3>
	<p><?php _e( 'Nominate This is a little tool that lets you grab bits of the web and create new posts with ease.' );?></p>
	<p><?php _e( 'Use Nominate This to clip text, images and videos from any web page. Then edit and add more straight from Nominate This before you save or publish it in a post on your site.' ); ?></p>


	<form>
		<h3><?php _e( 'Install Nominate This' ); ?></h3>
		<h4><?php _e( 'Bookmarklet' ); ?></h4>
		<p><?php _e( 'Drag the bookmarklet below to your bookmarks bar. Then, when you&#8217;re on a page you want to share, simply &#8220;press&#8221; it.' ); ?></p>

		<p class="pressthis-bookmarklet-wrapper">
			<a class="pressthis-bookmarklet" onclick="return false;" href="<?php echo htmlspecialchars( pf_get_nom_this_shortcut_link() ); ?>"><span><?php _e( 'Nominate This' ); ?></span></a>
			<button type="button" class="button button-secondary pressthis-js-toggle js-show-pressthis-code-wrap" aria-expanded="false" aria-controls="pressthis-code-wrap">
				<span class="dashicons dashicons-clipboard"></span>
				<span class="screen-reader-text"><?php _e( 'Copy &#8220;Nominate This&#8221; bookmarklet code' ) ?></span>
			</button>
		</p>

		<div class="hidden js-pressthis-code-wrap clear" id="pressthis-code-wrap">
			<p id="pressthis-code-desc">
				<?php _e( 'If you can&#8217;t drag the bookmarklet to your bookmarks, copy the following code and create a new bookmark. Paste the code into the new bookmark&#8217;s URL field.' ) ?>
			</p>
			<p>
				<textarea class="js-pressthis-code" rows="5" cols="120" readonly="readonly" aria-labelledby="pressthis-code-desc"><?php echo htmlspecialchars( pf_get_nom_this_shortcut_link() ); ?></textarea>
			</p>
		</div>

		<h4><?php _e( 'Direct link (best for mobile)' ); ?></h4>
		<p><?php _e( 'Follow the link to open Nominate This. Then add it to your device&#8217;s bookmarks or home screen.' ); ?></p>

		<p>
			<a class="button button-secondary" href="<?php echo htmlspecialchars( plugin_dir_url( __FILE__ ) . 'nominate-this.php' ); ?>"><?php _e( 'Open Nominate This' ) ?></a>
		</p>
		<script>
			jQuery( document ).ready( function( $ ) {
				var $showPressThisWrap = $( '.js-show-pressthis-code-wrap' );
				var $pressthisCode = $( '.js-pressthis-code' );
				$showPressThisWrap.on( 'click', function( event ) {
					var $this = $( this );
					$this.parent().next( '.js-pressthis-code-wrap' ).slideToggle( 200 );
					$this.attr( 'aria-expanded', $this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
				});
				// Select Press This code when focusing (tabbing) or clicking the textarea.
				$pressthisCode.on( 'click focus', function() {
					var self = this;
					setTimeout( function() { self.select(); }, 50 );
				});
			});
		</script>
	</form>
</div>
<?php
endif;

//Press This get_shortcut_link() from wp-includes/link-template.php
//http://wpseek.com/get_shortcut_link/

function pf_get_nom_this_shortcut_link() {
	global $is_IE, $wp_version;
	include_once( plugin_dir_path( __FILE__ ) . 'class-pf-nom-this.php' );
	$bookmarklet_version = $GLOBALS['pf_nom_this']->version;
	$link = '';
	if ( $is_IE ) {
		/**
		 * Return the old/shorter bookmarklet code for MSIE 8 and lower,
		 * since they only support a max length of ~2000 characters for
		 * bookmark[let] URLs, which is way to small for our smarter one.
		 * Do update the version number so users do not get the "upgrade your
		 * bookmarklet" notice when using PT in those browsers.
		 */
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if ( ! empty( $ua ) && preg_match( '/\bMSIE (\d)/', $ua, $matches ) && (int) $matches[1] <= 8 ) {
			$url = wp_json_encode( plugin_dir_url( __FILE__ ) . 'nominate-this.php' );
			$link = 'javascript:var d=document,w=window,e=w.getSelection,k=d.getSelection,x=d.selection,' .
				's=(e?e():(k)?k():(x?x.createRange().text:0)),f=' . $url . ',l=d.location,e=encodeURIComponent,' .
				'u=f+"?u="+e(l.href)+"&t="+e(d.title)+"&s="+e(s)+"&v=' . $bookmarklet_version . '";' .
				'a=function(){if(!w.open(u,"t","toolbar=0,resizable=1,scrollbars=1,status=1,width=600,height=700"))l.href=u;};' .
				'if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else a();void(0)';
		}
	}
	if ( empty( $link ) ) {
		$src = @file_get_contents( ABSPATH . 'wp-admin/js/bookmarklet.min.js' );
		if ( $src ) {
			$url = wp_json_encode( plugin_dir_url( __FILE__ ) . 'nominate-this.php' . '?v=' . $bookmarklet_version );
			$link = 'javascript:' . str_replace( 'window.pt_url', $url, $src );
		}
	}
	$link = str_replace( array( "\r", "\n", "\t" ),  '', $link );
	/**
	 * Filter the Press This bookmarklet link.
	 *
	 * @since 2.6.0
	 *
	 * @param string $link The Press This bookmarklet link.
	 */
	return apply_filters( 'pf_shortcut_link', $link );
}


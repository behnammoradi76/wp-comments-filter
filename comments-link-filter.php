<?php
/**
 * Comments filter plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Comments link filter
 * Version:           1.0.0
 * Plugin URI:        https://github.com/behnammoradi76
 * Description:       This plugin filter links in WordPress comments, remove the link, empty the link address, star the link title, add the no-follow rel to the link.
 * Author:            Behnam Moradi
 * Author URI:        https://behnammoradi.com
 * License:           GPL v3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 */

defined( 'ABSPATH' ) || exit;

/**
 * CommentsFilter class.
 *
 * @class Main class of the plugin.
 */
final class CommentsFilter {

	/**
	 * The single instance of the class.
	 *
	 * @var CommentsFilter
	 */
	protected static $instance = null;

	/**
	 * Retrieve main CommentsFilter instance.
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @return CommentsFilter
	 * @see comments_filter()
	 */
	public static function get() {
		if ( is_null( self::$instance ) && ! ( self::$instance instanceof CommentsFilter ) ) {
			self::$instance = new CommentsFilter();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the plugin.
	 */
	private function setup() {
		// Define plugin constants.
		$this->define_constants();

		// Hooks required filters.
		$this->hooks();
	}

	/**
	 * Define the plugin constants.
	 */
	private function define_constants() {
		define( 'CLF_FILE', __FILE__ );
		define( 'CLF_PATH', plugin_dir_path( CLF_FILE ) . '/' );
		define( 'CLF_URL', plugins_url( '', CLF_FILE ) . '/' );
	}

	/**
	 * Hooks actions and filter to WordPress.
	 */
	private function hooks() {
//		add_filter( 'comment_text', [ $this, 'remove_a_tag' ] );
		add_filter( 'comment_text', [ $this, 'empty_a_tag' ] );
		add_filter( 'comment_text', [ $this, 'add_nofollow' ] );
	}

	/**
	 * Remove a tag from comments.
	 */
	public function remove_a_tag( $comment ): string {
		//note: PHP 7.4 required (at least).
		$allowed_tags = [ 'p', 'b', 'hr', 'strong' ];

		return strip_tags( $comment, $allowed_tags );
	}

	/**
	 * Change a tag text to *** and href empty.
	 */
	public function empty_a_tag( $comment ) {
		$comment = $this->empty_href_tag( $comment );

		return preg_replace( '/(<a.*?>).*?(<\/a>)/', '$1' . '***' . '$2', $comment );
	}

	/**
	 * Remove href content from a tag.
	 */
	private function empty_href_tag( string $comment ): string {
		return preg_replace( '/(?<=href\=")[^]]+?(?=")/', '', $comment );
	}

	/**
	 * Add rel nofollow to a tag.
	 */
	public function add_nofollow( $comment ): string {
		$result = preg_replace( '@rel="(.*)"@U', '', $comment );

		return preg_replace( '@<a(.*)>@U', '<a$1 rel="nofollow">', $result );
	}
}

/**
 * Returns the main instance of CommentsFilter to prevent the need to use globals.
 *
 * @return CommentsFilter
 */
function comments_filter(): ?CommentsFilter {
	return CommentsFilter::get();
}

// Start it.
comments_filter();
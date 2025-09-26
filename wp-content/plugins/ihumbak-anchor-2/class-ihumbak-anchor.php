<?php

class Ihumbak_Anchor {

	private $meta_key = '_ihumbak_anchor_links';

	public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'ihumbak_anchor_enqueue_scripts'));
		add_action( 'save_post', array( $this, 'save_anchor_links_to_meta' ), 10, 3 );
		add_shortcode( 'ihumbak_anchor', array( $this, 'ihumbak_anchor_shortcode' ) );
	}

	public function ihumbak_anchor_enqueue_scripts() {
		wp_enqueue_style( 'ihumbak-anchor2-style', plugin_dir_url( __FILE__ ) . 'style.css' );
	}

	/**
	 * Save anchor links to post meta when the post is saved
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 * @param bool    $update
	 */
	public function save_anchor_links_to_meta( $post_id, $post, $update ) {
		// Check if this is an autosave or a revision
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		// Only process for posts and pages
		if ( ! in_array( $post->post_type, array( 'post', 'page' ), true ) ) {
			return;
		}
		// Find anchor links in the post content
		$anchor_links = $this->find_anchor_links( $post->post_content );
		// Save to post meta
		if ( ! empty( $anchor_links ) ) {
			update_post_meta( $post_id, $this->meta_key, $anchor_links );
		} else {
			delete_post_meta( $post_id, $this->meta_key );
		}
	}

	/**
	 * Find all headings (having ID attribute) in the content and return array of anchor links.
	 * Name, ID, and level
	 *
	 * @param $content
	 * @return array
	 */
	public function find_anchor_links( $content ) {
		$anchor_links = array();
		$pattern = '/<h([1-6])[^>]*id=["\']([^"\']+)["\'][^>]*>(.*?)<\/h\1>/i';
		if ( preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$level = intval( $match[1] );
				$id = sanitize_title( $match[2] );
				$name = wp_strip_all_tags( $match[3] );
				$anchor_links[] = array(
					'level' => $level,
					'id'    => $id,
					'name'  => $name,
				);
			}
		}
		return $anchor_links;
	}

	/**
	 * Shortcode to display anchor links
	 *
	 * Usage: [ihumbak_anchor]
	 *
	 * @param $atts
	 * @return string
	 */
	public function ihumbak_anchor_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'post_id' => get_the_ID(),
		), $atts, 'ihumbak_anchor' );

		$post_id = intval( $atts['post_id'] );
		if ( ! $post_id ) {
			return '';
		}

		$anchor_links = get_post_meta( $post_id, $this->meta_key, true );
		if ( empty( $anchor_links ) || ! is_array( $anchor_links ) ) {
			return '';
		}
		$output = '';
		$output .= '<div class="ihumbak-anchor-menu-wrapper">';
		$output .= '<strong>Spis treści</strong>';
		$output .= '<div class="ihumbak-anchor-menu-hide">';
		$output .= 'Zwiń <i class="fa fa-chevron-up"></i>';
		$output .= '</div>';
		$output .= '<div class="ihumbak-anchor-menu-show ihumbak-anchor-d-none">Rozwiń <i class="fa fa-chevron-down"></i></div>';
		$output .= '<ul class="ihumbak-anchor-menu">';
		foreach ( $anchor_links as $link ) {
			$level = intval( $link['level'] );
			$id = esc_attr( $link['id'] );
			$name = esc_html( $link['name'] );
			$output .= sprintf(
				'<li class="ihumbak-anchor-level-%d"><a class="ihumbak-anchor-link" href="#%s">%s</a></li>',
				$level,
				$id,
				$name
			);
		}
		$output .= '</ul>';
		$output .= '</div>';

		return $output;
	}
}

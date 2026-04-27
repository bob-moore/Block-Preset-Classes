<?php
/**
 * WordPress mock functions and classes.
 *
 * @package Bmd\Tests
 */

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		return (string) $url;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	function trailingslashit( $string ) {
		return untrailingslashit( $string ) . '/';
	}
}

if ( ! function_exists( 'untrailingslashit' ) ) {
	function untrailingslashit( $value ) {
		return rtrim( (string) $value, '/\\' );
	}
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
	function plugin_dir_url( $file = '' ) {
		return 'https://example.test/' . basename( dirname( (string) $file ) ) . '/';
	}
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
	function plugin_dir_path( $file = '' ) {
		return trailingslashit( dirname( (string) $file ) );
	}
}

if ( ! function_exists( 'wp_normalize_path' ) ) {
	function wp_normalize_path( $path ) {
		return str_replace( '\\', '/', (string) $path );
	}
}

if ( ! function_exists( 'sanitize_title' ) ) {
	function sanitize_title( $title ) {
		$title = strtolower( (string) $title );
		$title = preg_replace( '/[^a-z0-9]+/', '-', $title );

		return trim( (string) $title, '-' );
	}
}

if ( ! class_exists( 'WP_Block_Type_Registry' ) ) {
	class WP_Block_Type_Registry {
		private static ?WP_Block_Type_Registry $instance = null;

		public static function get_instance(): WP_Block_Type_Registry {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

if ( ! class_exists( 'WP_REST_Request' ) ) {
	class WP_REST_Request {}
}

if ( ! class_exists( 'WP_REST_Response' ) ) {
	class WP_REST_Response {
		/**
		 * @var mixed
		 */
		private $data;

		private int $status;

		/**
		 * @param mixed $data   Response data.
		 * @param int   $status Response status.
		 */
		public function __construct( $data = null, int $status = 200 ) {
			$this->data   = $data;
			$this->status = $status;
		}

		/**
		 * @return mixed
		 */
		public function get_data() {
			return $this->data;
		}

		public function get_status(): int {
			return $this->status;
		}
	}
}

if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
	class WP_HTML_Tag_Processor {
		private string $html;

		public function __construct( string $html ) {
			$this->html = $html;
		}

		/**
		 * @param array<string, string> $query Query arguments.
		 */
		public function next_tag( array $query = [] ): bool {
			$tag = strtolower( $query['tag_name'] ?? '' );

			if ( '' === $tag ) {
				return false;
			}

			return (bool) preg_match( '/<' . preg_quote( $tag, '/' ) . '[\s>\/]/i', $this->html );
		}
	}
}

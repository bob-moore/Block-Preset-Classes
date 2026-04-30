<?php
/**
 * WordPress mock functions.
 */

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', sys_get_temp_dir() . '/wp-content' );
}

if ( ! function_exists( 'plugin_basename' ) ) {
	function plugin_basename( $file = '' ) {
		if ( isset( $GLOBALS['wp_framework_plugin_basename'] ) ) {
			return (string) $GLOBALS['wp_framework_plugin_basename'];
		}

		return basename( (string) $file );
	}
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
	function plugin_dir_url( $file = '' ) {
		if ( isset( $GLOBALS['wp_framework_plugin_dir_url'] ) ) {
			return (string) $GLOBALS['wp_framework_plugin_dir_url'];
		}

		$dir = trailingslashit( dirname( (string) $file ) );

		return 'https://example.test/' . ltrim( str_replace( DIRECTORY_SEPARATOR, '/', $dir ), '/' );
	}
}

if ( ! function_exists( 'is_admin' ) ) {
	function is_admin() {
		return (bool) ( $GLOBALS['wp_framework_is_admin'] ?? false );
	}
}

if ( ! function_exists( 'get_current_screen' ) ) {
	function get_current_screen() {
		return $GLOBALS['wp_framework_current_screen'] ?? null;
	}
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
	/**
	 * Get the filesystem directory path (with trailing slash) for the plugin __FILE__ passed in.
	 *
	 * @since 2.8.0
	 *
	 * @param string $file The filename of the plugin (__FILE__).
	 *
	 * @return string the filesystem path of the directory that contains the plugin.
	 */
	function plugin_dir_path( $file ) {
		return trailingslashit( dirname( $file ) );
	}
}

if ( ! function_exists( 'get_the_title' ) ) {
	function get_the_title() {
		return 'Sample Page';
	}
}

if ( ! function_exists( 'the_title' ) ) {
	function the_title() {
		echo get_the_title();
	}
}

if ( ! function_exists( 'get_theme_file_path' ) ) {
	function get_theme_file_path( $file = '' ) {
		return ! empty( $file ) ? WP_CONTENT_DIR . '/' . $file : WP_CONTENT_DIR;
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	/**
	 * Sanitize a URL for database/storage contexts.
	 *
	 * Test bootstrap provides a pass-through implementation because WordPress is not loaded.
	 *
	 * @param string $url URL to sanitize.
	 *
	 * @return string
	 */
	function esc_url_raw( $url ) {
		return $url;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	/**
	 * Escape HTML text.
	 *
	 * Test bootstrap provides a lightweight implementation because WordPress is not loaded.
	 *
	 * @param string $text Text to escape.
	 *
	 * @return string
	 */
	function esc_html( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	/**
	 * Sanitize a key.
	 *
	 * @param string $key Key to sanitize.
	 *
	 * @return string
	 */
	function sanitize_key( $key ) {
		return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) );
	}
}

if ( ! function_exists( 'wp_normalize_path' ) ) {
	/**
	 * Normalize a filesystem path.
	 *
	 * @param string $path Path to normalize.
	 *
	 * @return string
	 */
	function wp_normalize_path( $path ) {
		return str_replace( '\\', '/', (string) $path );
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	/**
	 * Appends a trailing slash.
	 *
	 * Will remove trailing forward and backslashes if it exists already before adding
	 * a trailing forward slash. This prevents double slashing a string or path.
	 *
	 * The primary use of this is for paths and thus should be used for paths. It is
	 * not restricted to paths and offers no specific path support.
	 *
	 * @since 1.2.0
	 *
	 * @param string $string What to add the trailing slash to.
	 *
	 * @return string String with trailing slash added.
	 */
	function trailingslashit( $string ) {
		return untrailingslashit( $string ) . '/';
	}
}

if ( ! function_exists( 'untrailingslashit' ) ) {
	/**
	 * Removes trailing forward slashes and backslashes if they exist.
	 *
	 * The primary use of this is for paths and thus should be used for paths. It is
	 * not restricted to paths and offers no specific path support.
	 *
	 * @since 2.2.0
	 *
	 * @param string $value What to remove the trailing slashes from.
	 *
	 * @return string String without the trailing slashes.
	 */
	function untrailingslashit( $value ) {
		return rtrim( $value, '/\\' );
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	/**
	 * Check whether variable is a WordPress Error.
	 *
	 * Returns true if $thing is an object of the WP_Error class.
	 *
	 * @since 2.1.0
	 *
	 * @param mixed $thing Check if unknown variable is a WP_Error object.
	 *
	 * @return bool True, if WP_Error. False, if not WP_Error.
	 */
	function is_wp_error( $thing ) {
		return ( $thing instanceof \WP_Error );
	}
}

if ( ! function_exists( 'wp_parse_args' ) ) {
	/**
	 * Merge user defined arguments into defaults array.
	 *
	 * @param string|array $args Value to merge with $defaults.
	 * @param array        $defaults Optional. Array that serves as the defaults. Default empty.
	 * @return array Merged user defined values with defaults.
	 */
	function wp_parse_args( $args, $defaults = [] ) {
		if ( is_object( $args ) ) {
			$r = get_object_vars( $args );
		} elseif ( is_array( $args ) ) {
			$r = &$args;
		} else {
			return $defaults;
		}

		if ( is_array( $defaults ) && $defaults ) {
			return array_merge( $defaults, $r );
		}

		return $r;
	}
}

if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
	/**
	 * Minimal tag processor compatible with the plugin's test needs.
	 */
	class WP_HTML_Tag_Processor {
		/**
		 * Original HTML.
		 *
		 * @var string
		 */
		private string $html;

		/**
		 * Matched opening tag.
		 *
		 * @var string
		 */
		private string $matched_tag = '';

		/**
		 * Matched attributes.
		 *
		 * @var array<string, string>
		 */
		private array $attributes = [];

		/**
		 * Constructor.
		 *
		 * @param string $html HTML to process.
		 */
		public function __construct( string $html ) {
			$this->html = $html;
		}

		/**
		 * Move to the first tag matching the requested class name.
		 *
		 * @param array<string, string> $query Query arguments.
		 *
		 * @return bool
		 */
		public function next_tag( array $query = [] ): bool {
			if ( ! preg_match( '/<([a-z0-9-]+)\s+([^>]*)>/i', $this->html, $matches ) ) {
				return false;
			}

			$this->matched_tag = $matches[0];
			$this->attributes  = $this->parse_attributes( $matches[2] );

			if ( isset( $query['class_name'] ) ) {
				$classes = preg_split( '/\s+/', $this->attributes['class'] ?? '' );

				return in_array( $query['class_name'], $classes, true );
			}

			return true;
		}

		/**
		 * Get an attribute from the current tag.
		 *
		 * @param string $name Attribute name.
		 *
		 * @return string|null
		 */
		public function get_attribute( string $name ): ?string {
			return $this->attributes[ $name ] ?? null;
		}

		/**
		 * Set an attribute on the current tag.
		 *
		 * @param string $name  Attribute name.
		 * @param string $value Attribute value.
		 *
		 * @return void
		 */
		public function set_attribute( string $name, string $value ): void {
			$this->attributes[ $name ] = $value;
		}

		/**
		 * Return updated HTML.
		 *
		 * @return string
		 */
		public function get_updated_html(): string {
			$updated_tag = '<div';

			foreach ( $this->attributes as $name => $value ) {
				$updated_tag .= sprintf(
					' %s="%s"',
					$name,
					htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' )
				);
			}

			$updated_tag .= '>';

			return preg_replace( '/' . preg_quote( $this->matched_tag, '/' ) . '/', $updated_tag, $this->html, 1 ) ?? $this->html;
		}

		/**
		 * Parse attributes from an opening tag.
		 *
		 * @param string $attribute_html Attribute HTML.
		 *
		 * @return array<string, string>
		 */
		private function parse_attributes( string $attribute_html ): array {
			$attributes = [];

			preg_match_all( '/([a-z0-9_-]+)="([^"]*)"/i', $attribute_html, $matches, PREG_SET_ORDER );

			foreach ( $matches as $match ) {
				$attributes[ $match[1] ] = html_entity_decode( $match[2], ENT_QUOTES, 'UTF-8' );
			}

			return $attributes;
		}
	}
}

if ( ! class_exists( 'WP_Error' ) ) {
	/**
	 * WordPress Error class for testing.
	 *
	 * Minimal implementation for unit test compatibility.
	 */
	class WP_Error {
		/**
		 * Error code.
		 *
		 * @var string
		 */
		private string $code = '';

		/**
		 * Error message.
		 *
		 * @var string
		 */
		private string $message = '';

		/**
		 * Error data.
		 *
		 * @var mixed
		 */
		private mixed $data = '';

		/**
		 * Constructor.
		 *
		 * @param string $code Error code.
		 * @param string $message Error message.
		 * @param mixed  $data Optional. Error data.
		 */
		public function __construct( $code = '', $message = '', $data = '' ) {
			$this->code    = $code;
			$this->message = $message;
			$this->data    = $data;
		}

		/**
		 * Get error code.
		 *
		 * @return string
		 */
		public function get_error_code() {
			return $this->code;
		}

		/**
		 * Get error message.
		 *
		 * @return string
		 */
		public function get_error_message() {
			return $this->message;
		}

		/**
		 * Get error data.
		 *
		 * @return mixed
		 */
		public function get_error_data() {
			return $this->data;
		}
	}
}

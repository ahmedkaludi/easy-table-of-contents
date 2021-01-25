<?php

namespace Easy_Plugins\Table_Of_Contents;

use WP_Error;

/**
 * Class Debug
 *
 * @package Easy_Plugins\Table_Of_Contents
 */
final class Debug extends WP_Error {

	/**
	 * @since 2.0.13
	 * @var bool
	 */
	protected $display = false;

	/**
	 * @since 2.0.13
	 * @var bool
	 */
	protected $enabled = false;

	/**
	 * Debug constructor.
	 *
	 * @since 2.0.13
	 *
	 * @param string $code
	 * @param string $message
	 * @param string $data
	 */
	public function __construct( $code = '', $message = '', $data = '' ) {

		$this->display = defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;
		$this->enabled = apply_filters(
			'Easy_Plugins/Table_Of_Contents/Debug/Enabled',
			( defined( 'WP_DEBUG' ) && WP_DEBUG ) && current_user_can( 'manage_options' )
		);

		parent::__construct( $code, $message, $data );
	}

	/**
	 * @since 2.0.13
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function appendTo( $content = '' ) {

		return $content . $this;
	}

	/**
	 * @since 2.0.13
	 *
	 * @return string
	 */
	public function dump() {

		$dump = array();

		foreach ( (array) $this->errors as $code => $messages ) {

			$data = $this->get_error_data( $code );
			$data = is_string( $data ) ? $data : '<code>' . var_export( $data, true ) . '</code>';
			$data = "\t\t<li class=\"ez-toc-debug-message-data\">{$data}</li>" . PHP_EOL;

			array_push(
				$dump,
				PHP_EOL . "\t<ul class=\"ez-toc-debug-message-{$code}\">" . PHP_EOL . "\t\t<li class=\"ez-toc-debug-message\">" . implode( '</li>' . PHP_EOL . '<li>' . PHP_EOL, $messages ) . '</li>' . PHP_EOL . "{$data}\t</ul>" . PHP_EOL
			);
		}

		return '<div class="ez-toc-debug-message">' . implode( '</div>' . PHP_EOL . '<div class="ez-toc-debug-message">', $dump ) . '</div>' . PHP_EOL;
	}

	/**
	 * @since 2.0.13
	 *
	 * @return string
	 */
	public function __toString() {

		if ( ! $this->enabled ) {

			return '';
		}

		if ( ! $this->has_errors() ) {

			return '';
		}

		$intro = sprintf(
			'You see the following because <a href="%1$s"><code>WP_DEBUG</code></a> and <a href="%1$s"><code>WP_DEBUG_DISPLAY</code></a> are enabled on this site. Please disabled these to prevent the display of these developers\' debug messages.',
			'https://codex.wordpress.org/WP_DEBUG'
		);

		$intro = PHP_EOL . "<p>{$intro}</p>" .PHP_EOL;

		$display = $this->display ? 'block' : 'none';
		$dump    = $this->dump();

		return PHP_EOL . "<div class='ez-toc-debug-messages' style='display: block;'>{$intro}{$dump}</div>" . PHP_EOL;
	}
}

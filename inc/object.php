<?php

class Youtubedj_Object {
	private $filter_tag = '';

	public function __construct() {
		$this->filter_name = strtolower( get_class( $this ) );
	}

	public function html() {
		$method = array( $this, 'get_html' );
		$args   = func_get_args();
		$html   = call_user_func_array( $method, $args );

		if ( $this->filter_tag ) {
			return apply_filters( $this->filter_tag, $html, $args );
		}
		else {
			return $html;
		}
	}

	protected function get_html() {
		return '';
	}

}
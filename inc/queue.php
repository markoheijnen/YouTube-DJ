<?php

class Youtubedj_Queue {
	function __construct() {
		
	}

	function html( $title ) {
		$html  = '<div class="que gear">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<div class="queue">';
		$html .= '<ul class="videolist"></ul>';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}
}
<?php

class Youtubedj_Queue {
	function __construct() {
		
	}

	function html( $id, $title ) {
		$html  = '<div id="' . $id . '" class="queue gear">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<ul class="queuelist">';
		$html .= '</ul>';

		$html .= '</div>';

		return $html;
	}
}
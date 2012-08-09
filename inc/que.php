<?php

class Youtubedj_Que {
	function __construct() {
		
	}

	function html( $title, $deck1, $deck2, $deck1_title = 'A', $deck2_title = 'B' ) {
		$html  = '<div class="que gear">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<div class="queue">';
		$html .= '<ul class="videolist"></ul>';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}
}
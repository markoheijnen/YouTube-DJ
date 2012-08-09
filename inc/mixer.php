<?php

class Youtubedj_Mixer {
	function __construct() {
		
	}

	function html( $title, $deck1, $deck2, $deck1_title = 'A', $deck2_title = 'B' ) {
		$html  = '<div class="mixer gear">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<div class="faders">';
		$html .= '<a>' . $deck1_title . '</a>';
		$html .= '<div id="crossfader"></div>';
		$html .= '<a>' . $deck2_title . '</a>';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}
}
<?php

class Youtubedj_Mixer {

	public function __construct() {
		
	}

	public function html( $title, $deck1, $deck2, $deck1_title = 'A', $deck2_title = 'B' ) {
		$html  = '<div class="mixer gear" deck1="' . $deck1 . '" deck2="' . $deck2 . '">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<div class="faders">';
		$html .= '<a class="mixer-button mixer-button-a">' . $deck1_title . '</a>';
		$html .= '<div class="crossfader"></div>';
		$html .= '<a class="mixer-button mixer-button-b">' . $deck2_title . '</a>';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

}

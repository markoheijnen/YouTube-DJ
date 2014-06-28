<?php

class Youtubedj_Mixer extends Youtubedj_Object {

	protected function get_html( $title, $deck1, $deck2, $deck1_title = 'A', $deck2_title = 'B' ) {
		$html  = '<div class="mixer gear" data-deck1="' . $deck1 . '" data-deck2="' . $deck2 . '">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<div class="faders">';
		$html .= '<a class="mixer-button mixer-button-a">' . $deck1_title . '</a>';
		$html .= '<a class="mixer-button mixer-button-b">' . $deck2_title . '</a>';
		$html .= '<div class="crossfader"></div>';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

}

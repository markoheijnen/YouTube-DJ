<?php

class Youtubedj_Queue {

	public function __construct() {
		
	}

	public function html( $id, $title, $decks, $queuelist ) {
		$html  = '<div id="' . $id . '" class="queue gear" decks="' . implode( ',', $decks ) . '">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<ul class="queuelist">';
		$html .= '</ul>';

		$html .= '</div>';

		return $html;
	}

}

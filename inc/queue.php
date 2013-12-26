<?php

class Youtubedj_Queue {

	public function __construct() {
		
	}

	public function html( $id, $title, $decks, $queuelist = array() ) {
		$html  = '<div id="' . $id . '" class="queue gear" decks="' . implode( ',', $decks ) . '">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<ul class="queuelist">';

		foreach( $queuelist as $item ) {
			$html .= '<li songid="' . $item['id'] . '" class="song"><h5>' . $item['title'] . '</h5></li>';
		}

		$html .= '</ul>';

		$html .= '</div>';

		return $html;
	}

}

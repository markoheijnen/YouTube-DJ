<?php

class Youtubedj_Queue extends Youtubedj_Object {

	protected function get_html( $id, $title, $decks, $queuelist = array() ) {
		$html  = '<div id="' . $id . '" class="queue gear" data-decks="' . implode( ',', $decks ) . '">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<ul class="queuelist videolist">';

		foreach( $queuelist as $item ) {
			$html .= '<li data-songid="' . $item['id'] . '" class="song">';
			$html .= '<h5>' . $item['title'] . '</h5>';
			$html .= ' <span class="song-buttons"><a href="#" class="song-delete dashicons dashicons-dismiss"></a></span>';
			$html .= '</li>';
		}

		$html .= '</ul>';

		$html .= '</div>';

		return $html;
	}

}

<?php

class Youtubedj_Deck {

	public function __construct() {
		
	}

	public function html( $id, $title, $movie_code = false, $queue = false ) {
		$html  = '<div id="' . $id . '" class="deck gear">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<div class="player-holder">';
		$html .= '<div id="' . $id . '-player" class="player"';

		if( $movie_code ) {
			$html .= ' movie="' . $movie_code . '"';
		}

		if( $queue ) {
			$html .= ' queue="' . $queue . '"';
		}

		$html .= '></div></div>';

		$html .= '<div class="btns">';
		$html .= '<a class="play">' . __( 'Play', 'youtube-dj' ) . '</a>';
		$html .= '<a class="pause">' . __( 'Pause', 'youtube-dj' ) . '</a>';
		$html .= '<a class="stop">' . __( 'Stop', 'youtube-dj' ) . '</a>';
		$html .= '</div>';
	
		$html .= '<div class="fader"><div class="gain"></div></div>';

		$html .= '</div>';

		return $html;
	}

}

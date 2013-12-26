<?php

class Youtubedj_Search {

	public function __construct() {
		add_action( 'wp_ajax_youtubedj_search', array( $this, 'search' ) );
		add_action( 'wp_ajax_nopriv_youtubedj_search', array( $this, 'search' ) );
	}

	public function html( $title, $queue, $decks, $value = '' ) {
		$html  = '<div class="search gear" queue="' . $queue . '" decks="' . implode( ',', $decks ) . '">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<form action="" method="post">';
		$html .= '<input name="searchTerm" class="searchTerm" type="text" value="' . $value . '" placeholder="' . __( 'Your song', 'youtube-dj' ) . '" />';
		$html .= '<input type="submit" value="' . _x( 'Search', 'Search button', 'youtube-dj' ) . '" />';
		$html .= '</form>';

		$html .= '<div class="searchResults"></div>';
		$html .= '<div class="searchResultsNavigation" style="display:none">';
		$html .= '<a class="SearchResultsBack">' . __( 'Previous', 'youtube-dj' ) . '</a>';
		$html .= '<a class="SearchResultsNext">' . __( 'Next', 'youtube-dj' ) . '</a>';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

	public function search() {
		header('Content-type: application/json');

		$max_results = 5;
		$start_index = absint( $_REQUEST['offset'] );

		$response = Youtubedj_API::search( $_REQUEST['searchTerm'], $max_results, $start_index );

		echo json_encode( $response );
		wp_die();
	}

}

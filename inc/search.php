<?php

class Youtubedj_Search {
	function __construct() {
		add_action( 'wp_ajax_youtubedj_search', array( &$this, 'search' ) );
		add_action( 'wp_ajax_nopriv_youtubedj_search', array( &$this, 'my_action_callback' ) );
	}

	function html( $title, $decks ) {
		$html  = '<div class="search gear">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<form action="" method="post">';
		$html .= '<input name="searchTerm" class="searchTerm" type="text" value="Your song" />';
		$html .= '<input type="submit" value="Search" />';
		$html .= '</form>';

		$html .= '<div class="searchResults"></div>';
		$html .= '<div class="searchResultsNavigation">';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

	function search() {
		header('Content-type: application/json');

		$search      = esc_attr( $_REQUEST['searchTerm'] );
		$max_results = 10;
		$start_index = absint( $_REQUEST['offset'] );

		$url = 'https://gdata.youtube.com/feeds/api/videos?q=' . $search . '&max-results=' . $max_results . '&start-index=' . $start_index . '&v=2&alt=jsonc';

		$response = wp_remote_get( $url );
		$data     = json_decode( wp_remote_retrieve_body( $response ) );

		$response = array( 'total' => 0, 'max_results' => $max_results, 'start_index' => $start_index, 'songs' => array() );

		if( ! empty( $data ) ) {
			$response['total']       = $data->data->totalItems;
			$response['start_index'] = $data->data->startIndex;
			$items                   = $data->data->items;

			foreach( $items as $item ) {
				array_push( $response['songs'], array(
					'id'        => $item->id,
					'title'     => $item->title,
					'thumbnail' => $item->thumbnail,
					'duration'  => $this->duration,
					'mobile'    => true
				) );
			}
		}

		echo json_encode( $response );
		wp_die();
	}
}
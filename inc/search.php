<?php

class Youtubedj_Search {
	function __construct() {
		add_action( 'wp_ajax_youtubedj_search', array( $this, 'search' ) );
		add_action( 'wp_ajax_nopriv_youtubedj_search', array( $this, 'search' ) );
	}

	function html( $title, $queue, $decks, $value = '' ) {
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

	function search() {
		header('Content-type: application/json');

		$search      = sanitize_text_field( $_REQUEST['searchTerm'] );
		$max_results = 5;
		$start_index = absint( $_REQUEST['offset'] );

		$data = Youtubedj_API::search( $search, $max_results, $start_index );

		$response = array( 'total' => 0, 'max_results' => $max_results, 'start_index' => $start_index, 'songs' => array() );

		if( ! empty( $data ) ) {
			$response['total']       = $data->data->totalItems;
			$response['start_index'] = $data->data->startIndex;
			$items                   = $data->data->items;

			foreach( $items as $item ) {
				array_push( $response['songs'], array(
					'id'        => $item->id,
					'title'     => $item->title,
					'thumbnail' => array( 'normal' => $item->thumbnail->sqDefault, 'high' => $item->thumbnail->hqDefault ),
					'duration'  => $this->duration,
					'mobile'    => isset( $this->player->mobile )
				) );
			}
		}

		echo json_encode( $response );
		wp_die();
	}
}
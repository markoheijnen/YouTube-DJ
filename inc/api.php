<?php

class Youtubedj_API {
	//format=5 = embed only and format=1,6 is mobile only

	public static function user_playlist( $user, $max_results = 25, $start_index = 0 ) {
		$user = sanitize_text_field( $user );
		$url  = 'https://gdata.youtube.com/feeds/api/users/' . $user . '/uploads?max-results=' . $max_results . '&start-index=' . $start_index . '&format=1,5,6&v=2&alt=jsonc';

		$response = wp_remote_get( $url );
		$data     = json_decode( wp_remote_retrieve_body( $response ) );

		return self::normalize( $data );
	}

	public static function search( $search, $max_results = 25, $start_index = 0 ) {
		$search = sanitize_text_field( $search );
		$url    = 'https://gdata.youtube.com/feeds/api/videos?q=' . $search . '&max-results=' . $max_results . '&start-index=' . $start_index . '&format=1,5,6&v=2&alt=jsonc';

		$response = wp_remote_get( $url );
		$data     = json_decode( wp_remote_retrieve_body( $response ) );

		return self::normalize( $data );
	}


	private static function normalize( $data ) {
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

		return $response;
	}

}
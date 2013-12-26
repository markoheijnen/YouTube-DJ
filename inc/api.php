<?php

class Youtubedj_API {
	//format=5 = embed only and format=1,6 is mobile only

	public static function user_playlist( $user, $max_results = 25, $start_index = 0 ) {
		$url = 'https://gdata.youtube.com/feeds/api/users/' . $user . '/uploads?max-results=' . $max_results . '&start-index=' . $start_index . '&format=1,5,6&v=2&alt=jsonc';

		$response = wp_remote_get( $url );
		$data     = json_decode( wp_remote_retrieve_body( $response ) );

		return $data;
	}

	public static function search( $search, $max_results = 25, $start_index = 0 ) {
		$url = 'https://gdata.youtube.com/feeds/api/videos?q=' . $search . '&max-results=' . $max_results . '&start-index=' . $start_index . '&format=1,5,6&v=2&alt=jsonc';

		$response = wp_remote_get( $url );
		$data     = json_decode( wp_remote_retrieve_body( $response ) );

		return $data;
	}

}
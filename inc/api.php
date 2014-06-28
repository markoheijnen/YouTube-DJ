<?php

class Youtubedj_API {
	private static $max_results = 25;
	private static $start_index = 1;


	public static function set_max_results( $amount ) {
		self::$max_results = absint( $amount );
	}

	public static function set_start_index( $amount ) {
		self::$start_index = absint( $amount );
	}


	public static function user_info( $user ) {
		$user = sanitize_text_field( $user );
		$url  = 'https://gdata.youtube.com/feeds/api/users/' . $user . '?v=2';

		$data     = array();
		$response = wp_remote_get( $url );
		$body     = wp_remote_retrieve_body( $response );

		if( $body ) {
			$xml        = simplexml_load_string( $body );
			$attributes = $xml->children( 'http://search.yahoo.com/mrss/' )->thumbnail->attributes();

			$data['id']         = (string) $xml->author->children( 'http://gdata.youtube.com/schemas/2007' );
			$data['photo']      = (string) $attributes['url'];
			$data['background'] = 'http://i1.ytimg.com/u/' . $data['id'] . '/channels4_banner_hd.jpg';
		}

		return $data;
	}

	public static function user_playlist( $user ) {
		$user = sanitize_text_field( $user );
		$url  = 'https://gdata.youtube.com/feeds/api/users/' . $user . '/uploads?';

		return self::get_data( $url );
	}

	public static function playlist( $playlist ) {
		$playlist = sanitize_text_field( $playlist );
		$url      = 'https://gdata.youtube.com/feeds/api/playlists/' . $playlist . '?';

		return self::get_data( $url );
	}

	public static function search( $search ) {
		$search = sanitize_text_field( $search );
		$url    = 'https://gdata.youtube.com/feeds/api/videos?q=' . $search . '&';

		return self::get_data( $url );
	}



	private static function get_data( $url ) {
		$url .= 'max-results=' . self::$max_results . '&start-index=' . self::$start_index;
		$url .= '&format=1,5,6'; //format=5 = embed only and format=1,6 is mobile only
		$url .= '&v=2&alt=jsonc';

		if ( ! self::being_cached() ) {
			$url .= '&restriction=' . self::get_ip_address();
		}

		$response = wp_remote_get( esc_url_raw( $url ) );
		$data     = json_decode( wp_remote_retrieve_body( $response ) );

		return self::normalize( $data );
	}

	private static function normalize( $data ) {
		$response = array( 'total' => 0, 'max_results' => $max_results, 'start_index' => $start_index, 'songs' => array() );

		if( ! empty( $data ) && ! isset( $data->error ) ) {
			$response['total']       = $data->data->totalItems;
			$response['start_index'] = $data->data->startIndex;
			$items                   = $data->data->items;

			foreach( $items as $item ) {
				if ( ! isset( $item->title ) && isset( $item->video, $item->video->title ) ) {
					$item = $item->video;
				}

				array_push( $response['songs'], array(
					'id'        => $item->id,
					'title'     => $item->title,
					'thumbnail' => array( 'normal' => $item->thumbnail->sqDefault, 'high' => $item->thumbnail->hqDefault ),
					'duration'  => $item->duration,
					'mobile'    => isset( $item->player->mobile )
				) );
			}
		}

		return $response;
	}


	private static function get_ip_address() {
		$remote_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;

		$remote_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $remote_ip );

		return $remote_ip;
	}

	private static function being_cached() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}

		if ( ! defined( 'WP_CACHE' ) || ! WP_CACHE ) {
			return false;
		}

		if ( is_user_logged_in() ) {
			return false;
		}

		return true;
	}

}

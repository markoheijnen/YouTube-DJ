<?php

class Youtubedj_API {
	const url = 'https://www.googleapis.com/youtube/v3/';

	private static $key = 'some-key';

	private static $max_results = 25;
	private static $start_index = 1;


	public static function set_max_results( $amount ) {
		self::$max_results = absint( $amount );
	}

	public static function set_start_index( $amount ) {
		self::$start_index = absint( $amount );
	}


	public static function user_info( $user ) {
		$args = array(
			'part'        => 'id',
			'forUsername' => sanitize_text_field( $user )
		);
		$response = self::get_data( 'channels', $args );

		$args = array(
			'part'        => 'brandingSettings,snippet',
			'id' => $response->items[0]->id
		);
		$response = self::get_data( 'channels', $args );

		$data = array();
		if ( $response ) {
			$data['id']          = $response->items[0]->id;
			$data['title']       = $response->items[0]->snippet->title;
			$data['description'] = $response->items[0]->snippet->description;
			$data['photo']       = $response->items[0]->snippet->thumbnails->medium->url;
			$data['background']  = $response->items[0]->brandingSettings->image->bannerImageUrl;
		}

		return $data;
	}

	public static function user_playlist( $user ) {
		$args = array(
			'part'        => 'contentDetails',
			'forUsername' => sanitize_text_field( $user )
		);
		$response = self::get_data( 'channels', $args );

		$args = array(
			'part'        => 'snippet',
			'maxResults'  => self::$max_results,
			'playlistId'  => sanitize_text_field( $response->items[0]->contentDetails->relatedPlaylists->uploads )
		);
		$response = self::get_data( 'playlistItems', $args );

		return self::normalize( $response );
	}

	public static function playlist( $playlist ) {
		$args = array(
			'part'        => 'snippet',
			'maxResults'  => self::$max_results,
			'playlistId'  => sanitize_text_field( $playlist )
		);
		$response = self::get_data( 'playlistItems', $args );

		return self::normalize( $response );
	}

	public static function search( $search ) {
		$args = array(
			'part'        => 'snippet',
			'maxResults'  => self::$max_results,
			'q'           => sanitize_text_field( $search )
		);
		$response = self::get_data( 'search', $args );

		return self::normalize( $response );
	}



	private static function get_data( $method, $args = array() ) {
		$url = self::url . $method;

		$args['key'] = self::$key;
		$url         = add_query_arg( $args, $url );

		$response = wp_remote_get( esc_url_raw( $url ) );
		$data     = json_decode( wp_remote_retrieve_body( $response ) );

		return $data;
	}

	private static function normalize( $data ) {
		$response = array( 'total' => 0, 'max_results' => 0, 'start_index' => 0, 'songs' => array() );

		if ( ! empty( $data ) && ! isset( $data->error ) ) {
			$response['total']       = $data->pageInfo->totalResults;
			$items                   = $data->items;

			foreach ( $items as $item ) {
				if ( isset( $item->id->videoId ) ) {
					$id = $item->id->videoId;
				}
				elseif ( isset( $item->snippet->resourceId ) ) {
					$id = $item->snippet->resourceId->videoId;
				}
				else {
					$id = $item->id;
				}

				array_push( $response['songs'], array(
					'id'        => $id,
					'title'     => $item->snippet->title,
					'thumbnail' => array(
						'normal' => $item->snippet->thumbnails->default->url,
						'high'   => $item->snippet->thumbnails->high->url,
					)
				) );
			}
		}

		return $response;
	}

}
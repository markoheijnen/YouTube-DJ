<?php
/*
Plugin Name: YouTube DJ
Description: Be a DJ with the YouTube DJ Gear.
Version:     0.4

Plugin URI:  http://github.com/markoheijnen/youtube-dj

Author:      Marko Heijnen
Author URI:  http://markoheijnen.com
Donate link: https://markoheijnen.com/donate

License:     GPL2
Text Domain: youtube-dj
Domain Path: /languages
*/

/*  Copyright 2013-2014 YouTube DJ  (email : info@markoheijnen.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include 'inc/api.php';
include 'inc/object.php';

include 'objects/deck.php';
include 'objects/mixer.php';
include 'objects/queue.php';
include 'objects/search.php';

class Youtubedj {
	private $version = '0.4-dev';
	private $objects = array();

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, '_load' ) );
		add_shortcode( 'youtubedj', array( $this, 'default_player' ) );

		add_action( 'wp_enqueue_scripts', array( $this, '_register_scripts' ), 1 );
	}

	public function _load() {
		if ( $this->objects ) {
			return;
		}

		$this->objects['Deck']   = new Youtubedj_Deck;
		$this->objects['Mixer']  = new Youtubedj_Mixer;
		$this->objects['Queue']  = new Youtubedj_Queue;
		$this->objects['Search'] = new Youtubedj_Search;
	}

	public function get( $object ) {
		if( isset( $this->objects[ $object ] ) ) {
			return $this->objects[ $object ];
		}

		return false;
	}

	public function _register_scripts() {
		wp_register_script( 'youtubedj', plugins_url( 'js/ytdj.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-sortable' ), $this->version, true );

		wp_register_style( 'youtubedj', plugins_url( 'css/style.css', __FILE__ ), array('dashicons'), $this->version, 'all' );
		wp_register_style( 'youtubedj-jqui', plugins_url( 'css/ui-lightness/jquery-ui-1.7.2.custom.css', __FILE__ ), array(), '1.7.2', 'all' );
	}

	public function default_player( $atts = '' ) {
		extract( shortcode_atts( array(
			'desk1'    => 'BR_DFMUzX4E',
			'desk2'    => 'sOS9aOIXPEk',
			'user'     => isset( $_GET['user'] ) ? $_GET['user'] : false,
			'playlist' => isset( $_GET['playlist'] ) ? $_GET['playlist'] : false,
		), $atts ) );

		wp_enqueue_script('youtubedj');

		$args = array(
			'ajax'         => admin_url( 'admin-ajax.php' ),
			'are_you_sure' => __( 'Are you sure you want to stop the music?', 'youtube-dj' ),
			'add_to_queue' => __( 'Add to queue', 'youtube-dj' ),
		);
		wp_localize_script( 'youtubedj', 'youtubedj', $args );

		wp_enqueue_style('youtubedj');
		wp_enqueue_style('youtubedj-jqui');


		$queue = array();

		if( $user ) {
			$user_data = Youtubedj_API::user_playlist( $user );

			if( $user_data['total'] > 1 ) {
				$desk1 = $user_data['songs'][0]['id'];
				$desk2 = $user_data['songs'][1]['id'];
				unset( $user_data['songs'][0], $user_data['songs'][1] );

				$queue = $user_data['songs'];
			}
		}
		else if( $playlist ) {
			$playlist_data = Youtubedj_API::playlist( $playlist );

			if( $playlist_data['total'] > 1 ) {
				$desk1 = $playlist_data['songs'][0]['id'];
				$desk2 = $playlist_data['songs'][1]['id'];
				unset( $playlist_data['songs'][0], $playlist_data['songs'][1] );

				$queue = $playlist_data['songs'];
			}
		}


		$html  = '<div class="booth">';

		$html .= '<div class="rack-left rack">';
		$html .= $this->get( 'Deck' )->html( 'deck1', __( 'Deck 1', 'youtube-dj' ), $desk1, 'queue' );
		$html .= '</div>';

		$html .= '<div class="rack-right rack">';
		$html .= $this->get( 'Deck' )->html( 'deck2', __( 'Deck 2', 'youtube-dj' ), $desk2, 'queue' );
		$html .= '</div>';

		$html .= '<div class="rack-center rack">';
		$html .= $this->get( 'Mixer' )->html( __( 'Mixer', 'youtube-dj' ), 'deck1', 'deck2' );
		$html .= $this->get( 'Search' )->html( _x( 'Search', 'Compontent title', 'youtube-dj' ), 'queue', array( 'deck1', 'deck2' ) );
		$html .= $this->get( 'Queue' )->html( 'queue', __( 'Queue', 'youtube-dj' ), array( 'deck1', 'deck2' ), $queue );
		$html .= '</div>';

		$html .= '</div>';

		add_action( 'wp_footer', array( $this, 'load_close_dialog' ) );

		return $html;
	}

	public function load_close_dialog() {
		?>

			<div class="modal fade" id="closeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="myModalLabel"><?php _e( 'Stopping the music', 'youtube-dj' ); ?></h4>
						</div>
						<div class="modal-body">
							<p><?php _e( 'Are you sure you want to stop the music?', 'youtube-dj' ); ?></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default btn-follow-link"><?php _e( 'Leave this page', 'youtube-dj' ); ?></button>
							<button type="button" class="btn btn-primary" data-dismiss="modal"><?php _e( 'Stay on this page', 'youtube-dj' ); ?></button>
						</div>
					</div>
				</div>
			</div>

	<?php
	}

}

$GLOBAL['youtubedj'] = new Youtubedj;

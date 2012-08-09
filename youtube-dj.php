<?php
/*
Plugin Name: YouTube DJ
Plugin URI: http://github.com/markoheijnen/youtube-dj
Description: Be a DJ with the YouTube DJ Gear.
Version: 1.0
Author: markoheijnen, delans
Author URI: http://markoheijnen.com http://basvanderlans.nl
License: GPL2
*/

/*  Copyright YEAR  YouTube DJ  (email : info@markoheijnen.com)

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

include 'inc/deck.php';
include 'inc/mixer.php';
include 'inc/que.php';
include 'inc/search.php';

class Youtubedj {
	private $version = '1.0';
	private $objects = array();

	function __construct() {
		add_action( 'plugins_loaded', array( &$this, '_load' ) );
		add_shortcode( 'youtubedj', array( &$this, 'default_player' ) );

		add_action( 'wp_enqueue_scripts', array( &$this, '_register_scripts' ), 1 );
	}

	function _load() {
		$this->objects['Deck']   = new Youtubedj_Deck;
		$this->objects['Mixer']  = new Youtubedj_Mixer;
		$this->objects['Que']    = new Youtubedj_Que;
		$this->objects['Search'] = new Youtubedj_Search;
	}

	public function get( $object ) {
		if( isset( $this->objects[ $object ] ) ) {
			return $this->objects[ $object ];
		}

		return false;
	}

	function _register_scripts() {
		wp_register_script( 'youtubedj', plugins_url( 'js/ytdj,js', __FILE__ ), array( 'jquery', 'jquery-ui' ), $this->version, true );

		wp_register_style( 'youtubedj', plugins_url( 'css/style,css', __FILE__ ), array(), $this->version, 'all' );
		wp_register_style( 'youtubedj-jqui', plugins_url( 'css/ui-lightness.jquery-ui-1.7.2.custom.css', __FILE__ ), array(), '1.7.2', 'all' );
	}

	public function default_player( $atts = '' ) {
		wp_enqueue_script('youtubedj');
		wp_enqueue_script('youtubedj');
		wp_enqueue_script('youtubedj-jqui');

		$html  = '<div class="booth">';

		$html .= '<div class="rack">';
		$html .= $this->get( 'Deck' )->html( 'deck1', 'Deck 1' );
		$html .= '</div>';

		$html .= '<div class="rack">';
		$html .= $this->get( 'Mixer' )->html( 'Mixer', 'deck1', 'deck2' );
		$html .= $this->get( 'Search' )->html( 'Search', array( 'deck1', 'deck2' ) );
		$html .= $this->get( 'Mixer' )->html( 'Mixer', 'deck1', 'deck2' );
		$html .= '</div>';

		$html .= '<div class="rack">';
		$html .= $this->get( 'Deck' )->html( 'deck2', 'Deck 2' );
		$html .= '</div>';

		return $html;
	}
}

$GLOBAL['youtubedj'] = new Youtubedj;
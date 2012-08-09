<?php

class Youtubedj_Search {
	function __construct() {
		
	}

	function html( $title, $decks ) {
		$html  = '<div class="search gear">';
		$html .= '<h2>' . $title . '</h2>';

		$html .= '<form action="" method="post">';
		$html .= '<input name="searchTerm" type="text" value="Your song" />';
		$html .= '<input type="submit" value="Search" />';
		$html .= '</form>';

		$html .= '<div id="searchResultsVideoList"></div>';
		$html .= '<div id="searchResultsNavigation" style="display: none;">';
		$html .= '<form class="navigationForm" action="" method="post" >';
		$html .= '<input type="button" class="previousPageButton" value="Back" />';
		$html .= '<input type="button" class="nextPageButton" value="Next" />';
		$html .= '</form>';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}
}
/**
 * Fix needToConfirm to check all players
 */

var tag = document.createElement('script');
tag.src = "http://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

function onYouTubePlayerAPIReady() {
	jQuery('.deck').deck();
	jQuery('.mixer').mixer();
}

(function( $ ){

	$.fn.deck = function() {
		return this.each(function() {
			var _this  = $(this);
			var player = null;
			var id     = _this.find('.player').attr('id');
			var code   = _this.find('.player').attr('movie');

			if( code.length > 0 ) {
				player = load_movie( id, code );
			}

			$( '.play', _this ).click(function() {
				if( player ) {
					player.playVideo();
					needToConfirm = true;
				}
			});

			$( '.pause', _this ).click(function() {
				if( player ) {
					player.pauseVideo();
				}
			});

			$( '.stop', _this ).click(function() {
				if( player ) {
					player.stopVideo();
					needToConfirm = false;
				}
			});

			$( '.gain', _this ).slider({ 
				orientation: "vertical",
				min: 0,
				max: 100,
				value: 100,
				slide: function(event, ui) {
					if( player ) {
						player.setVolume(ui.value);
					}
				}
			});
		});

		function load_movie( id, code ) {
			player = new YT.Player( id, {
				height: '250',
				width: '300',
				videoId: code
			});

			$.data( document.body, id, player );

			return player;
		};
	}

	$.fn.mixer = function() {
		return this.each(function() {
			var _this = $(this);
			var deck1 = $(this).attr('deck1');
			var deck2 = $(this).attr('deck2');

			if( deck1.length > 0 && deck2.length > 0 ) {
				$( '.crossfader', _this ).slider({ 
					min: 0,
					max: 100,
					value: 60,
					slide: function(event, ui) {
						crossFade( deck1, deck2, ui.value );
					}
				});

				$( '.mixer-button-a', _this ).click(function () {
					$( '.crossfader', _this ).slider('option', 'value', 0);
					crossFade( deck1, deck2, 0 );
				});	
				
				$( 'mixer-button-b', _this ).click(function () {
					$( '.crossfader', _this ).slider('option', 'value', 100);
					crossFade( deck1, deck2, 100 );
				});
			}
		});

		function crossFade(deckId1, deckId2, fadeLoc) {
			var deck1 = $( '#' + deckId1 );
			var deck2 = $( '#' + deckId2 );

			var volDeck1 = jQuery( '.gain', deck1 ).slider('option', 'value');
			var volDeck2 = jQuery( '.gain', deck2 ).slider('option', 'value');

			$.data( document.body, deck1.find('.player').attr('id') ).setVolume( ( 100 - fadeLoc ) * ( volDeck1 / 100 ) );
			$.data( document.body, deck2.find('.player').attr('id') ).setVolume( fadeLoc * ( volDeck2 / 100 ) );
		}
	}

})( jQuery );


//onError
needToConfirm = false;
window.onbeforeunload = askConfirm;

function askConfirm(){
	if (needToConfirm){
		return "ARE YOU SURE!?";
	}    
}



var params = { allowScriptAccess: "always" };

		
function deckLoad(deckId, videoId) {
	//alert(videoId + ' ' + deckId);
	var startSeconds = 0;
	var suggestedQuality = "small";
	ytplayers[deckId].cueVideoById(videoId, startSeconds, suggestedQuality);
}


function getUrlVars()
{
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	//alert (vars[1]);
}

//getUrlVars();

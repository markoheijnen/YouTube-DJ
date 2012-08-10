/**
 * Fix needToConfirm to check all players
 */

var tag = document.createElement('script');
tag.src = "http://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

function onYouTubePlayerAPIReady() {
	jQuery('.deck').deck();
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
					player.setVolume(ui.value);
				}
			});
		});

		function load_movie( id, code ) {
			player = new YT.Player( id, {
				height: '250',
				width: '300',
				videoId: code
			});

			return player;
		};
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
		
		function crossFade(deckId1, deckId2, fadeLoc) {
			var volDeck1 = jQuery('#deck0 .gain').slider('option', 'value');
			var volDeck2 = jQuery('#deck1 .gain').slider('option', 'value');
			ytplayers[deckId1].setVolume( ( 100 - fadeLoc ) * ( volDeck1 / 100 ) );
			ytplayers[deckId2].setVolume(fadeLoc * ( volDeck2 / 100 ) );
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
			
		// jQuery Shizzle
		jQuery(function( $ ) {
			
			$("#crossfader").slider({ 
				//orientation: "vertical",
				//range: "min",
				min: 0,
				max: 100,
				value: 60,
				slide: function(event, ui) {
					//$("#amount").val(ui.value);
					crossFade(0, 1, ui.value);
				}
			});

			$('#cfToA').click(function () {
				$('#crossfader').slider('option', 'value', 0);
				crossFade(0, 1, 0);
			});	
			
			$('#cfToB').click(function () {
				$('#crossfader').slider('option', 'value', 100);
				crossFade(0, 1, 100);
			});
			
		});

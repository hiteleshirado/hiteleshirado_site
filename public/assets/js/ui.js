$(document).ready( function()
{
	var sBodyId = document.getElementsByTagName( 'body' )[0].id;
	if ( "undefined" !== typeof window.oUiInit[sBodyId] )
	{
		window.oUiInit[sBodyId]();
	}
	if ( "undefined" !== typeof window.oUiInit['general'] )
	{
		window.oUiInit['general']();
	}
} );

var oUiInit =
{
	'list': function()
	{
		$('div#list').infinitescroll(
		{
			navSelector  : "ul#pagination",
			nextSelector : "ul#pagination li a.next",
			itemSelector : "div#list div.img",
			loading: {
				finishedMsg: "<em>Itt a vége a zinternettnek.</em>",
				img: "/assets/img/loading.gif",
				msgText: "<em>További riportok betöltése</em>"
			},
			state: {
				currPage : parseInt( $('#page_number').val(), 10 )
			},
			pathParse    : ['/','']
		});
	},
	'general': function()
	{
		$('form.delete').submit( function()
		{
			return confirm( "Tényleg törölni akarod ezt a riportot?" );
		});
	}
};
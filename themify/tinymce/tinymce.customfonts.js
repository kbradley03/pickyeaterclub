(function($) {

	'use strict';

	if( typeof themifyCustomFonts === 'undefined' )
		return;

	tinymce.PluginManager.add( 'themifyCustomFonts', function( editor, url ) {
		var menu = [];
		var apply_font = function(e){
			var text = editor.selection.getContent( { format: 'text' } ) || themifyCustomFonts.default_text;
			editor.insertContent( '<span class="' + this.value() + '"><!-- [themify_gfont id="' + themifyCustomFonts.fonts[this.value()] + '"] -->' + text + '</span>' );
		};
		$.each(themifyCustomFonts.fonts, function(i, val){
			menu.push( {
				text: val,
				value: i,
				onclick: apply_font
			} );
		});

		editor.addButton( 'themifyCustomFonts', {
			text: themifyCustomFonts.label,
			type: 'menubutton',
			menu: menu
		} );

		editor.on( 'init', function( args ){
			$(editor.dom.doc.head).append( '<link rel="stylesheet" href="' + themifyCustomFonts.stylesheet + '" type="text/css" />' ).append(themifyCustomFonts.styles);
		} );
	} );

})(jQuery);
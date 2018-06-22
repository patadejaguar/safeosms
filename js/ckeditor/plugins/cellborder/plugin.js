( function() {
	var commandDefinition = {
		preserveState: true,
		editorFocus: false,
		readOnly: 1,

		exec: function( editor ) {
			this.toggleState();
			this.refresh( editor );
		},

		refresh: function( editor ) {
			if ( editor.document ) {
				var funcName = ( this.state == CKEDITOR.TRISTATE_ON ) ? 'attachClass' : 'removeClass';
				editor.editable()[ funcName ]( 'cke_show_borders' );
			}
		}
	};

	var showBorderClassName = 'cke_show_border';

CKEDITOR.plugins.add( 'border', {
	requires: 'menubutton',
	icons: 'border_bottom,border_double_bottom,border_left,border_outer,border_right,border_top,border_none',
	
	init: function( editor ) {
		var borderouter = ( editor.config.borderTypeList || [ 'border_bottom:Rahmenlinie unten:border-bottom:1px solid black', 
		                                                      'border_top:Rahmenlinie oben:border-top:1px solid black', 
		                                                      'border_right:Rahmenlinie rechts:border-right:1px solid black', 
		                                                      'border_left:Rahmenlinie links:border-left:1px solid black', 
		                                                      'border_double_bottom:Rahmenlinie doppelt unten:border-bottom:3px double black'] ),
			plugin = this,
			items = {},
			parts,
			curBorderType,
			i;

			// Registers command.
			editor.addCommand( 'border', {
			allowedContent : 'table tr th td[style]{*} caption;',
			contextSensitive: true,
			exec: function( editor, item) {
				if ( item )
					editor[ item.style.checkActive( editor.elementPath() ) ? 'removeStyle' : 'applyStyle' ]( item.style );
			},
			
			remove: function(editor){
				editor.removeStyle();
			},
			
			refresh: function( editor, path ) {
				if(path.contains('table')){
					var curStyle = plugin.getCurrentBorderType( editor );
					if ( curStyle ){
						var att = curStyle.getAttribute( 'style' );
						if(att){
							var parts = att.split( ':' );
							this.setState( parts[0].indexOf('border') == 0 ? CKEDITOR.TRISTATE_ON : CKEDITOR.TRISTATE_OFF );
						}
						else
							this.setState(CKEDITOR.TRISTATE_OFF );
					}
					else
						this.setState(CKEDITOR.TRISTATE_OFF );
				}
				else
					this.setState(CKEDITOR.TRISTATE_DISABLED);
			}
		} );

		// Parse borderConfigStrings, and create items entry for each border.
		for ( i = 0; i < borderouter.length; i++ ) {
			parts = borderouter[ i ].split( ':' );
			curBorderType = parts[ 2 ];
			items[ parts[ 0 ] ] = {
				label: parts[ 1 ],
				borderType: curBorderType,
				itemName : parts[0],
				group: 'border_outer',
				icon: parts[ 0 ],
				order: i,
				onClick: function() {
					editor.execCommand( 'border', this);
				},
				role: 'menuitemcheckbox'
			};

			// Init style property.
			items[ parts[ 0 ] ].style = new CKEDITOR.style( {
				element: 'td',
				attributes: {
					style: 	curBorderType + ":" + parts[3]
				}
			} );
		}
		
		items.border_none = {
				label: 'Kein Rahmen',
				group: 'border_general',
				icon: 'border_none',
				order: items.length,
				onClick: function() {
					var curBorderType = plugin.getCurrentBorderType(editor);
				
					if ( curBorderType ){
						var att = curBorderType.getAttribute( 'style' );
						if(att){
							parts = att.split( ':' );
							this.style = new CKEDITOR.style( {
								element: 'td',
								attributes: {
									style: 	att,
								}
							});
							if ( curBorderType )
								editor.execCommand( 'border', this);
						}		
					}		
				}
			};
		

		items.border= {
				label: 'Alle Rahmenlinien',
				group: 'border_general',
				icon: 'border_outer',
				borderType: 'border',
				order: items.length,
				onClick: function() {
					editor.execCommand( 'border', this );
				}
			};
		
		items.border.style = new CKEDITOR.style( {
			element: 'td',
			attributes: {
				style: 	'border: 1px solid black',
			}
		} );

		// Initialize groups for menu.
		editor.addMenuGroup( 'border_outer', 1 );
		editor.addMenuGroup( 'border_general', 2);
		editor.addMenuItems( items );

		editor.ui.add( 'Border',  CKEDITOR.UI_MENUBUTTON, {
			label: 'Rahmenlinie hinzufügen',
			icon: "border_bottom",
			toolbar: 'insert',
			command: 'border',
			allowedContent : 'table tr th td[style]{*} caption;',
			onMenu: function() {
				var activeItems = {}, parts;	
				curBorderType = plugin.getCurrentBorderType(editor);
				for ( var prop in items )
						activeItems[ prop ] = CKEDITOR.TRISTATE_OFF;
				if ( curBorderType ){
					var att = 	curBorderType.getAttribute( 'style' );
					if(att){
						parts = att.split( ':' );
						borderType = parts[0];
						activeItems[ borderType ] = CKEDITOR.TRISTATE_ON;
					}
				}
				return activeItems;
			}
		} );
	},
	
	// Gets the bordertype for the current editor selection.
	// @param {CKEDITOR.editor} editor
	// @returns {CKEDITOR.dom.element} The bordertype element, if any.
	getCurrentBorderType: function( editor ) {
		var elementPath = editor.elementPath(),
			activePath = elementPath && elementPath.elements,
			pathMember, ret;
		// IE8: upon initialization if there is no path elementPath() returns null.
		if ( elementPath ) {
			for ( var i = 0; i < activePath.length; i++ ) {
				pathMember = activePath[ i ];
				if ( !ret && pathMember.getName() == 'td' && pathMember.hasAttribute( 'style' )){
					ret = pathMember;
				}
			}
		}
		return ret;
	}
} );


} )();

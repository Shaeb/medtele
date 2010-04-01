String.prototype.trim = function() { return this.replace(/^\s\s*/, '').replace(/\s\s*$/, ''); };

Object.prototype.explain = function() {
	var x = '[ ';
	for( var i in this ) {
		x += '\n' + i + ': ' + this[ i ]; 
	}
	x += '\n]';
	alert( x );
	delete i;
	delete x;
};

Function.prototype.bind = function (object)
{
        var method = this;
       
        return function ()
        {
                return method.apply(object, arguments);
        };
};


var Wow = {
	Class: function() {
			return function() {
		    		this.initialize.apply( this,arguments );
		};
	},
	    Import: function( strScript,strStartURL,bDotsAreFolders )
	    {
	        // Cache the packaged files that are in base.js.  No need to include
	        // these because they are already included.  Also, let's use a "set" to
	        // keep lookup time down
	        if (typeof arguments.callee.packagedFiles === "undefined") {
	            arguments.callee.packagedFiles = {
	                "Figment":1,
	                "Figment.EntryPoint":1,
	                "Figment.EventHandler":1,
	                "Figment.Cookies":1,
	                "Figment.Date":1,
	                "Figment.DOM":1,
	                "Figment.HashTable":1,
	                "Figment.Net.Request":1,
	                "Figment.Window":1,
	                "Figment.Form":1,
	                "Disney.DOL.Chrome.Settings":1,
	                "Disney.WDPRO.UI.Widgets.Modules":1,
	                "Disney.WDPRO.UI.InterfaceBlocking":1,
	                "Disney.WDPRO.IBC.UI.DynamicPleaseWait":1
	            };
	        }
	        // If it's 1, then it's in the list and we need to leave
	        if (arguments.callee.packagedFiles[strScript] === 1) { return; }
	
	        var bImport = false;
	        var strNamespace = null;
	        var listOfNamespaces = null;
	        var root = null;
	        var iLen = null;
	        var i = 0;
	        var objHead = null;
	        var strJs = null;
	        var objScript = null;
	        var ajaxRequestor = null;
	
	        strNamespace = strScript;
	        if( arguments.length === 3 && bDotsAreFolders )
	        {
	            strScript = strScript.replaceAll( '.','/' );
	        }
	        strScript += '.js';
	
	        if( arguments.length < 2 )
	        {
	            strStartURL = '';
	        }
	        else
	        {
	            if( strStartURL !== null && strStartURL !== '' && !strStartURL.endsWith( '/' ) )
	            {
	                strStartURL += '/';
	            }
	        }
	
	        listOfNamespaces = strNamespace.split( '.' );
	        root = window;
	        iLen = listOfNamespaces.length;
	        for( i=0; i < iLen; i++ )
	        {
	            if( !root[ listOfNamespaces[i] ] )
	            {
	                bImport = true;
	                break;
	            }
	            root = root[ listOfNamespaces[i] ];
	        }
	
	    // Label the import section
	    markImport:
	
	        if( bImport )
	        {
	            objScript = document.createElement( "SCRIPT" );
	            objHead = document.getElementsByTagName( "HEAD" );
	
	            if( objHead.length > 0 )
	            {
	                objHead = objHead[0];
	            }
	            else
	            {
	                alert( "Script not loaded" );
	                break markImport;
	            }
	
	            objScript.type = "text/javascript";
	            objScript.src = Figment.getBase() + strStartURL + strScript;
	            objHead.appendChild( objScript );
	
	            delete objScript;
	            delete objHead;
	        }
	        delete listOfNamespaces;
	        delete root;
    }
};

Wow.DOM = {
	// taken from http://javascript.about.com/library/bldom08.htm
	getElementsByClassName:function( className ) {
		var retnode = [];
		var classRegEx = new RegExp( '\\b' + className + '\\b' );
		var elem = document.getElementsByTagName('*');
		for( var i = 0; i < elem.length; i++ ) {
			var classes = elem[ i ].className;
			if ( classRegEx.test( classes ) ) retnode.push( elem[ i ] );
		}
		delete classRegEx;
		delete elem;
		delete classes;
		return retnode;
	},
	
	removeChildElements: function( target ) {
		if ( target.hasChildNodes() )
		{
		    while ( target.childNodes.length >= 1 )
		    {
			target.removeChild( target.firstChild );       
		    } 
		}
	},
	
	addElement: function( target, element ) {
		target.appendChild( element );
	}
};

Wow.Events = {
	onLoad: function( func ) {
		if( func ) {
			this.addEvent( window, 'load', func );
		}
	},
	
	addEvent: function( target, event, func, bubble )
	{
		if( target.addEventListener ) {
			target.addEventListener( event, func, bubble );
		}
		if( target.attachEvent ) {	
			target.attachEvent( 'on' + event, func );
		}
	},
	
	getEvent:function(event){
		if( null === event){
			event=window.event;
		}
		var element = null;
		if(event.target){
			element = event.target;
			//_e6=event.currentTarget;
			//_e7=event.target;
			//_e8=event.relatedTarget;
		}else{
			element = event.srcElement;
			//_e6=event.srcElement;
			//_e7=event.srcElement;
			//_e8=event.fromElement!==null?event.fromElement:event.toElement;
		}
		return element;
	},
	
	attachToGroup: function( className, eventName, func ) {
		var elements = Wow.DOM.getElementsByClassName( className );
		var i = 0;
		for( i; i < elements.length; (++i) ) {
			Wow.Events.addEvent( elements[ i ], eventName, func );
		}
		delete elements;
		delete i;
	},
	
	stop: function( event ) {	
			//event.cancelBubble is supported by IE - this will kill the bubbling process.
			event.cancelBubble = true;
			event.returnValue = false;
	
			//event.stopPropagation works only in Firefox.
			if (event.stopPropagation) {
				event.stopPropagation();
				event.preventDefault();
			}
			return false;
	}
};

Wow.UI = {
	toggleElement: function( target ) {
		if( Wow.UI.Styles.hasClass( target, 'hide' ) ) {
			Wow.UI.Styles.removeClass( target, 'hide' );
			Wow.UI.Styles.addClass( target, 'reveal' );
		} else {
			Wow.UI.Styles.removeClass( target, 'reveal' );
			Wow.UI.Styles.addClass( target, 'hide' );
		}
	},
	
	show: function( target ) {
		if( Wow.UI.Styles.hasClass( target, 'hide' ) ) {
			Wow.UI.Styles.removeClass( target, 'hide' );
		}	
		Wow.UI.Styles.addClass( target, 'reveal' );
	},
	
	hide: function( target ) {
		if( Wow.UI.Styles.hasClass( target, 'reveal' ) ) {
			Wow.UI.Styles.removeClass( target, 'reveal' );
		}
		Wow.UI.Styles.addClass( target, 'hide' );
	}
};

Wow.UI.Styles = {
	hasClass: function (ele,cls) {
		return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
	},
	addClass: function (ele,cls) {
		if (!this.hasClass(ele,cls)) ele.className += " "+cls;
	},
	removeClass: function (ele,cls) {
		if (this.hasClass(ele,cls)) {
			var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
			ele.className=ele.className.replace(reg,' ');
		}
	}
};

Wow.UI.Layover = {
	body: null,
	container: null,
	target: null,
	CONTAINER_ID: 'overlay',
	CLOSE_CLICK_BACKGROUND: true, //default
	REVEAL_CLASS: 'reveal',
	HIDE_CLASS: 'hide',
	TRIGGER_CLASS_NAME: 'trigger',
	BUFFER_WIDTH: 10, // in pixels, makes a 10px border around the object
	BUFFER_HEIGHT: 10, // same as above but for height

	onLoad: function() {
		body = document.getElementsByTagName( 'body' )[0];
		container = document.createElement( 'div' );
		container.id = Wow.UI.Layover.CONTAINER_ID;
		var elements = Wow.DOM.getElementsByClassName( Wow.UI.Layover.TRIGGER_CLASS_NAME );
		var i = 0;
		for( i; i < elements.length; (++i) ) {
			Wow.UI.Layover.add( elements[ i ] );
		}
		delete elements;
		delete i;
	},

	add: function( trigger, closure ) {
		if( trigger ) {
			Wow.Events.addEvent( trigger, 'click', this.onClick );
		}
		if ( !closure ) {
			Wow.Events.addEvent( container, 'click', this.exit, false );
		}
	},

	onClick: function( x ) {
		var event = Wow.Events.getEvent( x );
		if( event && event.getAttribute( 'target' ) ) {
			target = document.getElementById( event.getAttribute( 'target' ) );
		} else {
			return;
		}
		
		Wow.UI.Styles.addClass( target, Wow.UI.Layover.REVEAL_CLASS );
		container.appendChild( document.createTextNode( '\xa0' ) );
		body.appendChild( target );
		body.appendChild( container );
		
		var bodyWidth = screen.width;
		var bodyHeight = screen.height;
		
		var height = ( target.offsetHeight ) ? target.offsetHeight : target.style.pixelHeight;
		var width =  ( target.offsetWidth ) ? target.offsetWidth : target.style.pixelWidth;
		
		//target.style.position = 'absolute';
		target.style.left = Math.round( ( bodyWidth - width ) / 2 ) + 'px';
		target.style.top = Math.round( ( bodyHeight - height ) / 4 ) + 'px';
		
		Wow.Events.addEvent( target, 'click', function(){ return false; }, false );
		
		delete event;
		delete height;
		delete width;
		
		Wow.Events.stop( x );
	},
	
	exit: function() {
		Wow.UI.Styles.removeClass( target, Wow.UI.Layover.REVEAL_CLASS );
		body.removeChild( container );
	}
};

Wow.UI.Editable = {
	parent: null,
	target: null,
	text: null,
	isEditing: false,
	update: null,
	
	onLoad: function() {
		var elements = Wow.DOM.getElementsByClassName( 'editable' );
		var i = 0;
		
		for( i; i < elements.length; (++i) ) {
			Wow.Events.addEvent( elements[ i ], 'click', Wow.UI.Editable.onClick );
		}
		
		delete elements;
		delete i;
	},
	
	onClick: function( e ) {
		e = Wow.Events.getEvent( e );
		Wow.UI.Editable.target = e;
		Wow.UI.Editable.parent = e.parentNode;
		Wow.UI.Editable.text = e.innerHTML;
		var textarea = document.createElement( 'TEXTAREA' );
		textarea.style.width = e.offsetWidth + 'px';
		textarea.style.height = e.offsetHeight + 'px';
		textarea.id = 'editing_' + e.id;

		textarea.value = Wow.UI.Editable.text.trim();
		Wow.Events.addEvent( textarea, 'blur', Wow.UI.Editable.onBlur );
		textarea.style.className = 'editing';
		Wow.UI.Editable.parent.replaceChild( textarea, e );
		textarea.focus();
		
		Wow.UI.Editable.isEditing = true;
		
		delete textarea;
	},
	
	onBlur: function( e ) {
		e = Wow.Events.getEvent( e );
		Wow.UI.Editable.target.innerHTML = e.value;
		Wow.UI.Editable.parent.replaceChild( Wow.UI.Editable.target, e );
		Wow.UI.Editable.isEditing = false;
		
		if( null !== Wow.UI.Editable.update ) {
			Wow.UI.Editable.update( Wow.UI.Editable.target );
		}
		
	}
};

Wow.UI.Fade = {
	fadeFactor: 0, // given in milliseconds
	FADE_ATTRIBUTE: 'fadeTarget',
	FADE_SPEED_ATTRIBUTE: 'fadeSpeed',
	callback: null,

	fade: function( target, time ) {
		if(target == null)
		return;

		if ( time ) {
			Wow.UI.Fade.fadeFactor = time;	
		}

		if ( Wow.UI.Fade.fadeFactor == 0 ) {
			Wow.UI.Fade.fadeFactor = 1000.0; // one second
		}

		if ( target.FadeState == null ) {
			if ( target.style.opacity == null || target.style.opacity == '' || target.style.opacity == '1')	{
				target.FadeState = 2;
			} else {
				target.FadeState = -2;
			}
		}

		if ( target.FadeState == 1 || target.FadeState == -1 ) {
			target.FadeState = target.FadeState == 1 ? -1 : 1;
			target.FadeTimeLeft = Wow.UI.Fade.fadeFactor - target.FadeTimeLeft;
		} else {
			target.FadeState = target.FadeState == 2 ? -1 : 1;
			target.FadeTimeLeft = Wow.UI.Fade.fadeFactor;
			setTimeout("Wow.UI.Fade.animateFade(" + new Date().getTime() + ",'" + target.id + "')", 33);
		}  
	},

	animateFade: function( lastTick, targetId ) {  
		var curTick = new Date().getTime();
		var elapsedTicks = curTick - lastTick;

		var target = document.getElementById( targetId );

		if ( target.FadeTimeLeft <= elapsedTicks )	{
			target.style.opacity = target.FadeState == 1 ? '1' : '0';
			target.style.filter = 'alpha(opacity = '+ ( target.FadeState == 1 ? '100' : '0' ) + ')';
			target.FadeState = target.FadeState == 1 ? 2 : -2;
			if( Wow.UI.Fade.callback ) {
				Wow.UI.Fade.callback( target );
			}
			return;
		}

		target.FadeTimeLeft -= elapsedTicks;
		var newOpVal = target.FadeTimeLeft/Wow.UI.Fade.fadeFactor;
		
		if ( target.FadeState == 1 ) {
			newOpVal = 1 - newOpVal;
		
		}

		target.style.opacity = newOpVal;
		target.style.filter = 'alpha(opacity = ' + (newOpVal*100) + ')';

		setTimeout("Wow.UI.Fade.animateFade(" + curTick + ",'" + targetId + "')", 33);
		
		delete curTick;
		delete elapsedTicks;
		delete target;
		delete newOpVal;
	},
	
	onActivate: function( event ) {
		event = Wow.Events.getEvent( event );
		if ( !event ) {
			return;
		}
		if( !event.getAttribute( Wow.UI.Fade.FADE_ATTRIBUTE ) ) {
			return;
		}
		var target = document.getElementById( event.getAttribute( Wow.UI.Fade.FADE_ATTRIBUTE ) );
		if( !target ) { 
			return;
		}
		var fadeSpeed = 0; // will default to 1000 automatically
		if( event.getAttribute( Wow.UI.Fade.FADE_SPEED_ATTRIBUTE ) ) {
			fadeSpeed = event.getAttribute( Wow.UI.Fade.FADE_SPEED_ATTRIBUTE );
		}
		Wow.UI.Fade.fade( target, fadeSpeed );
		delete target;
		delete fadeSpeed;
	},
	
	onLoad: function() {
		Wow.Events.attachToGroup( 'fadeTrigger', 'click', Wow.UI.Fade.onActivate );
	}
};

Wow.UI.MessageBox = function( messageBoxID, messageBoxClass, messageText ){
	this.messageBox = document.createElement( 'div' );
	this.originalMessage = messageText;
	
	this.messageBox.id = messageBoxID;
	Wow.UI.Styles.addClass( this.messageBox, messageBoxClass );

	var body = document.getElementsByTagName( 'body' )[0];
		
	Wow.DOM.addElement( body, this.messageBox );
	
	if ( messageText ) {
		Wow.DOM.addElement( this.messageBox, this.originalMessage );
	}
		
	delete body;
};

Wow.UI.MessageBox.prototype.addMessage = function( text ) {
	var message = document.createElement( 'p' );
	message.innerHTML = text;
	Wow.DOM.addElement( this.messageBox, message );
	delete message;
};

Wow.UI.MessageBox.prototype.clear = function() {
	Wow.DOM.removeChildElements( this.messageBox );
};

Wow.UI.MessageBox.prototype.reset = function() {
	this.clear();
	Wow.DOM.addElement( this.messageBox, this.originalMessage );
};

/*********************************
Wow.UI.DragAndDrop = {
	dragObject: null,
	dragOffsetX: 0,
	dragOffsetY: 0,
	dragging: false,
	originalCursor: '',
	class: 'moveable',
	moveX: true,
	moveY: true,
	moveXClass: 'moveXOnly',
	moveYClass: 'moveYOnly',
	
	onLoad: function( event ) {
		var moveables = Wow.DOM.getElementsByClassName( 'moveable' );
		var i = 0;
		for( i; i < moveables.length; (++i) ) {
			Wow.Events.addEvent( moveables[ i ], 'click', Wow.UI.DragAndDrop.onClick );
			if( Wow.UI.Styles.hasClass( moveables[ i ], Wow.UI.DragAndDrop.moveXClass ) ) {
				Wow.UI.DragAndDrop.moveX = true;
				Wow.UI.DragAndDrop.moveY = false;
			}
			if( Wow.UI.Styles.hasClass( moveables[ i ], Wow.UI.DragAndDrop.moveYClass ) ) {
				Wow.UI.DragAndDrop.moveX = false;
				Wow.UI.DragAndDrop.moveY = true;
			}
		}
		delete moveables;
		delete i;
	},
	
	onClick: function( event ) {
		if( !Wow.UI.DragAndDrop.dragging ) {
			Wow.UI.DragAndDrop.dragObject = Wow.Events.getEvent( event );
			if( null === Wow.UI.DragAndDrop.dragObject ) { return false; }
			if( !Wow.UI.Styles.hasClass( Wow.UI.DragAndDrop.dragObject, Wow.UI.DragAndDrop.class ) ) { return false; }
			Wow.UI.DragAndDrop.originalCursor = Wow.UI.DragAndDrop.dragObject.style.cursor;
			Wow.UI.DragAndDrop.dragObject.style.cursor = 'move';
			Wow.UI.DragAndDrop.dragging = true;
			Wow.UI.DragAndDrop.dragOffsetX = event.clientX - parseInt( Wow.UI.DragAndDrop.dragObject.offsetLeft );
			Wow.UI.DragAndDrop.dragOffsetY = event.clientY - parseInt( Wow.UI.DragAndDrop.dragObject.offsetTop );

			Wow.Events.addEvent( document, 'mousemove', Wow.UI.DragAndDrop.onMouseMove );
			return false;
		} else {
			Wow.UI.DragAndDrop.onMouseUp( event );
		}
	},
	
	onMouseUp: function( event ) {
		document.onmousemove = null;
		document.onmousedown = null;
		Wow.UI.DragAndDrop.dragObject.style.cursor = Wow.UI.DragAndDrop.originalCursor;
		Wow.UI.DragAndDrop.dragging = false;
	},
	
	onMouseMove: function( event ) {
		if( null == event ) { event = window.event; }
		if( 2 > event.button && Wow.UI.DragAndDrop.dragging ) {
			if( Wow.UI.DragAndDrop.moveX ) {
				Wow.UI.DragAndDrop.dragObject.style.left = event.clientX - Wow.UI.DragAndDrop.dragOffsetX + 'px';
			}
			if( Wow.UI.DragAndDrop.moveY ) {
				Wow.UI.DragAndDrop.dragObject.style.top = event.clientY - Wow.UI.DragAndDrop.dragOffsetY + 'px';
			}
		}
		return false;
	},
};
*******************************************/	

SimpleAjax = function( baseURL, callbackFunc, dataAsXML ) {
	this.ajax = null;	
	this.baseURL = baseURL;
	this.response = null;
	this.callback = callbackFunc;
	this.mode = '';
	this.POST = 'POST';
	this.GET = 'GET';
	this.asynchronus = true;
	this.data = null;
	this.contentType = 'application/x-www-form-urlencoded';
	this.xmlData = ( null != dataAsXML ) ? dataAsXML : true;
	
	//Events.addEvent( this.ajax, 'readystatechange', this.onReadyStateChange );
};

SimpleAjax.prototype.onReadyStateChange = function( event ) {
	//event = Wow.Events.getEvent(event);
	if ( 4 == this.ajax.readyState && 200 == this.ajax.status ) {
		if( null != this.callback ) {
			if( this.xmlData ) {
				this.callback( this.ajax.responseXML );
			} else {
				this.callback( this.ajax.responseText );
			}
		}
	
	}
};

SimpleAjax.prototype.createAjax = function() {
	try {
		this.ajax = new XMLHttpRequest();
	} catch( e_xmlhttprequest ) {
		try {
			this.ajax = new ActiveXObject('Msxml2.XMLHTTP');
		} catch( e_msxml2 ) {
			try {
			} catch( e_msxml ) {
				this.ajax = new ActiveXObject( 'Microsoft.XMLHTTP' );
			}
		}
	}
	var loader = this;
	this.ajax.parent = this;
	this.ajax.onreadystatechange =  function( event ) {
		event = Wow.Events.getEvent( event );
		event.parent.onReadyStateChange.apply( event.parent, arguments ); 
	};
	delete loader;
};

SimpleAjax.prototype.send = function( url ) {
	if( null === url ) { return false; }
	this.createAjax();
	//this.ajax.overrideMimeType('text/xml');
	this.ajax.open( this.mode, this.baseURL + url, this.asynchronus );
	if ( this.mode.toUpperCase() == this.POST.toUpperCase() ) {
		this.ajax.setRequestHeader( 'Content-Type', this.contentType );
	}
	this.ajax.send( this.data );
	return true;
};

Wow.Events.onLoad( Wow.UI.Layover.onLoad );
Wow.Events.onLoad( Wow.UI.Editable.onLoad );
Wow.Events.onLoad( Wow.UI.Fade.onLoad );
//Wow.Events.onLoad( Wow.UI.DragAndDrop.onLoad );
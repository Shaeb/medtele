Application = {};


Application.Login = {
	ajax: null,
	trigger: 'lbx_build_login_button',
	
	onLoad: function() {
		Application.Login.ajax = new Ajax( 'http://localhost', Application.Login.callback, false );
		var trigger = document.getElementById( Application.Login.trigger );
		
		if ( trigger ) {
			Wow.Events.addEvent( trigger, 'click', Application.Login.onClick );
		}
		
		delete trigger;
	},
	
	onClick: function() {
		Application.Login.ajax.mode = 'GET';
		Application.Login.ajax.send( '/user/nathanfl' );
	},
	
	callback: function( data ) {
		alert( data );
	}
};

Application.Mar = {};

Application.Mar.Administer = {
	messageBox: null,
	medicationsToAdminister: new Array(),
	patientId: null,
	ajax: null,
	
	administerAndSaveOnClick: function( x ) {
		var event = Wow.Events.getEvent( x );
		if( event == null ) {
			return;
		}
		
		var patientId = document.getElementById('mar_administer_patientId');
		patientId = patientId.value;
		var query = '?userId=1&patientId=' + patientId + '&medications=';
		var length = Application.Mar.Administer.medicationsToAdminister.length;
		var i = 0;
		var med = null;
		for( i; i < length; (++i)){
			med = Application.Mar.Administer.medicationsToAdminister[ i ];
			if( med.getAttribute( 'medicationId' ) ) {
				query += med.getAttribute( 'medicationId' );
				if( i != (length-1) ){
					query += ',';
				}	
			}
		}
		alert( Application.Mar.Administer.medicationsToAdminister );
		ajax = new SimpleAjax( 'http://localhost', Application.Mar.Administer.onCallback, false );
		ajax.mode = 'GET';
		ajax.send.apply( ajax, [ '/chart/resources/bin/ajax/administerMedication.php' + query ] );
		
		delete event;
		delete patientId;
		delete query;
		delete length;
		delete i;
	},
	
	onCallback: function( data ) {
		var length = Application.Mar.Administer.medicationsToAdminister.length;
		var i = 0;
		var med = null;
		for( i; i < length; (++i)){
			med = document.getElementById(Application.Mar.Administer.medicationsToAdminister[ i ].id + '_container' );
			Wow.UI.Fade.fade(med, 1000);
			//med.parentNode.removeChild( med ); // need a post fade event proc
		}
		Application.Mar.Administer.messageBox.reset();
		//Wow.UI.Fade.onActivate( x );
		Wow.UI.hide( Application.Mar.Administer.messageBox.messageBox );
		Application.Mar.Administer.medicationsToAdminister = new Array();
	},
	
	administerOnClick: function( x ) {
		
		var event = Wow.Events.getEvent( x );
		
		var drug = null;
		var dosage = null;
		var medicationId = null;
		var message = '';
		
		if( event.getAttribute( 'dosage' ) ) {
			dosage = event.getAttribute( 'dosage' );
			message = 'Administering ' + dosage;
		}
		if( event.getAttribute( 'drug' ) ) {
			drug = event.getAttribute( 'drug' );
			message += ' of ' + drug + '.';
		}
		if ( event.getAttribute( 'medicationId' ) ) {
			Application.Mar.Administer.medicationsToAdminister.push( event );
		} else {
			message = 'Drug not available.';
		}

		if( drug && dosage ) {
			Application.Mar.Administer.messageBox.addMessage( message );
			Wow.UI.show( Application.Mar.Administer.messageBox.messageBox );
		}
		x.preventDefault();
		delete drug;
		delete dosage;
		delete medicationId;
		delete message;
		delete event;
	},
	
	fadeCallBack: function( target ) {
		if( target ) {
			target.parentNode.removeChild( target );
		}
	},
		
	onLoad: function( className ) {
		if ( !className ) {
			return false;
		}
		Wow.UI.Fade.callback = Application.Mar.Administer.fadeCallBack;
		var elements = Wow.DOM.getElementsByClassName( className );
		var i = 0;
		for( i; i < elements.length; (++i) ) {
			if( elements[ i ] ) {
				Wow.Events.addEvent( elements[ i ], 'click', Application.Mar.Administer.administerOnClick );
			}
		}
		delete elements;
		delete i;
		
		var save = document.createElement( 'input' );
		save.type = 'button';
		save.id = 'action_administer_save';
		save.value = 'Administer Medication(s)';
		save.className = 'fadeTrigger';
		save.setAttribute( 'fadeTarget', 'mar_message' );
		save.setAttribute( 'fadeSpeed', '2000' );
		
		Wow.Events.addEvent( save, 'click', Application.Mar.Administer.administerAndSaveOnClick );
		
		var message = new Wow.UI.MessageBox( 'mar_message', 'message hide', save );
		Application.Mar.Administer.messageBox = message;
		
		delete message;
		delete save;
	}
};

Application.Mar.AutoSuggest = {
	// id, value, info
	onComplete: function( x ) {
		var checkbox = document.createElement( 'input' );
		checkbox.type = 'checkbox';
		checkbox.value = x.id;
		checkbox.checked = true;
		
		var label = document.createElement( 'label' );
		label.innerHTML = x.info;
		label.for = checkbox.id;
		label.appendChild( checkbox );
		
		var li = document.createElement( 'li' );
		Wow.UI.Styles.addClass( li, 'highlight' );
		li.appendChild( label );
		
		var list = document.getElementById( 'addMedicationList' );
		list.appendChild( li );
		
		delete li;
		delete label;
		delete checkbox;
	}
};

// version 1, all forms have to be in a row
// this is HIGHLY specific to my current application
Application.FormWizard = {
	steps: [],
	
	onLoad: function() {
		Application.FormWizard.steps = Wow.DOM.getElementsByClassName( 'formWizard' );
		var i = 1;
		var name = '';
		var submit = null;
		for( i; i <= Application.FormWizard.steps.length; (++i) ) {
			name = 'step_' + i + '_submit';
			submit = document.getElementById( name );
			if ( submit ) {
				Wow.Events.addEvent( submit, 'click', Application.FormWizard.onClick );
			}
		}
		delete i;
		delete name;
		delete submit;
	},
	
	onClick: function( e ) {
		var event = Wow.Events.getEvent( e );
		
		if ( !event ) { 
			return false;
		}
		
		var parent = event.form;
		var nextStep = 0;
		var newForm = null;
		
		if( !parent.getAttribute( 'step' ) ) {
			return false;
		}
		
		var nextStep = parent.getAttribute( 'step' );
		newForm = Application.FormWizard.steps[ nextStep ];
		
		if( !newForm ) {
			if ( 'true' == event.getAttribute( 'restart' ) ) {
				newForm = Application.FormWizard.steps[ 0 ];
			} else {
				return false;
			}
		}
		
		Wow.UI.hide( parent );
		Wow.UI.show( newForm );
		
		Wow.Events.stop( e );
	}
};

// steps is an array of ids
// triggers is an array of ids
// steps[ 0 ] = triggers[ 0 ] to render steps[ 1 ]...
// onComplete means custom event handler, will be called when last step is submitted
var FormWizard = function( steps, triggers, onComplete ) {
	this.steps = new Array();
	this.triggers = new Array();
	this.onComplete = null;
	
	if ( onComplete ) {
		this.onComplete = onComplete; // expecting an array
	} else {
		this.onComplete = function( event ) { Wow.Events.stop( event ); };
	}
	
	var i = 0;
	var element = null;
	for( i; i < steps.length; (++i) ) {
		element = null;
		element = document.getElementById( steps[ i ] ) ;
		if( element != null ) {
			this.steps.push( element );
		}
	}
	
	i = 0;
	for( i; i < triggers.length; (++i) ) {
		element = null;
		element = document.getElementById( triggers[ i ] ) ;
		if( element!= null ) {
			Wow.Events.addEvent( element, 'click', this.onClick );
			this.triggers.push( element );
		} else {
			alert( 'not ataching: ' + i );
		}
	}
	delete i;
	delete element;
};

FormWizard.prototype.onLoad = function() {
	var i = 0;
	var length = this.triggers.length - 1;
	for( i; i <= length; (++i) ) {
		Wow.Events.addEvent( this.triggers[ i ], 'click', this.onClick );
	}
	delete i;
	delete length;
};

FormWizard.prototype.alert = function() { alert( this.triggers.length ); };

FormWizard.prototype.onClick =  function( e ) {
	alert( this );
	var trigger = Wow.Events.getEvent( e );

	if ( !trigger ) { 
		return false;
	}
	alert( this.triggers.length );

	var i = 0;
	var length = this.triggers.length - 1;
	for( i; i <= length; (++i) ) {
		if ( this.triggers[ i ].id == trigger.id ) {
			break;
		}
	}
	
	if ( i != length ) {
		// assumption: parent form is 2 levels higher
		var parent = trigger.form;
		var newForm = this.steps[ i ];
		
		Wow.UI.hide( parent );
		Wow.UI.show( newForm );
		
		delete parent;
		delete newForm;
	} else {
		this.onComplete( e );
	}

	Wow.Events.stop( e );
};

function addAutoSuggest() {
	var options = {
		script: 'resources/bin/ajax/medicationAutoSuggest.php?',
		//varname: 'term', // doesnt seem to work?
		json: false,		
		callback: Application.Mar.AutoSuggest.onComplete
	};
	
	var as = new AutoSuggest( 'medicationSearchName', options );
	delete options;
	/*
	var fw = new FormWizard( [ 'addMedicationStep1', 'addMedicationStep2', 'addMedicationStep3', 'addMedicationStep4' ],
		[ 'step_1_submit', 'step_2_submit', 'step_3_submit', 'step_4_submit' ],
		function(e) { alert( 'called' ); Wow.Events.stop(e); } );
	*/
};

Wow.Events.onLoad( Application.Login.onLoad );
Wow.Events.onLoad( function() { Application.Mar.Administer.onLoad( 'action_administer_message'); } );
Wow.Events.onLoad( Application.FormWizard.onLoad );
Wow.Events.onLoad( addAutoSuggest );

if ( Wow ) {
	Wow.Events.onLoad( function() {
		settings = {
		  tl: { radius: 10 },
		  tr: { radius: 10 },
		  bl: { radius: 10 },
		  br: { radius: 10 },
		  antiAlias: true,
		  autoPad: true
		}
		var myBoxObject = new curvyCorners(settings, "rounded");
		myBoxObject.applyCornersToAll();
	} );
}
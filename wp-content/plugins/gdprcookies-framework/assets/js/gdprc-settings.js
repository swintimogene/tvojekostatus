/*
 * Please see gdprcookies-framework.php for more details.
 * 
 * @author $Author: NULL $
 * @version $Id: gdprc-settings.js 170 2018-02-28 22:56:29Z NULL $
 * @since 1.2
 */
var gdprcSETT = (function($) {	
	""; // fix for yuicompressor
	"use strict";
	var settings = {			
		// declare public params
		currNsData					: {},
		currNsDatal10n				: {},			
		settingsform 				: {},
		settingGrp					: '',
		resetBtn 					: {}, 
		showOnRequestEl 			: {},
		inActiveEl 					: {}, 
		loadingImg 					: {}, 
		togglethis					: {},
		descrIcons					: {},
		done						: false,
	};
		
	// declare private params
	var valSetCookies = '1',
		valPopup 	  = '0',		
		valBar 		  = '1';
	
	// extend 
	var extend = {					
		gdprcTablerows: function() {
			$('tbody tr:even', this).addClass('alternate');
			$('tbody tr:last', this).addClass('last');			
		},	
		showRow: function () {		  	
			var field = $(this);
			
	  		field.parents('td').css({'padding':'', 'min-height':'', 'height':''});
		  	field.parents('td').siblings('th').show();	  	
		},		  
		hideRow: function () {		  	
			var field = $(this);
			
		  	field.parents('td:first').css({'padding-top':'0','padding-bottom':'0', 'min-height':'0', 'height':'0'});		
		  	field.parents('tr:first').addClass('noborder');
		  	// hide title
		  	field.parents('td:first').siblings('th').hide();  	  	
		},		  
		showField: function () {	  	
			var field = $(this);
			
			if(field.is(':disabled')) {
				field.removeAttr('disabled');
			}
			
		  	field.data('active', 'yes');
		  	field.attr('data-active', 'yes');  	
		},		  
		hideField: function () {			
			var field = $(this);
			
		  	field.data('active', 'no');
		  	field.attr('data-active', 'no');
		},
		isCheckbox: function() {
			var el = $(this);
			
			if(!el.length) {
				return false;
			} else if('checkbox' !== el[0].type) {
				return false;
			} else {
				return true;
			}
		},
		isChecked: function() {
			var el = $(this);
			
			if(!el.isCheckbox()) {
				return;
			}
			
			return (el.is(':checked'));			
		}
	}

	/** private methods **/	
	var showRows = function () {		  
		$(this).showRow();  	
	}  
	  
	var hideRows = function () {		  
		$(this).hideRow();	
	}	
	
	/** public methods	**/
	
	settings.showRow = function(el) {
		if('undefined' === typeof el || (el && 0 === el.length)) {
			return;
		}		
		
		var row = el.parents('tr'),
			rowDescr;
		
		if(0 === row.length) {
			return;
		}			
		
		rowDescr = row.next('.description');
		
		row.show().find('input, select').prop('disabled', false);
		rowDescr.show();		
	}

	settings.hideRow = function(el) {
		if('undefined' === typeof el || (el && 0 === el.length)) {
			return;
		}		
		
		var row = el.parents('tr'),
			rowDescr;
		
		if(0 === row.length) {
			return;
		}			
		
		rowDescr = row.next('.description');
		
		row.hide().find('input, select')
		//.prop('disabled', true)
		;
		rowDescr.hide();	
	}	
	
	settings.showHideRowForCheckbox = function($this, el) {
		if(0 === $this.length || 0 === el.length) {
			return;
		}
						
		if($this.isChecked()) {
			this.showRow(el);
		} else {
			this.hideRow(el);
		}		
	}
	
	settings.showGroup = function(show) {
		if(0 === show.length) {
			return;
		}			
		show.prev('h3').show();		
		show.show().find('input, select').prop('disabled', false);		
	}
	
	settings.hideGroup = function(hide) {
		if(0 === hide.length) {
			return;
		}			
		hide.prev('h3').hide();
		hide.hide().find('input, select')
		//.prop('disabled', true)
		;
	}	
	
	settings.showHideGroup = function(show, hide) {		
		if(0 === show.length || 0 === hide.length) {
			return;
		}			
		
		this.showGroup(show);
		this.hideGroup(hide);
	}	
	
	settings.getAjaxArgs = function(action, data) {	
		var namespaceAction = gdprcData['curr_sett_ns'] + '_action',		
			args = {};
		
		args['action'] = 'gdprc-action';
		args[namespaceAction] = action;
		args['data'] = data;
		args['nonce'] = gdprcSETT.currNsData.nonce;
		
		return args;			
	}
	

	settings.handlerCheckboxClick = function (e) {			
		if( !$(this).hasClass('checked') || $(this).hasClass('unchecked') ) {				
			$(this).removeClass('unchecked');
			$(this).addClass('checked');			
		} else {		
			$(this).removeClass('checked');
			$(this).addClass('unchecked');
		}  	  	  	
	}  
  
	settings.handlerResetSettings = function (e) {		
		var thiz = e.data.thiz;
		
		if (confirm(gdprcSETT.currNsDatal10n.reset_confirm)) {			
			thiz.settingsform.submit(function(d) {			
				$('input[gdprc_do_reset]"]').val('1');    		
			
				return true;    		
			});			
		} else {			
			e.preventDefault();
			return false;
		}
	}	
	
	/**
	 * Handler for toggling an element 
	 * 
	 * @param object e
	 * 
	 * @returns void
	 */
	settings.handlerToggle = function(e) {		
		var thiz = e.data.thiz, toggler = $(this);
		
		toggler.next('.togglethis').slideToggle('fast', function() {			
			var togglethis = $(this);
			
			if(togglethis.is(':visible')) {
				$(this).data('toggle-status', 'show');	
				$(this).attr('data-toggle-status', 'show');				
			} else {
				 $(this).data('toggle-status', 'hide');	
				 $(this).attr('data-toggle-status', 'hide');
			}								
		});					
	}	
	
	settings.handlerEachToggleThis = function(e) {		
		var status = $(this).data('toggle-status');
		
		if('hide' === status) {
			$(this).hide();	
		} else {
			$(this).show();
		}
	}	
	
	settings.handlerToggleDescr = function(e) {		
		var thiz = e.data.thiz, toggler = $(this);
		
		toggler.parents('tr').next('tr').find('.togglethis').slideToggle(75, function() {			
			var togglethis = $(this);
			
			if(togglethis.is(':visible')) {
				$(this).data('toggle-status', 'show');	
				$(this).attr('data-toggle-status', 'show');				
			} else {
				 $(this).data('toggle-status', 'hide');	
				 $(this).attr('data-toggle-status', 'hide');
			}								
		});			
	}	
	
	/**
	 * Init all events
	 * 
	 * @returns void
	 */
	settings.events = function() {		
		// reset settings button
		this.resetBtn.click({thiz:this}, this.handlerResetSettings);		
		this.settingsform.on('click', '.toggler', {thiz:this}, this.handlerToggle);		
		this.settingsform.on('click', '.descr-i', {thiz:this}, this.handlerToggleDescr);	
	}
	
	/**
	 * Init the settings logic when the DOM is ready
	 * 
	 * Tasks:
	 * 		- settings params
	 * 		- extending jQuery with methods
	 * 		- callings methods: this.events()
	 * 		- do CSS logic 
	 * 
	 */
	settings.init = function() {		
		try {
			if(null == gdprcData) {
				throw new Error('Missing global param: gdprcData');
			}
			
			var requiredGlobals = ['curr_sett_ns'];		
			
			for(var i in requiredGlobals) {
				var param = requiredGlobals[i];
				if(null == gdprcData[param]) {
					throw new Error('Missing global param: '+param);
				}				
			}

			if('' == gdprcData['curr_sett_ns'] || 'string' !== typeof gdprcData['curr_sett_ns']) {
				throw new Error('Namespace is not valid, value found: '+gdprcData['curr_sett_ns']);
			}			
			
			// namespace dependent global plugin data
			this.currNsData = window[gdprcData.curr_sett_ns + 'Data'];
			this.currNsDatal10n = window[gdprcData.curr_sett_ns + 'Datal10n'];
			
			// init DOM elements
			this.settingsform 				= $('form.gdprc-settings-form'); 
			this.resetBtn 					= $('.gdprc-btn-reset');
			this.showOnRequestEl 			= $('.showonrequest');
			this.inActiveEl 				= $('.in-active');
			this.loadingImg 				= $('img.ajax-loading');
			this.togglethis					= $('.togglethis');		
			this.descrIcons					= $('.descr-i');	
			
			// setting name
			this.settingGrp = this.settingsform.data('setting-group');
			
			// extend the jQuery with methods stored in param "extend"
			$.fn.extend(extend);	
			
			// init all events
			this.events();
				
			// hide rows that we will show later on request
			$.each(this.showOnRequestEl, hideRows); 
			$.each(this.inActiveEl, hideRows);
			
			// init toggle elements
			this.togglethis.each(this.handlerEachToggleThis);		
			
			// set textarea CSS max-width to not exceed the table
			$('textarea').css( 'max-width', $('textarea').parent('td').innerWidth()+'px' );	
			
			this.done = true;
			
		} catch (exc) {			
			this.done = false;
			
			if(null != console && null != console.log) {
				console.log('gdprcookies Exception: ', exc.message);
			}
		}		
	}	
	
	return settings;	
	
})(jQuery || {}, gdprcSETT || {});

jQuery(function($) {	
	// call init method when DOM is ready	
	gdprcSETT.init();	
});
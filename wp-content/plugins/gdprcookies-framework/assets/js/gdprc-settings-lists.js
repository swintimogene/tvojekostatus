/**
  * Please see gdprcookies-framework.php for more details.
  * 
  * @author $Author: NULL $
  * @version $Id: gdprc-settings-lists.js 170 2018-02-28 22:56:29Z NULL $
  * @since 1.2
  */
var gdprcSETTLISTS = (function($) {	
	""; // fix for yuicompressor
	"use strict";
	var settlists = {			
		// declare public params
		pageWrap			: {},
		listWrapper			: {},
		btnPostAdd 			: {},
		btnPostDel			: {},
		btnPostSave			: {},
		listRowsTable 		: {},
		listFieldTitle		: {},
		listFieldMetaItem	: {},
	};
		
	// declare private params
	var	_classInputPostNew			= 'gdprc-list-new-post',
		_classListRowsTable			= 'gdprc-list-rows-table',
		_classListRow		 		= 'gdprc-list-row',
		_classListRowFields	 		= 'gdprc-list-row-fields',
		_classListRowfield	 		= 'gdprc-list-row-field',
		_classListRowFieldTitle		= 'gdprc-list-row-field-post-title',
		_classListRowFieldMetaItem  = 'gdprc-list-row-field-meta-item',		
		_classListRowActions 		= 'gdprc-list-row-actions',
		_classListRowAction  		= 'gdprc-list-row-action',
		_classBtnAdd				= 'gdprc-btn-add',
		_classBtnPostAdd			= 'gdprc-btn-add-post',
		_classBtnPostDel			= 'gdprc-btn-del',
		_classBtnPostSav			= 'gdprc-btn-save',
		_classFieldSelected			= 'gdprc-list-selected-post',
		_classListRowSelected		= 'gdprc-list-row-selected';		
	
			
	// extend jQuery
	var extend = {			
		/**
		 * Indicate a list item as 'last' by adding a CSS class "last"
		 * 
		 * @returns void
		 */				
		setLastInList: function() {			
			$(this).find('tr').removeClass('last');			
			$(this).find('tr:last').addClass('last');			
		},		
		/**
		 * Show or hide the AJAX loading image
		 * 
		 * @param bool show true or false to indicate
		 * 
		 * @returns void
		 */			
		setLoadingImg: function(show) {			
			var el = $(this).siblings('.ajax-loading');
			
			if(el && 0 < el.length) {		
				if(show) {
					el.css({'visibility':'visible', 'display':'inline-block'}).show();
				} else {
					el.css('visibility','hidden').hide();
				}
			}			
		},
		
		/**
		 * Get Post tr element by ID
		 * 
		 * @param int id the Post ID
		 * 
		 * @returns the DOM element or bool false if no tr is found
		 */			
		getPostRow: function (id) {			
			var listRowsTable = $(this),			
				tr = listRowsTable.find('tr[data-post-id="'+id+'"]');
			
			if(0 < tr.length) {
				return tr;
			} else {
				 return false;
			}		
		},		
		/**
		 * Get the Post ID from given row
		 * 
		 * @returns void
		 */			
		getPostId: function () {			
			var el = $(this);
			
			if('undefined' !== typeof el.data('post-id')) {			
				return el.data('post-id');
			} else {
				return false;
			}
		},		
		/**
		 * Delete post row from list
		 * 
		 * @param int postId the Post ID of the Property
		 * 
		 * @returns void
		 */			
		deletePost: function (postId) {			
			var listRowsTable = $(this);
			
			listRowsTable.find('tr[data-post-id="'+postId+'"]').remove();
		}
	}
	
	/** private methods **/ 
	
	/**
	 * Get the base of an input name attribute when in array format 
	 * 
	 * Array format like <input name="myname[123]" />. 
	 * The regex support 1 level only, so NOT <input name="myname[123][anothername]" />
	 * 
	 * @param string name value of name attribute 
	 * 
	 * @returns bool false if no name is found or string the base name
	 */		
	var getInputBaseName = function (name) {
		var patt, m;
		
		patt = /([a-zA-Z_-]+)\[\d+\]/;
		m = patt.exec(name);			

		if(null != m && null != m[1]) {				
			return m[1];
		} else {
			return false;
		}					
	}	
	
	var getInputNameSubKey = function (name) {
		var patt, m;
		
		patt = /[a-zA-Z_-]+(?:\[[^\]]+\])\[\d+\](?:\[([a-zA-Z_-]+)\])/;
		m = patt.exec(name);			
		
		if(null != m && null != m[1]) {				
			return m[1];
		} else {
			return false;
		}		
	}
	
	/**
	 * Determine if current event is a right mouse click
	 * 
	 * @returns bool
	 */
	var isMouseClickRight = function (e) {
		if('undefined' !== typeof e.button && 2 === e.button) {			
			return true;
		} else {
			return false;
		}
	}
	
	/** public methods **/ 		
	
	/**
	 * Handler for adding a new Post
	 * 
	 * if Property value is NOT empty, an AJAX call is made with gdprc-action 'gdprc-add-post'
	 * 
	 * @param object e
	 * 
	 * @returns void on adding or bool false when NOT adding
	 */		
	settlists.handlerBtnPostAdd = function (e) {		
		var thiz = e.data.thiz, 
			btn = $(this),
			wrapper = btn.parents('.gdprc-list-wrapper'),
			inputNewPost = wrapper.data('listFieldNewPost'),
			val = inputNewPost.val();
						
		if( '' !== val ) {			
			btn.parent('.gdprc-list-controls').hide().setLoadingImg(true);
			
			var hasTitle = false, 
				hasMedia = false,
				canDel = false,
				canSave = false,				
				postType = null,
				context = null;

			hasTitle 	= ( null != wrapper.data('has-title') &&  true == wrapper.data('has-title') ) ? true : hasTitle;
			hasMedia 	= ( null != wrapper.data('has-media') &&  true == wrapper.data('has-media') ) ? true : hasMedia;
			canDel		= ( null != wrapper.data('can-del') &&  true == wrapper.data('can-del') ) ? true : canDel;
			canSave 	= ( null != wrapper.data('can-save') &&  true == wrapper.data('can-save') ) ? true : canSave;			

			postType 	= ( null != wrapper.data('post-type') && 'string' === typeof wrapper.data('post-type') ) ? wrapper.data('post-type') : '';
			context 	= ( null != wrapper.data('context') && 'string' === typeof wrapper.data('context') ) ? wrapper.data('context') : '';
						
			var args = gdprcSETT.getAjaxArgs( 'gdprc-add-post', {
				val:val, 
				context:context, 
				post_type:postType, 
				has_title:hasTitle, 
				has_media:hasMedia,
				can_del:canDel,
				can_save:canSave
				});
			
			$.post(ajaxurl, args, function(r) {				
				if(null == r) {
					alert(gdprcSETT.currNsDatal10n.unknown_error);
					btn.parent('.gdprc-list-controls').show().setLoadingImg(false);	 
					return false;
				}					
				
			  	switch(r.state) {
			  		case '-1':
			  			alert(r.out);
			  		case '1':			  			
			  			wrapper.addClass('gdprc-list-has-posts');
			  			wrapper.data('listRowsTable').append(r.out.template);
			  			
			  			// let others hook into this state
			  			wrapper.trigger('gdprcBtnPostAdd', [thiz, r, wrapper]);
		  			
			  			inputNewPost.val('');
			  			btn.parent('.gdprc-list-controls').show().setLoadingImg(false);	  			
				    break;		    		
			  	}				
			}, 'json');			
		} else {
			e.preventDefault();
			return false;				
		}	
	}	
	
	/**
	 * Handler for deleting a Post
	 * 
	 * if deleting is needed, an AJAX call is made with gdprc-action 'del-prop'
	 * 
	 * @param object e
	 * 
	 * @returns void on deleting or bool false when canceling (by client)
	 */		
	settlists.handlerBtnPostDel = function (e) {		
		var thiz = e.data.thiz, 
			btn = $(this), 
			wrapper = btn.parents('.gdprc-list-wrapper'),
			tr = btn.parents('tr'), 
			postId, 
			toDelete = [];		
		
		postId = tr.getPostId();		
		toDelete.push(postId);	
		
		if(0 < toDelete.length) {			
			if (confirm(gdprcSETT.currNsDatal10n.del_confirm_post)) {				
				btn.hide().setLoadingImg(true);
				var args = gdprcSETT.getAjaxArgs( 'gdprc-del-post', {post_id:toDelete} );
				
				$.post(ajaxurl, args, function(r) {					
					if(null == r) {
						alert(gdprcSETT.currNsDatal10n.unknown_error);
						btn.show().setLoadingImg(true);
						return false;
					}					
					
				  	switch(r.state) {
				  		case '-1':
				  			alert(r.out);
				  		case '1':			  				
			  				if( -1 !== r.out.deleted.indexOf(String(postId)) ) {			  					
			  					$.when( wrapper.data('listRowsTable').deletePost(postId) ).then( function() {			  						
			  						wrapper.trigger('gdprcBtnPostDel', [thiz, r, postId]);			  					
			  					});
			  					
			  					if( 0 === wrapper.data('listRowsTable').find('tr').length ) {
			  						wrapper.removeClass('gdprc-list-has-posts');
			  					}			  					
			  				}				  			
			  
				  			btn.show().setLoadingImg(true);
					    break;		    		
				  	}				
				}, 'json');				
			}	else {
				e.preventDefault();
				return false;				
			}	 
		}			
	}		
	
	/**
	 * Handler for saving Post data
	 * 
	 * if saving is needed, an AJAX call is made with gdprc-action 'update-post'
	 * 
	 * @param object e
	 * 
	 * @returns void
	 */	
	settlists.handlerBtnPostSave = function (e) {
		// prevent right mouse click
		if(isMouseClickRight(e)) {
			e.preventDefault();
			return false;
		}
		
		var thiz = e.data.thiz, 
			btn = $(this),
			wrapper = btn.parents('.gdprc-list-wrapper'),
			tr, 
			inputPostTitle,
			postId, 
			val, 
			postMeta = {}, 
			hasEmptyPost, 
			hasEmptyPostMeta,		
			extraData = {},
			grpMeta = false,
			grpMetaKey = false,
			context = null;
		
		tr = btn.parents('tr.'+_classListRow);		
		inputPostTitle = tr.find('.'+_classListRowFieldTitle);
		postId = tr.getPostId();
		val = inputPostTitle.val();
		
		grpMeta = ( null != wrapper.data( 'group-meta' ) &&  true == wrapper.data( 'group-meta' ) ) ? true : grpMeta;		
		if(grpMeta) {
			grpMetaKey = ( null != wrapper.data( 'group-meta-key' ) ) ? wrapper.data( 'group-meta-key' ) : grpMetaKey;
		}
		
		hasEmptyPost = ( '' ===  val ) ? true : false;		
		context = ( null != wrapper.data('context') && 'string' === typeof wrapper.data('context') ) ? wrapper.data('context') : '';
				
		var emptyPostMeta;		
		tr.find('.'+_classListRowFieldMetaItem).each(function() {			
			var input = $(this), 
				name, 
				type, 
				val, 
				strippedName, 
				subKey;
			
			name = input.attr('name');
			type = input.attr('type');
			val = input.val();
			
			//if( 'hidden' === type )
			//	return true; //continue
			
			if( '' ===  val ) {				
				emptyPostMeta = input;
				hasEmptyPostMeta = true; return false;
			}
			
			if( !grpMeta ) {
				strippedName = getInputBaseName(name);		
				if(false !== strippedName) {				
					postMeta[strippedName] = val;	
				}				
			} else {			
				// all meta data should be passed to one meta_key
				if(false !== (subKey = getInputNameSubKey(name))) {				
					postMeta[subKey] = val;					
				}
			}
		});
		
		// give possibilty to add data 
		btn.trigger('gdprcBeforePostSave', [thiz, postId, tr] );		
		extraData = btn.data('extra-data') || {};		
		postMeta = $.extend( postMeta, extraData );
		
		if(hasEmptyPost || hasEmptyPostMeta) {
			if(hasEmptyPost) { 
				inputPostTitle.trigger('blur'); 
			}
			if(hasEmptyPostMeta) { 
				emptyPostMeta.trigger('blur'); 
			}
			
			alert(gdprcSETT.currNsDatal10n.post_empty);

			return false;
		}
				
		inputPostTitle.val(gdprcSETT.currNsDatal10n.updating);
		btn.hide().setLoadingImg(true);
		
		var args = gdprcSETT.getAjaxArgs( 'gdprc-update-post', {
			context:context,
			post_id:postId, 
			val:val, 
			post_meta:postMeta, 
			group_meta:grpMeta, 
			group_meta_key:grpMetaKey
			});	
		
		$.post(ajaxurl, args, function(r) {			
			if(null == r) {
				alert(gdprcSETT.currNsDatal10n.unknown_error);
				btn.show().setLoadingImg(false);	
				return false;
			}	
			
		  	switch(r.state) {
		  		case '-1':
		  			alert(r.out);
		  			inputPostTitle.val(val);
		  			btn.show().setLoadingImg(false);
		  			break;
		  		case '0':
		  			if( null != r.out.msg ) {
		  				alert(r.out.msg);	
		  			}
		  			if( null != r.out.fields && $.isArray(r.out.fields) && 0 < r.out.fields.length ) {		  				
		  				for(var i=0; i<r.out.fields.length; i++) {
		  					var field = tr.find('.field-'+r.out.fields[i]);
		  					field.find('.'+_classListRowFieldMetaItem).addClass('gdprc-sett-field-err');
		  				}
		  			}
		  			inputPostTitle.val(val);
		  			btn.show().setLoadingImg(false);	
		  			break;
		  		case '1':
		  			tr.find('.gdprc-sett-field-err').removeClass('gdprc-sett-field-err');
		  			inputPostTitle.val(val);
		  			inputPostTitle.prop('defaultValue', val);
		  			
		  			if(r.out.meta_added) {		  			
			  			// update meta val
			  			$.each(r.out.meta_added, function(name, value) {			  				
			  				var postMetaInput = tr.find('input.gdprc-list-row-field-meta-item[name="'+name+'['+postId+']"]');
			  				postMetaInput.attr('value', value);	  
			  				postMetaInput.prop('defaultValue', value);
			  			});			  			  			
		  			}	
		  			
		  			if(r.out.changed) {
		  				//
		  			}
		  			
		  			wrapper.trigger('gdprcBtnPostSave', [thiz, postId, tr, r, val]);		  			
		  			btn.show().setLoadingImg(false);		  			
			    break;		    		
		  	}				
		}, 'json');			
	};	

	settlists.handlerRowClick = function(e) {		
		var thiz = e.data.thiz, 
			tr = $(this), 
			wrapper = tr.parents('.gdprc-list-wrapper'),
			trs = wrapper.find('.'+_classListRow),
			postId = tr.getPostId(),
			clickSelectEl,
			toSelect = [];
		 
		trs.removeClass(_classListRowSelected);
		tr.addClass(_classListRowSelected).focus();
		
		clickSelectEl = wrapper.find('.'+_classFieldSelected);
		clickSelectEl.val(postId);
	}	
	
	/**
	 * Init all events
	 * 
	 * @returns void
	 */
	settlists.events = function() {
		
		var thiz = this; // {thiz:this}		
		
		this.listWrappers.each(function() {			
			var listWrapper 	= $(this),
				clickSelect 	= ( null != listWrapper.data('click-select') && true == listWrapper.data('click-select') ) ? true : false,
				context			= ( null != listWrapper.data('context') && 'string' === typeof listWrapper.data('context') ) ? listWrapper.data('context') : '',		
				clickSelectEl 	= {};
			
			listWrapper.data('btnPostAdd', listWrapper.find('.'+_classBtnPostAdd));
			listWrapper.data('btnPostDel', listWrapper.find('.'+_classBtnPostDel));
			listWrapper.data('btnPostSave', listWrapper.find('.'+_classBtnPostSav));
			listWrapper.data('listFieldNewPost', listWrapper.find('.'+_classInputPostNew)),
			listWrapper.data('listRowsTable', listWrapper.find('.'+_classListRowsTable));
			listWrapper.data('listFieldTitle', listWrapper.find('.'+_classListRowFieldTitle));
			listWrapper.data('listFieldMetaItem', listWrapper.find('.'+_classListRowFieldMetaItem));

			if(clickSelect && '' != context) {				
				var settingGrp = gdprcSETT.settingGrp,					
					nameAttr = settingGrp+'['+context.replace('-', '_')+'_selected]';
				
				clickSelectEl = listWrapper.find('.'+_classFieldSelected);
				clickSelectEl.attr({'name':nameAttr});
				
				// click on the row, make it selected
				listWrapper.on('click', '.'+_classListRow, {thiz:thiz}, thiz.handlerRowClick);
			}
			
			listWrapper.find('input[type="text"], .'+_classBtnAdd).on('keypress', function(e) {			
				if (13 === (e.keyCode || e.which)) {				
					e.preventDefault();					
				};			
			});
			
			// list actions (add, delete, save)		
			listWrapper.on('click', '.'+_classBtnAdd, {thiz:thiz}, thiz.handlerBtnPostAdd);
			listWrapper.on('click', '.'+_classBtnPostDel, {thiz:thiz}, thiz.handlerBtnPostDel);
			listWrapper.on('mousedown', '.'+_classBtnPostSav, {thiz:thiz}, thiz.handlerBtnPostSave);		
		});	
		
		// trigger btn add when hitting 'Enter' key on input text or add btn
		this.pageWrap.on('keypress', '.'+_classInputPostNew+',.'+_classBtnAdd, {thiz:thiz}, function(e) {			
			if (13 === (e.keyCode || e.which)) {				
				$(this).closest(thiz.listWrappers).find('.'+_classBtnAdd).trigger('click');				
			};			
		});
		
		// blur, keydown events
		this.pageWrap.on('blur', '.'+_classListRowFieldTitle, function(e) {			
			var input = $(this);
			
			if( '' === input.val() ) {
				input.val( input.prop('defaultValue') );
			}
		});	
		
		// EVENTS ON PROP META INPUT FIELDS
		this.pageWrap.on('keydown', '.'+_classListRowFieldMetaItem, function(e) {			
			var input = $(this);
			
			if( input.val() === input.prop('placeholder') ) {
				input.prop('value', null);	
			}
		});		
		
		this.pageWrap.on('blur', '.'+_classListRowFieldMetaItem, function(e) {			
			var input = $(this);
			
			if( '' === input.val() ) {
				input.val( input.prop('defaultValue') );
			}
			
			if(input.val() === input.prop('placeholder')) {}			
		});
	};
	
	settlists.setCSS = function() {
		
		// loop trough all lists
		this.listWrappers.each(function() {
			var wrapper 		= $(this),
				rowActionsEls	= wrapper.find('.'+_classListRowActions),
				isCompactLayout = ( null != wrapper.data('layout') && 'compact' === wrapper.data('layout') ) ? true : false,
				outerW			= ( null != wrapper.data('outer-w') && 0 < parseInt(wrapper.data('outer-w')) ) ? parseInt(wrapper.data('outer-w')) : false,
				canSave			= ( null != wrapper.data('can-save') && true == wrapper.data('can-save') ) ? true : false;
							
			rowActionsEls.each(function() {								
				var	rowActionsEl 	= $(this),
					parentRow 	 	= rowActionsEl.parent('.'+_classListRow),
					rowFieldTitle	= parentRow.find('.'+_classListRowFieldTitle), 
					rowFieldsEl		= parentRow.find('.'+_classListRowFields), 
					rowFields		= rowFieldsEl.find('.'+_classListRowfield),
					numFields		= rowFields.length,
					hasFields 		= (0 < numFields) ? true : false,
					widthFields		= null,
					hasRowActions	= ( 0 < rowActionsEl.find('span.'+_classListRowAction).length ) ? true : false;				
				
				if(isCompactLayout && hasFields) {
					widthFields = (100/numFields).toFixed(2);					
					if(0 < outerW) {
						parentRow.css({'width':outerW+'%'});
					}
				}				
				if(!canSave) {
					rowFieldTitle.prop('readonly', true);
				}
				
				if(hasFields) {
					rowFields.css({
						'width':widthFields+'%'						
						});
				}				
				if(!hasRowActions) {
					rowFieldsEl.css({
						'float':'none', 
						'width':'100%'
							});
					rowActionsEl.css({'display':'none', 'float':'none', 'width':'0%'});	
				}
				parentRow.show();
			});			
		});		
	}	

	/**
	 * Init the logic when the DOM is ready
	 * 
	 * Tasks:
	 * 		- settings params
	 * 		- extending jQuery with methods 
	 *		- callings method: this.events()
	 *		- callings method: this.setCSS()
	 */
	settlists.init = function() {		
		try {			
			if( false === gdprcSETT.done ) {
				throw new Error('Could not init gdprc Setting Lists: gdprcSETT is not finished.');
			}
			
			// init DOM elements				
			this.pageWrap		= $('.wrap');
			this.listWrappers	= $('.gdprc-list-wrapper');
			this.btnPostAdd		= $('.'+_classBtnPostAdd);
									
			// extend the jQuery with methods stored in param "extend"
			$.fn.extend(extend);
			
			// init all events
			this.events();		
			
			// markup
			this.setCSS();
			
		} catch (exc) {
			if(null != console && null != console.log) {
				console.log('gdprcookies Exception: ', exc.message);
			}
		}			
	}		
	
	return settlists;
	
})(jQuery || {}, gdprcSETTLISTS || {}); 

jQuery(function($) {	
	// call init method when DOM is ready
	gdprcSETTLISTS.init();	
});
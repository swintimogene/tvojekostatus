/*
 * Please see gdprcookies-framework.php for more details.
 * 
 * @author $Author: NULL $
 * @version $Id: gdprc-notices.js 71 2015-08-11 21:57:59Z NULL $
 * @since 1.1.8
 */
jQuery(function($) {  
	
	var msgSelector = ['#setting-error-gdprc_updated', '#setting-error-gdprc_error', '.error', '.updated', '.gdprc-updated', '.gdprc-error', '.gdprc-notice', '.update-nag'];
	var noticeClass = 'gdprc-notice-msg';
	var noticeHolderSelector = '#notice-holder';
		
	// add the specific class 
	$.each(msgSelector, function(k,v) {	
		
		$(v).addClass(noticeClass);
		
		if('.update-nag' === v) { 
			$(v).css({'margin':'0'});
		}
	});	
 
	// append WP and non WP messages to the notice holder
	$('.'+noticeClass).appendTo($(noticeHolderSelector)).css({'display':'block'});
  
  // hide the notice holder if its not an error
  $(noticeHolderSelector+':not(:has(.error)):not(:has(.update-nag))').delay(4000).animate({'height':'0px'}, 333, function(){ $(this).hide();});  
});
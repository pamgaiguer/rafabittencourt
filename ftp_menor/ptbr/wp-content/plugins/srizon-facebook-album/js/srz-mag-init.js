jQuery(document).ready(function() {
	jQuery('.mpjfb').each(function() {
		jQuery(this).magnificPopup({
			delegate: 'a.aimg',
			type: 'image',
			gallery:{enabled:true}
		});
	}); 
});
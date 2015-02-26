jQuery(document).ready(function($){

	// Delete gallery

	$("." + gabfire_cps.prefix + "delete").click(function(){
		gabfire_cps_delete($(this).attr('id').substring(34), $("#"  + gabfire_cps.prefix + "delete_url_" + $(this).attr('id').substring(34)).text());
	});

	// Form submition

	$("." + gabfire_cps.prefix + "form").submit(function(e){

		if ($("#" + gabfire_cps.prefix + "status").val() == '') {
			e.preventDefault();
			$("#" + gabfire_cps.prefix + "status").css('border-color', 'red');
		}
	});
})

function gabfire_cps_delete(message, url) {

	var c = confirm("Are you sure you want to delete: " + message);

	if (c == true) {
		window.location = url;
	}
}
// Code pour le module liste-zones-geo
$(document).bind('zones-geo_charge', function() {
	// Taille de page
	$('#select-nb-par-page').change(function() {
		$(this).closest('form').submit();
	});
})

$(document).ready(function() {
});
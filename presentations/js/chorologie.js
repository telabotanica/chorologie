// Code pour le module liste-zones-geo
$(document).bind('zones-geo_charge', function() {
	// Taille de page
	$('#select-nb-par-page').change(function() {
		$(this).closest('form').submit();
	});
});

// Code pour le module liste-taxons
$(document).bind('liste-taxons_charge', function() {
});

// Code pour le module carte-taxon
$(document).bind('carte-taxon_charge', function() {
});

$(document).ready(function() {
});
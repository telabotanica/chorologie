// Code pour le module liste-zones-geo
$(document).bind('zones-geo_charge', function() {
	chargerSelecteurNbParPage();
});

// Code pour le module liste-taxons
$(document).bind('liste-taxons_charge', function() {
	chargerSelecteurNbParPage();
});

// Code pour le module carte-taxon
$(document).bind('carte-taxon_charge', function() {
	$('svg').find('path').click(function() {
		var idZone = $(this).attr('id');
		var urlBaseListeTaxons = $('#carte').data('url-base-liste-taxons'),
			url = urlBaseListeTaxons + '&zone-geo=' + idZone;
		window.location = url;
	});
});

$(document).ready(function() {
});

function chargerSelecteurNbParPage() {
	// Taille de page
	$('#select-nb-par-page').change(function() {
		$(this).closest('form').submit();
	});
}
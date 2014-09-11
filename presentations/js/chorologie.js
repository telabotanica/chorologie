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
	gererEvenementClicCarte();
});

//Code pour le module carte
$(document).bind('carte-charge', function() {
	gererEvenementClicCarte();
});

$(document).ready(function() {
});

function chargerSelecteurNbParPage() {
	// Taille de page
	$('#select-nb-par-page').change(function() {
		$(this).closest('form').submit();
	});
}

function gererEvenementClicCarte() {
	// En attendant mieux, ça marche tout de même
	// mais ça n'est pas très générique
	
	// carte chorodep
	$('svg').find('path').click(function() {
		var idZone = $(this).attr('id');
		idZone = idZone.replace('INSEE-D', '');
		var urlBaseListeTaxons = $('#carte').data('url-base-liste-taxons'),
			url = urlBaseListeTaxons + '&zone-geo=' + idZone;
		window.location = url;
	});
	
	// carte gentiana
	$('svg').find('polygon').click(function() {
		var idZone = $(this).attr('id');
		idZone = idZone.replace('INSEE-C-', '');
		var urlBaseListeTaxons = $('#carte').data('url-base-liste-taxons'),
			url = urlBaseListeTaxons + '&zone-geo=' + idZone;
		window.location = url;
	});
}
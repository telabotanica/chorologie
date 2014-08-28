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
	
	// carte chorodep
	$('svg').find('path').click(function() {
		var idZone = $(this).attr('id');
		idZone = idZone.replace('INSEE-D', '');
		var titre = $(this).attr('title').split(" ");
		var urlBaseListeTaxons = $('#carte').data('url-base-liste-taxons'),
			url = urlBaseListeTaxons + '&zone-geo=' + idZone + '&nom-zone-geo=' + titre[0];
		window.location = url;
	});
	
	// carte gentiana
	$('svg').find('polygon').click(function() {
		var idZone = $(this).attr('id');
		var titre = $(this).attr('title').split(" ");
		var urlBaseListeTaxons = $('#carte').data('url-base-liste-taxons'),
			url = urlBaseListeTaxons + '&zone-geo=' + idZone + '&nom-zone-geo=' + titre[0];
		window.location = url;
	});
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
		var titre = $(this).attr('title').split(" ");
		var urlBaseListeTaxons = $('#carte').data('url-base-liste-taxons'),
			url = urlBaseListeTaxons + '&zone-geo=' + idZone + '&nom-zone-geo=' + titre[0];
		window.location = url;
	});
	
	// carte gentiana
	$('svg').find('polygon').click(function() {
		var idZone = $(this).attr('id');
		idZone = idZone.replace('INSEE-C-', '');
		var titre = $(this).attr('title').split(" ");
		var urlBaseListeTaxons = $('#carte').data('url-base-liste-taxons'),
			url = urlBaseListeTaxons + '&zone-geo=' + idZone + '&nom-zone-geo=' + titre[0];
		window.location = url;
	});
}
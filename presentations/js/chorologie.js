var jq = jQuery.noConflict();

// Code pour le module liste-zones-geo
jq(document).bind('zones-geo_charge', function() {
	chargerSelecteurNbParPage();
});

// Code pour le module liste-taxons
jq(document).bind('liste-taxons_charge', function() {
	chargerSelecteurNbParPage();
});

// Code pour le module carte-taxon
jq(document).bind('carte-taxon_charge', function() {
	gererEvenementClicCarte();
});

//Code pour le module carte
jq(document).bind('carte-charge', function() {
	gererEvenementClicCarte();
});

jq(document).ready(function() {
});

function chargerSelecteurNbParPage() {
	// Taille de page
	jq('#select-nb-par-page').change(function() {
		jq(this).closest('form').submit();
	});
}

function gererEvenementClicCarte() {
	// En attendant mieux, ça marche tout de même
	// mais ça n'est pas très générique
	
	// carte chorodep
	jq('svg').find('path').click(function() {
		var idZone = jq(this).attr('id');
		idZone = idZone.replace('INSEE-D', '');
		var urlBaseListeTaxons = jq('#carte').data('url-base-liste-taxons'),
			url = urlBaseListeTaxons + '&zone-geo=' + idZone;
		window.location = url;
	});
	
	// carte gentiana
	jq('svg').find('polygon').click(function() {
		var idZone = jq(this).attr('id');
		idZone = idZone.replace('INSEE-C-', '');
		var urlBaseListeTaxons = jq('#carte').data('url-base-liste-taxons'),
			url = urlBaseListeTaxons + '&zone-geo=' + idZone;
		window.location = url;
	});
}
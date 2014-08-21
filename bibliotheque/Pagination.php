<?php
/**
 * Un machin qui aide à paginer des résultats, côté interface
 *
 * @author mathias
 */
class Pagination {

	/**
	 * Calcule le bombre de pages totales d'après le nombre de résultats et la
	 * taille de page souhaitée
	 */
	public static function nombreDePagesSelonResultats($nbResultats, $taillePage) {
		if ($nbResultats == 0 || $taillePage == 0) {
			$nbPages = 0;
		} else {
			$nbPages = intval($nbResultats / $taillePage);
			// "ça dépend", ça dépasse
			if (($nbPages * $taillePage) != $nbResultats) {
				$nbPages++;
			}
		}
		return $nbPages;
	}

	/**
	 * Renvoie un tableau d'index de pages (ou false pour exprimer un fossé si
	 * le nombre de pages est trop grand), pour construire un paginateur simple,
	 * une suite de liens par exemple
	 */
	public static function tableauPages($pageEnCours, $nbPages, $tailleCentre=5) {
		// seul les nombres impairs sont acceptés, pour que ça centre bien : gérer
		// les nombres de pages pairs, c'est chiant et ça sert à rien
		if ($tailleCentre % 2 == 0) {
			$tailleCentre++;
		}

		$demiCentre = intval($tailleCentre / 2);
		// centre + la première + la dernière
		$nombreLiensPrincipaux = $tailleCentre + 2;
		$limiteAffichagePointillesGauche = $demiCentre + 3;
		$limiteAffichagePointillesDroite = $nbPages - $demiCentre - 2;

		$pages = array();
		// trop de pages p/r à la place
		if ($nbPages > $nombreLiensPrincipaux) {
			// Définition des bornes
			//   le centre commence au moins à 2 et finit au plus à $nbPages-1,
			//   sinon c'est plus le centre
			$debCentre = max(2, $pageEnCours - $demiCentre);
			// théorème du député :
			// s'il n'y a plus de place à droite, on tente d'occuper de la place à gauche
			// pour afficher un nombre de pages constant
			if ($pageEnCours >= $nbPages - ($demiCentre + 1)) {
				$debCentre = max(1, $nbPages - ($nombreLiensPrincipaux - 1));
			}
			$finCentre = min($nbPages - 1, $pageEnCours + $demiCentre);
			// même principe que ci-dessus, de l'autre côté
			if ($pageEnCours <= ($demiCentre + 2)) {
				$finCentre = min($nombreLiensPrincipaux, $nbPages - 1);
			}
			// cas où les pointillés ne remplaceraient qu'un lien - autant mettre le lien
			if ($pageEnCours == $limiteAffichagePointillesDroite) {
				$finCentre++;
			}
			if ($pageEnCours == $limiteAffichagePointillesGauche) {
				$debCentre--;
			}

			// enfilage des éléments
			$pages[] = 1;
			// si le bord gauche du centre est à deux d'écart de la première page ou plus, on met des pointillés
			if (($debCentre - 1) > 2) {
				$pages[] = false;
			}
			for ($i = $debCentre; $i <= $finCentre; $i++) {
				$pages[] = $i;
			}
			// si le bord droit du centre est à deux d'écart de la dernière page ou plus, on met des pointillés
			if (($nbPages - $finCentre) > 2) {
				$pages[] = false;
			}
			$pages[] = $nbPages;
		} else {
			for ($i=1; $i <= $nbPages; $i++) {
				$pages[] = $i;
			}
		}

		return $pages;
	}

	public static function rendu() {
		
	}
}
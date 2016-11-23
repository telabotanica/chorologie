<?php

// bricolage pour Papyrus
class PapControleur {
    public static $appControleur;
}

// Le fichier Framework.php du Framework de Tela Botanica doit être appelé avant tout autre chose dans l'application.
// Sinon, rien ne sera chargé.
// L'emplacement du Framework peut varier en fonction de l'environnement (test, prod...). Afin de faciliter la configuration
// de l'emplacement du Framework, un fichier framework.defaut.php doit être renommé en framework.php et configuré pour chaque installation de
// l'application.
// Chemin du fichier chargeant le framework requis
$framework = dirname(__FILE__).'/framework.php';
if (!file_exists($framework)) {
	$e = "Veuillez paramétrer l'emplacement et la version du Framework dans le fichier $framework";
	trigger_error($e, E_USER_ERROR);
} else {
	// Inclusion du Framework
	require_once $framework;
	// Ajout d'information concernant cette application
	Framework::setCheminAppli(__FILE__);// Obligatoire
	Framework::setInfoAppli(Config::get('info'));// Optionnel

	// Vous pouvez ci-dessous commencer le développement de votre application
	// Merci !
	if (Config::get('debogage')) {
		Debug::tailleMemoireScript('Taille mémoire du script :');
	}
	if (Config::get('benchmark_chrono')) {
		Chronometre::chrono("Lancement de Chorologie");
	}
}

// Découplage entre le chargement du JPFramework et l'initialisation de l'appli,
// pour avoir le temps d'écraser la config dans le fichier d'amorçage appelant
function chorologie_initialisation() {
	// Initialisation du controleur principal de l'application
	try {
		$conteneur = Conteneur::getInstance();
		$appControleur = new AppControleur($conteneur);
		PapControleur::$appControleur = $appControleur;
		$appControleur->executer();
	} catch (Exception $e) {
		$message = $e->getMessage()."\nLigne : ".$e->getLine()."\nFichier : ".$e->getFile();
		Debug::printr($message);
	}

	if (Config::get('benchmark_chrono')) {
		Chronometre::chrono("Arrêt de Chorologie");
	}

	return $appControleur;
}
?>
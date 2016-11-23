<?php
/** Inclusion du fichier principal de l'application*/
require_once 'chorologie.php';
restore_error_handler();
restore_exception_handler();
$appControleur = chorologie_initialisation();

$url_css_bootstrap = Config::get('url_css_bootstrap');
$url_jquery = Config::get('url_jquery');
$css_specifique = Config::get('source_donnees');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-style-type" content="text/css" />
		<meta http-equiv="Content-script-type" content="text/javascript" />
		<meta http-equiv="Content-language" content="fr" />

   	 	<title><?php echo $appControleur->getMetaTitre(); ?></title>

		<meta name="description" content="<?php echo $appControleur->getMetaDescription();?>" />
		<meta name="keywords" content="<?php echo $appControleur->getMetaTags();?>" />
		<meta name="revisit-after" content="15 days" />
		<meta name="robots" content="index,follow" />
		<meta name="author" content="Tela Botanica" />

		<!-- OpenGraph pour Facebook, Pinterest, Google+ -->
		<meta property="og:type" content="website" />
		<meta property="og:title" content="Répartition géographique des plantes" />
		<meta property="og:site_name" content="Tela Botanica" />
		<meta property="og:description" content="Consultez la liste des espèces par zone géographique" />
		<meta property="og:image" content="http://resources.tela-botanica.org/tb/img/256x256/carre_englobant.png" />
		<meta property="og:image:type" content="image/png" /> 
		<meta property="og:image:width" content="256" /> 
		<meta property="og:image:height" content="256" />
		<meta property="og:locale" content="fr_FR" />

		<!-- Favicones -->
		<link rel="icon" type="image/png" href="http://resources.tela-botanica.org/tb/img/16x16/favicon.png" />
		<link rel="shortcut icon" type="image/x-icon" href="http://resources.tela-botanica.org/tb/img/16x16/favicon.ico" />

		<!-- CSS -->
		<link href="<?= $url_css_bootstrap ?>" rel="stylesheet" type="text/css" />
		<link href="http://www.tela-botanica.org/sites/commun/generique/styles/commun.css" rel="stylesheet" type="text/css" />
		<link href="http://www.tela-botanica.org/sites/botanique/generique/styles/botanique.css" rel="stylesheet" type="text/css" />
		<link href="presentations/css/chorologie.css" rel="stylesheet" type="text/css" />
		<link href="presentations/css/<?= $css_specifique; ?>.css" rel="stylesheet" type="text/css" />

		<!-- JavaScript -->
		<script type="text/Javascript" src="<?= $url_jquery ?>"></script>
		<script type="text/Javascript" src="presentations/js/chorologie.js"></script>

		<!-- JavaScript et CSS spécifiques au module -->
		<?php echo $appControleur->getEntete(); ?>
	</head>

	<body id="chorologie">

		<div id="zone-principale">
			<div id="zone-botanique" class="zone-haut">
				<h1 id="zone-logo-tela">
					<a href="http://www.tela-botanica.org/site:accueil" title="Retour à l'accueil">
						<img src="http://resources.tela-botanica.org/tb/img/135x102/logo_carre_officiel.png" alt="Tela Botanica"/>
					</a>
				</h1>
				<h2>Le réseau de la botanique francophone</h2>

				<div class="motsclefs">
					<h3>Botanique</h3>
					<h4>
						<span class="mot1">se former</span><span class="cacher">, </span>
						<span class="mot2">identifier</span><span class="cacher">, </span>
						<span class="mot3">plantes sauvages</span>

					</h4>
				</div>
			</div>

			<div id="zone-menu-navigation">
				<ul>
					<li id="menuAccueil" >
						<span ><a href="http://www.tela-botanica.org/site:accueil">Accueil</a></span>
					</li>
					<li id="menuBotanique" >
						<span class="menuHautActif"><a href="http://www.tela-botanica.org/site:botanique">Botanique</a></span>
					</li>
					<li id="menuActualites" >
						<span ><a href="http://www.tela-botanica.org/site:actu">Actualit&eacute;s</a></span>
					</li>
					<li id="menuReseau" >
						<span ><a href="http://www.tela-botanica.org/site:reseau">R&eacute;seau</a></span>
					</li>
					<li id="menuProjets" >
						<span ><a href="http://www.tela-botanica.org/site:projets">Projets</a></span>
					</li>
				</ul>
			</div>

			<div id="zone-gauche">
				<div id="zone-menu-gauche">
					<h1>Simulation de Papyrus pour Chorologie</h1>
					<?php echo $appControleur->getContenuMenu(); ?>
				</div>
				<div id="zone-menu-connexion">
					<form id="form_connexion" class="form_identification" action="http://www.tela-botanica.org/page:accueil_botanique#form_connexion" method="post">
						<fieldset>
							<h3>Identifiez vous</h3>
							<label for="username">Courriel : </label>
							<input type="text"  id="username" name="username" maxlength="80" tabindex="1" value="courriel" />

							<label for="password">Mot de passe : </label>
							<input type="password" id="password" name="password" maxlength="80" tabindex="2" value="mot de passe" />

							<input type="submit" id="connexion" name="connexion" tabindex="4" value="ok" />
							<p class="connectvertpetit"><input type="checkbox" id="persistant" name="persistant" tabindex="3" value="o" />
							<label id="persistant_label" for="persistant">Se souvenir de moi</label></p>
							<div id="colonneDroite"><br />
								<a href="/page:inscription">S'inscrire... </a>
								<p class="connectgris">Devenez telabotaniste et partagez votre passion pour le végétal !</p>
								<p> <a id="lien_inscription" href="http://www.tela-botanica.org/page:inscription?m=f_oubli_mdp">Mot de passe perdu ? </a></p>
							</div>
						</fieldset>
					</form>
				</div>
			</div>

			<div id="zone-droite">
				<div id="bsh"><!-- bsh : bootstrap starts here (limitation de la portée du CSS) -->
					<div id="chorologie">
						<div id="zone_contenu_tete">
							<?php echo $appControleur->getContenuTete(); ?>
						</div>

						<div id="zone_contenu_corps">
							<?php echo $appControleur->getContenuCorps(); ?>
						</div>

						<div id="zone_contenu_pied">
							<?php echo $appControleur->getContenuPied(); ?>
							<div>
								<?php echo $appControleur->getChrono(); ?>
								<?php echo $appControleur->getExceptions(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="bandeauProjets">
				<ul>
				    <li><img height="80" width="58" src="http://www.tela-botanica.org/sites/commun/generique/images/projets_defilants/floraBellissima.jpeg" alt="Illustration DVD Flora Bellissima" />
				    <h3><a href="http://www.tela-botanica.org/page:flora_bellissima"> Flora Bellissima</a></h3>
				    Premier outil d'aide à la reconnaissance des plantes, ce logiciel vous permet d'identifier plus de 1500 plantes de France. 	<br />
				    <a href="http://www.tela-botanica.org/page:flora_bellissima">Plus d'infos</a></li>

				    <li><img src="http://www.tela-botanica.org/sites/commun/generique/images/projets_defilants/partage_80.jpg" alt="illustration quiz bota" />
				    <h3><a href="http://www.tela-botanica.org/actu/article4536.html">Quiz botanique</a></h3>
				    Pour tester vos connaissances en botanique... <br />
				    <a href="http://www.tela-botanica.org/actu/article4536.html">Plus d'infos</a></li>

				    <li><img style="width: 109px; height: 81px;" src="http://www.tela-botanica.org/sites/commun/generique/hetre_120.JPG" alt="Illustration hêtre tortillard" />
				    <h3><a href="http://www.tela-botanica.org/actu/article4467.html">À la recherche des hêtres tortillards</a></h3>
				    Participer au projet de recensement des hêtres tortillards. <a href="http://www.tela-botanica.org/page:liste_projets?id_projet=94">Plus d'infos</a></li>
				</ul>
			</div>

			<div id="zone-bas-page">
				<div>
					<ul>
						<li id="accueil"><a id="menu_lien_Array_759" href="http://www.tela-botanica.org/page:accueil?langue=fr" title="Accueil du site de Tela Botanica." >Accueil</a></li>
						<li id="faq"><a id="menu_lien_Array_386" href="http://www.tela-botanica.org/page:faq?langue=fr" title="Foire aux Questions, aide en ligne" >Aide</a></li>
						<li id="contact"><a id="menu_lien_Array_105" href="http://www.tela-botanica.org/page:contact?langue=fr" accesskey="9" >Contacts</a></li>
						<li id="plan"><a id="menu_lien_Array_9" href="http://www.tela-botanica.org/page:plan_du_site?langue=fr" accesskey="5" >Plan du site</a></li>
						<li id="rss"><a id="menu_lien_Array_630" href="http://www.tela-botanica.org/page:fluxRSS?langue=fr" title="Flux Rss du site Tela Botanica" >Flux RSS</a></li>
						<li id="telechargement"><a id="menu_lien_Array_273" href="http://www.tela-botanica.org/page:telechargement?langue=fr" title="[Raccourci : Alt+6 ] T&eacute;l&eacute;chargement de l'ensemble des fichiers des projets." accesskey="6" >T&eacute;l&eacute;chargement</a></li>
						<li id="visite"><a id="menu_lien_Array_25" href="http://www.tela-botanica.org/page:comment_marche_le_reseau?langue=fr" >Pr&eacute;sentation du r&eacute;seau</a></li>
						<li id="mentions"><a id="menu_lien_Array_104" href="http://www.tela-botanica.org/page:licence?langue=fr" title="Droits de reproduction" >Mentions l&eacute;gales</a></li>
					</ul>
				</div>
				<address id="coordonees">
					<span>Association TELA BOTANICA</span>
					<span>Institut de Botanique </span>
					<span>163, Rue Auguste Broussonnet</span>
					<span>34090 Montpellier</span>
					<span>Tél. +334 67 52 41 22</span>

					<span>accueil[at]tela-botanica.org</span>
					<br /><br />
				</address>
			</div>

			<div id="zone-menu-haut">
				<div id="zone-visiteur">
					<ul>
						<li id="connectes">En ligne : <span class="gris"></span></li>

						<li id="inscrits">Inscrits : <span class="gris"></span></li>
					</ul>
				</div>
				<div id="zone-acces-rapide">
					<ul>
						<li id="don">	<a href="http://www.tela-botanica.org/page:soutien">Faites un don</a></li>
						<li id="lettre">	<a href="http://www.tela-botanica.org/page:mon_inscription_au_reseau">Lettre d'actualit&eacute;s</a></li>
						<li id="recherche">
							<form action="http://www.tela-botanica.org/page:accueil_botanique" method="post" id="form_more_recherche">
								<fieldset>
									<legend>Moteur de recherche</legend>
									<label for="more_motif">Rechercher : </label>
									<input id="more_motif" name="more_motif" tabindex="100" maxlength="100" accesskey="4" type="text" value="Rechercher" onfocus="nettoyerChamp(this.id, 'Rechercher');" onblur="nettoyerChamp(this, 'Rechercher'); " title="Mettre les termes entre guillemets pour rechercher sur la phrase exacte."/>
									<input id="more_ok" name="more_ok" tabindex="101" value="ok" type="submit" />
								</fieldset>
							</form>
						</li>
						<li class="drapeau">
							<a href="?langue=fr">
								<img src="http://www.tela-botanica.org/sites/commun/generique/images/graphisme/drapeau_fr.png" alt="Français" title="Tela Botanica en Français" />
							</a>
						</li>
						<li class="drapeau">
							<a href="?langue=en">
								<img src="http://www.tela-botanica.org/sites/commun/generique/images/graphisme/drapeau_gb.png" alt="Anglais" title="Tela Botanica in english" />
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</body>
</html>
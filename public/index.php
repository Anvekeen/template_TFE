<?php
// Importer le routeur d'URL.
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Routeur.php';

// Etat de l'environnement : 'dev' en mode développement ou 'prod' en mode production.
// Ceci me permet d'utiliser des conditions pour réaliser certaines actions seulement si je suis dans un mode spécifique.
// Par exemple, dans le fichier /core/gestion_bdd.php, les erreurs ne s'afficheront dans le navigateur que si la constante ENV est configurée sur "dev".
define('ENV', 'dev');

// Chemin de base de l'application (Utile si l'application est hebergée dans un sous-dossier. Dans ce cas, n'oubliez pas d'adapter le fichier .htaccess).
// Par exemple si votre url racine est le suviant : localhost/monprojet/,
// Alors vous devrez configurer BASE_URL à '/monprojet' et dans le fichier .htacces : RewriteCond %{REQUEST_URI} !^/monprojet/public/
define('BASE_URL', '');

// Routes :
Routeur::configurer_route('GET', '/', 'AccueilController', 'index');
Routeur::configurer_route('GET', '/connexion', 'ConnexionController', 'index');
Routeur::configurer_route('POST', '/connexion', 'ConnexionController', 'connecter');
Routeur::configurer_route('GET', '/contact', 'ContactController', 'index');
Routeur::configurer_route('POST', '/contact', 'ContactController', 'envoyer');
Routeur::configurer_route('GET', '/inscription', 'InscriptionController', 'index');
Routeur::configurer_route('POST', '/inscription', 'InscriptionController', 'stocker');
Routeur::configurer_route('GET', '/profil', 'ProfilController', 'index');
Routeur::configurer_route('POST', '/profil', 'ProfilController', 'deconnecter');

Routeur::demarrer_routeur();
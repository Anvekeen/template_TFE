<?php

class GestionVue
{
    public static function afficher_vue(array $pageInfos, string $action, ?array $args = null): void
    {
        // Enregistrer le chemin vers le dossier des vues pour faciliter la lecture et éviter les répétitions.
        $cheminDesVues = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;

        // Enregistrer le chemin vers le dossier "templates" des vues, contenant les différentes parties du modèle de page (entête et pied de page) pour les mêmes raisons.
        $cheminDesVuesPart = $cheminDesVues . 'templates' . DIRECTORY_SEPARATOR;

        // Importer l'entête.
        require_once $cheminDesVuesPart . 'header.php';
        // Importer le contenu de la page.
        require_once $cheminDesVues . $pageInfos['vue'] . DIRECTORY_SEPARATOR . $action . '.php';
        // Importer le pied de page.
        require_once $cheminDesVuesPart . 'footer.php';
    }

    public static function afficher_erreur404()
    {
        // Indiquer au navigateur qu'il s'agit d'une erreur 404.
        header("HTTP/1.0 404 Not Found");
        // Charger la vue pour la page d'erreur 404.
        self::afficher_vue(['vue' => 'erreur404'], 'index');
        exit();
    }
}
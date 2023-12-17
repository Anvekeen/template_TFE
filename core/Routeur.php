<?php

class Routeur
{
    private static $routes = [];

    // Méthode pour configurer une route :
    public static function configurer_route(string $methode, string $chemin, string $controleur, string $action): void
    {
        self::$routes[] = [
            'methode' => $methode,
            'chemin' => $chemin,
            'controleur' => $controleur,
            'action' => $action,
        ];
    }

    // Méthode pour tester les routes et charger le contrôleur si une route correspond à celle de l'URL :
    public static function demarrer_routeur(?array $patterns = []): void
    {
        // Récupérer les différents segments de l'URL dans "$_GET['url']".
        // Ceci est rendu possible grace à cette ligne dans le fichier ".htaccess" : RewriteRule ^(.*)$ public/index.php?url=$1 [QSA,L]
        // Éviter les injection de code à l'aide de la fonction filter_input :
            // "INPUT_GET" indique que la fonction doit récupérer la variable depuis la superglobale "$_GET".
            // "url" est le nom qu'on lui a attribué dans le fichier ".htaccess"
            // "FILTER_SANITIZE_URL" est le filtre utilisé pour nettoyer la variable en supprimant tous les caractères illégaux d'une URL.
        $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL) ?? "";

        // Effacer les slashs présent au début et en fin d'url.
        $url = trim($url, '/');

        // Récupérer la méthode de la requête serveur ("GET" ou "POST").
        $methode = $_SERVER['REQUEST_METHOD'];

        // Si la méthode est "POST", on vérifie si une méthode particulière a été ajoutée dans les champs cachés du formulaire ("PUT" ou "DELETE").
        $methode = $methode === 'POST' && isset($_POST['_methode']) ? strtoupper($_POST['_methode']) : $methode;

        // Traiter chaque route :
        foreach (self::$routes as $route)
        {
            // Effacer les slashs présent au début et en fin d'URL.
            $chemin = trim($route['chemin'], '/');

            // Préparer le chemin pour vérifier s'il correspond à l'URL et ce même si celui-ci est composé de marqueur (ex.: {id}).
            $chemin = Routeur::preparer_cheminPourComparaisonUrl($chemin, $patterns);

            // Vérifier si la route courante correspond à l'URL et si les méthodes sont identiques.
            if ($route['methode'] === $methode && preg_match("#^$chemin$#", $url, $matches))
            {
                // Préparer les paramètres d'URL pour pouvoir les transmettre proprement au contrôleur :
                array_shift($matches);

                Routeur::charger_controleur($route['controleur'], $route['action'], $matches);
                return;
            }
        }

        // Charger le contrôleur pour la page d'erreur 404.
        Routeur::charger_controleur('Erreur404Controller', 'index');
    }

    private static function preparer_cheminPourComparaisonUrl(string $chemin, array $patterns): string
    {
        // Parcourir les patterns :
        foreach ($patterns as $marqueur => $pattern)
        {
            // Remplacer {marqueur} par l'expression régulière correspondant.
            $chemin = str_replace('{' . $marqueur . '}', '(' . $pattern . ')', $chemin);
        }
        return $chemin;
    }

    private static function charger_controleur(string $controleur, string $methode, ?array $urlParams = []): void
    {
        // Charger le contrôleur.
        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $controleur . '.php';

        // Appeler la méthode statique à partir des deux chaînes de caractères et lui communiquer les potentiels paramètres récupérés dans l'URL (id, slug, etc.)
        call_user_func([$controleur, $methode], ...$urlParams);
    }
}
?>
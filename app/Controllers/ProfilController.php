<?php 
// Importer le gestionnaire de vues.
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'GestionVue.php';
// Importer le gestionnaire de fréquence des requêtes "POST".
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'GestionFrequenceRequetes.php';
// Importer le gestionnaire de token CSRF.
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'GestionJetonCsrf.php';
// Importer le gestionnaire de mails.
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'UtilisateurModel.php';

class ProfilController
{
    // Les informations de la page nécessaire au bon fonctionnement de la vue :
    private static $pageInfos = [
        'vue' => 'profil',
        'titre' => "Profil",
        'description' => "Description de la page de profil...",
        'baseUrl' => BASE_URL . '/' . 'profil' . '/'
    ];


    // index : Afficher la liste des utilisateurs (il s'agit de la partie chargée par défaut) :
    public static function index(?array $args = []): void
    {
        // Vérifier si l'utilisateur est connecté.
        if (GestionAuthentification::est_connecte())
        {
            // Si l'utilisateur est connecté récupérer ses informations dans la base de données.    
            $args['utilisateur'] = UtilisateurModel::obtenir_utilisateurParSonId($_SESSION['id']);
        }
        else
        {
            // Si l'utilisateur n'est pas connecté, le rediriger vers la page de connexion :
            header("Location: " . BASE_URL . "/connexion");
            exit(); 
        }

        // Générer un nouveau jeton CSRF pour l'ajouter au formulaire de la vue.
        $args['jetonCSRF'] = GestionJetonCsrf::generer();

        // Appeler la vue.
        GestionVue::afficher_vue(self::$pageInfos, 'index', $args);
    }

    public static function deconnecter(): void
    {
        // Vérifier qu'il y a eu une tentative de soumission de formulaire avec la méthode "POST".
        // Vérifier si la fréquence des requêtes est bien inférieur à 3 requêtes par seconde.
        // Vérifier si le jeton CSRF est valide.
        if ($_SERVER["REQUEST_METHOD"] == "POST" && GestionFrequenceRequetes::est_respecteLimitationRequetes(1, 3) && GestionJetonCsrf::est_valide())
        {
            // Vérifier que c'est bien le formulaire de deconnexion qui a été soumis :
            if (isset($_POST['formNom']) && $_POST['formNom'] === 'deconnexion')
            {
                GestionAuthentification::deconnecter_utilisateur();
            }
        }
        self::index();
    }
}
?>
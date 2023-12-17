<?php
// Importer le gestionnaire de vues.
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'GestionVue.php';
// Importer le gestionnaire de vues.
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'GestionFormulaire.php';
// Importer le gestionnaire de fréquence des requêtes "POST".
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'GestionFrequenceRequetes.php';
// Importer le gestionnaire de token CSRF.
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'GestionJetonCsrf.php';
// Importer le gestionnaire de mails.
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'UtilisateurModel.php';

class InscriptionController
{
    // Les informations de la page nécessaire au bon fonctionnement de la vue :
    private static $pageInfos = [
        'vue' => 'inscription',
        'titre' => "Inscription",
        'description' => "Description de la page d'inscription'...",
        'baseUrl' => BASE_URL . '/' . 'inscription' . '/'
    ];


    // index : Afficher la liste des utilisateurs (il s'agit de la partie chargée par défaut) :
    public static function index(?array $args = []): void
    {
        // Redirigez l'utilisateur vers la page de profil si celui-ci est connecté.
        if (GestionAuthentification::est_connecte())
        {
            header("Location: " . BASE_URL . "/profil");
            exit();
        }

        // Générer un nouveau jeton CSRF pour l'ajouter au formulaire de la vue.
        $args['jetonCSRF'] = GestionJetonCsrf::generer();

        // Appeler la vue.
        GestionVue::afficher_vue(self::$pageInfos, 'index', $args);
    }

    public static function stocker(): void
    {
        // Vérifier qu'il y a eu une tentative de soumission de formulaire avec la méthode "POST".
        // Vérifier si la fréquence des requêtes est bien inférieur à 3 requêtes par seconde.
        // Vérifier si le jeton CSRF est valide.
        if ($_SERVER["REQUEST_METHOD"] == "POST" && GestionFrequenceRequetes::est_respecteLimitationRequetes(1, 3) && GestionJetonCsrf::est_valide())
        {
            // Vérifier la validité des entrées utilisateur.
            $resultat = GestionFormulaire::verifier_validiteChamps(UtilisateurModel::obtenir_champsConfigInscription(), $_POST);

            // Fais quelque chose si aucune erreur n'a été trouvée :
            if (count($resultat['erreurs']) === 0)
            {
                // Ajouter le nouvel utilisateur à la bdd, vérifier et agir en fonction du résultat de la requête (true ou false) :
                if (UtilisateurModel::ajouter_utilisateur($_POST['pseudo'], $_POST['email'], $_POST['motDePasse'])) 
                {
                    $resultat['messageValidation'] = GestionMessage::obtenir_messageValidation('form', 'inscription_succes');
                } 
                else 
                {
                    $resultat['messageValidation'] = GestionMessage::obtenir_messageValidation('form', 'generique_echec', false);
                }
            }
            else
            {
                $resultat['messageValidation'] = GestionMessage::obtenir_messageValidation('form', 'champs_echec', false);
            }
        }
        self::index($resultat ?? []);
    }
}
?>
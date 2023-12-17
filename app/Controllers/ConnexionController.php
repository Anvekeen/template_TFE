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
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'GestionMail.php';
// Importer le modèle des utilisateurs.
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'UtilisateurModel.php';

class ConnexionController
{
    // Les informations de la page nécessaire au bon fonctionnement de la vue :
    private static $pageInfos = [
        'vue' => 'connexion',
        'titre' => "Connexion",
        'description' => "Description de la page de connexion...",
        'baseUrl' => BASE_URL . '/' . 'connexion' . '/'
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

    public static function connecter(): void
    {
        // Vérifier qu'il y a eu une tentative de soumission de formulaire avec la méthode "POST".
        // Vérifier si la fréquence des requêtes est bien inférieur à 3 requêtes par seconde.
        // Vérifier si le jeton CSRF est valide.
        if ($_SERVER["REQUEST_METHOD"] == "POST" && GestionFrequenceRequetes::est_respecteLimitationRequetes(1, 3) && GestionJetonCsrf::est_valide())
        {
            // Vérifier que c'est bien le formulaire de connexion qui a été soumis :
            if (isset($_POST['formNom']) && $_POST['formNom'] === 'connexion')
            {
                $resultat = self::traiter_formulaireConnexion();
            }
            // Vérifier que c'est bien le formulaire avec le code de vérification qui a été soumis :
            elseif (isset($_POST['formNom']) && $_POST['formNom'] === 'activationCompte')
            {
                $resultat = self::traiter_formulaireCodeActivation();
            }
        }
        self::index($resultat ?? []);
    }

    private static function envoyer_codeActivationParMail(string $destinataire, int $codeActivation): bool
    {
        // --- Envoyer le mail avec le code d'activation :
        $expediteur = "5idw4-1@ifosup.wavre.be";
        $sujet = "IFOSUP - 5idw4-1 - Code d'activation";

        // Configurer l'en-tête.
        $entete = GestionMail::generer_entete($expediteur, $destinataire, $sujet);

        // Monter le message dans une liste non-ordonnée HTML :
        $message = "<p>Voici votre code d'activation : <b>$codeActivation</b>.</p>";

        return mail($destinataire, $sujet, $message, $entete);
    }

    // login : Tenter de se connecter à son compte utilisateur :
    private static function traiter_formulaireConnexion(): array
    {
        // Vérifier la validité des entrées utilisateur.
        $resultat = GestionFormulaire::verifier_validiteChamps(UtilisateurModel::obtenir_champsConfigConnexion(), $_POST);

        // Fais quelque chose si aucune erreur n'a été trouvée :
        if (count($resultat['erreurs']) === 0)
        {
            $utilisateur = UtilisateurModel::obtenir_utilisateurParSonPseudo($_POST['pseudo']);

            // Vérifier si l'utilisateur existe et que le mot de passe est valide :
            if ($utilisateur !== null && password_verify($_POST['motDePasse'], $utilisateur['uti_motdepasse']))
            {
                // Si le compte a déjà été activé, valider la connexion :
                if ($utilisateur['uti_compte_active'] === 1)
                {
                    GestionAuthentification::connecter_utilisateur($utilisateur['uti_id']);
                }
                // Si le compte n'a pas encore été activé, générer un code d'activation et l'envoyer par mail :
                else
                {
                    // Générer le code d'activation.
                    $codeActivation = UtilisateurModel::generer_codeActivation($utilisateur['uti_id']);

                    // Tenter d'envoyer le mail et agir en fonction de sa valeur de retour ("true" en cas d'envoi réussi, "false" si l'envoi a échoué) :
                    if (self::envoyer_codeActivationParMail($utilisateur['uti_email'], $codeActivation))
                    {
                        // Code d'activation envoyé avec succès par mail.
                        $resultat['messageValidation'] = GestionMessage::obtenir_messageValidation('form', 'envoi_codeActivation');

                        // Récupérer l'ID de l'utilisateur, ce qui permettra de signaler ultérieurement la nécessité de charger le formulaire d'activation et de transmettre cette valeur à ce dernier.
                        $resultat['utiId'] = $utilisateur['uti_id'];
                    }
                    else
                    {
                        // Echec de l'envoi du mail.
                        $resultat['messageValidation'] = GestionMessage::obtenir_messageValidation('form', 'envoi_mail_echec', false); 
                    }
                }
            }
            else 
            {
                $resultat['messageValidation'] = GestionMessage::obtenir_messageValidation('form', 'connexion_echec', false);
            }
        }
        else
        {
            $resultat['messageValidation'] = GestionMessage::obtenir_messageValidation('form', 'champs_echec', false);
        }

        return $resultat;
    }


    // Gestion du formulaire de connexion :
    private static function traiter_formulaireCodeActivation(): array
    {
        // Vérifier la validité des entrées utilisateur.
        $resultat = GestionFormulaire::verifier_validiteChamps(UtilisateurModel::obtenir_champsConfigActivationCode(), $_POST);

        // Si aucune erreur n'a été trouvée :
        if (count($resultat['erreurs']) === 0)
        {
            // Vérifier que l'id utilisateur n'a pas été supprimé dans le code HTML par l'utilisateur.
            if (isset($_POST['activation_utilisateurId']) && isset($_POST['activation_code']))
            {
                // Vérifier si le code entré par l'utilisateur correspond bien à celui présent dans la base de données.
                $utilisateur = UtilisateurModel::verifier_codeActivation(intval($_POST['activation_utilisateurId']), intval($_POST['activation_code']));

                // Si le code est valide, le supprimer de la base de données et activer le compte.
                if ($utilisateur !== null)
                {
                    UtilisateurModel::activer_compteUtilisateur($utilisateur['uti_id']);
                    GestionAuthentification::connecter_utilisateur($utilisateur['uti_id']);
                }
                else
                {
                    $resultat['messageValidation'] = GestionMessage::obtenir_messageValidation('form', 'activationCompte_echec', false); 
                }
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

        // $_POST['activation_utilisateurId'] ?? null permet de récupérer l'ID utilisateur pour le retransmettre au formulaire d'activation de compte.
        // Lui attribuer "null" dans l'éventualité où l'utilisateur aurait effectué des modifications dans le code HTML avant la soumission du formulaire.
        $resultat['utiId'] = $_POST['activation_utilisateurId'] ?? null;

        return $resultat;
    }
}
?>
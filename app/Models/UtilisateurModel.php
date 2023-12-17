<?php
// Importer le gestionnaire de base de données.
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'GestionBdd.php';

class UtilisateurModel
{
    private static $table = 't_utilisateur_uti';

    // Les champs dont on veut tester la validité avec les règles qu'il faut appliquer :
    public static function obtenir_champsConfigConnexion(): array
    {
        return [
            'pseudo' => [
                'requis' => true,
                'minLength' => 2,
                'maxLength' => 255
            ],
            'motDePasse' => [
                'requis' => true,
                'type' => 'password',
                'minLength' => 8,
                'maxLength' => 72
            ]
        ];
    }

    public static function obtenir_champsConfigActivationCode(): array
    {
        return [
            'activation_code' => [
                'requis' => true,
                'minLength' => 5,
                'maxLength' => 5
            ]
        ];
    }

    public static function obtenir_champsConfigInscription(): array
    {
        $table = self::$table;

        // Importer les informations de champs provenant des règles de connexion.
        $champsConfig = self::obtenir_champsConfigConnexion();

        // Mettre les règles à jour pour l'inscription :
        $champsConfig['pseudo']['unique'] = [
            'table' => $table,
            'colonne' => 'uti_pseudo'
        ];

        $champsConfig['email']['unique'] = [
            'table' => $table,
            'colonne' => 'uti_email'
        ];

        $champsConfig['motDePasse'] = [
            'requis' => true,
            'type' => 'password',
            'confirmation' => 'motDePasse_confirmation',
            'minLength' => 8,
            'maxLength' => 72
        ];

        return $champsConfig;
    }

    // Obtenir les infos utilisateur à partir de son pseudo :
    public static function obtenir_utilisateurParSonPseudo(string $pseudo): ?array
    {
        // La requête.
        $table = self::$table;
        $requete = "SELECT uti_id, uti_pseudo, uti_email, uti_compte_active, uti_motdepasse FROM $table WHERE uti_pseudo = :pseudo";

        // Retourner le résutlat de la requête si celle-ci a été fructueuse, sinon retourner "null".
        return GestionBdd::selectionner_dansTable($requete, ['pseudo' => $pseudo])[0] ?? null;
    }

    // Obtenir les informations de l'utilisateur par son ID :
    public static function obtenir_utilisateurParSonId(int $id): ?array
    {
        // La requête.
        $table = self::$table;
        $requete = "SELECT uti_pseudo, uti_email FROM $table WHERE uti_id = :id";

        // Retourner le résutlat de la requête si celle-ci a été fructueuse, sinon retourner "null".
        return GestionBdd::selectionner_dansTable($requete, ['id' => $id])[0] ?? null;
    }

    // Générer et retourner le code d'activation du compte :
    public static function generer_codeActivation(int $id): int
    {
        // Générer le code d'activation.
        $codeActivation = rand(10000, 99999);

        // La requête.
        $table = self::$table;
        $requete = "UPDATE $table SET uti_code_activation = :codeActivation WHERE uti_id = :id";

        // Fonction générique présente dans le gestionnaire de base de données (gestion_bdd.php).
        GestionBdd::modifier_dansTable($requete, ['id' => $id, 'codeActivation' => $codeActivation]);

        return $codeActivation;
    }

    // Vérifier le code d'activation du compte :
    public static function verifier_codeActivation(int $id, int $codeActivation): ?array
    {
        // La requête.
        $table = self::$table;
        $requete = "SELECT uti_id FROM $table WHERE uti_id = :id AND uti_code_activation = :codeActivation";

        // Si le code entré par l'utilisateur correspond bien à celui présent dans la base de données, retourner true, sinon false.
        return GestionBdd::selectionner_dansTable($requete, ['id' => $id, 'codeActivation' => $codeActivation])[0] ?? null;
    }

    // Activer le compte utilisateur :
    public static function activer_compteUtilisateur(int $id): bool
    {
        // La requête.
        $table = self::$table;
        $requete = "UPDATE $table SET uti_code_activation = null, uti_compte_active = 1 WHERE uti_id = :id";

        // Fonction générique présente dans le gestionnaire de base de données (gestion_bdd.php).
        return GestionBdd::modifier_dansTable($requete, ['id' => $id]);
    }

    // Fonction pour enregistrer un utilisateur dans la base de données :
    public static function ajouter_utilisateur(string $pseudo, string $email, string $mdp): bool
    {
        // La requête.
        $table = self::$table;
        $requete = "INSERT INTO $table (uti_pseudo, uti_email, uti_motdepasse) VALUES (:pseudo, :email, :motDePasse)";

        // Hashage du mot de passe.
        $mdp = password_hash($mdp, PASSWORD_BCRYPT);

        // Fonction générique présente dans le gestionnaire de base de données (gestion_bdd.php).
        return GestionBdd::modifier_dansTable($requete, ['pseudo' => $pseudo, 'email' => $email, 'motDePasse' => $mdp]);
    }
}
?>
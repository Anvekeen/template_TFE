<?php 
// Importer le gestionnaire de session.
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GestionSession.php';

class GestionAuthentification
{
    public static function connecter_utilisateur(int $id): void
    {
        self::init();

        // Enregistrer l'id de l'utilisateur dans une variable de session :
        $_SESSION['id'] = $id;
    }

    public static function deconnecter_utilisateur(): void
    {
        self::init();

        // Supprimer la propriété "ID" des variables de session.
        unset($_SESSION['id']);
    }

    public static function est_connecte(): bool
    {
        self::init();

        // Vérifier si la variable de session "ID" existe.
        return !empty($_SESSION['id']);
    }

    private static function init(): void
    {
        GestionSession::demarrer();
    }
}
?>
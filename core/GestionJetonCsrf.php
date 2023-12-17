<?php
// Importer le gestionnaire de session.
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GestionSession.php';

class GestionJetonCsrf
{
    public static function generer(): string
    {
        GestionSession::demarrer();

        // random_bytes(32) génère 32 octets (256 bits) de données aléatoires.
        // bin2hex() convertit les données binaires en chaîne de 64 caractères hexadécimaux.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
    
    public static function est_valide(): bool
    {
        GestionSession::demarrer();

        return (
            isset($_POST['csrf_token']) &&
            $_POST['csrf_token'] === $_SESSION['csrf_token']
        );
    }
}
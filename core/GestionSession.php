<?php

class GestionSession
{
    public static function demarrer(): void
    {
        // Démarrer une session si ça n'a pas déjà été fait :
        if (session_status() === PHP_SESSION_NONE)
        {
            // Configurez les paramètres du cookie de session pour qu'ils soient HTTPOnly par défaut (mesure de sécurité pour éviter certaines attaques XSS).
            session_set_cookie_params([
                'httponly' => true
            ]);
    
            // Démarrer la gestion des variables de session.
            session_start();
        }
    }
}

<?php
class GestionFrequenceRequetes
{
    public static function est_respecteLimitationRequetes(int $tempsObservation, int $maxRequetesParPeriode): bool
    {
        // Déclencher le limiteur de requêtes uniquement pour la méthode "POST".
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            // Initialiser les variables de session necessaires au fonctionnement du programme si au moins l'une d'elle n'a pas encore été définie.
            if (!isset($_SESSION['requeteDeRef']) || !isset($_SESSION['requeteCompteur'])) 
            {
                self::init_limitationRequetes();
            } 
            else 
            {
                // Calculer le nombre de secondes écoulées depuis le lancement de la dernière requête de référence.
                $tempsDelta = time() - $_SESSION['requeteDeRef'];

                // Si le temps écoulé ne dépasse pas le temps passé depuis la requête de référence :
                if ($tempsDelta < $tempsObservation) 
                {
                    // Vérifier si le nombre de requête dépasse le nombre de requête admises durant le temps passé depuis la requête de référence. 
                    if ($_SESSION['requeteCompteur'] >= $maxRequetesParPeriode) 
                    {
                        return false;
                    }

                    // Incrémenter le compteur de requêtes.
                    $_SESSION['requeteCompteur']++;
                } 
                else 
                {
                    // Si le temps d'observation a été dépassé, réinitialiser les variables .
                    self::init_limitationRequetes();
                }
            }

            return true;
        }
    }

    private static function init_limitationRequetes()
    {
        $_SESSION['requeteDeRef'] = time();
        $_SESSION['requeteCompteur'] = 1; 
    }
}
?>
<?php
// Importer le gestionnaire des messages.
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GestionMessage.php';

// Importer le gestionnaire de base de données.
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GestionBdd.php';

class GestionFormulaire
{
    public static function verifier_validiteChamps(array $champs, array $entreesUtilisateur): array
    {
        // Initialiser des variables de sortie.
        $accessibilite = [];
        $erreurs = [];
        $valeursDeSortie = [];

        // Parcourir les différents champs :
        foreach ($champs as $nomDuChamp => $config)
        {
            $champValidation = [];
            // Vérifier la validité des entrées en fonction de la configuration des champs :
            if (!self::est_rempli($nomDuChamp, $entreesUtilisateur) && isset($config['requis']) && $config['requis'] === true)
            {
                $champValidation = self::gerer_erreur($nomDuChamp, 'requis');
            }
            elseif (self::est_rempli($nomDuChamp, $entreesUtilisateur))
            {
                if (isset($config['type']) && $config['type'] === 'email' && !self::est_valideEmail($nomDuChamp, $entreesUtilisateur))
                {
                    $champValidation = self::gerer_erreur($nomDuChamp, 'email');
                }
                elseif (isset($config['minLength']) && isset($config['maxLength']) && !self::est_longeurTexteMinMax($nomDuChamp, $entreesUtilisateur, $config['minLength'], $config['maxLength']))
                {
                    $champValidation = self::gerer_erreur($nomDuChamp, 'minMaxLength', [$config['minLength'], $config['maxLength']]);
                }
                elseif (isset($config['minLength']) && !self::est_longeurTexteMin($nomDuChamp, $entreesUtilisateur, $config['minLength']))
                {
                    $champValidation = self::gerer_erreur($nomDuChamp, 'minLength', [$config['minLength']]);
                }
                elseif (isset($config['maxLength']) && !self::est_longeurTexteMax($nomDuChamp, $entreesUtilisateur, $config['maxLength']))
                {
                    $champValidation = self::gerer_erreur($nomDuChamp, 'maxLength', [$config['maxLength']]);
                }
                elseif (isset($config['confirmation']) && (!isset($entreesUtilisateur[$config['confirmation']]) || !self::est_valideConfirmation($nomDuChamp, $config['confirmation'], $entreesUtilisateur)))
                {
                    $champValidation = self::gerer_erreur($nomDuChamp, 'confirmation', ['mots de passe']);
                }
                elseif (isset($config['unique']) && !self::est_unique($nomDuChamp, $entreesUtilisateur, $config['unique']))
                {
                    $champValidation = self::gerer_erreur($nomDuChamp, 'unique');
                }
            }

            // Si le champ ne présente pas d'erreurs :
            if (!isset($champValidation['erreur']))
            {
                // Stocker l'attribut d'accessibilité stipulant que le champ est correct.
                $accessibilite[$nomDuChamp] = 'aria-invalid="false"';

                // Stocker la valeur du champ pour la réafficher dans le formulaire lors d'un potentiel echec de soumission.
                $valeursDeSortie[$nomDuChamp] = isset($entreesUtilisateur[$nomDuChamp]) ? htmlentities($entreesUtilisateur[$nomDuChamp]) : '';
            }
            else
            {
                $erreurs[$nomDuChamp] = $champValidation['erreur'];
                $accessibilite[$nomDuChamp] = $champValidation['accessibilite'];
            }
        }

        // Si tous les champs ont été correctement complétés, il ne sera pas nécessaire de réafficher les valeurs et les information d'accessibilité de validation des champs dans le formulaire.
        if (count($erreurs) === 0)
        {
            $valeursDeSortie = $accessibilite = [];
        }

        return ['erreurs' => $erreurs, 'valeursValides' => $valeursDeSortie, 'accessibilite' => $accessibilite];
    }

    private static function est_unique(string $nomDuChamp, array $entreesUtilisateur, array $infoBdd): bool
    {
        try
        {
            $pdo = GestionBdd::charger_bdd(); 

            // Construire la requête SQL de vérification d'unicité.
            $requete = "SELECT " . $infoBdd['colonne'] . " FROM " . $infoBdd['table'] . " WHERE " . $infoBdd['colonne'] . " = :valeur";

            // Vérifier si une condition d'exclusion est définie.
            $possedeConditionExclusion = isset($infoBdd['exclusion']);

            // Ajouter la condition d'exclusion à la requête si elle existe :
            if ($possedeConditionExclusion)
            {
                $requete .= " AND " . $infoBdd['exclusion']['colonne'] . " != :valeurExclusion";
            }

            // Préparer la requête SQL.
            $stmt = $pdo->prepare($requete);

            // Lier les paramètres de la requête.
            $stmt->bindParam(':valeur', $entreesUtilisateur[$nomDuChamp], PDO::PARAM_STR);

            // Lier la condition d'exclusion si elle existe :
            if ($possedeConditionExclusion)
            {
                $stmt->bindParam(':valeurExclusion', $infoBdd['exclusion']['valeur']);
            }

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) === false;
        }
        catch (PDOException $e) 
        {
            throw new Exception("Erreur lors de la vérification d'unicité : " . $e->getMessage());
        }
    }

    private static function est_valideConfirmation(string $nomDuChamp, string $nomDuChampConfirmation, array $entreesUtilisateur): bool
    {
        return $entreesUtilisateur[$nomDuChamp] === $entreesUtilisateur[$nomDuChampConfirmation];
    }

    private static function est_rempli(string $nomDuChamp, array $entreesUtilisateur): bool
    {
        return isset($entreesUtilisateur[$nomDuChamp]) && !empty($entreesUtilisateur[$nomDuChamp]);
    }

    private static function est_valideEmail(string $nomDuChamp, array $entreesUtilisateur): bool
    {
        return filter_var($entreesUtilisateur[$nomDuChamp], FILTER_VALIDATE_EMAIL);
    }

    private static function est_longeurTexteMin(string $nomDuChamp, array $entreesUtilisateur, int $minLength): bool
    {
        $min = isset($minLength) ? $minLength : PHP_INT_MIN;
        return strlen($entreesUtilisateur[$nomDuChamp]) >= $min;
    }

    private static function est_longeurTexteMax(string $nomDuChamp, array $entreesUtilisateur, int $maxLength): bool
    {
        $max = isset($maxLength) ? $maxLength : PHP_INT_MAX;
        return strlen($entreesUtilisateur[$nomDuChamp]) <= $max;
    }

    private static function est_longeurTexteMinMax(string $nomDuChamp, array $entreesUtilisateur, int $minLength, int $maxLength): bool
    {
        return (self::est_longeurTexteMin($nomDuChamp, $entreesUtilisateur, $minLength) && self::est_longeurTexteMax($nomDuChamp, $entreesUtilisateur, $maxLength));
    }

    private static function gerer_erreur(string $nomDuChamp, string $erreur, ?array $valeurSmsDynamique = null): array
    {
        // Préparer l'ID d'accessibilité.
        $accessibiliteId = 'champ-' . $nomDuChamp . '-erreur';

        // Stocker le contenu de l'attribut d'accessibilité avec la valeur pointant vers l'ID du message d'erreur.
        $accessibiliteAttribut = 'aria-invalid="true" aria-describedby="' . $accessibiliteId . '"';

        // Construire le message d'erreur avec son ID d'accessibilité :
        $message = GestionMessage::obtenir_message('form', $erreur, $valeurSmsDynamique);
        $messageErreur = "<p id=\"$accessibiliteId\" class=\"alert\">$message</p>";

        return [
            'erreur' => $messageErreur,
            'accessibilite' => $accessibiliteAttribut
        ];
    }
}
?>
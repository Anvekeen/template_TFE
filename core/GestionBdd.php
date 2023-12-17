<?php
class GestionBdd
{
    public static function charger_bdd(): ?PDO
    {
        $config = include(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bdd.php');

        try
        {
            $pdo = new PDO("mysql:host={$config['serveur']};dbname={$config['bdd']};charset=utf8", $config['utilisateur'], $config['motDePasse']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        }
        catch(PDOException $erreur)
        {
            // Relancer l'exception pour qu'elle soit capturée par la fonction parente.
            throw $erreur;
        }
    }

    public static function selectionner_dansTable(string $requete, ?array $parametres = []): ?array
    {
        try
        {
            // Préparer et exécuter la requête.
            $stmt = self::executer_requete($requete, $parametres);

            // Récupérer un tableau avec les données retournées par la requêtes.
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $erreur)
        {
            self::gerer_exceptionSQL($erreur);
            return null;
        }
    }

    // Cette fonction permet de réaliser des insertions (insert), des mises à jour (update) ainsi que des suppression (delete).
    // La fonction retournera true si une modification a bien été réalisée et false dans le cas contraire.
    // La méthode rowCount() permet connaître le nombre de lignes de la table qui ont été affectées par la requête.
    public static function modifier_dansTable(string $requete, array $parametres): bool
    {
        try
        {
            // Exécuter la requête et retourner le résultat (true ou false).
            self::executer_requete($requete, $parametres);
            return true;
        }
        catch (PDOException $erreur)
        {
            self::gerer_exceptionSQL($erreur);
            return false;
        }
    }

    private static function executer_requete(string $requete, array $parametres = []): ?PDOStatement 
    {
        try
        {
            // Instancier la connexion à la base de données.
            $pdo = self::charger_bdd();

            // Charger la requête préparée.
            $stmt = $pdo->prepare($requete);

            // Lier toutes les valeurs à leur marqueur :
            foreach ($parametres as $marqueur => $valeur)
            {
                $stmt->bindValue(":$marqueur", $valeur);
            }

            // Exécuter la requête.
            $stmt->execute();

            return $stmt;
        }
        catch (PDOException $erreur)
        { 
            // Relancer l'exception pour qu'elle soit capturée par la fonction parente.
            throw $erreur;
        }
    }

    private static function gerer_exceptionSQL(PDOException $erreur): void
    {
        // Afficher les erreurs dans le navigateur uniquement en mode développement :
        if (ENV === 'dev')
        {
            echo '<div style="background-color: #e6c4c4; padding: 10px; margin: 10px;">';
            echo '<strong>Erreur d\'exécution de requête :</strong> ' . $erreur->getMessage();
            echo '</div>';
        }

        // Monter le message d'erreur pour le journal.
        $messageErreur = '[' . date('Y-m-d H:i:s') . '] Erreur d\'exécution de requête : ' . $erreur->getMessage() . PHP_EOL;

        // Enregistrer le message d'erreur dans le journal (fichier sql.log).
        // L'utilisation du chiffre 3 en deuxième paramètre signifie que le message sera ajouté à la fin du fichier spécifié (le fichier sera créé s'il n'existe pas).
        error_log($messageErreur, 3, dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'sql.log');
    }
}
?>
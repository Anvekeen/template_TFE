# Cours PHP (IFOSUP Wavre)

## 1. Installation de l'application sur Laragon

- ### 1.1 Si vous Utilisez les VirtualHosts Automatiques de Laragon (URL = nomDuDossierDeVotreApp.test)

    1. Copiez le dossier de votre projet dans le dossier "www" de Laragon
    2. Démarrez les services de Laragon en cliquant sur le bouton "Démarrer".
    3. Rendez-vous dans le fichier de configuration du VirtualHost de votre application. Dans Laragon : "Menu" > "Apache" > "sites-enabled" > "auto.nomDuDossierDeVotreApp.conf".
    4. Supprimez la/les chaîne(s) de caractères "\public" du fichier de configuration et sauvegardez le fichier.
    5. Relancer les services de Laragon.

- ### 1.2 Si vous Utilisez Localhost (URL = localhost/nomDuDossierDeVotreApp)

    1. Rendez-vous dans le fichier ".htaccess" situé à la racine du projet :
        1.1 Repérez la ligne suivante :
        ```
        RewriteCond %{REQUEST_URI} !^/public/
        ```
        1.2 Ajoutez le nom du dossier de votre projet à la racine :
        ```
        RewriteCond %{REQUEST_URI} !^/nomDuDossierDeVotreProjet/public/
        ```
    2. Rendez-vous dans le fichier "index.php" situé dans le dossier "public" :
        2.1 Repérez la ligne suivante :
        ```
        define('BASE_URL', '');
        ```
        2.2 Assignez la constante "BASE_URL" avec le nom du dossier de votre projet :
        ```
        define('BASE_URL', '/nomDuDossierDeVotreProjet');
        ```


<?php
// Importer la navigation principale.
require_once __DIR__ . DIRECTORY_SEPARATOR . 'NavPrincipale.php';
$navPrincipale = NavPrincipale::creer();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--
        Afficher le contenu de "$metaDescription" et celui de "$pageTitre" si la variable existe sinon afficher une chaîne de caractères vide.
    -->
    <meta name="description" content="<?=$metaDescription ?? '' ?>">
    <link rel="stylesheet" href="<?=BASE_URL?>/public/ressources/css/style.css">
    <title><?=$pageTitre ?? ''?></title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <?= $navPrincipale; ?>
            </ul>
        </nav>
    </header>
    <main>

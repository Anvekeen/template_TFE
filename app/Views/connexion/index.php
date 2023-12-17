<h1>Connexion</h1>
<?php
if (isset($args['utiId']) && is_numeric($args['utiId']))
{
    ?>
    <form action="" method="post">
        <p aria-hidden="true"><span class="alert">*</span> champs obligatoires</p>
        <input type="hidden" name="formNom" value="activationCompte">
        <input type="hidden" name="csrf_token" value="<?=$args['jetonCSRF']?>">
        <input type="hidden" name="activation_utilisateurId" value="<?=$args['utiId']?>">
        <div>
            <label class="required" for="activation_code">Code d'activation </label>
            <input type="text" id="activation_code" name="activation_code" <?=$args['accessibilite']['activation_code'] ?? ''?> minlength="5" maxlength="5" required>
            <?=$args['erreurs']['activation_code'] ?? ''?>
        </div>
        <div>
            <button type="submit">Envoyer</button>
            <?=$args['messageValidation'] ?? ''?>
        </div>
    </form>
    <?php
}
else
{
    ?>
    <form action="" method="post">
        <p aria-hidden="true"><span class="alert">*</span> champs obligatoires</p>
        <input type="hidden" name="formNom" value="connexion">
        <input type="hidden" name="csrf_token" value="<?=$args['jetonCSRF']?>">
        <div>
            <label class="required" for="pseudo">Pseudo </label>
            <input type="text" id="pseudo" name="pseudo" value="<?=$args['valeursValides']['pseudo'] ?? ''?>" <?=$args['accessibilite']['pseudo'] ?? ''?> minlength="2" maxlength="255" required>
            <?=$args['erreurs']['pseudo'] ?? ''?>
        </div>
        <div>
            <label class="required" for="motDePasse">Mot de Passe </label>
            <input type="password" id="motDePasse" name="motDePasse" <?=$args['accessibilite']['motDePasse'] ?? ''?> minlength="8" maxlength="72" required>
            <?=$args['erreurs']['motDePasse'] ?? ''?>
        </div>
        <div>
            <button type="submit">Envoyer</button>
            <?=$args['messageValidation'] ?? ''?>
        </div>
    </form>
    <p><a href="/inscription">Créer un nouveau compte.</a></p>
    <?php
}
?>
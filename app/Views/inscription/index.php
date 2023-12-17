<h1>Inscription</h1>
<form action="" method="post">
    <p aria-hidden="true"><span class="alert">*</span> champs obligatoires</p>
    <input type="hidden" name="csrf_token" value="<?=$args['jetonCSRF']?>">
    <div>
        <label class="required" for="pseudo">Pseudo </label>
        <input type="text" id="pseudo" name="pseudo" value="<?=$args['valeursValides']['pseudo'] ?? ''?>" <?=$args['accessibilite']['pseudo'] ?? ''?> minlength="2" maxlength="255" required>
        <?=$args['erreurs']['pseudo'] ?? ''?>
    </div>
    <div>
        <label class="required" for="email">E-Mail </label>
        <input type="email" id="email" name="email" value="<?=$args['valeursValides']['email'] ?? ''?>" <?=$args['accessibilite']['email'] ?? ''?> required>
        <?=$args['erreurs']['email'] ?? ''?>
    </div>
    <div>
        <label class="required" for="motDePasse">Mot de Passe </label>
        <input type="password" id="motDePasse" name="motDePasse" <?=$args['accessibilite']['motDePasse'] ?? ''?> minlength="8" maxlength="72" required>
        <?=$args['erreurs']['motDePasse'] ?? ''?>
    </div>
    <div>
        <label class="required" for="motDePasse_confirmation">Confirmation Mot de Passe </label>
        <input type="password" id="motDePasse_confirmation" name="motDePasse_confirmation" <?=$args['accessibilite']['motDePasse_confirmation'] ?? ''?> minlength="8" maxlength="72" required>
        <?=$args['erreurs']['motDePasse_confirmation'] ?? ''?>
    </div>
    <div>
        <button type="submit">Envoyer</button>
        <?=$args['messageValidation'] ?? ''?>
    </div>
</form>
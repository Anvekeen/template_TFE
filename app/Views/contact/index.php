<h1>Contact</h1>
<form action="" method="post">
    <p aria-hidden="true"><span class="alert">*</span> champs obligatoires</p>
    <input type="hidden" name="csrf_token" value="<?=$args['jetonCSRF']?>">
    <div>
        <label class="required" for="nom">Nom </label>
        <input type="text" id="nom" name="nom" value="<?=$args['valeursValides']['nom'] ?? ''?>" <?=$args['accessibilite']['nom'] ?? ''?> minlength="2" maxlength="255" required>
        <?=$args['erreurs']['nom'] ?? ''?>
    </div>
    <div>
        <label for="prenom">Prénom </label>
        <input type="text" id="prenom" name="prenom" value="<?=$args['valeursValides']['prenom'] ?? ''?>" <?=$args['accessibilite']['prenom'] ?? ''?> minlength="2" maxlength="255">
        <?=$args['erreurs']['prenom'] ?? ''?>
    </div>
    <div>
        <label class="required" for="email">E-Mail </label>
        <input type="email" id="email" name="email" value="<?=$args['valeursValides']['email'] ?? ''?>" <?=$args['accessibilite']['email'] ?? ''?> required>
        <?=$args['erreurs']['email'] ?? ''?>
    </div>
    <div>
        <label class="required" for="message">Message </label>
        <textarea id="message" name="message" <?=$args['accessibilite']['message'] ?? ''?> minlength="10" maxlength="3000" required><?=$args['valeursValides']['message'] ?? ''?></textarea>
        <?=$args['erreurs']['message'] ?? ''?>
    </div>
    <div>
        <button type="submit">Envoyer</button>
        <?=$args['messageValidation'] ?? ''?>
    </div>
</form>
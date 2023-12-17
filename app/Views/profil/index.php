<h1>Profil</h1>
<p>
    <ul>
        <li><b>Pseudo : </b><?=htmlentities($args['utilisateur']['uti_pseudo'])?></li>
        <li><b>E-mail : </b><?=htmlentities($args['utilisateur']['uti_email'])?></li>
    </ul>
</p>
<p>
    <form action="" method="post">
        <input type="hidden" name="formNom" value="deconnexion">
        <input type="hidden" name="csrf_token" value="<?=$args['jetonCSRF']?>">
        <div>
            <button type="submit">Déconnexion</button>
        </div>
    </form>
</p>
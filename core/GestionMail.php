<?php
class GestionMail
{
    public static function generer_entete(string $expediteur, string $destinataire, string $sujet):string
    {
        // Configurer l'en-tête.
        $entete = "From: 5idw4-1 (Projet de Développement Web) <$expediteur>\r\n";
        $entete .= "To: $destinataire\r\n";
        $entete .= "Subject: $sujet\r\n";
        $entete .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $entete .= "Content-Transfer-Encoding: quoted-printable\r\n";

        return $entete;
    }
}
?>
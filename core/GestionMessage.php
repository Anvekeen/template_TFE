<?php

class GestionMessage
{
    private static $messagesForm;

    public static function obtenir_messagesForm(): array
    {
        self::$messagesForm = self::importer_messages('form_messages.json');
        return self::$messagesForm;
    }

    public static function obtenir_message(string $cat, string $cle, ?array $valeurSmsDynamique = null): string
    {
        // Prevision pour de futures catégories classés par fichiers .json :
        if ($cat === 'form')
        {
            $messages = self::$messagesForm ?? self::obtenir_messagesForm();
        }

        // Stocker le message d'erreur et son ID d'accessibilité.
        $message = $messages[$cle];

        // Gérer les messages qui nécessitent de leur ajouter des données :
        if ($valeurSmsDynamique !== null)
        {
            foreach ($valeurSmsDynamique as $key => $value)
            {
                $message = str_replace("%$key%", $value, $message);
            }
        }

        return $message;
    }

    public static function obtenir_messageValidation(string $cat, string $cle, bool $estSucces = true, ?array $valeurSmsDynamique = null): string
    {
        $message = self::obtenir_message($cat, $cle, $valeurSmsDynamique);
        $classeCss = $estSucces ? 'succes' : 'alert';
        return "<p class='$classeCss'>$message</p>";
    }

    private static function importer_messages(string $nomFichier): array
    {
        // Charger les messages liés aux formulaires à partir du fichier 'form_messages.json et le convertir en tableau associatif :
        $messages = file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'fr' . DIRECTORY_SEPARATOR . $nomFichier);
        $messages = json_decode($messages, true);
        return $messages;
    }
}
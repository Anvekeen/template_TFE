<?php

class ContactModel
{
    // Les champs dont on veut tester la validité avec les règles qu'il faut appliquer.
    public static function obtenir_champsConfig(): array
    {
        return [
            'nom' => [
                'requis' => true, 
                'minLength' => 2,
                'maxLength' => 255
            ],
            'prenom' => [
                'minLength' => 2,
                'maxLength' => 255
            ],
            'email' => [
                'requis' => true, 
                'type' => 'email'
            ],
            'message' => [
                'requis' => true, 
                'minLength' => 10,
                'maxLength' => 3000
            ]
        ];
    }
}
?>
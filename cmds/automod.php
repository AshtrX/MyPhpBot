<?php

if ($message->author->id == $discord->id || $message->author->bot) {
    return;
}

// Liste des mots interdits
$forbiddenWords = ['mot1', 'mot2', 'mot3'];

// Vérification des mots interdits
foreach ($forbiddenWords as $word) {
    if (stripos($message->content, $word) !== false) {
        $message->delete();
        $message->channel->sendMessage("{$message->author}, votre message contient des mots interdits et a été supprimé.");
        return;
    }
}

// Vérification des liens
if (preg_match('/https?:\/\/[^\s]+/i', $message->content)) {
    $message->delete();
    $message->channel->sendMessage("{$message->author}, les liens ne sont pas autorisés dans ce salon.");
    return;
}

// Vérification des mentions excessives
$maxMentions = 5;
if (count($message->mentions) > $maxMentions) {
    $message->delete();
    $message->channel->sendMessage("{$message->author}, trop de mentions dans votre message. Limite : {$maxMentions} mentions.");
    return;
}

// Ajoutez d'autres vérifications selon vos besoins

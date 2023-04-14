<?php

if ($message->author->id == $discord->id) {
    return;
}

if (!$message->member->hasPermission('KICK_MEMBERS')) {
    $message->reply("Vous n'avez pas la permission d'expulser des membres.");
    return;
}

$args = explode(' ', $message->content);

if (count($args) < 3) {
    $message->reply('Utilisation : !kick @utilisateur raison');
    return;
}

$userToKick = $message->mentions->first();

if (!$userToKick) {
    $message->reply('Veuillez mentionner un utilisateur à expulser.');
    return;
}

$reason = implode(' ', array_slice($args, 2));

$guild = $message->channel->guild;
$member = $guild->members->get('id', $userToKick->id);

if (!$member) {
    $message->reply("L'utilisateur mentionné n'est pas membre de ce serveur.");
    return;
}

if (!$member->kickable) {
    $message->reply("Je n'ai pas la permission d'expulser cet utilisateur.");
    return;
}

$member->kick($reason)->done(function () use ($message) {
    $message->reply('Utilisateur expulsé avec succès.');
}, function ($error) use ($message) {
    $message->reply("Une erreur s'est produite lors de l'expulsion de l'utilisateur : " . $error->getMessage());
});

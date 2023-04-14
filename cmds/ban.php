<?php

if ($message->author->id == $discord->id) {
    return;
}

if (!$message->member->hasPermission('BAN_MEMBERS')) {
    $message->reply("Vous n'avez pas la permission de bannir des membres.");
    return;
}

$args = explode(' ', $message->content);

if (count($args) < 3) {
    $message->reply('Utilisation : !ban @utilisateur raison');
    return;
}

$userToBan = $message->mentions->first();

if (!$userToBan) {
    $message->reply('Veuillez mentionner un utilisateur à bannir.');
    return;
}

$reason = implode(' ', array_slice($args, 2));

$guild = $message->channel->guild;
$member = $guild->members->get('id', $userToBan->id);

if (!$member) {
    $message->reply("L'utilisateur mentionné n'est pas membre de ce serveur.");
    return;
}

if (!$member->bannable) {
    $message->reply("Je n'ai pas la permission de bannir cet utilisateur.");
    return;
}

$guild->ban($member, [
    'reason' => $reason,
])->done(function () use ($message) {
    $message->reply('Utilisateur banni avec succès.');
}, function ($error) use ($message) {
    $message->reply("Une erreur s'est produite lors du bannissement de l'utilisateur : " . $error->getMessage());
});

<?php

if ($message->author->id == $discord->id) {
    return;
}

if (!$message->member->hasPermission('BAN_MEMBERS')) {
    $message->reply("Vous n'avez pas la permission de bannir des membres.");
    return;
}

$args = explode(' ', $message->content);

if (count($args) < 4) {
    $message->reply('Utilisation : !tempban @utilisateur durée(e.g. 10m) raison');
    return;
}

$userToBan = $message->mentions->first();

if (!$userToBan) {
    $message->reply('Veuillez mentionner un utilisateur à bannir temporairement.');
    return;
}

$durationString = $args[2];
$duration = strtotime("+$durationString") - time();

if ($duration <= 0) {
    $message->reply('Veuillez fournir une durée valide pour le bannissement temporaire.');
    return;
}

$reason = implode(' ', array_slice($args, 3));

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
])->done(function () use ($guild, $userToBan, $duration, $message) {
    $message->reply("Utilisateur banni temporairement pour $duration secondes.");

    sleep($duration);

    $guild->unban($userToBan)->done(function () use ($message) {
        $message->reply('Utilisateur débanni.');
    }, function ($error) use ($message) {
        $message->reply("Une erreur s'est produite lors du débannissement de l'utilisateur : " . $error->getMessage());
    });
}, function ($error) use ($message) {
    $message->reply("Une erreur s'est produite lors du bannissement de l'utilisateur : " . $error->getMessage());
});

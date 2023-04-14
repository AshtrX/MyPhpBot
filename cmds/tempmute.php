<?php

if ($message->author->id == $discord->id) {
    return;
}

if (!$message->member->hasPermission('MANAGE_ROLES')) {
    $message->reply("Vous n'avez pas la permission de gérer les rôles.");
    return;
}

$args = explode(' ', $message->content);

if (count($args) < 4) {
    $message->reply('Utilisation : !tempmute @utilisateur durée(e.g. 10m) raison');
    return;
}

$userToMute = $message->mentions->first();

if (!$userToMute) {
    $message->reply('Veuillez mentionner un utilisateur à mute temporairement.');
    return;
}

$durationString = $args[2];
$duration = strtotime("+$durationString") - time();

if ($duration <= 0) {
    $message->reply('Veuillez fournir une durée valide pour le mute temporaire.');
    return;
}

$reason = implode(' ', array_slice($args, 3));

$guild = $message->channel->guild;
$member = $guild->members->get('id', $userToMute->id);

if (!$member) {
    $message->reply("L'utilisateur mentionné n'est pas membre de ce serveur.");
    return;
}

$muteRole = null;

foreach ($guild->roles as $role) {
    if (strtolower($role->name) === 'muted') {
        $muteRole = $role;
        break;
    }
}

if (!$muteRole) {
    $message->reply("Impossible de trouver le rôle 'Muted'. Veuillez créer un rôle 'Muted' avec les autorisations de messagerie désactivées.");
    return;
}

$member->addRole($muteRole)->done(function () use ($member, $muteRole, $duration, $message) {
    $message->reply("Utilisateur mute temporairement pour $duration secondes.");

    sleep($duration);

    $member->removeRole($muteRole)->done(function () use ($message) {
        $message->reply('Utilisateur unmute.');
    });
}, function ($error) use ($message) {
    $message->reply("Une erreur s'est produite lors de l'ajout du rôle Muted : " . $error->getMessage());
});

<?php

if ($message->author->id == $discord->id) {
    return;
}

$args = explode(' ', $message->content);

if (count($args) < 2) {
    $message->reply('Utilisation : !say message');
    return;
}

$sayMessage = implode(' ', array_slice($args, 1));

$message->channel->sendMessage($sayMessage);

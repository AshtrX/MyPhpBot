<?php

if ($message->author->id == $discord->id) {
    return;
}

$message->reply('Pong!');

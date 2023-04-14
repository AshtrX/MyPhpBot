<?php

require __DIR__ . '/vendor/autoload.php';

use Discord\Discord;

$botToken = 'YOUR_BOT_TOKEN_HERE';

$discord = new Discord([
    'token' => $botToken,
]);

$discord->on('ready', function ($discord) {
    echo "Bot is ready!", PHP_EOL;

    $discord->on('message', function ($message) use ($discord) {
        $prefix = '!';
        $content = $message->content;

        if (substr($content, 0, 1) === $prefix) {
            $command = substr($content, 1);
            $file = __DIR__ . '/cmds/' . $command . '.php';

            if (file_exists($file)) {
                require $file;
            }
        }
    });
});

$discord->run();

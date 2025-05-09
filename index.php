<?php
require __DIR__.'/config.php';
require __DIR__.'/src/Db.php';
require __DIR__.'/src/Bot.php';
require __DIR__.'/src/Handlers.php';

$bot = new Bot();
$update = json_decode(file_get_contents('php://input'), true);
if (isset($update['message'])) Handlers::onMessage($bot, $update['message']);
elseif (isset($update['callback_query'])) Handlers::onCallback($bot, $update['callback_query']);
?>
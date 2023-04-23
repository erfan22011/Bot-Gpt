<?php

use Longman\TelegramBot\Exception\TelegramException;

use Longman\TelegramBot\Request;

use OpenAI\Api;

// Replace YOUR_BOT_TOKEN with the token provided by BotFather

define('BOT_TOKEN', 'YOUR_BOT_TOKEN');

// Replace YOUR_OPENAI_API_KEY with your OpenAI API key

define('OPENAI_API_KEY', 'YOUR_OPENAI_API_KEY');

// Replace YOUR_ENGINE_ID with your OpenAI engine ID

define('ENGINE_ID', 'YOUR_ENGINE_ID');

require DIR . '/vendor/autoload.php';

try {

    // Create Telegram API object

    $telegram = new Longman\TelegramBot\Telegram(BOT_TOKEN, 'Botan.io API key');

    // Handle incoming message

    $telegram->onCommand('start', function ($update) use ($telegram) {

        $chat_id = $update->getMessage()->getChat()->getId();

        $message = 'Hello! Send me a message and I will respond with an AI-generated response.';

        $data = [

            'chat_id' => $chat_id,

            'text' => $message,

        ];

        Request::sendMessage($data);

    });

    $telegram->onMessage(function ($update) use ($telegram) {

        $chat_id = $update->getMessage()->getChat()->getId();

        $message_text = $update->getMessage()->getText();

        

        // Call OpenAI's completion API to generate a response

        $api = new Api(OPENAI_API_KEY);

        $response = $api->completions([

            'engine' => ENGINE_ID,

            'prompt' => $message_text,

            'max_tokens' => 1024,

            'n' => 1,

            'stop' => null,

            'temperature' => 0.7,

        ]);

        

        // Send the response back to the user

        $data = [

            'chat_id' => $chat_id,

            'text' => $response['choices'][0]['text'],

        ];

        Request::sendMessage($data);

    });

    // Start the bot

    $telegram->handle();

} catch (TelegramException $e) {

    echo 'Error: ' . $e->getMessage();

}

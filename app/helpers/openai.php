<?php

function callOpenAI(string $apiKey, string $systemMessage, string $userPrompt, string $model = 'gpt-4'): ?string
{
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemMessage],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => 0.5
        ])
    ]);

    $response = curl_exec($ch);
    if (!$response) {
        return null;
    }

    $responseData = json_decode($response, true);
    return $responseData['choices'][0]['message']['content'] ?? null;
}

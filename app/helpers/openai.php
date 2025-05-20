<?php
require_once 'app/helpers/settings.php';

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

function buildAISummaryPrompt(array $data)
{
    $lines = [];

    $lines[] = "Project Title: " . $data['project'];
    $lines[] = "Total Participants: " . $data['participantCount'];
    $lines[] = "Average Age: " . ($data['averageAge'] ?? 'N/A');
    $lines[] = "Total Evaluations: " . $data['totalEvaluations'];
    $lines[] = "Total Responses: " . $data['totalResponses'];
    $lines[] = "Average Task Time: " . $data['avgTime'] . " seconds";
    $lines[] = "Task Success Rate: " . $data['taskSuccessRate'] . "%";

    if (!empty($data['sus'])) {
        $lines[] = "SUS Summary:";
        $lines[] = "- Average Score: " . $data['sus']['average'];
        $lines[] = "- Usability Rating: " . $data['sus']['label'];
        $lines[] = "- Score Variation: " . $data['sus']['variation'];
        $lines[] = "- Low Scores (<50): " . $data['sus']['low'];
    }

    $lines[] = "Write a 5-paragraph analysis identifying potential usability issues, participant trends, and actionable insights.";

    return implode("\n", $lines);
}

function generateAISummary(PDO $pdo, array $data)
{
    $apiKey = getOpenAIKey($pdo); // <- now passes $pdo correctly

    if (!$apiKey) {
        return "AI summary unavailable (missing API key).";
    }

    $prompt = buildAISummaryPrompt($data);

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a usability expert. Based on the data provided, write a concise usability analysis.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.6,
        'max_tokens' => 300
    ]));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return "⚠️ Failed to connect to OpenAI.";
    }
    $data = json_decode($response, true);
    curl_close($ch);

    return $data['choices'][0]['message']['content'] ?? "⚠️ No AI summary generated.";
}

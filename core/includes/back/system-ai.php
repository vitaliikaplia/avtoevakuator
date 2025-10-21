<?php

if(!defined('ABSPATH')){exit;}

function get_open_ai_response($prompt, $system = null){
    $api_key  = get_option('open_ai_api_key');
    $model    = get_option('open_ai_api_model') ?: 'gpt-4o-mini';
    $max_toks = intval(get_option('open_ai_max_tokens')) ?: 512;

    if (!$api_key || !$model || !$prompt) {
        return new WP_Error('openai_config', 'OpenAI: відсутні налаштування або prompt');
    }

    $messages = [];
    if ($system) $messages[] = ['role' => 'system', 'content' => $system];
    $messages[] = ['role' => 'user', 'content' => $prompt];

    $payload = [
        'model'      => $model,
        'messages'   => $messages,
        'max_tokens' => $max_toks,
    ];

    $resp = wp_remote_post(
        'https://api.openai.com/v1/chat/completions',
        [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ],
            'body'    => wp_json_encode($payload, JSON_UNESCAPED_UNICODE),
            'timeout' => 30,
        ]
    );

    if (is_wp_error($resp)) {
        return $resp;
    }

    $code = wp_remote_retrieve_response_code($resp);
    $body = wp_remote_retrieve_body($resp);
    $data = json_decode($body, true);

    if ($code !== 200) {
        $msg = isset($data['error']['message']) ? $data['error']['message'] : 'OpenAI HTTP ' . $code;
        return new WP_Error('openai_http', $msg, ['status' => $code, 'body' => $body]);
    }

    return $data['choices'][0]['message']['content'] ?? new WP_Error('openai_payload', 'Порожня відповідь OpenAI');
}

function get_claude_response($prompt){
    if( ($claude_api_key = get_option('claude_api_key')) && ($claude_api_model = get_option('claude_api_model')) && ($claude_max_tokens = get_option('claude_max_tokens')) ){
        $apiUrl = 'https://api.anthropic.com/v1/messages';

        $data = [
            'model' => $claude_api_model,
            'max_tokens' => intval($claude_max_tokens),
            'messages' => [['role' => 'user', 'content' => $prompt]]
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $claude_api_key,
            'anthropic-version: 2023-06-01'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        return $decodedResponse['content'][0]['text'] ?? 'Error';
    }
}

function get_gemini_response($prompt){
    $api_key  = get_option('gemini_api_key');
    $model    = get_option('gemini_api_model') ?: 'gemini-1.5-flash';
    $max_toks = intval(get_option('gemini_max_tokens')) ?: 512;

    if (!$api_key || !$model || !$prompt) {
        return 'Error: missing parameters';
    }

    $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $api_key;

    $data = [
        'contents' => [
            [
                'role'  => 'user',
                'parts' => [['text' => $prompt]]
            ]
        ],
        'generationConfig' => [
            'maxOutputTokens' => $max_toks
        ]
    ];

    $ch = curl_init($api_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($data, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $errno = curl_errno($ch);
    $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno) return 'cURL error';
    $decoded = json_decode($response, true);

    if ($http !== 200) {
        return $decoded['error']['message'] ?? 'HTTP ' . $http;
    }

    return $decoded['candidates'][0]['content']['parts'][0]['text'] ?? 'Error';
}

function ai_responsive_wrapper($prompt){
    if($ai_engine = get_option('ai_engine')){
        switch ($ai_engine){
            case 'openai':
                return get_open_ai_response($prompt);
            case 'claude':
                return get_claude_response($prompt);
            case 'gemini':
                return get_gemini_response($prompt);
            default:
                return 'Error: unknown AI engine';
        }
    }
}

function ai_translate_content($content, $target_lang){
    $prompt = 'Переклади цей текст на наступну мову: ' . $target_lang . ' і поверни лише переклад без жодних коментарів, попереджень та застережень щодо контексту, якщо в тексті є HTML структура - ти маєш зберегти її в перекладі. Оберни результат в теги <result></result>: ' . $content;
    $response = ai_responsive_wrapper($prompt);
    if (is_wp_error($response)) {
        return '';
    }
    if (!is_string($response)) {
        return '';
    }
    if (preg_match('/<result>(.*?)<\/result>/is', $response, $matches)) {
        return trim($matches[1]);
    }
    return trim(str_ireplace(['<result>', '</result>', '```html', '```'], '', $response));
}

add_action( 'init', function(){
    if(is_admin() && isset($_GET['test_ai']) && $_GET['test_ai']=='ok' && current_user_can('manage_options')){
        $prompt = "Привіт, з якою моделлю я наразі спілкуюся? Напиши мені цікаві факти про себе.";
        pr(ai_translate_content($prompt, 'ru'));
    }
});

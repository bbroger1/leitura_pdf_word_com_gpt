<?php

namespace App;
class Gemini
{

    public function completion(string $original_prompt, string $model, string $system, array $chat_history, string $context)
    {


        $API_KEY = MODEL_API_KEY;
        $gemini_way = [];
        if (!empty($chat_history["messages"])) {
            $history = $chat_history["messages"];
            foreach ($history as $item) {
                if (!empty($item["role"]) && ($item["role"] == "assistant" or $item["role"] == "user")) {
                    $name = $item["role"];
                    if ($item["role"] == "assistant") {
                        $name = "model";
                    }
                    $gemini_way[] = [
                        "role" => $name,
                        "parts" => [
                            [
                                "text" => $item["content"]
                            ]
                        ]
                    ];
                }
            }
        } else {
            exit("Gemini: message is empty");
        }


        $data = array(
            "contents" => array(
                array(
                    "role" => "user",
                    "parts" => array(
                        array(
                            "text" => "System prompt: " . $system
                        )
                    )
                ),
                array(
                    "role" => "model",
                    "parts" => array(
                        array(
                            "text" => "Entendido."
                        )
                    )
                )
            )
        );

        $prompt = "$context\r\n Pergunta: $original_prompt";

        $last_prompt = array(
            "role" => "user",
            "parts" => array(
                array(
                    "text" => $prompt
                )
            )
        );

        if(count($gemini_way) > 0){
            $data['contents'][] = $gemini_way;
        }
        $data['contents'][] = $last_prompt;

        $safety_settings = [
            [
                'category' => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => 'BLOCK_NONE'
            ],
            [
                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => 'BLOCK_NONE'
            ],
            [
                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => 'BLOCK_NONE'
            ],
            [
                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => 'BLOCK_NONE'
            ]
        ];
        $data['safetySettings'] = $safety_settings;

        $postData = json_encode($data);
        $curl = curl_init();

      //  $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$API_KEY";
        $endpoint = str_replace(["{{model}}","{{api_key}}"], [$model, $API_KEY], MODEL_ENDPOINT);
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        print_r($postData);
//        exit();
        $response = curl_exec($curl);
        if(!PRODUCTION && DO_DEBUG) {
            print_r($response);
        }
        curl_close($curl);
        $arr = json_decode($response);
        $text = $arr->candidates[0]->content->parts[0]->text ?? '';
        $error_msg = $arr->error->message ?? false;
        $blocked = $arr->promptFeedback->blockReason ?? false;
        if ($error_msg OR $blocked) {
             echo "Ops, houve um erro: {$error_msg} - {$blocked} ";
           return "";
        }
        return $text;

    }





    public function vision(string $prompt, string $model, string $system, $img_url)
    {

        if(!filter_var($img_url, FILTER_VALIDATE_URL)){
            exit('img url não é válida');
        }
        if(!$this->isImage($img_url)){
            exit('URL não parece ser imagem');
        }
        $API_KEY = MODEL_API_KEY;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $system],
                        ["text" => $prompt],
                        ["text" => "Object: "],
                        [
                            "inlineData" => [
                                "mimeType" => "image/jpeg",
                                "data" => base64_encode(file_get_contents($img_url))
                            ]
                        ],
                        ["text" => "Description: "]
                    ]
                ]
            ]
        ];

        $generationConfig = [
            "temperature" => 0.9,
            "topK" => 40,
            "topP" => 0.95,
            "maxOutputTokens" => 55555,
            "stopSequences" => []
    ];
        $data['generationConfig'] = $generationConfig;
        $safety_settings = [
            [
                'category' => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => 'BLOCK_NONE'
            ],
            [
                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => 'BLOCK_NONE'
            ],
            [
                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => 'BLOCK_NONE'
            ],
            [
                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => 'BLOCK_NONE'
            ]
        ];
        $data['safetySettings'] = $safety_settings;

        $postData = json_encode($data);

        $curl = curl_init();

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.0-pro-vision-latest:generateContent?key=$API_KEY";

        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        if(!PRODUCTION && DO_DEBUG) {
            print_r($response);
        }
        curl_close($curl);
        $arr = json_decode($response);
        $text = $arr->candidates[0]->content->parts[0]->text ?? '';
        $error_msg = $arr->error->message ?? false;
        $blocked = $arr->promptFeedback->blockReason ?? false;
        if ($error_msg OR $blocked) {
            echo "Ops, houve um erro: {$error_msg} {$blocked} ";
            return "";
        } else {
            // print_r($arr->candidates);
            return $text;
        }

    }


    public function isImage(string $link){

        if(!filter_var($link, FILTER_VALIDATE_URL)){
            exit('Not URL');
        }
        $headers = get_headers($link, 1); // Obtém os cabeçalhos HTTP do arquivo
        if ($headers && (isset($headers['Content-Type']) OR isset($headers['content-type']))) {
            $contentType = $headers['Content-Type'] ?? $headers['content-type'];
            if (preg_match( '/image\//',$contentType)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }



}
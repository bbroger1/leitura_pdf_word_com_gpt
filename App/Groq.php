<?php
namespace App;
class Groq
{
    public function completion(string $prompt, string $model, $system, array $chat_history = [], string $llm = "groq", int $max_tokens = 1024)
    {
       // return "sou groq";
        $curl = curl_init();
        $chat_model = MODEL_NAME;
        $endpoint = MODEL_ENDPOINT;
        $api_key = MODEL_API_KEY;
        $llm = strtolower($llm);
        if($model !='default'){
            $chat_model = $model;
        }

        if($llm == 'openai' OR $llm =='claude'){
            $postFields = array(
                "messages" => array(
                    array("role" => "user", "content" => $prompt)
                ),
                "model" => $chat_model,
                "system" => $system,
                "max_tokens" => $max_tokens
            );

        }else if ($llm == 'groq'){
            $postFields = array(
                "messages" => array(
                    array("role" => "system", "content" => $system),
                    array("role" => "user", "content" => $prompt)
                ),
                "model" => $chat_model,
            );

        }else{
            return "LLM need to be groq or openai or claude!";
        }



        if(!empty($chat_history) && !empty($chat_history['messages'])){
            if($llm == "claude"){
                $system = array_shift($chat_history['messages']);
                $chat_history['system'] = $system['content'];
                $chat_history['max_tokens'] = $max_tokens;
            }
            $user_prompt = ["role" => "user", "content"=> $prompt];
            $chat_history['messages'][] = $user_prompt;
            $postFields = $chat_history;
        }

        $postFieldsJson = json_encode($postFields);
//        print_r($postFieldsJson);
//        exit();

        $http_header = array(
            "Authorization: Bearer " . $api_key,
            "anthropic-version: 2023-06-01",
            "Content-Type: application/json"
        );
        if($llm == "claude"){
            $http_header[0] =  "x-api-key: ".$api_key;
        }
//        echo "Eu sou groq";
//        exit();
//        print_r($postFields);
//        exit();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true, // Return response as string
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postFieldsJson,
            CURLOPT_HTTPHEADER => $http_header,
        ));

        $response = curl_exec($curl);
        if(!PRODUCTION && DO_DEBUG) {
            print_r($response);
        }
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            // Process the API response
            $result = json_decode($response);
            if($llm == 'claude'){
                $msg =  $result->content[0]->text ?? '';
            }else{
                $msg =  $result->choices[0]->message->content ?? '';
            }
        }
        if(empty($msg)){
            $arr = json_decode($response);
            $err_msg = $arr->error->message ?? '';
            if($err_msg){
                echo ucwords($llm).": $err_msg \n\n";
            }
        }
        return $msg;

    }
}
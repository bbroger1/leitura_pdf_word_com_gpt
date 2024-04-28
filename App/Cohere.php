<?php
namespace App;
class Cohere{
    public function goodSearchTerm(string $from_prompt, array $chat_history, string $system, $search_queries_only = true, $api_key = COHERE_QUERY_API_KEY){
        $last_from = "none";
        $cohere_way = [];
        $cohere_way[] = [
            "role" => "SYSTEM", "message"=> $system
            ];
        $messages = $chat_history['messages'] ?? [];
        foreach ($messages as $item){
            $role = $item['role'] ?? "";
            $cnt = $item['content'] ?? "";
            if($role == "user" && !empty($cnt)){
                if($last_from === "user"){
                    continue; // deve haver uma alternância entre o usuário e IA
                }
                $last_from = "user";
                $cohere_way[] = ["role" => "User",  "message"=> $cnt];
            }else if ($role == "assistant" && !empty($cnt)){
                if($last_from === "assistant"){
                    continue; // deve haver uma alternância entre o usuário e IA
                }
                $last_from = "assistant";
                $cohere_way[] = ["role" => "Chatbot",  "message"=> $cnt];
            }
        }
        $data = array(
            "chat_history" => $cohere_way,
            "message" => $from_prompt,
            "search_queries_only" => $search_queries_only,
            "model" => COHERE_QUERY_MODEL,
        );



        $data_string = json_encode($data);
        $endpoint = COHERE_QUERY_ENDPOINT;
        $ch = curl_init($endpoint);
//        print_r($data_string);
//        echo "<hr>";
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer " . $api_key
        ));

        $result = curl_exec($ch);
        $arr = json_decode($result);
        curl_close($ch);
        $text = $arr->text ?? '';
        $text =  $arr->search_queries[0]->text ?? $text;
        if(!PRODUCTION && DO_DEBUG) {
            print_r($result);
        }
        $err_msg = $arr->message ?? '';
        if(empty($text) && !empty($err_msg)){
            echo "<b>Cohere</b>: $err_msg \n\n ";
        }
        return $text;
    }

    public function chat(string $from_prompt, array $chat_history, string $system){
        return $this->goodSearchTerm($from_prompt, $chat_history, $system, false, MODEL_API_KEY);
    }
}
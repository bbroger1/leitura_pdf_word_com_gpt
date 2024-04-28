<?php
//echo strtolower(trim("Dicas para iniciar a prática da tenepes"));
//exit();
require __DIR__ . "/../../../../../../../config.php";
require __DIR__ . "/../../../../../../../vendor/autoload.php";
$Database = new \App\Database();
$RagTechnique = new \App\RagTechnique();
$VectorSearch = new App\VectorSearch();
$user_input = json_decode(file_get_contents("php://input"), true);

$prompt = null;
$max_res = 17;
$max_tokens = 1024;
$max_msgs_allowed = 2; // mensagens do histórico/contexto
$company = COMPANY;
$model = MODEL_NAME;
$llm = $company;
if (!empty($_SERVER['HTTP_REFERER'])) {
    $llm_get_param = $_SERVER['HTTP_REFERER'];
    preg_match("/llm=(.*)/s", $llm_get_param, $match);

    if (!empty($match[1])) {
        $llm_get_param = preg_replace("/[^a-z]/", "", $match[1]);
    }
    $valid_llm = ['gemini', 'groq', 'cohere', 'claude'];
    if (in_array($llm_get_param, $valid_llm)) {
        $llm = $llm_get_param;
    } else {
        //  exit('não é valido '.$llm_get_param);
    }
}


$force_model = 'default'; // automático


$do_rag = DO_RAG;
if (!empty($prompt) && str_contains($prompt, "rag:no")) {
    $do_rag = false;
    $prompt = str_replace("rag:no", "", $prompt);
}
$chat_history = [];
if (!empty($user_input['messages'])) {
    $messages = $user_input['messages'];
    if (is_array($messages)) {
        $roles = [];
        $total_msgs = 0;
        foreach ($messages as $m) {
            if (!empty($m["role"]) && $m["role"] == 'user') {
                $total_msgs++;
            }
        }

        foreach ($messages as $item) {
            if (!empty($item["role"])) {
                if ($item["role"] == "user") {
                    $user_cnt = $item["content"];
                    $roles[] = ["role" => "user", "content" => $user_cnt];
                    $prompt = $user_cnt;
                }
                if ($item["role"] == "assistant") {
                    $agent_cnt = $item["content"];
                    $roles[] = ["role" => "assistant", "content" => $agent_cnt];
                }
            }
        }

        if ($total_msgs > $max_msgs_allowed) {
            $total_to_remove = $total_msgs - $max_msgs_allowed;
            for ($idx = 0; $idx < $total_to_remove; $idx++) {
                $removed = array_shift($roles);
            }
            if (!empty($removed['role']) && $removed['role'] == 'user') {
                array_unshift($roles, $removed);
                // obriga o primeiro role ser user, Claude/Anthropic exige isso
            }

        }
        if ($llm != 'cohere') {
            array_pop($roles);
            // remove prompt atual do usuário)
            // ele(o prompt) será passado com o contexto mais abaixo no código
            // mas apenas para groq, cohere e claude
        }

        $role_system = ["role" => "system", "content" => CHAT_SYSTEM_PROMPT];
        array_unshift($roles, $role_system); // adiciona ao ínicio do array

        if ($force_model != 'default') {
            $chat_model_here = $force_model;
        }
        $chat_history = array(
            "messages" => $roles,
            "model" => MODEL_NAME,
            // "max_tokens" => 1024
        );
    }
} else {
    exit('no messages.');
}

$term = $prompt;
$img_url = '';


if (!empty($prompt) && str_contains($prompt, "rag:no")) {
    $do_rag = false;
    $prompt = str_replace("rag:no", "", $prompt);
}


$Cohere = new \App\Cohere();
if($do_rag){
    $search_term = $Cohere->goodSearchTerm($prompt, $chat_history, COHERE_QUERY_GEN_PROMPT);
}else{
    $search_term = '';
}
$search_term = trim($search_term);
if (empty($search_term)) {
    $search_term = $prompt;
} else {
    if ($search_term != $prompt) {
        echo "st:[$search_term]\n";
    }
}

if (!empty($prompt)) {

    if (preg_match("/img\((.*)\)/", $prompt, $match)) {
        $img_url = $match[1] ?? '';
        $img_url = trim($img_url);
        $prompt = preg_replace("/img\((.*)\)/", ":", $prompt);
        $do_rag = false;
        /// Vai usar o VISION então não irá fazer RAG
    }

    if ($do_rag) {
        $context = $VectorSearch->doSearch($search_term, $max_res, 2, 2, true, 0.1);
        if (empty(trim($context))) {
            exit('Não foi possível encontrar resultados para: ' . $search_term);
        }
    } else {
        $context = " ";
    }

    if (mb_strlen($prompt) > 490) {
        $prompt = mb_substr($prompt, 0, 490);
    }
    $original_prompt = $prompt;
    if ($do_rag) {
        $prompt = "Contexto: <article>$context</article>\n Pergunta: $prompt\n Sua resposta:";
    }
    if (!$do_rag) {
        $prompt = $original_prompt;
    }

    $context = "Contexto: <article>$context</article>"; // changes
   // $prompt = preg_replace("/\r|\n|\r\n/", " ", $prompt); // talvez descomentar
    //$prompt = str_replace(["\"", "'", "“", "", "”"], " ", $prompt); /// mesmo

    // model é escolhido automaticamente de acordo o LLM
    // caso queiro outro model bastar passar o nome do model em vez de "dafult"
    $system = CHAT_SYSTEM_PROMPT;
    $few_shot_system = "Verifique o contexto informado  e retorne um JSON no seguinte formato {\"search_terms\": [term1, term2, ...]} com os termos de pesquisa que você precisa para responder a pergunta : \"$original_prompt\"";
//    if($do_rag){
//        $few_shot_system = trim($system,".").". $few_shot_system"; // change
//    }


//    if($llm !='gemini'){
//        if(!empty($chat_history['messages'])){
//            $total_roles = count($chat_history['messages']);
//            $role_content = $chat_history['messages'][($total_roles - 1)]['content'] ?? '[vazio]';
//            $chat_history['messages'][($total_roles - 2)]["content"] = "\n ---- $context";
//        }
//    }

    if (!empty($chat_history)) {
        if ($llm == "gemini") {
            $Gemini = new \App\Gemini();
            if ($img_url) {
                $msg = $Gemini->vision($original_prompt, '', 'O que você ver? Descreva com detalhes!', $img_url);
            } else {
                if ($do_rag AND DO_EXTRA_SEARCH) {
                    $msg = $Gemini->completion($original_prompt, $model, $few_shot_system, $chat_history, $context);
                    $more_context = $RagTechnique->extraSearch($msg, $VectorSearch, $original_prompt);
                    if ($more_context) {
                        $context .= " \n $more_context";
                        $msg = $Gemini->completion($original_prompt, $model, $system, $chat_history, $context);
                    }
                } else {
                    $msg = $Gemini->completion($original_prompt, $model, $system, $chat_history, $context);
                }

            }
        } else if ($llm == "cohere") {
            //  exit("Você chamou cohere; Exiting!");
            $Cohere = new \App\Cohere();
            $msg = $Cohere->chat($prompt, $chat_history, $system);
        } else {
            $Groq = new \App\Groq();
            $msg = $Groq->completion($prompt, $model, $system, $chat_history, $llm, $max_tokens);
        }
        if (!empty($msg)) {
            echo "[llm=$llm]\n\n{$msg}";
        } else {
            // header('HTTP/1.1 503 Service Unavailable');
            echo " Ops. Houve um erro!";
        }
    } else {
        //  header('HTTP/1.1 400 Bad request');
        exit('empty history/messages.');
    }

} else {
    //  header('HTTP/1.1 400 Bad request');
    echo "prompt is empty";
}

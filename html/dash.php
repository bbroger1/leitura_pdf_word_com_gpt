<?php


session_start();
set_time_limit(0);
require __DIR__ . "/../config.php";

function changeInfo($new_name, $new_desc){
    $old_name = 'SapiensRAG'; // SapiensRAG
    $old_desc = "Making RAG easy!"; // Making RAG easy.
    $page_path_original =  __DIR__."/chat/_next/app/original_page_ee64c25ce188d23c.js";
    $page_path = __DIR__."/chat/_next/app/page-ee64c25ce188d23c.js";

    $index_path = __DIR__."/chat/index.html";
    $index_path_original = __DIR__."/chat/original_index.html";

    $cnt = file_get_contents($page_path_original);
    $cnt = str_replace($old_name, $new_name, $cnt);
    $cnt = str_replace($old_desc, $new_desc, $cnt);
    $status_script = file_put_contents($page_path, $cnt);


    $cnt_index = file_get_contents($index_path_original);
    $cnt_index = str_replace("$old_name","$new_name", $cnt_index);
    $cnt_index = str_replace($old_desc, $new_desc, $cnt_index);
    $status_index = file_put_contents($index_path, $cnt_index);
    if($status_index && $status_script){
        return true;
    }else{
        return false;
    }
}


$login_attempt = $_POST['pass'] ?? false;
if($login_attempt){
    $_SESSION['adm_pass'] = $login_attempt;
}
$session_pass = $_SESSION['adm_pass'] ?? false;
if(!$session_pass OR ($session_pass != DASHBOARD_PASSWORD)){
    require __DIR__."/login_template.html";
    exit('');
}
//require __DIR__ . "/../vendor/autoload.php";
$Indexator = new \App\Indexator();
$Vectara = new \App\Vectara();
$Database = new \App\Database();



$temp_file = $_FILES['file']['tmp_name'] ?? false;
$file_name = $_FILES['file']['name'] ?? '';

$active_corpus = $_POST['active_corpus'] ?? false;
if ($active_corpus) {
    $corpus_sql = "UPDATE vectara_config SET active = 0 WHERE active = 1"; // deactivated old
    $Database->update($corpus_sql, []);
    // activate the new
    $corpus_sql = "UPDATE vectara_config SET active = 1 WHERE id = :id";
    $Database->update($corpus_sql, ['id' => $active_corpus]);
}


$new_sys_prompt = $_POST['sys_prompt'] ?? false;
if($new_sys_prompt){
    $sql_up_prompt = "UPDATE models_config SET model_prompt = :sys_p WHERE active = 1";
    $Database->update($sql_up_prompt, ['sys_p'=> $new_sys_prompt]);
}



$sql_model_active = "SELECT model_name, company,model_prompt FROM models_config WHERE active = 1 LIMIT 1";
$data = $Database->select($sql_model_active, [])->fetch();
$active_model = $data->model_name ?? '';
$active_company = $data->company ?? '';
$sys_prompt = $data->model_prompt ?? '';

$set_model = $_POST['set_model'] ?? false;
$set_company = $_POST['company'] ?? false;
$set_api_key = $_POST['api_key'] ?? false;
if($set_api_key){
    $set_api_key = trim($set_api_key);
}
if ($set_model && $set_company) {
    // disable the current model active
    $sql = "UPDATE models_config SET active = 0 WHERE active = 1";
    $Database->update($sql, []);
    if ($set_api_key) {
        $set_company = trim($set_company);
        $sql_set_new = "UPDATE models_config SET active = 1, model_name = :model_name, api_key = :api_key WHERE company = :company";
        $Database->update($sql_set_new, ['model_name' => $set_model, 'company' => $set_company, 'api_key' => $set_api_key]);
        $api_is_set = true;
    } else {
        $sql_set_new = "UPDATE models_config SET active = 1, model_name = :model_name WHERE company = :company";
        $Database->update($sql_set_new, ['model_name' => $set_model, 'company' => $set_company]);
    }
    $active_model = $set_model;
    $active_company = $set_company;
}

$upload = null;
if ($temp_file) {
    $mime = mime_content_type($temp_file);
    if($mime === "application/pdf" OR $mime === "application/x-pdf"){
        $is_pdf = 1;
    }else{
        $is_pdf = 0;
    }

    $title = $Indexator->getFileTitleRuddly($file_name);
    $file_hash = hash_file('sha256', $temp_file);
    $sql_hf = "SELECT file_hash FROM uploaded WHERE file_hash = :fh";
    if(!$Database->select($sql_hf, ['fh'=> $file_hash])->rowCount()){
        $upload = $Vectara->uploadFiles($title, $temp_file, $file_hash);

        $sql_set_upado = "INSERT INTO uploaded SET file_hash = :fh, file_name = :fname, file_path = :fpath, is_pdf = :is_pdf";
        $binds_sp = ['fh'=> $file_hash, 'fname'=> $title, 'fpath'=>'temporário','is_pdf' => $is_pdf];
        $Database->insert($sql_set_upado, $binds_sp);

    }else{
        echo "Arquivo já upado!";
        $upload = false;
    }
}

$vectara_customer_id = $_POST['vectara_customer_id'] ?? false;
$vectara_corpus_id = $_POST['vectara_corpus_id'] ?? false;
$vectara_api_key = $_POST['vectara_api_key'] ?? false;
$description = $_POST['vectara_description'] ?? false;

if ($vectara_customer_id && $vectara_corpus_id && $vectara_api_key && $description) {
    $sql = "INSERT INTO vectara_config(corpus_id, customer_id, api_key, description) VALUES (:corpus_id, :customer_id, :api_key, :desc)";
    $binds = [
        'corpus_id' => $vectara_corpus_id,
        'customer_id' => $vectara_customer_id,
        'api_key' => $vectara_api_key,
        'desc' => $description
    ];
    $Database->insert($sql, $binds);
}

$sql_vectara = "SELECT corpus_id, customer_id, id, active, description FROM vectara_config";
$vectara_data = $Database->select($sql_vectara, [])->fetchAll();


$model_config = [
    "openai" => [
        "models" => [
            'gpt-4-turbo',
            'gpt-4-turbo-2024-04-09',
            'gpt-3.5-turbo',
            'gpt-3.5-turbo-1106',
            'gpt-4-0125-preview',
            'gpt-4-1106-preview',
            'gpt-4-0613',
            'gpt-4-32k',
            'gpt-4-32k-0613'
        ],
        'endpoint' => ''
    ],

    "claude" => [
        "models" => [
            'claude-3-opus-20240229',
            'claude-3-sonnet-20240229',
            'claude-3-haiku-20240307'
        ],
        'endpoint' => ''
    ],

    "gemini" => [
        "models" => [
            'gemini-1.5-pro-latest',
            'gemini-1.0-pro'
        ],
        'endpoint' => ''
    ],

    "groq" => [
        "models" => [
            'llama3-70b-8192',
            'llama3-8b-8192',
            'llama2-70b-4096',
            'mixtral-8x7b-32768',
            'gemma-7b-it'
        ],
        'endpoint' => ''
    ],

    "cohere" => [
        "models" => [
            'command-r-plus',
            'command-r'
        ]
    ]

]
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="dash change_name">
            <h1>Alterar nome e descrição do chat.</h1>
            <p>Uma descrição menor vai se encaixar melhor.</p>
            <form method="post">
                <input name="chat_name" type="text" placeholder="Nome do chat">
                <input name="chat_desc" type="text" placeholder="Descrição do chat">
                <button>Mudar</button>
            </form>
            <?php
            $new_chat_name = $_POST['chat_name'] ?? false;
            $new_chat_desc = $_POST['chat_desc'] ?? false;
            if($new_chat_desc && $new_chat_name){
                $has_changed = changeInfo($new_chat_name, $new_chat_desc);
                if($has_changed){
                    echo "<div class='success'>Nome modificado com sucesso</div>";
                    echo "<p>Atenção: Cache do navegador pode fazer o nome e descrição antiga continuar aparecendo por algum tempo.</p>";
                    echo "<p>Você pode verificar se a alteração deu certo abrindo o link em uma guia anônima</p>";
                }else{
                    echo "<div class='warning'>Ops. Houve um erro. Verifique se o arquivo tem permissão de escrita</div>";
                }
            }
            ?>
        </div>
        <div class="dash">
            <h1>Configuração Vectara</h1>
            <form method="post">
                <input name="vectara_customer_id" type="number" placeholder="Customer ID">
                <input name="vectara_corpus_id" type="number" placeholder="Corpus ID">
                <input name="vectara_api_key" type="text" placeholder="Vectara API key">
                <input maxlength="45" name="vectara_description" placeholder="Pequena descrição">
                <button>Setar</button>
            </form>

            <?php
            $active_corpus_id = 'none';
            $active_corpus_desc = 'none';
            if ($vectara_data) {
                echo "<p>Você pode ativar um corpus específico abaixo</p>";
                foreach ($vectara_data as $item) {
                    $active_corpus = '';
                    $btn_status = '';
                    $btn_text = '';
                    if ($item->active == 1) {
                        $active_corpus = 'active_corpus';
                        $btn_status = '';
                        $btn_text = 'title="Já está ativo"';
                        $active_corpus_id = $item->corpus_id;
                        $active_corpus_desc = $item->description;
                    }
                    echo "<div class='center'>";
                    echo "<form method='post'>";
                    echo "<div class='vectara_corpus $active_corpus'>";
                    echo "<p>Corpus ID: $item->corpus_id</p>";
                    echo "<p>Customer ID: $item->customer_id</p>";
                    echo "<p>Descrição: $item->description</p>";
                    echo "<input type='hidden' value='$item->id' name='active_corpus'>";
                    echo "<button $btn_text  $btn_status class='$active_corpus'>Activate</button>";
                    echo "</div>";
                    echo "</form>";
                    echo "</div>";
                }
            }
            ?>
        </div>
        <div class="height_10px"></div>
        <div class="height_10px"></div>
        <div class="dash">
            <h1>Upload</h1>
            <p>Enviar arquivos para Vectara</p>
            <form method="post" enctype="multipart/form-data">
                <label for="file">Select a file</label>
                <input id="file" name="file" type="file">
                <button>Upload</button>
            </form>
            <p><b class="warning">Atenção:</b> O upload será feito no corpus ativo no momento, que no caso é o corpus
                <b><?= $active_corpus_id ?></b> <br> Descrição: <b><?= $active_corpus_desc ?></b></p>
            <?php
            if ($upload) {
                echo "<div class='success' id='scrool_to_view'>Upload de <b>[$file_name]</b> feito com sucesso!</div>";
            } else {
                if ($upload !== null) {
                    echo "<div class='alert' id='scrool_to_view'>Upload de <b>[$file_name]</b> falhou!</div>";
                }
            }
            ?>
            <p>Os arquivos enviados para Vectara serão usados como contexto para as respostas da IA</p>
        </div>
        <p>Coloque seus arquivos em <b><?= __DIR__ ?>/pdf</b> e execute add_files.php para fazer upload de vários
            arquivos de uma vez.</p>
        <div class="dash">
            <h1>Prompt de sistema:</h1>
            <form method="post">
                <textarea name="sys_prompt" placeholder="Instrua o modelo a se comportar de determinada maneira."><?= $sys_prompt ?></textarea>
                <br><button>Ativar</button>
            </form>
        </div>
        <div class="dash">
            <h1>Escolha um Modelo de IA</h1>
            <p>Escolha o modelo que irá responder no chat.</p>
            <?php
            if (empty($active_model)) {
                echo "<div class='warning'>Você não tem nenhum modelo ativo.</div>";
            } else {
                echo "<p>O modelo ativo no momento é <span class=\"hl\">$active_model</span> da <span
                        class=\"hl\">" . ucwords($active_company) . "</span></p>";
            }
            foreach ($model_config as $company => $models) {
                $is_active = '';
                if (strtolower($company) == strtolower($active_company)) {
                    $is_active = 'is_active';
                }

                ?>
                <div class="all_models">
                <form class="models_list" method="post">
                    <div class="height_35px">Chave API para <?= ucwords($company) ?></div>
                    <input placeholder="API Key" type="text" name="api_key"><br><br>
                    <span class="company <?= $is_active; ?>">Modelo</span><br>
                    <input type="hidden" name="company" value="<?= $company ?>">
                    <select name="set_model">
                        <?php
                        foreach ($models['models'] as $idx => $model) {
                            $is_model_active = '';
                            if (strtolower($model) == strtolower($active_model)) {
                                $is_model_active = 'active_model';
                            }
                            echo "<option class='$is_model_active' value=\"$model\">$model</option>";
                            ?>
                        <?php } ?>
                    </select>
                    <button>Setar</button>
                </form>
                <?php
            }
            ?>
                </div>
        </div>
    </div>
</div>
<style>
    html {
        background-color: rgb(63 81 181 / 7%);
    }

    body {
        margin: 0;
    }

    label[for='file'] {
        padding: 8px;
        background-color: #fff;
        color: rgb(75, 69, 93);
        display: inline-block;
        border-radius: 4px;
        cursor: pointer;
    }

    input#file {
        display: none;
    }

    h1 {
        color: #fff;
    }

    button {
        cursor: pointer;
        border-radius: 4px;
        padding: 9px;
        display: inline-block;
        background-color: #fff;
        color: rgb(75, 69, 93);
        border: none;
    }

    .vectara_corpus button {
        background-color: #2161c6;
        font-weight: 600;
        margin-bottom: 6px;
        color: #fff;
    }

    .content {
        text-align: center;
    }

    .dash {
        border-radius: 4px;
        width: 60%;
        margin: 15px 20%;
        padding: 2px 0;
        background-color: rgb(255, 139, 68);
        min-height: 250px;
        box-sizing: border-box;
        font-size: 1.2em;
    }

    .success, .error {
        padding: 8px;
        background-color: #35bc4a;
        border-radius: 4px;
        color: #fff;
    }

    .error {
        background-color: #ff5646;
    }

    .models_list {
        padding: 25px 8px;
        box-sizing: border-box;
        background-color: #1f2ad4;
        color: #fff;
        margin-bottom: 5px;
        font-size: 1.2em;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    select {
        padding: 8px;
        border-radius: 4px;
    }

    input {
        padding: 7px;
        border: none;
        border-radius: 4px;
    }

    option {
        font-weight: 600;
        color: #484648;
    }

    .company {
        color: #fff;

    }

    .is_active {
        /*background-color: #35bc4a;*/
        /*padding: 8px;*/
        /*border-radius: 4px;*/
    }

    .active_model {
        padding: 8px;
        background-color: #35bc4a;;
        color: #fff;
    }

    .warning {
        background-color: #554f4e;
        color: #fff;
        font-weight: 600;
        padding: 6px;
        border-radius: 4px;
    }

    .alert{
        background-color: #c11100;
        padding: 4px 8px;
        color: #fff;
        margin: 5px 0;
    }

    .hl {
        padding: 5px;
        background-color: #4d614e;
        border-radius: 6px;
        color: #fff;
    }

    .height_10px {
        height: 10px;
    }

    .height_35px {
        height: 35px;
    }


    .center {
        text-align: center;
        width: 100%;
    }

    .vectara_corpus {
        background-color: #fff;
        width: 250px;
        display: inline-block;
        padding: 0 15px;
        border-radius: 5px;
        box-sizing: border-box;
        margin-bottom: 5px;
    }

    .active_corpus {

    }

    button.active_corpus {
        background-color: #edd8d8;
        border: none;
    }
    .change_name{
        background-color: #282a48;
        color: burlywood;
    }
    textarea[name='sys_prompt']{
        width: 70%;
        height: 80px;
        border-radius: 6px;
        padding: 5px;
    }
    .all_models{
        background-color: #fff;
    }
</style>
<script>
    let active_corpus = document.querySelector(".active_corpus button");
    if (active_corpus) {
        active_corpus.onclick = (event) => {
            event.preventDefault();
            event.stopPropagation();
            alert('Esse corpus já está ativo!');
        }
    }

    let scrool_to_view = document.getElementById('scrool_to_view');
    if(scrool_to_view){
        scrool_to_view.scrollIntoView();
        setTimeout(()=>{
            scrool_to_view.scrollIntoView();
        },1000);
    }
</script>
</body>
</html>
<?php
require_once __DIR__."/vendor/autoload.php";
const PRODUCTION = false;
const DO_DEBUG = false;

const DASHBOARD_PASSWORD = 'senha_forte_aqui';

const DO_EXTRA_SEARCH = true; /// apenas para gemini por enquanto
/// o modelo poderá requisitar pesquisas extras para responder à pergunta
// essa funcionalidade só é usada quando DO_RAG também estiver ativo
const DO_RAG = true; // define se irá fazer RAG ou seja usar conteúdo da base de dados como contexto para a IA

$css_version = rand(0,9999);



/**
// Google Search - Possível Funcionalidade no Futuro
const GOOGLE_SEARCH_API_KEY = '';
// pegue aqui seu API key em: https://developers.google.com/custom-search/v1/overview?hl=pt-br
const GOOGLE_SEARCH_CX = ''; // O ID do Mecanismo de Pesquisa Programável a ser usado na solicitação.
// Pegue seu CX ID em: https://programmablesearchengine.google.com/controlpanel/all
 **/

#DATABASE DEFINITIONS
const DB_CONFIG = [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => 'sapiens_rag', // nome da base de dados
    "username" => 'crud', // usuário
    "password" => 'love', // senha
    "options" => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
];

$Database = new \App\Database();



$sql = "SELECT model_name, api_key, company, model_prompt, model_endpoint FROM models_config WHERE active = :active LIMIT 1";
$model_data = $Database->select($sql, ['active' => 1])->fetch();
$model_name = $model_data->model_name ?? '';
$model_api_key = $model_data->api_key ?? '';
$company = $model_data->company ?? '';
$model_prompt = $model_data->model_prompt ?? '';
$model_endpoint = $model_data->model_endpoint ?? '';



define("CHAT_SYSTEM_PROMPT", $model_prompt);
define("COMPANY", $company);
define('MODEL_NAME', $model_name);
define('MODEL_API_KEY', $model_api_key);
define("MODEL_ENDPOINT", $model_endpoint);


const COHERE_QUERY_GEN_PROMPT = "Você é um excelente gerador de termos de pesquisa que permite ao usuário receber melhores resultados. Gere novos termos com base no termo passado. ";
const COHERE_QUERY_ENDPOINT = 'https://api.cohere.ai/v1/chat';
const COHERE_QUERY_MODEL = 'command-r-plus';
const COHERE_QUERY_API_KEY = '';


if(!PRODUCTION){
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

$sql = "SELECT corpus_id, customer_id, api_key FROM vectara_config WHERE active = 1 LIMIT 1";
$vec_data = $Database->select($sql, [])->fetch();
$vectara_corpus_id = $vec_data->corpus_id ?? '';
$vectara_customer_id = $vec_data->customer_id ?? '';
$vectara_api_key = $vec_data->api_key ?? '';

define('VECTARA_API_KEY', $vectara_api_key);

define('VECTARA_CUSTOMER_ID', $vectara_customer_id);

define('VECTARA_CORPUS_ID', $vectara_corpus_id);


<?php
namespace App;
use CURLFile;

class Vectara{
    public function indexDocument($document){

        $url = 'https://api.vectara.io/v1/index';
        $index_data = [
            'corpusId' => VECTARA_CORPUS_ID,
            'customerId' => VECTARA_CUSTOMER_ID,
            'document' => $document
        ];

//        echo "<pre>";
//        print_r($index_data);
//        exit();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'x-api-key:' . VECTARA_API_KEY,
            'customer-id: ' . VECTARA_CUSTOMER_ID,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($index_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $status = json_decode($result)->status->code ?? 'error';
        if(strtolower($status) == "ok"){
            return true;
        }
        print_r($status);
        return false;

    }


    public function isFileMimeValid($file_path){
        $mime = mime_content_type($file_path);
        $all_mimes = [
             'text/markdown', 'text/plain',
             'application/pdf','application/x-pdf',
             'application/vnd.oasis.opendocument.text',
             'application/msword', 'application/vnd.ms-office',
             'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip',
             'application/vnd.ms-powerpoint',
             'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip',
             'text/plain',
             'text/html',
             'application/xml',
             'application/rtf', 'text/rtf',
             'application/epub+zip',
            ];
        if (in_array($mime, $all_mimes)) {
            return true;
        }
        if(explode("/", $mime)[0] == 'text'){
            return true;
        }
        return false;
    }
    public function uploadFiles($title, $file_path, $doc_hash){
        //PDFs must contain text: Vectara does not currently support indexing scanned images via OCR.
        if(!is_file($file_path)){
            echo ('Not a file: '.$file_path);
            return false;
        }
        if(!$this->isFileMimeValid($file_path)){
            echo('Not a valid mime from file: '.$file_path);
            return false;
        }
        $curl = curl_init();
        $postFields = array(
            'file' => new CURLFile($file_path),
            'doc_metadata' => json_encode([
                'doc_hash' => $doc_hash,
                'p_title' => $title
            ])
            );
        $endpoint = 'https://api.vectara.io/v1/upload?c='.VECTARA_CUSTOMER_ID.'&o='.VECTARA_CORPUS_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: multipart/form-data',
                'Accept: application/json',
                'x-api-key: '.VECTARA_API_KEY
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $obj = json_decode($response);
        $success = $obj->response->status->code ?? '';
        if($success =='' OR strtolower($success) == 'ok'){
            // Vectara parece retornar um objeto status vazio quando o upload é realizado
            // No entanto, acho que isso é um erro deles, e no futuro pode ser que venha como ok
            // esse código está preparado para esse cenário
            $sc =  $obj->status->code ?? ''; // em outros casos pode vir com status fora de "response"
            if(strtolower($sc) !='ok' && $sc !=''){
                print_r($obj);
                return false;
            }
            $http_code = $obj->httpCode ?? 200;
            $http_code = (int) $http_code;
            if($http_code >= 400){
                print_r($obj);
                return false;
            }
            return  true;
        }
        return false;


    }
}
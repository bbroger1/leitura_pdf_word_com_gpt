<?php
set_time_limit(0);
require __DIR__."/../config.php";
require __DIR__."/../vendor/autoload.php";
$Indexator = new \App\Indexator();
$Vectara = new \App\Vectara();

$CaptionsHandle  = new \App\CaptionsHandle();
echo "CaptionsHandle<br>";
$start_time = time();
$folder = __DIR__."/pdfs";
$files = $Indexator->mapFolders($folder);
$arr = $Indexator->getProcessed();
$processed = array_column($arr, 'file_path'); // mudar para file_hash
$total_processed = 0;

foreach ($files as $file_path){

    $mime = mime_content_type($file_path);
    if($mime === "application/pdf" OR $mime === "application/x-pdf"){
        $is_pdf = true;
    }else{
        $is_pdf = false;
    }
    $doc_hash = hash_file("sha256", $file_path);
    $file_name = basename($file_path);
    if(in_array($file_path,  $processed)){ // mudar para $doc_hash
       // echo "Já processado: $file_name<br>";
        continue;
    }else{
        echo "Processando: $file_name<br>";
    }


    if($is_pdf){
        $tot_pages =  $Indexator->pdfTotalPages($file_path);
        $rude_title = $Indexator->getFileTitleRuddly($file_name);
        $doc_sections = [];
        if($tot_pages > 0){
            $pg = 1; /// precisar iniciar com 1, 0 faria extrair to.do texto
            $all_cnt = [];
            while ($pg <= $tot_pages){
                $options = ["-f $pg", "-l $pg"];
                $cnt = $Indexator->pdfToText($file_path, $options);
                $text_now = $Indexator->cleanText($cnt);
                $all_cnt[$pg] = $text_now;
                $doc_sections[] = ["id"=> $pg, "text" => $text_now];
                $pg++;
            }
            // $json_doc = json_encode($all_cnt);

            $title = $Indexator->smartTitleDetect($all_cnt, $tot_pages);
            $remove = $title;
            if(empty($title) OR strlen($title) < 3){
                $title = $rude_title;
                $remove = '';
            }
            foreach ($all_cnt as $pg => $cnt){
                if(strlen($cnt) < 5){
                    echo "Página {$pg} de $file_path está vazia <br>";
                    continue;

                }
                $title = strtolower($title);
                $title = ucwords($title);
                $document = [
                    'documentId' => $doc_hash,
                    'title' => $title,
                    'section' => $doc_sections
                ];
            }
            if(!empty($document)){
                $status = $Vectara->indexDocument($document);
                if(!$status){
                    exit("Erro ao inserir $file_name<br>");
                }else{
                    $total_processed++;
                    $Indexator->postDB($file_name, $file_path, $doc_hash, 1);
                }
            }else{
                echo "Document is Empty<br>";
            }
        }else{
            $cnt = $Indexator->pdfToText($file_path);
            $cnt = $Indexator->cleanText($cnt);
            if(strlen($cnt) < 5){
                echo "<h2>Atenção</h2>";
                echo "Conteúdo vázio para $file_path<br>";
                continue;
            }
            $rude_title = ucwords($rude_title);
            $document = [
                'documentId' => $doc_hash,
                'title' => $rude_title,
                'section' => [
                    ["id"=> 1, "text"=> $cnt]
                ]
            ];
            $status = $Vectara->indexDocument($document);
            if(!$status){
                exit("Erro ao inserir conteúdo para $file_name<br> ");
            }else{
                $total_processed++;
                $Indexator->postDB($file_name, $file_path, $doc_hash, 1);
            }
        }
    }else{
        $title = $Indexator->getFileTitleRuddly($file_name);

        echo "file: $file_name <br>";


        $has_uploaded = $Vectara->uploadFiles($title, $file_path, $doc_hash);
        if($has_uploaded){
            $Indexator->postDB($file_name, $file_path, $doc_hash, 0);
        }else{
            echo "Erro ao fazer upload de $file_path<br>";
            exit('Verifique o erro');
        }


    }
   // exit();

    $passed_time = time() - $start_time; // tempo em segundos
    if($passed_time > 60){
        $sl_time = ($passed_time/60) * 5;
        if($sl_time > 15){
            $sl_time = 15;
        }
        echo "Descansando por {$sl_time}...<br>";
        $star_time = time();
        sleep(5);
    }

}
echo "$total_processed arquivos processados";

?>
<?php
namespace App;
use App\Database;
use Spatie\PdfToText\Pdf;
class Indexator{
    private Database $db;
    private Pdf $PDF;
    private string $setted_pdf_file ='';
    public function __construct()
    {
        $this->db = new Database();
        $this->PDF = new Pdf();
    }


    public function pdfToText($file_path, array $options = []){
        $this->PDF->setPdf($file_path);
        if($options){
            $this->PDF->setOptions($options);
        }
        return $this->PDF->text();
    }

    public function postDB($file_name, $file_path, $file_hash, int $is_pdf){
       $sql = "INSERT INTO uploaded SET file_name = :file_name, file_path = :file_path, file_hash = :file_hash, is_pdf = :is_pdf";
       $binds = ['file_name' => $file_name, 'file_path' => $file_path,'file_hash' => $file_hash, 'is_pdf' => $is_pdf];
       return $this->db->insert($sql, $binds);
    }

    public function pdfTotalPages($file_path){
        $file_path = escapeshellarg($file_path);
        exec("pdfinfo $file_path | grep -Po 'Pages:[[:space:]]+\K[[:digit:]]+'", $output);
        $total = $output[0] ?? 0;
        $total = (int) $total;
        return $total;
    }


    public function getFileTitleRuddly($file_path){
        $title = substr($file_path,0, strrpos($file_path, "."));
        $title = preg_replace("/site|-|_|\.|\(|\)/i", " ", $title);
        return trim($title);
    }

    public function mapFolders($folder)
    {
        $all_files  = [];
        $openedFolder = opendir($folder);
        while ($file = readdir($openedFolder)) {
            if ($file != '..' && $file != '.') {
                $completeFileName = "$folder/$file";
                if (is_dir($completeFileName)) {
                   $files =  $this->mapFolders($completeFileName);
                   $all_files = array_merge($files, $all_files);
                }else{
                    $all_files[] = $completeFileName;
                }
            }
        }
        return $all_files;
    }

    public function getProcessed(){
        $sql = "SELECT file_path,file_hash FROM uploaded";
        $select = $this->db->select($sql, []);
        if($select->rowCount()){
            $data = $select->fetchAll();
            return $data;
        }
        return  [];

    }
    public function hasProcessed($file_name){
        $sql = "SELECT doc_path FROM my_docs WHERE doc_path = :doc_path LIMIT 1";
        return $this->db->select($sql, ['doc_path'=> $file_name])->rowCount();
    }

    public function cleanText(string $text, string $removeRepeat = '')
    {


        if($removeRepeat && strlen($removeRepeat) < 100){
            $removeRepeat = preg_quote($removeRepeat);
            $text = preg_replace("/([0-9]+)?\s?\n?$removeRepeat\s?\n?([0-9]+)?/", "\n", $text);
            $text = preg_replace("/^([0-9]+)?\s?\n?$removeRepeat\s?\n?([0-9]+)?/", "\n", $text);
        }
        $text = preg_replace("/-­\n+/","-\n", $text);
        $text = preg_replace("/­\n+/","-\n", $text);
        $text = preg_replace("/­/","", $text);
        $text = preg_replace("/(-|–)(\n+)/", "", $text);
        $text = preg_replace("/\n+/", " ", $text);
        $text = preg_replace("/\s+/u", " ", $text);
        return $text;
    }

    public function smartTitleDetect(array $pages, int $tot_pages){
        if($tot_pages < 30){
            // pouco confiável se o arquivo tem poucas páginas
            return  '';
        }
        $first_texts = [];
        foreach ($pages as $cnt){
            $parts = explode("\n", $cnt);
           foreach ($parts as $item){
               $item = trim($item);
               if(strlen($item) > 0){
                   $first_texts[] = $item;
               }
           }
        }
        $repeat_most = [];
        foreach ($first_texts as $text){
            if(!empty($repeat_most[$text])){
                $repeat_most[$text] +=1;
            }else{
                $repeat_most[$text] = 1;
            }
        }
        arsort($repeat_most);

        $key = array_key_first($repeat_most);
        if(!empty($key)){
            $repeat_times = (int) $repeat_most[$key];
            if($repeat_times > ($tot_pages / 2)){
                // pelo menos em 50% das páginas
                return trim($key); // título
            }
        }
        return '';
    }
}
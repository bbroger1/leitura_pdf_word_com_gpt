<?php

namespace App;

use Exception;

class CaptionsHandle {

    private Database $Database;
    private $orto_lex;


    public function __construct() {
        $this->Database = new Database('legendas');
    }


    /**
     * Esse método vai buscar por erros conhecidos nas transcrições, fazer as devidas correções
     * e retornar o texto corrigido ou o mesmo texto caso não tenha encontrado erros.
     * @param string $original_text o texto que será corrigido
     * @return string o texto corrigido
     **/
    public function ortoCaption(string $original_text): string {
        $text = " $original_text ";
        $corrections = $this->getOrtoLex();
        if ($corrections) {
            foreach ($corrections as $item) {
                $correct_word = $item->correct_word;
                $wrong_word = $item->wrong_word;
                if(trim($wrong_word) == 0){
                    echo "Wrong word não pode ser espaço ou vázio - correct $correct_word<br>";
                    continue;
                }
                // echo "[$correct_word]<br>";
                /// extremamente importante por espaço
                $pre = $text;
                $wrong_word = preg_quote($wrong_word);
                $text = preg_replace("/\b$wrong_word\b/iu",  "$correct_word", $text);
                if (preg_last_error_msg() != 'No error') {
                    echo "Erro na regex<br>";
                    // isso não deve ser necessário, mesmo assim...
                    $text = $pre;
                }
            }
            $text = str_replace(" conscienciologia ", " Conscienciologia ", $text);
            $text = str_replace(["[Música]","[música]","[Music]","[music]"], " ", $text);
            return trim($text);
        }else{
            exit("O método ortoCaption() precisa do arquivo de dados da table corrections para funcionar");
        }

    }



    /**
     * Obtém lista de palavras conhecidas por ser transcritas de forma errada e a alternativa correta
     * @return array|false Retorna array com a lista das palavras ou false caso não encontre na tabela corrections
    **/
    private function getOrtoLex():array|false{
        if(!empty($this->orto_lex)){
            return $this->orto_lex;
        }
        $sql = "SELECT wrong_word, correct_word FROM corrections";
        $data = $this->Database->select($sql, [])->fetchAll();
        if($data){
            $this->orto_lex = $data;
            return $this->orto_lex;
        }
        return false;

    }


}







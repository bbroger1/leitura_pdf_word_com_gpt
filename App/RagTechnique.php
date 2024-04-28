<?php
namespace App;
use App\Google;

class RagTechnique{

    /**
     * Verifica se necessita pesquisa extra e a faz se necessário
     * @param string $text texto retornando pelo LLM
     * @param VectorSearch $VectorSearch classe de busca em vectores
     * @return string|false Retorna false caso não precise de pesquisas extras ou o conteúdo da pesquisa se for necessário
     *
     * @throws \Exception
     */
    public function extraSearch(string $text, VectorSearch $VectorSearch, string $prompt, int $max_searchs = 3, $search_at ='db'){
        $search_at = strtolower($search_at);
        if($search_at =='google'){
            $Google = new Google();
        }
        $prompt = strtolower(trim($prompt));
        $clen_text = str_replace(["```","```",'json'],"", $text);
        $clen_text = trim($clen_text);
        $obj = json_decode($clen_text);
        $mais_contexto = "";
        $total_searches = 0;
        if(is_object($obj)){
            $arr = $obj->search_terms ?? [];
            foreach ($arr as $term){
                $term_to_compare  = strtolower(trim(trim($term,"?")));
                $prompt_to_compare = trim(trim($prompt,"?"));
                if($term_to_compare == $prompt_to_compare){
                    // termo já foi pesquisado
                    continue;
                }
                echo "Pesquisando por: $term<br>";
                if($search_at =='google'){
                    echo "Funcionalidade apenas para teste, não usar em produção. \n";
                    $items = $Google->search($term, 1)->getItems();
                    if (!empty($items['0'])){
                        $link = $items['0']->link ?? '';
                        $result = $this->getLinkContent($link);
                        $mais_contexto .= "<article>$result</article>";
                        break; // se for busca com google será apenas uma
                    }
                }else{
                    $result = $VectorSearch->doSearch($term, 3);
                }
                $total_searches++;
                //echo "$result <hr>";
                $mais_contexto .= "<article>$term : $result</article>\n";
                if($total_searches >= $max_searchs){
                    break;
                }
            }
            return $mais_contexto;
        }else{
            echo "Não precisa mais pesquisa";
            return false;
        }
    }

    private function getLinkContent(string $link){
        echo $link." ";
        $content = $this->httpRequest($link);
        $content = preg_replace("/<(style|script).*?(style|script)>/s","", $content);
        $content = str_replace(">","> ", $content);
        $content = strip_tags($content);
        return $content;
    }

    public function isHTML(string $link){

        if(!filter_var($link, FILTER_VALIDATE_URL)){
            exit('Not URL');
        }
        $headers = get_headers($link, 1); // Obtém os cabeçalhos HTTP do arquivo
        if ($headers && (isset($headers['Content-Type']) OR isset($headers['content-type']))) {
            $contentType = $headers['Content-Type'] ?? $headers['content-type'];
            if (preg_match( '/text\/html/',$contentType)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }



    private function httpRequest(string $link){
        $html = file_get_contents($link);
        return $html;
    }
}
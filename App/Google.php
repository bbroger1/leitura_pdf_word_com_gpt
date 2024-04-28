<?php
namespace App;
use Exception;
class Google {
    private object $search_results;

    /**
     * @throws Exception
     */
    public function search(string $term, int $max_results, int $start = 0, $lang = 'lang_pt', $country = 'countryBR', $geo_lang = 'pt-BR', $hl = 'pt-BR'):Google {
       exit('Google->search está desativado');
        if ($max_results > 10) {
            throw new Exception('O número máximo é de resultados por página é 10.');
        }
        if ($start > 91) {
            throw new Exception('Não é possível listar mais de 100 resultados, start= 91 é o máximo possível');
        }
        $term = urlencode($term);
        $url = "https://www.googleapis.com/customsearch/v1?key=" . GOOGLE_SEARCH_API_KEY . "&cx=".GOOGLE_SEARCH_CX."&q=$term&lr=$lang&cr=$country&gl=$geo_lang&hl=$hl&num=$max_results&start=$start";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            throw new Exception("cURL Error #: " . $err);
        }
        $response = json_decode($response);
        $response_error = $response->error ?? false;
        if($response_error){
           if(!PRODUCTION){
               echo "<pre>";
               print_r($response_error);
               echo "</pre>";
               throw new Exception($response->error->message ?? 'Houve um erro!');
           }
        }
        $this->search_results = $response ?? new \stdClass();
        return $this;
    }

    public function getItems() {
        return $this->search_results->items ?? [];
    }

}
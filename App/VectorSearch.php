<?php
namespace App;
class VectorSearch{
    public function doSearch(string $term, int $numResults, int $sentencesBefore = 2, int $sentencesAfter = 2,  bool $do_rr = false, $lambda = 0.025){
        //return 'eu sou Vectara';
        $rerank = '';
        if($do_rr){
            $rerank = '"rerankingConfig": {"rerankerId": 272725718, "mmrConfig": {"diversityBias": 0.5} },';
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.vectara.io/v1/query',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
  "query": [
    {
      "query": '.json_encode($term).',
      "start": 0,
      "numResults": '.$numResults.',
      "contextConfig": {
        "charsBefore": 0,
        "charsAfter": 0,
        "sentencesBefore": '.$sentencesBefore.',
        "sentencesAfter": '.$sentencesAfter.',
        "startTag": " ",
        "endTag": " "
      },
      '.$rerank.'
      "corpusKey": [
        {
          "customerId": '.VECTARA_CUSTOMER_ID.',
          "corpusId": '.VECTARA_CORPUS_ID.',
          "semantics": "DEFAULT",
          "lexicalInterpolationConfig": {
            "lambda": '.$lambda.'
          }
        }
      ]
    }
  ]
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'x-api-key: '.VECTARA_API_KEY,
                'customer-id: '.VECTARA_CUSTOMER_ID,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $results = json_decode($response, false);
        $document = $results->responseSet[0]->document ?? [];
        $data = $results->responseSet[0]->response ?? [];
        $status_msg = $results->message ?? $results->responseSet[0]->status[0]->statusDetail ?? '';
        $context = '';
        foreach ($data as $item) {
            $text = $item->text;
            $text = preg_replace("/\n+|\r+/", " ", $text);
            $doc_idx = $item->documentIndex;
            $file_name = $document[$doc_idx]->id;
            $file_name = str_replace(".txt", ".pdf", $file_name);
            $context .= "<p>$text</p>\n";

        }
        if(empty(trim($context))){
            if(!PRODUCTION && DO_DEBUG){
                print_r($response);
                echo "<hr>";
                if($err){
                    echo "CURL error: $err";
                }
            }
        }
        if(empty($context) && !empty($status_msg)){
            exit("<b>**Vectara:**</b>". $status_msg);
        }
        return $context;
    }
}
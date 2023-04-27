<?php

/****************************************************************************************/
/******************************INICIO "FUNCIONES" DEL PROGRAMA***************************/
/****************************************************************************************/

//devuelve el elemento con el mayor valor
function cmp($a, $b)
{
    return ($a->score_document) < ($b->score_document);
}

//genera la expansion de terminos a partir de los terminos de entrada y un numero limite asignado
function get_array_expansion_terms($searchTerms, $numberExpansion)
{
    $arr_terms = array();
    $endPoint = "https://api.datamuse.com/words";
    $params = [
        "action" => "query",
        "ml" => $searchTerms,
        "format" => "json"
    ];

    $url = $endPoint . "?" . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($output, true);

    //validando que no exista error en la respuesta del servidor
    $service = true;
    foreach($result as $key => $value){
        if($value == 404){
            $service = false;
        }
    }
    
    if($service){
        if (count($result) > 5) {
            $limiteResultados = 1;
            foreach ($result as $element) {
                $newWords = new Words($element['word'], $element['score']);
                array_push($arr_terms, $newWords);
                $limiteResultados++;
                if ($limiteResultados > 5) {
                    return $arr_terms;
                }
            }
        } else {
            foreach ($result as $element) {
                $newWords = new Words($element['word'], $element['score']);
                array_push($arr_terms, $newWords);
            }
        }
    }

    return $arr_terms;
}

//busca y devuelve el array de terminos a partir del buscador journals plos usando su API
function get_array_search_plos($searchTerms, $relatedLeved)
{
    $arr_terms = array();
    $browser = "journals plos";
    $endPoint = "https://api.plos.org/search";
    $params = [
        "action" => "query",
        "q" => "everything:" . $searchTerms,
        "format" => "json"
    ];
    $url = $endPoint . "?" . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($output, true);

    //validando que no exista error en la respuesta del servidor
    $service = true;
    foreach($result as $key => $value){
        foreach($value as $sub_value){
            if($sub_value == 400){
                $service = false;
            }
        }
    }
    
    if($service == true){
        if (count($result['response']['docs']) > 5) {
            $limiteResultados = 1;
            foreach ($result['response']['docs'] as $element) {
                $newTerm = new Terms($element['title_display'], $element['score'], $relatedLeved, $browser, "https://journals.plos.org/plosone/article?id=" . $element['id']);
                array_push($arr_terms, $newTerm);
                $limiteResultados++;
                if ($limiteResultados > 5) {
                    return $arr_terms;
                }
            }
        } else {
            foreach ($result['response']['docs'] as $element) {
                $newTerm = new Terms($element['title_display'], $element['score'], $relatedLeved,  $browser, "https://journals.plos.org/plosone/article?id=" . $element['id']);
                array_push($arr_terms, $newTerm);
                //https://journals.plos.org/plosone/article?id=10.1371/journal.pone.0035159
            }
        }
    }

    
    return $arr_terms;
}

//busca y devuelve el array de terminos a partir del buscador europeana usando su API
function get_array_search_europeana($searchTerms, $relatedLeved)
{

    $arr_terms = array();
    $browser = "europeana";
    $endPoint = "https://api.europeana.eu/record/v2/search.json";
    $params = [
        "action" => "query",
        "wskey" => "isconlaphyse",
        "query" => $searchTerms,
        "format" => "json"
    ];
    $url = $endPoint . "?" . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($output, true);
    //echo $result;

    //validando que no exista error en la respuesta del servidor
    if ($result['success'] == true) {
        if (count($result['items']) > 5) {
            $limiteResultados = 1;
            foreach ($result['items'] as $element) {
                $newTerm = new Terms($element['title'][0], $element['score'], $relatedLeved, $browser, $element['guid']);
                array_push($arr_terms, $newTerm);
                $limiteResultados++;
                if ($limiteResultados > 5) {
                    return $arr_terms;
                }
            }
        } else {
            foreach ($result['items'] as $element) {
                foreach ($result['items'] as $element) {
                    $newTerm = new Terms($element['title'][0], $element['score'], $relatedLeved, $browser, $element['guid']);
                    array_push($arr_terms, $newTerm);
                }
            }
        }
        return $arr_terms;
    } else {
        return array();
    }
}

//genera la expansion de terminos usando las funciones get_array_search_plos() y get_array_search_europeana()
//y devuelve un array con la expansion de los terminos usando la logica de "class Terms"
function expandTemns($arrayTerms, $allArrayExapnsionTerms, $numberTemrs)
{
    //$level = 4;
    foreach ($arrayTerms as $terms) {
        $arr_expansion_PLOS = get_array_search_plos($terms->words, $numberTemrs);
        $arr_expansion_EUROPEANA = get_array_search_europeana($terms->words, $numberTemrs);

        if ($arr_expansion_PLOS != null && $arr_expansion_EUROPEANA != null) {
            $allArrayExapnsionTerms_temporal = array_merge($arr_expansion_PLOS, $arr_expansion_EUROPEANA);
            usort($allArrayExapnsionTerms_temporal, "cmp");
            $allArrayExapnsionTerms = array_merge($allArrayExapnsionTerms, $allArrayExapnsionTerms_temporal);
        } else if ($arr_expansion_PLOS != null) {
            usort($arr_expansion_PLOS, "cmp");
            $allArrayExapnsionTerms = array_merge($allArrayExapnsionTerms, $arr_expansion_PLOS);
        } else if ($arr_expansion_EUROPEANA != null) {
            usort($arr_expansion_EUROPEANA, "cmp");
            $allArrayExapnsionTerms = array_merge($allArrayExapnsionTerms, $arr_expansion_EUROPEANA);
        }
        $numberTemrs = $numberTemrs - 1;
    }

    return $allArrayExapnsionTerms;
}

/****************************************************************************************/
/******************************FIN "FUNCIONES" DEL PROGRAMA******************************/
/****************************************************************************************/


/******************************"CLASES" DEL PROGRAMA******************************/
class Terms
{
    public $title;
    public $score_document;
    public $score_level;
    public $browser;
    public $link;

    public function __construct($title, $score_document, $score_level, $browser, $link)
    {
        $this->title = $title;
        $this->score_document = $score_document;
        $this->score_level = $score_level;
        $this->browser = $browser;
        $this->link = $link;
    }
}

class Words
{
    public $words;
    public $score;

    public function __construct($words, $score)
    {
        $this->words = $words;
        $this->score = $score;
    }
}
/******************************"FIN" CLASES DEL PROGRAMA******************************/


/*******************************RECURSOS DE APIS USADAS*******************************/
//SCORE APLI PLOS
//https://api.plos.org/search?q=everything:Systematic Differences in Signal Emitting and Receiving Revealed by PageRank Analysis of a Human Protein Interactome 
//score:36
//https://api.plos.org/search?q=everything:protein
//score:1.34
//SCORE EUROPEAN 
//https://api.europeana.eu/record/v2/search.json?wskey=isconlaphyse&query=protein
//score:18.61
//https://api.europeana.eu/record/v2/search.json?wskey=isconlaphyse&query=Osvald%20Helmuth%20dressed%20as%20Mona%20Lisa
//score	84.54504

<?php

/***************************************************************************************/
/*****************************"INICIO" LOGICA DEL PROGRAMA******************************/
/***************************************************************************************/
include 'funciones/funciones_buscadores.php';

if (isset($_POST['btnSearch'])) {
    $searchTerms = "";
    $searchTerms = $_POST["mySearch"];

    $array_original_terms_plos = get_array_search_plos($searchTerms, 5);
    $array_original_terms_europeana = get_array_search_europeana($searchTerms, 5);
    $allArrayExapnsionTerms = array();

    if ($array_original_terms_plos != null && $array_original_terms_europeana != null) {
        $allArrayExapnsionTerms = array_merge($array_original_terms_europeana, $array_original_terms_plos);
    } else if ($array_original_terms_plos != null) {
        $allArrayExapnsionTerms = $array_original_terms_plos;
    } else if ($array_original_terms_europeana != null) {
        $allArrayExapnsionTerms = $array_original_terms_europeana;
    }

    usort($allArrayExapnsionTerms, "cmp");

    $terms_expansion_array = array();
    $terms_expansion_array = get_array_expansion_terms($searchTerms, 4);
    if($terms_expansion_array!=null){
        if(count($terms_expansion_array)>0){
            $allArrayExapnsionTerms = expandTemns($terms_expansion_array, $allArrayExapnsionTerms, 4);
        }
    }
}else{
    echo "<h2>Error al buscar!<h2>";
}

include 'vista/vista.php';


?>

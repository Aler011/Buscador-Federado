<?php

echo "<br>";
echo "<br>";
echo '<div style="margin:auto;max-width:1000px">';

echo "<table >";
echo '<tr>';
echo '<th style="border: .1rem solid black">' . "Título" . '</th>';
echo '<th style="border: .1rem solid black">' . "Val. Original Relev." . '</th>';
echo '<th style="border: .1rem solid black">' . "Val. Normalizado Relev." . '</th>';
echo '<th style="border: .1rem solid black">' . "Buscador" . '</th>';
echo '<th style="border: .1rem solid black">' . "Enlace" . '</th>';
echo '</tr>';

foreach ($allArrayExapnsionTerms as $terms) {
    echo "<tr>";
    foreach ($terms as $key => $value) {

        if ($key == "link") {
            echo "<td> <a href=$value>" . $value . "</a></td>";
        } else {
            echo "<td>" . $value . "</td>";
        }
    }
    echo "</tr>";
}
echo "</table>";
echo "</div>";

/****************************************************************************************/
/******************************"FIN" LOGICA DEL PROGRAMA*********************************/
/****************************************************************************************/
?>
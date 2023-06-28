<?php
###############################################
# &UUML;berpr&uuml;fen, ob eine Auswahl getroffen wurde #
###############################################

if(isset($_POST['aktion']) AND !isset($_POST['auswahl']) AND !isset($_POST['auswahl_string']))
{
    echo("<div class=\"Information_Warnung\" style=\"text-align:center;\">\n");
    echo("<b>Fehler bei der Eingabe!</b><br>");
    echo("Es wurde kein Bewerber ausgew&auml;hlt<br><br>");
    echo("<form action=\"index.php?seite=intern_admin&intern_a=bewerber_einsehen\" method=\"post\">\n");
    //Funktion packt alle &uuml;bergebenen Parameter aus $_POST in versteckte Formularfelder
    //Beim Dr&uuml;cken des Zur&uuml;ck-Buttons gelangt man so zur &uuml;rspr&uuml;nglich eingestellten Seite zur&uuml;ck
    post_back($_POST);
    echo("<input class=\"Buttons_Unten\" type=\"submit\" value=\">> Zur&uuml;ck\">\n");
    echo("</form>\n");
    echo("</div>\n");
    echo("<div class=\"Abstandhalter_Div\"></div>\n");
}

###########################
# Oft ben&ouml;tigte Parameter #
###########################

//Die Variable "$auswahl_string" enth&auml;lt alle &uuml;bergebenen Pkys, getrennt durch Kommas
//F&uuml;r die Weitergabe per $_POST und f&uuml;r die SELECT Abfragen
if(isset($_POST['auswahl']))
{
    $auswahl_string = implode(", ", $_POST['auswahl']);
}
elseif(isset($_POST['auswahl_string']))
{
    $auswahl_string = $_POST['auswahl_string'];
}
//Das Array "$auswahl_array" enth&auml;lt alle Pkys
//F&uuml;r Schleifen
if(isset($auswahl_string))
{
    $auswahl_array = explode(", ", $auswahl_string);
}

#######################################
# Includen der entsprechenden Skripte #
#######################################
if(isset($_POST['aktion']) AND (isset($_POST['auswahl']) OR isset($_POST['auswahl_string'])))
{
    //Abh&auml;ngig von der gew&auml;hlten Aktion die entsprechenden Skripte includen
    switch($_POST['aktion'])
    {
        case "details":
        include("bewerber_details.php");
        break;

        case "auswahlgespraech_planen":
        include("bewerber_auswahlgespraech_planen.php");
        break;

        case "auswahlgespraech":
        include("bewerber_auswahlgespraech.php");
        break;
    }
}
?>
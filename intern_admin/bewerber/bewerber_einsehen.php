<div class="h1">Bewerber verwalten</div>

<?php
##################
# POST VARIABLEN #
##################

if(isset($_POST['status_bewerber'])) {$post_status_bewerber = $_POST['status_bewerber'];} else {$post_status_bewerber = 0;}
if(isset($_POST['jahr_bewerbung']))  {$post_jahr_bewerbung = $_POST['jahr_bewerbung'];} else {$post_jahr_bewerbung = 0;}
#if(isset($_POST['jahr_bewerbung']))  {$post_jahr_bewerbung = $_POST['jahr_bewerbung'];} else {$post_jahr_bewerbung = date("Y");}
if(isset($_POST['subseite']))        {$post_subseite = $_POST['subseite'];} else {$post_subseite = 1;}

#############################
# WHERE BEDINGUNG FESTLEGEN #
#############################

$where_array = array();

//Status Bewerber
switch($post_status_bewerber)
{
    //Endsumme erreicht
    case 1:
    $where_array[] = "lb.Endsumme IS NOT NULL AND lb.Endsumme >= ".GRENZE_ENDSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
    break;
    //Endsumme nicht erreicht
    case 2:
    $where_array[] = "lb.Endsumme IS NOT NULL AND lb.Endsumme < ".GRENZE_ENDSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
    break;
    //Endsumme nicht vorhanden
    case 3:
    $where_array[] = "lb.Endsumme IS NULL AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
    break;
    //Zwischensumme erreicht
    case 4:
    $where_array[] = "lb.Zwischensumme IS NOT NULL AND lb.Zwischensumme >= ".GRENZE_ZWISCHENSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
    break;
    //Zwischensumme nicht erreicht
    case 5:
    $where_array[] = "lb.Zwischensumme IS NOT NULL AND lb.Zwischensumme < ".GRENZE_ZWISCHENSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
    break;
    //Zwischensumme nicht berechenbar
    case 6:
    $where_array[] = "lb.Zwischensumme IS NULL AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
    break;
    //Account wurde nicht aktiviert
    case 7:
    $where_array[] = "b.Key_Aktivierung IS NOT NULL AND b.Account_gesperrt IS NULL";
    break;
    //Bewerbung wurde zur&uuml;ckgezogen
    case 8:
    $where_array[] = "b.Bewerbung_zurueckgezogen = 1 AND b.Account_gesperrt IS NULL";
    break;
    //Account wurde gesperrt
    case 9:
    $where_array[] = "b.Account_gesperrt = 1";
    break;
}

//Auswahl des Studienjahrs
if($post_jahr_bewerbung != 0)
{
    $where_array[] = "((b.Datum_Bewerbung BETWEEN '".$post_jahr_bewerbung."-01-01' AND '".($post_jahr_bewerbung+1)."-01-01') OR (b.Datum_Wiederbewerbung BETWEEN '".$post_jahr_bewerbung."-01-01' AND '".($post_jahr_bewerbung+1)."-01-01'))";
}

//Den WHERE-String zusammensetzten
if(!empty($where_array))
{
    $where_string = "WHERE ";
    $where_string .= implode(" AND ", $where_array);
}
else
{
    $where_string = "";
}

#################################
### ANZAHL DER SUCHERGEBNISSE ###
#################################

//Anzahl der Datens&auml;tze auslesen
$sql = "SELECT
            COUNT(*) as Anzahl
        FROM
            bewerber b
        INNER JOIN
            leistungen_bewerber lb
        ON
            b.pky_Bewerber = lb.fky_Bewerber
        ".$where_string.";";
$result = mysqli_query($link, $sql) OR die(mysqli_error($link));
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$anzahl_datensatz = $row['Anzahl'];

###########################################################################################
### AUSWAHL VON STATUS UND JAHR DER BEWERBUNG UND AUSGABE DER ANZAHL DER SUCHERGEBNISSE ###
###########################################################################################

//Formular und Tabelle Start
echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
echo("<table cellpadding=\"0\" cellspacing=\"2\" class=\"Tabelle_Statistik\" border=\"0\">\n");
echo("<tr>\n");

//Auswahl des Bewerberstatus und des Jahres der Bewerbung
echo("<td>\n");
//Select f&uuml;r die Auswahl des Bewerberstatus
echo("<select class=\"Auswahlfeld\" name=\"status_bewerber\" size=\"1\">\n");
//Alle Bewerber
if(!isset($_POST['status_bewerber']) OR (isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == "0"))
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"0\">Alle Bewerber</option>\n");
//Endsumme erreicht
if(isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == 1)
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"1\">Endsumme erreicht</option>\n");
//Endsumme nicht erreicht
if(isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == 2)
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"2\">Endsumme nicht erreicht</option>\n");
//Endsumme nicht vorhanden
if(isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == 3)
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"3\">Endsumme nicht vorhanden </option>\n");
//Zwischensumme erreicht
if(isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == 4)
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"4\">Zwischensumme erreicht</option>\n");
//Zwischensumme nicht erreicht
if(isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == 5)
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"5\">Zwischensumme nicht erreicht</option>\n");
//Zwischensumme nicht berechenbar
if(isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == 6)
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"6\">Zwischensumme nicht berechenbar</option>\n");
//Account wurde nicht aktiviert
if(isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == 7)
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"7\">Account wurde nicht aktiviert</option>\n");
//Bewerbung wurde zur&uuml;ckgezogen
if(isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == 8)
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"8\">Bewerbung wurde zur&uuml;ckgezogen</option>\n");
//Account wurde gesperrt
if(isset($_POST['status_bewerber']) AND $_POST['status_bewerber'] == 9)
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"9\">Account wurde gesperrt</option>\n");
echo("</select>\n");
echo(" von \n");
//Select f&uuml;r die Auswahl des Studienjahrs
echo("<select class=\"Auswahlfeld\" name=\"jahr_bewerbung\" size=\"1\">\n");
//Alle Jahre
if(!isset($_POST['jahr_bewerbung']))
{$select = " selected=\"selected\"";}else{$select = "";}
echo("<option".$select." value=\"0\">Alle Jahre</option>\n");
for($i=ERSTES_STUDIENJAHR; $i<=date("Y"); $i++)
{
    //if((!isset($_POST['jahr_bewerbung']) AND date("Y") == $i) OR (isset($_POST['jahr_bewerbung']) AND $_POST['jahr_bewerbung'] == $i))
    if(isset($_POST['jahr_bewerbung']) AND $_POST['jahr_bewerbung'] == $i)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"".$i."\">".$i."</option>\n");
}
echo("</select> \n");
//$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
echo("<input type=\"hidden\" name=\"subseite\" value=\"".$post_subseite."\">\n");
//Submit-Button
echo("<input class=\"Buttons_Unten\" type=\"submit\" value=\">> Anzeigen\">\n");
echo("</td>\n");

//Ausgabe der Anzahl der Suchergebnisse
echo("<td style=\"text-align:right;\">\n");
echo("<b>Anzahl der ermittelten Bewerber: ".$anzahl_datensatz."</b>");
echo("</td>\n");

echo("</tr>\n");
echo("</table>\n");
echo("</form>\n");

#######################
### BL&AUML;TTERFUNKTION ###
#######################

//wenn kein Datensatz vorhanden ist
if($anzahl_datensatz == 0)
{$anzahl_datensatz = 1;}
//Anzahl der Seiten (aufgerundet)
$anzahl_subseiten = ceil($anzahl_datensatz / ANZAHL_DATENSATZ_PRO_SUBSEITE_UEBERSICHT);
//wenn keine Seitenzahl mit $_POST &uuml;bergeben wurde
if(!isset($_POST['subseite']))
{$subseite = 1;}
//wenn die &uuml;bergebene Subseite kleiner als 1 ist
elseif(isset($_POST['subseite']) AND $_POST['subseite'] < 1)
{$subseite = 1;}
//wenn die &uuml;bergebene Subseite gr&ouml;&szlig;er als die maximale Seitenanzahl ist
elseif(isset($_POST['subseite']) AND $_POST['subseite'] > $anzahl_subseiten)
{$subseite = $anzahl_subseiten;}
//wenn direkt zur angegebenen Seite gesprungen werden soll
else
{$subseite = $_POST['subseite'];}

//LIMIT Bedingung
$limit = "LIMIT ".($subseite - 1) * ANZAHL_DATENSATZ_PRO_SUBSEITE_UEBERSICHT.", ".ANZAHL_DATENSATZ_PRO_SUBSEITE_UEBERSICHT."";

//wenn die Anzahl der Unterseiten nur 1 betr&auml;gt, wird die Bl&auml;tterfunktion nicht angezeigt
if($anzahl_subseiten > 1)
{
    //Tabelle Start
    echo("<table cellpadding=\"0\" cellspacing=\"2\" class=\"Tabelle_Blaettern\" border=\"0\" style=\"margin:0 0 10px 0;\">\n");
    echo("<tr>\n");

    //Button f&uuml;r erste Seite
    echo("<td style=\"width:40px;\">");
    echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
    //$_POST Variablen f&uuml;r die Anzeige von Status und Jahr versteckt &uuml;bergeben
    echo("<input type=\"hidden\" name=\"status_bewerber\" value=\"".$post_status_bewerber."\">\n");
    echo("<input type=\"hidden\" name=\"jahr_bewerbung\" value=\"".$post_jahr_bewerbung."\">\n");
    //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
    echo("<input type=\"hidden\" name=\"subseite\" value=\"1\">\n");
    echo("<input type=\"submit\" name=\"erste_subseite\" value=\"<<\" style=\"width:40px;\">\n");
    echo("</form>\n");
    echo("</td>\n");

    //Button f&uuml;r Seite zur&uuml;ck
    echo("<td style=\"width:40px;\">");
    echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
    //$_POST Variablen f&uuml;r die Anzeige von Status und Jahr versteckt &uuml;bergeben
    echo("<input type=\"hidden\" name=\"status_bewerber\" value=\"".$post_status_bewerber."\">\n");
    echo("<input type=\"hidden\" name=\"jahr_bewerbung\" value=\"".$post_jahr_bewerbung."\">\n");
    //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
    echo("<input type=\"hidden\" name=\"subseite\" value=\"".($subseite-1)."\">\n");
    echo("<input type=\"submit\" name=\"subseite_zurueck\" value=\"<\" style=\"width:40px;\">\n");
    echo("</form>\n");
    echo("</td>\n");

    //Button f&uuml;r gezielten Sprung zu einer Seite und Formular f&uuml;r die Angabe der Seitenzahl und Angabe der Seitenzahl insgesamt
    echo("<td align=\"center\">");
    echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
    //$_POST Variablen f&uuml;r die Anzeige von Status und Jahr versteckt &uuml;bergeben
    echo("<input type=\"hidden\" name=\"status_bewerber\" value=\"".$post_status_bewerber."\">\n");
    echo("<input type=\"hidden\" name=\"jahr_bewerbung\" value=\"".$post_jahr_bewerbung."\">\n");
    //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
    echo("<input type=\"submit\" name=\"seite_anzeigen\" value=\"Seite\">\n");
    echo("<input type=\"text\" name=\"subseite\" value=\"".$subseite."\" size=\"3\" maxlength=\"3\">\n");
    echo(" von ".$anzahl_subseiten."\n");
    echo("</form>\n");
    echo("</td>\n");

    //Button f&uuml;r Seite vor
    echo("<td style=\"width:40px;\">");
    echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
    //$_POST Variablen f&uuml;r die Anzeige von Status und Jahr versteckt &uuml;bergeben
    echo("<input type=\"hidden\" name=\"status_bewerber\" value=\"".$post_status_bewerber."\">\n");
    echo("<input type=\"hidden\" name=\"jahr_bewerbung\" value=\"".$post_jahr_bewerbung."\">\n");
    //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
    echo("<input type=\"hidden\" name=\"subseite\" value=\"".($subseite+1)."\">\n");
    echo("<input type=\"submit\" name=\"subseite_vor\" value=\">\" style=\"width:40px;\">\n");
    echo("</form>\n");
    echo("</td>\n");

    //Button f&uuml;r letzte Seite
    echo("<td style=\"width:40px;\">");
    echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
    //$_POST Variablen f&uuml;r die Anzeige von Status und Jahr versteckt &uuml;bergeben
    echo("<input type=\"hidden\" name=\"status_bewerber\" value=\"".$post_status_bewerber."\">\n");
    echo("<input type=\"hidden\" name=\"jahr_bewerbung\" value=\"".$post_jahr_bewerbung."\">\n");
    //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
    echo("<input type=\"hidden\" name=\"subseite\" value=\"".$anzahl_subseiten."\">\n");
    echo("<input type=\"submit\" name=\"letzte_subseite\" value=\">>\" style=\"width:40px;\">\n");
    echo("</form>\n");
    echo("</td>\n");

    echo("</tr>\n");
    echo("</table>\n");
}

####################################
### DATEN DES BEWERBERS AUSLESEN ###
####################################

//pers&ouml;nliche Daten des Bewerbers aus der Tabelle "bewerber" auslesen
$sql = "SELECT
            b.pky_Bewerber,
            b.Anrede,
            b.Nachname,
            b.Vorname,
            b.Geburtsdatum,
            b.Datum_Bewerbung,
            b.Datum_Wiederbewerbung,
            b.Key_Aktivierung,
            b.Account_gesperrt,
            b.Bewerbung_zurueckgezogen,
            lb.Zwischensumme,
            lb.Auswahlgespraech_Summe,
            lb.Endsumme
        FROM
            bewerber b
        INNER JOIN
            leistungen_bewerber lb
        ON
            b.pky_Bewerber = lb.fky_Bewerber
        ".$where_string."
        ORDER BY
            lb.Endsumme DESC, b.Account_gesperrt ASC, b.Bewerbung_zurueckgezogen ASC, b.Key_Aktivierung ASC, lb.Zwischensumme DESC, b.Nachname ASC
        ".$limit.";";
$ergebnis = mysqli_query($link, $sql) OR die(mysqli_error($link));

##################################
# &UUML;BERSICHTSANZEIGE DER BEWERBER #
##################################

echo("<form action=\"index.php?seite=intern_admin&intern_a=bewerber_aktionen\" method=\"post\">\n");
echo("<table id=\"checkbox\" class=\"Tabelle_Uebersicht\">\n");

echo("<tr>\n");
//leere Reihe wegen Checkboxen
echo("<td style=\"width:2%;\"></td>\n");
//Name
echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"\">");
echo("Name");
echo("</td>\n");
//Geburtsdatum
echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:12%;\">");
echo("Geburtsdatum");
echo("</td>\n");
//Status
echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:26%;\">");
echo("Status");
echo("</td>\n");
//Zwischensumme
echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:13%;\">");
echo("Zwischensumme");
echo("</td>\n");
//Punkte im Auswahlgespr&auml;ch
echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:14%;\">");
echo("Auswahlgespr&auml;ch");
echo("</td>\n");
//Endsumme
echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:9%;\">");
echo("Endsumme");
echo("</td>\n");
echo("</tr>\n");

while($datensatz = mysqli_fetch_assoc($ergebnis))
{
    //wenn ein Eintrag vorhanden ist, wird die Variable "$eintrag_vorhanden" auf 1 gesetzt
    $eintrag_vorhanden = 1;

    //Zeilenformatierung und Statustext ermitteln
    $format = bewerber_status_zeile($datensatz['Zwischensumme'], $datensatz['Endsumme'], $datensatz['Key_Aktivierung'], $datensatz['Account_gesperrt'], $datensatz['Bewerbung_zurueckgezogen']);

    echo("<tr valign=\"top\" style=\"text-align:left; background-color:".$format['style_bg'].";color:".$format['style_color'].";\">\n");
    //Checkboxen
    echo("<td><input type=\"checkbox\" name=\"auswahl[]\" value=\"".$datensatz['pky_Bewerber']."\"></td>\n");
    //Name
    echo("<td style=\"border:1px solid ".$format['style_color'].";\"><b>".($datensatz['Anrede'] == "h"?"Herr":"Frau")." ".$datensatz['Vorname']." ".$datensatz['Nachname']."</b></td>\n");
    //Geburtsdatum
    echo("<td style=\"border:1px solid ".$format['style_color'].";\">".datum_dbdate_d($datensatz['Geburtsdatum'])."</td>\n");
    //Status
    echo("<td style=\"border:1px solid ".$format['style_color'].";\">".$format['status']."</td>\n");
    //Zwischensumme
    echo("<td style=\"border:1px solid ".$format['style_color'].";\">".$datensatz['Zwischensumme']."</td>\n");
    //Punkte im Auswahlgespr&auml;ch
    echo("<td style=\"border:1px solid ".$format['style_color'].";\">".($datensatz['Auswahlgespraech_Summe'] == NULL ? "" : float_e_d(clean_num($datensatz['Auswahlgespraech_Summe'], "en")))."</td>\n");
    //Endsumme
    echo("<td style=\"border:1px solid ".$format['style_color'].";\">".$datensatz['Endsumme']."</td>\n");
    echo("</tr>\n");
}
//wenn kein Eintrag f&uuml;r die Anfrage vorhanden ist
if(!isset($eintrag_vorhanden))
{
    echo("<tr>\n");
    echo("<td colspan=\"7\" class=\"Tabelle_Leer\">");
    echo("Keine Eintr&auml;ge vorhanden!");
    echo("</td>\n");
    echo("</tr>\n");
}

//Buttons zur Auswahl ALLER oder KEINER der angezeigten Bewerber
echo("<tr>\n");
echo("<td colspan=\"7\">\n");
echo("<img src=\"./bilder/Pfeil_re_2.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"><input class=\"Buttons_Klein\" type=\"button\" value=\"alle\" onClick=\"checkAll('checkbox')\">\n");
echo("<img src=\"./bilder/Pfeil_re_2.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"><input class=\"Buttons_Klein\" type=\"button\" value=\"keine\" onClick=\"uncheckAll('checkbox')\">\n");
echo("</td>\n");
echo("</tr>\n");

//Submit
echo("<tr>\n");
echo("<td colspan=\"7\" class=\"Tabelle_Uebersicht_Footer\">");
//Versteckte Felder, welche eine R&uuml;ckkehr zur aktuellen Seite erm&ouml;glichen
echo("<input type=\"hidden\" name=\"subseite\" value=\"".$subseite."\">\n");
echo("<input type=\"hidden\" name=\"status_bewerber\" value=\"".$post_status_bewerber."\">\n");
echo("<input type=\"hidden\" name=\"jahr_bewerbung\" value=\"".$post_jahr_bewerbung."\">\n");
//Drop-Down Men&uuml; f&uuml;r die Aktionen der Bewerber
echo("<select class=\"Auswahlfeld\" name=\"aktion\" size=\"1\">\n");
//Details der Bewerbung einsehen
echo("<option value=\"details\" selected=\"selected\">Details der Bewerbung einsehen</option>\n");
//Auswahlgespr&auml;ch planen
echo("<option value=\"auswahlgespraech_planen\">Auswahlgespr&auml;ch planen</option>\n");
//Auswahlgespr&auml;ch eintragen/&auml;ndern
echo("<option value=\"auswahlgespraech\">Auswahlgespr&auml;ch eintragen/&auml;ndern</option>\n");
echo("</select>\n");
//Submit
echo("<input class=\"Buttons_Unten\" type=\"submit\" value=\">> Aktion ausf&uuml;hren\">\n");
echo("</td>\n");
echo("</tr>\n");

echo("</table>\n");
echo("</form>\n");

mysqli_free_result($ergebnis);
?>
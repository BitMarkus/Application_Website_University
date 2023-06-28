<?php
####################################
### DATEN DES BEWERBERS AUSLESEN ###
####################################

//pers&ouml;nliche Daten des Bewerbers aus der Tabelle "bewerber" auslesen
$sql = "SELECT
            Anrede,
            Nachname,
            Vorname,
            Geburtsdatum,
            Email,
            Nationalitaet_fky_Land,
            Strasse,
            Hausnummer,
            Adresszusatz,
            Postleitzahl,
            Wohnort,
            fky_Land,
            Datum_Bewerbung,
            fky_HZB,
            HZB_Sonstige,
            HZB_Jahr,
            HZB_Ort,
            HZB_fky_Land,
            Soziales_Jahr,
            fky_Ausbildung,
            Begruendung,
            Datum_zurueckgezogen,
            Grund_zurueckgezogen
        FROM
            bewerber
        WHERE
            pky_Bewerber = '".$_SESSION['SESSION_PKY_BEWERBER']."';";
$result = mysqli_query($link, $sql) OR die(mysqli_error($link));
$daten_bewerber = mysqli_fetch_assoc($result);
mysqli_free_result($result);

//Leistungen des Bewerbers aus der Tabelle "leistungen_bewerber" auslesen
$sql = "SELECT
            Leistungen_Art,
            HZB_Note,
            HZB_Punkte,
            Naturw_belegt,
            fky_Naturw_Fach,
            Naturw_HJ_1_Note,
            Naturw_HJ_2_Note,
            Naturw_HJ_3_Note,
            Naturw_HJ_4_Note,
            Naturw_End_Note,
            Naturw_HJ_1_Punkte,
            Naturw_HJ_2_Punkte,
            Naturw_HJ_3_Punkte,
            Naturw_HJ_4_Punkte,
            Naturw_End_Punkte,
            Mathe_belegt,
            Mathe_HJ_1_Note,
            Mathe_HJ_2_Note,
            Mathe_HJ_3_Note,
            Mathe_HJ_4_Note,
            Mathe_End_Note,
            Mathe_HJ_1_Punkte,
            Mathe_HJ_2_Punkte,
            Mathe_HJ_3_Punkte,
            Mathe_HJ_4_Punkte,
            Mathe_End_Punkte
        FROM
            leistungen_bewerber
        WHERE
            fky_Bewerber = '".$_SESSION['SESSION_PKY_BEWERBER']."';";
$result = mysqli_query($link, $sql) OR die(mysqli_error($link));
$leistungen_bewerber = mysqli_fetch_assoc($result);
mysqli_free_result($result);

//Daten des Lebenslaufs des Bewerbers aus der Tabelle "lebenslauf_bewerber" auslesen
//und in ein Array Speichern
$sql = "SELECT
            Nr_Eintrag,
            Datum_am_von,
            Datum_bis,
            Eintrag
        FROM
            lebenslauf_bewerber
        WHERE
            fky_Bewerber = '".$_SESSION['SESSION_PKY_BEWERBER']."'
        ORDER BY Nr_Eintrag ASC;";
$result = mysqli_query($link, $sql) OR die(mysqli_error($link));
//Array aus dem Datensatz erzeugen
while($row = mysqli_fetch_assoc($result))
{
    $lebenslauf_bewerber[$row['Nr_Eintrag']]['datum_am_von'] = $row['Datum_am_von'];
    $lebenslauf_bewerber[$row['Nr_Eintrag']]['datum_bis'] = $row['Datum_bis'];
    $lebenslauf_bewerber[$row['Nr_Eintrag']]['eintrag'] = $row['Eintrag'];
}
mysqli_free_result($result);

###################
### &UUML;BERSCHRIFT ###
###################

echo("<div class=\"h1\">Bewerbung vom ".datum_dbdate_d($daten_bewerber['Datum_Bewerbung'])." einsehen</div> \n");

#########################
### AUSGABE DER DATEN ###
#########################
//Ausgabe erfolgt analog zur Zusammenfassung bei der Neuanmeldung

//Tabelle Start
echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" style=\"width:100%;\">\n");

///////////////////////////////////////////////////
/////////////// Status der Bewerbung //////////////
///////////////////////////////////////////////////

if($bewerbung_zurueckgezogen)
{
//&UUML;berschrift
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
echo("Status Ihrer Bewerbung");
echo("</td>\n");
echo("</tr>\n");

//Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
$zeile = 0;

//Name
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td style=\"width:21%;\" class=\"Zeile_Bezeichnung\">");
echo("Status:");
echo("</td>\n");
echo("<td style=\"color:red;\">");
echo("Sie haben Ihre Bewerbung am ".datum_dbdate_d($daten_bewerber['Datum_zurueckgezogen'])." zur&uuml;ckgezogen!");
echo("</td>\n");
echo("</tr>\n");

//Grund
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\" valign=\"top\">");
echo("Grund:");
echo("</td>\n");
echo("<td>");
if($daten_bewerber['Grund_zurueckgezogen'] == "")
{echo("keine Angabe");}
else
{echo("".nl2br($daten_bewerber['Grund_zurueckgezogen'])."");}
echo("</td>\n");
echo("</tr>\n");

//Leerzeile
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Leerzeile\">");
echo("</td>\n");
echo("</tr>\n");
}

///////////////////////////////////////////////////
/////////////// pers&ouml;nliche Angaben ///////////////
///////////////////////////////////////////////////

//&UUML;berschrift
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
echo("Pers&ouml;nliche Angaben");
echo("</td>\n");
echo("</tr>\n");

//Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
$zeile = 0;

//Name
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td style=\"width:21%;\" class=\"Zeile_Bezeichnung\">");
echo("Name:");
echo("</td>\n");
echo("<td>");
echo("".($daten_bewerber['Anrede'] == "h" ? "Herr" : "Frau")." ".$daten_bewerber['Vorname']." ".$daten_bewerber['Nachname']."");
echo("</td>\n");
echo("</tr>\n");

//Geburtsdatum
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Geburtsdatum:");
echo("</td>\n");
echo("<td>");
echo("".datum_dbdate_d($daten_bewerber['Geburtsdatum'])."");
echo("</td>\n");
echo("</tr>\n");

//Nationalit&auml;t
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Nationalit&auml;t:");
echo("</td>\n");
echo("<td>");
echo("".land_eintrag($link, $daten_bewerber['Nationalitaet_fky_Land'])."");
echo("</td>\n");
echo("</tr>\n");

//Strasse und Hausnummer
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Stra&szlig;e/Nummer:");
echo("</td>\n");
echo("<td>");
echo("".$daten_bewerber['Strasse']." ".$daten_bewerber['Hausnummer']."");
echo("</td>\n");
echo("</tr>\n");

//Adresszusatz
if($daten_bewerber['Adresszusatz'] != "")
{
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Adresszusatz:");
    echo("</td>\n");
    echo("<td>");
    echo("".$daten_bewerber['Adresszusatz']."");
    echo("</td>\n");
    echo("</tr>\n");
}

//PLZ und Wohnort
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("PLZ/Wohnort:");
echo("</td>\n");
echo("<td>");
echo("".$daten_bewerber['Postleitzahl']." ".$daten_bewerber['Wohnort']."");
echo("</td>\n");
echo("</tr>\n");

//Land
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Land:");
echo("</td>\n");
echo("<td>");
echo("".land_eintrag($link, $daten_bewerber['fky_Land'])."");
echo("</td>\n");
echo("</tr>\n");

//Email
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Email:");
echo("</td>\n");
echo("<td>");
echo("".$daten_bewerber['Email']."");
echo("</td>\n");
echo("</tr>\n");

////////////////////////////////////////////////////////////
/////////////// Hochschulzugangsberechtigung ///////////////
////////////////////////////////////////////////////////////

//Leerzeile
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Leerzeile\">");
echo("</td>\n");
echo("</tr>\n");

//&UUML;berschrift
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
echo("Schulabschluss/Hochschulzugangsberechtigung (HZB)");
echo("</td>\n");
echo("</tr>\n");

//Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
$zeile = 0;

//Art der HZB
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Art der HZB:");
echo("</td>\n");
echo("<td>");
echo("".hzb_art_eintrag($link, $daten_bewerber['fky_HZB'])."");
echo("</td>\n");
echo("</tr>\n");

//HZB sonstige
if($daten_bewerber['fky_HZB'] == PKY_SONST_HZB)
{
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Sonstige HZB:");
    echo("</td>\n");
    echo("<td>");
    echo("".$daten_bewerber['HZB_Sonstige']."");
    echo("</td>\n");
    echo("</tr>\n");
}

//HZB Jahr
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Jahr der HZB:");
echo("</td>\n");
echo("<td>");
echo("".$daten_bewerber['HZB_Jahr']."");
echo("</td>\n");
echo("</tr>\n");

//HZB Ort
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Ort der HZB:");
echo("</td>\n");
echo("<td>");
echo("".$daten_bewerber['HZB_Ort']."");
echo("</td>\n");
echo("</tr>\n");

//HZB Land
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Land der HZB:");
echo("</td>\n");
echo("<td>");
echo("".land_eintrag($link, $daten_bewerber['HZB_fky_Land'])."");
echo("</td>\n");
echo("</tr>\n");

//HZB Note
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Abschlussnote:");
echo("</td>\n");
echo("<td>");
echo("".float_e_d(clean_num($leistungen_bewerber['HZB_Note'], "en"))."");
echo("</td>\n");
echo("</tr>\n");

//////////////////////////////////////////
/////////////// Leistungen ///////////////
//////////////////////////////////////////

//Leerzeile
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Leerzeile\">");
echo("</td>\n");
echo("</tr>\n");

//&UUML;berschrift
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
echo("Leistungen");
echo("</td>\n");
echo("</tr>\n");

//Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
$zeile = 0;

//Art der Angabe
if($leistungen_bewerber['Leistungen_Art'] != "")
{
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Art der Angabe:");
    echo("</td>\n");
    echo("<td>");
    if($leistungen_bewerber['Leistungen_Art'] == "n")
    {echo("Schulnoten");}
    else
    {echo("Punkte");}
    echo("</td>\n");
    echo("</tr>\n");
}

///////////////////////////
//////   Mathemathik //////
///////////////////////////

$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
echo("Mathematik:");
echo("</td>\n");
echo("<td>\n");

//Wenn Mathemathik in den letzten vier Halbjahren belegt wurde
if($leistungen_bewerber['Mathe_belegt'] == 1)
{
    //eigene Tabelle einf&uuml;gen
    echo("<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" style=\"width:100%;\">\n");

    //alle Halbjahre
    for($l=1; $l<=4; $l++)
    {
        echo("<tr>\n");
        echo("<td style=\"width:15%;\">");
        echo("".$l.". Halbjahr:");
        echo("</td>\n");
        echo("<td>");
        if($leistungen_bewerber['Leistungen_Art'] == "n")
        {
            if(is_numeric($leistungen_bewerber['Mathe_HJ_'.$l.'_Note']))
            {echo("".float_e_d(clean_num($leistungen_bewerber['Mathe_HJ_'.$l.'_Note'], "en"))."");}
            else
            {echo("keine Angabe");}
        }
        else
        {
            if(is_numeric($leistungen_bewerber['Mathe_HJ_'.$l.'_Punkte']))
            {echo("".clean_num($leistungen_bewerber['Mathe_HJ_'.$l.'_Punkte'], "en")."");}
            else
            {echo("keine Angabe");}
        }
        echo("</td>\n");
        echo("</tr>\n");
    }

    //Abiturpr&uuml;fung
    echo("<tr>\n");
    echo("<td>");
    echo("Abiturpr&uuml;fung:");
    echo("</td>\n");
    echo("<td>");
    if($leistungen_bewerber['Leistungen_Art'] == "n")
    {
        if(is_numeric($leistungen_bewerber['Mathe_End_Note']))
        {echo("".float_e_d(clean_num($leistungen_bewerber['Mathe_End_Note'], "en"))."");}
        else
        {echo("keine Angabe");}
    }
    else
    {
        if(is_numeric($leistungen_bewerber['Mathe_End_Punkte']))
        {echo("".clean_num($leistungen_bewerber['Mathe_End_Punkte'], "en")."");}
        else
        {echo("keine Angabe");}
    }
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
}
//Wenn Mathemathik in den letzten vier Halbjahren nicht belegt wurde
else
{
    echo("Das Fach Mathematik wurde w&auml;hrend der letzten vier Halbjahre Ihrer schulischen Laufbahn nicht belegt!");
}
echo("</td>\n");
echo("</tr>\n");

/////////////////////////////////////////////////////
//////   Bestes naturwissenschaftliches Fach   //////
/////////////////////////////////////////////////////

$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
echo("Bestes naturwiss. Fach:");
echo("</td>\n");
echo("<td>\n");

//Wenn ein naturw. Fach in den letzten vier Halbjahren belegt wurde
if($leistungen_bewerber['Naturw_belegt'] == 1)
{
    //eigene Tabelle einf&uuml;gen
    echo("<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" style=\"width:100%;\">\n");

    //Art des naturwissenschaftlichen Fachs
    $zeile++;
    echo("<tr>\n");
    echo("<td colspan=\"2\">");
    echo("<b>".naturw_fach_eintrag($link, $leistungen_bewerber['fky_Naturw_Fach'])."</b>");
    echo("</td>\n");
    echo("</tr>\n");

    //alle Halbjahre
    for($l=1; $l<=4; $l++)
    {
        echo("<tr>\n");
        echo("<td style=\"width:15%;\">");
        echo("".$l.". Halbjahr:");
        echo("</td>\n");
        echo("<td>");
        if($leistungen_bewerber['Leistungen_Art'] == "n")
        {
            if(is_numeric($leistungen_bewerber['Naturw_HJ_'.$l.'_Note']))
            {echo("".float_e_d(clean_num($leistungen_bewerber['Naturw_HJ_'.$l.'_Note'], "en"))."");}
            else
            {echo("keine Angabe");}
        }
        else
        {
            if(is_numeric($leistungen_bewerber['Naturw_HJ_'.$l.'_Punkte']))
            {echo("".clean_num($leistungen_bewerber['Naturw_HJ_'.$l.'_Punkte'], "en")."");}
            else
            {echo("keine Angabe");}
        }
        echo("</td>\n");
        echo("</tr>\n");
    }

    //Abiturpr&uuml;fung
    echo("<tr>\n");
    echo("<td>");
    echo("Abiturpr&uuml;fung:");
    echo("</td>\n");
    echo("<td>");
    if($leistungen_bewerber['Leistungen_Art'] == "n")
    {
        if(is_numeric($leistungen_bewerber['Naturw_End_Note']))
        {echo("".float_e_d(clean_num($leistungen_bewerber['Naturw_End_Note'], "en"))."");}
        else
        {echo("keine Angabe");}
    }
    else
    {
        if(is_numeric($leistungen_bewerber['Naturw_End_Punkte']))
        {echo("".clean_num($leistungen_bewerber['Naturw_End_Punkte'], "en")."");}
        else
        {echo("keine Angabe");}
    }
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
}
//Wenn kein naturw. Fach in den letzten vier Halbjahren belegt wurde
else
{
    echo("Sie haben kein naturw. Fach w&auml;hrend der letzten vier Halbjahre Ihrer schulischen Laufbahn belegt!");
}
echo("</td>\n");
echo("</tr>\n");

/////////////////////////////////////////////////////////
/////////////// nachschulischen Werdegang ///////////////
/////////////////////////////////////////////////////////

//Leerzeile
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Leerzeile\">");
echo("</td>\n");
echo("</tr>\n");

//&UUML;berschrift
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
echo("Nachschulischen Werdegang");
echo("</td>\n");
echo("</tr>\n");

//Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
$zeile = 0;

//Freiwilliges soziales Jahr, Wehrdienst, Zivildienst
$zeile++;
echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
echo("<td class=\"Zeile_Bezeichnung\">");
echo("Soziales Jahr:");
echo("</td>\n");
echo("<td>");
if($daten_bewerber['Soziales_Jahr'] == 1)
{echo("Es wurde ein Soziales Jahr (oder Wehrdienst/Zivildienst) geleistet");}
else
{echo("Es wurde kein Soziales Jahr (oder Wehrdienst/Zivildienst) geleistet");}
echo("</td>\n");
echo("</tr>\n");

//Ausbildung
if($daten_bewerber['fky_Ausbildung'] != 0)
{
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Ausbildung als:");
    echo("</td>\n");
    echo("<td>");
    echo("".ausbildungen_eintrag($link, $daten_bewerber['fky_Ausbildung'])."");
    echo("</td>\n");
    echo("</tr>\n");
}

//////////////////////////////////////////
/////////////// Lebenslauf ///////////////
//////////////////////////////////////////

//Leerzeile
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Leerzeile\">");
echo("</td>\n");
echo("</tr>\n");

//&UUML;berschrift
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
echo("Lebenslauf");
echo("</td>\n");
echo("</tr>\n");

//Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
$zeile = 0;

echo("<tr>\n");
echo("<td colspan=\"2\">\n");
//Eigene Tabelle f&uuml;r die Darstellung des Lebenslaufs
echo("<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" style=\"width:100%;\">\n");

//Ausgabe der einzelnen Eintr&auml;ge
foreach($lebenslauf_bewerber AS  $nr_eintrag => $array_eintrag)
{
    $zeile++;
    //Darstellung der einzelnen Zeilen
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    //Datum
    echo("<td valign=\"top\" style=\"width:21%;\">");
    //Datum "am/von"
    echo("".$array_eintrag['datum_am_von']."");
    //Datum "bis"
    if($array_eintrag['datum_bis'] != "")
    {echo(" - ".$array_eintrag['datum_bis']."");}
    echo(":");
    echo("</td>\n");
    //Eintrag
    echo("<td>");
    echo("".nl2br($array_eintrag['eintrag'])."");
    echo("</td>\n");
    echo("</tr>\n");
}

echo("</table>\n");
echo("</td>\n");
echo("</tr>\n");

//////////////////////////////////////////
/////////////// Begr&uuml;ndung ///////////////
//////////////////////////////////////////

//Leerzeile
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Leerzeile\">");
echo("</td>\n");
echo("</tr>\n");

//&UUML;berschrift
echo("<tr>\n");
echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
echo("Begr&uuml;ndung");
echo("</td>\n");
echo("</tr>\n");

echo("<tr style=\"background-color:#EEEEEE\">\n");
echo("<td colspan=\"2\">");
echo("".nl2br($daten_bewerber['Begruendung'])."");
echo("</td>\n");
echo("</tr>\n");

echo("</table>\n");
echo("<br />\n");
?>
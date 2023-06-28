<div class="h1">Details der ausgew&auml;hlten Bewerber einsehen</div>

<?php
####################################
### DATEN DES BEWERBERS AUSLESEN ###
####################################

//pers&ouml;nliche Daten des Bewerbers aus der Tabelle "bewerber" und "leistungen_bewerber" auslesen
$sql = "SELECT
            b.pky_Bewerber,
            b.Anrede,
            b.Nachname,
            b.Vorname,
            b.Geburtsdatum,
            b.Email,
            b.Nationalitaet_fky_Land,
            b.Strasse,
            b.Hausnummer,
            b.Adresszusatz,
            b.Postleitzahl,
            b.Wohnort,
            b.fky_Land,
            b.Datum_Bewerbung,
            b.Datum_Wiederbewerbung,
            b.Datum_Aktivierung,
            b.Datum_Aenderung,
            b.fky_HZB,
            b.HZB_Sonstige,
            b.HZB_Jahr,
            b.HZB_Ort,
            b.HZB_fky_Land,
            b.Soziales_Jahr,
            b.fky_Ausbildung,
            b.Begruendung,
            b.Key_Aktivierung,
            b.Account_gesperrt,
            b.Bewerbung_zurueckgezogen,
            b.Datum_zurueckgezogen,
            b.Grund_zurueckgezogen,
            b.Datum_reaktiviert,
            b.Grund_reaktiviert,
            lb.Leistungen_Art,
            lb.HZB_Note,
            lb.HZB_Punkte,
            lb.Naturw_belegt,
            lb.fky_Naturw_Fach,
            lb.Naturw_HJ_1_Note,
            lb.Naturw_HJ_2_Note,
            lb.Naturw_HJ_3_Note,
            lb.Naturw_HJ_4_Note,
            lb.Naturw_End_Note,
            lb.Naturw_HJ_1_Punkte,
            lb.Naturw_HJ_2_Punkte,
            lb.Naturw_HJ_3_Punkte,
            lb.Naturw_HJ_4_Punkte,
            lb.Naturw_End_Punkte,
            lb.Mathe_belegt,
            lb.Mathe_HJ_1_Note,
            lb.Mathe_HJ_2_Note,
            lb.Mathe_HJ_3_Note,
            lb.Mathe_HJ_4_Note,
            lb.Mathe_End_Note,
            lb.Mathe_HJ_1_Punkte,
            lb.Mathe_HJ_2_Punkte,
            lb.Mathe_HJ_3_Punkte,
            lb.Mathe_HJ_4_Punkte,
            lb.Mathe_End_Punkte,
            lb.Zwischensumme,
            lb.Auswahlgespraech,
            lb.Fachkompetenz,
            lb.Sozialkompetenz,
            lb.Auswahlgespraech_Summe,
            lb.Endsumme,
            ab.Erschienen,
            ab.Auswahlgespraech_Datum,
            ab.Auswahlgespraech_Uhrzeit_von,
            ab.Auswahlgespraech_Uhrzeit_bis,
            TIME_FORMAT(TIMEDIFF(ab.Auswahlgespraech_Uhrzeit_bis, ab.Auswahlgespraech_Uhrzeit_von), '%i') AS Zeit_in_Min,
            ab.Auswahlgespraech_Kommentar,
            tkb.Datum_Termin,
            tkb.Uhrzeit_Termin,
            tkb.fky_Kommissionsmitglied_1,
            tkb.fky_Kommissionsmitglied_2,
            tkb.fky_Kommissionsmitglied_3,
            tkb.fky_Kommissionsmitglied_4
        FROM
            bewerber b
        INNER JOIN
            leistungen_bewerber lb
        ON
            b.pky_Bewerber = lb.fky_Bewerber
        LEFT JOIN
            auswahlgespraech_bewerber ab
        ON
            b.pky_Bewerber = ab.fky_Bewerber
        LEFT JOIN
            termin_kommission_bewerber tkb
        ON
            b.pky_Bewerber = tkb.fky_Bewerber
        WHERE
            pky_Bewerber IN (".$auswahl_string.")
        ORDER BY
            lb.Endsumme DESC, b.Account_gesperrt ASC, b.Bewerbung_zurueckgezogen ASC, b.Key_Aktivierung ASC, lb.Zwischensumme DESC, b.Nachname ASC;";
$ergebnis_bewerber = mysqli_query($link, $sql) OR die(mysqli_error($link));

while($datensatz = mysqli_fetch_assoc($ergebnis_bewerber))
{
    //Zeilenformatierung und Statustext ermitteln
    $format = bewerber_status_zeile($datensatz['Zwischensumme'], $datensatz['Endsumme'], $datensatz['Key_Aktivierung'], $datensatz['Account_gesperrt'], $datensatz['Bewerbung_zurueckgezogen']);

    ###################
    ### &UUML;BERSCHRIFT ###
    ###################

    echo("<div class=\"h2_teil\" style=\"border-left:10px solid ".$format['style_color'].";border-bottom:1px solid ".$format['style_color'].";color:".$format['style_color'].";\">");
    echo("<div style=\"float:right;\">(Interne Nr.: ".$datensatz['pky_Bewerber'].")</div>");
    echo("".($datensatz['Anrede'] == "h"?"Herr":"Frau")." ".$datensatz['Vorname']." ".$datensatz['Nachname']." (".$format['status'].")");
    echo("</div>\n");

    #########################
    ### AUSGABE DER DATEN ###
    #########################

    //Tabelle Start
    echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" style=\"width:100%;border-bottom:4px double ".$format['style_color'].";\">\n");

    //////////////////////////////////////////////////////////
    /////////////// Informationen zur Bewerbung //////////////
    //////////////////////////////////////////////////////////

    //&UUML;berschrift
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
    echo("Informationen zur Bewerbung");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Datum der Erstbewerbung
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td style=\"width:21%;\" class=\"Zeile_Bezeichnung\">");
    if($datensatz['Datum_Wiederbewerbung'] != NULL)
    {echo("Erstbewerbung am:");}
    else
    {echo("Datum der Bewerbung:");}
    echo("</td>\n");
    echo("<td>");
    echo("".datum_dbdate_d($datensatz['Datum_Bewerbung'])."");
    if($datensatz['Datum_Aktivierung'] != NULL)
    {echo(" (Account wurde aktiviert am ".datum_dbdate_d($datensatz['Datum_Aktivierung']).")");}
    else
    {echo(" (Account wurde nicht aktiviert)");}
    echo("</td>\n");
    echo("</tr>\n");

    if($datensatz['Datum_Wiederbewerbung'] != NULL)
    {
        //Datum der Zweitbewerbung
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Zweitbewerbung am:");
        echo("</td>\n");
        echo("<td>");
        echo("".datum_dbdate_d($datensatz['Datum_Wiederbewerbung'])."");
        echo("</td>\n");
        echo("</tr>\n");
    }

    if($datensatz['Datum_Aenderung'] != NULL)
    {
        //Datum der letzten Änderung
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Letzte &Auml;nderung der Daten:");
        echo("</td>\n");
        echo("<td>");
        echo("".datum_dbdate_d($datensatz['Datum_Aenderung'])."");
        echo("</td>\n");
        echo("</tr>\n");
    }

    if($datensatz['Datum_zurueckgezogen'] != NULL)
    {
        //Zur&uuml;ckgezogen
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Zur&uuml;ckgezogen:");
        echo("</td>\n");
        echo("<td>");
        echo("Die Bewerbung wurde am ".datum_dbdate_d($datensatz['Datum_zurueckgezogen'])." zur&uuml;ckgezogen!");
        echo("</td>\n");
        echo("</tr>\n");

        //Grund
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\" valign=\"top\">");
        echo("Grund:");
        echo("</td>\n");
        echo("<td>");
        if($datensatz['Grund_zurueckgezogen'] == "")
        {echo("keine Angabe");}
        else
        {echo("".nl2br($datensatz['Grund_zurueckgezogen'])."");}
        echo("</td>\n");
        echo("</tr>\n");
    }

    if($datensatz['Datum_reaktiviert'] != NULL AND $datensatz['Bewerbung_zurueckgezogen'] == NULL)
    {
        //Reaktiviert
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Reaktiviert:");
        echo("</td>\n");
        echo("<td>");
        echo("Die Bewerbung wurde am ".datum_dbdate_d($datensatz['Datum_reaktiviert'])." wieder reaktiviert!");
        echo("</td>\n");
        echo("</tr>\n");

        //Grund
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\" valign=\"top\">");
        echo("Grund:");
        echo("</td>\n");
        echo("<td>");
        if($datensatz['Grund_reaktiviert'] == "")
        {echo("keine Angabe");}
        else
        {echo("".nl2br($datensatz['Grund_reaktiviert'])."");}
        echo("</td>\n");
        echo("</tr>\n");
    }

    //Leerzeile
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    ///////////////////////////////////////////////////
    /////////////// pers&ouml;nliche Angaben ///////////////
    ///////////////////////////////////////////////////

    //&UUML;berschrift
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
    echo("pers&ouml;nliche Angaben");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Geburtsdatum
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td style=\"width:21%;\" class=\"Zeile_Bezeichnung\">");
    echo("Geburtsdatum:");
    echo("</td>\n");
    echo("<td>");
    echo("".datum_dbdate_d($datensatz['Geburtsdatum'])."");
    echo("</td>\n");
    echo("</tr>\n");

    //Nationalit&auml;t
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Nationalit&auml;t:");
    echo("</td>\n");
    echo("<td>");
    echo("".land_eintrag($link, $datensatz['Nationalitaet_fky_Land'])."");
    echo("</td>\n");
    echo("</tr>\n");

    //Strasse und Hausnummer
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Stra&szlig;e/Nummer:");
    echo("</td>\n");
    echo("<td>");
    echo("".$datensatz['Strasse']." ".$datensatz['Hausnummer']."");
    echo("</td>\n");
    echo("</tr>\n");

    //Adresszusatz
    if($datensatz['Adresszusatz'] != "")
    {
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Adresszusatz:");
        echo("</td>\n");
        echo("<td>");
        echo("".$datensatz['Adresszusatz']."");
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
    echo("".$datensatz['Postleitzahl']." ".$datensatz['Wohnort']."");
    echo("</td>\n");
    echo("</tr>\n");

    //Land
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Land:");
    echo("</td>\n");
    echo("<td>");
    echo("".land_eintrag($link, $datensatz['fky_Land'])."");
    echo("</td>\n");
    echo("</tr>\n");

    //Email
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Email:");
    echo("</td>\n");
    echo("<td>");
    echo("".$datensatz['Email']."");
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
    echo("".hzb_art_eintrag($link, $datensatz['fky_HZB'])."");
    echo("</td>\n");
    echo("</tr>\n");

    //HZB sonstige
    if($datensatz['fky_HZB'] == PKY_SONST_HZB)
    {
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Sonstige HZB:");
        echo("</td>\n");
        echo("<td>");
        echo("".$datensatz['HZB_Sonstige']."");
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
    echo("".$datensatz['HZB_Jahr']."");
    echo("</td>\n");
    echo("</tr>\n");

    //HZB Ort
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Ort der HZB:");
    echo("</td>\n");
    echo("<td>");
    echo("".$datensatz['HZB_Ort']."");
    echo("</td>\n");
    echo("</tr>\n");

    //HZB Land
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Land der HZB:");
    echo("</td>\n");
    echo("<td>");
    echo("".land_eintrag($link, $datensatz['HZB_fky_Land'])."");
    echo("</td>\n");
    echo("</tr>\n");

    //HZB Note
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Abschlussnote:");
    echo("</td>\n");
    echo("<td>");
    echo("<b>".float_e_d(clean_num($datensatz['HZB_Note'], "en"))."</b>");
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
    echo("Nachschulischer Werdegang");
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
    if($datensatz['Soziales_Jahr'] == 1)
    {echo("Es wurde ein Soziales Jahr (oder Wehrdienst/Zivildienst) geleistet");}
    else
    {echo("Es wurde kein Soziales Jahr (oder Wehrdienst/Zivildienst) geleistet");}
    echo("</td>\n");
    echo("</tr>\n");

    //Ausbildung
    if($datensatz['fky_Ausbildung'] != 0)
    {
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Ausbildung als:");
        echo("</td>\n");
        echo("<td>");
        echo("".ausbildungen_eintrag($link, $datensatz['fky_Ausbildung'])."");
        echo("</td>\n");
        echo("</tr>\n");
    }

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
    if($datensatz['Mathe_belegt'] == 1)
    {
        //eigene Tabelle einf&uuml;gen
        echo("<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" style=\"width:100%;\">\n");
        echo("<tr>\n");

        //alle Halbjahre
        for($l=1; $l<=4; $l++)
        {
            echo("<td style=\"width:15%;\">");
            echo("<b>HJ".$l.":</b> ");
            if($datensatz['Leistungen_Art'] == "n")
            {
                if(is_numeric($datensatz['Mathe_HJ_'.$l.'_Note']))
                {echo("".float_e_d(clean_num($datensatz['Mathe_HJ_'.$l.'_Note'], "en"))." (Note)");}
                else
                {echo("keine Angabe");}
            }
            else
            {
                if(is_numeric($datensatz['Mathe_HJ_'.$l.'_Punkte']))
                {echo("".clean_num($datensatz['Mathe_HJ_'.$l.'_Punkte'], "en")." (Punkte)");}
                else
                {echo("keine Angabe");}
            }
            echo("</td>\n");
        }

        //Abiturpr&uuml;fung
        echo("<td style=\"width:25%;\">");
        echo("<b>Abiturpr&uuml;fung:</b> ");
        if($datensatz['Leistungen_Art'] == "n")
        {
            if(is_numeric($datensatz['Mathe_End_Note']))
            {echo("".float_e_d(clean_num($datensatz['Mathe_End_Note'], "en"))." (Note)");}
            else
            {echo("keine Angabe");}
        }
        else
        {
            if(is_numeric($datensatz['Mathe_End_Punkte']))
            {echo("".clean_num($datensatz['Mathe_End_Punkte'], "en")." (Punkte)");}
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
        echo("Das Fach Mathematik wurde w&auml;hrend der letzten vier Halbjahre der schulischen Laufbahn nicht belegt!");
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
    if($datensatz['Naturw_belegt'] == 1)
    {
        //eigene Tabelle einf&uuml;gen
        echo("<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" style=\"width:100%;\">\n");

        //Art des naturwissenschaftlichen Fachs
        echo("<tr>\n");
        echo("<td colspan=\"2\">");
        echo("<u>".naturw_fach_eintrag($link, $datensatz['fky_Naturw_Fach'])."</u>");
        echo("</td>\n");
        echo("</tr>\n");

        echo("<tr>\n");

        //alle Halbjahre
        for($l=1; $l<=4; $l++)
        {
            echo("<td style=\"width:15%;\">");
            echo("<b>HJ".$l.":</b> ");
            if($datensatz['Leistungen_Art'] == "n")
            {
                if(is_numeric($datensatz['Naturw_HJ_'.$l.'_Note']))
                {echo("".float_e_d(clean_num($datensatz['Naturw_HJ_'.$l.'_Note'], "en"))." (Note)");}
                else
                {echo("keine Angabe");}
            }
            else
            {
                if(is_numeric($datensatz['Naturw_HJ_'.$l.'_Punkte']))
                {echo("".clean_num($datensatz['Naturw_HJ_'.$l.'_Punkte'], "en")." (Punkte)");}
                else
                {echo("keine Angabe");}
            }
            echo("</td>\n");
        }

        //Abiturpr&uuml;fung
        echo("<td style=\"width:25%;\">");
        echo("<b>Abiturpr&uuml;fung:</b> ");
        if($datensatz['Leistungen_Art'] == "n")
        {
            if(is_numeric($datensatz['Naturw_End_Note']))
            {echo("".float_e_d(clean_num($datensatz['Naturw_End_Note'], "en"))." (Note)");}
            else
            {echo("keine Angabe");}
        }
        else
        {
            if(is_numeric($datensatz['Naturw_End_Punkte']))
            {echo("".clean_num($datensatz['Naturw_End_Punkte'], "en")." (Punkte)");}
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
        echo("Es wurde kein naturw. Fach w&auml;hrend der letzten vier Halbjahre der schulischen Laufbahn belegt!");
    }
    echo("</td>\n");
    echo("</tr>\n");

    //Bonus freiwilliges soziales Jahr, Wehrdienst, Zivildienst
    if($datensatz['Soziales_Jahr'] == 1)
    {
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Bonus Soziales Jahr:");
        echo("</td>\n");
        echo("<td>");
        echo("<b>-".float_e_d(clean_num(BONUS_SOZ_JAHR, "en"))."</b> von der Abschlu&szlig;note");
        echo("</td>\n");
        echo("</tr>\n");
    }

    //Bonus Ausbildung
    if($datensatz['fky_Ausbildung'] != 0)
    {
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Bonus Ausbildung:");
        echo("</td>\n");
        echo("<td>");
        echo("<b>-".float_e_d(clean_num(BONUS_AUSBILDUNG, "en"))."</b> von der Abschlu&szlig;note");
        echo("</td>\n");
        echo("</tr>\n");
    }

    //Zwischensumme
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Zwischensumme:");
    echo("</td>\n");
    echo("<td>");
    if($datensatz['Zwischensumme'] != NULL)
    {echo("<b>".$datensatz['Zwischensumme']." Punkte</b>");}
    else
    {echo("<b>nicht berechenbar!</b>");}
    echo("</td>\n");
    echo("</tr>\n");

    //ben&ouml;tigte Punkte
    //Werden nur angezeigt, wenn f&uuml;r den Bewerber noch keine Angaben f&uuml;r das Auswahlgespr&auml;ch gemacht wurden
    if($datensatz['Auswahlgespraech'] === NULL)
    {
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Ben&ouml;tigte Punkte:");
        echo("</td>\n");
        echo("<td>");
        echo("<b>".notwendige_punkte_auswahlgespraech($datensatz['Zwischensumme'])."</b> zum Erreichen der Endsumme");
        echo("</td>\n");
        echo("</tr>\n");
    }

    ///////////////////////////////////////////////
    /////////////// Auswahlgespr&auml;ch ///////////////
    ///////////////////////////////////////////////

    //Leerzeile
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //&UUML;berschrift
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
    echo("Auswahlgespr&auml;ch");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Kommission
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\" valign=\"top\">");
    echo("Kommission:");
    echo("</td>\n");
    echo("<td>\n");
    if($datensatz['Datum_Termin'] === NULL)
    {echo("Es wurde noch keine Kommission ausgew&auml;hlt!");}
    elseif($datensatz['fky_Kommissionsmitglied_1'] == 0 AND $datensatz['fky_Kommissionsmitglied_2'] == 0 AND $datensatz['fky_Kommissionsmitglied_3'] == 0 AND $datensatz['fky_Kommissionsmitglied_4'] == 0)
    {echo("Es wurde keine Kommission angegeben!");}
    else
    {
        echo("<ul style=\"margin:0 0 0 20px;padding:0;\">\n");
        for($w=1; $w<=4; $w++)
        {
            if($datensatz['fky_Kommissionsmitglied_'.$w] != 0)
            {
                echo("<li>".kommissionsmitglied_eintrag($link, $datensatz['fky_Kommissionsmitglied_'.$w])."</li>\n");
            }
        }
        echo("</ul>\n");
    }
    echo("</td>\n");
    echo("</tr>\n");

    //vereinbarter Termin
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Vereinbarter Termin:");
    echo("</td>\n");
    echo("<td>");
    if($datensatz['Datum_Termin'] === NULL)
    {echo("Es wurde noch kein Termin vereinbart!");}
    else
    {echo("am ".datum_dbdate_d($datensatz['Datum_Termin'])." um ".cut_sec($datensatz['Uhrzeit_Termin'])." Uhr");}
    echo("</td>\n");
    echo("</tr>\n");

    //Wenn das Auswahlgespr&auml;ch schon stattgefunden hat
    if($datensatz['Auswahlgespraech'] == 1)
    {
        //Bewerber NICHT zum Auswahlgespr&auml;ch erschienen
        if($datensatz['Erschienen'] != 1)
        {
            //Hinweis
            $zeile++;
            echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
            echo("<td colspan=\"2\">");
            echo("<b>Der Bewerber ist nicht zum Auswahlgespr&auml;ch erschienen!</b>");
            echo("</td>\n");
            echo("</tr>\n");
        }
        else
        {
            //tats&auml;chlicher Termin
            $zeile++;
            echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Tats&auml;chlicher Termin:");
            echo("</td>\n");
            echo("<td>");
            echo("am ".datum_dbdate_d($datensatz['Auswahlgespraech_Datum'])." von ".cut_sec($datensatz['Auswahlgespraech_Uhrzeit_von'])." bis ".cut_sec($datensatz['Auswahlgespraech_Uhrzeit_bis'])." Uhr");
            echo(" (".$datensatz['Zeit_in_Min']." min)");
            echo("</td>\n");
            echo("</tr>\n");
        }

        //Punkte im Auswahlgespr&auml;ch f&uuml;r Fachkompetenz
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Fachkompetenz:");
        echo("</td>\n");
        echo("<td>");
        echo("".$datensatz['Fachkompetenz']." Punkte");
        echo("</td>\n");
        echo("</tr>\n");

        //Punkte im Auswahlgespr&auml;ch f&uuml;r soziale Kompetenz
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Soziale Kompetenz:");
        echo("</td>\n");
        echo("<td>");
        echo("".$datensatz['Sozialkompetenz']." Punkte");
        echo("</td>\n");
        echo("</tr>\n");

        //Summe der Punkte im Auswahlgespr&auml;ch
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Summe Auswahlgespr&auml;ch:");
        echo("</td>\n");
        echo("<td>");
        echo("".float_e_d($datensatz['Auswahlgespraech_Summe'])." Punkte");
        echo("</td>\n");
        echo("</tr>\n");

        //Erreichte Endsumme
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("erreichte Endsumme:");
        echo("</td>\n");
        echo("<td>");
        echo("<b>".$datensatz['Endsumme']." Punkte</b>");
        echo("</td>\n");
        echo("</tr>\n");

        if($datensatz['Auswahlgespraech_Kommentar'] != "")
        {
            //Kommentar
            $zeile++;
            echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Kommentar:");
            echo("</td>\n");
            echo("<td>");
            echo("".nl2br($datensatz['Auswahlgespraech_Kommentar'])."");
            echo("</td>\n");
            echo("</tr>\n");
        }
    }
    //Wenn das Auswahlgespr&auml;ch noch nicht stattgefunden hat
    else
    {
        //Hinweis
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td colspan=\"2\">");
        echo("<b>Der Bewerber war noch nicht im Auswahlgespr&auml;ch!</b>");
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
                fky_Bewerber = '".$datensatz['pky_Bewerber']."'
            ORDER BY Nr_Eintrag ASC;";
    $ergebnis_ll = mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Array aus dem Datensatz erzeugen
    while($daten_ll = mysqli_fetch_assoc($ergebnis_ll))
    {
        $zeile++;
        //Darstellung der einzelnen Zeilen
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        //Datum
        echo("<td valign=\"top\" style=\"width:21%;\">");
        //Datum "am/von"
        echo("".$daten_ll['Datum_am_von']."");
        //Datum "bis"
        if($daten_ll['Datum_bis'] != "")
        {echo(" - ".$daten_ll['Datum_bis']."");}
        echo(":");
        echo("</td>\n");
        //Eintrag
        echo("<td>");
        echo("".nl2br($daten_ll['Eintrag'])."");
        echo("</td>\n");
        echo("</tr>\n");
    }
    mysqli_free_result($ergebnis_ll);

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
    echo("".nl2br($datensatz['Begruendung'])."");
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
    echo("<br />\n");
}
mysqli_free_result($ergebnis_bewerber);
?>

<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1"><a href="index.php?seite=intern_admin&intern_a=bewerber_einsehen">zur&uuml;ck zur Verwaltung f&uuml;r Bewerber</a></span></span><br/>
<div class="h1">Auswahlgespr&auml;ch</div>

<?php
#################
### WARNUNGEN ###
#################

if(isset($_POST['senden']))
{
    foreach($auswahl_array AS $pky_bewerber)
    {
        //Wenn bei einem Bewerber Angaben zum Auswahlgespr&auml;ch nicht m&ouml;glich sind, dann werden diese nicht gepr&uuml;ft
        if(!isset($_POST['angabe_nicht_moeglich'][$pky_bewerber]))
        {
            //wenn nicht angegeben wurde, ob der Bewerber zum Auswahlgespr&auml;ch erschienen ist
            if(!isset($_POST['bewerber_erschienen'][$pky_bewerber]))
            {
                $warnung[$pky_bewerber]['bewerber_erschienen'] = "Es wurde nicht angegeben, ob der Bewerber zum Auswahlgespr&auml;ch erschienen ist!";
            }
            else
            {
                //Wenn das Datum des Auswahlgespr&auml;chs nicht eingetragen wurde (und der Bewerber erschienen ist)
                if($_POST['bewerber_erschienen'][$pky_bewerber] == "1" AND !datum_regex(trim($_POST['datum'][$pky_bewerber])))
                {
                    $warnung[$pky_bewerber]['datum'] = "Es wurde kein oder ein ung&uuml;ltiges Datum eingetragen!";
                }
                //Wenn die Uhrzeit (von) des Auswahlgespr&auml;chs nicht eingetragen wurde (und der Bewerber erschienen ist)
                if($_POST['bewerber_erschienen'][$pky_bewerber] == "1" AND (trim($_POST['uhrzeit_von'][$pky_bewerber]) == "" OR !uhrzeit_check($_POST['uhrzeit_von'][$pky_bewerber])))
                {
                    $warnung[$pky_bewerber]['uhrzeit_von'] = "Es wurden keine Uhrzeit f&uuml;r den Beginn des Auswahlgespr&auml;chs eingetragen oder die Angabe ist ung&uuml;ltig!";
                }
                //Wenn die Uhrzeit (bis) des Auswahlgespr&auml;chs nicht eingetragen wurde (und der Bewerber erschienen ist)
                if($_POST['bewerber_erschienen'][$pky_bewerber] == "1" AND (trim($_POST['uhrzeit_bis'][$pky_bewerber]) == "" OR !uhrzeit_check($_POST['uhrzeit_bis'][$pky_bewerber])))
                {
                    $warnung[$pky_bewerber]['uhrzeit_bis'] = "Es wurden keine Uhrzeit f&uuml;r das Ende des Auswahlgespr&auml;chs eingetragen oder die Angabe ist ung&uuml;ltig!";
                }
                //Wenn die erreichten Punkte bei Fachkompetenz nicht eingetragen wurden (und der Bewerber erschienen ist)
                if($_POST['bewerber_erschienen'][$pky_bewerber] == "1" AND !punkte_check(trim($_POST['fachkompetenz'][$pky_bewerber])))
                {
                    $warnung[$pky_bewerber]['fachkompetenz'] = "Es wurden keine erreichten Punkte bei \"Fachkompetenz\" eingetragen oder die Angabe ist ung&uuml;ltig!";
                }
                //Wenn die erreichten Punkte bei soziale Kompetenz nicht eingetragen wurden (und der Bewerber erschienen ist)
                if($_POST['bewerber_erschienen'][$pky_bewerber] == "1" AND !punkte_check(trim($_POST['sozialkompetenz'][$pky_bewerber])))
                {
                    $warnung[$pky_bewerber]['sozialkompetenz'] = "Es wurden keine erreichten Punkte bei \"Soziale Kompetenz\" eingetragen oder die Angabe ist ung&uuml;ltig!";
                }
                //Wenn die Endsumme nicht eingetragen wurden (gilt nur f&uuml;r Bewerber, deren Zwischensumme nicht berechenbar ist und wo der Bewerber auch erschienen ist)
                if($_POST['bewerber_erschienen'][$pky_bewerber] == "1" AND isset($_POST['endsumme'][$pky_bewerber]) AND !endsumme_check(trim($_POST['endsumme'][$pky_bewerber])))
                {
                    $warnung[$pky_bewerber]['endsumme'] = "Es wurden keine Endsumme eingetragen oder die Angabe ist ung&uuml;ltig!";
                }
            }
        }
    }
}

#############################
### HINWEIS AUF WARNUNGEN ###
#############################

if(isset($_POST['senden']) AND isset($warnung))
{
    echo("<div class=\"Information_Warnung\" style=\"text-align:center;\">\n");
    echo("<b>Fehler bei der Eingabe!");
    echo("</div>\n");
}

if((isset($_POST['auswahl'])) OR (isset($_POST['senden']) AND isset($warnung)))
{
    ####################################
    ### DATEN DES BEWERBERS AUSLESEN ###
    ####################################

    //pers&ouml;nliche Daten des Bewerbers aus der Tabelle "bewerber" und "leistungen_bewerber" auslesen
    $sql = "SELECT
                b.pky_Bewerber,
                b.Anrede,
                b.Nachname,
                b.Vorname,
                b.Soziales_Jahr,
                b.fky_Ausbildung,
                b.Key_Aktivierung,
                b.Account_gesperrt,
                b.Bewerbung_zurueckgezogen,
                b.Begruendung,
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

    //Formular Start
    echo("<form method=\"post\" action=\"index.php?seite=intern_admin&intern_a=bewerber_aktionen\">\n");
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

        ###################################################
        ### AUSGABE DER DATEN ZUR LEISTUNG UND FORMULAR ###
        ###################################################

        //Tabelle Start
        echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" style=\"width:100%;border-bottom:4px double ".$format['style_color'].";\">\n");

        //////////////////////////////////////////
        /////////////// Leistungen ///////////////
        //////////////////////////////////////////

        //&UUML;berschrift
        echo("<tr>\n");
        echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
        echo("&Uuml;berblick &uuml;ber die erbrachten Leistungen");
        echo("</td>\n");
        echo("</tr>\n");

        //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
        $zeile = 0;

        //Abschlussnote
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Abschlussnote:");
        echo("</td>\n");
        echo("<td>");
        echo("<b>".float_e_d(clean_num($datensatz['HZB_Note'], "en"))."</b>");
        echo("</td>\n");
        echo("</tr>\n");

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
            echo("<b>-".float_e_d(clean_num(BONUS_AUSBILDUNG, "en"))."</b> von der Abschlu&szlig;note (".ausbildungen_eintrag($link, $datensatz['fky_Ausbildung']).")");
            echo("</td>\n");
            echo("</tr>\n");
        }

        //Zwischensumme
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\" style=\"width:21%;\">");
        echo("Zwischensumme:");
        echo("</td>\n");
        echo("<td>");
        if($datensatz['Zwischensumme'] != NULL)
        {echo("<b>".$datensatz['Zwischensumme']." Punkte</b>");}
        else
        {echo("<b>nicht berechenbar!</b>");}
        //Die Zwischensumme versteckt weitergeben
        echo("<input type=\"hidden\" name=\"zwischensumme[".$datensatz['pky_Bewerber']."]\" value=\"".$datensatz['Zwischensumme']."\">\n");
        //Information versteckt weitergeben, ob f&uuml;r den Bewerber schon ein Eintrag zum Auswahlgespr&auml;ch gemacht wurde
        echo("<input type=\"hidden\" name=\"auswahlgespraech[".$datensatz['pky_Bewerber']."]\" value=\"".$datensatz['Auswahlgespraech']."\">\n");
        echo("</td>\n");
        echo("</tr>\n");

        //ben&ouml;tigte Punkte
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Ben&ouml;tigte Punkte:");
        echo("</td>\n");
        echo("<td>");
        echo("<b>".notwendige_punkte_auswahlgespraech($datensatz['Zwischensumme'])."</b>");
        echo("</td>\n");
        echo("</tr>\n");

        ////////////////////////////////////////////////////
        /////////////// Termin und Kommission ///////////////
        ////////////////////////////////////////////////////

        //Leerzeile
        echo("<tr>\n");
        echo("<td colspan=\"2\" class=\"Leerzeile\">");
        echo("</td>\n");
        echo("</tr>\n");

        //&UUML;berschrift
        echo("<tr>\n");
        echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
        echo("Vereinbarter Termin und Kommission");
        echo("</td>\n");
        echo("</tr>\n");

        //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
        $zeile = 0;

        //Termin
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Termin:");
        echo("</td>\n");
        echo("<td>");
        if($datensatz['Datum_Termin'] === NULL)
        {echo("Es wurde noch kein Termin vereinbart!");}
        else
        {echo("am ".datum_dbdate_d($datensatz['Datum_Termin'])." um ".cut_sec($datensatz['Uhrzeit_Termin'])." Uhr");}
        echo("</td>\n");
        echo("</tr>\n");

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

        //////////////////////////////////////////////////////////////////////
        /////////////// Formularfelder f&uuml;r das Auswahlgespr&auml;ch ///////////////
        //////////////////////////////////////////////////////////////////////

        //Leerzeile
        echo("<tr>\n");
        echo("<td colspan=\"2\" class=\"Leerzeile\">");
        echo("</td>\n");
        echo("</tr>\n");

        //&UUML;berschrift
        echo("<tr>\n");
        echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
        echo("Angaben zum Auswahlgespr&auml;ch ");
        if($datensatz['Auswahlgespraech'] == 1)
        {echo("&auml;ndern");}
        else
        {echo("eintragen");}
        echo("</td>\n");
        echo("</tr>\n");

        #########################
        # Ausgabe von Warnungen #
        #########################

        if(isset($_POST['senden']) AND isset($warnung[$datensatz['pky_Bewerber']]))
        {
            echo("<tr>\n");
            echo("<td colspan=\"2\" class=\"Warnung\">");
            echo("<ul style=\"margin:0;\">");
            foreach($warnung[$datensatz['pky_Bewerber']] AS $var)
            {
                echo("<li>".$var."</li>");
            }
            echo("</ul>");
            echo("</td>\n");
            echo("</tr>\n");
        }

        //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
        $zeile = 0;

        //Wenn der Account nicht aktiviert oder gesperrt wurde oder wenn die Bewerbung zur&uuml;ckgezogen wurde,
        //dann sind keine Angaben zum Auswahlgespr&auml;ch m&ouml;glich
        if($datensatz['Key_Aktivierung'] != NULL OR
           $datensatz['Account_gesperrt'] == 1 OR
           $datensatz['Bewerbung_zurueckgezogen'] == 1)
        {
            echo("<tr>\n");
            echo("<td colspan=\"2\" class=\"Tabelle_Leer\">");
            echo("<input type=\"hidden\" name=\"angabe_nicht_moeglich[".$datensatz['pky_Bewerber']."]\" value=\"0\">\n");
            echo("Angaben zum Auswahlgespr&auml;ch sind bei diesem Bewerber nicht m&ouml;glich!");
            echo("</td>\n");
            echo("</tr>\n");
        }
        else
        {
            //Bewerber erschienen
            $zeile++;
            if(isset($warnung[$datensatz['pky_Bewerber']]['bewerber_erschienen']))
            {$ergebnis_check = false;}else{$ergebnis_check = true;}
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Bewerber erschienen: *");
            echo("</td>\n");
            echo("<td>");
            if((!isset($_POST['bewerber_erschienen'][$datensatz['pky_Bewerber']]) AND $datensatz['Auswahlgespraech'] == 1 AND $datensatz['Erschienen'] == 1) OR
               (isset($_POST['bewerber_erschienen'][$datensatz['pky_Bewerber']]) AND $_POST['bewerber_erschienen'][$datensatz['pky_Bewerber']] == "1"))
            {$check = "checked=\"checked\"";}
            else
            {$check = "";}
            echo("<input name=\"bewerber_erschienen[".$datensatz['pky_Bewerber']."]\" type=\"radio\" value=\"1\" ".$check."> Ja");
            if((!isset($_POST['bewerber_erschienen'][$datensatz['pky_Bewerber']]) AND $datensatz['Auswahlgespraech'] == 1 AND $datensatz['Erschienen'] == 0) OR
               (isset($_POST['bewerber_erschienen'][$datensatz['pky_Bewerber']]) AND $_POST['bewerber_erschienen'][$datensatz['pky_Bewerber']] == "0"))
            {$check = "checked=\"checked\"";}
            else
            {$check = "";}
            echo("<input name=\"bewerber_erschienen[".$datensatz['pky_Bewerber']."]\" type=\"radio\" value=\"0\" ".$check."> Nein");
            echo(" <span style=\"font-size:10pt;\"> (Wenn der Bewerber <b>nicht</b> zum Auswahlgespr&auml;ch erschienen ist werden die restlichen Eingaben ignoriert!)</span>");
            echo("</td>\n");
            echo("</tr>\n");

            //Datum des Auswahlgespr&auml;chs
            $zeile++;
            if(isset($warnung[$datensatz['pky_Bewerber']]['datum']))
            {$ergebnis_check = false;}else{$ergebnis_check = true;}
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Datum: *");
            echo("</td>\n");
            echo("<td>");
            if(!isset($_POST['datum'][$datensatz['pky_Bewerber']]) AND $datensatz['Auswahlgespraech'] == 1 AND $datensatz['Auswahlgespraech_Datum'] != NULL)
            {$value = datum_dbdate_d($datensatz['Auswahlgespraech_Datum']);}
            elseif(isset($_POST['datum'][$datensatz['pky_Bewerber']]))
            {$value = htmlXspecialchars(trim($_POST['datum'][$datensatz['pky_Bewerber']]));}
            else
            {$value = "";}
            echo("<input name=\"datum[".$datensatz['pky_Bewerber']."]\" id=\"datum_".$datensatz['pky_Bewerber']."\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".$value."\">");
            //Button zum einf&uuml;gen des aktuellen Datums
            echo("<input class=\"Buttons_Klein\" type=\"button\" value=\"(<< heute)\" onClick=\"Datum('datum_".$datensatz['pky_Bewerber']."')\">\n");
            echo(" <span style=\"font-size:10pt;\"> (Bitte im Format <b>(T)T:(M)M:JJJJ</b> eingeben)</span>");
            echo("</td>\n");
            echo("</tr>\n");

            //Uhrzeit des Auswahlgespr&auml;chs
            $zeile++;
            if(isset($warnung[$datensatz['pky_Bewerber']]['uhrzeit_von']) OR isset($warnung[$datensatz['pky_Bewerber']]['uhrzeit_bis']))
            {$ergebnis_check = false;}else{$ergebnis_check = true;}
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Uhrzeit: *");
            echo("</td>\n");
            echo("<td>");
            if(!isset($_POST['uhrzeit_von'][$datensatz['pky_Bewerber']]) AND $datensatz['Auswahlgespraech'] == 1 AND $datensatz['Auswahlgespraech_Uhrzeit_von'] != NULL)
            {$value = cut_sec($datensatz['Auswahlgespraech_Uhrzeit_von']);}
            elseif(isset($_POST['uhrzeit_von'][$datensatz['pky_Bewerber']]))
            {$value = htmlXspecialchars(trim($_POST['uhrzeit_von'][$datensatz['pky_Bewerber']]));}
            else
            {$value = "";}
            echo("<input name=\"uhrzeit_von[".$datensatz['pky_Bewerber']."]\" id=\"uhrzeit_von_".$datensatz['pky_Bewerber']."\" type=\"text\" size=\"5\" maxlength=\"5\" value=\"".$value."\"> Uhr");
            //Button zum einf&uuml;gen der aktuellen Uhrzeit
            echo(" <input class=\"Buttons_Klein\" type=\"button\" value=\"(<< jetzt)\" onClick=\"Uhrzeit('uhrzeit_von_".$datensatz['pky_Bewerber']."')\">\n");
            if(!isset($_POST['uhrzeit_bis'][$datensatz['pky_Bewerber']]) AND $datensatz['Auswahlgespraech'] == 1 AND $datensatz['Auswahlgespraech_Uhrzeit_bis'] != NULL)
            {$value = cut_sec($datensatz['Auswahlgespraech_Uhrzeit_bis']);}
            elseif(isset($_POST['uhrzeit_bis'][$datensatz['pky_Bewerber']]))
            {$value = htmlXspecialchars(trim($_POST['uhrzeit_bis'][$datensatz['pky_Bewerber']]));}
            else
            {$value = "";}
            echo(" bis <input name=\"uhrzeit_bis[".$datensatz['pky_Bewerber']."]\" id=\"uhrzeit_bis_".$datensatz['pky_Bewerber']."\" type=\"text\" size=\"5\" maxlength=\"5\" value=\"".$value."\"> Uhr");
            //Button zum einf&uuml;gen der aktuellen Uhrzeit
            echo(" <input class=\"Buttons_Klein\" type=\"button\" value=\"(<< jetzt)\" onClick=\"Uhrzeit('uhrzeit_bis_".$datensatz['pky_Bewerber']."')\">\n");
            echo(" <span style=\"font-size:10pt;\"> (Bitte im Format <b>(H)H:MM</b> eingeben)</span>");
            echo("</td>\n");
            echo("</tr>\n");

            //Fachkompetenz
            $zeile++;
            if(isset($warnung[$datensatz['pky_Bewerber']]['fachkompetenz']))
            {$ergebnis_check = false;}else{$ergebnis_check = true;}
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Fachkompetenz: *");
            echo("</td>\n");
            echo("<td>");
            if(!isset($_POST['fachkompetenz'][$datensatz['pky_Bewerber']]) AND $datensatz['Auswahlgespraech'] == 1)
            {$value = $datensatz['Fachkompetenz'];}
            elseif(isset($_POST['fachkompetenz'][$datensatz['pky_Bewerber']]))
            {$value = htmlXspecialchars(trim($_POST['fachkompetenz'][$datensatz['pky_Bewerber']]));}
            else
            {$value = "";}
            echo("<input name=\"fachkompetenz[".$datensatz['pky_Bewerber']."]\" type=\"text\" size=\"2\" maxlength=\"2\" value=\"".$value."\"> Punkte (0-15)");
            echo("</td>\n");
            echo("</tr>\n");

            //Soziale Kompetenz
            $zeile++;
            if(isset($warnung[$datensatz['pky_Bewerber']]['sozialkompetenz']))
            {$ergebnis_check = false;}else{$ergebnis_check = true;}
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Soziale Kompetenz: *");
            echo("</td>\n");
            echo("<td>");
            if(!isset($_POST['sozialkompetenz'][$datensatz['pky_Bewerber']]) AND $datensatz['Auswahlgespraech'] == 1)
            {$value = $datensatz['Sozialkompetenz'];}
            elseif(isset($_POST['sozialkompetenz'][$datensatz['pky_Bewerber']]))
            {$value = htmlXspecialchars(trim($_POST['sozialkompetenz'][$datensatz['pky_Bewerber']]));}
            else
            {$value = "";}
            echo("<input name=\"sozialkompetenz[".$datensatz['pky_Bewerber']."]\" type=\"text\" size=\"2\" maxlength=\"2\" value=\"".$value."\"> Punkte (0-15)");
            echo("</td>\n");
            echo("</tr>\n");

            //Angabe der Endpunkte f&uuml;r Bewerber, deren Zwischensumme nicht berechenbar ist
            //ODER die die Zwischensumme nicht erreicht haben
            if( $datensatz['Zwischensumme'] == NULL OR
               ($datensatz['Zwischensumme'] != NULL AND $datensatz['Zwischensumme'] < GRENZE_ZWISCHENSUMME) )
            {
                $zeile++;
                if(isset($warnung[$datensatz['pky_Bewerber']]['endsumme']))
                {$ergebnis_check = false;}else{$ergebnis_check = true;}
                echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
                echo("<td class=\"Zeile_Bezeichnung\">");
                echo("Endsumme: *");
                echo("</td>\n");
                echo("<td>");
                if(!isset($_POST['endsumme'][$datensatz['pky_Bewerber']]) AND $datensatz['Auswahlgespraech'] == 1)
                {$value = $datensatz['Endsumme'];}
                elseif(isset($_POST['endsumme'][$datensatz['pky_Bewerber']]))
                {$value = htmlXspecialchars(trim($_POST['endsumme'][$datensatz['pky_Bewerber']]));}
                else
                {$value = "";}
                echo("<input name=\"endsumme[".$datensatz['pky_Bewerber']."]\" type=\"text\" size=\"3\" maxlength=\"3\" value=\"".$value."\">");
                echo("</td>\n");
                echo("</tr>\n");
            }

            //Kommentar
            $zeile++;
            echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
            echo("<td class=\"Zeile_Bezeichnung\" valign=\"top\">");
            echo("Kommentar:");
            echo("</td>\n");
            echo("<td>");
            if(!isset($_POST['kommentar'][$datensatz['pky_Bewerber']]) AND $datensatz['Auswahlgespraech'] == 1)
            {$value = $datensatz['Auswahlgespraech_Kommentar'];}
            elseif(isset($_POST['kommentar'][$datensatz['pky_Bewerber']]))
            {$value = htmlXspecialchars(trim($_POST['kommentar'][$datensatz['pky_Bewerber']]));}
            else
            {$value = "";}
            echo("<textarea name=\"kommentar[".$datensatz['pky_Bewerber']."]\" cols=\"60\" rows=\"5\">".$value."</textarea>");
            echo("</td>\n");
            echo("</tr>\n");
        }

        echo("</table>\n");
    }
    mysqli_free_result($ergebnis_bewerber);

    ##############
    ### Submit ###
    ##############

    echo("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;\">\n");
    //Leerzeile mit Linie
    echo("<tr>\n");
    echo("<td style=\"height:30px; border-bottom:1px solid #6A6A6A;\" valign=\"bottom\">");
    echo("<span style=\"font-size:10pt;\">Die mit einem Stern (*) gekennzeichneten Felder m&uuml;ssen ausgef&uuml;llt werden!</span>");
    echo("</td>\n");
    echo("</tr>\n");
    //Submit Button
    echo("<tr>\n");
    echo("<td>");
    //Versteckte Eingabefelder
    echo("<input type=\"hidden\" name=\"aktion\" value=\"auswahlgespraech\">\n");
    echo("<input type=\"hidden\" name=\"auswahl_string\" value=\"".$auswahl_string."\">\n");
    echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"senden\" value=\">> Daten eintragen/&auml;ndern\">\n");
    echo("</td>\n");
    echo("</tr>\n");
    echo("</table><br />\n");

    echo("</form>\n");
}

###################################
### &AUML;NDERUNGEN IN DER DATENBANK ###
###################################

if(isset($_POST['senden']) AND !isset($warnung))
{
    foreach($auswahl_array AS $pky_bewerber)
    {
        //Wenn bei einem Bewerber Angaben zum Auswahlgespr&auml;ch nicht m&ouml;glich sind, dann werden diese auch nicht ge&auml;ndert
        if(isset($_POST['angabe_nicht_moeglich'][$pky_bewerber]))
        {
            echo("<div class=\"Information_Warnung\">\n");
            echo("<b>Angaben zum Auswahlgespr&auml;ch sind bei Bewerber ".name_bewerber($link, $pky_bewerber, 2)." nicht m&ouml;glich!</b>");
            echo("</div>\n");
        }
        else
        {
            //WENN DER BEWERBER NICHT ERSCHIENEN IST
            //dann werden eventuell gemachte Angaben ignoriert
            if(isset($_POST['bewerber_erschienen'][$pky_bewerber]) AND $_POST['bewerber_erschienen'][$pky_bewerber] == "0")
            {
                //&AUML;nderung ODER Neueintrag in der Tabelle "auswahlgespraech_bewerber"
                if($_POST['auswahlgespraech'][$pky_bewerber] == "1")
                {
                    //UPDATE
                    $sql = "UPDATE auswahlgespraech_bewerber
                            SET
                                Erschienen = 0,
                                Auswahlgespraech_Datum = NULL,
                                Auswahlgespraech_Uhrzeit_von = NULL,
                                Auswahlgespraech_Uhrzeit_bis = NULL,
                                Auswahlgespraech_Kommentar = '".addslashes(htmlXspecialchars(trim($_POST['kommentar'][$pky_bewerber])))."'
                            WHERE
                                fky_Bewerber = ".$pky_bewerber.";";
                    mysqli_query($link, $sql) OR die(mysqli_error($link));
                }
                else
                {
                    //INSERT
                    $sql = "INSERT INTO auswahlgespraech_bewerber
                               (fky_Bewerber,
                                Erschienen,
                                Auswahlgespraech_Kommentar)
                            VALUES
                                (".$pky_bewerber.",
                                 0,
                                 '".addslashes(htmlXspecialchars(trim($_POST['kommentar'][$pky_bewerber])))."');";
                    mysqli_query($link, $sql) OR die(mysqli_error($link));
                }

                //Die Leistungen des Bewerbers im Auswahlgespr&auml;ch festlegen
                $fachkompetenz = 0;
                $sozialkompetenz = 0;
                $auswahlgespraech_summe = 0;
                //Endsumme festlegen
                //Wenn bei einem nicht erschienenen Bewerber die Zwischensumme nicht berechenbar ist, ist die Endsumme automatisch "0"
                if($_POST['zwischensumme'][$pky_bewerber] == "")
                {$endsumme = 0;}
                //Wenn bei einem nicht erschienenen Bewerber die Zwischensumme berechenbar ist, ist die Endsumme gleich der Zwischensumme
                else
                {$endsumme = $_POST['zwischensumme'][$pky_bewerber];}
            }

            //WENN DER BEWERBER ERSCHIENEN IST
            if(isset($_POST['bewerber_erschienen'][$pky_bewerber]) AND $_POST['bewerber_erschienen'][$pky_bewerber] == "1")
            {
                //&AUML;nderung ODER Neueintrag in der Tabelle "auswahlgespraech_bewerber"
                if($_POST['auswahlgespraech'][$pky_bewerber] == "1")
                {
                    //UPDATE
                    $sql = "UPDATE auswahlgespraech_bewerber
                            SET
                                Erschienen = 1,
                                Auswahlgespraech_Datum = '".datum_d_dbdate(trim($_POST['datum'][$pky_bewerber]))."',
                                Auswahlgespraech_Uhrzeit_von = '".trim($_POST['uhrzeit_von'][$pky_bewerber])."',
                                Auswahlgespraech_Uhrzeit_bis = '".trim($_POST['uhrzeit_bis'][$pky_bewerber])."',
                                Auswahlgespraech_Kommentar = '".addslashes(htmlXspecialchars(trim($_POST['kommentar'][$pky_bewerber])))."'
                            WHERE
                                fky_Bewerber = ".$pky_bewerber.";";
                    mysqli_query($link, $sql) OR die(mysqli_error($link));
                }
                else
                {
                    //INSERT
                    $sql = "INSERT INTO auswahlgespraech_bewerber
                               (fky_Bewerber,
                                Erschienen,
                                Auswahlgespraech_Datum,
                                Auswahlgespraech_Uhrzeit_von,
                                Auswahlgespraech_Uhrzeit_bis,
                                Auswahlgespraech_Kommentar)
                            VALUES
                                (".$pky_bewerber.",
                                 1,
                                 '".datum_d_dbdate(trim($_POST['datum'][$pky_bewerber]))."',
                                 '".trim($_POST['uhrzeit_von'][$pky_bewerber])."',
                                 '".trim($_POST['uhrzeit_bis'][$pky_bewerber])."',
                                 '".addslashes(htmlXspecialchars(trim($_POST['kommentar'][$pky_bewerber])))."');";
                    mysqli_query($link, $sql) OR die(mysqli_error($link));
                }

                //Die Leistungen des Bewerbers im Auswahlgespr&auml;ch festlegen
                $fachkompetenz = (int)trim($_POST['fachkompetenz'][$pky_bewerber]);
                $sozialkompetenz = (int)trim($_POST['sozialkompetenz'][$pky_bewerber]);
                $auswahlgespraech_summe = ($fachkompetenz + $sozialkompetenz) / 2;
                //Endsumme festlegen
                //Wenn bei einem Bewerber die Endsumme manuell eingegeben wurde (bei Zwischensumme nicht berechenbar oder bei Zwischensumme nicht erreicht)
                if(isset($_POST['endsumme'][$pky_bewerber]) AND ($_POST['zwischensumme'][$pky_bewerber] == "" OR $_POST['zwischensumme'][$pky_bewerber] < GRENZE_ZWISCHENSUMME))
                {
                    $endsumme = trim($_POST['endsumme'][$pky_bewerber]);
                }
                //Wenn keine Endsumme &uuml;bergeben wurde (d.h. die Zwischensumme ist berechenbar und wurde erreicht)
                //In diesem Fall kann die Endsumme nur eine glatte Zahl ergeben
                else
                {
                    $endsumme = (int)$_POST['zwischensumme'][$pky_bewerber] + ($auswahlgespraech_summe * 2);
                }
            }

            //UPDATE
            $sql = "UPDATE leistungen_bewerber
                    SET
                        Auswahlgespraech = 1,
                        Fachkompetenz = ".$fachkompetenz.",
                        Sozialkompetenz = ".$sozialkompetenz.",
                        Auswahlgespraech_Summe = ".$auswahlgespraech_summe.",
                        Endsumme = ".$endsumme."
                    WHERE
                        fky_Bewerber = ".$pky_bewerber.";";
            mysqli_query($link, $sql) OR die(mysqli_error($link));

            //Bei erfolgreicher &AUML;nderung erscheint ein entsprechender Hinweis
            echo("<div class=\"Information\">\n");
            echo("<b>Die Angaben zum Auswahlgespr&auml;ch von Bewerber ".name_bewerber($link, $pky_bewerber, 2)." wurden erfolgreich");
            if($_POST['auswahlgespraech'][$pky_bewerber] == "1")
            {echo(" ge&auml;ndert!</b><br />");}
            else
            {echo(" eingetragen!</b><br />");}
            echo("Die Endsumme betr&auml;gt ".$endsumme." Punkte.");
            echo("</div>\n");
        }
    }
    echo("<br />\n");
}
?>

<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1"><a href="index.php?seite=intern_admin&intern_a=bewerber_einsehen">zur&uuml;ck zur Verwaltung f&uuml;r Bewerber</a></span></span><br/>
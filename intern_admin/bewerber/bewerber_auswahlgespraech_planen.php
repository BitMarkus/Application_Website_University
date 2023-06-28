<div class="h1">Auswahlgespr&auml;ch planen</div>

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
            //Wenn das Datum des Termins f&uuml;r das Auswahlgespr&auml;chs nicht eingetragen wurde
            if(!datum_regex(trim($_POST['datum'][$pky_bewerber])))
            {
                $warnung[$pky_bewerber]['datum'] = "Es wurde kein oder ein ung&uuml;ltiges Datum eingetragen!";
            }
            //Wenn die Uhrzeit des Auswahlgespr&auml;chs nicht eingetragen wurde
            if(!uhrzeit_check(trim($_POST['uhrzeit'][$pky_bewerber])))
            {
                $warnung[$pky_bewerber]['uhrzeit'] = "Es wurden keine oder eine ung&uuml;ltige Uhrzeit f&uuml;r das Auswahlgespr&auml;ch eingetragen!";
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
                b.Key_Aktivierung,
                b.Account_gesperrt,
                b.Bewerbung_zurueckgezogen,
                lb.Zwischensumme,
                lb.Auswahlgespraech,
                lb.Endsumme,
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

        //////////////////////////////////////////////////////////////////////
        /////////////// Formularfelder f&uuml;r das Auswahlgespr&auml;ch ///////////////
        //////////////////////////////////////////////////////////////////////

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
            //&UUML;berschrift
            echo("<tr>\n");
            echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
            //Wenn f&uuml;r den Bewerber schon Angaben zum Auswahlgespr&auml;ch eingetragen wurden, dann erfolgt ein Warnhinweis
            if($datensatz['Auswahlgespraech'] == 1)
            {echo("<span style=\"color:red;float:right;\">(Achtung: Bewerber war bereits im Auswahlgespr&auml;ch!)</span>");}
            echo("Termin f&uuml;r das Auswahlgespr&auml;ch ");
            if($datensatz['Datum_Termin'] != NULL)
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

            //Termin f&uuml;r das Datum des Auswahlgespr&auml;chs
            $zeile++;
            if(isset($warnung[$datensatz['pky_Bewerber']]['datum']))
            {$ergebnis_check = false;}else{$ergebnis_check = true;}
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\" style=\"width:20%;\">");
            echo("Datum: *");
            echo("</td>\n");
            echo("<td>");
            if(!isset($_POST['datum'][$datensatz['pky_Bewerber']]) AND $datensatz['Datum_Termin'] != NULL)
            {$value = datum_dbdate_d($datensatz['Datum_Termin']);}
            elseif(isset($_POST['datum'][$datensatz['pky_Bewerber']]))
            {$value = htmlXspecialchars(trim($_POST['datum'][$datensatz['pky_Bewerber']]));}
            else
            {$value = "";}
            echo("<input name=\"datum[".$datensatz['pky_Bewerber']."]\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".$value."\">");
            echo(" <span style=\"font-size:10pt;\"> (Bitte im Format <b>(T)T:(M)M:JJJJ</b> eingeben)</span>");
            //Information versteckt weitergeben, ob f&uuml;r den Bewerber schon ein Eintrag zum Auswahlgespr&auml;ch gemacht wurde
            echo("<input type=\"hidden\" name=\"eintrag_vorhanden[".$datensatz['pky_Bewerber']."]\" value=\"".$datensatz['Datum_Termin']."\">\n");
            echo("</td>\n");
            echo("</tr>\n");

            //Uhrzeit des Auswahlgespr&auml;chs
            $zeile++;
            if(isset($warnung[$datensatz['pky_Bewerber']]['uhrzeit']) OR isset($warnung[$datensatz['pky_Bewerber']]['uhrzeit']))
            {$ergebnis_check = false;}else{$ergebnis_check = true;}
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Uhrzeit: *");
            echo("</td>\n");
            echo("<td>");
            if(!isset($_POST['uhrzeit'][$datensatz['pky_Bewerber']]) AND $datensatz['Uhrzeit_Termin'] != NULL)
            {$value = cut_sec($datensatz['Uhrzeit_Termin']);}
            elseif(isset($_POST['uhrzeit'][$datensatz['pky_Bewerber']]))
            {$value = htmlXspecialchars(trim($_POST['uhrzeit'][$datensatz['pky_Bewerber']]));}
            else
            {$value = "";}
            echo("<input name=\"uhrzeit[".$datensatz['pky_Bewerber']."]\" type=\"text\" size=\"5\" maxlength=\"5\" value=\"".$value."\"> Uhr");
            echo(" <span style=\"font-size:10pt;\"> (Bitte im Format <b>(H)H:MM</b> eingeben)</span>");
            echo("</td>\n");
            echo("</tr>\n");

            //&UUML;berschrift
            echo("<tr>\n");
            echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
            echo("Kommission f&uuml;r das Auswahlgespr&auml;ch ");
            if($datensatz['Datum_Termin'] != NULL)
            {echo("&auml;ndern");}
            else
            {echo("eintragen");}
            echo("</td>\n");
            echo("</tr>\n");

            //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
            $zeile = 0;

            //Kommissionsmitglieder
            for($i=1; $i<=4; $i++)
            {
                $zeile++;
                echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
                echo("<td class=\"Zeile_Bezeichnung\">");
                echo("Kommissionsmitglied ".$i.":");
                echo("</td>\n");
                echo("<td>");
                echo("<select name=\"kommissionsmitglied_".$i."[".$datensatz['pky_Bewerber']."]\" class=\"Auswahlfeld\" size=\"1\">\n");
                if(!isset($_POST['kommissionsmitglied_'.$i][$datensatz['pky_Bewerber']]) AND $datensatz['fky_Kommissionsmitglied_'.$i] === 0)
                {$selected = " selected=\"selected\"";}
                elseif(isset($_POST['kommissionsmitglied_'.$i][$datensatz['pky_Bewerber']]) AND $_POST['kommissionsmitglied_'.$i][$datensatz['pky_Bewerber']] == "0")
                {$selected = " selected=\"selected\"";}
                else
                {$selected = "";}
                echo("<option".$selected." value=\"0\">Bitte w&auml;hlen</option>\n");
                $kommissionsmitglieder = kommissionsmitglieder($link);
                while($row = mysqli_fetch_assoc($kommissionsmitglieder))
                {
                    if(!isset($_POST['kommissionsmitglied_'.$i][$datensatz['pky_Bewerber']]) AND $datensatz['fky_Kommissionsmitglied_'.$i] == $row['pky_Kommissionsmitglied'])
                    {$selected = " selected=\"selected\"";}
                    elseif(isset($_POST['kommissionsmitglied_'.$i][$datensatz['pky_Bewerber']]) AND $_POST['kommissionsmitglied_'.$i][$datensatz['pky_Bewerber']] == $row['pky_Kommissionsmitglied'])
                    {$selected = " selected=\"selected\"";}
                    else
                    {$selected = "";}
                    echo("<option".$selected." value=\"".$row['pky_Kommissionsmitglied']."\">".$row['Kommissionsmitglied']."</option>\n");
                }
                mysqli_free_result($kommissionsmitglieder);
                echo("</select>");
                echo("</td>\n");
                echo("</tr>\n");
            }
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
    echo("<input type=\"hidden\" name=\"aktion\" value=\"auswahlgespraech_planen\">\n");
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
            //INSERT
            if(isset($_POST['eintrag_vorhanden'][$pky_bewerber]) AND trim($_POST['eintrag_vorhanden'][$pky_bewerber]) == "")
            {
                $sql = "INSERT INTO termin_kommission_bewerber
                           (fky_Bewerber,
                            Datum_Termin,
                            Uhrzeit_Termin,
                            fky_Kommissionsmitglied_1,
                            fky_Kommissionsmitglied_2,
                            fky_Kommissionsmitglied_3,
                            fky_Kommissionsmitglied_4)
                        VALUES
                           (".$pky_bewerber.",
                            '".datum_d_dbdate(trim($_POST['datum'][$pky_bewerber]))."',
                            '".trim($_POST['uhrzeit'][$pky_bewerber])."',
                            ".$_POST['kommissionsmitglied_1'][$pky_bewerber].",
                            ".$_POST['kommissionsmitglied_2'][$pky_bewerber].",
                            ".$_POST['kommissionsmitglied_3'][$pky_bewerber].",
                            ".$_POST['kommissionsmitglied_4'][$pky_bewerber].");";
                mysqli_query($link, $sql) OR die(mysqli_error($link));

                //Bei erfolgreichem Eintrag erscheint ein entsprechender Hinweis
                echo("<div class=\"Information\">\n");
                echo("<b>Die Angaben zum Auswahlgespr&auml;ch von Bewerber ".name_bewerber($link, $pky_bewerber, 2)." wurden erfolgreich eingetragen!</b>");
                echo("</div>\n");
            }

            //UPDATE
            if(isset($_POST['eintrag_vorhanden'][$pky_bewerber]) AND trim($_POST['eintrag_vorhanden'][$pky_bewerber]) != "")
            {
                $sql = "UPDATE termin_kommission_bewerber
                        SET
                            Datum_Termin = '".datum_d_dbdate(trim($_POST['datum'][$pky_bewerber]))."',
                            Uhrzeit_Termin = '".trim($_POST['uhrzeit'][$pky_bewerber])."',
                            fky_Kommissionsmitglied_1 = ".$_POST['kommissionsmitglied_1'][$pky_bewerber].",
                            fky_Kommissionsmitglied_2 = ".$_POST['kommissionsmitglied_2'][$pky_bewerber].",
                            fky_Kommissionsmitglied_3 = ".$_POST['kommissionsmitglied_3'][$pky_bewerber].",
                            fky_Kommissionsmitglied_4 = ".$_POST['kommissionsmitglied_4'][$pky_bewerber]."
                        WHERE
                            fky_Bewerber = ".$pky_bewerber.";";
                mysqli_query($link, $sql) OR die(mysqli_error($link));

                //Bei erfolgreicher &AUML;nderung erscheint ein entsprechender Hinweis
                echo("<div class=\"Information\">\n");
                echo("<b>Die Angaben zum Auswahlgespr&auml;ch von Bewerber ".name_bewerber($link, $pky_bewerber, 2)." wurden erfolgreich ge&auml;ndert!</b>");
                echo("</div>\n");
            }
        }
    }
    echo("<br />\n");
}
?>

<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1"><a href="index.php?seite=intern_admin&intern_a=bewerber_einsehen">zur&uuml;ck zur Verwaltung f&uuml;r Bewerber</a></span></span><br/>
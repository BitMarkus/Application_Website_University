<div class="h1">Daten der Bewerbung &auml;ndern</div>

<?php
#################################
# DATEN IN DER DATENBANK &AUML;NDERN #
#################################

if(isset($_POST['aendern']))
{
    //Wenn f&uuml;r die Art der HZB nicht "Sonstige" gew&auml;hlt wurde, aber im Textfeld zur Spezifizierung trotzdem etwas eingetragen wurde
    //dann wird dieser Eintrag gel&ouml;scht
    if($_POST['pky_hzb'] != PKY_SONST_HZB AND trim($_POST['hzb_sonstige']) != "")
    {$_POST['hzb_sonstige'] = "";}

    //Angabe, ob ein soziales Jahr geleistet wurde
    //Nein = 0, Ja = 1
    if(isset($_POST['soz_jahr']) AND $_POST['soz_jahr'] == "1")
    {$soziales_jahr = 1;}
    else
    {$soziales_jahr = 0;}

    //Daten in der Tabelle "bewerber" &auml;ndern
    $sql = "UPDATE bewerber
            SET
                Anrede = '".$_POST['anrede']."',
                Nachname = '".addslashes(htmlXspecialchars(trim($_POST['nachname'])))."',
                Vorname = '".addslashes(htmlXspecialchars(trim($_POST['vorname'])))."',
                Geburtsdatum = '".datum_d_dbdate(trim($_POST['geburtsdatum']))."',
                Email = '".addslashes(htmlXspecialchars(trim($_POST['email'])))."',
                Nationalitaet_fky_Land = '".$_POST['nationalitaet_pky_land']."',
                Strasse = '".addslashes(htmlXspecialchars(trim($_POST['strasse'])))."',
                Hausnummer = '".addslashes(htmlXspecialchars(trim($_POST['hausnr'])))."',
                Adresszusatz = '".addslashes(htmlXspecialchars(trim($_POST['adresszusatz'])))."',
                Postleitzahl = '".addslashes(htmlXspecialchars(trim($_POST['plz'])))."',
                Wohnort = '".addslashes(htmlXspecialchars(trim($_POST['ort'])))."',
                fky_Land = '".$_POST['pky_land']."',
                Datum_Aenderung = NOW(),
                fky_HZB = '".$_POST['pky_hzb']."',
                HZB_Sonstige = '".addslashes(htmlXspecialchars(trim($_POST['hzb_sonstige'])))."',
                HZB_Jahr = '".trim($_POST['hzb_jahr'])."',
                HZB_Ort = '".addslashes(htmlXspecialchars(trim($_POST['hzb_ort'])))."',
                HZB_fky_Land = '".$_POST['hzb_pky_land']."',
                Soziales_Jahr = '".$soziales_jahr."',
                fky_Ausbildung = '".$_POST['pky_ausbildung']."',
                Begruendung = '".addslashes(htmlXspecialchars(trim($_POST['begruendung'])))."'
            WHERE
                pky_Bewerber = ".$_SESSION['SESSION_PKY_BEWERBER'].";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));

    //Gegebenenfalls das Passwort &auml;ndern
    if($_POST['passwort_1'] != "")
    {
        $sql = "UPDATE bewerber
                SET
                    Passwort = MD5('".$_POST['passwort_1']."')
                WHERE
                    pky_Bewerber = ".$_SESSION['SESSION_PKY_BEWERBER'].";";
        mysqli_query($link, $sql) OR die(mysqli_error($link));
    }

    //Noten in Punkte bzw. Punkte in Noten umrechnen und ins Datenbankformat bringen
    //Die Daten im Array "$leistung" speichern
    //Wenn bei den Noten kein Eintrag gemacht wurde, wird "NULL" in die Datenbank eingetragen
    //Wenn bei einem Fach angegeben wurde, dass es nicht belegt wurde (Variable $_POST['naturw_belegt'] oder $_POST['mathe_belegt'] nicht vorhanden),
    //dann werden dort auch keine Noten eingetragen (sondern NULL), unabh&auml;ngig davon, ob die entsprechenden Felder ausgef&uuml;llt wurden
    $leistung = array();
    foreach($_POST['leistungen'] AS $fach => $arr_fach)
    {
        foreach($arr_fach AS $hj => $leistung_hj)
        {
            if(trim($leistung_hj) != "" AND !isset($_POST[$fach.'_belegt']))
            {
                if($_POST['leistungen_art'] == "p")
                {
                    $leistung[$fach]['punkte'][$hj] = (float)trim($leistung_hj);
                    $leistung[$fach]['note'][$hj] = punkte_in_noten(trim($leistung_hj), "en");
                }
                if($_POST['leistungen_art'] == "n")
                {
                    $leistung[$fach]['note'][$hj] = (float)float_d_e(trim($leistung_hj));
                    $leistung[$fach]['punkte'][$hj] = noten_in_punkte(trim($leistung_hj), "de");
                }
            }
            else
            {
                $leistung[$fach]['punkte'][$hj] = 'NULL';
                $leistung[$fach]['note'][$hj] = 'NULL';
            }
        }
    }
    //Noten und Punkte f&uuml;r die HZB
    $leistung['hzb_note'] = (float)float_d_e(trim($_POST['hzb_note']));
    $leistung['hzb_punkte'] = noten_in_punkte(trim($_POST['hzb_note']), "de");

    //Eintr&auml;ge, ob die jeweiligen F&auml;cher belegt wurden oder nicht
    //nicht belegt = 0, belegt = 1
    if(!isset($_POST['naturw_belegt']))
    {$nat_belegt = 1;}
    else
    {$nat_belegt = 0;}
    if(!isset($_POST['mathe_belegt']))
    {$mat_belegt = 1;}
    else
    {$mat_belegt = 0;}

    //Wenn angegeben wurde, dass keine Naturwissenschaft belegt wurde, dann wird der Fky des naturw. Fachs auf den Wert NULL gesetzt
    if(!isset($_POST['naturw_belegt']))
    {$pky_naturw_fach = $_POST['pky_naturw_fach'];}
    else
    {$pky_naturw_fach = 'NULL';}

    //Im Falle, dass der Bewerber keine Mathemathik und kein naturw. Fach belegt hat, muss auch die Art der Leistungen nicht angegeben werden
    //Wenn er trotzdem eine Angabe gemacht hat, wird auch ein Leerstring eingetragen
    //In diesem Fall wird ein Leerstring "Leistungen_Art" in das Feld "" geschrieben
    if(!isset($_POST['leistungen_art']) OR (isset($_POST['naturw_belegt']) AND isset($_POST['mathe_belegt'])))
    {$leist_art = "";}
    else
    {$leist_art = $_POST['leistungen_art'];}

    //Die Zwischensumme &uuml;ber eine Funktion berechnen
    $zwischensumme = zwischensumme($leistung, $mat_belegt, $nat_belegt, $soziales_jahr, $_POST['pky_ausbildung']);

    //Daten in der Tabelle "leistungen_bewerber" &auml;ndern
    $sql = "UPDATE leistungen_bewerber
            SET
                Leistungen_Art = '".$leist_art."',
                HZB_Note = ".$leistung['hzb_note'].",
                HZB_Punkte = ".$leistung['hzb_punkte'].",
                Naturw_belegt = ".$nat_belegt.",
                fky_Naturw_Fach = ".$pky_naturw_fach.",
                Naturw_HJ_1_Note = ".$leistung['naturw']['note']['hj1'].",
                Naturw_HJ_2_Note = ".$leistung['naturw']['note']['hj2'].",
                Naturw_HJ_3_Note = ".$leistung['naturw']['note']['hj3'].",
                Naturw_HJ_4_Note = ".$leistung['naturw']['note']['hj4'].",
                Naturw_End_Note = ".$leistung['naturw']['note']['abi'].",
                Naturw_HJ_1_Punkte = ".$leistung['naturw']['punkte']['hj1'].",
                Naturw_HJ_2_Punkte = ".$leistung['naturw']['punkte']['hj2'].",
                Naturw_HJ_3_Punkte = ".$leistung['naturw']['punkte']['hj3'].",
                Naturw_HJ_4_Punkte = ".$leistung['naturw']['punkte']['hj4'].",
                Naturw_End_Punkte = ".$leistung['naturw']['punkte']['abi'].",
                Mathe_belegt = ".$mat_belegt.",
                Mathe_HJ_1_Note = ".$leistung['mathe']['note']['hj1'].",
                Mathe_HJ_2_Note = ".$leistung['mathe']['note']['hj2'].",
                Mathe_HJ_3_Note = ".$leistung['mathe']['note']['hj3'].",
                Mathe_HJ_4_Note = ".$leistung['mathe']['note']['hj4'].",
                Mathe_End_Note = ".$leistung['mathe']['note']['abi'].",
                Mathe_HJ_1_Punkte = ".$leistung['mathe']['punkte']['hj1'].",
                Mathe_HJ_2_Punkte = ".$leistung['mathe']['punkte']['hj2'].",
                Mathe_HJ_3_Punkte = ".$leistung['mathe']['punkte']['hj3'].",
                Mathe_HJ_4_Punkte = ".$leistung['mathe']['punkte']['hj4'].",
                Mathe_End_Punkte = ".$leistung['mathe']['punkte']['abi'].",
                Zwischensumme = ".$zwischensumme."
            WHERE
                fky_Bewerber = ".$_SESSION['SESSION_PKY_BEWERBER'].";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));

    //Die alten Eintr&auml;ge des Bewerbers in der Tabelle "lebenslauf_bewerber" l&ouml;schen
    $sql = "DELETE
            FROM
                lebenslauf_bewerber
            WHERE
                fky_Bewerber = ".$_SESSION['SESSION_PKY_BEWERBER'].";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));

    //Neue Eintr&auml;ge in die Tabelle "lebenslauf_bewerber"
    $nr_eintrag = 1;
    foreach($_POST['lebenslauf'] AS $array_eintrag)
    {
        //Leere Zeilen l&ouml;schen
        if(trim($array_eintrag['am_von_ll']) != "" AND trim($array_eintrag['text_ll']) != "")
        {
            $sql = "INSERT INTO lebenslauf_bewerber
                       (fky_Bewerber,
                        Nr_Eintrag,
                        Datum_am_von,
                        Datum_bis,
                        Eintrag)
                    VALUES
                       ('".$_SESSION['SESSION_PKY_BEWERBER']."',
                        '".$nr_eintrag."',
                        '".addslashes(htmlXspecialchars(trim($array_eintrag['am_von_ll'])))."',
                        '".addslashes(htmlXspecialchars(trim($array_eintrag['bis_ll'])))."',
                        '".addslashes(htmlXspecialchars(trim($array_eintrag['text_ll'])))."');";
            mysqli_query($link, $sql) OR die(mysqli_error($link));
            $nr_eintrag++;
        }
    }

    //Bei erfolgreicher &AUML;nderung erscheint ein entsprechender Hinweis
    echo("<div class=\"Information\">\n");
    echo("<b>Ihre Daten wurden erfolgreich ge&auml;ndert!</b>");

    ##############################################################################################################################################
    //Ausgabe der Zwischensumme zu Testzwecken
    //if(isset($zwischensumme)){echo("<br />Die Zwischensumme ist: <b>".($zwischensumme == "NULL" ? "nicht berechenbar!" : $zwischensumme)."</b>");}
    ##############################################################################################################################################

    echo("</div>\n");
    echo("<div class=\"Abstandhalter_Div\"></div>\n");
}

##################
# FORMULAR START #
##################

else
{
    ##################################################
    # Daten des Benutzers aus der Datenbank auslesen #
    ##################################################
    //Ist nur beim allerersten Aufruf des Seite notwendig
    //Das ist u.a. der Fall, wenn die $_POST Variable "oldpage" noch nicht existiert

    if(!isset($_POST['oldpage']))
    {
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
                    Begruendung
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
    }

    ####################################################
    # Herausfinden, welche Seite angezeigt werden soll #
    ####################################################

    //Wenn &uuml;ber die Submit-Buttons "weiter" oder "zur&uuml;ck" navigiert wird
    if((!isset($_POST['oldpage'])) OR (!ctype_digit($_POST['oldpage'])) OR ($_POST['oldpage'] < 1) OR ($_POST['oldpage'] > SEITENANZAHL))
    {
        $_POST['oldpage'] = '1';
    }

    if(isset($_POST['vor']))
    {
        $active_page = min($_POST['oldpage'] + 1, SEITENANZAHL);
    }
    elseif(isset($_POST['zurueck']))
    {
        $active_page = max($_POST['oldpage'] - 1, 1);
    }
    else
    {
        $active_page = $_POST['oldpage'];
    }
    //Wenn &uuml;ber die Reiter-Navigation navigiert wird
    for($i=1; $i<=SEITENANZAHL; $i++)
    {
        if(isset($_POST['seite'.$i]))
        {
            $active_page = $i;
            break;
        }
    }

    //Hinweise zur &AUML;nderung
    echo("<div class=\"Information\">\n");
    echo("<b>Eine &Auml;nderung Ihrer Daten ist nur bis zum Anmeldeschluss m&ouml;glich. Dies ist der 15. Juli des aktuellen Jahres. ");
    echo("Danach ist es Ihnen bis zur folgenden Bewerbungsperiode im n&auml;chsten Jahr nicht mehr m&ouml;glich, sich im internen Bereich f&uuml;r Bewerber anzumelden.</b>");
    echo("</div><br />\n");

    ###################################
    # Formular und Haupttabelle Start #
    ###################################

    echo("<form method=\"post\" action=\"index.php?seite=intern_bewerber&intern_b=ib_aendern\">\n");
    echo("<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" id=\"Tabelle_Anmeldung\">\n");

    //Reiter
    echo("<tr>\n");
    if($active_page == 1)
    {$class_reiter = "Reiter_aktiv"; $class_button = "Reiter_Buttons_aktiv";}
    else
    {$class_reiter = "Reiter_inaktiv"; $class_button = "Reiter_Buttons_inaktiv";}
    echo("<td class=\"".$class_reiter."\">");
    echo("<input class=\"".$class_button."\" type=\"submit\" name=\"seite1\" value=\"Pers&ouml;nl. Angaben\">");
    echo("</td>\n");
    if($active_page == 2)
    {$class_reiter = "Reiter_aktiv"; $class_button = "Reiter_Buttons_aktiv";}
    else
    {$class_reiter = "Reiter_inaktiv"; $class_button = "Reiter_Buttons_inaktiv";}
    echo("<td class=\"".$class_reiter."\">");
    echo("<input class=\"".$class_button."\" type=\"submit\" name=\"seite2\" value=\"Schulabschluss\">");
    echo("</td>\n");
    if($active_page == 3)
    {$class_reiter = "Reiter_aktiv"; $class_button = "Reiter_Buttons_aktiv";}
    else
    {$class_reiter = "Reiter_inaktiv"; $class_button = "Reiter_Buttons_inaktiv";}
    echo("<td class=\"".$class_reiter."\">");
    echo("<input class=\"".$class_button."\" type=\"submit\" name=\"seite3\" value=\"Leistungen\">");
    echo("</td>\n");
    if($active_page == 4)
    {$class_reiter = "Reiter_aktiv"; $class_button = "Reiter_Buttons_aktiv";}
    else
    {$class_reiter = "Reiter_inaktiv"; $class_button = "Reiter_Buttons_inaktiv";}
    echo("<td class=\"".$class_reiter."\">");
    echo("<input class=\"".$class_button."\" type=\"submit\" name=\"seite4\" value=\"Lebenslauf\">");
    echo("</td>\n");
    if($active_page == 5)
    {$class_reiter = "Reiter_aktiv_letzte"; $class_button = "Reiter_Buttons_aktiv";}
    else
    {$class_reiter = "Reiter_inaktiv_letzte"; $class_button = "Reiter_Buttons_inaktiv";}
    echo("<td class=\"".$class_reiter."\">");
    echo("<input class=\"".$class_button."\" type=\"submit\" name=\"seite5\" value=\"Zusammenfassung\">");
    echo("</td>\n");
    echo("</tr>\n");

    echo("<tr>\n");
    echo("<td colspan=\"5\" class=\"Formular_Anmeldung\">\n");

    ##############################################################################################################################
    ##############################################################################################################################
    echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" style=\"width:100%;\">\n");

    ##########################################
    # Formularfelder f&uuml;r pers&ouml;nliche Angaben #
    ##########################################

    //Infozeile
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Infozeile\">");
    echo("Angabe von <b>pers&ouml;nlichen Daten</b>");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Anrede
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td style=\"width:21%;\" class=\"Zeile_Bezeichnung\">");
    echo("Anrede: *");
    echo("</td>\n");
    echo("<td>");
    if((!isset($_POST['anrede']) AND $daten_bewerber['Anrede'] == "f") OR (isset($_POST['anrede']) AND $_POST['anrede'] == "f"))
    {$check_anrede = "checked=\"checked\"";}
    else
    {$check_anrede = "";}
    echo("Frau: <input name=\"anrede\" type=\"radio\" value=\"f\" ".$check_anrede."> ");
    if((!isset($_POST['anrede']) AND $daten_bewerber['Anrede'] == "h") OR (isset($_POST['anrede']) AND $_POST['anrede'] == "h"))
    {$check_anrede = "checked=\"checked\"";}
    else
    {$check_anrede = "";}
    echo("Herr: <input name=\"anrede\" type=\"radio\" value=\"h\" ".$check_anrede.">");
    echo("</td>\n");
    echo("</tr>\n");

    //Nachname
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Nachname: *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"nachname\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['nachname'])) ? $_POST['nachname'] : $daten_bewerber['Nachname']))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Vorname
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Vorname: *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"vorname\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['vorname'])) ? $_POST['vorname'] : $daten_bewerber['Vorname']))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Geburtsdatum
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Geburtsdatum: *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"geburtsdatum\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".htmlXspecialchars(trim((isset($_POST['geburtsdatum'])) ? $_POST['geburtsdatum'] : datum_dbdate_d($daten_bewerber['Geburtsdatum'])))."\">");
    echo(" <span style=\"font-size:10pt;\"> (Bitte im Format <b>TT.MM.JJJJ</b> eingeben)</span>");
    echo("</td>\n");
    echo("</tr>\n");

    //Nationalit&auml;t
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Nationalit&auml;t: *");
    echo("</td>\n");
    echo("<td>");
    $nationalitaet = land($link);
    echo("<select name=\"nationalitaet_pky_land\" class=\"Auswahlfeld\" size=\"1\">\n");
    while($row = mysqli_fetch_assoc($nationalitaet))
    {
        if(!isset($_POST['nationalitaet_pky_land']) AND $daten_bewerber['Nationalitaet_fky_Land'] == $row['pky_Land'])
        {$selected = " selected=\"selected\"";}
        elseif(isset($_POST['nationalitaet_pky_land']) AND $_POST['nationalitaet_pky_land'] == $row['pky_Land'])
        {$selected = " selected=\"selected\"";}
        else
        {$selected = "";}
        echo("<option".$selected." value=\"".$row['pky_Land']."\">".$row['Land']."</option>\n");
    }
    mysqli_free_result($nationalitaet);
    echo("</select>");
    echo("</td>\n");
    echo("</tr>\n");

    //Strasse und Hausnummer
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Stra&szlig;e/Nummer: *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"strasse\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['strasse'])) ? $_POST['strasse'] : $daten_bewerber['Strasse']))."\">");
    echo(" <input name=\"hausnr\" type=\"text\" size=\"5\" maxlength=\"10\" value=\"".htmlXspecialchars(trim((isset($_POST['hausnr'])) ? $_POST['hausnr'] : $daten_bewerber['Hausnummer']))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Adresszusatz
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Adresszusatz:");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"adresszusatz\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['adresszusatz'])) ? $_POST['adresszusatz'] : $daten_bewerber['Adresszusatz']))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //PLZ und Wohnort
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("PLZ/Wohnort: *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"plz\" type=\"text\" size=\"5\" maxlength=\"10\" value=\"".htmlXspecialchars(trim((isset($_POST['plz'])) ? $_POST['plz'] : $daten_bewerber['Postleitzahl']))."\">");
    echo(" <input name=\"ort\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['ort'])) ? $_POST['ort'] : $daten_bewerber['Wohnort']))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Land
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Land: *");
    echo("</td>\n");
    echo("<td>");
    $nationalitaet = land($link);
    echo("<select name=\"pky_land\" class=\"Auswahlfeld\" size=\"1\">\n");
    while($row = mysqli_fetch_assoc($nationalitaet))
    {
        if(!isset($_POST['pky_land']) AND $daten_bewerber['fky_Land'] == $row['pky_Land'])
        {$selected = " selected=\"selected\"";}
        elseif(isset($_POST['pky_land']) AND $_POST['pky_land'] == $row['pky_Land'])
        {$selected = " selected=\"selected\"";}
        else
        {$selected = "";}
        echo("<option".$selected." value=\"".$row['pky_Land']."\">".$row['Land']."</option>\n");
    }
    mysqli_free_result($nationalitaet);
    echo("</select>");
    echo("</td>\n");
    echo("</tr>\n");

    //Leerzeile
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Infozeile
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Infozeile\">");
    echo("Angaben zur Anmeldung im <b>internen Bereich</b> (&Auml;nderung des Passworts nur bei Eingabe)");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Email
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Email: *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"email\" type=\"text\" size=\"30\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['email'])) ? $_POST['email'] : $daten_bewerber['Email']))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Passwort alt
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Passwort (alt): *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"passwort_alt\" type=\"password\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars((isset($_POST['passwort_alt'])) ? $_POST['passwort_alt'] : "")."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Passwort (neu)
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Passwort (neu): *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"passwort_1\" type=\"password\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars((isset($_POST['passwort_1'])) ? $_POST['passwort_1'] : "")."\">");
    echo(" <span style=\"font-size:10pt;\"> (Das Passwort muss aus <b>mind. 8 Zeichen</b> bestehen)</span>");
    echo("</td>\n");
    echo("</tr>\n");

    //Passwort (neu, Best&auml;tigung)
    $zeile++;
    echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Passwort (Best&auml;tigung): *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"passwort_2\" type=\"password\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars((isset($_POST['passwort_2'])) ? $_POST['passwort_2'] : "")."\">");
    echo("</td>\n");
    echo("</tr>\n");

    ##########################
    # Formularfelder f&uuml;r HZB #
    ##########################

    //Infozeile
    echo("<tr style=\"".($active_page == 2 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Infozeile\">");
    echo("Allgemeine Angaben zur <b>Hochschulzugangsberechtigung (HZB)</b>");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Art der HZB
    $zeile++;
    echo("<tr style=\"".($active_page == 2 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td style=\"width:21%;\" class=\"Zeile_Bezeichnung\">");
    echo("Art der HZB: *");
    echo("</td>\n");
    echo("<td>");
    $hzb_art = hzb_art($link);
    echo("<select name=\"pky_hzb\" class=\"Auswahlfeld\" size=\"1\">\n");
    while($row = mysqli_fetch_assoc($hzb_art))
    {
        if(!isset($_POST['pky_hzb']) AND $daten_bewerber['fky_HZB'] == $row['pky_HZB'])
        {$selected = " selected=\"selected\"";}
        elseif(isset($_POST['pky_hzb']) AND $_POST['pky_hzb'] == $row['pky_HZB'])
        {$selected = " selected=\"selected\"";}
        else
        {$selected = "";}
        echo("<option".$selected." value=\"".$row['pky_HZB']."\">".$row['HZB']."</option>\n");
    }
    mysqli_free_result($hzb_art);
    echo("</select>");
    echo("</td>\n");
    echo("</tr>\n");

    //HZB sonstige
    $zeile++;
    echo("<tr style=\"".($active_page == 2 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Sonstige HZB: (*)");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"hzb_sonstige\" type=\"text\" size=\"30\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['hzb_sonstige'])) ? $_POST['hzb_sonstige'] : $daten_bewerber['HZB_Sonstige']))."\">");
    echo(" <span style=\"font-size:10pt;\">(Bitte konkretisieren, wenn <b>\"sonstige Hochschulzugangsberechtigung\"</b> gew&auml;hlt wurde)</span>");
    echo("</td>\n");
    echo("</tr>\n");

    //HZB Jahr
    $zeile++;
    echo("<tr style=\"".($active_page == 2 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Jahr der HZB: *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"hzb_jahr\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".htmlXspecialchars(trim((isset($_POST['hzb_jahr'])) ? $_POST['hzb_jahr'] : $daten_bewerber['HZB_Jahr']))."\">");
    echo(" <span style=\"font-size:10pt;\"> (Bitte im Format <b>JJJJ</b> eingeben)</span>");
    echo("</td>\n");
    echo("</tr>\n");

    //HZB Ort
    $zeile++;
    echo("<tr style=\"".($active_page == 2 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Ort der HZB: *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"hzb_ort\" type=\"text\" size=\"30\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['hzb_ort'])) ? $_POST['hzb_ort'] : $daten_bewerber['HZB_Ort']))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //HZB Land
    $zeile++;
    echo("<tr style=\"".($active_page == 2 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Land der HZB: *");
    echo("</td>\n");
    echo("<td>");
    $hzb_land = land($link);
    echo("<select name=\"hzb_pky_land\" class=\"Auswahlfeld\" size=\"1\">\n");
    while($row = mysqli_fetch_assoc($hzb_land))
    {
        if(!isset($_POST['hzb_pky_land']) AND $daten_bewerber['HZB_fky_Land'] == $row['pky_Land'])
        {$selected = " selected=\"selected\"";}
        elseif(isset($_POST['hzb_pky_land']) AND $_POST['hzb_pky_land'] == $row['pky_Land'])
        {$selected = " selected=\"selected\"";}
        else
        {$selected = "";}
        echo("<option".$selected." value=\"".$row['pky_Land']."\">".$row['Land']."</option>\n");
    }
    mysqli_free_result($hzb_land);
    echo("</select>");
    echo("</td>\n");
    echo("</tr>\n");

    //HZB Note
    $zeile++;
    echo("<tr style=\"".($active_page == 2 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Abschlussnote: *");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"hzb_note\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".htmlXspecialchars(trim((isset($_POST['hzb_note'])) ? $_POST['hzb_note'] : float_e_d(clean_num($leistungen_bewerber['HZB_Note'], "en"))))."\">");
    echo(" <span style=\"font-size:10pt;\">(Bitte Angabe im <b>deutschen Schulnotensystem</b>: 1,00 - 4,00)</span>");
    echo("</td>\n");
    echo("</tr>\n");

    #################################
    # Formularfelder f&uuml;r Leistungen #
    #################################

    //Info zu den Angaben
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" style=\"border:1px solid #6A6A6A; color:#6A6A6A; padding:3px;\">");
    echo("<b>Bitte beachten Sie:</b> ");
    echo("Tragen Sie hier bitte die erbrachten Leistungen f&uuml;r das Fach Mathematik und Ihrem besten naturwissenschaftlichen Fach ein. ");
    echo("Ausschlaggebend sind die Leistungen der <b>letzten vier Halbjahre</b> Ihrer schulischen Laufbahn. Wurden Sie in diesen F&auml;chern im Abitur ");
    echo("gepr&uuml;ft, so m&uuml;ssen auch diese Leistungen angegeben werden. Wurde eines dieser F&auml;cher nicht &uuml;ber die vollen vier Halbjahre belegt, sind nur die ");
    echo("belegten Halbjahre auszuf&uuml;llen. F&uuml;r alle belegten Halbjahre m&uuml;ssen die Leistungen angegeben werden. ");
    echo("Hatten Sie in den letzten vier Halbjahren keine Mathemathik bzw. kein naturw. Fach belegt, dann muss dies ");
    echo("durch Best&auml;tigen der Checkbox \"nicht zutreffend\" kenntlich gemacht werden.");
    echo("</td>\n");
    echo("</tr>\n");

    //Leerzeile
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Infozeile
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Infozeile\">");
    echo("<b>Art der Angabe</b> (* Wenn mindestens eines der F&auml;cher teilweise oder &uuml;ber die vollen vier Halbjahre belegt wurde)");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Art der Angabe
    $zeile++;
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td colspan=\"2\">");

    if((!isset($_POST['leistungen_art']) AND isset($leistungen_bewerber['Leistungen_Art']) AND $leistungen_bewerber['Leistungen_Art'] == "p") OR (isset($_POST['leistungen_art']) AND $_POST['leistungen_art'] == "p"))
    {$check_leistung_art = "checked=\"checked\"";}
    else
    {$check_leistung_art = "";}
    echo("<input name=\"leistungen_art\" type=\"radio\" value=\"p\" ".$check_leistung_art."> Punkte (0-15)<br>");

    if((!isset($_POST['leistungen_art']) AND isset($leistungen_bewerber['Leistungen_Art']) AND $leistungen_bewerber['Leistungen_Art'] == "n") OR (isset($_POST['leistungen_art']) AND $_POST['leistungen_art'] == "n"))
    {$check_leistung_art = "checked=\"checked\"";}
    else
    {$check_leistung_art = "";}
    echo("<input name=\"leistungen_art\" type=\"radio\" value=\"n\" ".$check_leistung_art."> deutsches Schulnotensystem (1,00-6,00)");

    echo("</td>\n");
    echo("</tr>\n");

    //Leerzeile mit Linie
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Infozeile
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Infozeile\">");
    echo("Angaben f&uuml;r das Fach <b>Mathematik</b> (*)");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Checkbox f&uuml;r "nicht zutreffend" f&uuml;r Mathematik
    $zeile++;
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td colspan=\"2\">");
    if((!isset($_POST['anrede']) AND $leistungen_bewerber['Mathe_belegt'] == "0") OR (isset($_POST['mathe_belegt'])))
    {$check_mathe = "checked=\"checked\"";}
    else
    {$check_mathe = "";}
    echo("<input name=\"mathe_belegt\" type=\"checkbox\" value=\"0\" ".$check_mathe."> <b>nicht zutreffend</b>");
    echo("</td>\n");
    echo("</tr>\n");

    //Mathemathik alle Halbjahre
    for($x=1; $x<=4; $x++)
    {
        $zeile++;
        echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\" style=\"width:15%;\">");
        echo("".$x.". Halbjahr:");
        echo("</td>\n");
        echo("<td>");
        if(isset($_POST['leistungen']['mathe']['hj'.$x]))
        {$leistung = htmlXspecialchars(trim($_POST['leistungen']['mathe']['hj'.$x]));}
        else
        {
            if($leistungen_bewerber['Leistungen_Art'] == "n")
            {
                if(is_numeric($leistungen_bewerber['Mathe_HJ_'.$x.'_Note']))
                $leistung = float_e_d(clean_num($leistungen_bewerber['Mathe_HJ_'.$x.'_Note'], "en"));
                else
                {$leistung = "";}
            }
            else
            {
                if(is_numeric($leistungen_bewerber['Mathe_HJ_'.$x.'_Punkte']))
                {$leistung = clean_num($leistungen_bewerber['Mathe_HJ_'.$x.'_Punkte'], "en");}
                else
                {$leistung = "";}
            }
        }
        echo("<input name=\"leistungen[mathe][hj".$x."]\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".$leistung."\">");
        echo("</td>\n");
        echo("</tr>\n");
    }

    //Mathemathik Abitur
    $zeile++;
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Abiturpr&uuml;fung:");
    echo("</td>\n");
    echo("<td>");
    if(isset($_POST['leistungen']['mathe']['abi']))
    {$leistung = htmlXspecialchars(trim($_POST['leistungen']['mathe']['abi']));}
    else
    {
        if($leistungen_bewerber['Leistungen_Art'] == "n")
        {
            if(is_numeric($leistungen_bewerber['Mathe_End_Note']))
            {$leistung = float_e_d(clean_num($leistungen_bewerber['Mathe_End_Note'], "en"));}
            else
            {$leistung = "";}
        }
        else
        {
            if(is_numeric($leistungen_bewerber['Mathe_End_Punkte']))
            {$leistung = clean_num($leistungen_bewerber['Mathe_End_Punkte'], "en");}
            else
            {$leistung = "";}
        }
    }
    echo("<input name=\"leistungen[mathe][abi]\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".$leistung."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Leerzeile
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Infozeile
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Infozeile\">");
    echo("Angaben f&uuml;r das <b>beste naturwissenschaftliche Fach</b> (*)");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Checkbox f&uuml;r "nicht zutreffend" f&uuml;r das beste naturwissenschaftliche Fach
    $zeile++;
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td colspan=\"2\">");
    if((!isset($_POST['anrede']) AND $leistungen_bewerber['Naturw_belegt'] == "0") OR (isset($_POST['naturw_belegt'])))
    {$check_naturw = "checked=\"checked\"";}
    else
    {$check_naturw = "";}
    echo("<input name=\"naturw_belegt\" type=\"checkbox\" value=\"0\" ".$check_naturw."> <b>nicht zutreffend</b>");
    echo("</td>\n");
    echo("</tr>\n");

    //Art des naturwissenschaftlichen Fachs
    $zeile++;
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Art des Fachs:");
    echo("</td>\n");
    echo("<td>");
    echo("<select name=\"pky_naturw_fach\" class=\"Auswahlfeld\" size=\"1\">\n");
    if(!isset($_POST['pky_naturw_fach']) AND !is_numeric($leistungen_bewerber['fky_Naturw_Fach']))
    {$selected = " selected=\"selected\"";}
    elseif(isset($_POST['pky_naturw_fach']) AND $_POST['pky_naturw_fach'] == "0")
    {$selected = " selected=\"selected\"";}
    else
    {$selected = "";}
    echo("<option".$selected." value=\"0\">Bitte w&auml;hlen</option>\n");
    $naturw_fach_art = naturw_fach_art($link);
    while($row = mysqli_fetch_assoc($naturw_fach_art))
    {
        if(!isset($_POST['pky_naturw_fach']) AND $leistungen_bewerber['fky_Naturw_Fach'] == $row['pky_naturw_Fach'])
        {$selected = " selected=\"selected\"";}
        elseif(isset($_POST['pky_naturw_fach']) AND $_POST['pky_naturw_fach'] == $row['pky_naturw_Fach'])
        {$selected = " selected=\"selected\"";}
        else
        {$selected = "";}
        echo("<option".$selected." value=\"".$row['pky_naturw_Fach']."\">".$row['naturw_Fach']."</option>\n");
    }
    mysqli_free_result($naturw_fach_art);
    echo("</select>");
    echo("</td>\n");
    echo("</tr>\n");

    //bestes naturwissenschaftliches Fach alle Halbjahre
    for($y=1; $y<=4; $y++)
    {
        $zeile++;
        echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("".$y.". Halbjahr:");
        echo("</td>\n");
        echo("<td>");
        if(isset($_POST['leistungen']['naturw']['hj'.$y]))
        {$leistung = htmlXspecialchars(trim($_POST['leistungen']['naturw']['hj'.$y]));}
        else
        {
            if($leistungen_bewerber['Leistungen_Art'] == "n")
            {
                if(is_numeric($leistungen_bewerber['Naturw_HJ_'.$y.'_Note']))
                {$leistung = float_e_d(clean_num($leistungen_bewerber['Naturw_HJ_'.$y.'_Note'], "en"));}
                else
                {$leistung = "";}
            }
            else
            {
                if(is_numeric($leistungen_bewerber['Naturw_HJ_'.$y.'_Punkte']))
                {$leistung = clean_num($leistungen_bewerber['Naturw_HJ_'.$y.'_Punkte'], "en");}
                else
                {$leistung = "";}
            }
        }
        echo("<input name=\"leistungen[naturw][hj".$y."]\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".$leistung."\">");
        echo("</td>\n");
        echo("</tr>\n");
    }

    //bestes naturwissenschaftliches Fach Abitur
    $zeile++;
    echo("<tr style=\"".($active_page == 3 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Abiturpr&uuml;fung:");
    echo("</td>\n");
    echo("<td>");
    if(isset($_POST['leistungen']['naturw']['abi']))
    {$leistung = htmlXspecialchars(trim($_POST['leistungen']['naturw']['abi']));}
    else
    {
        if($leistungen_bewerber['Leistungen_Art'] == "n")
        {
            if(is_numeric($leistungen_bewerber['Naturw_End_Note']))
            {$leistung = float_e_d(clean_num($leistungen_bewerber['Naturw_End_Note'], "en"));}
            else
            {$leistung = "";}
        }
        else
        {
            if(is_numeric($leistungen_bewerber['Naturw_End_Punkte']))
            {$leistung = clean_num($leistungen_bewerber['Naturw_End_Punkte'], "en");}
            else
            {$leistung = "";}
        }
    }
    echo("<input name=\"leistungen[naturw][abi]\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".$leistung."\">");
    echo("</td>\n");
    echo("</tr>\n");

    ################################################
    # Formularfelder f&uuml;r nachschulischen Werdegang #
    ################################################

    //Infozeile
    echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Infozeile\">");
    echo("Angaben zum <b>nachschulischen Werdegang</b> (Bitte im Lebenslauf konkretisieren, falls zutreffend)");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Checkbox f&uuml;r freiwilliges soziales Jahr, Wehrdienst, Zivildienst
    $zeile++;
    echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td colspan=\"2\" style=\"color:#6A6A6A;\">");
    if((!isset($_POST['anrede']) AND $daten_bewerber['Soziales_Jahr'] == "1") OR (isset($_POST['soz_jahr'])))
    {$check_soz_jahr = "checked=\"checked\"";}
    else
    {$check_soz_jahr = "";}
    echo("<input name=\"soz_jahr\" type=\"checkbox\" value=\"1\" ".$check_soz_jahr.">");
    echo(" <b>Freiwilliges soziales Jahr</b> (oder Wehrdienst/Zivildienst) wurde geleistet");
    echo("</td>\n");
    echo("</tr>\n");

    //Ausbildung
    $zeile++;
    echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td colspan=\"2\" style=\"color:#6A6A6A;\">");
    echo("<b>Eine der folgenden Ausbildungen wurde erfolgreich abgeschlossen:</b> ");
    echo("<select name=\"pky_ausbildung\" class=\"Auswahlfeld\" size=\"1\">\n");
    if(!isset($_POST['pky_ausbildung']))
    {$selected = " selected=\"selected\"";}
    if(isset($_POST['pky_ausbildung']) AND $_POST['pky_ausbildung'] == "0")
    {$selected = " selected=\"selected\"";}
    else
    {$selected = "";}
    echo("<option".$selected." value=\"0\">keine</option>\n");
    $ausbildungen = ausbildungen($link);
    while($row = mysqli_fetch_assoc($ausbildungen))
    {
        if(!isset($_POST['pky_ausbildung']) AND $daten_bewerber['fky_Ausbildung'] == $row['pky_Ausbildung'])
        {$selected = " selected=\"selected\"";}
        elseif(isset($_POST['pky_ausbildung']) AND $_POST['pky_ausbildung'] == $row['pky_Ausbildung'])
        {$selected = " selected=\"selected\"";}
        else
        {$selected = "";}
        echo("<option".$selected." value=\"".$row['pky_Ausbildung']."\">".$row['Ausbildung']."</option>\n");
    }
    mysqli_free_result($ausbildungen);
    echo("</select>");
    echo("</td>\n");
    echo("</tr>\n");

    #################################
    # Formularfelder f&uuml;r Lebenslauf #
    #################################

    //Leerzeile
    echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Infozeile
    echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Infozeile\">");
    echo("<b>Lebenslauf *</b> (max. 100 W&ouml;rter)");
    echo("</td>\n");
    echo("</tr>\n");

    echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\">");
    //Eigene Tabelle f&uuml;r den Lebenslauf
    echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" style=\"width:100%;\">\n");

    //Anzahl der angezeigten Zeilen festlegen
    if(!isset($_POST['lebenslauf']) AND isset($lebenslauf_bewerber) AND !isset($_POST['neuer_eintrag_ll']) AND !isset($_POST['eintrag_ll_loeschen']))
    {
        foreach($lebenslauf_bewerber AS  $nr_eintrag => $array_eintrag)
        {
            $_POST['lebenslauf'][$nr_eintrag]['am_von_ll'] = $array_eintrag['datum_am_von'];
            $_POST['lebenslauf'][$nr_eintrag]['bis_ll'] = $array_eintrag['datum_bis'];
            $_POST['lebenslauf'][$nr_eintrag]['text_ll'] = $array_eintrag['eintrag'];
        }
    }
    //Wenn alle Zeilen gel&ouml;scht wurden
    elseif(!isset($_POST['lebenslauf']) AND !isset($lebenslauf_bewerber))
    {
        //Drei Eintr&auml;ge vorgeben
        $_POST['lebenslauf'][1]['am_von_ll'] = "";
        $_POST['lebenslauf'][1]['bis_ll'] = "";
        $_POST['lebenslauf'][1]['text_ll'] = "";

        $_POST['lebenslauf'][2]['am_von_ll'] = "";
        $_POST['lebenslauf'][2]['bis_ll'] = "";
        $_POST['lebenslauf'][2]['text_ll'] = "";

        $_POST['lebenslauf'][3]['am_von_ll'] = "";
        $_POST['lebenslauf'][3]['bis_ll'] = "";
        $_POST['lebenslauf'][3]['text_ll'] = "";
    }
    elseif(isset($_POST['neuer_eintrag_ll']))
    {
        if(isset($_POST['lebenslauf']))
        {$max_key = max(array_keys($_POST['lebenslauf']));}
        else
        {$max_key = 0;}
        $_POST['lebenslauf'][($max_key + 1)]['am_von_ll'] = "";
        $_POST['lebenslauf'][($max_key + 1)]['bis_ll'] = "";
        $_POST['lebenslauf'][($max_key + 1)]['text_ll'] = "";
    }
    elseif(isset($_POST['eintrag_ll_loeschen']))
    {
        $arr_key = array_search(">> l&ouml;schen", $_POST['eintrag_ll_loeschen']);
        unset($_POST['lebenslauf'][$arr_key]);
    }

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Ausgabe der einzelnen Eintr&auml;ge als Schleife
    foreach($_POST['lebenslauf'] AS $key => $value)
    {
        $zeile++;
        echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
        echo("am/von: <input name=\"lebenslauf[".$key."][am_von_ll]\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".htmlXspecialchars(trim($value['am_von_ll']))."\">");
        echo("</td>\n");
        echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
        echo("bis: <input name=\"lebenslauf[".$key."][bis_ll]\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".htmlXspecialchars(trim($value['bis_ll']))."\">");
        echo("</td>\n");
        echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
        echo("Eintrag:");
        echo("</td>\n");
        echo("<td>");
        echo("<textarea name=\"lebenslauf[".$key."][text_ll]\" cols=\"55\" rows=\"2\">".htmlXspecialchars(trim($value['text_ll']))."</textarea>");
        echo("</td>\n");

        //Button zum l&ouml;schen von Zeilen
        echo("<td valign=\"top\">");
        echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"eintrag_ll_loeschen[".$key."]\" value=\">> l&ouml;schen\">");
        echo("</td>\n");
        echo("</tr>\n");
    }

    //Button zum Anf&uuml;gen neuer Zeilen
    echo("<tr>\n");
    echo("<td colspan=\"5\">");
    echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"neuer_eintrag_ll\" value=\">> weiteren Eintrag anf&uuml;gen\">");
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
    //Tabelle Lebenslauf ende
    echo("</td>\n");
    echo("</tr>\n");

    #################################
    # Formularfelder f&uuml;r Begr&uuml;ndung #
    #################################

    //Leerzeile
    echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Infozeile
    echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."\">\n");
    echo("<td colspan=\"2\" class=\"Infozeile\">");
    echo("<b>Begr&uuml;ndung *</b>, warum der Studiengang Molekulare Medizin an der Uni Regensburg angestrebt wird (max. 200 W&ouml;rter)");
    echo("</td>\n");
    echo("</tr>\n");

    //Begr&uuml;ndung
    echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."background-color:#EEEEEE;\">\n");
    echo("<td colspan=\"2\" align=\"center\">");
    echo("<textarea name=\"begruendung\" cols=\"100\" rows=\"4\">".htmlXspecialchars(trim((isset($_POST['begruendung'])) ? $_POST['begruendung'] : $daten_bewerber['Begruendung']))."</textarea>");
    echo("</td>\n");
    echo("</tr>\n");

    #############################################
    # Auflistung aller Eintr&auml;ge mit Validierung #
    #############################################

    //Die Inhalte dieser Seite m&uuml;ssen nur angezeigt werden, wenn die Seite auch tats&auml;chlich aufgerufen wird
    //da KEINE Formulardaten &uuml;bergeben werden m&uuml;ssen
    if(isset($active_page) AND $active_page == 5)
    {
        //Information
        echo("<tr>\n");
        echo("<td colspan=\"2\" class=\"Information\">");
        echo("<b>Bitte &uuml;berpr&uuml;fen Sie Ihre Angaben noch einmal auf Vollst&auml;ndigkeit und Richtigkeit.</b><br />");
        echo(" Wenn alle Pflichtfelder ordnungsgem&auml;&szlig; ausgef&uuml;llt wurden erscheint rechts unten im Formular die Schaltfl&auml;che \"Daten &auml;ndern\".");
        echo(" Ihr Passwort wird nur ge&auml;ndert, wenn in den entsprechenden Feldern unter \"pers&ouml;nl. Angaben\" ein Eintrag erfolgt ist. Ansonsten behalten Sie Ihr altes Passwort.");
        echo("</td>\n");
        echo("</tr>\n");

        ///////////////////////////////////////////////////
        /////////////// pers&ouml;nliche Angaben ///////////////
        ///////////////////////////////////////////////////

        //Leerzeile
        echo("<tr>\n");
        echo("<td colspan=\"2\" class=\"Leerzeile\">");
        echo("</td>\n");
        echo("</tr>\n");

        //&UUML;berschrift
        echo("<tr>\n");
        echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
        echo("Pers&ouml;nliche Angaben");
        echo("</td>\n");
        echo("</tr>\n");

        //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
        $zeile = 0;

        //Anrede
        $zeile++;
        if($_POST['anrede'] == "")
        {$ergebnis_check = false; $warnung['anrede'] = "Es wurde keine Anrede angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td style=\"width:21%;\" class=\"Zeile_Bezeichnung\">");
        echo("Anrede:");
        echo("</td>\n");
        echo("<td>");
        if($_POST['anrede'] == "f")
        {echo("Frau");}
        elseif($_POST['anrede'] == "h")
        {echo("Herr");}
        else
        {echo("".$warnung['anrede']."");}
        echo("</td>\n");
        echo("</tr>\n");

        //Nachname
        $zeile++;
        if(isset($_POST['nachname']) AND trim($_POST['nachname']) == "")
        {$ergebnis_check = false; $warnung['nachname'] = "Es wurde kein Nachname angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Nachname:");
        echo("</td>\n");
        echo("<td>");
        if($ergebnis_check == true)
        {echo("".htmlXspecialchars(trim($_POST['nachname']))."");}
        else
        {echo("".$warnung['nachname']."");}
        echo("</td>\n");
        echo("</tr>\n");

        //Vorname
        $zeile++;
        if(isset($_POST['vorname']) AND trim($_POST['vorname']) == "")
        {$ergebnis_check = false; $warnung['vorname'] = "Es wurde kein Vorname angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Vorname:");
        echo("</td>\n");
        echo("<td>");
        if($ergebnis_check == true)
        {echo("".htmlXspecialchars(trim($_POST['vorname']))."");}
        else
        {echo("".$warnung['vorname']."");}
        echo("</td>\n");
        echo("</tr>\n");

        //Geburtsdatum
        $zeile++;
        if(isset($_POST['geburtsdatum']) AND !datum_regex(trim($_POST['geburtsdatum'])))
        {$ergebnis_check = false; $warnung['geburtsdatum'] = "Es wurde kein oder ein ung&uuml;ltiges Geburtsdatum angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Geburtsdatum:");
        echo("</td>\n");
        echo("<td>");
        if($ergebnis_check == true)
        {echo("".trim($_POST['geburtsdatum'])."");}
        else
        {echo("".$warnung['geburtsdatum']."");}
        echo("</td>\n");
        echo("</tr>\n");

        //Nationalit&auml;t
        $zeile++;
        echo("<tr".style_input_check($zeile, $ergebnis_check = true).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Nationalit&auml;t:");
        echo("</td>\n");
        echo("<td>");
        echo("".land_eintrag($link, $_POST['nationalitaet_pky_land'])."");
        echo("</td>\n");
        echo("</tr>\n");

        //Strasse und Hausnummer
        $zeile++;
        if( (isset($_POST['strasse']) AND trim($_POST['strasse']) == "") AND (isset($_POST['hausnr']) AND trim($_POST['hausnr']) == "") )
        {$ergebnis_check = false; $warnung['strasse_nr'] = "Es wurde keine Stra&szlig;e und keine Hausnummer angegeben!";}
        elseif( (isset($_POST['strasse']) AND trim($_POST['strasse']) == "") AND (isset($_POST['hausnr']) AND trim($_POST['hausnr']) != "") )
        {$ergebnis_check = false; $warnung['strasse'] = "Es wurde keine Stra&szlig;e angegeben!";}
        elseif( (isset($_POST['strasse']) AND trim($_POST['strasse']) != "") AND (isset($_POST['hausnr']) AND trim($_POST['hausnr']) == "") )
        {$ergebnis_check = false; $warnung['hausnr'] = "Es wurde keine Hausnummer angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Stra&szlig;e/Nummer:");
        echo("</td>\n");
        echo("<td>");
        if( (isset($_POST['strasse']) AND trim($_POST['strasse']) == "") AND (isset($_POST['hausnr']) AND trim($_POST['hausnr']) == "") )
        {echo("".$warnung['strasse_nr']."");}
        elseif( (isset($_POST['strasse']) AND trim($_POST['strasse']) == "") AND (isset($_POST['hausnr']) AND trim($_POST['hausnr']) != "") )
        {echo("(".$warnung['strasse'].") ".htmlXspecialchars(trim($_POST['hausnr']))."");}
        elseif( (isset($_POST['strasse']) AND trim($_POST['strasse']) != "") AND (isset($_POST['hausnr']) AND trim($_POST['hausnr']) == "") )
        {echo("".htmlXspecialchars(trim($_POST['strasse']))." (".$warnung['hausnr'].")");}
        else
        {echo("".htmlXspecialchars(trim($_POST['strasse']))." ".htmlXspecialchars(trim($_POST['hausnr']))."");}
        echo("</td>\n");
        echo("</tr>\n");

        //Adresszusatz
        if(isset($_POST['adresszusatz']) AND trim($_POST['adresszusatz']) != "")
        {
            $zeile++;
            echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Adresszusatz:");
            echo("</td>\n");
            echo("<td>");
            echo("".htmlXspecialchars(trim($_POST['adresszusatz']))."");
            echo("</td>\n");
            echo("</tr>\n");
        }

        //PLZ und Wohnort
        $zeile++;
        if( (isset($_POST['plz']) AND trim($_POST['plz']) == "") AND (isset($_POST['ort']) AND trim($_POST['ort']) == "") )
        {$ergebnis_check = false; $warnung['plz_ort'] = "Es wurde keine Postleitzahl und kein Wohnort angegeben!";}
        elseif( (isset($_POST['plz']) AND trim($_POST['plz']) == "") AND (isset($_POST['ort']) AND trim($_POST['ort']) != "") )
        {$ergebnis_check = false; $warnung['plz'] = "Es wurde keine Postleitzahl angegeben!";}
        elseif( (isset($_POST['plz']) AND trim($_POST['plz']) != "") AND (isset($_POST['ort']) AND trim($_POST['ort']) == "") )
        {$ergebnis_check = false; $warnung['ort'] = "Es wurde kein Wohnort angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("PLZ/Wohnort:");
        echo("</td>\n");
        echo("<td>");
        if( (isset($_POST['plz']) AND trim($_POST['plz']) == "") AND (isset($_POST['ort']) AND trim($_POST['ort']) == "") )
        {echo("".$warnung['plz_ort']."");}
        elseif( (isset($_POST['plz']) AND trim($_POST['plz']) == "") AND (isset($_POST['ort']) AND trim($_POST['ort']) != "") )
        {echo("(".$warnung['plz'].") ".htmlXspecialchars(trim($_POST['ort']))."");}
        elseif( (isset($_POST['plz']) AND trim($_POST['plz']) != "") AND (isset($_POST['ort']) AND trim($_POST['ort']) == "") )
        {echo("".htmlXspecialchars(trim($_POST['plz']))." (".$warnung['ort'].")");}
        else
        {echo("".htmlXspecialchars(trim($_POST['plz']))." ".htmlXspecialchars(trim($_POST['ort']))."");}
        echo("</td>\n");
        echo("</tr>\n");

        //Land
        $zeile++;
        echo("<tr".style_input_check($zeile, $ergebnis_check = true).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Land:");
        echo("</td>\n");
        echo("<td>");
        echo("".land_eintrag($link, $_POST['pky_land'])."");
        echo("</td>\n");
        echo("</tr>\n");

        //Email
        $zeile++;
        if(isset($_POST['email']) AND !email_regex(trim($_POST['email'])))
        {$ergebnis_check = false; $warnung['email'] = "Es wurde keine oder eine ung&uuml;ltige Email Adresse angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Email:");
        echo("</td>\n");
        echo("<td>");
        if($ergebnis_check == true)
        {echo("".trim($_POST['email'])."");}
        else
        {echo("".$warnung['email']."");}
        echo("</td>\n");
        echo("</tr>\n");

        //Passwort

        //Wenn in keinem der drei Passwortfelder eine Eingabe gemacht wurde
        //soll dass Passwort nicht ge&auml;ndert werden und wird somit auch nicht angezeigt
        if($_POST['passwort_alt'] != "" OR $_POST['passwort_1'] != "" OR $_POST['passwort_2'] != "")
        {
            //&UUML;berpr&uuml;fen, ob ein Eintrag bei Passwort alt gemacht wurde
            if($_POST['passwort_alt'] == "")
            {$ergebnis_check = false; $warnung['passwort'] = "Um Ihr Passwort zu &auml;ndern m&uuml;ssen Sie Ihr altes Passwort angeben!";}
            else
            {
                //&UUML;berpr&uuml;fen, ob das alte Passwort richtig ist
                if(!passwort_check_bewerber($link, $_SESSION['SESSION_PKY_BEWERBER'], $_POST['passwort_alt']))
                {$ergebnis_check = false; $warnung['passwort'] = "Ihr altes Passwort wurde nicht korrekt eingegeben (Gro&szlig;- und Kleinschreibung beachten)!";}
                else
                {
                    //&UUML;berpr&uuml;fen, ob in das Passwortfeld ein Eintrag gemacht wurde
                    if($_POST['passwort_1'] == "")
                    {$ergebnis_check = false; $warnung['passwort'] = "Es wurde kein neues Passwort angegeben!";}
                    else
                    {
                        //&UUML;berpr&uuml;fen, ob das Passwort mind aus 8 Zeichen besteht
                        if(strlen($_POST['passwort_1']) < 8)
                        {$ergebnis_check = false; $warnung['passwort'] = "Das neue Passwort muss aus mindestens 8 Zeichen bestehen!";}
                        else
                        {
                            //&UUML;berpr&uuml;fen, ob eine Best&auml;tigung eingetragen wurde
                            if($_POST['passwort_2'] == "")
                            {$ergebnis_check = false; $warnung['passwort'] = "Es wurde keine Best&auml;tigung f&uuml;r das neue Passwort angegeben!";}
                            else
                            {
                                //&UUML;berpr&uuml;fen, ob PW und Best&auml;tigung &uuml;bereinstimmen
                                if($_POST['passwort_1'] != $_POST['passwort_2'])
                                {$ergebnis_check = false; $warnung['passwort'] = "Die Best&auml;tigung stimmt nicht mit dem neuen Passwort &uuml;berein (Gro&szlig;- und Kleinschreibung beachten)!";}
                                else
                                {$ergebnis_check = true;}
                            }
                        }
                    }
                }
            }

            $zeile++;
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("neues Passwort:");
            echo("</td>\n");
            echo("<td>");
            if($ergebnis_check == true)
            //Anzahl der Buchstaben des Passworts als Sterne darstellen
            {
                for($k=1; $k<=strlen($_POST['passwort_1']); $k++)
                {echo("*");}
            }
            else
            {echo("".$warnung['passwort']."");}
            echo("</td>\n");
            echo("</tr>\n");
        }

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
        echo("<tr".style_input_check($zeile, $ergebnis_check = true).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Art der HZB:");
        echo("</td>\n");
        echo("<td>");
        echo("".hzb_art_eintrag($link, $_POST['pky_hzb'])."");
        echo("</td>\n");
        echo("</tr>\n");

        //HZB sonstige
        if(isset($_POST['pky_hzb']) AND $_POST['pky_hzb'] == PKY_SONST_HZB)
        {
            $zeile++;
            if(isset($_POST['hzb_sonstige']) AND trim($_POST['hzb_sonstige']) == "")
            {$ergebnis_check = false; $warnung['hzb_sonstige'] = "Wenn \"sonstige Hochschulzugangsberechtigung\" gew&auml;hlt wurde, muss diese konkretisiert werden!";}
            else
            {$ergebnis_check = true;}
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Sonstige HZB:");
            echo("</td>\n");
            echo("<td>");
            if($ergebnis_check == true)
            {echo("".htmlXspecialchars(trim($_POST['hzb_sonstige']))."");}
            else
            {echo("".$warnung['hzb_sonstige']."");}
            echo("</td>\n");
            echo("</tr>\n");
        }

        //HZB Jahr
        $zeile++;
        if(isset($_POST['hzb_jahr']) AND !jahr_check($_POST['hzb_jahr']))
        {$ergebnis_check = false; $warnung['hzb_jahr'] = "Es wurde kein oder ein ung&uuml;ltiges Jahr angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Jahr der HZB:");
        echo("</td>\n");
        echo("<td>");
        if($ergebnis_check == true)
        {echo("".trim($_POST['hzb_jahr'])."");}
        else
        {echo("".$warnung['hzb_jahr']."");}
        echo("</td>\n");
        echo("</tr>\n");

        //HZB Ort
        $zeile++;
        if(isset($_POST['hzb_ort']) AND trim($_POST['hzb_ort']) == "")
        {$ergebnis_check = false; $warnung['hzb_ort'] = "Es wurde kein Ort der HZB angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Ort der HZB:");
        echo("</td>\n");
        echo("<td>");
        if($ergebnis_check == true)
        {echo("".htmlXspecialchars(trim($_POST['hzb_ort']))."");}
        else
        {echo("".$warnung['hzb_ort']."");}
        echo("</td>\n");
        echo("</tr>\n");

        //HZB Land
        $zeile++;
        if(isset($_POST['hzb_pky_land']) AND $_POST['hzb_pky_land'] == PKY_DEUTSCHLAND AND isset($_POST['pky_hzb']) AND $_POST['pky_hzb'] == PKY_HZB_AUSLAND)
        {$ergebnis_check = false; $warnung['hzb_land'] = "Wenn Sie Ihre HZB im Ausland erworben haben, dann darf nicht Deutschland angegeben werden!";}
        elseif(isset($_POST['hzb_pky_land']) AND $_POST['hzb_pky_land'] != PKY_DEUTSCHLAND AND isset($_POST['pky_hzb']) AND $_POST['pky_hzb'] == PKY_ALLG_ABITUR)
        {$ergebnis_check = false; $warnung['hzb_land'] = "Wenn hier nicht Deutschland gew&auml;hlt wurde, dann muss bei Art der HZB \"im Ausland erworbene Hochschulzugangsberechtigung\" gew&auml;hlt werden!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\" valign=\"top\">");
        echo("Land der HZB:");
        echo("</td>\n");
        echo("<td>");
        if($ergebnis_check == true)
        {echo("".land_eintrag($link, $_POST['hzb_pky_land'])."");}
        else
        {echo("".$warnung['hzb_land']."");}
        echo("</td>\n");
        echo("</tr>\n");

        //HZB Note
        $zeile++;
        if(isset($_POST['hzb_note']) AND !note_check(trim($_POST['hzb_note']), 4))
        {$ergebnis_check = false; $warnung['hzb_note'] = "Es wurde keine oder eine ung&uuml;ltige Note angegeben!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Abschlussnote:");
        echo("</td>\n");
        echo("<td>");
        if($ergebnis_check == true)
        {echo("".trim($_POST['hzb_note'])."");}
        else
        {echo("".$warnung['hzb_note']."");}
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

        //Die Variablen $_POST['mathe_belegt'] und $_POST['naturw_belegt'] setzen, wenn die Checkbox nicht gesetzt wurde
        if(!isset($_POST['mathe_belegt'])){$_POST['mathe_belegt'] = "";}
        if(!isset($_POST['naturw_belegt'])){$_POST['naturw_belegt'] = "";}

        //Art der Angabe
        //Angabe nur notwendig, wenn Mathe oder ein naturw. Fach auch belegt wurden
        if($_POST['mathe_belegt'] != "0" OR $_POST['naturw_belegt'] != "0")
        {
            $zeile++;
            if(!isset($_POST['leistungen_art']))
            {
                $ergebnis_check = false;
                $warnung['leistungen_art'] = "Die Art der Leistungen (Punkte oder Schulnoten) muss angegeben werden, wenn mindestens eines der genannten F&auml;cher teilweise oder &uuml;ber die vollen vier Halbjahre belegt wurde!";
                $warnung['leistungen_art'] .= " War dies nicht der Fall, dann muss die Checkbox \"nicht zutreffend\" bei beiden F&auml;chern aktiviert werden.";
            }
            else
            {$ergebnis_check = true;}
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\" valign=\"top\">");
            echo("Art der Angabe:");
            echo("</td>\n");
            echo("<td>");
            if(isset($_POST['leistungen_art']) AND $_POST['leistungen_art'] == "n")
            {echo("deutsche Schulnoten (1,00-6,00)");}
            elseif(isset($_POST['leistungen_art']) AND $_POST['leistungen_art'] == "p")
            {echo("Punkte (0-15)");}
            else
            {echo("".$warnung['leistungen_art']."");}
            echo("</td>\n");
            echo("</tr>\n");
        }

        //Weitere Pr&uuml;fungen sind nur m&ouml;glich, wenn die Art der Leistungsangabe erfolgt ist
        if(!isset($warnung['leistungen_art']))
        {
            ///////////////////////////
            //////   Mathemathik //////
            ///////////////////////////

            $zeile++;

            //Die Angaben f&uuml;r Mathematik werden nur gepr&uuml;ft, wenn diese auch belegt wurde
            //Wenn die Checkbox "nicht zutreffend" aktiviert wurde und trotzdem eine Eingabe von Noten erfolgte, so werde diese ignoriert bzw. nicht angezeigt
            if($_POST['mathe_belegt'] != "0")
            {
                //Wenn die Checkbox "nicht zutreffend" nicht aktiviert wurde (= Mathe belegt) und trotzden kein Eintrag bei den Halbjahren inkl. Abi erfolgte
                if(trim($_POST['leistungen']['mathe']['hj1']) == "" AND
                   trim($_POST['leistungen']['mathe']['hj2']) == "" AND
                   trim($_POST['leistungen']['mathe']['hj3']) == "" AND
                   trim($_POST['leistungen']['mathe']['hj4']) == "" AND
                   trim($_POST['leistungen']['mathe']['abi']) == "")
                {
                    $ergebnis_check = false;
                    $warnung['leistungen']['mathe'] = "Keine Angaben zu den Leisungen f&uuml;r Mathematik! Wenn Mathemathik in den letzten vier Halbjahren nicht belegt wurde, dann muss die Checkbox \"nicht zutreffend\" aktiviert werden.";
                    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
                    echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
                    echo("Mathematik:");
                    echo("</td>\n");
                    echo("<td>\n");
                    echo("".$warnung['leistungen']['mathe']."");

                }
                //Wenn die Checkbox "nicht zutreffend" nicht aktiviert wurde (= Mathe belegt) und irgend ein Eintrag bei den Halbjahren inkl. Abi gemacht wurde
                else
                {
                    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
                    echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
                    echo("Mathematik:");
                    echo("</td>\n");
                    echo("<td>\n");

                    //eigene Tabelle einf&uuml;gen
                    echo("<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" style=\"width:100%;\">\n");

                    //alle Halbjahre pr&uuml;fen
                    for($l=1; $l<=4; $l++)
                    {
                        if(trim($_POST['leistungen']['mathe']['hj'.$l]) != "" AND $_POST['leistungen_art'] == "n" AND !note_check($_POST['leistungen']['mathe']['hj'.$l], 6))
                        {$ergebnis_check = false; $warnung['leistungen']['mathe']['hj'.$l] = "Falsche Angabe!";}
                        elseif(trim($_POST['leistungen']['mathe']['hj'.$l]) != "" AND $_POST['leistungen_art'] == "p" AND !punkte_check($_POST['leistungen']['mathe']['hj'.$l]))
                        {$ergebnis_check = false; $warnung['leistungen']['mathe']['hj'.$l] = "Falsche Angabe!";}
                        else
                        {$ergebnis_check = true;}
                        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
                        echo("<td style=\"width:15%;\" valign=\"top\">");
                        echo("".$l.". Halbjahr:");
                        echo("</td>\n");
                        echo("<td>");
                        if($ergebnis_check == true)
                        {
                            //Wenn ein Eintrag stattgefunden hat
                            if(trim($_POST['leistungen']['mathe']['hj'.$l]) != "")
                            {echo("".trim($_POST['leistungen']['mathe']['hj'.$l])."");}
                            //Wenn kein Eintrag stattgefunden hat
                            else
                            {echo("keine Angabe");}
                        }
                        else
                        {echo("".$warnung['leistungen']['mathe']['hj'.$l]."");}
                        echo("</td>\n");
                        echo("</tr>\n");
                    }

                    //Abiturpr&uuml;fung

                    if(trim($_POST['leistungen']['mathe']['abi']) != "" AND $_POST['leistungen_art'] == "n" AND !note_check($_POST['leistungen']['mathe']['abi'], 6))
                    {$ergebnis_check = false; $warnung['leistungen']['mathe']['abi'] = "Falsche Angabe!";}
                    elseif(trim($_POST['leistungen']['mathe']['abi']) != "" AND $_POST['leistungen_art'] == "p" AND !punkte_check($_POST['leistungen']['mathe']['abi']))
                    {$ergebnis_check = false; $warnung['leistungen']['mathe']['abi'] = "Falsche Angabe!";}
                    elseif(trim($_POST['leistungen']['mathe']['abi']) != "" AND
                           (trim($_POST['leistungen']['mathe']['hj1']) == "" OR
                            trim($_POST['leistungen']['mathe']['hj2']) == "" OR
                            trim($_POST['leistungen']['mathe']['hj3']) == "" OR
                            trim($_POST['leistungen']['mathe']['hj4']) == ""))
                    {$ergebnis_check = false; $warnung['leistungen']['mathe']['abi'] = "".trim($_POST['leistungen']['mathe']['abi'])." (Wenn Sie f&uuml;r die Abiturpr&uuml;fung in Mathematik eine Angabe gemacht haben, dann m&uuml;ssen auch die Leistungen der letzten vier Halbjahre eingetragen werden!)";}
                    else
                    {$ergebnis_check = true;}
                    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
                    echo("<td valign=\"top\">");
                    echo("Abiturpr&uuml;fung:");
                    echo("</td>\n");
                    echo("<td>");
                    if($ergebnis_check == true)
                    {
                        //Wenn ein Eintrag stattgefunden hat
                        if(trim($_POST['leistungen']['mathe']['abi']) != "")
                        {echo("".trim($_POST['leistungen']['mathe']['abi'])."");}
                        //Wenn kein Eintrag stattgefunden hat
                        else
                        {echo("keine Angabe");}
                    }
                    else
                    {echo("".$warnung['leistungen']['mathe']['abi']."");}
                    echo("</td>\n");
                    echo("</tr>\n");

                    echo("</table>\n");
                }
            }
            //Hinweis, wenn die Checkbox "nicht zutreffend" aktiviert wurde
            else
            {
                echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
                echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
                echo("Mathematik:");
                echo("</td>\n");
                echo("<td>\n");
                echo("Das Fach Mathematik wurde w&auml;hrend der letzten vier Halbjahre Ihrer schulischen Laufbahn nicht belegt!");
            }

            echo("</td>\n");
            echo("</tr>\n");

            /////////////////////////////////////////////////////
            //////   Bestes naturwissenschaftliches Fach   //////
            /////////////////////////////////////////////////////

            $zeile++;

            //Die Angaben f&uuml;r das beste naturw. Fach werden nur gepr&uuml;ft, wenn dieses auch belegt wurde
            //Wenn die Checkbox "nicht zutreffend" aktiviert wurde und trotzdem eine Eingabe von Noten erfolgte, so werde diese ignoriert bzw. nicht angezeigt
            if($_POST['naturw_belegt'] != "0")
            {
                //Wenn die Checkbox "nicht zutreffend" nicht aktiviert wurde (= Naturw. belegt) und trotzden kein Eintrag bei den Halbjahren inkl. Abi erfolgte
                if(trim($_POST['leistungen']['naturw']['hj1']) == "" AND
                   trim($_POST['leistungen']['naturw']['hj2']) == "" AND
                   trim($_POST['leistungen']['naturw']['hj3']) == "" AND
                   trim($_POST['leistungen']['naturw']['hj4']) == "" AND
                   trim($_POST['leistungen']['naturw']['abi']) == "")
                {
                    $ergebnis_check = false;
                    $warnung['leistungen']['naturw'] = "Keine Angaben zu den Leisungen f&uuml;r Ihr bestes naturw. Fach! Wenn kein naturw. Fach in den letzten vier Halbjahren belegt wurde, dann muss die Checkbox \"nicht zutreffend\" aktiviert werden.";
                    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
                    echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
                    echo("Bestes naturw. Fach:");
                    echo("</td>\n");
                    echo("<td>\n");
                    echo("".$warnung['leistungen']['naturw']."");

                }
                //Wenn die Checkbox "nicht zutreffend" nicht aktiviert wurde (= Naturw. belegt) und irgend ein Eintrag bei den Halbjahren inkl. Abi gemacht wurde
                else
                {
                    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
                    echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
                    echo("Bestes naturw. Fach:");
                    echo("</td>\n");
                    echo("<td>\n");

                    //eigene Tabelle einf&uuml;gen
                    echo("<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" style=\"width:100%;\">\n");

                    //Art des Fachs
                    if($_POST['pky_naturw_fach'] == "0")
                    {$ergebnis_check = false; $warnung['naturw_fach'] = "Es wurde kein Fach ausgew&auml;hlt!";}
                    else
                    {$ergebnis_check = true;}
                    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
                    echo("<td colspan=\"2\">");
                    if($ergebnis_check == true)
                    {echo("<b>".naturw_fach_eintrag($link, $_POST['pky_naturw_fach'])."</b>");}
                    else
                    {echo("".$warnung['naturw_fach']."");}
                    echo("</td>\n");
                    echo("</tr>\n");

                    //alle Halbjahre pr&uuml;fen
                    for($l=1; $l<=4; $l++)
                    {
                        if(trim($_POST['leistungen']['naturw']['hj'.$l]) != "" AND $_POST['leistungen_art'] == "n" AND !note_check($_POST['leistungen']['naturw']['hj'.$l], 6))
                        {$ergebnis_check = false; $warnung['leistungen']['naturw']['hj'.$l] = "Falsche Angabe!";}
                        elseif(trim($_POST['leistungen']['naturw']['hj'.$l]) != "" AND $_POST['leistungen_art'] == "p" AND !punkte_check($_POST['leistungen']['naturw']['hj'.$l]))
                        {$ergebnis_check = false; $warnung['leistungen']['naturw']['hj'.$l] = "Falsche Angabe!";}
                        else
                        {$ergebnis_check = true;}
                        echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
                        echo("<td style=\"width:15%;\" valign=\"top\">");
                        echo("".$l.". Halbjahr:");
                        echo("</td>\n");
                        echo("<td>");
                        if($ergebnis_check == true)
                        {
                            //Wenn ein Eintrag stattgefunden hat
                            if(trim($_POST['leistungen']['naturw']['hj'.$l]) != "")
                            {echo("".trim($_POST['leistungen']['naturw']['hj'.$l])."");}
                            //Wenn kein Eintrag stattgefunden hat
                            else
                            {echo("keine Angabe");}
                        }
                        else
                        {echo("".$warnung['leistungen']['naturw']['hj'.$l]."");}
                        echo("</td>\n");
                        echo("</tr>\n");
                    }

                    //Abiturpr&uuml;fung

                    if(trim($_POST['leistungen']['naturw']['abi']) != "" AND $_POST['leistungen_art'] == "n" AND !note_check($_POST['leistungen']['naturw']['abi'], 6))
                    {$ergebnis_check = false; $warnung['leistungen']['naturw']['abi'] = "Falsche Angabe!";}
                    elseif(trim($_POST['leistungen']['naturw']['abi']) != "" AND $_POST['leistungen_art'] == "p" AND !punkte_check($_POST['leistungen']['naturw']['abi']))
                    {$ergebnis_check = false; $warnung['leistungen']['naturw']['abi'] = "Falsche Angabe!";}
                    elseif(trim($_POST['leistungen']['naturw']['abi']) != "" AND
                           (trim($_POST['leistungen']['naturw']['hj1']) == "" OR
                            trim($_POST['leistungen']['naturw']['hj2']) == "" OR
                            trim($_POST['leistungen']['naturw']['hj3']) == "" OR
                            trim($_POST['leistungen']['naturw']['hj4']) == ""))
                    {$ergebnis_check = false; $warnung['leistungen']['naturw']['abi'] = "".trim($_POST['leistungen']['naturw']['abi'])." (Wenn Sie f&uuml;r die Abiturpr&uuml;fung in Ihrem besten naturw. Fach eine Angabe gemacht haben, dann m&uuml;ssen auch die Leistungen der letzten vier Halbjahre eingetragen werden!)";}
                    else
                    {$ergebnis_check = true;}
                    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
                    echo("<td valign=\"top\">");
                    echo("Abiturpr&uuml;fung:");
                    echo("</td>\n");
                    echo("<td>");
                    if($ergebnis_check == true)
                    {
                        //Wenn ein Eintrag stattgefunden hat
                        if(trim($_POST['leistungen']['naturw']['abi']) != "")
                        {echo("".trim($_POST['leistungen']['naturw']['abi'])."");}
                        //Wenn kein Eintrag stattgefunden hat
                        else
                        {echo("keine Angabe");}
                    }
                    else
                    {echo("".$warnung['leistungen']['naturw']['abi']."");}
                    echo("</td>\n");
                    echo("</tr>\n");

                    echo("</table>\n");
                }
            }
            //Hinweis, wenn die Checkbox "nicht zutreffend" aktiviert wurde
            else
            {
                echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
                echo("<td valign=\"top\" class=\"Zeile_Bezeichnung\">");
                echo("Bestes naturw. Fach:");
                echo("</td>\n");
                echo("<td>\n");
                echo("Sie haben kein naturw. Fach w&auml;hrend der letzten vier Halbjahre Ihrer schulischen Laufbahn belegt!");
            }

            echo("</td>\n");
            echo("</tr>\n");
        }

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
        echo("<tr".style_input_check($zeile, $ergebnis_check = true).">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Soziales Jahr:");
        echo("</td>\n");
        echo("<td>");
        if(isset($_POST['soz_jahr']) AND $_POST['soz_jahr'] == "1")
        {echo("Es wurde ein Soziales Jahr (oder Wehrdienst/Zivildienst) geleistet");}
        else
        {echo("Es wurde kein Soziales Jahr (oder Wehrdienst/Zivildienst) geleistet");}
        echo("</td>\n");
        echo("</tr>\n");

        //Ausbildung
        if($_POST['pky_ausbildung'] != "0")
        {
            $zeile++;
            echo("<tr".style_input_check($zeile, $ergebnis_check = true).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Ausbildung als:");
            echo("</td>\n");
            echo("<td>");
            echo("".ausbildungen_eintrag($link, $_POST['pky_ausbildung'])."");
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

        //&UUML;berpr&uuml;fung, ob beim Lebenslauf &uuml;berhaupt etwas eingetragen wurde
        //Wenn in einer gesamten Zeile nichts eingetragen wurde, wird diese gel&ouml;scht
        foreach($_POST['lebenslauf'] AS $nr_eintrag => $array_eintrag)
        {
            if(trim($array_eintrag['am_von_ll']) == "" AND trim($array_eintrag['bis_ll']) == "" AND trim($array_eintrag['text_ll']) == "")
            {
                unset($_POST['lebenslauf'][$nr_eintrag]);
            }
        }
        //Wenn gar nichts im Lebenslauf eingetragen wurde
        if(empty($_POST['lebenslauf']))
        {
            $ergebnis_check = false; $warnung['lebenslauf_gesamt'] = "Es wurde kein Eintrag im Lebenslauf gemacht!";
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td colspan=\"2\">");
            echo("".$warnung['lebenslauf_gesamt']."");
            echo("</td>\n");
            echo("</tr>\n");
        }
        //Wenn irgend ein Eintrag im Lebenslauf gemacht wurde
        else
        {
            echo("<tr>\n");
            echo("<td colspan=\"2\">\n");
            //Eigene Tabelle f&uuml;r die Darstellung des Lebenslaufs
            echo("<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" style=\"width:100%;\">\n");

            //Den String "$string_word_count" initialisieren, der alle W&ouml;rter in den Feldern "Eintrag" enthalten soll
            //Dieser String wird in der nachfolgenden Schleife erweitert und am Ende die W&ouml;rter gez&auml;hlt
            $string_word_count = "";

            //&UUML;berpr&uuml;fung und Ausgabe der einzelnen Zeilen
            foreach($_POST['lebenslauf'] AS  $nr_eintrag => $array_eintrag)
            {
                //Wenn nichts in das Feld "am/von" eingetragen wurde
                if(trim($array_eintrag['am_von_ll']) == "")
                {$ergebnis_check = false; $warnung[$nr_eintrag]['kein_am_von'] = "Kein Eintrag im Feld \"am/von\"!";}
                //Wenn kein Eintrag im Feld "Eintrag" erfolgt ist
                if(trim($array_eintrag['text_ll']) == "")
                {$ergebnis_check = false; $warnung[$nr_eintrag]['kein_text'] = "Keine Angabe im Feld \"Eintrag\"!";}
                //Wenn Alle Angaben korrekt sind
                if(!isset($warnung[$nr_eintrag]))
                {$ergebnis_check = true;}

                $zeile++;
                //Darstellung der einzelnen Zeilen
                echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
                //Datum
                echo("<td valign=\"top\" style=\"width:21%;\">");
                //Datum "am/von"
                if(isset($warnung[$nr_eintrag]['kein_am_von']))
                {echo("Fehler");}
                else
                {echo("".htmlXspecialchars(trim($array_eintrag['am_von_ll']))."");}
                //Bindestrich
                if(trim($array_eintrag['bis_ll']) != "")
                {echo(" - ");}
                //Datum "bis"
                if(trim($array_eintrag['bis_ll']) != "")
                {echo("".htmlXspecialchars(trim($array_eintrag['bis_ll']))."");}
                echo(":");
                echo("</td>\n");
                //Eintrag
                echo("<td>");
                if(isset($warnung[$nr_eintrag]['kein_text']))
                {echo("".$warnung[$nr_eintrag]['kein_text']."");}
                else
                {echo("".nl2br(htmlXspecialchars(trim($array_eintrag['text_ll'])))."");}
                echo("</td>\n");
                echo("</tr>\n");

                //Den String "$string_word_count" erweitern
                $string_word_count .= "".trim($array_eintrag['text_ll'])." ";
            }

            //&UUML;berpr&uuml;fung, ob in allen Feldern "Eintrag" mehr als 100 W&ouml;rter verwendet wurden
            $anzahl_woerter = str_word_count($string_word_count, 0);
            if($anzahl_woerter > MAX_WOERTER_LEBENSLAUF)
            {
                $ergebnis_check = false; $warnung['worte_ll'] = "Der Lebenslauf enth&auml;lt mehr als 100 W&ouml;rter!";
                echo("<tr".style_input_check($zeile = 1, $ergebnis_check).">\n");
                echo("<td colspan=\"2\">");
                echo("<b>Warnung:</b> Der Lebenslauf enth&auml;lt mehr als 100 W&ouml;rter (insg. ".$anzahl_woerter.")!");
                echo("</td>\n");
                echo("</tr>\n");
            }

            echo("</table>\n");
            echo("</td>\n");
            echo("</tr>\n");
        }

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

        //&UUML;berpr&uuml;fung, ob irgend eine Angabe gemacht wurde
        if(trim($_POST['begruendung']) == "")
        {$ergebnis_check = false; $warnung['keine_begruendung'] = "Es wurde keine Begr&uuml;ndung angegeben, warum der Studiengang Molekulare Medizin an der Universit&auml;t Regensburg angestrebt wird!";}
        else
        {$ergebnis_check = true;}
        echo("<tr".style_input_check($zeile = 1, $ergebnis_check).">\n");
        echo("<td colspan=\"2\">");
        if(isset($warnung['keine_begruendung']))
        {echo("".$warnung['keine_begruendung']."");}
        else
        {echo("".nl2br(htmlXspecialchars(trim($_POST['begruendung'])))."");}
        echo("</td>\n");
        echo("</tr>\n");

        //&UUML;berpr&uuml;fen, ob die Begr&uuml;ndung mehr als 200 Worte enth&auml;lt
        $anzahl_woerter = str_word_count($_POST['begruendung'], 0);
        if(trim($_POST['begruendung']) != "" AND $anzahl_woerter > MAX_WOERTER_BEGRUENDUNG)
        {
            $ergebnis_check = false; $warnung['worte_begruendung'] = "<b>Warnung:</b> Die Begr&uuml;ndung enth&auml;lt mehr als 200 W&ouml;rter (insg. ".$anzahl_woerter.")!";
            echo("<tr".style_input_check($zeile = 1, $ergebnis_check).">\n");
            echo("<td colspan=\"2\">");
            echo("".$warnung['worte_begruendung']."");
            echo("</td>\n");
            echo("</tr>\n");
        }
    }

    ##################
    # Submit-Buttons #
    ##################

    //Leerzeile mit Linie
    echo("<tr>\n");
    echo("<td colspan=\"2\" style=\"height:30px; border-bottom:1px solid #6A6A6A;\" valign=\"bottom\">");
    echo("<span style=\"font-size:10pt;".($active_page == 5 ? " display:none;" : "")."\">Die mit einem Stern (*) gekennzeichneten Felder m&uuml;ssen ausgef&uuml;llt werden!</span>");
    echo("</td>\n");
    echo("</tr>\n");

    echo("<tr>\n");
    //Submit zur R&uuml;ckkehr zur vorherigen Seite
    echo("<td style=\"text-align:left;\">\n");
    if($active_page != 1)
    {echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"zurueck\" value=\"<< zur&uuml;ck\">\n");}
    echo("</td>\n");
    //Submit zur n&auml;chsten Seite oder Button "Absenden"
    echo("<td style=\"text-align:right;\">\n");
    if($active_page < SEITENANZAHL)
    {echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"vor\" value=\"weiter >>\">\n");}
    if($active_page == SEITENANZAHL AND !isset($warnung))
    {echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"aendern\" value=\">> Daten &auml;ndern\">\n");}
    echo("<input type=\"hidden\" name=\"oldpage\" value=\"".$active_page."\">\n");
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
    ##############################################################################################################################
    ##############################################################################################################################

    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
    echo("</form><br />\n");
}
?>
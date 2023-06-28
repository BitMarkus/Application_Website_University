<div class="h1">Neubewerbung</div>

<?php
//Wenn das aktuelle Datum innerhalb der Bewerbungsperiode liegt
if(bewerbungsperiode())
{
    ############################
    # EINTRAG IN DIE DATENBANK #
    ############################

    if(isset($_POST['senden']))
    {
        ### ACHTUNG!!! Warnung, wenn sich der Bewerber bereits beworben hat (Email check) ###
        //In der Tabelle "bewerber" nachsehen, ob diese Email Adresse bereits vorhanden ist
        //Wenn die Email Adresse bereits eingetragen wurde, wird ein Warnhinweis ausgegeben
        if(email_vorhanden($link, trim($_POST['email'])))
        {
            echo("<div class=\"Information_Warnung\">\n");
            echo("<b>ACHTUNG!</b><br />");
            echo("Es ist bereits ein Bewerber mit der Email Adresse <b>\"".trim($_POST['email'])."\"</b> eingetragen! ");
            echo("Wahrscheinlich haben Sie die Bewerbung zwei mal abgeschickt oder Sie haben sich schon einmal bei uns beworben. ");
            echo("Es ist au&szlig;erdem m&ouml;glich, dass sich eine andere Person wissentlich oder unwissentlich mit Angabe Ihrer Email Adresse beworben hat.<br /><br />");
            echo("<img src=\"bilder/Pfeil_re.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"> <span class=\"Link1\"><a href=\"index.php?seite=neuanmeldung\">zur&uuml;ck zum Bewerbungsformular</a></span>");
            echo("</div>\n");
            echo("<div class=\"Abstandhalter_Div\"></div>\n");
        }
        //Wenn die Email Adresse noch nicht eingetragen wurde, werden die Daten in die DB eingetragen
        else
        {
            //Key f&uuml;r die Aktivierung des Accounts zuf&auml;llig generieren
            $key_aktivierung = md5(uniqid(rand(), true));

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

            //Eintrag in die Tabelle "bewerber"
            $sql = "INSERT INTO bewerber
                       (Anrede,
                        Nachname,
                        Vorname,
                        Geburtsdatum,
                        Email,
                        Nationalitaet_fky_Land,
                        Passwort,
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
                        Key_Aktivierung)
                    VALUES
                        ('".$_POST['anrede']."',
                         '".addslashes(htmlXspecialchars(trim($_POST['nachname'])))."',
                         '".addslashes(htmlXspecialchars(trim($_POST['vorname'])))."',
                         '".datum_d_dbdate(trim($_POST['geburtsdatum']))."',
                         '".addslashes(htmlXspecialchars(trim($_POST['email'])))."',
                         '".$_POST['nationalitaet_pky_land']."',
                         MD5('".$_POST['passwort_1']."'),
                         '".addslashes(htmlXspecialchars(trim($_POST['strasse'])))."',
                         '".addslashes(htmlXspecialchars(trim($_POST['hausnr'])))."',
                         '".addslashes(htmlXspecialchars(trim($_POST['adresszusatz'])))."',
                         '".addslashes(htmlXspecialchars(trim($_POST['plz'])))."',
                         '".addslashes(htmlXspecialchars(trim($_POST['ort'])))."',
                         '".$_POST['pky_land']."',
                         NOW(),
                         '".$_POST['pky_hzb']."',
                         '".addslashes(htmlXspecialchars(trim($_POST['hzb_sonstige'])))."',
                         '".trim($_POST['hzb_jahr'])."',
                         '".addslashes(htmlXspecialchars(trim($_POST['hzb_ort'])))."',
                         '".$_POST['hzb_pky_land']."',
                         '".$soziales_jahr."',
                         '".$_POST['pky_ausbildung']."',
                         '".addslashes(htmlXspecialchars(trim($_POST['begruendung'])))."',
                         '".$key_aktivierung."');";
            mysqli_query($link, $sql) OR die(mysqli_error($link));

            //Den Pky des eben eingetragenen Bewerbers auslesen
            $sql = "SELECT
                        pky_Bewerber
                    FROM
                        bewerber
                    ORDER BY
                        pky_Bewerber DESC
                    LIMIT 1;";
            $ergebnis = mysqli_query($link, $sql) OR die(mysqli_error($link));
            $row = mysqli_fetch_assoc($ergebnis);
            $pky_bewerber = $row['pky_Bewerber'];
            mysqli_free_result($ergebnis);

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

            ######################################################################################################################
            //Ausgabe der Zwischensumme zu Testzwecken
            //if(isset($zwischensumme)){echo("<b>Die Zwischensumme ist:</b> ".($zwischensumme == "NULL" ? "nicht berechenbar!" : $zwischensumme)."");}
            ######################################################################################################################

            //Eintrag in die Tabelle "leistungen_bewerber"
            $sql = "INSERT INTO leistungen_bewerber
                       (fky_Bewerber,
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
                        Mathe_End_Punkte,
                        Zwischensumme)
                    VALUES
                        (".$pky_bewerber.",
                         '".$leist_art."',
                         ".$leistung['hzb_note'].",
                         ".$leistung['hzb_punkte'].",
                         ".$nat_belegt.",
                         ".$pky_naturw_fach.",
                         ".$leistung['naturw']['note']['hj1'].",
                         ".$leistung['naturw']['note']['hj2'].",
                         ".$leistung['naturw']['note']['hj3'].",
                         ".$leistung['naturw']['note']['hj4'].",
                         ".$leistung['naturw']['note']['abi'].",
                         ".$leistung['naturw']['punkte']['hj1'].",
                         ".$leistung['naturw']['punkte']['hj2'].",
                         ".$leistung['naturw']['punkte']['hj3'].",
                         ".$leistung['naturw']['punkte']['hj4'].",
                         ".$leistung['naturw']['punkte']['abi'].",
                         ".$mat_belegt.",
                         ".$leistung['mathe']['note']['hj1'].",
                         ".$leistung['mathe']['note']['hj2'].",
                         ".$leistung['mathe']['note']['hj3'].",
                         ".$leistung['mathe']['note']['hj4'].",
                         ".$leistung['mathe']['note']['abi'].",
                         ".$leistung['mathe']['punkte']['hj1'].",
                         ".$leistung['mathe']['punkte']['hj2'].",
                         ".$leistung['mathe']['punkte']['hj3'].",
                         ".$leistung['mathe']['punkte']['hj4'].",
                         ".$leistung['mathe']['punkte']['abi'].",
                         ".$zwischensumme.");";
            mysqli_query($link, $sql) OR die(mysqli_error($link));

            //Eintrag in die Tabelle "lebenslauf_bewerber"
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
                                ('".$pky_bewerber."',
                                 '".$nr_eintrag."',
                                 '".addslashes(htmlXspecialchars(trim($array_eintrag['am_von_ll'])))."',
                                 '".addslashes(htmlXspecialchars(trim($array_eintrag['bis_ll'])))."',
                                 '".addslashes(htmlXspecialchars(trim($array_eintrag['text_ll'])))."');";
                    mysqli_query($link, $sql) OR die(mysqli_error($link));
                    $nr_eintrag++;
                }
            }

            ###################################
            # Versandt der Best&auml;tigungs-Email #
            ###################################

            //Adresse, an welche die Email verschickt wird
            $adresse = "".trim($_POST['email'])."";
            //Betreff
            //$betreff = "Bewerbung f&uuml;r Molekulare Medizin - Universit&auml;t Regensburg htmlXspecialcharshars(trim($_POST['vorname'])).htmlXspecialcharshars(trim($_POST['nachname']))."";
            $betreff = "Bewerbung für Teststudiengang an Testuniversität";
            //Header
            $header = "From: ".EMAIL_SEKRETARIAT."\r\n";
            $header .= "Bcc: ".EMAIL_SEKRETARIAT."\r\n";
            //Inhalt der Mail
            $inhalt = "Sehr ".($_POST['anrede'] == "h" ? "geehrter Herr" : "geehrte Frau")." ".htmlXspecialchars(trim($_POST['nachname']))."!\n\n";
            $inhalt .= "Vielen Dank fuer Ihre Bewerbung. ";
            $inhalt .= "Bitte beachten Sie, dass Ihre Anmeldung erst abgeschlossen ist, wenn sie nachstehenden Link klicken:\n\n";
            $inhalt .= "".LINK_AKTIVIERUNG."&email=".urlencode(trim($_POST['email']))."&key=".$key_aktivierung."\n\n";
            $inhalt .= "Wenn die Aktivierung nicht erfolgreich sein sollte, wenden Sie sich bitte an folgende Email Adresse: ".EMAIL_SEKRETARIAT.".\n\n";
            $inhalt .= "Diese Email wurde automatisch generiert. Sollten Sie diese faelschlicherweise erhalten haben, dann hat sich eine andere Person ";
            $inhalt .= "wissentlich oder unwissentlich unter Angabe Ihrer Email Adresse beworben. In diesem Fall ignorieren Sie die Email bitte. ";
            $inhalt .= "Die angegebenen Daten werden dann aus dem System geloescht.\n\n";
            $inhalt .= "Mit freundlichen Grüssen,\n\n";
            $inhalt .= "Prof. Dr. Max Mustermann";
            //Mail absenden
            $mail_check = @mail($adresse,$betreff,$inhalt,$header);

            //Wenn die Email versendet werden konnte, dann wird ein entsprechender Hinweis ausgegeben
            if($mail_check)
            {
                //Bei erfolgreichem Abschicken erscheint ein entsprechender Hinweis
                echo("<div class=\"Information\">\n");
                echo("<b>Ihre Registrierung war erfolgreich!</b><br />");
                echo("Es wird automatisch eine Email an die angegebene Adresse <b>\"".trim($_POST['email'])."\"</b> versandt. ");
                echo("Diese Email enth&auml;lt einen Link, durch dessen Anklicken Sie Ihre Bewerbung endg&uuml;ltig abschlie&szlig;en. ");
                echo("Bei erfolgreicher Bewerbung k&ouml;nnen Sie sich in den internen Bereich einloggen, von wo aus Sie Ihre Daten einsehen und gegebenenfalls noch &auml;ndern k&ouml;nnen. ");
                echo("Sollten Sie aufgrund technischer Probleme keine Email erhalten, wenden Sie sich bitte an <b>\"".EMAIL_SEKRETARIAT."\"</b>.<br />");
                echo("<b>Bitte beachten Sie, dass Ihre Bewerbung erst erfolgreich abgeschlossen ist, wenn Sie den Ihnen zugesandten Link anklicken!</b>");
                echo("</div>\n");
                echo("<div class=\"Abstandhalter_Div\"></div>\n");
            }
            else
            {
                //Wenn die Email nicht erfolgreich versendet werden konnte
                echo("<div class=\"Information_Warnung\">\n");
                echo("<b>ACHTUNG!</b><br />");
                echo("Ihre Daten wurden zwar im System registriert aber die Email mit dem Aktivierungs-Link konnte nicht versandt werden! ");
                echo("Wenden Sie sich an <b>\"".EMAIL_SEKRETARIAT."\"</b>, um die Aktivierung Ihres Accounts manuell durchzuf&uuml;hren zu lassen. ");
                echo("Bitte f&uuml;llen Sie das Online Formular nicht ein zweites mal aus.");
                echo("</div>\n");
                echo("<div class=\"Abstandhalter_Div\"></div>\n");
            }
        }
    }

    ##################
    # FORMULAR START #
    ##################

    else
    {
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

        ###################################
        # Formular und Haupttabelle Start #
        ###################################

        echo("<form method=\"post\" action=\"index.php?seite=neuanmeldung\">\n");
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
        if(!isset($_POST['anrede'])){$_POST['anrede'] = "";}
        echo("Frau: <input name=\"anrede\" type=\"radio\" value=\"f\" ".(($_POST['anrede'] == "f") ? "checked=\"checked\"" : "")."> ");
        echo("Herr: <input name=\"anrede\" type=\"radio\" value=\"h\" ".(($_POST['anrede'] == "h") ? "checked=\"checked\"" : "").">");
        echo("</td>\n");
        echo("</tr>\n");

        //Nachname
        $zeile++;
        echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Nachname: *");
        echo("</td>\n");
        echo("<td>");
        echo("<input name=\"nachname\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['nachname'])) ? $_POST['nachname'] : ""))."\">");
        echo("</td>\n");
        echo("</tr>\n");

        //Vorname
        $zeile++;
        echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Vorname: *");
        echo("</td>\n");
        echo("<td>");
        echo("<input name=\"vorname\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['vorname'])) ? $_POST['vorname'] : ""))."\">");
        echo("</td>\n");
        echo("</tr>\n");

        //Geburtsdatum
        $zeile++;
        echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Geburtsdatum: *");
        echo("</td>\n");
        echo("<td>");
        echo("<input name=\"geburtsdatum\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".htmlXspecialchars(trim((isset($_POST['geburtsdatum'])) ? $_POST['geburtsdatum'] : ""))."\">");
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
            if(!isset($_POST['nationalitaet_pky_land']) AND $row['pky_Land'] == PKY_DEUTSCHLAND)
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
        echo("<input name=\"strasse\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['strasse'])) ? $_POST['strasse'] : ""))."\">");
        echo(" <input name=\"hausnr\" type=\"text\" size=\"5\" maxlength=\"10\" value=\"".htmlXspecialchars(trim((isset($_POST['hausnr'])) ? $_POST['hausnr'] : ""))."\">");
        echo("</td>\n");
        echo("</tr>\n");

        //Adresszusatz
        $zeile++;
        echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Adresszusatz:");
        echo("</td>\n");
        echo("<td>");
        echo("<input name=\"adresszusatz\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['adresszusatz'])) ? $_POST['adresszusatz'] : ""))."\">");
        echo("</td>\n");
        echo("</tr>\n");

        //PLZ und Wohnort
        $zeile++;
        echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("PLZ/Wohnort: *");
        echo("</td>\n");
        echo("<td>");
        echo("<input name=\"plz\" type=\"text\" size=\"5\" maxlength=\"10\" value=\"".htmlXspecialchars(trim((isset($_POST['plz'])) ? $_POST['plz'] : ""))."\">");
        echo(" <input name=\"ort\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['ort'])) ? $_POST['ort'] : ""))."\">");
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
            if(!isset($_POST['pky_land']) AND $row['pky_Land'] == PKY_DEUTSCHLAND)
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
        echo("Folgende Angaben sind wichtig, um sich in den <b>internen Bereich</b> einzuloggen");
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
        echo("<input name=\"email\" type=\"text\" size=\"30\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['email'])) ? $_POST['email'] : ""))."\">");
        echo("</td>\n");
        echo("</tr>\n");

        //Passwort
        $zeile++;
        echo("<tr style=\"".($active_page == 1 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td class=\"Zeile_Bezeichnung\">");
        echo("Passwort: *");
        echo("</td>\n");
        echo("<td>");
        echo("<input name=\"passwort_1\" type=\"password\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars((isset($_POST['passwort_1'])) ? $_POST['passwort_1'] : "")."\">");
        echo(" <span style=\"font-size:10pt;\"> (Das Passwort muss aus <b>mind. 8 Zeichen</b> bestehen)</span>");
        echo("</td>\n");
        echo("</tr>\n");

        //Passwort (Best&auml;tigung)
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
            if(!isset($_POST['pky_hzb']) AND $row['pky_HZB'] == PKY_ALLG_ABITUR)
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
        echo("<input name=\"hzb_sonstige\" type=\"text\" size=\"30\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['hzb_sonstige'])) ? $_POST['hzb_sonstige'] : ""))."\">");
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
        echo("<input name=\"hzb_jahr\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".htmlXspecialchars(trim((isset($_POST['hzb_jahr'])) ? $_POST['hzb_jahr'] : ""))."\">");
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
        echo("<input name=\"hzb_ort\" type=\"text\" size=\"30\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['hzb_ort'])) ? $_POST['hzb_ort'] : ""))."\">");
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
            if(!isset($_POST['hzb_pky_land']) AND $row['pky_Land'] == PKY_DEUTSCHLAND)
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
        echo("<input name=\"hzb_note\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".htmlXspecialchars(trim((isset($_POST['hzb_note'])) ? $_POST['hzb_note'] : ""))."\">");
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
        if(!isset($_POST['leistungen_art'])){$_POST['leistungen_art'] = "";}
        echo("<input name=\"leistungen_art\" type=\"radio\" value=\"p\" ".(($_POST['leistungen_art'] == "p") ? "checked=\"checked\"" : "")."> Punkte (0-15)<br />");
        echo("<input name=\"leistungen_art\" type=\"radio\" value=\"n\" ".(($_POST['leistungen_art'] == "n") ? "checked=\"checked\"" : "")."> deutsches Schulnotensystem (1,00-6,00)");
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
        if(!isset($_POST['mathe_belegt'])){$_POST['mathe_belegt'] = "";}
        echo("<input name=\"mathe_belegt\" type=\"checkbox\" value=\"0\" ".(($_POST['mathe_belegt'] == "0") ? "checked=\"checked\"" : "")."> <b>nicht zutreffend</b>");
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
            echo("<input name=\"leistungen[mathe][hj".$x."]\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".htmlXspecialchars(trim((isset($_POST['leistungen']['mathe']['hj'.$x])) ? $_POST['leistungen']['mathe']['hj'.$x] : ""))."\">");
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
        echo("<input name=\"leistungen[mathe][abi]\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".htmlXspecialchars(trim((isset($_POST['leistungen']['mathe']['abi'])) ? $_POST['leistungen']['mathe']['abi'] : ""))."\">");
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
        if(!isset($_POST['naturw_belegt'])){$_POST['naturw_belegt'] = "";}
        echo("<input name=\"naturw_belegt\" type=\"checkbox\" value=\"0\" ".(($_POST['naturw_belegt'] == "0") ? "checked=\"checked\"" : "")."> <b>nicht zutreffend</b>");
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
        if(!isset($_POST['pky_naturw_fach']))
        {$selected = " selected=\"selected\"";}
        elseif(isset($_POST['pky_naturw_fach']) AND $_POST['pky_naturw_fach'] == "0")
        {$selected = " selected=\"selected\"";}
        else
        {$selected = "";}
        echo("<option".$selected." value=\"0\">Bitte w&auml;hlen</option>\n");
        $naturw_fach_art = naturw_fach_art($link);
        while($row = mysqli_fetch_assoc($naturw_fach_art))
        {
            if(isset($_POST['pky_naturw_fach']) AND $_POST['pky_naturw_fach'] == $row['pky_naturw_Fach'])
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
            echo("<input name=\"leistungen[naturw][hj".$y."]\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".htmlXspecialchars(trim((isset($_POST['leistungen']['naturw']['hj'.$y])) ? $_POST['leistungen']['naturw']['hj'.$y] : ""))."\">");
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
        echo("<input name=\"leistungen[naturw][abi]\" type=\"text\" size=\"4\" maxlength=\"4\" value=\"".htmlXspecialchars(trim((isset($_POST['leistungen']['naturw']['abi'])) ? $_POST['leistungen']['naturw']['abi'] : ""))."\">");
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

        //Freiwilliges soziales Jahr, Wehrdienst, Zivildienst
        $zeile++;
        echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
        echo("<td colspan=\"2\" style=\"color:#6A6A6A;\">");
        if(!isset($_POST['soz_jahr'])){$_POST['soz_jahr'] = "0";}
        echo("<input name=\"soz_jahr\" type=\"checkbox\" value=\"1\" ".(($_POST['soz_jahr'] == "1") ? "checked=\"checked\"" : "").">");
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
        elseif(isset($_POST['pky_ausbildung']) AND $_POST['pky_ausbildung'] == "0")
        {$selected = " selected=\"selected\"";}
        else
        {$selected = "";}
        echo("<option".$selected." value=\"0\">keine</option>\n");
        $ausbildungen = ausbildungen($link);
        while($row = mysqli_fetch_assoc($ausbildungen))
        {
            if(isset($_POST['pky_ausbildung']) AND $_POST['pky_ausbildung'] == $row['pky_Ausbildung'])
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
        if(!isset($_POST['lebenslauf']) AND !isset($_POST['neuer_eintrag_ll']) AND !isset($_POST['eintrag_ll_loeschen']))
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
        echo("<b>Begr&uuml;ndung *</b>, warum der Studiengang angestrebt wird (max. 200 W&ouml;rter).");
        echo(" Bitte geben Sie hier kurz an, wann Sie im Zeitraum Juni-August ggf. nicht f&uuml;r ein Auswahlgespr&auml;ch zur Verf&uuml;gung stehen. Wir werden versuchen, dies zu ber&uuml;cksichtigen.");
        echo("</td>\n");
        echo("</tr>\n");

        //Begr&uuml;ndung
        echo("<tr style=\"".($active_page == 4 ? "" : "display:none;")."background-color:#EEEEEE;\">\n");
        echo("<td colspan=\"2\" align=\"center\">");
        echo("<textarea name=\"begruendung\" cols=\"100\" rows=\"4\">".htmlXspecialchars(trim((isset($_POST['begruendung'])) ? $_POST['begruendung'] : ""))."</textarea>");
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
            echo(" Wenn alle Pflichtfelder ordnungsgem&auml;&szlig; ausgef&uuml;llt wurden, erscheint rechts unten im Formular die Schaltfl&auml;che \"Absenden\".");
            echo(" Nach Absenden des Formulars k&ouml;nnen Sie Ihre Daten nachtr&auml;glich noch &auml;ndern, wenn Sie sich in den internen Bereich f&uuml;r Bewerber einloggen.");
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
            $zeile++;
            //Zun&auml;chst &uuml;berpr&uuml;fen, ob in das Passwortfeld ein Eintrag gemacht wurde
            if($_POST['passwort_1'] == "")
            {$ergebnis_check = false; $warnung['passwort'] = "Es wurde kein Passwort angegeben!";}
            else
            {
                //&UUML;berpr&uuml;fen, ob das Passwort mind aus 8 Zeichen besteht
                if(strlen($_POST['passwort_1']) < 8)
                {$ergebnis_check = false; $warnung['passwort'] = "Das Passwort muss aus mindestens 8 Zeichen bestehen!";}
                else
                {
                    //&UUML;berpr&uuml;fen, ob eine Best&auml;tigung eingetragen wurde
                    if($_POST['passwort_2'] == "")
                    {$ergebnis_check = false; $warnung['passwort'] = "Es wurde keine Best&auml;tigung f&uuml;r das Passwort angegeben!";}
                    else
                    {
                        //&UUML;berpr&uuml;fen, ob PW und Best&auml;tigung &uuml;bereinstimmen
                        if($_POST['passwort_1'] != $_POST['passwort_2'])
                        {$ergebnis_check = false; $warnung['passwort'] = "Die Best&auml;tigung stimmt nicht mit dem Passwort &uuml;berein (Gro&szlig;- und Kleinschreibung beachten)!";}
                        else
                        {$ergebnis_check = true;}
                    }
                }
            }
            echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
            echo("<td class=\"Zeile_Bezeichnung\">");
            echo("Passwort:");
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

            //Art der Angabe
            //Angabe nur notwendig, wenn Mathe oder ein naturw. Fach auch belegt wurden
            if($_POST['mathe_belegt'] != "0" OR $_POST['naturw_belegt'] != "0")
            {
                $zeile++;
                if($_POST['leistungen_art'] == "")
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
                if($_POST['leistungen_art'] == "n")
                {echo("deutsche Schulnoten (1,00-6,00)");}
                elseif($_POST['leistungen_art'] == "p")
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
            if($_POST['soz_jahr'] == "1")
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
            {$ergebnis_check = false; $warnung['keine_begruendung'] = "Es wurde keine Begr&uuml;ndung angegeben, warum der Studiengang angestrebt wird!";}
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
        {echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"senden\" value=\">> Absenden\">\n");}
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
}
//Wenn das aktuelle Datum NICHT innerhalb der Bewerbungsperiode liegt
else
{
    echo("<div class=\"Information_Warnung\">\n");
    echo("<b>ACHTUNG!</b><br />");
    echo("Das Anmelden im internen Bereich f&uuml;r Bewerber ist nur innerhalb der Bewerbungsperiode m&ouml;glich. ");
    echo("Die diesj&auml;hrige Bewerbungsperiode ist vom ".ANMELDEBEGINN_D_M."".date("Y")." bis zum ".ANMELDEENDE_D_M."".date("Y").".");
    echo("</div>\n");
    echo("<div class=\"Abstandhalter_Div\"></div>\n");
}
?>

<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1"><a href="index.php?seite=start">zur&uuml;ck zur Hauptseite</a></span></span><br/>
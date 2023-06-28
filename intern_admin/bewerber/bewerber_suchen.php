<div class="h1">Bewerber suchen</div>

<?php

#############
# Warnungen #
#############

if(isset($_POST['suchen']) AND !isset($_POST['where_string']))
{
    //Wenn eine Eingabe bei Geburtsdatum gemacht wurde, aber diese ung&uuml;ltig ist
    if(trim($_POST['geburtsdatum']) != "" AND !datum_regex($_POST['geburtsdatum']))
    {
        $warnung['geburtsdatum'] = "Es wurde ein ung&uuml;ltiges Geburtsdatum angegeben!";
    }
    //Wenn eine Eingabe bei Email gemacht wurde, aber diese ung&uuml;ltig ist
    if(trim($_POST['email']) != "" AND !email_regex($_POST['email']))
    {
        $warnung['email'] = "Es wurde eine ung&uuml;ltige Email Adresse angegeben!";
    }
    //Wenn eine Eingabe bei Interne Nummer gemacht wurde, aber diese ung&uuml;ltig ist
    if(trim($_POST['pky']) != "" AND !is_numeric($_POST['pky']))
    {
        $warnung['pky'] = "Es wurde eine ung&uuml;ltige interne Nummer angegeben!";
    }

    //Wenn die Art des Termins angegeben wurde, aber ansonsten keine weitere Angabe gemacht wurde
    if((isset($_POST['datum_uhrzeit_art']) AND $_POST['datum_uhrzeit_art'] != "") AND
       trim($_POST['datum_am']) == "" AND trim($_POST['datum_bis']) == "" AND
       trim($_POST['uhrzeit_um']) == "" AND trim($_POST['uhrzeit_bis']) == "" AND
       !isset($_POST['datum_eingetragen']))
    {
        $warnung['datum_uhrzeit_art'] = "Es wurde kein Suchkriterium f&uuml;r den vereinbarten/tats&auml;chlichen Termin angegeben!";
    }
    //Wenn ein Datum ODER eine Uhrzeit angegeben wurde ODER "Eintrag vorhanden" gew&auml;hlt wurde, aber NICHT die Art des Termins
    elseif((!isset($_POST['datum_uhrzeit_art']) OR $_POST['datum_uhrzeit_art'] == "") AND
           (trim($_POST['datum_am']) != "" OR trim($_POST['datum_bis']) != "" OR
            trim($_POST['uhrzeit_um']) != "" OR trim($_POST['uhrzeit_bis']) != "" OR
            isset($_POST['datum_eingetragen'])))
    {
        $warnung['datum_uhrzeit_art'] = "Es wurde nicht angegeben, ob nach dem vereinbarten oder dem tats&auml;chlichen Termin gesucht werden soll!";
    }
    //Wenn "Eintrag vorhanden" gew&auml;hlt wurde und ein Datum oder eine Uhrzeit angegeben wurde
    elseif(isset($_POST['datum_eingetragen']) AND
           (trim($_POST['datum_am']) != "" OR trim($_POST['datum_bis']) != "" OR
            trim($_POST['uhrzeit_um']) != "" OR trim($_POST['uhrzeit_bis']) != ""))
    {
        $warnung['datum_eingetragen'] = "Es kann entweder nach bestimmten Terminen gesucht werden ODER nach Bewerber, f&uuml;r die noch keine Termine eingetragen wurden!";
    }
    //Datum und Uhrzeiten &uuml;berpr&uuml;fen
    else
    {
        //Datum - am/vom
        if(trim($_POST['datum_am']) != "" AND !datum_regex($_POST['datum_am']))
        {
            $warnung['datum'] = "Es wurde ein ung&uuml;ltiges Datum bei \"Datum - am/vom\" angegeben!";
        }
        else
        {
            //Datum - bis
            if(trim($_POST['datum_bis']) != "" AND !datum_regex($_POST['datum_bis']))
            {
                $warnung['datum'] = "Es wurde ein ung&uuml;ltiges Datum bei \"Datum - bis\" angegeben!";
            }
            else
            {
                //Termin Datum: nur "bis" angegeben
                if(trim($_POST['datum_bis']) != "" AND trim($_POST['datum_am']) == "")
                {
                    $warnung['datum'] = "Wenn ein Datum bei \"Datum - bis\" angegeben wurde, muss auch ein Eintrag bei \"Datum - am/vom\" gemacht werden!";
                }
            }
        }
        //Uhrzeit - um/von
        if(trim($_POST['uhrzeit_um']) != "" AND !uhrzeit_check($_POST['uhrzeit_um']))
        {
            $warnung['uhrzeit'] = "Es wurde eine ung&uuml;ltige Uhrzeit bei \"Uhrzeit - um/von\" angegeben!";
        }
        else
        {
            //Uhrzeit - bis
            if(trim($_POST['uhrzeit_bis']) != "" AND !uhrzeit_check($_POST['uhrzeit_bis']))
            {
                $warnung['uhrzeit'] = "Es wurde eine ung&uuml;ltige Uhrzeit bei \"Uhrzeit - bis\" angegeben!";
            }
            else
            {
                //Uhrzeit: nur "bis" angegeben
                if(trim($_POST['uhrzeit_bis']) != "" AND trim($_POST['uhrzeit_um']) == "")
                {
                    $warnung['uhrzeit'] = "Wenn eine Uhrzeit bei \"Uhrzeit - bis\" angegeben wurde, muss auch ein Eintrag bei \"Uhrzeit - um/von\" gemacht werden!";
                }
            }
        }
    }
}

#########################
# Ausgabe von Warnungen #
#########################

if(isset($_POST['suchen']) AND isset($warnung))
{
    echo("<div class=\"Information_Warnung\">\n");
    echo("<b>Fehler bei der Eingabe!</b><br>");
    echo("<ul style=\"margin:0;\">");
    foreach($warnung AS $var)
    {
        echo("<li>".$var."</li>");
    }
    echo("</ul>");
    echo("</div>\n");
}

#####################################
# Formular zum Suchen von Bewerbern #
#####################################

if(!isset($_POST['suchen']) OR (isset($_POST['suchen']) AND isset($warnung)))
{
    echo("<form method=\"post\" action=\"index.php?seite=intern_admin&intern_a=bewerber_suchen\">\n");
    echo("<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" style=\"width:100%;\">\n");

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

    //Nachname
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\" style=\"width:21%;\">");
    echo("Nachname:");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"nachname\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['nachname'])) ? $_POST['nachname'] : ""))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Vorname
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Vorname:");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"vorname\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['vorname'])) ? $_POST['vorname'] : ""))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Geburtsdatum
    $zeile++;
    if(isset($warnung['geburtsdatum']))
    {$ergebnis_check = false;}else{$ergebnis_check = true;}
    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Geburtsdatum:");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"geburtsdatum\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".htmlXspecialchars(trim((isset($_POST['geburtsdatum'])) ? $_POST['geburtsdatum'] : ""))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Email
    $zeile++;
    if(isset($warnung['email']))
    {$ergebnis_check = false;}else{$ergebnis_check = true;}
    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Email:");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"email\" type=\"text\" size=\"30\" maxlength=\"100\" value=\"".htmlXspecialchars(trim((isset($_POST['email'])) ? $_POST['email'] : ""))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Pky = interne Nummer
    $zeile++;
    if(isset($warnung['pky']))
    {$ergebnis_check = false;}else{$ergebnis_check = true;}
    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Interne Nummer:");
    echo("</td>\n");
    echo("<td>");
    echo("<input name=\"pky\" type=\"text\" size=\"4\" maxlength=\"5\" value=\"".htmlXspecialchars(trim((isset($_POST['pky'])) ? $_POST['pky'] : ""))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //////////////////////////////////////
    /////////////// Status ///////////////
    //////////////////////////////////////

    //Leerzeile
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //&UUML;berschrift
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
    echo("Status");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Status
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Status:");
    echo("</td>\n");
    echo("<td>");

    //Status 1
    echo("<select class=\"Auswahlfeld\" name=\"status_bewerber_1\" size=\"1\">\n");
    //Alle Bewerber
    if(!isset($_POST['status_bewerber_1']) OR (isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == ""))
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"\">Alle Bewerber</option>\n");
    //Endsumme erreicht
    if(isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == 1)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"1\">Endsumme erreicht</option>\n");
    //Endsumme nicht erreicht
    if(isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == 2)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"2\">Endsumme nicht erreicht</option>\n");
    //Endsumme nicht vorhanden
    if(isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == 3)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"3\">Endsumme nicht vorhanden </option>\n");
    //Zwischensumme erreicht
    if(isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == 4)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"4\">Zwischensumme erreicht</option>\n");
    //Zwischensumme nicht erreicht
    if(isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == 5)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"5\">Zwischensumme nicht erreicht</option>\n");
    //Zwischensumme nicht berechenbar
    if(isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == 6)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"6\">Zwischensumme nicht berechenbar</option>\n");
    //Account wurde nicht aktiviert
    if(isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == 7)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"7\">Account wurde nicht aktiviert</option>\n");
    //Bewerbung wurde zur&uuml;ckgezogen
    if(isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == 8)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"8\">Bewerbung wurde zur&uuml;ckgezogen</option>\n");
    //Account wurde gesperrt
    if(isset($_POST['status_bewerber_1']) AND $_POST['status_bewerber_1'] == 9)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"9\">Account wurde gesperrt</option>\n");
    echo("</select>\n");

    echo(" ODER ");

    //Status 2
    echo("<select class=\"Auswahlfeld\" name=\"status_bewerber_2\" size=\"1\">\n");
    //Alle Bewerber
    if(!isset($_POST['status_bewerber_2']) OR (isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == ""))
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"\">Alle Bewerber</option>\n");
    //Endsumme erreicht
    if(isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == 1)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"1\">Endsumme erreicht</option>\n");
    //Endsumme nicht erreicht
    if(isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == 2)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"2\">Endsumme nicht erreicht</option>\n");
    //Endsumme nicht vorhanden
    if(isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == 3)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"3\">Endsumme nicht vorhanden </option>\n");
    //Zwischensumme erreicht
    if(isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == 4)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"4\">Zwischensumme erreicht</option>\n");
    //Zwischensumme nicht erreicht
    if(isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == 5)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"5\">Zwischensumme nicht erreicht</option>\n");
    //Zwischensumme nicht berechenbar
    if(isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == 6)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"6\">Zwischensumme nicht berechenbar</option>\n");
    //Account wurde nicht aktiviert
    if(isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == 7)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"7\">Account wurde nicht aktiviert</option>\n");
    //Bewerbung wurde zur&uuml;ckgezogen
    if(isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == 8)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"8\">Bewerbung wurde zur&uuml;ckgezogen</option>\n");
    //Account wurde gesperrt
    if(isset($_POST['status_bewerber_2']) AND $_POST['status_bewerber_2'] == 9)
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"9\">Account wurde gesperrt</option>\n");
    echo("</select>\n");

    echo("</td>\n");
    echo("</tr>\n");

    //////////////////////////////////////////////////
    /////////////// Jahr der Bewerbung ///////////////
    //////////////////////////////////////////////////

    //Leerzeile
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //&UUML;berschrift
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
    echo("Jahr der Bewerbung/Wiederbewerbung");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Jahr der Bewerbung
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Jahr:");
    echo("</td>\n");
    echo("<td>");
    //Select f&uuml;r die Auswahl des Studienjahrs
    echo("<select class=\"Auswahlfeld\" name=\"jahr_bewerbung\" size=\"1\">\n");
    //Alle Jahre
    if(isset($_POST['jahr_bewerbung']) AND $_POST['jahr_bewerbung'] == "")
    {$select = " selected=\"selected\"";}else{$select = "";}
    echo("<option".$select." value=\"\">Alle Jahre</option>\n");
    for($i=ERSTES_STUDIENJAHR; $i<=date("Y"); $i++)
    {
        if((!isset($_POST['jahr_bewerbung']) AND date("Y") == $i) OR (isset($_POST['jahr_bewerbung']) AND $_POST['jahr_bewerbung'] == $i))
        {$select = " selected=\"selected\"";}else{$select = "";}
        echo("<option".$select." value=\"".$i."\">".$i."</option>\n");
    }
    echo("</select> \n");
    echo("</td>\n");
    echo("</tr>\n");

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

    //Art des Datums/der Uhrzeit
    $zeile++;
    if(isset($warnung['datum_uhrzeit_art']))
    {$ergebnis_check = false;}else{$ergebnis_check = true;}
    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
    echo("<td colspan=\"2\">");
    if(isset($_POST['datum_uhrzeit_art']) AND $_POST['datum_uhrzeit_art'] == "v")
    {$check_art = "checked=\"checked\"";}
    else
    {$check_art = "";}
    echo("<input name=\"datum_uhrzeit_art\" type=\"radio\" value=\"v\" ".$check_art."><b>Vereinbarter Termin</b>");
    if(isset($_POST['datum_uhrzeit_art']) AND $_POST['datum_uhrzeit_art'] == "t")
    {$check_art = "checked=\"checked\"";}
    else
    {$check_art = "";}
    echo(" <input name=\"datum_uhrzeit_art\" type=\"radio\" value=\"t\" ".$check_art."><b>Tats&auml;chlicher Termin</b>");
    echo(" <input name=\"datum_uhrzeit_art\" type=\"radio\" value=\"\">Auswahl r&uuml;ckg&auml;ngig machen");
    echo("</td>\n");
    echo("</tr>\n");

    //Datum
    $zeile++;
    if(isset($warnung['datum']) OR isset($warnung['datum_eingetragen']))
    {$ergebnis_check = false;}else{$ergebnis_check = true;}
    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Datum:");
    echo("</td>\n");
    echo("<td>");
    echo("am/vom <input name=\"datum_am\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".htmlXspecialchars(trim((isset($_POST['datum_am'])) ? $_POST['datum_am'] : ""))."\">");
    echo(" bis <input name=\"datum_bis\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".htmlXspecialchars(trim((isset($_POST['datum_bis'])) ? $_POST['datum_bis'] : ""))."\">");
    echo("</td>\n");
    echo("</tr>\n");

    //Uhrzeit
    $zeile++;
    if(isset($warnung['uhrzeit']) OR isset($warnung['datum_eingetragen']))
    {$ergebnis_check = false;}else{$ergebnis_check = true;}
    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Uhrzeit:");
    echo("</td>\n");
    echo("<td>");
    echo("um/von <input name=\"uhrzeit_um\" type=\"text\" size=\"5\" maxlength=\"5\" value=\"".htmlXspecialchars(trim((isset($_POST['uhrzeit_um'])) ? $_POST['uhrzeit_um'] : ""))."\"> Uhr");
    echo(" bis <input name=\"uhrzeit_bis\" type=\"text\" size=\"5\" maxlength=\"5\" value=\"".htmlXspecialchars(trim((isset($_POST['uhrzeit_bis'])) ? $_POST['uhrzeit_bis'] : ""))."\"> Uhr");
    echo("</td>\n");
    echo("</tr>\n");

    //Noch nicht eingetragen
    $zeile++;
    if(isset($warnung['datum_eingetragen']))
    {$ergebnis_check = false;}else{$ergebnis_check = true;}
    echo("<tr".style_input_check($zeile, $ergebnis_check).">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Ohne Termin:");
    echo("</td>\n");
    echo("<td>");
    if(isset($_POST['datum_eingetragen']) AND $_POST['datum_eingetragen'] == "1")
    {$check_eingetragen = "checked=\"checked\"";}
    else
    {$check_eingetragen = "";}
    echo("<input name=\"datum_eingetragen\" type=\"checkbox\" value=\"1\" ".$check_eingetragen."> Termin wurde noch nicht eingetragen\n");
    echo("</td>\n");
    echo("</tr>\n");


    ///////////////////////////////////////////////////
    /////////////// Kommissionsmitglied ///////////////
    ///////////////////////////////////////////////////

    //Leerzeile
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Leerzeile\">");
    echo("</td>\n");
    echo("</tr>\n");

    //&UUML;berschrift
    echo("<tr>\n");
    echo("<td colspan=\"2\" class=\"Ueberschrift_Zusammenfassung\">");
    echo("Kommissionsmitglied");
    echo("</td>\n");
    echo("</tr>\n");

    //Z&auml;hler f&uuml;r abwechselndes Einf&auml;rben der Zeilen setzen
    $zeile = 0;

    //Kommissionsmitglied
    $zeile++;
    echo("<tr style=\"".($zeile%2 == 0 ? "" : "background-color:#EEEEEE;")."\">\n");
    echo("<td class=\"Zeile_Bezeichnung\">");
    echo("Bei Kommissionsmitglied:");
    echo("</td>\n");
    echo("<td>");
    echo("<select name=\"kommissionsmitglied\" class=\"Auswahlfeld\" size=\"1\">\n");
    if(isset($_POST['kommissionsmitglied']) AND $_POST['kommissionsmitglied'] == "")
    {$selected = " selected=\"selected\"";}
    else
    {$selected = "";}
    echo("<option".$selected." value=\"\">Bitte w&auml;hlen</option>\n");
    $kommissionsmitglieder = kommissionsmitglieder($link);
    while($row = mysqli_fetch_assoc($kommissionsmitglieder))
    {
        if(isset($_POST['kommissionsmitglied']) AND $_POST['kommissionsmitglied'] == $row['pky_Kommissionsmitglied'])
        {$selected = " selected=\"selected\"";}
        else
        {$selected = "";}
        echo("<option".$selected." value=\"".$row['pky_Kommissionsmitglied']."\">".$row['Kommissionsmitglied']."</option>\n");
    }
    mysqli_free_result($kommissionsmitglieder);
    echo("</select>");
    echo("</td>\n");
    echo("</tr>\n");

    //Submit
    echo("<tr>\n");
    echo("<td colspan=\"2\" style=\"border-bottom:1px solid #6A6A6A;\" valign=\"bottom\">");
    echo("</td>\n");
    echo("</tr>\n");

    echo("<tr>\n");
    echo("<td colspan=\"2\">");
    echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"suchen\" value=\">> Suchen\">\n");
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
    echo("</form>\n");
}

########################
# Skript f&uuml;r die Suche #
########################

if(isset($_POST['suchen']) AND !isset($warnung))
{
    ###ARRAY $_POST ANPASSEN###
    //Erst Whitespaces entfernen, dann leere &UUML;bergaben oder Variablen aus dem Submit-Button aus dem Array $_POST l&ouml;schen
    foreach($_POST as $key => $value)
    {
        $value = trim($_POST[$key]);
        if($value == "" OR $value == "Suchen")
        {
            unset($_POST[$key]);
        }
    }

    #####################
    # WHERE-Bedingungen #
    #####################
    $where_array = array();

    ###NACHNAME###
    if(isset($_POST['nachname']))
    {
        $where_array[] = "b.Nachname = '".$_POST['nachname']."'";
    }
    ###VORNAME###
    if(isset($_POST['vorname']))
    {
        $where_array[] = "b.Vorname = '".$_POST['vorname']."'";
    }
    ###GEBURTSDATUM###
    if(isset($_POST['geburtsdatum']))
    {
        $where_array[] = "b.Geburtsdatum = '".datum_d_dbdate($_POST['geburtsdatum'])."'";
    }
    ###EMAIL###
    if(isset($_POST['email']))
    {
        $where_array[] = "b.Email = '".$_POST['email']."'";
    }
    ###PKY = INTERNE NUMMER###
    if(isset($_POST['pky']))
    {
        $where_array[] = "b.pky_Bewerber = ".$_POST['pky']."";
    }
    ###STATUS BEWERBER###
    if(isset($_POST['status_bewerber_1']))
    {
        //Status Bewerber 1
        switch($_POST['status_bewerber_1'])
        {
            //Endsumme erreicht
            case 1:
            $status_1 = "lb.Endsumme IS NOT NULL AND lb.Endsumme >= ".GRENZE_ENDSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Endsumme nicht erreicht
            case 2:
            $status_1 = "lb.Endsumme IS NOT NULL AND lb.Endsumme < ".GRENZE_ENDSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Endsumme nicht vorhanden
            case 3:
            $status_1 = "lb.Endsumme IS NULL AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Zwischensumme erreicht
            case 4:
            $status_1 = "lb.Zwischensumme IS NOT NULL AND lb.Zwischensumme >= ".GRENZE_ZWISCHENSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Zwischensumme nicht erreicht
            case 5:
            $status_1 = "lb.Zwischensumme IS NOT NULL AND lb.Zwischensumme < ".GRENZE_ZWISCHENSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Zwischensumme nicht berechenbar
            case 6:
            $status_1 = "lb.Zwischensumme IS NULL AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Account wurde nicht aktiviert
            case 7:
            $status_1 = "b.Key_Aktivierung IS NOT NULL AND b.Account_gesperrt IS NULL";
            break;
            //Bewerbung wurde zur&uuml;ckgezogen
            case 8:
            $status_1 = "b.Bewerbung_zurueckgezogen = 1 AND b.Account_gesperrt IS NULL";
            break;
            //Account wurde gesperrt
            case 9:
            $status_1 = "b.Account_gesperrt = 1";
            break;
        }
    }
    if(isset($_POST['status_bewerber_2']))
    {
        //Status Bewerber 1
        switch($_POST['status_bewerber_2'])
        {
            //Endsumme erreicht
            case 1:
            $status_2 = "lb.Endsumme IS NOT NULL AND lb.Endsumme >= ".GRENZE_ENDSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Endsumme nicht erreicht
            case 2:
            $status_2 = "lb.Endsumme IS NOT NULL AND lb.Endsumme < ".GRENZE_ENDSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Endsumme nicht vorhanden
            case 3:
            $status_2 = "lb.Endsumme IS NULL AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Zwischensumme erreicht
            case 4:
            $status_2 = "lb.Zwischensumme IS NOT NULL AND lb.Zwischensumme >= ".GRENZE_ZWISCHENSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Zwischensumme nicht erreicht
            case 5:
            $status_2 = "lb.Zwischensumme IS NOT NULL AND lb.Zwischensumme < ".GRENZE_ZWISCHENSUMME." AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Zwischensumme nicht berechenbar
            case 6:
            $status_2 = "lb.Zwischensumme IS NULL AND b.Key_Aktivierung IS NULL AND b.Bewerbung_zurueckgezogen IS NULL AND b.Account_gesperrt IS NULL";
            break;
            //Account wurde nicht aktiviert
            case 7:
            $status_2 = "b.Key_Aktivierung IS NOT NULL AND b.Account_gesperrt IS NULL";
            break;
            //Bewerbung wurde zur&uuml;ckgezogen
            case 8:
            $status_2 = "b.Bewerbung_zurueckgezogen = 1 AND b.Account_gesperrt IS NULL";
            break;
            //Account wurde gesperrt
            case 9:
            $status_2 = "b.Account_gesperrt = 1";
            break;
        }
    }
    if(isset($status_1) AND !isset($status_2))
    {
        $where_array[] = $status_1;
    }
    elseif(!isset($status_1) AND isset($status_2))
    {
        $where_array[] = $status_2;
    }
    elseif(isset($status_1) AND isset($status_2))
    {
        $where_array[] = "((".$status_1.") OR (".$status_2."))";
    }
    ###JAHR BEWERBUNG/WIEDERBEWERBUNG###
    if(isset($_POST['jahr_bewerbung']))
    {
        $where_array[] = "((b.Datum_Bewerbung BETWEEN '".$_POST['jahr_bewerbung']."-01-01' AND '".($_POST['jahr_bewerbung']+1)."-01-01') OR (b.Datum_Wiederbewerbung BETWEEN '".$_POST['jahr_bewerbung']."-01-01' AND '".($_POST['jahr_bewerbung']+1)."-01-01'))";
    }

    ###AUSWAHLGESPR&AUML;CH###
    if(isset($_POST['datum_uhrzeit_art']))
    {
        //Wenn nach eingetragenen Terminen gesucht werden soll
        if(!isset($_POST['datum_eingetragen']))
        {
            ###VEREINBARTER TERMIN###
            if($_POST['datum_uhrzeit_art'] == "v")
            {
                //Wenn nur das "Datum am/vom" eingetragen wurde
                if(isset($_POST['datum_am']) AND !isset($_POST['datum_bis']))
                {
                    $where_array[] = "tkb.Datum_Termin = '".datum_d_dbdate($_POST['datum_am'])."'";
                }
                //Wenn das Datum von-bis eingetragen wurde
                if(isset($_POST['datum_am']) AND isset($_POST['datum_bis']))
                {
                    $where_array[] = "tkb.Datum_Termin BETWEEN '".datum_d_dbdate($_POST['datum_am'])."' AND '".datum_d_dbdate($_POST['datum_bis'])."'";
                }
                ###TERMIN UHRZEIT###
                //Wenn nur die "Uhrzeit um/von" eingetragen wurde
                if(isset($_POST['uhrzeit_um']) AND !isset($_POST['uhrzeit_bis']))
                {
                    $where_array[] = "tkb.Uhrzeit_Termin = '".$_POST['uhrzeit_um']."'";
                }
                //Wenn die Uhrzeit von-bis eingetragen wurde
                if(isset($_POST['uhrzeit_um']) AND isset($_POST['uhrzeit_bis']))
                {
                    $where_array[] = "tkb.Uhrzeit_Termin BETWEEN '".$_POST['uhrzeit_um']."' AND '".$_POST['uhrzeit_bis']."'";
                }
            }
            ###TATS&AUML;CHLICHER TERMIN###
            if($_POST['datum_uhrzeit_art'] == "t")
            {
                //Wenn nur das "Datum am/vom" eingetragen wurde
                if(isset($_POST['datum_am']) AND !isset($_POST['datum_bis']))
                {
                    $where_array[] = "ab.Auswahlgespraech_Datum = '".datum_d_dbdate($_POST['datum_am'])."'";
                }
                //Wenn das Datum von-bis eingetragen wurde
                if(isset($_POST['datum_am']) AND isset($_POST['datum_bis']))
                {
                    $where_array[] = "ab.Auswahlgespraech_Datum BETWEEN '".datum_d_dbdate($_POST['datum_am'])."' AND '".datum_d_dbdate($_POST['datum_bis'])."'";
                }
                ###AUSWAHLGESPR&AUML;CH UHRZEIT###
                //Wenn nur die "Uhrzeit um/von" eingetragen wurde
                if(isset($_POST['uhrzeit_um']) AND !isset($_POST['uhrzeit_bis']))
                {
                    $where_array[] = "ab.Auswahlgespraech_Uhrzeit_von = '".$_POST['uhrzeit_um']."'";
                }
                //Wenn die Uhrzeit von-bis eingetragen wurde
                if(isset($_POST['uhrzeit_um']) AND isset($_POST['uhrzeit_bis']))
                {
                    $where_array[] = "ab.Auswahlgespraech_Uhrzeit_von BETWEEN '".$_POST['uhrzeit_um']."' AND '".$_POST['uhrzeit_bis']."'";
                }
            }
        }
        //Wenn nach Bewerbern gesucht werden soll, f&uuml;r die noch KEINE Termine eingetragen wurden
        else
        {
            ###VEREINBARTER TERMIN###
            if($_POST['datum_uhrzeit_art'] == "v")
            {
                $where_array[] = "tkb.Datum_Termin IS NULL";
            }
            ###TATS&AUML;CHLICHER TERMIN###
            if($_POST['datum_uhrzeit_art'] == "t")
            {
                $where_array[] = "ab.Auswahlgespraech_Datum IS NULL AND (ab.Erschienen = 1 OR ab.Erschienen IS NULL)";
            }
        }
    }

    ###KOMMISSIONSMITGLIED###
    if(isset($_POST['kommissionsmitglied']))
    {
        $where_array[] = "(tkb.fky_Kommissionsmitglied_1 = ".$_POST['kommissionsmitglied']." OR
                           tkb.fky_Kommissionsmitglied_2 = ".$_POST['kommissionsmitglied']." OR
                           tkb.fky_Kommissionsmitglied_3 = ".$_POST['kommissionsmitglied']." OR
                           tkb.fky_Kommissionsmitglied_4 = ".$_POST['kommissionsmitglied'].")";
    }

    //Wenn das Array "$search_array" NICHT leer ist (dh. es wurde mindestens eine Angabe gemacht)...
    if(!empty($where_array) OR isset($_POST['where_string']))
    {
        #######################################################
        ### UND-Abfrage aus dem Array $search_array basteln ###
        #######################################################

        //Wenn kein "WHERE-String" per $_POST &uuml;bergeben wurde
        //Die &UUML;bergabe des "WHERE-String" per $_POST ist wichtig f&uuml;r die Bl&auml;tterfunktion
        if(!isset($_POST['where_string']))
        {
            $where_string = "WHERE (".$where_array[0].")";
            if(isset($where_array[1]))
            {
                for($i=1; $i<sizeof($where_array); $i++)
                {
                    $where_string .= " AND (".$where_array[$i].")";
                }
            }
        }
        else
        {
            $where_string = $_POST['where_string'];
        }

        ###################################
        # Anzahl der Datens&auml;tze ermitteln #
        ###################################

        //Anzahl der Datens&auml;tze auslesen
        $sql = "SELECT
                    COUNT(*) as Anzahl
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
                LEFT JOIN
                    auswahlgespraech_bewerber ab
                ON
                    b.pky_Bewerber = ab.fky_Bewerber
                ".$where_string.";";
        $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
        $row = mysqli_fetch_assoc($result);
        $anzahl_datensatz = $row['Anzahl'];
        mysqli_free_result($result);

        //Wenn es Suchergebnisse gibt...
        if($anzahl_datensatz > 0)
        {
            #############################
            # Versteckte Formularfelder #
            #############################

            //Allgemeine versteckte Felder
            $hidden_fields_allg = "<input type=\"hidden\" name=\"suchen\">\n";
            $hidden_fields_allg .= "<input type=\"hidden\" name=\"where_string\" value=\"".$where_string."\">\n";
            if(isset($_POST['datum_uhrzeit_art']) AND $_POST['datum_uhrzeit_art'] == "t")
            {$hidden_fields_allg .= "<input type=\"hidden\" name=\"datum_uhrzeit_art\" value=\"t\">\n";}
            else
            {$hidden_fields_allg .= "<input type=\"hidden\" name=\"datum_uhrzeit_art\" value=\"v\">\n";}
            //Versteckte Felder f&uuml;r die Bl&auml;tterfunktion
            $hidden_fields_bf = "";
            if(isset($_POST['sort_pky']) AND $_POST['sort_pky'] == "ASC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_pky\" value=\"ASC\">\n";}
            elseif(isset($_POST['sort_pky']) AND $_POST['sort_pky'] == "DESC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_pky\" value=\"DESC\">\n";}
            elseif(isset($_POST['sort_name']) AND $_POST['sort_name'] == "ASC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_name\" value=\"ASC\">\n";}
            elseif(isset($_POST['sort_name']) AND $_POST['sort_name'] == "DESC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_name\" value=\"DESC\">\n";}
            elseif(isset($_POST['sort_art_t']) AND $_POST['sort_art_t'] == "ASC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_art_t\" value=\"ASC\">\n";}
            elseif(isset($_POST['sort_art_t']) AND $_POST['sort_art_t'] == "DESC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_art_t\" value=\"DESC\">\n";}
            elseif(isset($_POST['sort_art_v']) AND $_POST['sort_art_v'] == "ASC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_art_v\" value=\"ASC\">\n";}
            elseif(isset($_POST['sort_art_v']) AND $_POST['sort_art_v'] == "DESC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_art_v\" value=\"DESC\">\n";}
            elseif(isset($_POST['sort_status']) AND $_POST['sort_status'] == "ASC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_status\" value=\"ASC\">\n";}
            elseif(isset($_POST['sort_status']) AND $_POST['sort_status'] == "DESC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_status\" value=\"DESC\">\n";}
            elseif(isset($_POST['sort_zs']) AND $_POST['sort_zs'] == "ASC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_zs\" value=\"ASC\">\n";}
            elseif(isset($_POST['sort_zs']) AND $_POST['sort_zs'] == "DESC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_zs\" value=\"DESC\">\n";}
            elseif(isset($_POST['sort_ag']) AND $_POST['sort_ag'] == "ASC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_ag\" value=\"ASC\">\n";}
            elseif(isset($_POST['sort_ag']) AND $_POST['sort_ag'] == "DESC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_ag\" value=\"DESC\">\n";}
            elseif(isset($_POST['sort_es']) AND $_POST['sort_es'] == "ASC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_es\" value=\"ASC\">\n";}
            elseif(isset($_POST['sort_es']) AND $_POST['sort_es'] == "DESC")
            {$hidden_fields_bf .= "<input type=\"hidden\" name=\"sort_es\" value=\"DESC\">\n";}

            ##############################
            # Sortierkriterien festlegen #
            ##############################

            if(isset($_POST['sort_pky']) AND $_POST['sort_pky'] == "ASC")
            {$order_by_string = "b.pky_Bewerber ASC";}
            elseif(isset($_POST['sort_pky']) AND $_POST['sort_pky'] == "DESC")
            {$order_by_string = "b.pky_Bewerber DESC";}
            elseif(isset($_POST['sort_name']) AND $_POST['sort_name'] == "ASC")
            {$order_by_string = "b.Nachname ASC, b.Vorname ASC";}
            elseif(isset($_POST['sort_name']) AND $_POST['sort_name'] == "DESC")
            {$order_by_string = "b.Nachname DESC, b.Vorname DESC";}
            elseif(isset($_POST['sort_art_t']) AND $_POST['sort_art_t'] == "ASC")
            {$order_by_string = "ab.Auswahlgespraech_Datum ASC, ab.Auswahlgespraech_Uhrzeit_von ASC";}
            elseif(isset($_POST['sort_art_t']) AND $_POST['sort_art_t'] == "DESC")
            {$order_by_string = "ab.Auswahlgespraech_Datum DESC, ab.Auswahlgespraech_Uhrzeit_von DESC";}
            elseif(isset($_POST['sort_art_v']) AND $_POST['sort_art_v'] == "ASC")
            {$order_by_string = "tkb.Datum_Termin ASC, tkb.Uhrzeit_Termin ASC";}
            elseif(isset($_POST['sort_art_v']) AND $_POST['sort_art_v'] == "DESC")
            {$order_by_string = "tkb.Datum_Termin DESC, tkb.Uhrzeit_Termin DESC";}
            elseif(isset($_POST['sort_status']) AND $_POST['sort_status'] == "ASC")
            {$order_by_string = "lb.Endsumme DESC, b.Account_gesperrt ASC, b.Bewerbung_zurueckgezogen ASC, b.Key_Aktivierung ASC, lb.Zwischensumme DESC, b.Nachname ASC";}
            elseif(isset($_POST['sort_status']) AND $_POST['sort_status'] == "DESC")
            {$order_by_string = "lb.Endsumme ASC, b.Account_gesperrt DESC, b.Bewerbung_zurueckgezogen DESC, b.Key_Aktivierung DESC, lb.Zwischensumme ASC, b.Nachname DESC";}
            elseif(isset($_POST['sort_zs']) AND $_POST['sort_zs'] == "ASC")
            {$order_by_string = "lb.Zwischensumme ASC";}
            elseif(isset($_POST['sort_zs']) AND $_POST['sort_zs'] == "DESC")
            {$order_by_string = "lb.Zwischensumme DESC";}
            elseif(isset($_POST['sort_ag']) AND $_POST['sort_ag'] == "ASC")
            {$order_by_string = "lb.Auswahlgespraech_Summe ASC";}
            elseif(isset($_POST['sort_ag']) AND $_POST['sort_ag'] == "DESC")
            {$order_by_string = "lb.Auswahlgespraech_Summe DESC";}
            elseif(isset($_POST['sort_es']) AND $_POST['sort_es'] == "ASC")
            {$order_by_string = "lb.Endsumme ASC";}
            elseif(isset($_POST['sort_es']) AND $_POST['sort_es'] == "DESC")
            {$order_by_string = "lb.Endsumme DESC";}
            //Standardm&auml;&szlig;ig wird nach dem Status sortiert
            else
            {$order_by_string = "lb.Endsumme DESC, b.Account_gesperrt ASC, b.Bewerbung_zurueckgezogen ASC, b.Key_Aktivierung ASC, lb.Zwischensumme DESC, b.Nachname ASC";}

            ######################################
            # Anzahl der Suchergebnisse ausgeben #
            ######################################

            echo("<div class=\"Information\" style=\"text-align:center; margin-bottom:5px;\">\n");
            echo("Anzahl der Suchergebnisse: <b>".$anzahl_datensatz."</b>");
            echo("</div>\n");

            ##################
            # EXPORTFUNKTION #
            ##################

            echo("<div style=\"margin-bottom:5px;\">\n");
            echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
            //Versteckte Felder
            echo("".$hidden_fields_allg."");
            echo("".$hidden_fields_bf."");
            echo("<input type=\"hidden\" name=\"order_by_string\" value=\"".$order_by_string."\">\n");
            echo("<input type=\"hidden\" name=\"exportieren\" value=\"exportieren\">\n");
            echo("<input class=\"Buttons_Unten\" type=\"submit\" value=\">> Suchergebnisse exportieren\">\n");
            echo("</form>\n");
            echo("</div>\n");

            #######################
            ### BL&AUML;TTERFUNKTION ###
            #######################

            //wenn kein Datensatz vorhanden ist
            if($anzahl_datensatz == 0)
            {$anzahl_datensatz = 1;}
            //Anzahl der Seiten (aufgerundet)
            $anzahl_subseiten = ceil($anzahl_datensatz / ANZAHL_DATENSATZ_PRO_SUBSEITE_SUCHE);
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
            $limit = "LIMIT ".($subseite - 1) * ANZAHL_DATENSATZ_PRO_SUBSEITE_SUCHE.", ".ANZAHL_DATENSATZ_PRO_SUBSEITE_SUCHE."";

            //wenn die Anzahl der Unterseiten nur 1 betr&auml;gt, wird die Bl&auml;tterfunktion nicht angezeigt
            if($anzahl_subseiten > 1)
            {
                //Tabelle Start
                echo("<table cellpadding=\"0\" cellspacing=\"2\" class=\"Tabelle_Blaettern\" border=\"0\" style=\"margin:0 0 10px 0;\">\n");
                echo("<tr>\n");

                //Button f&uuml;r erste Seite
                echo("<td style=\"width:40px;\">");
                echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
                //Versteckte Felder
                echo("".$hidden_fields_allg."");
                echo("".$hidden_fields_bf."");
                //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
                echo("<input type=\"hidden\" name=\"subseite\" value=\"1\">\n");
                echo("<input type=\"submit\" name=\"erste_subseite\" value=\"<<\" style=\"width:40px;\">\n");
                echo("</form>\n");
                echo("</td>\n");

                //Button f&uuml;r Seite zur&uuml;ck
                echo("<td style=\"width:40px;\">");
                echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
                //Versteckte Felder
                echo("".$hidden_fields_allg."");
                echo("".$hidden_fields_bf."");
                //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
                echo("<input type=\"hidden\" name=\"subseite\" value=\"".($subseite-1)."\">\n");
                echo("<input type=\"submit\" name=\"subseite_zurueck\" value=\"<\" style=\"width:40px;\">\n");
                echo("</form>\n");
                echo("</td>\n");

                //Button f&uuml;r gezielten Sprung zu einer Seite und Formular f&uuml;r die Angabe der Seitenzahl und Angabe der Seitenzahl insgesamt
                echo("<td align=\"center\">");
                echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
                //Versteckte Felder
                echo("".$hidden_fields_allg."");
                echo("".$hidden_fields_bf."");
                //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
                echo("<input type=\"submit\" name=\"seite_anzeigen\" value=\"Seite\">\n");
                echo("<input type=\"text\" name=\"subseite\" value=\"".$subseite."\" size=\"3\" maxlength=\"3\">\n");
                echo(" von ".$anzahl_subseiten."\n");
                echo("</form>\n");
                echo("</td>\n");

                //Button f&uuml;r Seite vor
                echo("<td style=\"width:40px;\">");
                echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
                //Versteckte Felder
                echo("".$hidden_fields_allg."");
                echo("".$hidden_fields_bf."");
                //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
                echo("<input type=\"hidden\" name=\"subseite\" value=\"".($subseite+1)."\">\n");
                echo("<input type=\"submit\" name=\"subseite_vor\" value=\">\" style=\"width:40px;\">\n");
                echo("</form>\n");
                echo("</td>\n");

                //Button f&uuml;r letzte Seite
                echo("<td style=\"width:40px;\">");
                echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
                //Versteckte Felder
                echo("".$hidden_fields_allg."");
                echo("".$hidden_fields_bf."");
                //$_POST Variable f&uuml;r die Subseite versteckt &uuml;bergeben
                echo("<input type=\"hidden\" name=\"subseite\" value=\"".$anzahl_subseiten."\">\n");
                echo("<input type=\"submit\" name=\"letzte_subseite\" value=\">>\" style=\"width:40px;\">\n");
                echo("</form>\n");
                echo("</td>\n");

                echo("</tr>\n");
                echo("</table>\n");
            }

            #######################
            # Datens&auml;tze auslesen #
            #######################

            $sql = "SELECT
                        b.pky_Bewerber,
                        b.Anrede,
                        b.Nachname,
                        b.Vorname,
                        b.Geburtsdatum,
                        b.Email,
                        b.Datum_Bewerbung,
                        b.Datum_Wiederbewerbung,
                        b.Key_Aktivierung,
                        b.Account_gesperrt,
                        b.Bewerbung_zurueckgezogen,
                        lb.Zwischensumme,
                        lb.Auswahlgespraech_Summe,
                        lb.Endsumme,
                        tkb.Datum_Termin,
                        tkb.Uhrzeit_Termin,
                        ab.Erschienen,
                        ab.Auswahlgespraech_Datum,
                        ab.Auswahlgespraech_Uhrzeit_von,
                        ab.Auswahlgespraech_Uhrzeit_bis
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
                    LEFT JOIN
                        auswahlgespraech_bewerber ab
                    ON
                        b.pky_Bewerber = ab.fky_Bewerber
                    ".$where_string."
                    ORDER BY
                        ".$order_by_string."
                    ".$limit.";";
            $ergebnis = mysqli_query($link, $sql) OR die(mysqli_error($link));

            ##################################
            # &UUML;BERSICHTSANZEIGE DER BEWERBER #
            ##################################

            echo("<table id=\"checkbox\" class=\"Tabelle_Uebersicht\">\n");

            echo("<tr>\n");
            //leere Reihe wegen Checkboxen
            echo("<td style=\"width:2%;\"></td>\n");

            //Interne Nummer (=Pky)
            echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:5%;\">");
            #SORT#
            echo("<div style=\"float:right;margin:5px 2px 0 2px;\">\n");
            echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
            //Versteckte Felder
            echo("".$hidden_fields_allg."");
            if(isset($_POST['sort_pky']) AND $_POST['sort_pky'] == "ASC")
            {
                echo("<input type=\"hidden\" name=\"sort_pky\" value=\"DESC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_DESC.gif\" alt=\"Sort DESC\">\n");
            }
            else
            {
                echo("<input type=\"hidden\" name=\"sort_pky\" value=\"ASC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_ASC.gif\" alt=\"Sort ASC\">\n");
            }
            echo("</form>\n");
            echo("</div>\n");
            #SORT ENDE#
            echo("ID");
            echo("</td>\n");

            //Name
            echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"\">");
            #SORT#
            echo("<div style=\"float:right;margin:5px 2px 0 2px;\">\n");
            echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
            //Versteckte Felder
            echo("".$hidden_fields_allg."");
            if(isset($_POST['sort_name']) AND $_POST['sort_name'] == "ASC")
            {
                echo("<input type=\"hidden\" name=\"sort_name\" value=\"DESC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_DESC.gif\" alt=\"Sort DESC\">\n");
            }
            else
            {
                echo("<input type=\"hidden\" name=\"sort_name\" value=\"ASC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_ASC.gif\" alt=\"Sort ASC\">\n");
            }
            echo("</form>\n");
            echo("</div>\n");
            #SORT ENDE#
            echo("Name");
            echo("</td>\n");

            //vereinbarter oder tats&auml;chlicher Termin
            echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:23%;\">");
            #SORT#
            echo("<div style=\"float:right;margin:5px 2px 0 2px;\">\n");
            echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
            //Versteckte Felder
            echo("".$hidden_fields_allg."");
            if(isset($_POST['datum_uhrzeit_art']) AND $_POST['datum_uhrzeit_art'] == "t")
            {
                if(isset($_POST['sort_art_t']) AND $_POST['sort_art_t'] == "ASC")
                {
                    echo("<input type=\"hidden\" name=\"sort_art_t\" value=\"DESC\">\n");
                    echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_DESC.gif\" alt=\"Sort DESC\">\n");
                }
                else
                {
                    echo("<input type=\"hidden\" name=\"sort_art_t\" value=\"ASC\">\n");
                    echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_ASC.gif\" alt=\"Sort ASC\">\n");
                }
            }
            else
            {
                if(isset($_POST['sort_art_v']) AND $_POST['sort_art_v'] == "ASC")
                {
                    echo("<input type=\"hidden\" name=\"sort_art_v\" value=\"DESC\">\n");
                    echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_DESC.gif\" alt=\"Sort DESC\">\n");
                }
                else
                {
                    echo("<input type=\"hidden\" name=\"sort_art_v\" value=\"ASC\">\n");
                    echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_ASC.gif\" alt=\"Sort ASC\">\n");
                }
            }
            echo("</form>\n");
            echo("</div>\n");
            #SORT ENDE#
            if(isset($_POST['datum_uhrzeit_art']) AND $_POST['datum_uhrzeit_art'] == "t")
            {echo("Im Auswahlgespr&auml;ch am/um");}
            else
            {echo("Termin Auswahlgespr&auml;ch");}
            echo("</td>\n");

            //Status
            echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:26%;\">");
            #SORT#
            echo("<div style=\"float:right;margin:5px 2px 0 2px;\">\n");
            echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
            //Versteckte Felder
            echo("".$hidden_fields_allg."");
            if(isset($_POST['sort_status']) AND $_POST['sort_status'] == "ASC")
            {
                echo("<input type=\"hidden\" name=\"sort_status\" value=\"DESC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_DESC.gif\" alt=\"Sort DESC\">\n");
            }
            else
            {
                echo("<input type=\"hidden\" name=\"sort_status\" value=\"ASC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_ASC.gif\" alt=\"Sort ASC\">\n");
            }
            echo("</form>\n");
            echo("</div>\n");
            #SORT ENDE#
            echo("Status");
            echo("</td>\n");

            //Zwischensumme
            echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:5%;\">");
            #SORT#
            echo("<div style=\"float:right;margin:5px 2px 0 2px;\">\n");
            echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
            //Versteckte Felder
            echo("".$hidden_fields_allg."");
            if(isset($_POST['sort_zs']) AND $_POST['sort_zs'] == "ASC")
            {
                echo("<input type=\"hidden\" name=\"sort_zs\" value=\"DESC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_DESC.gif\" alt=\"Sort DESC\">\n");
            }
            else
            {
                echo("<input type=\"hidden\" name=\"sort_zs\" value=\"ASC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_ASC.gif\" alt=\"Sort ASC\">\n");
            }
            echo("</form>\n");
            echo("</div>\n");
            #SORT ENDE#
            echo("ZS");
            echo("</td>\n");

            //Punkte im Auswahlgespr&auml;ch
            echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:5%;\">");
            #SORT#
            echo("<div style=\"float:right;margin:5px 2px 0 2px;\">\n");
            echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
            //Versteckte Felder
            echo("".$hidden_fields_allg."");
            if(isset($_POST['sort_ag']) AND $_POST['sort_ag'] == "ASC")
            {
                echo("<input type=\"hidden\" name=\"sort_ag\" value=\"DESC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_DESC.gif\" alt=\"Sort DESC\">\n");
            }
            else
            {
                echo("<input type=\"hidden\" name=\"sort_ag\" value=\"ASC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_ASC.gif\" alt=\"Sort ASC\">\n");
            }
            echo("</form>\n");
            echo("</div>\n");
            #SORT ENDE#
            echo("AG");
            echo("</td>\n");

            //Endsumme
            echo("<td class=\"Tabelle_Uebersicht_Rubrik\" style=\"width:5%;\">");
            #SORT#
            echo("<div style=\"float:right;margin:5px 2px 0 2px;\">\n");
            echo("<form action=\"index.php?seite=".$_GET['seite']."&intern_a=".$_GET['intern_a']."\" method=\"post\">\n");
            //Versteckte Felder
            echo("".$hidden_fields_allg."");
            if(isset($_POST['sort_es']) AND $_POST['sort_es'] == "ASC")
            {
                echo("<input type=\"hidden\" name=\"sort_es\" value=\"DESC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_DESC.gif\" alt=\"Sort DESC\">\n");
            }
            else
            {
                echo("<input type=\"hidden\" name=\"sort_es\" value=\"ASC\">\n");
                echo("<input type=\"image\" src=\"./bilder/Pfeil_Sort_ASC.gif\" alt=\"Sort ASC\">\n");
            }
            echo("</form>\n");
            echo("</div>\n");
            #SORT ENDE#
            echo("ES");
            echo("</td>\n");
            echo("</tr>\n");

            //Formular f&uuml;r die Auswahl der Bewerber Start
            echo("<form action=\"index.php?seite=intern_admin&intern_a=bewerber_aktionen\" method=\"post\">\n");

            while($datensatz = mysqli_fetch_assoc($ergebnis))
            {
                //Zeilenformatierung und Statustext ermitteln
                $format = bewerber_status_zeile($datensatz['Zwischensumme'], $datensatz['Endsumme'], $datensatz['Key_Aktivierung'], $datensatz['Account_gesperrt'], $datensatz['Bewerbung_zurueckgezogen']);

                echo("<tr valign=\"top\" style=\"text-align:left; background-color:".$format['style_bg'].";color:".$format['style_color'].";\">\n");
                //Checkboxen
                echo("<td><input type=\"checkbox\" name=\"auswahl[]\" value=\"".$datensatz['pky_Bewerber']."\"></td>\n");
                //Interne Nummer (=Pky)
                echo("<td style=\"border:1px solid ".$format['style_color'].";\"><b>".$datensatz['pky_Bewerber']."</b></td>\n");
                //Name
                echo("<td style=\"border:1px solid ".$format['style_color'].";\"><b>".$datensatz['Nachname']." ".$datensatz['Vorname']."</b></td>\n");
                //vereinbarter oder tats&auml;chlicher Termin
                echo("<td style=\"border:1px solid ".$format['style_color'].";\">");
                if(isset($_POST['datum_uhrzeit_art']) AND $_POST['datum_uhrzeit_art'] == "t")
                {
                    if($datensatz['Auswahlgespraech_Datum'] == NULL)
                    {echo("keine Angabe!");}
                    else
                    {echo("".datum_dbdate_d($datensatz['Auswahlgespraech_Datum'])." (".cut_sec($datensatz['Auswahlgespraech_Uhrzeit_von'])."-".cut_sec($datensatz['Auswahlgespraech_Uhrzeit_bis'])." Uhr)");}
                }
                else
                {
                    if($datensatz['Datum_Termin'] == NULL)
                    {echo("keine Angabe!");}
                    else
                    {echo("".datum_dbdate_d($datensatz['Datum_Termin'])." (".cut_sec($datensatz['Uhrzeit_Termin'])." Uhr)");}
                }
                echo("</td>\n");
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

            //Buttons zur Auswahl ALLER oder KEINER der angezeigten Bewerber
            echo("<tr>\n");
            echo("<td colspan=\"8\">\n");
            echo("<img src=\"./bilder/Pfeil_re_2.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"><input class=\"Buttons_Klein\" type=\"button\" value=\"alle\" onClick=\"checkAll('checkbox')\">\n");
            echo("<img src=\"./bilder/Pfeil_re_2.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"><input class=\"Buttons_Klein\" type=\"button\" value=\"keine\" onClick=\"uncheckAll('checkbox')\">\n");
            echo("</td>\n");
            echo("</tr>\n");

            //Submit
            echo("<tr>\n");
            echo("<td colspan=\"8\" class=\"Tabelle_Uebersicht_Footer\">");
            //Versteckte Felder, welche eine R&uuml;ckkehr zur aktuellen Seite erm&ouml;glichen
            echo("<input type=\"hidden\" name=\"subseite\" value=\"".$subseite."\">\n");
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

            echo("</form>\n");
            echo("</table>\n");

            mysqli_free_result($ergebnis);
        }
        //Wenn es KEINE Suchergebnisse gibt...
        else
        {
            echo("<div class=\"Information_Warnung\" style=\"text-align:center;\">\n");
            echo("<b>Es wurden keine passenden Bewerber f&uuml;r Ihre Suchanfrage gefunden!</b><br><br>\n");
            echo("<form action=\"index.php?seite=intern_admin&intern_a=bewerber_suchen\" method=\"post\">\n");
            echo("<input type=\"submit\" class=\"Buttons_Unten\" value=\">> neue Suche\">\n");
            echo("</form>\n");
            echo("</div>\n");
            echo("<div class=\"Abstandhalter_Div\"></div>\n");
        }
    }
    //Wenn das Array "$search_array" leer ist (dh. es wurde keine eine Angabe gemacht)...
    else
    {
        echo("<div class=\"Information_Warnung\" style=\"text-align:center;\">\n");
        echo("<b>Es wurde keine Suchanfrage gestellt!</b><br><br>\n");
        echo("<form action=\"index.php?seite=intern_admin&intern_a=bewerber_suchen\" method=\"post\">\n");
        echo("<input type=\"submit\" class=\"Buttons_Unten\" value=\">> Zur&uuml;ck\">\n");
        echo("</form>\n");
        echo("</div>\n");
        echo("<div class=\"Abstandhalter_Div\"></div>\n");
    }

    echo("<img src=\"bilder/Pfeil_re.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"> <span class=\"Link1\"><a href=\"index.php?seite=intern_admin&intern_a=bewerber_suchen\">zur&uuml;ck zum Suchformular</a></span></span><br/>\n");
}
?>
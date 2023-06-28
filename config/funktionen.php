<?php
#HTML Kopf
function html_kopf($title = "Testseite Bewerbung Studiengang")
{
    echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n");
    echo("<html>\n");
    echo("<head>\n");
    echo("<title>".$title."</title>\n");
    echo("<link rel=\"SHORTCUT ICON\" href=\"bilder/favicon.gif\">\n");
    echo("<meta name=\"description\" content=\"Testseite Bewerbung Studiengang\">\n");
    echo("<meta name=\"author\" content=\"Markus Reichold\">\n");
    echo("<meta name=\"keywords\" content=\"Testseite Bewerbung Studiengang\">\n");
    echo("<meta name=\"robots\" content=\"follow\">\n");
    echo("<meta http-equiv=\"expires\" content=\"0\">\n");
    echo("<meta http-equiv=\"content-type\" content=\"text/html; charset=".CHARSET_META."\">\n");
    echo("<meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\">\n");
    echo("<meta http-equiv=\"Content-Style-Type\" content=\"text/css\">\n");
    echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"css/formate_screen.css\">\n");
    echo("<script type=\"text/javascript\" src=\"javascript/scripte.js\"></script>\n");
    echo("</head>\n");
    echo("<body>\n");
}
#HTML Fuss
function html_fuss()
{
    echo("</body>\n");
    echo("</html>\n");
}
#Fix f&uuml;r Funktion htmlspecialchars() in PHP 5.4+ for Latin1 (ISO-8859-1)
#Bei UTF8 kann die Funktion wie vorher ohne zus&auml;tzliche Parameter verwendet werden
#Muss auch f&uuml;r die Funktionen htmlentities() und html_entity_decode() durchgef&uuml;hrt werden (im Projekt nicht verwendet)
function htmlXspecialchars($string)
{
    //Wenn die Datenbank in Latin1 kodiert ist
    if(CHARSET == 'latin1')
    {
        return htmlspecialchars($string, ENT_COMPAT, 'ISO-8859-1');
    }
    //Wenn die Datenbank in UTF8 kodiert ist
    elseif(CHARSET == 'utf8')
    {
        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
    }
}
#Funktion gibt das Abfrageergebnis aller L&auml;nder zur&uuml;ck#
function land($link)
{
    $sql = "SELECT
                pky_Land,
                Land_de AS Land
            FROM
                land
            ORDER BY
                Land_de ASC;";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    return $result;
}
#Funktion liest das entsprechende Land anhand des &uuml;bergebenen Pkys aus#
function land_eintrag($link, $pky_land)
{
    $sql = "SELECT
                Land_de
            FROM
                land
            WHERE
                pky_Land = '".$pky_land."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row['Land_de'];
}
#Funktion gibt das Abfrageergebnis aller Arten der HZB zur&uuml;ck#
function hzb_art($link)
{
    $sql = "SELECT
                pky_HZB,
                HZB
            FROM
                hzb
            ORDER BY
                pky_HZB ASC;";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    return $result;
}
#Funktion liest die entsprechende HZB anhand des &uuml;bergebenen Pkys aus#
function hzb_art_eintrag($link, $pky_hzb)
{
    $sql = "SELECT
                HZB
            FROM
                hzb
            WHERE
                pky_HZB = '".$pky_hzb."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row['HZB'];
}
#Funktion gibt das Abfrageergebnis aller Arten der naturwissenschaftlichen F&auml;cher zur&uuml;ck#
function naturw_fach_art($link)
{
    $sql = "SELECT
                pky_naturw_Fach,
                naturw_Fach
            FROM
                naturw_fach
            ORDER BY
                naturw_Fach ASC;";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    return $result;
}
#Funktion liest ein naturw. Fach anhand des &uuml;bergebenen Pkys aus
function naturw_fach_eintrag($link, $pky_naturw_fach)
{
    $sql = "SELECT
                naturw_Fach
            FROM
                naturw_fach
            WHERE
                pky_naturw_Fach = '".$pky_naturw_fach."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row['naturw_Fach'];
}
#Funktion gibt das Abfrageergebnis aller Ausbildungen (Bonus f&uuml;r Zwischensumme) zur&uuml;ck#
function ausbildungen($link)
{
    $sql = "SELECT
                pky_Ausbildung,
                Ausbildung
            FROM
                ausbildungen_bonus
            ORDER BY
                Ausbildung ASC;";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    return $result;
}
#Funktion liest die Bezeichnung einer Ausbildung (Bonus f&uuml;r Zwischensumme) anhand des &uuml;bergebenen Pkys aus
function ausbildungen_eintrag($link, $pky_ausbildung)
{
    $sql = "SELECT
                Ausbildung
            FROM
                ausbildungen_bonus
            WHERE
                pky_Ausbildung = '".$pky_ausbildung."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row['Ausbildung'];
}
#Funktion gibt das Abfrageergebnis aller Kommissionsmitglieder f&uuml;r das Auswahlgespr&auml;ch aus
function kommissionsmitglieder($link)
{
    $sql = "SELECT
                pky_Kommissionsmitglied,
                Kommissionsmitglied
            FROM
                kommissionsmitglieder
            ORDER BY
                Kommissionsmitglied ASC;";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    return $result;
}
#Funktion liest ein Kommissionsmitglied anhand des &uuml;bergebenen Pkys aus
function kommissionsmitglied_eintrag($link, $pky_kommissionsmitglied)
{
    $sql = "SELECT
                Kommissionsmitglied
            FROM
                kommissionsmitglieder
            WHERE
                pky_Kommissionsmitglied = '".$pky_kommissionsmitglied."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row['Kommissionsmitglied'];
}
#Funktion gibt alle Komissionsmitglieder aus, welche einem Bewerber zugeordnet sind
#Namen werden getrennt durch Kommas
function kommissionsmitglieder_bewerber($link, $pky_bewerber)
{
    $kommissionsmitglieder = array();
    for($i=1; $i<=4; $i++)
    {
        $sql = "SELECT
                    km.Kommissionsmitglied
                FROM
                    bewerber b
                LEFT JOIN
                    termin_kommission_bewerber tkb
                ON
                    b.pky_Bewerber = tkb.fky_Bewerber
                LEFT JOIN
                    kommissionsmitglieder km
                ON
                    km.pky_Kommissionsmitglied = tkb.fky_Kommissionsmitglied_".$i."
                WHERE
                    b.pky_Bewerber = ".$pky_bewerber."
                ORDER BY
                    km.Kommissionsmitglied ASC;";
        $ergebnis = mysqli_query($link, $sql) OR die(mysqli_error($link));
        $row = mysqli_fetch_assoc($ergebnis);
        mysqli_free_result($ergebnis);
        if($row['Kommissionsmitglied'] != "")
        {
            $kommissionsmitglieder[] = $row['Kommissionsmitglied'];
        }
    }
    $kommissionsmitglieder_strg = implode(", ", $kommissionsmitglieder);
    return $kommissionsmitglieder_strg;
}
#Funktion wandelt Kommazahlen im deutschen Format (mit Komma) ins englische Format um (mit Punkt)
function float_d_e($zahl_d)
{
    $zahl_e = strtr($zahl_d, ",", ".");
    return $zahl_e;
}
#Funktion wandelt Kommazahlen im englischen Format (mit Punkt) ins deutsche Format um (mit Komma)
function float_e_d($zahl_e)
{
    $zahl_d = strtr($zahl_e, ".", ",");
    return $zahl_d;
}
#Funktion entfernt &uuml;berfl&uuml;ssige Nullen in Nachkommastellen
#z.B. 1,100 = 1,1 oder 2,020 = 2,02 oder 1,00 = 1 (auch das Komma wird entfernt!)
#Zweiter Parameter gibt an, ob die Eingabe im englischen oder im deutschen Format erfolgt
# en = englisch (mit Punkte als Dezimaltrennzeichen), de = deutsch (mit Kommas als Dezimaltrennzeichen)
function clean_num($zahl, $sprache)
{
    if($zahl == 0)
    {$zahl = 0;}
    else
    {
        if($sprache == "de")
        {$zahl = rtrim(rtrim($zahl, '0'), ',');}
        if($sprache == "en")
        {$zahl = rtrim(rtrim($zahl, '0'), '.');}
    }
    return $zahl;
}
#Funktion schneidet bei Zeitangaben aus der Datenbank (Type Time) die Sekunden ab
function cut_sec($uhrzeit)
{
    $uhrzeit_array = explode(":", $uhrzeit);
    $uhrzeit_ohne_sec = "".$uhrzeit_array[0].":".$uhrzeit_array[1]."";
    return $uhrzeit_ohne_sec;
}
#Funktion gibt das Einf&auml;rben von Zeilen f&uuml;r die &UUML;berpr&uuml;fung der Formulardaten aus
#F&uuml;r die Neuanmeldung
function style_input_check($zeile, $ergebnis_check)
{
    if($ergebnis_check == false)
    {
        $style_zeile = " style=\"color:red;background-color:#FFE1E2;\"";
    }
    else
    {
        if($zeile%2 == 0)
        {$style_zeile = "";}
        else
        {$style_zeile = " style=\"background-color:#EEEEEE;\"";}
    }
    return $style_zeile;
}
#Funktion &uuml;berpr&uuml;ft, ob ein Datum im richtigen Format eingetragen wurde
#UND ob das angegebene Datum existiert
function datum_regex($datum)
{
    $datum_regex = preg_match('/^\d{1,2}\.\d{1,2}\.(\d{2}|\d{4})$/', trim($datum));
    if($datum_regex)
    {
        $array_datum = explode(".", trim($datum));
        if(checkdate((int)$array_datum[1], (int)$array_datum[0], (int)$array_datum[2]))
        {return true;}
        else
        {return false;}
    }
}
#Funktion validiert die Richtigkeit einer Email-Adresse
function email_regex($email)
{
    //Regex von: http://seong.respice.net/2005/04/07/regexp-fur-e-mail-adressen/
    $email_regex = preg_match("#^([a-zA-Z0-9_\-])+(\.([a-zA-Z0-9_\-])+)*@((\[(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5]))\]))|((([a-zA-Z0-9])+(([\-])+([a-zA-Z0-9])+)*\.)+([a-zA-Z])+(([\-])+([a-zA-Z0-9])+)*))$#", trim($email));
    return $email_regex;
}
#Funktion checkt Jahreszahlen auf G&uuml;ltigkeit
function jahr_check($jahr)
{
    //&UUML;berpr&uuml;fen, ob die Jahreszahl eine vierstellige Zahl ist
    $jahr_regex = preg_match("/^[0-9]{4}$/", $jahr);
    //aktuelle Jahreszahl ermitteln
    //Eintrag nur bis zum aktuellen Jahr g&uuml;ltig
    $jahr_obergrenze = (int) date("Y");
    //Alle Jahreszahlen vor 1950 sind ung&uuml;ltig
    $jahr_untergrenze = 1950;
    //G&uuml;ltigkeit pr&uuml;fen
    if($jahr_regex AND $jahr >= $jahr_untergrenze AND $jahr <= $jahr_obergrenze)
    {return true;}
    else
    {return false;}
}
#Funktion &uuml;berpr&uuml;ft, ob eine Eingabe im Schulnotensystem erfolgte
#Zweiter Parameter gibt die Obergrenze der Note an (entweder 4 oder 6)
function note_check($note, $grenze)
{
    //Regex &uuml;berpr&uuml;ft, ob eine Zahl eingegeben wurde
    //Die Eingabe kann als Integer ODER als Float erfolgen (mit max. 2 Nachkommastellen)
    $regex = preg_match('#^[1-6]+(,[0-9]{1,2})?$#', trim($note));
    //Deutsche Kommas f&uuml;r den Vergleich in Punkte umwandeln
    $note = float_d_e(trim($note));
    //Pr&uuml;fung und Ausgabe
    if($regex AND $note >= 1 AND $note <= $grenze)
    {return true;}
    else
    {return false;}
}
#Funktion &uuml;berpr&uuml;ft, ob eine Eingabe im Punktesystem erfolgte
//Wird &uuml;ber einen einfachen Stringvergleich gel&ouml;st
//drei = notwendig, da sonst auch 01 oder 1.0 wahr ist
function punkte_check($punkte)
{
    if($punkte === "0" OR $punkte === "1" OR $punkte === "2" OR $punkte === "3" OR $punkte === "4" OR $punkte === "5" OR $punkte === "6" OR $punkte === "7" OR
       $punkte === "8" OR $punkte === "9" OR $punkte === "10" OR $punkte === "11" OR $punkte === "12" OR $punkte === "13" OR $punkte === "14" OR $punkte === "15")
    {return true;}
    else
    {return false;}
}
#Funktion &uuml;berpr&uuml;ft, ob die eingetragenen Endpunkte g&uuml;ltig sind
#Eine Endsumme kann nur eingetragen werden, wenn die Zwischensumme eines Bewerbers nicht berechenbar ist
function endsumme_check($endsumme)
{
    if(is_numeric($endsumme) AND $endsumme >= 23 AND $endsumme < 130)
    {return true;}
    else
    {return false;}
}
#Funktion &uuml;berpr&uuml;ft, ob die Uhrzeit in einem g&uuml;ltigen Format eingegeben wurde
function uhrzeit_check($uhrzeit)
{
    #Alternativ: /^([0-1][0-9]|2[0-3]):([0-5][0-9])(?::([0-5][0-9]))?$/  Pr&uuml;ft auch auf Sekunden. ABER: 9:05 geht nicht, Angabe der Stunde immer im zweistelligen Format
    $regex = preg_match('#^(?:0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$#', trim($uhrzeit));
    if($regex)
    {return true;}
    else
    {return false;}
}
#Funktion wandelt aus Formularen &uuml;bergebene deutsche Datumsformate in das Datenbankformat um
#Wird ein g&uuml;ltiges Datum &uuml;bergeben, so ist das Format: JJJJ-MM-TT
#Wird kein Datum &uuml;bergeben: 0000-00-00 (Standartformat)
function datum_d_dbdate($datum_d)
{
    if($datum_d != "")
    {
        $array = explode(".", $datum_d);
        $datum_dbdate = $array[2]."-".$array[1]."-".$array[0];
        return $datum_dbdate;
    }
    else
    {
        $datum_dbdate = "0000-00-00";
        return $datum_dbdate;
    }
}
#Funktion wandelt ein Datum im Datenbankformat (JJJJ-MM-TT) in ein deutsches Format (TT.MM.JJJJ) um
#Wurde kein Datum eingetragen (0000-00-00, Standartformat), wird der String "keine Angabe" zur&uuml;ckgegeben
function datum_dbdate_d($datum_db)
{
    if($datum_db == "0000-00-00")
    {
        $datum_d = "keine Angabe!";
        return $datum_d;
    }
    else
    {
        $array = explode("-", $datum_db);
        $datum_d = $array[2].".".$array[1].".".$array[0];
        return $datum_d;
    }
}
#Funktion wandelt Schulnoten in Abitur-&uuml;bliche Punkte um
#Der zweite Parameter gibt an, ob die Eingabe im englischen (mit Punkte als Trennzeichen) oder deutschen (mit Kommas als Trennzeichen) Format erfolgt
# de = deutsch, en = englisch
#Der dritte Parameter ist optional und gibt an, auf wieviele Nachkommastellen gerundet werden soll
#Erfolgt keine Angabe, wird auf zwei Stellen hinter dem Komma gerundet
#Ausgabe im englischen Format
function noten_in_punkte($note, $sprache, $stellen = 2)
{
    if($sprache == "de")
    {
        //Die deutsch Angabe der Noten (mit Komma) in das englische Format umwandeln
        $note_e = float_d_e($note);
    }
    if($sprache == "en")
    {
        $note_e = $note;
    }
    //Punkte berechnen
    $punkte_float = 17 - (3 * $note_e);
    //Punkte auf eine ganze Zahl runden
    $punkte = round($punkte_float, $stellen);
    return $punkte;
}
#Funktion wandelt Abitur-&uuml;bliche Punkte in Schulnoten um
#Eingabe muss im englischen Format stattfinden, wenn Nachkommastellen vorhanden sind
#Der zweite Parameter gibt an, ob die Ausgabe im englischen Format (f&uuml;r Eintrag in die DB) oder im deutschen Format (f&uuml;r Ausgabe am Bildschirm) erfolgen soll
# de = deutsch, en = englisch
#Der dritte Parameter ist optional und gibt an, auf wieviele Nachkommastellen gerundet werden soll
#Erfolgt keine Angabe, wird auf zwei Stellen hinter dem Komma gerundet
function punkte_in_noten($punkte, $sprache, $stellen = 2)
{
    //Note berechnen
    $note = (17 - $punkte) / 3;
    //Note runden
    $note_en = round($note, $stellen);
    //Optional die Note ins deutsche Format umwandeln (mit Komma)
    if($sprache == "de")
    {
        $note_d = float_e_d($note_en);
        return $note_d;
    }
    if($sprache == "en")
    {
        return $note_en;
    }
}
#Funktion errechnet die Zwischensumme aus den Leistungsangaben der Bewerbung
#Funktion muss auch aufgerufen werden, wenn die Angaben ge&auml;ndert werden
#Werden 85 Punkte oder mehr erreicht, wird der Bewerber zum Auswahlgespr&auml;ch eingeladen
function zwischensumme($leistung, $mat_belegt, $nat_belegt, $soziales_jahr, $pky_ausbildung)
{
    //Wenn entweder Mathemathik oder ein naturw. Fach in den letzten 4 Halbjahren nicht belegt wurden, dann ist die Berechnung nicht m&ouml;glich
    //dann wird der String NULL zur&uuml;ckgegeben, welcher dann als NULL in die Tabelle "leistungen_bewerber" eingetragen wird
    if($mat_belegt == 0 OR $nat_belegt == 0)
    {
        return 'NULL';
    }
    //Wenn eine Berechnung der Zwischensumme m&ouml;glich ist
    else
    {
        ########################
        ### Gesamtpunkte HZB ###
        ########################

        //Die Note der HZB wird als Kommazahl (float) im englischen Format &uuml;bergeben
        $hzb_note = $leistung['hzb_note'];

        //eventuellen Bonus f&uuml;r Soziales Jahr von der HZB Note subtrahieren
        if($soziales_jahr == 1)
        {
            $hzb_note = $hzb_note - BONUS_SOZ_JAHR;
        }

        //eventuellen Bonus f&uuml;r Ausbildung von der HZB Note subtrahieren
        if($pky_ausbildung != 0)
        {
            $hzb_note = $hzb_note - BONUS_AUSBILDUNG;
        }

        //Berechnung der HZB in Punkte mit max. 2 Nachkommastellen
        $hzb_punkte = noten_in_punkte($hzb_note, "en");

        //Berechnung der Gesamtpunkte f&uuml;r die HZB (mit Faktor multiplizieren)
        $hzb_gesamt = $hzb_punkte * FAKTOR_HZB;

        ###############################
        ### Gesamtpunkte Mathematik ###
        ###############################

        //Berechnung des Mittelwerts der Punkte f&uuml;r Mathemathik
        $teiler = 0;
        $summe_mathe = 0;
        foreach($leistung['mathe']['punkte'] AS $angabe)
        {
            if(is_numeric($angabe))
            {
                $summe_mathe = $summe_mathe + $angabe;
                $teiler++;
            }
        }
        $mw_mathe = $summe_mathe / $teiler;

        //Berechnung der Gesamtpunkte f&uuml;r Mathemathik (mit Faktor multiplizieren)
        $mathe_gesamt = $mw_mathe * FAKTOR_MATHEMATIK;

        ######################################
        ### Gesamtpunkte Naturwissenschaft ###
        ######################################

        //Berechnung des Mittelwerts f&uuml;r die Naturwissenschaft
        $teiler = 0;
        $summe_naturw = 0;
        foreach($leistung['naturw']['punkte'] AS $angabe)
        {
            if(is_numeric($angabe))
            {
                $summe_naturw = $summe_naturw + $angabe;
                $teiler++;
            }
        }
        $mw_naturw = $summe_naturw / $teiler;

        //Berechnung der Gesamtpunkte f&uuml;r die Naturwissenschaft (mit Faktor multiplizieren)
        $naturw_gesamt = $mw_naturw * FAKTOR_NATURWISSENSCHAFT;

        ##################################################
        ### Berechnung der Punktesumme (Zwischensumme) ###
        ##################################################

        //Zwischensumme berechnen und auf eine ganze Zahl runden
        $zwischensumme_float = $hzb_gesamt + $mathe_gesamt + $naturw_gesamt;
        $zwischensumme_int = round($zwischensumme_float, 0);

        //R&uuml;ckgabe der Zwischensumme
        return $zwischensumme_int;
    }
}
#Funktion gibt die ben&ouml;tingten Punkte f&uuml;r das Auswahlgespr&auml;ch an, damit der Bewerber die Endsumme erreicht
#Das Ergebnis wird auf eine Stelle hinter dem Komma gerundet und als deutsche Zahl ausgegeben
function notwendige_punkte_auswahlgespraech($zwischensumme)
{
    if($zwischensumme == NULL)
    {
        $ausgabe = "nicht berechenbar!";
    }
    else
    {
        $notwendige_punkte = (GRENZE_ENDSUMME - $zwischensumme) / 2;
        $notwendige_punkte = round($notwendige_punkte, 1);
        if($notwendige_punkte >= 15)
        {$ausgabe = "<span style=\"color:red;\">".float_e_d($notwendige_punkte)." Punkte</span>";}
        else
        {$ausgabe = "".float_e_d($notwendige_punkte)." Punkte";}
    }
    return $ausgabe;
}
#Funktion &uuml;berpr&uuml;ft, ob eine Email Adresse in der Tabelle "bewerber" bereits vorkommt
#Gibt "true" zur&uuml;ck, wenn diese Email Adresse bereits eingetragen wurde, ansonsten "false"
function email_vorhanden($link, $email)
{
    $sql = "SELECT
                COUNT(*) as Anzahl
            FROM
                bewerber
            WHERE
                Email = '".$email."';";
    $ergebnis = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($ergebnis);
    if($row['Anzahl'] > 0)
    {return true;}
    else
    {return false;}
}
#Funktion &uuml;berpr&uuml;ft, ob das aktuelle Datum innerhalb der Bewerbungsperiode liegt
function bewerbungsperiode()
{
    //Aktueller Timestamp
    $timestamp_aktuell = time();
    //Timestamp f&uuml;r den Beginn der Bewerbungsperiode
    $tag_monat_beginn = explode(".", ANMELDEBEGINN_D_M);
    $timestamp_beginn =  mktime(0, 0, 0, $tag_monat_beginn[1], $tag_monat_beginn[0], date("Y"));
    //Timestamp f&uuml;r das Ende der Bewerbungsperiode
    $tag_monat_ende = explode(".", ANMELDEENDE_D_M);
    $timestamp_ende =  mktime(0, 0, 0, $tag_monat_ende[1], ($tag_monat_ende[0]+1), date("Y"));
    //Vergleich
    if($timestamp_aktuell >= $timestamp_beginn AND $timestamp_aktuell <= $timestamp_ende)
    {return true;}
    else
    {return false;}
}
#Funktion &uuml;berpr&uuml;ft die eingegebenen Login-Daten der Bewerber auf G&uuml;ltigkeit
#Wenn Email und Passwort g&uuml;ltig sind, au&szlig;erdem der Account per Email Link aktiviert wurde (= NULL) und der Account nicht gesperrt wurde (= NULL), gibt die Funktion "1" zur&uuml;ck
#Ansonsten die entsprechende Fehlermeldung
function login_right_bewerber($link, $email, $passwort)
{
    $sql = "SELECT
                Email,
                Passwort,
                Key_Aktivierung,
                Account_gesperrt
            FROM
                bewerber
            WHERE
                Email = '".$email."' AND
                Passwort = MD5('".$passwort."');";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);

    if(mysqli_num_rows($result) == 1 AND $row['Key_Aktivierung'] == NULL AND $row['Account_gesperrt'] == NULL AND bewerbungsperiode())
    {return 1;}
    else
    {
        if(mysqli_num_rows($result) > 1)
        {return "Kein Login m&ouml;glich, da Ihre Angaben unerwartet mehrfach im System auftauchen!";}
        elseif(mysqli_num_rows($result) == 0)
        {return "Keine oder falsche Angabe bei Email/Passwort";}
        elseif(mysqli_num_rows($result) == 1)
        {
            if(!bewerbungsperiode())
            {return "Sie k&ouml;nnen sich nur innerhalb der Bewerbungsperiode im internen Bereich f&uuml;r Bewerber anmelden!";}
            elseif($row['Key_Aktivierung'] != NULL AND $row['Account_gesperrt'] == NULL)
            {return "Kein Login m&ouml;glich, da Ihr Account noch nicht durch Best&auml;tigung des Ihnen zugesandten Links aktiviert wurde!";}
            elseif($row['Key_Aktivierung'] == NULL AND $row['Account_gesperrt'] != NULL)
            {return "Ihr Account wurde gesperrt!";}
            elseif($row['Key_Aktivierung'] != NULL AND $row['Account_gesperrt'] != NULL)
            {return "Ihr Account wurde nicht aktiviert und au&szlig;erdem gesperrt!";}
        }
    }
    mysqli_free_result($result);
}
#Funktion &uuml;berpr&uuml;ft die eingegebenen Login-Daten der Administratoren auf G&uuml;ltigkeit
#Wenn keine &UUML;bereinstimmung gibt, gibt es die Funktion "false" zur&uuml;ck
function login_right_admin($link, $email, $passwort)
{
    $sql = "SELECT
                COUNT(*) as Anzahl
            FROM
                administratoren
            WHERE
                Email = '".$email."' AND
                Passwort = MD5('".$passwort."');";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    if($row['Anzahl'] >= 1)
    {return true;}
    else
    {return false;}
}
#Funktion &uuml;berpr&uuml;ft, ob ein Bewerber sein Passwort korrekt angegeben hat
#Wichtig f&uuml;r die &AUML;nderung des Passworts im internen Benutzerbereich
function passwort_check_bewerber($link, $pky_bewerber, $passwort)
{
    $sql = "SELECT
                COUNT(*) as Anzahl
            FROM
                bewerber
            WHERE
                pky_Bewerber = '".$pky_bewerber."' AND
                Passwort = MD5('".$passwort."') AND
                Key_Aktivierung IS NULL;";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    if($row['Anzahl'] >= 1)
    {return true;}
    else
    {return false;}
}
#Funktion liese den Pky eines Bewerbers anhand dessen Email Adresse aus
function pky_bewerber($link, $email)
{
    $sql = "SELECT
                pky_Bewerber
            FROM
                bewerber
            WHERE
                Email = '".$email."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row['pky_Bewerber'];
}
#Funktion liese den Pky eines Administrators anhand dessen Email Adresse aus
function pky_admin($link, $email)
{
    $sql = "SELECT
                pky_Admin
            FROM
                administratoren
            WHERE
                Email = '".$email."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row['pky_Admin'];
}
#Funktion liest den Namen eines Bewerbers anhand dessen Pkys aus
#Der Zweite Parameter "$was" gibt an, wieviel vom Namen ausgelesen werden soll
# 1 = nur Nachname, 2 = Vor- und Nachname, 3 = Anrede, Vor- und Nachname, 4 = geehrter/geehrte, Anrede und Nachname
function name_bewerber($link, $pky, $was)
{
    $sql = "SELECT
                Anrede,
                Nachname,
                Vorname
            FROM
                bewerber
            WHERE
                pky_Bewerber = '".$pky."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    if($was == 1)
    {$name = $row['Nachname'];}
    elseif($was == 2)
    {$name = "".$row['Vorname']." ".$row['Nachname']."";}
    elseif($was == 3)
    {
        if($row['Anrede'] == "h")
        {$anrede = "Herr";}
        else
        {$anrede = "Frau";}
        $name = "".$anrede." ".$row['Vorname']." ".$row['Nachname']."";
    }
    elseif($was == 4)
    {
        if($row['Anrede'] == "h")
        {$anrede = "geehrter Herr";}
        else
        {$anrede = "geehrte Frau";}
        $name = "".$anrede." ".$row['Nachname']."";
    }
    return $name;
}
#Funktion liest den Namen eines Administrators anhand dessen Pkys aus
#Der Zweite Parameter "$was" gibt an, wieviel vom Namen ausgelesen werden soll
# 1 = nur Nachname, 2 = Vor- und Nachname, 3 = Anrede, Vor- und Nachname, 4 = geehrter/geehrte, Anrede und Nachname
function name_admin($link, $pky, $was)
{
    $sql = "SELECT
                Anrede,
                Nachname,
                Vorname
            FROM
                administratoren
            WHERE
                pky_Admin = '".$pky."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    if($was == 1)
    {$name = $row['Nachname'];}
    elseif($was == 2)
    {$name = "".$row['Vorname']." ".$row['Nachname']."";}
    elseif($was == 3)
    {
        if($row['Anrede'] == "h")
        {$anrede = "Herr";}
        else
        {$anrede = "Frau";}
        $name = "".$anrede." ".$row['Vorname']." ".$row['Nachname']."";
    }
    elseif($was == 4)
    {
        if($row['Anrede'] == "h")
        {$anrede = "geehrter Herr";}
        else
        {$anrede = "geehrte Frau";}
        $name = "".$anrede." ".$row['Nachname']."";
    }
    return $name;
}
#Funktion gibt an, ob ein Bewerber seine Bewerbung zur&uuml;ckgezogen hat
//Wurde die Bewerbung zur&uuml;ckgezogen, gibt die Funktion "true" aus, ansonsten "fasle"
function bewerbung_zurueckgezogen($link, $pky_bewerber)
{
    $sql = "SELECT
                Bewerbung_zurueckgezogen
            FROM
                bewerber
            WHERE
                pky_Bewerber = '".$pky_bewerber."';";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    if($row['Bewerbung_zurueckgezogen'] == 1)
    {return true;}
    else
    {return false;}
}
#Funktion gibt bei der &UUML;bersichtsanzeige der Bewerber die Zeilenfarbe und den Statustext aus
function bewerber_status_zeile($punkte_zs, $punkte_es, $acc_aktiviert, $acc_gesperrt, $bewerb_zurueck)
{
    $format = array();
    //Wenn die Bewerbung zur&uuml;ckgezogen wurde oder der Account nicht aktiviert/gesperrt wurde
    // -> Grau
    if($acc_aktiviert != NULL OR $acc_gesperrt == 1 OR $bewerb_zurueck == 1)
    {
        $format['style_bg'] = "#EEEEEE";
        $format['style_color'] = "#6A6A6A";
        if($acc_aktiviert =! NULL)
        {$format['status'] = "Account wurde nicht aktiviert!";}
        if($acc_gesperrt == 1)
        {$format['status'] = "Account wurde gesperrt!";}
        if($bewerb_zurueck == 1)
        {$format['status'] = "Bewerbung wurde zur&uuml;ckgezogen!";}
    }
    else
    {
        //Wenn eine Endsumme eingetragen wurde und diese erreicht wurde
        // -> Gr&uuml;n
        if($punkte_es != NULL AND $punkte_es >= GRENZE_ENDSUMME)
        {
            $format['style_bg'] = "#D9FFD9";
            $format['style_color'] = "green";
            $format['status'] = "Endsumme erreicht";
        }
        //Wenn eine Endsumme eingetragen wurde, aber diese nicht erreicht wurde
        // -> Rot
        elseif($punkte_es != NULL AND $punkte_es < GRENZE_ENDSUMME)
        {
            $format['style_bg'] = "#FFE1E2";
            $format['style_color'] = "red";
            $format['status'] = "Endsumme nicht erreicht";
        }
        //Wenn noch keine Endsumme eingetragen wurde
        else
        {
            //Wenn die Zwischensumme erreicht wurde
            // -> Blau
            if($punkte_zs != NULL AND $punkte_zs >= GRENZE_ZWISCHENSUMME)
            {
                $format['style_bg'] = "#D9D9FF";
                $format['style_color'] = "blue";
                $format['status'] = "Zwischensumme erreicht";
            }
            //Wenn die Zwischensumme nicht erreicht wurde
            // -> Rot
            elseif($punkte_zs != NULL AND $punkte_zs < GRENZE_ZWISCHENSUMME)
            {
                $format['style_bg'] = "#FFE1E2";
                $format['style_color'] = "red";
                $format['status'] = "Zwischensumme nicht erreicht!";
            }
            //Wenn die Zwischensumme nicht berechenbar ist
            // -> Orange
            else
            {
                $format['style_bg'] = "#feecc7";
                $format['style_color'] = "orange";
                $format['status'] = "Zwischensumme nicht berechenbar!";
            }
        }
    }
    return $format;
}
#Funktion, welche alle &uuml;bergebenen $_POST Parameter in versteckte Formularfelder packt
function post_back($array_post)
{
    foreach ($array_post as $key => $value)
    {
        //keine Arrays zur&uuml;ck schicken
        if(!is_array($value))
        {
            echo("<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">\n");
        }
    }
}
#Ersatzfunktion f&uuml;r alte "mysql_field_name" Funktion
function mysqli_field_name($result, $field_offset) 
{
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}
#Funktion bietet die wichtigsten Daten zu den Bewerbern als Export (xls) an#
function export($link, $where_string, $order_by_string)
{
    //pers&ouml;nliche Daten des Bewerbers aus der Tabelle "bewerber" und "leistungen_bewerber" auslesen
    $sql = "SELECT
                b.pky_Bewerber AS Nummer,
                IF(b.Anrede = 'h', 'Herr', 'Frau') AS Anrede,
                b.Nachname,
                b.Vorname,
                b.Email,
                b.Strasse,
                REPLACE(REPLACE(b.Hausnummer, ' ', ''), '/', '|') AS Hausnummer,
                b.Adresszusatz,
                b.Postleitzahl AS PLZ,
                b.Wohnort,
                l.Land_de AS Land,
                REPLACE(lb.HZB_Note, '.', ',') AS Abiturnote,
                lb.Zwischensumme,
                lb.Fachkompetenz,
                lb.Sozialkompetenz,
                REPLACE(lb.Auswahlgespraech_Summe, '.', ',') AS Auswahlgespraech_Summe,
                lb.Endsumme,
                ab.Auswahlgespraech_Datum,
                REPLACE(ab.Auswahlgespraech_Kommentar, '\r\n', ', ') AS Auswahlgespraech_Kommentar
            FROM
                bewerber b
            INNER JOIN
                leistungen_bewerber lb
            ON
                b.pky_Bewerber = lb.fky_Bewerber
            INNER JOIN
                land l
            ON
                l.pky_Land = b.fky_Land
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
                ".$order_by_string.";";
    $result = mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Anzahl der Datens&auml;tze bestimmen
    $num_fields = mysqli_num_fields($result);

    //&UUML;berschrift
    $header = "";
    for($i=0; $i<$num_fields; $i++)
    {
        $header .= mysqli_field_name($result, $i)."\t";
    }
    //&UUML;berschrift Kommissionsmitglieder
    $header .= "Kommissionsmitglieder\t";
    //&UUML;berschrift Status
    $header .= "Status\t";

    //Datens&auml;tze
    $data = "";
    while($row = mysqli_fetch_row($result))
    {
        $line = '';
        foreach($row as $value)
        {
            if((!isset($value)) || ($value == ""))
            {
                $value = "\t";
            }
            else
            {
                $value = str_replace('"', '""', $value);
                $value = '"'.$value.'"'."\t";
            }
            $line .= $value;
        }
        #######################################
        //Komissionsmitglieder einf&uuml;gen
        $kommissionsmitglieder = kommissionsmitglieder_bewerber($link, $row[0]);
        if($kommissionsmitglieder == "")
        {
            $line .= "\t";
        }
        else
        {
            $line .= '"'.$kommissionsmitglieder.'"'."\t";
        }
        #######################################
        //Status einf&uuml;gen
        $sql = "SELECT
                    b.Key_Aktivierung,
                    b.Account_gesperrt,
                    b.Bewerbung_zurueckgezogen
                FROM
                    bewerber b
                WHERE
                    b.pky_Bewerber = ".$row[0].";";
        $ergebnis = mysqli_query($link, $sql) OR die(mysqli_error($link));
        $status = mysqli_fetch_assoc($ergebnis);
        mysqli_free_result($ergebnis);
        $status_text = bewerber_status_zeile($row[12], $row[16], $status['Key_Aktivierung'], $status['Account_gesperrt'], $status['Bewerbung_zurueckgezogen']);
        $line .= '"'.$status_text['status'].'"'."\t";
        #######################################

        $data .= trim($line)."\n";
    }
    $data = str_replace("\r", "", $data);

    //Wenn kein Eintrag vorhanden ist
    if($data == "")
    {
        $data = "\n Kein Eintrag vorhanden!n";
    }

    //Daten begrenzen, da sonst der Quelltext des HTML Dokuments mit exportiert wird
    $xlsdata = $header."\n".$data;
    //Header der Datei festlegen
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=export.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Cache-Control: public");
    header("Content-length: ".strlen($xlsdata));
    //Ausgabe
    print $xlsdata;
    //Datens&auml;tze freigeben
    mysqli_free_result($result);
}
?>
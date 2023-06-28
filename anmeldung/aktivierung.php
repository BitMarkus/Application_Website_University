<?php
//&UUML;berpr&uuml;fen, ob der angegebene Link eine g&uuml;ltige Email Adresse enth&auml;lt
if(isset($_GET['email']) AND email_regex($_GET['email']))
{
    $email = $_GET['email'];
}
//&UUML;berpr&uuml;fen, ob der angegebene Link einen g&uuml;ltigen Aktivierungskey enth&auml;lt
if(isset($_GET['key']) AND (strlen($_GET['key']) == 32))
{
    $key = $_GET['key'];
}
//Wenn beide Parameter korrekt im Link enthalten sind
if(isset($email) AND isset($key))
{
    //Überprüfen, ob die angegebene Email Adresse aus dem Link in der Tabelle "bewerber" eingetragen ist
    if(email_vorhanden($link, $email))
    {
        //Den eingetragenen Key f&uuml;r die entsprechende Email Adresse aus der Tabelle "bewerber" auslesen
        $sql = "SELECT
                    Key_Aktivierung
                FROM
                    bewerber
                WHERE
                    Email = '".$email."';";
        $result_key = mysqli_query($link, $sql) OR die(mysqli_error($link));
        $row = mysqli_fetch_assoc($result_key);
        $key_db = $row['Key_Aktivierung'];
        mysqli_free_result($result_key);

        //Überprüfen, ob der Account bereits aktiviert wurde
        if($key_db != NULL)
        {
            //Das Feld "Key_Aktivierung" in der Tabelle "bewerber" auf den Wert "NULL" &auml;ndern
            $sql = "UPDATE bewerber
                    SET
                        Key_Aktivierung = NULL,
                        Datum_Aktivierung = NOW()
                    WHERE
                       (Email = '".$email."' AND Key_Aktivierung = '".$key."')
                    LIMIT 1";
            mysqli_query($link, $sql) OR die(mysqli_error($link));
            //Wenn die Abfrage erfolgreich war
            if(mysqli_affected_rows($link) == 1)
            {
                echo("<div class=\"Information\">\n");
                echo("<b>Ihre Bewerbung ist erfolgreich abgeschlossen!</b><br />");
                echo("Vielen Dank f&uuml;r Ihre Bewerbung f&uuml;r den Studiengang Molekulare Medizin an der Universit&auml;t Regensburg. ");
                echo("Sie k&ouml;nnen sich nun in den internen Bereich einloggen, von wo aus Sie Ihre Daten einsehen und gegebenenfalls noch &auml;ndern k&ouml;nnen.");
                echo("</div><br />\n");

                //Infos zur Bewerbung ausgeben
                echo("<div class=\"h2\">Hinweise zum Verfahren der Studienplatzvergabe</div>\n");
                echo("<div>\n");

                echo("Die Vergabe der Studienpl&auml;tze erfolgt &uuml;ber ein sogenanntes \"Eignungsfeststellungsverfahren\". Bei dem zweistufigen Verfahren werden zun&auml;chst Ihre Angaben aus ");
                echo("der Online-Bewerbung (1. Stufe) bewertet. Bei ausreichend hohem Ergebnis werden Sie zum Auswahlgespr&auml;ch (2. Stufe) eingeladen, welches ebenfalls benotet wird. ");
                echo("F&uuml;r die Feststellung Ihrer Eignung zum Bachelorstudium Molekulare Medizin werden die Leistungen aus Stufe 1 und 2 miteinander verrechnet. ");
                echo("Bei einem ausreichend hohem Endergebnis bekommen Sie dann einen Studienplatz angeboten.<br /><br />");

                echo("<u>Auswahlgespr&auml;ch</u><br />");
                echo("Sie werden bis Ende Juli ".date("Y")." per Email und per Post dar&uuml;ber informiert, ob und gegebenenfalls wann Sie zu einem Auswahlgespr&auml;ch eingeladen werden. ");
                echo("Das Auswahlgespr&auml;ch wird in der Regel bis 15.08. an der Universit&auml;t Regensburg durchgef&uuml;hrt. ");
                echo("Die Auswahlkommission f&uuml;hrt dabei ca. 30 min&uuml;tige Gruppengespr&auml;che mit maximal drei Bewerbern. ");
                echo("Erst nach dem Auswahlgespr&auml;ch wird dar&uuml;ber entschieden, ob Ihnen ein Studienplatz angeboten wird.<br /><br />");
                echo("<b>Bitte beachten Sie:</b> ");
                echo("Zum Auswahlgespr&auml;ch m&uuml;ssen mitgebracht werden:");
                echo("<ul>");
                echo("<li>Eine Kopie und das Original der Hochschulzugangsberechtigung</li>");
                echo("<li>Ggf. Originalnachweise und eine Kopie &uuml;ber ein abgeleistetes freiwilliges soziales Jahr, Zivil- oder Wehrdienst</li>");
                echo("<li>Ggf. Originalnachweise und eine Kopie &uuml;ber eine in der Online-Bewerbung (unter \"Lebenslauf\") genannte abgeschlossene Berufsausbildung</li>");
                echo("</ul>");
                echo("Die darin enthaltenen Informationen werden unmittelbar vor dem Auswahlgespr&auml;ch mit den von Ihnen gemachten Angaben der Online-Bewerbung verglichen, ");
                echo("um evtl. vorhandene &UUML;bertragungsfehler zu korrigieren.<br /><br />");

                echo("<u>&AUML;nderungen Ihrer Daten</u><br />");
                echo("Im internen Bereich f&uuml;r Bewerber k&ouml;nnen Sie die Angaben Ihrer Bewerbung einsehen und gegebenenfalls noch &auml;ndern. Dies ist jedoch nur bis zum Ablauf der ");
                echo("Bewerbungsfrist m&ouml;glich (".ANMELDEENDE_D_M."".date("Y")."). Danach ist es bis zur Anmeldeperiode im darauffolgenden Studienjahr nicht mehr m&ouml;glich, ");
                echo(" sich in den internen Bereich einzuloggen.<br /><br />");

                echo("<u>Zur&uuml;ckziehen der Bewerbung</u><br />");
                echo("Neben Einsicht und &AUML;nderungen Ihrer Angaben bietet der interne Bereich die M&ouml;glichkeit, Ihre Bewerbung zur&uuml;ckzuziehen. In diesem Fall wird Ihre ");
                echo("Bewerbung nicht als Bewerbungsversuch gewertet. Ihre Daten bleiben dabei trotzdem in unserem System gespeichert. Die Speicherung Ihrer Daten bietet ");
                echo("den Vorteil, dass Sie - sollten Sie es sich anders &uuml;berlegen - Ihre Bewerbung innerhalb der Bewerbungsperiode jederzeit wieder reaktivieren k&ouml;nnen.<br /><br />");

                echo("<u>Wiederholung der Bewerbung</u><br />");
                echo("Wer im Eignungsfeststellungsverfahren abgelehnt wurde, kann sich zum Termin des folgenden Jahres erneut zum Eignungsfeststellungsverfahren anmelden. ");
                echo("Eine zweite Wiederholung ist ausgeschlossen. Die Wiederholungsfunktion f&uuml;r die Online-Bewerbung im internen Bereich f&uuml;r Bewerber wird ab 2012 ");
                echo("zur Verf&uuml;gung stehen.");

                echo("</div><br />\n");
            }
            //Wenn die Abfrage nicht erfolgreich war
            else
            {
                echo("<div class=\"Information_Warnung\">\n");
                echo("<b>ACHTUNG!</b><br />");
                echo("Ein Fehler ist aufgetreten. Ihre Bewerbung konnte nicht erfolgreich beendet werden.<br />");
                echo("Klicken Sie bitte erneut den Link in der Ihnen zugesandten Email. ");
                echo("Sollte dies nicht erfolgreich sein wenden Sie sich an <b>\"".EMAIL_SEKRETARIAT."\"</b>.");
                echo("</div>\n");
                echo("<div class=\"Abstandhalter_Div\"></div>\n");
            }
        }
        //Wenn der Account bereits aktiviert wurde
        else
        {
            echo("<div class=\"Information_Warnung\">\n");
            echo("<b>ACHTUNG!</b><br />");
            echo("Ihre Bewerbung ist bereits durch die Best&auml;tigung per zugesandtem Link erfolgreich abgeschlossen worden.");
            echo("</div>\n");
            echo("<div class=\"Abstandhalter_Div\"></div>\n");
        }
    }
    //Wenn die angegebene Email Adresse aus dem Link in der Tabelle "bewerber" nicht vorhanden ist
    else
    {
        echo("<div class=\"Information_Warnung\">\n");
        echo("<b>ACHTUNG!</b><br />");
        echo("Ein Fehler ist aufgetreten. Ihre Bewerbung konnte nicht erfolgreich beendet werden.<br />");
        echo("Ihre Daten sind nicht mehr im System gespeichert. Dies kann passieren, wenn zwischen dem Ausf&uuml;llen des Online-Formulars und der Aktivierung per zugesandtem Link zu viel Zeit vergangen ist. ");
        echo("In diesem Fall wird davon ausgegangen, dass im Formular eine falsche Email Adresse angegeben wurde oder dass der Bewerber die Bewerbung nicht abschlie&szlig;en wollte. ");
        echo("F&uuml;r eine erfolgreiche Bewerbung muss das Online-Formular erneut ausgef&uuml;llt werden.");
        echo("</div>\n");
        echo("<div class=\"Abstandhalter_Div\"></div>\n");
    }
}
else
{
    echo("<div class=\"Information_Warnung\">\n");
    echo("<b>ACHTUNG!</b><br />");
    echo("Ein Fehler ist aufgetreten. Ihre Bewerbung konnte nicht erfolgreich beendet werden.<br />");
    echo("Klicken Sie bitte erneut den Link in der Ihnen zugesandten Email. ");
    echo("Sollte dies nicht erfolgreich sein wenden Sie sich an <b>\"".EMAIL_SEKRETARIAT."\"</b>.");
    echo("</div>\n");
    echo("<div class=\"Abstandhalter_Div\"></div>\n");
}
?>

<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1"><a href="index.php?seite=anmeldung">zum internen Bereich f&uuml;r Bewerber</a></span><br/>
<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1"><a href="index.php?seite=start">zur Hauptseite</a></span></span><br/>
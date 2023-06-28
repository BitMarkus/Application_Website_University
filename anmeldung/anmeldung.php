<div class="h1">Online-Bewerbung</div>

<?php
//Wenn das aktuelle Datum innerhalb der Bewerbungsperiode liegt
if(bewerbungsperiode())
{
    //Informationen zum Verfahren der Studienplatzvergabe
    echo("<div class=\"h2\">Hinweise zum Verfahren der Studienplatzvergabe</div>\n");
    echo("<div>\n");
    echo("Die Vergabe der Studienpl&auml;tze erfolgt &uuml;ber ein sogenanntes \"Eignungsfeststellungsverfahren\". Bei dem zweistufigen Verfahren werden zun&auml;chst Ihre ");
    echo("Angaben aus der Online-Bewerbung (1. Stufe) bewertet. Bei ausreichend hohem Ergebnis werden Sie zum Auswahlgespr&auml;ch (2. Stufe) eingeladen, welches ");
    echo("ebenfalls benotet wird. F&uuml;r die Feststellung Ihrer Eignung zum Bachelorstudium werden die Leistungen aus Stufe 1 und 2 miteinander ");
    echo("verrechnet. Bei einem ausreichend hohem Endergebnis bekommen Sie dann einen Studienplatz angeboten.<br />\n");
    echo("<b>Bitte beachten Sie:</b> Sie sind verpflichtet, zum Auswahlgespr&auml;ch das Original Ihres Abiturzeugnisses bzw. Ihrer Hochschulzugangsberechtigung sowie ");
    echo("ggf. Nachweise &uuml;ber ein freiwilliges soziales Jahr, Zivil- oder Wehrdienst und ggf. &uuml;ber eine abgeschlossene Ausbildung (BTA, MTA, CTA, PTA) vorzulegen. ");
    echo("Die darin enthaltenen Informationen werden unmittelbar vor dem Auswahlgespr&auml;ch mit den von Ihnen gemachten Angaben der Online-Bewerbung verglichen, ");
    echo("um evtl. vorhandene &Uuml;bertragungsfehler zu korrigieren.");
    echo("</div>\n");

    //Link zum Anmeldungsformular
    echo("<div class=\"h2\">Neubewerbung</div>\n");
    echo("<div>\n");
    echo("Bei einer Neubewerbung gelangen Sie &uuml;ber folgenden Link zum Bewerbungsformular: \n");
    echo("<img src=\"bilder/Pfeil_re.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"> <span class=\"Link2\">");
    echo("<a href=\"index.php?seite=neuanmeldung\">Neubewerbung</a></span>\n");
    echo("</div>\n");

    //Hinweise zum internen Bereich für Bewerber
    echo("<div class=\"h2\">Interner Bereich f&uuml;r Bewerber</div>\n");
    echo("<div>\n");
    echo("Wenn Sie sich bereits erfolgreich registriert haben (Ausf&uuml;llen der Online-Bewerbung und Best&auml;tigung des zugesandten Email-Links), kommen Sie ");
    echo("&uuml;ber nachfolgendes Login-Formular zum internen Bereich f&uuml;r Bewerber. Geben Sie hierf&uuml;r Ihre Email-Adresse und Ihr Passwort ein. Beachten Sie ");
    echo("dabei Gro&szlig;- und Kleinschreibung. Hier haben Sie die M&ouml;glichkeit, Ihre Angaben einzusehen und gegebenenfalls nachtr&auml;glich zu &auml;ndern oder Ihre Bewerbung zur&uuml;ckzuziehen.");
    echo("</div>\n");

    //Formatierung der Login-Tabelle festlegen
    if(isset($_POST['login_bewerber']) AND $login_right_bewerber != 1)
    {$class_login_table = "Tabelle_Login_Warnung";}
    else
    {$class_login_table = "Tabelle_Login";}

    //Login Formular
    echo("<form action=\"index.php?seite=anmeldung\" method=\"post\">\n");
    echo("<table border=\"0\" class=\"".$class_login_table."\">\n");

    //Bei einem Fehler bei der Anmeldung wird eine Warnung ausgeben
    //Diese Warnung (Variable "$login_right_bewerber") wird in der Datei "index.php" per Funktion (login_right_bewerber) &uuml;bergeben
    if(isset($_POST['login_bewerber']) AND $login_right_bewerber != 1)
    {
        echo("<tr>\n");
        echo("<td colspan=\"2\" style=\"text-align:center; border-bottom:1px solid red;\">");
        echo("".$login_right_bewerber."");
        echo("</td>\n");
        echo("</tr>\n");
    }
    else
    {
        echo("<tr>\n");
        echo("<td colspan=\"2\" style=\"text-align:center; border-bottom:1px solid #6A6A6A;\">");
        echo("Bitte einloggen, um den internen Bereich zu betreten");
        echo("</td>\n");
        echo("</tr>\n");
    }

    echo("<tr>\n");
    echo("<td style=\"width:10%;\">Email:</td>\n");
    echo("<td><input name=\"email_bewerber\" type=\"text\" size=\"25\" maxlength=\"100\"></td>\n");
    echo("</tr>\n");

    echo("<tr>\n");
    echo("<td>Passwort:</td>\n");
    echo("<td><input name=\"pw_bewerber\" type=\"password\" size=\"25\" maxlength=\"100\"></td>\n");
    echo("</tr>\n");

    echo("<tr>\n");
    echo("<td><input type=\"submit\" class=\"Buttons_Unten\" name=\"login_bewerber\" value=\">> Einloggen\"></td>\n");
    echo("<td style=\"text-align:right;\">");
    echo("<img src=\"bilder/Pfeil_re.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"> <span class=\"Link1\"><a href=\"index.php?seite=pw_vergessen\">Passwort vergessen?</a></span>");
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
    echo("</form>\n");
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
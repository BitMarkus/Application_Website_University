<div class="h1">Passwort vergessen</div>

<div>
Wenn Sie Ihr Passwort f&uuml;r den internen Bereich f&uuml;r Bewerber vergessen haben, haben Sie hier die M&ouml;glichkeit, ein neues Passwort
anzufordern. Geben Sie dazu in das nachfolgende Formular Ihre Email Adresse ein, welche Sie bei der Bewerbung angegeben haben.
Ein neues Passwort wird Ihnen dann umgehend per Mail zugesandt.
</div>

<?php
//Email Adresse pr&uuml;fen
if(isset($_POST['pw_anfordern']))
{
    //&UUML;berpr&uuml;fen, ob die angegebene Email Adresse valide ist
    if(!email_regex(trim($_POST['email_bewerber'])))
    {
        $warnung = "Es wurde keine oder eine falsche Email Adresse angegeben!";
    }
    else
    {
        //&UUML;berpr&uuml;fen, ob die Email Adresse in der Tabelle "bewerber" vorhanden ist
        if(!email_vorhanden($link, trim($_POST['email_bewerber'])))
        {
            $warnung = "Die angegebene Email Adresse ist in unserem System nicht registriert!";
        }
    }
}

//Formatierung der Tabelle festlegen
if(isset($warnung))
{$class_login_table = "Tabelle_Login_Warnung";}
else
{$class_login_table = "Tabelle_Login";}

//Formular Anzeigen, wenn die Seite das erste mal aufgerufen wurde
//ODER wenn die angegebene Email Adresse nicht in der Tabelle "bewerber" eingetragen ist
if(!isset($_POST['pw_anfordern']) OR isset($warnung))
{
    echo("<form action=\"index.php?seite=pw_vergessen\" method=\"post\">\n");
    echo("<table border=\"0\" class=\"".$class_login_table."\">\n");

    if(isset($warnung))
    {
        echo("<tr>\n");
        echo("<td colspan=\"2\" style=\"text-align:center; border-bottom:1px solid red;\">");
        echo("".$warnung."");
        echo("</td>\n");
        echo("</tr>\n");
    }

    echo("<tr>\n");
    echo("<td style=\"width:8%;\">Email:</td>\n");
    echo("<td><input name=\"email_bewerber\" type=\"text\" size=\"25\" maxlength=\"100\"></td>\n");
    echo("</tr>\n");

    echo("<tr>\n");
    echo("<td colspan=\"2\">");
    echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"pw_anfordern\" value=\">> neues Passwort anfordern\">");
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
    echo("</form>\n");
}

//Versendung der Email und &AUML;nderung des Passworts in der Datenbank
if(isset($_POST['pw_anfordern']) AND !isset($warnung))
{
    //Zufallspasswort erstellen
    $passwort_neu = substr(md5(microtime()),0,8);
    //Den Pky des Bewerbers anhand der Email Adresse auslesen
    $pky_bewerber = pky_bewerber($link, trim($_POST['email_bewerber']));

    //Adresse, an welche die Email verschickt wird
    $adresse = "".trim($_POST['email_bewerber'])."";
    //Betreff
    $betreff = "Studiengang: ".name_bewerber($link, $pky_bewerber, 2)."";
    //Header
    $header = "From: ".EMAIL_SEKRETARIAT."\r\n";
    $header .= "Bcc: ".EMAIL_SEKRETARIAT."\r\n";
    //Inhalt der Mail
    $inhalt = "Sehr ".name_bewerber($link, $pky_bewerber, 4)."!\n\n";
    $inhalt .= "Sie haben ein neues Passwort f&uuml;r Ihre Bewerbung f&uuml;r den Studiengang angefordert. ";
    $inhalt .= "Mit diesem Passwort k&ouml;nnen Sie sich in den internen Bereich f&uuml;r Bewerber einloggen.\n";
    $inhalt .= "Folgendes Passwort wurde zuf&auml;llig generiert und Ihnen zugeteilt:\n\n";
    $inhalt .= "neues Passwort: ".$passwort_neu."\n\n";
    $inhalt .= "Sollten Sie sich mit dem neuen Passwort nicht einloggen k&ouml;nnen, wenden Sie sich bitte an: ".EMAIL_SEKRETARIAT.".\n\n";
    $inhalt .= "Diese Email wurde automatisch generiert. Wenn Sie diese f&auml;lschlicherweise erhalten haben, dann hat sich eine andere Person ";
    $inhalt .= "wissentlich oder unwissentlich unter Angabe Ihrer Email Adresse beworben. ";
    $inhalt .= "Sollte dies der Fall sein, kontaktieren Sie bitte die oben angegebene Email Adresse.\n\n";
    $inhalt .= "Mit freundlichen Gr&uuml;&szlig;en,\n\n";
    $inhalt .= "Prof. Dr. Max Mustermann";
    //Mail absenden
    $mail_check = @mail($adresse, $betreff, $inhalt, $header);

    //Wenn die Email versendet werden konnte, dann wird das neue Passwort in die Datenbank eingetragen
    if($mail_check)
    {
        $sql = "UPDATE bewerber
                SET
                    Passwort = MD5('".$passwort_neu."')
                WHERE
                    pky_Bewerber = ".$pky_bewerber.";";
        mysqli_query($link, $sql) OR die(mysqli_error($link));

        //Bei erfolgreicher &AUML;nderung erscheint ein entsprechender Hinweis
        echo("<div class=\"Information\">\n");
        echo("<b>Ihr Passwort wurde erfolgreich ge&auml;ndert!</b><br />");
        echo("Eine Mail mit dem neuen Passwort wurde an <b>\"".trim($_POST['email_bewerber'])."\"</b> gesandt. ");
        echo("Sollten Sie keine Email erhalten haben, wenden Sie sich bitte an <b>\"".EMAIL_SEKRETARIAT."\"</b>.");
        echo("</div><br/>\n");
    }
    else
    {
        echo("<div class=\"Information_Warnung\">\n");
        echo("<b>ACHTUNG!</b><br />");
        echo("Die Email mit dem neuen Passwort konnte nicht versandt werden! ");
        echo("Bitte versuchen Sie es noch einmal (<img src=\"bilder/Pfeil_re.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"> <span class=\"Link1\"><a href=\"index.php?seite=pw_vergessen\">nochmal</a></span>).<br />");
        echo("Sollte wieder der gleiche Warnhinweis erscheinen, wenden Sie sich bitte an <b>\"".EMAIL_SEKRETARIAT."\"</b>.");
        echo("</div><br/>\n");
    }
}
?>

<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1"><a href="index.php?seite=anmeldung">zur&uuml;ck zum internen Bereich f&uuml;r Bewerber</a></span><br/>
<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1"><a href="index.php?seite=start">zur&uuml;ck zur Hauptseite</a></span></span><br/>
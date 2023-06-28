<?php
//wenn die Seite manuell aufgerufen wird und keine Session gestartet wurde,
//dann wird ein Warnhinweis ausgegeben
//$_SESSION['SESSION_PKY_BEWERBER'] ist dann nicht vorhanden
if(isset($_SESSION['SESSION_PKY_BEWERBER']))
{
    //Pr&uuml;fen, ob der Bewerber seine Bewerbung zur&uuml;ckgezogen hat
    $bewerbung_zurueckgezogen = bewerbung_zurueckgezogen($link, $_SESSION['SESSION_PKY_BEWERBER']);

    //Logout Formular
    echo("<form action=\"index.php?seite=anmeldung\" method=\"post\">\n");
    echo("<table border=\"0\" class=\"Tabelle_Logout\">\n");

    echo("<tr>\n");
    echo("<td colspan=\"2\" style=\"text-align:center;\">");
    echo("<u>Willkommen im internen Bereich f&uuml;r Bewerber - Sie sind angemeldet als ".name_bewerber($link, $_SESSION['SESSION_PKY_BEWERBER'], 3)."</u>");
    echo("</td>\n");
    echo("</tr>\n");

    echo("<tr>\n");
    echo("<td>");
    //Navigation f&uuml;r den internen Bereich f&uuml;r Bewerber
    echo("<div id=\"Nav_I\">\n");
    echo("<ul>\n");
    echo("<li><img src=\"bilder/pfeil_unten.gif\" alt=\"\" border=\"0\" width=\"10\" height=\"12\"> <a href=\"index.php?seite=intern_bewerber&intern_b=ib_info\">Informationen zu Ihrer Bewerbung</a></li>\n");
    echo("<li><img src=\"bilder/pfeil_unten.gif\" alt=\"\" border=\"0\" width=\"10\" height=\"12\"> <a href=\"index.php?seite=intern_bewerber&intern_b=ib_einsehen\" id=\"current\">Bewerbung einsehen</a></li>\n");
    echo("<li><img src=\"bilder/pfeil_unten.gif\" alt=\"\" border=\"0\" width=\"10\" height=\"12\"> <a href=\"index.php?seite=intern_bewerber&intern_b=ib_aendern\">Angaben &auml;ndern</a></li>\n");
    if($bewerbung_zurueckgezogen)
    {
        echo("<li><img src=\"bilder/pfeil_unten.gif\" alt=\"\" border=\"0\" width=\"10\" height=\"12\"> <a href=\"index.php?seite=intern_bewerber&intern_b=ib_zurueck\">Bewerbung reaktivieren</a> ");
        echo("<span style=\"color:red; font-weight:bold;\">(Achtung! Bewerbung wurde zur&uuml;ckgezogen)</span>");
        echo("</li>\n");
    }
    else
    {echo("<li><img src=\"bilder/pfeil_unten.gif\" alt=\"\" border=\"0\" width=\"10\" height=\"12\"> <a href=\"index.php?seite=intern_bewerber&intern_b=ib_zurueck\">Bewerbung zur&uuml;ckziehen</a></li>\n");}
    echo("</ul>\n");
    echo("</div>\n");
    echo("</td>\n");
    //Logout-Button
    echo("<td style=\"text-align:right;\" valign=\"bottom\">");
    echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"logout\" value=\">> Ausloggen\">");
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
    echo("</form>\n");

    //includen der richtigen Seite im Inhalt
    if(isset($_GET['intern_b']) AND isset($nav_ib[$_GET['intern_b']]))
    {
        include($nav_ib[$_GET['intern_b']]);
    }
    else
    {
        include($nav_ib['ib_info']);
    }
}
else
{
    echo("<div class=\"Information_Warnung\" style=\"text-align:center;\">\n");
    echo("<b>ACHTUNG!</b><br /><br />");
    echo("<b>Sie haben kein Recht, diesen Bereich zu betreten!</b><br /><br />");
    echo("<img src=\"bilder/Pfeil_re.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"> <span class=\"Link1\"><a href=\"index.php?seite=start\">zur&uuml;ck zur Hauptseite</a></span>");
    echo("</div>\n");
    echo("<div class=\"Abstandhalter_Div\"></div>\n");
}
?>
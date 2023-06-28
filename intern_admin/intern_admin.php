<?php
//wenn die Seite manuell aufgerufen wird und keine Session gestartet wurde,
//dann wird ein Warnhinweis ausgegeben
//$_SESSION['SESSION_PKY_ADMIN'] ist dann nicht vorhanden
if(isset($_SESSION['SESSION_PKY_ADMIN']))
{
    //Logout Formular
    echo("<form action=\"index.php?seite=login\" method=\"post\">\n");
    echo("<table border=\"0\" class=\"Tabelle_Logout\">\n");

    echo("<tr>\n");
    echo("<td colspan=\"2\" style=\"text-align:center;\">");
    echo("<u>Willkommen im internen Bereich f&uuml;r Administratoren - Sie sind angemeldet als ".name_admin($link, $_SESSION['SESSION_PKY_ADMIN'], 3)."</u>");
    echo("</td>\n");
    echo("</tr>\n");

    echo("<tr>\n");
    echo("<td>");
    //Link zur Navigation f&uuml;r den internen Bereich f&uuml;r Administratoren
    echo("<img src=\"bilder/pfeil_unten.gif\" alt=\"\" border=\"0\" width=\"10\" height=\"12\"> <span class=\"Link1\"><a href=\"index.php?seite=intern_admin&intern_a=ia_navigation\">Navigation einblenden</a></span>");
    echo("</td>\n");
    //Logout-Button
    echo("<td style=\"text-align:right;\">");
    echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"logout\" value=\">> Ausloggen\">");
    echo("</td>\n");
    echo("</tr>\n");

    echo("</table>\n");
    echo("</form>\n");

    //includen der richtigen Seite im Inhalt
    if(isset($_GET['intern_a']) AND isset($nav_ia[$_GET['intern_a']]))
    {
        include($nav_ia[$_GET['intern_a']]);
    }
    else
    {
        include($nav_ia['ia_navigation']);
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
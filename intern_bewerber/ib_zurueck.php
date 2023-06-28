<?php
if($bewerbung_zurueckgezogen)
{echo("<div class=\"h1\">Ihre Bewerbung reaktivieren</div>\n");}
else
{echo("<div class=\"h1\">Ihre Bewerbung zur&uuml;ckziehen</div>\n");}

//Hinweis
echo("<div class=\"Information\">\n");
echo("<b>Bitte beachten Sie:</b><br />");
echo("Hier k&ouml;nnen Sie Ihre Bewerbung mit oder ohne Angabe eines Grundes zur&uuml;ckziehen. ");
echo("Wenn Sie Ihre Bewerbung zur&uuml;ckziehen, dann werden Ihre Daten nicht aus dem System gel&ouml;scht, ");
echo("d.h. Sie k&ouml;nnen sich weiterhin bis zur Anmeldefrist in den internen Bereich einloggen. ");
echo("Wenn Sie sich trotzdem bewerben wollen, k&ouml;nnen Sie Ihre Bewerbung auch unter optionaler Angabe eines Grundes wieder reaktivieren. ");
echo("Sollten Sie Ihre Bewerbung zur&uuml;ckziehen, dann werden Sie, ungeachtet Ihrer Leistungen und sonstiger Angaben, nicht zu einem Auswahlgespr&auml;ch eingeladen.");
echo("</div>\n");

//Wenn die Bewerbung bereits zur&uuml;ckgezogen wurde, dann das Formular f&uuml;r die Reaktivierung anzeigen
if($bewerbung_zurueckgezogen)
{
    //Wenn die Seite das erste mal aufgerufen wird, dann wird das Formular angezeigt
    if(!isset($_POST['b_reakt']) AND !isset($_POST['bestaetigung']))
    {
        //Formular Start
        echo("<form action=\"index.php?seite=intern_bewerber&intern_b=ib_zurueck\" method=\"post\">\n");
        echo("<table border=\"0\" cellspacing=\"3\" style=\"width:100%; margin:20px 0 20px 0; padding:3px; background-color:#EEEEEE;\">\n");

        echo("<tr>\n");
        echo("<td style=\"width:10%;\" valign=\"top\" class=\"Zeile_Bezeichnung\">");
        echo("Begr&uuml;ndung:");
        echo("</td>\n");
        echo("<td>");
        echo("<textarea name=\"begruendung\" cols=\"50\" rows=\"4\"></textarea>");
        echo("</td>\n");
        echo("</tr>\n");

        echo("<tr>\n");
        echo("<td colspan=\"2\">");
        echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"b_reakt\" value=\">> Bewerbung reaktivieren\">");
        echo("</td>\n");
        echo("</tr>\n");

        echo("</table>\n");
        echo("</form>\n");
    }
    //Die Aktion best&auml;tigen lassen
    elseif(isset($_POST['b_reakt']) AND !isset($_POST['bestaetigung']))
    {
        echo("<table border=\"0\" cellspacing=\"3\" style=\"width:100%; margin:20px 0 20px 0; padding:3px; background-color:#FFE1E2; color:red;\">\n");

        echo("<tr>\n");
        echo("<td colspan=\"2\" style=\"text-align:center;\">");
        echo("<u><b>Sind Sie wirklich sicher, dass Sie Ihre Bewerbung reaktivieren wollen?</b></u>");
        echo("</td>\n");
        echo("</tr>\n");


        echo("<tr>\n");
        echo("<td colspan=\"2\" style=\"height:20px;\">");
        echo("</td>\n");
        echo("</tr>\n");

        echo("<tr>\n");
        echo("<td style=\"width:50%; padding-right:30px;\" align=\"right\">");
        echo("<form action=\"index.php?seite=intern_bewerber&intern_b=ib_zurueck\" method=\"post\">\n");
        echo("<input type=\"hidden\" name=\"begruendung\" value=\"".trim($_POST['begruendung'])."\">");
        echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"bestaetigung\" value=\">> OK\">");
        echo("</form>\n");
        echo("</td>\n");
        echo("<td style=\"padding-left:px;\">");
        echo("<form action=\"index.php?seite=intern_bewerber&intern_b=ib_zurueck\" method=\"post\">\n");
        echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"abbrechen\" value=\">> Abbrechen\">");
        echo("</form>\n");
        echo("</td>\n");
        echo("</tr>\n");

        echo("</table>\n");
    }
    //Wenn das Reaktivieren der Bewerbung best&auml;tigt wurde
    elseif(!isset($_POST['b_reakt']) AND isset($_POST['bestaetigung']))
    {
        //Tabelle "bewerber" updaten
        $sql = "UPDATE bewerber
                SET
                    Bewerbung_zurueckgezogen = NULL,
                    Datum_reaktiviert = NOW(),
                    Grund_reaktiviert = '".addslashes(htmlXspecialchars($_POST['begruendung']))."'
                WHERE
                    pky_Bewerber = ".$_SESSION['SESSION_PKY_BEWERBER'].";";
        mysqli_query($link, $sql) OR die(mysqli_error($link));

        //Hinweis
        echo("<div class=\"Information\">\n");
        echo("<b>Sie haben Ihre Bewerbung erfolgreich reaktiviert!</b>");
        if($_POST['begruendung'] != "")
        {
            echo("<br /><u>Grund:</u><br />");
            echo("".nl2br(htmlXspecialchars($_POST['begruendung']))."<br /><br />");
        }
        else
        {
            echo("<br /><br />");
        }
        echo("<img src=\"bilder/Pfeil_re.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"> <span class=\"Link1\"><a href=\"index.php?seite=intern_bewerber&intern_b=ib_info\">Bitte hier klicken, um Ihre Ansicht zu aktualisieren</a></span>");
        echo("</div><br />\n");
    }
}

//Wenn die Bewerbung noch nicht zur&uuml;ckgezogen wurde, dann das Formular f&uuml;r Bewerbung zur&uuml;ckziehen anzeigen
else
{
    //Wenn die Seite das erste mal aufgerufen wird, dann wird das Formular angezeigt
    if(!isset($_POST['b_zurueck']) AND !isset($_POST['bestaetigung']))
    {
        //Formular Start
        echo("<form action=\"index.php?seite=intern_bewerber&intern_b=ib_zurueck\" method=\"post\">\n");
        echo("<table border=\"0\" cellspacing=\"3\" style=\"width:100%; margin:20px 0 20px 0; padding:3px; background-color:#EEEEEE;\">\n");

        echo("<tr>\n");
        echo("<td style=\"width:10%;\" valign=\"top\" class=\"Zeile_Bezeichnung\">");
        echo("Begr&uuml;ndung:");
        echo("</td>\n");
        echo("<td>");
        echo("<textarea name=\"begruendung\" cols=\"50\" rows=\"4\"></textarea>");
        echo("</td>\n");
        echo("</tr>\n");

        echo("<tr>\n");
        echo("<td colspan=\"2\">");
        echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"b_zurueck\" value=\">> Bewerbung zur&uuml;ckziehen\">");
        echo("</td>\n");
        echo("</tr>\n");

        echo("</table>\n");
        echo("</form>\n");
    }
    //Die Aktion best&auml;tigen lassen
    elseif(isset($_POST['b_zurueck']) AND !isset($_POST['bestaetigung']))
    {
        echo("<table border=\"0\" cellspacing=\"3\" style=\"width:100%; margin:20px 0 20px 0; padding:3px; background-color:#FFE1E2; color:red;\">\n");

        echo("<tr>\n");
        echo("<td colspan=\"2\" style=\"text-align:center;\">");
        echo("<u><b>Sind Sie wirklich sicher, dass Sie Ihre Bewerbung zur&uuml;ckziehen wollen?</b></u>");
        echo("</td>\n");
        echo("</tr>\n");


        echo("<tr>\n");
        echo("<td colspan=\"2\" style=\"height:20px;\">");
        echo("</td>\n");
        echo("</tr>\n");

        echo("<tr>\n");
        echo("<td style=\"width:50%; padding-right:30px;\" align=\"right\">");
        echo("<form action=\"index.php?seite=intern_bewerber&intern_b=ib_zurueck\" method=\"post\">\n");
        echo("<input type=\"hidden\" name=\"begruendung\" value=\"".trim($_POST['begruendung'])."\">");
        echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"bestaetigung\" value=\">> OK\">");
        echo("</form>\n");
        echo("</td>\n");
        echo("<td style=\"padding-left:px;\">");
        echo("<form action=\"index.php?seite=intern_bewerber&intern_b=ib_zurueck\" method=\"post\">\n");
        echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"abbrechen\" value=\">> Abbrechen\">");
        echo("</form>\n");
        echo("</td>\n");
        echo("</tr>\n");

        echo("</table>\n");
    }
    //Wenn das Zur&uuml;ckziehen der Bewerbung best&auml;tigt wurde
    elseif(!isset($_POST['b_zurueck']) AND isset($_POST['bestaetigung']))
    {
        //Tabelle "bewerber" updaten
        $sql = "UPDATE bewerber
                SET
                    Bewerbung_zurueckgezogen = 1,
                    Datum_zurueckgezogen = NOW(),
                    Grund_zurueckgezogen = '".addslashes(htmlXspecialchars($_POST['begruendung']))."'
                WHERE
                    pky_Bewerber = ".$_SESSION['SESSION_PKY_BEWERBER'].";";
        mysqli_query($link, $sql) OR die(mysqli_error($link));

        //Hinweis
        echo("<div class=\"Information\">\n");
        echo("<b>Sie haben Ihre Bewerbung erfolgreich zur&uuml;ckgezogen!</b>");
        if($_POST['begruendung'] != "")
        {
            echo("<br /><u>Grund:</u><br />");
            echo("".nl2br(htmlXspecialchars($_POST['begruendung']))."<br /><br />");
        }
        else
        {
            echo("<br /><br />");
        }
        echo("<img src=\"bilder/Pfeil_re.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"> <span class=\"Link1\"><a href=\"index.php?seite=intern_bewerber&intern_b=ib_info\">Bitte hier klicken, um Ihre Ansicht zu aktualisieren</a></span>");
        echo("</div><br />\n");
    }
}
?>
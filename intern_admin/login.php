<div class="h1">Anmeldung f&uuml;r den internen Bereich f&uuml;r Administratoren</div>

<?php
//Formatierung der Login-Tabelle festlegen
if(isset($_POST['login_admin']) AND !isset($_SESSION['SESSION_PKY_ADMIN']))
{$class_login_table = "Tabelle_Login_Warnung";}
else
{$class_login_table = "Tabelle_Login";}

//Login Formular
echo("<form action=\"index.php?seite=login\" method=\"post\">\n");
echo("<table border=\"0\" class=\"".$class_login_table."\">\n");

//Bei falscher Anmeldung eine Warnung ausgeben
if(isset($_POST['login_admin']) AND !isset($_SESSION['SESSION_PKY_ADMIN']))
{
    echo("<tr>\n");
    echo("<td colspan=\"2\" style=\"text-align:center; border-bottom:1px solid red;\">");
    echo("Keine oder falsche Angaben bei Email/Passwort!");
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
echo("<td><input name=\"email_admin\" type=\"text\" size=\"25\" maxlength=\"100\"></td>\n");
echo("</tr>\n");

echo("<tr>\n");
echo("<td>Passwort:</td>\n");
echo("<td><input name=\"pw_admin\" type=\"password\" size=\"25\" maxlength=\"100\"></td>\n");
echo("</tr>\n");

echo("<tr>\n");
echo("<td colspan=\"2\">");
echo("<input type=\"submit\" class=\"Buttons_Unten\" name=\"login_admin\" value=\">> Einloggen\">");
echo("</td>\n");
echo("</tr>\n");

echo("</table>\n");
echo("</form>\n");
?>

<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1"><a href="index.php?seite=start">zur&uuml;ck zur Hauptseite</a></span></span><br/>
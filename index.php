<?php
//Warnungen und Hinweise ausgeben
error_reporting(E_ALL);

//Konfigurationsdateien laden
include("config/config.php");

//Zeitzone definieren
if(!ini_get('date.timezone'))
{
    date_default_timezone_set('Europe/Berlin');
}

#######################################
# VERBINDUNG ZUR DATENBANK HERSTELLEN #
#######################################

$link = @mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DATABASE);
//Verbindung &uuml;berpr&uuml;fen
if(mysqli_connect_errno())
{
    echo "Verbindung zu MySQL fehlgeschlagen: ".mysqli_connect_error();
}

###################
# SESSION STARTEN #
###################

//Die Session starten, wenn der interne Bereich f&uuml;r Mitarbeiter angew&auml;hlt wird ($GET Parameter "seite=intern_mitarbeiter")
//ODER wenn der interne Bereich f&uuml;r Bewerber angew&auml;hlt wird ($GET Parameter "seite=intern_bewerber")
//ODER wenn die Login Seite f&uuml;r Bewerber aufgerufen wird ($GET Parameter "seite=anmeldung")
//ODER wenn die Login Seite f&uuml;r Administratoren der Webseite aufgerufen wird ($GET Parameter "seite=login")
if(isset($_GET['seite']) AND ($_GET['seite'] == "intern_admin" OR $_GET['seite'] == "login" OR $_GET['seite'] == "intern_bewerber" OR $_GET['seite'] == "anmeldung"))
{
    session_start();

    //&UUML;berpr&uuml;fen, ob die IP-Adresse des eingeloggten Benutzers gleich bleibt
    ### SESSIONVARIABLE SETZEN ###
    if(!isset($_SESSION['IP_ADRESSE']))
    {
        $_SESSION['IP_ADRESSE'] = $_SERVER['REMOTE_ADDR'];
    }
    if($_SESSION['IP_ADRESSE'] != $_SERVER['REMOTE_ADDR'])
    {
        echo("<div style=\"font-size:1.3em; color:red; border:1px solid red; background-color:#FFE1E2; padding:1em; text-align:center;\">\n");
        echo("<b>Warnung:</b> Sie d&uuml;rfen nicht die Session von einem anderen User benutzten!<br>\n");
        echo("<a href=\"index.php?seite=start\" style=\"color:red;\">Zur&uuml;ck zur Startseite</a>\n");
        echo("</div>\n");
        //Aus Sicherheitsgr&uuml;nden die Abarbeitung sofort beenden
        die();
    }
}

#############################
### LOGIN BEWERBER INTERN ###
#############################

if(isset($_POST['login_bewerber']) AND isset($_POST['email_bewerber']) AND isset($_POST['pw_bewerber']))
{
    //&UUML;berpr&uuml;fen, ob Benutzername und Passwort des Bewerbers richtig sind
    //Au&szlig;erdem wird gepr&uuml;ft, ob der per Email zugesandte Link best&auml;tigt wurde und ob der Account gesperrt wurde
    //Bei erfolgreichem Login gibt die Funktion "1" zur&uuml;ck
    //Ansonsten eine Fehlermeldung als String, welcher bei der Anmeldung ausgegeben wird
    $login_right_bewerber = login_right_bewerber($link, addslashes($_POST['email_bewerber']), addslashes($_POST['pw_bewerber']));
    if($login_right_bewerber == 1)
    {
        ### SESSIONVARIABLE SETZEN ###
        //Den Pky des Bewerbers in der Session speichern
        $_SESSION['SESSION_PKY_BEWERBER'] = pky_bewerber($link, addslashes(trim($_POST['email_bewerber'])));

        //Umleitung zum internen Bereich f&uuml;r Bewerber
        header("Location: ".LINK_INT_BEREICH_BEWERBER."&".session_name()."=".session_id()."");
        exit;
    }
}

####################################
### LOGIN ADMINISTRATOREN INTERN ###
####################################

if(isset($_POST['login_admin']) AND isset($_POST['email_admin']) AND isset($_POST['pw_admin']))
{
    //&UUML;berpr&uuml;fen, ob Benutzername und Passwort des Bewerbers richtig sind
    if(login_right_admin($link, addslashes($_POST['email_admin']), addslashes($_POST['pw_admin'])) == true)
    {
        ### SESSIONVARIABLE SETZEN ###
        //Den Pky des Admins in der Session speichern
        $_SESSION['SESSION_PKY_ADMIN'] = pky_admin($link, addslashes(trim($_POST['email_admin'])));

        //Umleitung zum internen Bereich f&uuml;r Administratoren
        header("Location: ".LINK_INT_BEREICH_ADMIN."&".session_name()."=".session_id()."");
        exit;
    }
}

############################
# SESSION LÖSCHEN / LOGOUT #
############################

//Die Session l&ouml;schen, wenn der Logout Button gedr&uuml;ckt wurde
//ODER wenn es die Session Variable "$_SESSION['SESSION_PKY_BENUTZER']" nicht gibt, dh.
//- beim ersten Aufrufen der Login-Seite
//- wenn der Login nicht erfolgreich war
if(isset($_POST['logout']))
{
    //L&ouml;schen aller Session-Variablen
    $_SESSION = array();
    //Session-Cookies l&ouml;schen
    if(isset($_COOKIE[session_name()]))
    {setcookie(session_name(), '', time()-42000, '/');}
    //Session l&ouml;schen
    session_destroy();
}

##################
# EXPORTFUNKTION #
##################

if(isset($_POST['exportieren']) AND $_POST['exportieren'] = "exportieren")
{
    export($link, $_POST['where_string'], $_POST['order_by_string']);
}

#################
# HTML DOKUMENT #
#################

html_kopf();

//HAUPTTABELLE
echo("<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" id=\"Haupttabelle\">\n");

//Kopfzeile
echo("<tr>\n");
echo("<td id=\"Kopf\">\n");
//////////////////////////
include("kopfzeile.html");
//////////////////////////
echo("</td>\n");
echo("</tr>\n");
echo("<tr><td style=\"height:1px; background-color:red\"></td></tr>\n");
echo("<tr><td style=\"height:10px; background-color:#888888\"></td></tr>\n");

//Inhalt
echo("<tr>\n");
echo("<td id=\"Inhalt\">\n");
//////////////////////
include("inhalt.php");
//////////////////////
echo("</td>\n");
echo("</tr>\n");

//Fusszeile
echo("<tr><td style=\"height:10px; background-color:#888888\"></td></tr>\n");
echo("<tr><td style=\"height:1px; background-color:red\"></td></tr>\n");
echo("<tr>\n");
echo("<td id=\"Fuss\">\n");
//////////////////////////
include("fusszeile.html");
//////////////////////////
echo("</td>\n");
echo("</tr>\n");

echo("</table>\n");

html_fuss();

##################################
# DATENBANKVERBINDUNG SCHLIESSEN #
##################################

if($link)
{
    mysqli_close($link);
}

/*
echo ("<pre>");
var_dump ($_SESSION);
echo ("</pre>");

echo ("<pre>");
var_dump ($_POST);
echo ("</pre>");
*/
?>
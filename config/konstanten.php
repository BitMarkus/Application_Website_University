<?php
#####################################
### Konstanten für lokalen Server ###
#####################################
/*
//Zugangsdaten zur lokalen Datenbank "bewstud"
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');
define('MYSQL_DATABASE', 'bewstud');

//Link, auf welchen die Best&auml;tigungs-Email verweist
define("LINK_AKTIVIERUNG", "http://localhost/StudBew/index.php?seite=aktivierung");
//Link zum internen Bereich der Bewerber
define("LINK_INT_BEREICH_BEWERBER", "http://localhost/StudBew/index.php?seite=intern_bewerber");
//Link zum internen Bereich der Administratoren
define("LINK_INT_BEREICH_ADMIN", "http://localhost/StudBew/index.php?seite=intern_admin");
*/
######################################
### Konstanten für all-inkl Server ###
######################################

//Zugangsdaten zur Datenbank "d03d6c49" auf dem All-Inkl Server
define('MYSQL_HOST', 'reichold-markus.de');
define('MYSQL_USER', 'd03d6c49');
define('MYSQL_PASS', 'kghf2xbhgCpvCtK2aiNY');
define('MYSQL_DATABASE', 'd03d6c49');

//Link, auf welchen die Best&auml;tigungs-Email verweist
define("LINK_AKTIVIERUNG", "http://www.reichold-markus.de/StudBew/index.php?seite=aktivierung");
//Link zum internen Bereich der Bewerber
define("LINK_INT_BEREICH_BEWERBER", "http://www.reichold-markus.de/StudBew/index.php?seite=intern_bewerber");
//Link zum internen Bereich der Administratoren
define("LINK_INT_BEREICH_ADMIN", "http://www.reichold-markus.de/StudBew/index.php?seite=intern_admin");

###################################
# CHARSET UND KOLLATE VON STRINGS #
###################################

define('CHARSET', 'utf8');
define('CHARSET_META', 'UTF-8');
define('COLLATE', 'utf8_general_ci');
/*
define('CHARSET', 'latin1');
define('CHARSET_META', 'ISO-8859-1');
define('COLLATE', 'latin1_general_ci');
*/

#####################################
### Server unabhängige Konstanten ###
#####################################

// Anzahl der Formularseiten bei der Neuanmeldung
define("SEITENANZAHL", 5);
//Pky f&uuml;r Deutschland in der Tabelle "land"
define("PKY_DEUTSCHLAND", 56);
//Pky f&uuml;r das allgemeine Abitur in der Tabelle "hzb"
define("PKY_ALLG_ABITUR", 1);
//Pky f&uuml;r "im Ausland erworbene Hochschulzugangsberechtigung" in der Tabelle "hzb"
define("PKY_HZB_AUSLAND", 2);
//Pky f&uuml;r "sonstige Hochschulzugangsberechtigung" in der Tabelle "hzb"
define("PKY_SONST_HZB", 3);
//Konstante gibt das erste Studienjahr an, in welchem der Studiengang angeboten wurde
define('ERSTES_STUDIENJAHR', 2020);


///////////////////////////////////////////////////
/// Bitte NUR an diesen Konstanten rumschrauben ///
///////////////////////////////////////////////////

//Maximale W&ouml;rter im Lebenslauf (Neuanmeldung)
define("MAX_WOERTER_LEBENSLAUF", 100);
//Maximale W&ouml;rter f&uuml;r die Begr&uuml;ndung (Neuanmeldung)
define("MAX_WOERTER_BEGRUENDUNG", 200);
//Email Adresse, welche bei der Aktivierung als Sender angegeben wird
define("EMAIL_SEKRETARIAT", "sekretariat@test.de");
//Punkte f&uuml;r die Zwischensumme, ab der zum Auswahlgespr&auml;ch eingeladen wird (gleich oder mehr)
define("GRENZE_ZWISCHENSUMME", 85);
//Punkte f&uuml;r die Endsumme, ab der ein Bewerber zum Studiengang zugelassen wird (gleich oder mehr)
define("GRENZE_ENDSUMME", 115);
//Konstante gibt den den Bonus f&uuml;r die HZB Note an, wenn ein Bewerber ein soziales Jahr geleistet hat
define('BONUS_SOZ_JAHR', 0.1);
//Konstante gibt den den Bonus f&uuml;r die HZB Note an, wenn ein Bewerber eine der angegebenen Ausbildungen erfolgreich abgeschlossen hat
define('BONUS_AUSBILDUNG', 0.3);
//Konstante gibt den Faktor an, mit welchem die Punkte der HZB zur berechnung der Zwischensumme multipliziert werden
define('FAKTOR_HZB', 4.6);
//Konstante gibt den Faktor an, mit welchem die Punkte im Fach Mathematik zur berechnung der Zwischensumme multipliziert werden
define('FAKTOR_MATHEMATIK', 1.3);
//Konstante gibt den Faktor an, mit welchem die Punkte im naturw. Fach zur berechnung der Zwischensumme multipliziert werden
define('FAKTOR_NATURWISSENSCHAFT', 1.3);
//Konstante f&uuml;r die Anzahl an Datens&auml;tzen, die bei der Bl&auml;tterfunkton angezeigt werden soll
define('ANZAHL_DATENSATZ_PRO_SUBSEITE_UEBERSICHT', 100);
define('ANZAHL_DATENSATZ_PRO_SUBSEITE_SUCHE', 100);
//Konstante gibt den Tag und den Monat des Anmeldebeginns an
define('ANMELDEBEGINN_D_M', "15.06.");
//Konstante gibt den Tag und den Monat der Anmeldefrist an
define('ANMELDEENDE_D_M', "15.07.");
?>
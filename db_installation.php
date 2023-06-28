<?php
//Warnungen und Hinweise ausgeben
error_reporting(E_ALL);

//Konfigurationsdateien laden
include("config/config.php");

#######################################
# VERBINDUNG ZUR DATENBANK HERSTELLEN #
#######################################

$link = @mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DATABASE);
//Verbindung überprüfen
if(mysqli_connect_errno())
{
    echo "Verbindung zu MySQL fehlgeschlagen: " . mysqli_connect_error();
}

#################
# HTML DOKUMENT #
#################

html_kopf();
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo("<div style=\"font-size:2em; font-weight:bold;\">Datenbankinstallation</div>\n");

##########################
# FORMULAR ZUM AUSFÜHREN #
##########################

if(!isset($_POST['ausfuehren']))
{
    echo("<form action=\"db_installation.php\" method=\"post\">\n");
    echo("<input type=\"submit\" name=\"ausfuehren\" value=\"Ausführen\">\n");
    echo("</form>\n");
}

####################
# TABELLEN ANLEGEN #
####################

if(isset($_POST['ausfuehren']))
{
    ###TABELLE ADMINISTRATOREN###
    $sql = "DROP TABLE IF EXISTS administratoren;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS administratoren
                (pky_Admin      int(10) NOT NULL AUTO_INCREMENT,
                Anrede          char(1) COLLATE ".COLLATE." NOT NULL,
                Vorname         varchar(100) COLLATE ".COLLATE." NOT NULL,
                Nachname        varchar(100) COLLATE ".COLLATE." NOT NULL,
                Email           varchar(100) COLLATE ".COLLATE." NOT NULL,
                Passwort        varchar(32) COLLATE ".COLLATE." NOT NULL,
                PRIMARY KEY     (pky_Admin))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Standardwerte eintragen
    $sql = "INSERT INTO administratoren
                (pky_Admin, Anrede, Vorname, Nachname, Email, Passwort)
            VALUES
                (1, 'h', 'Admin', '', 'admin@a.de', '1bbd886460827015e5d605ed44252251');";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"administratoren\" wurden erfolgreich angelegt!");
    echo("</div>");

    ###TABELLE AUSBILDUNGEN BONUS###
    $sql = "DROP TABLE IF EXISTS ausbildungen_bonus;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS ausbildungen_bonus
                (pky_Ausbildung     int(10) NOT NULL AUTO_INCREMENT,
                Ausbildung          varchar(100) COLLATE ".COLLATE." NOT NULL,
                PRIMARY KEY         (pky_Ausbildung))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Standardwerte eintragen
    $sql = "INSERT INTO ausbildungen_bonus
                (pky_Ausbildung, Ausbildung)
            VALUES
                (1, 'Biologisch technische(r) Assistent(in)'),
                (2, 'Medizinisch technische(r) Assistent(in)'),
                (3, 'Pharmazeutisch technische(r) Assistent(in)'),
                (4, 'Chemisch technische(r) Assistent(in)');";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"ausbildungen_bonus\" wurden erfolgreich angelegt!");
    echo("</div>");

    ###TABELLE AUSWAHLGESPRAECH BEWERBER###
    $sql = "DROP TABLE IF EXISTS auswahlgespraech_bewerber;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS auswahlgespraech_bewerber
                (pky_Auswahlgespraech           int(10) NOT NULL AUTO_INCREMENT,
                fky_Bewerber                    int(10) NOT NULL,
                Erschienen                      int(1) NOT NULL,
                Auswahlgespraech_Datum          date DEFAULT NULL,
                Auswahlgespraech_Uhrzeit_von    time DEFAULT NULL,
                Auswahlgespraech_Uhrzeit_bis    time DEFAULT NULL,
                Auswahlgespraech_Kommentar      text COLLATE ".COLLATE." DEFAULT NULL,
                PRIMARY KEY                     (pky_Auswahlgespraech))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Indizes hinzufügen
    $sql = "ALTER TABLE auswahlgespraech_bewerber ADD INDEX(fky_Bewerber);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"auswahlgespraech_bewerber\" wurden erfolgreich angelegt!");
    echo("</div>");

    ###TABELLE BEWERBER###
    $sql = "DROP TABLE IF EXISTS bewerber;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS bewerber
                (pky_Bewerber               int(10) NOT NULL AUTO_INCREMENT,
                Anrede                      char(1) COLLATE ".COLLATE." NOT NULL,
                Nachname                    varchar(100) COLLATE ".COLLATE." NOT NULL,
                Vorname                     varchar(100) COLLATE ".COLLATE." NOT NULL,
                Geburtsdatum                date NOT NULL,
                Email                       varchar(100) COLLATE ".COLLATE." NOT NULL,
                Nationalitaet_fky_Land      int(4) NOT NULL,
                Passwort                    varchar(32) COLLATE ".COLLATE." NOT NULL,
                Strasse                     varchar(100) COLLATE ".COLLATE." NOT NULL,
                Hausnummer                  varchar(10) COLLATE ".COLLATE." NOT NULL,
                Adresszusatz                varchar(100) COLLATE ".COLLATE." DEFAULT NULL,
                Postleitzahl                varchar(10) COLLATE ".COLLATE." NOT NULL,
                Wohnort                     varchar(100) COLLATE ".COLLATE." NOT NULL,
                fky_Land                    int(4) NOT NULL,
                Datum_Bewerbung             date NOT NULL,
                Datum_Wiederbewerbung       date DEFAULT NULL,
                Datum_Aktivierung           date DEFAULT NULL,
                Datum_Aenderung             date DEFAULT NULL,
                fky_HZB                     int(2) NOT NULL,
                HZB_Sonstige                varchar(100) COLLATE ".COLLATE." DEFAULT NULL,
                HZB_Jahr                    int(4) NOT NULL,
                HZB_Ort                     varchar(100) COLLATE ".COLLATE." NOT NULL,
                HZB_fky_Land                int(4) NOT NULL,
                Soziales_Jahr               int(1) NOT NULL DEFAULT 0,
                fky_Ausbildung              int(10) NOT NULL DEFAULT 0,
                Begruendung                 text COLLATE ".COLLATE." NOT NULL,
                Key_Aktivierung             varchar(32) COLLATE ".COLLATE." DEFAULT NULL,
                Account_gesperrt            int(1) DEFAULT NULL,
                Bewerbung_zurueckgezogen    int(1) DEFAULT NULL,
                Datum_zurueckgezogen        date DEFAULT NULL,
                Grund_zurueckgezogen        text COLLATE ".COLLATE." DEFAULT NULL,
                Datum_reaktiviert           date DEFAULT NULL,
                Grund_reaktiviert           text COLLATE ".COLLATE." DEFAULT NULL,
                Kommentar_Admin             text COLLATE ".COLLATE." DEFAULT NULL,
                Email_Einladung_Absage      int(1) DEFAULT NULL,
                Email_Zusage_Absage         int(1) DEFAULT NULL,
                Bewerber_Zusage_Absage      int(1) DEFAULT NULL,
                PRIMARY KEY                 (pky_Bewerber))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Indizes hinzufügen
    $sql = "ALTER TABLE bewerber ADD INDEX(Nationalitaet_fky_Land);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "ALTER TABLE bewerber ADD INDEX(fky_Land);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "ALTER TABLE bewerber ADD INDEX(fky_HZB);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "ALTER TABLE bewerber ADD INDEX(fky_Ausbildung);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"bewerber\" wurden erfolgreich angelegt!");
    echo("</div>");

    ###TABELLE HZB###
    $sql = "DROP TABLE IF EXISTS hzb;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS hzb
                (pky_HZB        int(10) NOT NULL AUTO_INCREMENT,
                HZB             varchar(50) COLLATE ".COLLATE." NOT NULL,
                PRIMARY KEY     (pky_HZB))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Standardwerte eintragen
    $sql = "INSERT INTO hzb
                (pky_HZB, HZB)
            VALUES
                (1, 'allgemeine Hochschulreife'),
                (2, 'im Ausland erworbene Hochschulzugangsberechtigung'),
                (3, 'sonstige Hochschulzugangsberechtigung');";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"hzb\" wurden erfolgreich angelegt!");
    echo("</div>");

    ###TABELLE KOMISSIONSMITGLIEDER###
    $sql = "DROP TABLE IF EXISTS kommissionsmitglieder;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS kommissionsmitglieder
                (pky_Kommissionsmitglied    int(10) NOT NULL AUTO_INCREMENT,
                Kommissionsmitglied         varchar(100) COLLATE ".COLLATE." NOT NULL,
                PRIMARY KEY                 (pky_Kommissionsmitglied))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Standardwerte eintragen
    $sql = "INSERT INTO kommissionsmitglieder
                (pky_Kommissionsmitglied, Kommissionsmitglied)
            VALUES
                (1, 'Prof. Dr. Max Mustermann'),
                (4, 'Prof. Dr. Berta Musterfrau');";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"kommissionsmitglieder\" wurden erfolgreich angelegt!");
    echo("</div>");

    ###TABELLE LAND###
    $sql = "DROP TABLE IF EXISTS land;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS land
                (pky_Land       int(11) NOT NULL AUTO_INCREMENT,
                Code            char(2) COLLATE ".COLLATE." NOT NULL,
                Land_en         varchar(50) COLLATE ".COLLATE." NOT NULL,
                Land_de         varchar(50) COLLATE ".COLLATE." NOT NULL,
                PRIMARY KEY     (pky_Land))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Standardwerte eintragen
    $sql = "INSERT INTO land
                (pky_Land, Code, Land_en, Land_de)
            VALUES
                (1, 'AD', 'Andorra', 'Andorra'),
                (2, 'AE', 'United Arab Emirates', 'Vereinigte Arabische Emirate'),
                (3, 'AF', 'Afghanistan', 'Afghanistan'),
                (4, 'AG', 'Antigua and Barbuda', 'Antigua und Barbuda'),
                (5, 'AI', 'Anguilla', 'Anguilla'),
                (6, 'AL', 'Albania', 'Albanien'),
                (7, 'AM', 'Armenia', 'Armenien'),
                (8, 'AN', 'Netherlands Antilles', 'Niederländische Antillen'),
                (9, 'AO', 'Angola', 'Angola'),
                (10, 'AQ', 'Antarctica', 'Antarktis'),
                (11, 'AR', 'Argentina', 'Argentinien'),
                (12, 'AS', 'American Samoa', 'Amerikanisch-Samoa'),
                (13, 'AT', 'Austria', 'Österreich'),
                (14, 'AU', 'Australia', 'Australien'),
                (15, 'AW', 'Aruba', 'Aruba'),
                (16, 'AX', 'Aland Islands', 'Åland'),
                (17, 'AZ', 'Azerbaijan', 'Aserbaidschan'),
                (18, 'BA', 'Bosnia and Herzegovina', 'Bosnien und Herzegowina'),
                (19, 'BB', 'Barbados', 'Barbados'),
                (20, 'BD', 'Bangladesh', 'Bangladesch'),
                (21, 'BE', 'Belgium', 'Belgien'),
                (22, 'BF', 'Burkina Faso', 'Burkina Faso'),
                (23, 'BG', 'Bulgaria', 'Bulgarien'),
                (24, 'BH', 'Bahrain', 'Bahrain'),
                (25, 'BI', 'Burundi', 'Burundi'),
                (26, 'BJ', 'Benin', 'Benin'),
                (27, 'BM', 'Bermuda', 'Bermuda'),
                (28, 'BN', 'Brunei', 'Brunei Darussalam'),
                (29, 'BO', 'Bolivia', 'Bolivien'),
                (30, 'BR', 'Brazil', 'Brasilien'),
                (31, 'BS', 'Bahamas', 'Bahamas'),
                (32, 'BT', 'Bhutan', 'Bhutan'),
                (33, 'BV', 'Bouvet Island', 'Bouvetinsel'),
                (34, 'BW', 'Botswana', 'Botswana'),
                (35, 'BY', 'Belarus', 'Belarus (Weißrussland)'),
                (36, 'BZ', 'Belize', 'Belize'),
                (37, 'CA', 'Canada', 'Kanada'),
                (38, 'CC', 'Cocos (Keeling) Islands', 'Kokosinseln (Keelinginseln)'),
                (39, 'CD', 'Congo (Kinshasa)', 'Kongo'),
                (40, 'CF', 'Central African Republic', 'Zentralafrikanische Republik'),
                (41, 'CG', 'Congo (Brazzaville)', 'Republik Kongo'),
                (42, 'CH', 'Switzerland', 'Schweiz'),
                (43, 'CI', 'Ivory Coast', 'Elfenbeinküste'),
                (44, 'CK', 'Cook Islands', 'Cookinseln'),
                (45, 'CL', 'Chile', 'Chile'),
                (46, 'CM', 'Cameroon', 'Kamerun'),
                (47, 'CN', 'China', 'China, Volksrepublik'),
                (48, 'CO', 'Colombia', 'Kolumbien'),
                (49, 'CR', 'Costa Rica', 'Costa Rica'),
                (50, 'CS', 'Serbia And Montenegro', 'Serbien und Montenegro'),
                (51, 'CU', 'Cuba', 'Kuba'),
                (52, 'CV', 'Cape Verde', 'Kap Verde'),
                (53, 'CX', 'Christmas Island', 'Weihnachtsinsel'),
                (54, 'CY', 'Cyprus', 'Zypern'),
                (55, 'CZ', 'Czech Republic', 'Tschechische Republik'),
                (56, 'DE', 'Germany', 'Deutschland'),
                (57, 'DJ', 'Djibouti', 'Dschibuti'),
                (58, 'DK', 'Denmark', 'Dänemark'),
                (59, 'DM', 'Dominica', 'Dominica'),
                (60, 'DO', 'Dominican Republic', 'Dominikanische Republik'),
                (61, 'DZ', 'Algeria', 'Algerien'),
                (62, 'EC', 'Ecuador', 'Ecuador'),
                (63, 'EE', 'Estonia', 'Estland (Reval)'),
                (64, 'EG', 'Egypt', 'Ägypten'),
                (65, 'EH', 'Western Sahara', 'Westsahara'),
                (66, 'ER', 'Eritrea', 'Eritrea'),
                (67, 'ES', 'Spain', 'Spanien'),
                (68, 'ET', 'Ethiopia', 'Äthiopien'),
                (69, 'FI', 'Finland', 'Finnland'),
                (70, 'FJ', 'Fiji', 'Fidschi'),
                (71, 'FK', 'Falkland Islands', 'Falklandinseln (Malwinen)'),
                (72, 'FM', 'Micronesia', 'Mikronesien'),
                (73, 'FO', 'Faroe Islands', 'Färöer'),
                (74, 'FR', 'France', 'Frankreich'),
                (75, 'GA', 'Gabon', 'Gabun'),
                (76, 'GB', 'United Kingdom', 'Großbritannien und Nordirland'),
                (77, 'GD', 'Grenada', 'Grenada'),
                (78, 'GE', 'Georgia', 'Georgien'),
                (79, 'GF', 'French Guiana', 'Französisch-Guayana'),
                (80, 'GG', 'Guernsey', 'Guernsey (Kanalinsel)'),
                (81, 'GH', 'Ghana', 'Ghana'),
                (82, 'GI', 'Gibraltar', 'Gibraltar'),
                (83, 'GL', 'Greenland', 'Grönland'),
                (84, 'GM', 'Gambia', 'Gambia'),
                (85, 'GN', 'Guinea', 'Guinea'),
                (86, 'GP', 'Guadeloupe', 'Guadeloupe'),
                (87, 'GQ', 'Equatorial Guinea', 'Äquatorialguinea'),
                (88, 'GR', 'Greece', 'Griechenland'),
                (89, 'GS', 'South Georgia and the South Sandwich Islands', 'Südgeorgien und die Südl. Sandwichinseln'),
                (90, 'GT', 'Guatemala', 'Guatemala'),
                (91, 'GU', 'Guam', 'Guam'),
                (92, 'GW', 'Guinea-Bissau', 'Guinea-Bissau'),
                (93, 'GY', 'Guyana', 'Guyana'),
                (94, 'HK', 'Hong Kong S.A.R., China', 'Hongkong'),
                (95, 'HM', 'Heard Island and McDonald Islands', 'Heard- und McDonald-Inseln'),
                (96, 'HN', 'Honduras', 'Honduras'),
                (97, 'HR', 'Croatia', 'Kroatien'),
                (98, 'HT', 'Haiti', 'Haiti'),
                (99, 'HU', 'Hungary', 'Ungarn'),
                (100, 'ID', 'Indonesia', 'Indonesien'),
                (101, 'IE', 'Ireland', 'Irland'),
                (102, 'IL', 'Israel', 'Israel'),
                (103, 'IM', 'Isle of Man', 'Insel Man'),
                (104, 'IN', 'India', 'Indien'),
                (105, 'IO', 'British Indian Ocean Territory', 'Britisches Territorium im Indischen Ozean'),
                (106, 'IQ', 'Iraq', 'Irak'),
                (107, 'IR', 'Iran', 'Iran'),
                (108, 'IS', 'Iceland', 'Island'),
                (109, 'IT', 'Italy', 'Italien'),
                (110, 'JE', 'Jersey', 'Jersey (Kanalinsel)'),
                (111, 'JM', 'Jamaica', 'Jamaika'),
                (112, 'JO', 'Jordan', 'Jordanien'),
                (113, 'JP', 'Japan', 'Japan'),
                (114, 'KE', 'Kenya', 'Kenia'),
                (115, 'KG', 'Kyrgyzstan', 'Kirgisistan'),
                (116, 'KH', 'Cambodia', 'Kambodscha'),
                (117, 'KI', 'Kiribati', 'Kiribati'),
                (118, 'KM', 'Comoros', 'Komoren'),
                (119, 'KN', 'Saint Kitts and Nevis', 'St. Kitts und Nevis'),
                (120, 'KP', 'North Korea', 'Nordkorea'),
                (121, 'KR', 'South Korea', 'Südkorea'),
                (122, 'KW', 'Kuwait', 'Kuwait'),
                (123, 'KY', 'Cayman Islands', 'Kaimaninseln'),
                (124, 'KZ', 'Kazakhstan', 'Kasachstan'),
                (125, 'LA', 'Laos', 'Laos'),
                (126, 'LB', 'Lebanon', 'Libanon'),
                (127, 'LC', 'Saint Lucia', 'St. Lucia'),
                (128, 'LI', 'Liechtenstein', 'Liechtenstein'),
                (129, 'LK', 'Sri Lanka', 'Sri Lanka'),
                (130, 'LR', 'Liberia', 'Liberia'),
                (131, 'LS', 'Lesotho', 'Lesotho'),
                (132, 'LT', 'Lithuania', 'Litauen'),
                (133, 'LU', 'Luxembourg', 'Luxemburg'),
                (134, 'LV', 'Latvia', 'Lettland'),
                (135, 'LY', 'Libya', 'Libyen'),
                (136, 'MA', 'Morocco', 'Marokko'),
                (137, 'MC', 'Monaco', 'Monaco'),
                (138, 'MD', 'Moldova', 'Moldawien'),
                (139, 'MG', 'Madagascar', 'Madagaskar'),
                (140, 'MH', 'Marshall Islands', 'Marshallinseln'),
                (141, 'MK', 'Macedonia', 'Mazedonien'),
                (142, 'ML', 'Mali', 'Mali'),
                (143, 'MM', 'Myanmar', 'Myanmar (Burma)'),
                (144, 'MN', 'Mongolia', 'Mongolei'),
                (145, 'MO', 'Macao S.A.R., China', 'Macao'),
                (146, 'MP', 'Northern Mariana Islands', 'Nördliche Marianen'),
                (147, 'MQ', 'Martinique', 'Martinique'),
                (148, 'MR', 'Mauritania', 'Mauretanien'),
                (149, 'MS', 'Montserrat', 'Montserrat'),
                (150, 'MT', 'Malta', 'Malta'),
                (151, 'MU', 'Mauritius', 'Mauritius'),
                (152, 'MV', 'Maldives', 'Malediven'),
                (153, 'MW', 'Malawi', 'Malawi'),
                (154, 'MX', 'Mexico', 'Mexiko'),
                (155, 'MY', 'Malaysia', 'Malaysia'),
                (156, 'MZ', 'Mozambique', 'Mosambik'),
                (157, 'NA', 'Namibia', 'Namibia'),
                (158, 'NC', 'New Caledonia', 'Neukaledonien'),
                (159, 'NE', 'Niger', 'Niger'),
                (160, 'NF', 'Norfolk Island', 'Norfolkinsel'),
                (161, 'NG', 'Nigeria', 'Nigeria'),
                (162, 'NI', 'Nicaragua', 'Nicaragua'),
                (163, 'NL', 'Netherlands', 'Niederlande'),
                (164, 'NO', 'Norway', 'Norwegen'),
                (165, 'NP', 'Nepal', 'Nepal'),
                (166, 'NR', 'Nauru', 'Nauru'),
                (167, 'NU', 'Niue', 'Niue'),
                (168, 'NZ', 'New Zealand', 'Neuseeland'),
                (169, 'OM', 'Oman', 'Oman'),
                (170, 'PA', 'Panama', 'Panama'),
                (171, 'PE', 'Peru', 'Peru'),
                (172, 'PF', 'French Polynesia', 'Französisch-Polynesien'),
                (173, 'PG', 'Papua New Guinea', 'Papua-Neuguinea'),
                (174, 'PH', 'Philippines', 'Philippinen'),
                (175, 'PK', 'Pakistan', 'Pakistan'),
                (176, 'PL', 'Poland', 'Polen'),
                (177, 'PM', 'Saint Pierre and Miquelon', 'St. Pierre und Miquelon'),
                (178, 'PN', 'Pitcairn', 'Pitcairninseln'),
                (179, 'PR', 'Puerto Rico', 'Puerto Rico'),
                (180, 'PS', 'Palestinian Territory', 'Palästinensische Autonomiegebiete'),
                (181, 'PT', 'Portugal', 'Portugal'),
                (182, 'PW', 'Palau', 'Palau'),
                (183, 'PY', 'Paraguay', 'Paraguay'),
                (184, 'QA', 'Qatar', 'Katar'),
                (185, 'RE', 'Reunion', 'Réunion'),
                (186, 'RO', 'Romania', 'Rumänien'),
                (187, 'RU', 'Russia', 'Russische Föderation'),
                (188, 'RW', 'Rwanda', 'Ruanda'),
                (189, 'SA', 'Saudi Arabia', 'Saudi-Arabien'),
                (190, 'SB', 'Solomon Islands', 'Salomonen'),
                (191, 'SC', 'Seychelles', 'Seychellen'),
                (192, 'SD', 'Sudan', 'Sudan'),
                (193, 'SE', 'Sweden', 'Schweden'),
                (194, 'SG', 'Singapore', 'Singapur'),
                (195, 'SH', 'Saint Helena', 'St. Helena'),
                (196, 'SI', 'Slovenia', 'Slowenien'),
                (197, 'SJ', 'Svalbard and Jan Mayen', 'Svalbard und Jan Mayen'),
                (198, 'SK', 'Slovakia', 'Slowakei'),
                (199, 'SL', 'Sierra Leone', 'Sierra Leone'),
                (200, 'SM', 'San Marino', 'San Marino'),
                (201, 'SN', 'Senegal', 'Senegal'),
                (202, 'SO', 'Somalia', 'Somalia'),
                (203, 'SR', 'Suriname', 'Suriname'),
                (204, 'ST', 'Sao Tome and Principe', 'São Tomé und Príncipe'),
                (205, 'SV', 'El Salvador', 'El Salvador'),
                (206, 'SY', 'Syria', 'Syrien'),
                (207, 'SZ', 'Swaziland', 'Swasiland'),
                (208, 'TC', 'Turks and Caicos Islands', 'Turks- und Caicosinseln'),
                (209, 'TD', 'Chad', 'Tschad'),
                (210, 'TF', 'French Southern Territories', 'Französische Süd- und Antarktisgebiete'),
                (211, 'TG', 'Togo', 'Togo'),
                (212, 'TH', 'Thailand', 'Thailand'),
                (213, 'TJ', 'Tajikistan', 'Tadschikistan'),
                (214, 'TK', 'Tokelau', 'Tokelau'),
                (215, 'TL', 'East Timor', 'Timor-Leste'),
                (216, 'TM', 'Turkmenistan', 'Turkmenistan'),
                (217, 'TN', 'Tunisia', 'Tunesien'),
                (218, 'TO', 'Tonga', 'Tonga'),
                (219, 'TR', 'Turkey', 'Türkei'),
                (220, 'TT', 'Trinidad and Tobago', 'Trinidad und Tobago'),
                (221, 'TV', 'Tuvalu', 'Tuvalu'),
                (222, 'TW', 'Taiwan', 'Taiwan'),
                (223, 'TZ', 'Tanzania', 'Tansania'),
                (224, 'UA', 'Ukraine', 'Ukraine'),
                (225, 'UG', 'Uganda', 'Uganda'),
                (226, 'UM', 'United States Minor Outlying Islands', 'Amerikanisch-Ozeanien'),
                (227, 'US', 'United States', 'Vereinigte Staaten von Amerika'),
                (228, 'UY', 'Uruguay', 'Uruguay'),
                (229, 'UZ', 'Uzbekistan', 'Usbekistan'),
                (230, 'VA', 'Vatican', 'Vatikanstadt'),
                (231, 'VC', 'Saint Vincent and the Grenadines', 'St. Vincent und die Grenadinen'),
                (232, 'VE', 'Venezuela', 'Venezuela'),
                (233, 'VG', 'British Virgin Islands', 'Britische Jungferninseln'),
                (234, 'VI', 'U.S. Virgin Islands', 'Amerikanische Jungferninseln'),
                (235, 'VN', 'Vietnam', 'Vietnam'),
                (236, 'VU', 'Vanuatu', 'Vanuatu'),
                (237, 'WF', 'Wallis and Futuna', 'Wallis und Futuna'),
                (238, 'WS', 'Samoa', 'Samoa'),
                (239, 'YE', 'Yemen', 'Jemen'),
                (240, 'YT', 'Mayotte', 'Mayotte'),
                (241, 'ZA', 'South Africa', 'Südafrika'),
                (242, 'ZM', 'Zambia', 'Sambia'),
                (243, 'ZW', 'Zimbabwe', 'Simbabwe');";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"land\" wurden erfolgreich angelegt!");
    echo("</div>");

    ###TABELLE LEBENSLAUF BEWERBER###
    $sql = "DROP TABLE IF EXISTS lebenslauf_bewerber;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS lebenslauf_bewerber
                (pky_Lebenslauf_Eintrag int(10) NOT NULL AUTO_INCREMENT,
                fky_Bewerber            int(10) NOT NULL,
                Nr_Eintrag              int(2) NOT NULL,
                Datum_am_von            varchar(10) COLLATE ".COLLATE." NOT NULL,
                Datum_bis               varchar(10) COLLATE ".COLLATE." NOT NULL,
                Eintrag text            COLLATE ".COLLATE." NOT NULL,
                PRIMARY KEY             (pky_Lebenslauf_Eintrag))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Indizes hinzufügen
    $sql = "ALTER TABLE lebenslauf_bewerber ADD INDEX(fky_Bewerber);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"lebenslauf_bewerber\" wurden erfolgreich angelegt!");
    echo("</div>");

    ###TABELLE LEISTUNGEN BEWERBER###
    $sql = "DROP TABLE IF EXISTS leistungen_bewerber;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS leistungen_bewerber
                (pky_Leistungen         int(10) NOT NULL AUTO_INCREMENT,
                fky_Bewerber            int(10) NOT NULL,
                Leistungen_Art          char(1) COLLATE ".COLLATE." NOT NULL DEFAULT '',
                HZB_Note                float(3,2) NOT NULL,
                HZB_Punkte              float(4,2) NOT NULL,
                Naturw_belegt           int(1) NOT NULL,
                fky_Naturw_Fach         int(2) DEFAULT NULL,
                Naturw_HJ_1_Note        float(3,2) DEFAULT NULL,
                Naturw_HJ_2_Note        float(3,2) DEFAULT NULL,
                Naturw_HJ_3_Note        float(3,2) DEFAULT NULL,
                Naturw_HJ_4_Note        float(3,2) DEFAULT NULL,
                Naturw_End_Note         float(3,2) DEFAULT NULL,
                Naturw_HJ_1_Punkte      float(4,2) DEFAULT NULL,
                Naturw_HJ_2_Punkte      float(4,2) DEFAULT NULL,
                Naturw_HJ_3_Punkte      float(4,2) DEFAULT NULL,
                Naturw_HJ_4_Punkte      float(4,2) DEFAULT NULL,
                Naturw_End_Punkte       float(4,2) DEFAULT NULL,
                Mathe_belegt            int(1) NOT NULL,
                Mathe_HJ_1_Note         float(3,2) DEFAULT NULL,
                Mathe_HJ_2_Note         float(3,2) DEFAULT NULL,
                Mathe_HJ_3_Note         float(3,2) DEFAULT NULL,
                Mathe_HJ_4_Note         float(3,2) DEFAULT NULL,
                Mathe_End_Note          float(3,2) DEFAULT NULL,
                Mathe_HJ_1_Punkte       float(4,2) DEFAULT NULL,
                Mathe_HJ_2_Punkte       float(4,2) DEFAULT NULL,
                Mathe_HJ_3_Punkte       float(4,2) DEFAULT NULL,
                Mathe_HJ_4_Punkte       float(4,2) DEFAULT NULL,
                Mathe_End_Punkte        float(4,2) DEFAULT NULL,
                Zwischensumme           int(3) DEFAULT NULL,
                Auswahlgespraech        int(1) DEFAULT NULL,
                Fachkompetenz           int(2) DEFAULT NULL,
                Sozialkompetenz         int(2) DEFAULT NULL,
                Auswahlgespraech_Summe  float(3,1) DEFAULT NULL,
                Endsumme                int(3) DEFAULT NULL,
                PRIMARY KEY             (pky_Leistungen))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Indizes hinzufügen
    $sql = "ALTER TABLE leistungen_bewerber ADD INDEX(fky_Bewerber);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"leistungen_bewerber\" wurden erfolgreich angelegt!");
    echo("</div>");

    ###TABELLE NATURW FACH###
    $sql = "DROP TABLE IF EXISTS naturw_fach;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS naturw_fach
                (pky_naturw_Fach    int(10) NOT NULL AUTO_INCREMENT,
                naturw_Fach         varchar(50) COLLATE ".COLLATE." NOT NULL,
                PRIMARY KEY         (pky_naturw_Fach))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));  
    //Standardwerte eintragen
    $sql = "INSERT INTO naturw_fach
                (pky_naturw_Fach, naturw_Fach)
            VALUES
                (1, 'Biologie'),
                (2, 'Physik'),
                (3, 'Astronomie (Astrophysik)'),
                (4, 'Informatik, Textverarbeitung'),
                (5, 'Chemie'),
                (7, 'Technik, Werken'),
                (8, 'Technisches Zeichnen/Darstellende Geometrie'),
                (9, 'NWT (Naturwissenschaft und Technik)');";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"naturw_fach\" wurden erfolgreich angelegt!");
    echo("</div>");  

    ###TABELLE TERMIN KOMISSION BEWERBER###
    $sql = "DROP TABLE IF EXISTS termin_kommission_bewerber;";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "CREATE TABLE IF NOT EXISTS termin_kommission_bewerber
                (pky_Termin                 int(10) NOT NULL AUTO_INCREMENT,
                fky_Bewerber                int(10) NOT NULL,
                Datum_Termin                date NOT NULL,
                Uhrzeit_Termin              time NOT NULL,
                fky_Kommissionsmitglied_1   int(10) NOT NULL DEFAULT 0,
                fky_Kommissionsmitglied_2   int(10) NOT NULL DEFAULT 0,
                fky_Kommissionsmitglied_3   int(10) NOT NULL DEFAULT 0,
                fky_Kommissionsmitglied_4   int(10) NOT NULL DEFAULT 0,
                PRIMARY KEY                 (pky_Termin))
                ENGINE=MyISAM DEFAULT CHARSET=".CHARSET." COLLATE=".COLLATE.";";
    mysqli_query($link, $sql) OR die(mysqli_error($link));  
    //Indizes hinzufügen
    $sql = "ALTER TABLE termin_kommission_bewerber ADD INDEX(fky_Bewerber);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "ALTER TABLE termin_kommission_bewerber ADD INDEX(fky_Kommissionsmitglied_1);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "ALTER TABLE termin_kommission_bewerber ADD INDEX(fky_Kommissionsmitglied_2);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "ALTER TABLE termin_kommission_bewerber ADD INDEX(fky_Kommissionsmitglied_3);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    $sql = "ALTER TABLE termin_kommission_bewerber ADD INDEX(fky_Kommissionsmitglied_4);";
    mysqli_query($link, $sql) OR die(mysqli_error($link));
    //Hinweis
    echo("<div class=\"Hinweis\">");
    echo("Die Tabelle \"termin_kommission_bewerber\" wurden erfolgreich angelegt!");
    echo("</div>");

    //Hinweis
    echo("<div style=\"font-weight:bold;\">");
    echo("Alle Tabellen wurden erfolgreich angelegt!");
    echo("</div>");
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
html_fuss();

##################################
# DATENBANKVERBINDUNG SCHLIESSEN #
##################################

mysqli_close($link);

?>
<?php
//Array nav mit allen Pfaden zu den Seiten
$nav = array();
$nav['start'] = "seiten/start.php";
$nav['lehre'] = "seiten/lehre.php";

//Seiten für die Online-Anmeldung für Bewerber
$nav['anmeldung'] = "anmeldung/anmeldung.php";                       //Loginformular für den int. Bereich der Bewerber und Link zur Neuanmeldung
$nav['neuanmeldung'] = "anmeldung/neuanmeldung.php";
$nav['aktivierung'] = "anmeldung/aktivierung.php";
$nav['pw_vergessen'] = "anmeldung/pw_vergessen.php";

//Seiten für den internen Bereich der Bewerber
$nav['intern_bewerber'] = "intern_bewerber/intern_bewerber.php";     //Navigation für den int. Bereich der Bewerber und includen der einzelnen Seiten
$nav_ib = array();
$nav_ib['ib_info'] = "intern_bewerber/ib_info.php";
$nav_ib['ib_einsehen'] = "intern_bewerber/ib_einsehen.php";
$nav_ib['ib_aendern'] = "intern_bewerber/ib_aendern.php";
$nav_ib['ib_zurueck'] = "intern_bewerber/ib_zurueck.php";
$nav_ib['ib_neu'] = "intern_bewerber/ib_neu.php";

//Seiten für den internen Bereich der Administratoren der Webseite
$nav['login'] = "intern_admin/login.php";                           //Loginformular für den int. Bereich der Administratoren
$nav['intern_admin'] = "intern_admin/intern_admin.php";             //Link zur Navigation für den int. Bereich der Administratoren und includen der einzelnen Seiten
$nav_ia = array();
$nav_ia['ia_navigation'] = "intern_admin/ia_navigation.php";        //Navigation für den int. Bereich der Administratoren
$nav_ia['bewerber_einsehen'] = "intern_admin/bewerber/bewerber_einsehen.php";
$nav_ia['bewerber_aktionen'] = "intern_admin/bewerber/bewerber_aktionen.php";
$nav_ia['bewerber_suchen'] = "intern_admin/bewerber/bewerber_suchen.php";
?>
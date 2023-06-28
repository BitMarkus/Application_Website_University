<div class="h1">Studieninformationen</div>

<!--Unternavigation-->
<ul style="list-style-type:none; background-color:#EEEEEE">
<li><img src="bilder/pfeil_unten.gif" width="10" height="12" border="0" alt=""> <span class="Link1"><a href="#info_1">&Uuml;ber den Studiengang</a></span></li>
<li><img src="bilder/pfeil_unten.gif" width="10" height="12" border="0" alt=""> <span class="Link1"><a href="#info_4">F&uuml;r wen ist der Studiengang geeignet?</a></span></li>
<li><img src="bilder/pfeil_unten.gif" width="10" height="12" border="0" alt=""> <span class="Link1"><a href="#info_5">Online-Bewerbung</a></span></li>
<li><img src="bilder/pfeil_unten.gif" width="10" height="12" border="0" alt=""> <span class="Link1"><a href="#info_7">Infobrosch&uuml;re und FAQs</a></span></li>
<li><img src="bilder/pfeil_unten.gif" width="10" height="12" border="0" alt=""> <span class="Link1"><a href="#info_8">Infos f&uuml;r Studienanf&auml;nger</a></span></li>
<li><img src="bilder/pfeil_unten.gif" width="10" height="12" border="0" alt=""> <span class="Link1"><a href="#info_9">Lehre</a></span></li>
<li><img src="bilder/pfeil_unten.gif" width="10" height="12" border="0" alt=""> <span class="Link1"><a href="#info_10">Ansprechpartner</a></span></li>
</ul>

<!--Info 1-->
<div class="Info">
<a name="info_1"><div class="h2">&Uuml;ber den Studiengang</div></a>
<div>An der Universit&auml;t wird ab dem Wintersemester ein neuer Bachelor-Studiengang "Studiengang" (6 Semester) mit anschlie&szlig;endem 
Master-Studiengang (4 Semester) angeboten. An den Master wird sich dann im Regelfall eine Doktorarbeit anschlie&szlig;en.</div>
</div>

<!--Info 2-->
<div class="Info">
<a name="info_4"><div class="h2">F&uuml;r wen ist der Studiengang geeignet?</div></a>
<div>Der Bachelor-Studiengang bietet naturwissenschaftlich begabten und motivierten Abiturienten eine moderne,
attraktive und intensive naturwissenschaftliche Ausbildung.</div>
</div>

<!--Info 3-->
<div class="Info">
<a name="info_5"><div class="h2">Wie kann ich mich bewerben?</div></a>
<?php
//Wenn das aktuelle Datum innerhalb der Bewerbungsperiode liegt wird der Link zur Online-Bewerbung angezeigt
if(bewerbungsperiode())
{
    echo("<div>\n");
    echo("F&uuml;r den Bachelor-Studiengang ist das Bestehen einer Eignungsfeststellungspr&uuml;fung erforderlich. Die Zulassung erfolgt jeweils zum Wintersemester. \n");
    echo("Die Bewerbung ist nur innerhalb der Bewerbungsperiode m&ouml;glich (".ANMELDEBEGINN_D_M." - ".ANMELDEENDE_D_M.") und erfolgt ausschlie&szlig;lich &uuml;ber das unten angegebene Online-Formular (siehe Link). \n");
    echo("</div>\n");
    echo("<img src=\"bilder/Pfeil_re.gif\" alt=\"\" border=\"0\" width=\"12\" height=\"10\"> <span class=\"Link2\"><a href=\"index.php?seite=anmeldung\">zur Online-Bewerbung</a></span>\n");
}
else
{
    echo("<div>\n");
    echo("F&uuml;r den Bachelor-Studiengang ist das Bestehen einer Eignungsfeststellungspr&uuml;fung erforderlich. Die Zulassung erfolgt jeweils zum Wintersemester. \n");
    echo("Die Bewerbung erfolgt ausschlie&szlig;lich &uuml;ber ein Online-Formular und ist nur innerhalb der Bewerbungsperiode m&ouml;glich (".ANMELDEBEGINN_D_M." - ".ANMELDEENDE_D_M."). \n");
    echo("</div>\n");
}
?>
</div>

<!--Info 4-->
<div class="Info">
<a name="info_7"><div class="h2">Infobrosch&uuml;re und FAQs</div></a>
<div>
Hier k&ouml;nnen Sie eine
<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1">
<a href="https://WWW.TOTE-LINKS.DE/broschuere-studiengang.pdf" target="blank">Brosch&uuml;re (toter Link!)</a>
</span> mit weiteren Information und h&auml;ufig gestellten Fragen herunterladen.<br>
</div>
</div>

<!--Info 5-->
<div class="Info">
<a name="info_8"><div class="h2">Infos f&uuml;r Studienanf&auml;nger</div></a>
<div>
Die Stundenpl&auml;ne und das entsprechende Vorlesungsverzeichnis k&ouml;nnen 
<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1">
<a href="https://WWW.TOTE-LINKS.DE" target="blank">hier (toter Link!)</a>
</span> 
eingesehen und heruntergeladen werden.
</div>
</div>

<!--Info 6-->
<div class="Info">
<a name="info_9"><div class="h2">Lehre</div></a>
<div>
Skripte zu Vorlesungen und Seminare k&ouml;nnen 
<img src="bilder/Pfeil_re.gif" alt="" border="0" width="12" height="10"> <span class="Link1">
<a href="https://WWW.TOTE-LINKS.DE" target="blank">hier (toter Link!)</a>
</span> 
heruntergeladen werden (Login erforderlich).
</div>
</div>

<!--Info 7-->
<div class="Info">
<a name="info_10"><div class="h2">Ansprechpartner</div></a>

<div style="float:right; margin-right:350px;">
<b><u>Fakult&auml;t:</u></b><br>
<b>Prof. Dr. Berta Musterfrau</b><br>
Universit&auml;t<br>
Institut<br>
Musterstra&szlig;e 1<br>
123456 Musterstadt<br>
Tel.: 123456<br>
Fax: 123456<br>
</div>

<div>
<b><u>Fakult&auml;t:</u></b><br>
<b>Prof. Dr. Max Mustermann</b><br>
Universit&auml;t<br>
Institut<br>
Musterstra&szlig;e 1<br>
123456 Musterstadt<br>
Tel.: 123456<br>
Fax: 123456<br>
</div>

</div>
<!--Info 10 ende-->
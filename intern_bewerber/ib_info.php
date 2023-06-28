<div class="h1">Informationen zu Ihrer Bewerbung</div>

<div style="margin:0 0 25px 0;">
<?php
echo("Sehr ".name_bewerber($link, $_SESSION['SESSION_PKY_BEWERBER'], 4).",<br /><br />");

//Wenn die Bewerbung zur&uuml;ckgezogen wurde, dann wird ein anderer Text angezeigt
if(!$bewerbung_zurueckgezogen)
{
    echo("Vielen Dank f&uuml;r Ihre Bewerbung f&uuml;r den Bachelorstudiengang. Ihre Bewerbung ist erfolgreich bei uns eingegangen.<br />");
    echo("Im Folgenden sind wichtige Informationen des Auswahlverfahrens f&uuml;r Sie zusammengestellt:<br /><br />");

    echo("<u>Hinweise zum Verfahren der Studienplatzvergabe</u><br />");
    echo("Die Vergabe der Studienpl&auml;tze erfolgt &uuml;ber ein sogenanntes \"Eignungsfeststellungsverfahren\". Bei dem zweistufigen Verfahren werden zun&auml;chst Ihre Angaben aus ");
    echo("der Online-Bewerbung (1. Stufe) bewertet. Bei ausreichend hohem Ergebnis werden Sie zum Auswahlgespr&auml;ch (2. Stufe) eingeladen, welches ebenfalls benotet wird. ");
    echo("F&uuml;r die Feststellung Ihrer Eignung zum Bachelorstudium werden die Leistungen aus Stufe 1 und 2 miteinander verrechnet. ");
    echo("Bei einem ausreichend hohem Endergebnis bekommen Sie dann einen Studienplatz angeboten.<br /><br />");

    echo("<u>Auswahlgespr&auml;ch</u><br />");
    echo("Sie werden bis Ende Juli ".date("Y")." per Email und per Post dar&uuml;ber informiert, ob und gegebenenfalls wann Sie zu einem Auswahlgespr&auml;ch eingeladen werden. ");
    echo("Das Auswahlgespr&auml;ch wird in der Regel bis 15.08. an der Universit&auml;t durchgef&uuml;hrt. ");
    echo("Die Auswahlkommission f&uuml;hrt dabei ca. 30 min&uuml;tige Gruppengespr&auml;che mit maximal drei Bewerbern. ");
    echo("Erst nach dem Auswahlgespr&auml;ch wird dar&uuml;ber entschieden, ob Ihnen ein Studienplatz angeboten wird.<br /><br />");
    echo("<b>Bitte beachten Sie:</b> ");
    echo("Zum Auswahlgespr&auml;ch m&uuml;ssen mitgebracht werden:");
    echo("<ul>");
    echo("<li>Eine Kopie und das Original der Hochschulzugangsberechtigung</li>");
    echo("<li>Ggf. Originalnachweise und eine Kopie &uuml;ber ein abgeleistetes freiwilliges soziales Jahr, Zivil- oder Wehrdienst</li>");
    echo("<li>Ggf. Originalnachweise und eine Kopie &uuml;ber eine in der Online-Bewerbung (unter \"Lebenslauf\") genannte abgeschlossene Berufsausbildung</li>");
    echo("</ul>");
    echo("Die darin enthaltenen Informationen werden unmittelbar vor dem Auswahlgespr&auml;ch mit den von Ihnen gemachten Angaben der Online-Bewerbung verglichen, ");
    echo("um evtl. vorhandene &Uuml;bertragungsfehler zu korrigieren.<br /><br />");

    echo("<u>&Auml;nderungen Ihrer Daten</u><br />");
    echo("Im internen Bereich f&uuml;r Bewerber k&ouml;nnen Sie die Angaben Ihrer Bewerbung einsehen und gegebenenfalls noch &auml;ndern. Dies ist jedoch nur bis zum Ablauf der ");
    echo("Bewerbungsfrist m&ouml;glich (".ANMELDEENDE_D_M."".date("Y")."). Danach ist es bis zur Anmeldeperiode im darauffolgenden Studienjahr nicht mehr m&ouml;glich, ");
    echo(" sich in den internen Bereich einzuloggen.<br /><br />");

    echo("<u>Zur&uuml;ckziehen der Bewerbung</u><br />");
    echo("Neben Einsicht und &AUML;nderungen Ihrer Angaben bietet der interne Bereich die M&ouml;glichkeit, Ihre Bewerbung zur&uuml;ckzuziehen. In diesem Fall wird Ihre ");
    echo("Bewerbung nicht als Bewerbungsversuch gewertet. Ihre Daten bleiben dabei trotzdem in unserem System gespeichert. Die Speicherung Ihrer Daten bietet ");
    echo("den Vorteil, dass Sie - sollten Sie es sich anders &uuml;berlegen - Ihre Bewerbung innerhalb der Bewerbungsperiode jederzeit wieder reaktivieren k&ouml;nnen.<br /><br />");

    echo("&Uuml;ber Ihr Interesse an dem Bachelorstudiengang freue ich mich sehr und w&uuml;nsche ");
    echo("Ihnen f&uuml;r den weiteren Verlauf des Bewerbungsverfahrens viel Erfolg!<br /><br />");
}
else
{
    echo("Ihre Bewerbung ist erfolgreich zur&uuml;ckgezogen. Ihre Bewerbung wurde nicht als Bewerbungsversuch gewertet. Ihre Daten bleiben in unserem System gespeichert. ");
    echo("Die Speicherung Ihrer Daten bietet den Vorteil, dass Sie Ihre Bewerbung innerhalb dieser oder folgender Bewerbungsperioden reaktivieren k&ouml;nnen.<br /><br />");
}
echo("Mit freundlichen Gr&uuml;&szlig;en,<br /><br />");

echo("Prof. Dr. Max Mustermann");
?>
</div>
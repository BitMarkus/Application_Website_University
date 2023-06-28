/* ALLE CHECKBOXEN EINES FORMULARES AKTIVIEREN */
function checkAll(id)
{
    var f = document.getElementById(id);
    var inputs = f.getElementsByTagName("input");
    for(var t = 0;t < inputs.length;t++)
    {
        if(inputs[t].type == "checkbox")
        inputs[t].checked = true;
    }
}
function uncheckAll(id)
{
    var f = document.getElementById(id);
    var inputs = f.getElementsByTagName("input");
    for(var t = 0;t < inputs.length;t++)
    {
        if(inputs[t].type == "checkbox")
        inputs[t].checked = false;
    }
}
function invertAll(id)
{
    var f = document.getElementById(id);
    var inputs = f.getElementsByTagName("input");
    for(var t = 0;t < inputs.length;t++)
    {
        if(inputs[t].type == "checkbox")
        inputs[t].checked = !inputs[t].checked;
    }
}
/* ALLE CHECKBOXEN EINES FORMULARES AKTIVIEREN ENDE */

/* AKTUELLES DATUM UND UHRZEIT IN TEXTBOX EINF&Uuml;GEN */
function Datum(id)
{
    var Jetzt = new Date();
    var Tag = Jetzt.getDate();
    var Monat = Jetzt.getMonth() + 1;
    var Jahr = Jetzt.getFullYear();
    var datum=(Tag + "." + Monat + "." + Jahr);
    document.getElementById(id).value = datum;
}
function Uhrzeit(id)
{
    var Jetzt = new Date();
    var Stunden = Jetzt.getHours();
    var Minuten = Jetzt.getMinutes();
    var NachVoll = ((Minuten < 10) ? ":0" : ":");
    var zeit=(Stunden + NachVoll + Minuten);
    document.getElementById(id).value = zeit;
}
/* AKTUELLES DATUM UND UHRZEIT IN TEXTBOX EINF&Uuml;GEN ENDE */
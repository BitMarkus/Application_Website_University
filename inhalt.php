<?php
//includen der richtigen Seite im Inhalt
if(isset($_GET['seite']) AND isset($nav[$_GET['seite']]))
{
    include($nav[$_GET['seite']]);
}
else
{
    include($nav['start']);
}
?>
<?php

error_reporting(E_ALL ^ E_NOTICE);

require_once('autoloader.php');
$rssFeed = new SimplePie();

$url = $_GET['url'];

$rssFeed->set_feed_url($url);
$rssFeed->init();
$rssItems = $rssFeed->get_items(0,15);
$art = 0;

$out = '<table>';

foreach ($rssItems as $rssItem) {
    $out .= '<tr class="artrow">
             <td>' . $rssItem->get_date('Y-m-d H:i:s') . '</td>
             <td id="' . $art . '|' . $url . '" class="artlink">' . $rssItem->get_title() . '</td>
             <td><a target="_blank" href="' . $rssItem->get_link() . '">[>>]</a></td>
             </tr>';      
    $art++;
}

$out .= '</table>
         <br/>
         <a id="lnkRefresh" href="#">Refresh</a>';
echo($out);
?>

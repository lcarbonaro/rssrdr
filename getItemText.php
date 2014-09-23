<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once('autoloader.php');
$rssFeed = new SimplePie();

$idArr = explode('|',$_GET['id']);

$art = $idArr[0];
$url = $idArr[1];

$rssFeed->set_feed_url(trim($url));
$rssFeed->init();
$rssItems = $rssFeed->get_items($art,1);

foreach ($rssItems as $rssItem) {
    //$out = $rssItem->get_description();
    $hdl = '<b>'.$rssItem->get_title().'</b><br/>';
    $out = $rssItem->get_content();
    $gplusone = '<br/><div class="g-plusone" data-href="'.$rssItem->get_link().'" data-annotation="none" data-size="small"></div>
    
    <!-- Place this tag after the last +1 button tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
    po.src = "https://apis.google.com/js/plusone.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
    
    ';
}

// remove any html tags & display 2k chars
echo($hdl.substr(strip_tags($out),0,4000).$gplusone); 

?>
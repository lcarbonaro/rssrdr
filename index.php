<?php
error_reporting(E_ALL);

// build arrays from subscriptions XML file (Google Reader OPML export)
$rssURL = array();
$folderTitles = array();
$feedTitles = array();

// read in XML
$xmlFile = "subscriptions.xml";

if (file_exists($xmlFile)) {
    $xmlObject = simplexml_load_file($xmlFile);
    $folders = $xmlObject->xpath('body/outline');
    //echo('found '.count($folders).' folders<br/>');
    $title = 'Folders:'.count($folders);
    $totalFeeds = 0;
    $i = 0;

    foreach ($folders as $folder) {
        //echo($folder['title'].'<br/>');
        $folderTitles[$i] = $folder['title'];

        $feeds = $folder->xpath('outline');
        //echo('found '.count($feeds).' feeds<br/>');
        $totalFeeds += count($feeds);   
        $j = 0;

        foreach ($feeds as $feed) {
            //echo('   - '.$feed['title'].'<br/>');
            $feedTitles[$i][$j] = utf8_encode($feed['title']);
            $rssURL[$i][$j] = $feed['xmlUrl'];
            $j++;
        }

        $i++;
    }
    
    $title .= ' Feeds:'.$totalFeeds;
  
} else {
    exit('Subscriptions file not found!');
}

$optSources = '';
$allItems = array();

foreach ($rssURL as $folderid => $feeds) {
    
    $optSources .= '<optgroup label="'.$folderTitles[$folderid].'">';

    foreach ($feeds as $feedid => $url) {
        $optSources .= '<option value="'.trim($url).'">'.$feedTitles[$folderid][$feedid].'</option>';
    }
    
    $optSources .= '</optgroup>';
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
        <style type="text/css"> 
            .divRSSItems table {
                border:1px solid black; 
                border-collapse:collapse; 
                padding:2px 2px 2px 2px;
            }
            .divRSSItems table thead tr td {
                border:1px solid black; 
                border-collapse:collapse; 
                padding:2px 2px 2px 2px;
                font-weight:bold;
                text-align:center;
                vertical-align: middle;
            }
            .divRSSItems table tbody tr td {
                border:1px solid black; 
                border-collapse:collapse; 
                padding:2px 2px 2px 2px;
            }
            .folder {
                background-color:lightgrey;
            }
            .isClicked {
                font-weight:bold;
            }
            .isEntered {
                background-color:lightgray;
            }
            .isLeft {
                background-color:white;
            }
        </style>
    </head>
    <body>

        <div id="divRSSReader" class="divRSSReader" >
            
            <table id="tblRSSReader" class="tblRSSReader">
                <tr>
                    <td>
                        <div id="divSources" class="divSources">
                            <select id="selSource" class="selSource">
                                <option value="0">Select source...</option>
                                <?php echo($optSources); ?>                                
                            </select>
                        </div>                            
                    </td> 
                </tr>
                <tr>
                    <td>                        
                        <div id="divItemList" class="divItemList" style="margin-top:20px;"></div>                            
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="divItem" class="divItem" style="margin-top:20px; width:600px;"></div>                            
                    </td>
                </tr>
            </table>
            
            <br/>
            
            

        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                //console.log('doc ready');                  
                
                $('.divRSSReader .tblRSSReader .divSources').on('change','.selSource',function(){
                    var rssFeedURL = $(this).val();
                    if ( rssFeedURL!='0') {
                        $('.divRSSReader .tblRSSReader .divItem').html('');
                        $('.divRSSReader .tblRSSReader .divItemList').html('<img src="images/load.gif" />');
                        $.get('getItemList.php?url='+rssFeedURL,function(data) {
                            $('.divRSSReader .tblRSSReader .divItemList').html(data);
                        });
                    }
                    return false;
                });
                
                $('.divRSSReader .tblRSSReader .divItemList').on('click','td.artlink', function(){
                    //console.log('clicked artlink '+$(this).attr('id'));  

                    // clear any clicked rows
                    $('.divRSSReader .tblRSSReader .divItemList tr').each(function(){
                        $(this).removeClass('isClicked');
                    });
                    // set this one to clicked
                    $(this).parent('tr').addClass('isClicked');
                    
                    var id = $(this).attr('id');                                        
                    $('.divRSSReader .tblRSSReader .divItem').html('<img src="images/load.gif" />');
                    $.get('getItemText.php?id='+id,function(data) {
                        $('.divRSSReader .tblRSSReader .divItem').html(data);
                    });
                    
                    return false;
                });
                
                $('.divRSSReader .tblRSSReader .divItemList').on('mouseenter','tr.artrow',function(){
                    //$(this).css('background-color','lightgray');
                    $(this).addClass('isEntered');
                    $(this).removeClass('isLeft');
                });
                
                $('.divRSSReader .tblRSSReader .divItemList').on('mouseleave','tr.artrow',function(){
                    //$(this).css('background-color','white');
                    $(this).addClass('isLeft');
                    $(this).removeClass('isEntered');
                });
                
                $('.divRSSReader .tblRSSReader .divItemList').on('click','a#lnkRefresh',function(){
                    //console.log('refresh');                    
                    var rssFeedURL = $('.divRSSReader .tblRSSReader .divSources .selSource').val();
                    if ( rssFeedURL!='0') {
                        $('.divRSSReader .tblRSSReader .divItem').html('');
                        $('.divRSSReader .tblRSSReader .divItemList').html('<img src="images/load.gif" />');
                        $.get('getItemList.php?url='+rssFeedURL,function(data) {
                            $('.divRSSReader .tblRSSReader .divItemList').html(data);
                        });
                    }
                    return false;
                });
                
            });
        </script>

    </body>
</html>
<?php

require_once(e./LineTalk/LineTalk.php');

// AccessToken
$access_token = eaccesstoken_stringf;

// Account Mid 
$to_mid = euser_mid_stringf;

// Account Lineat Id 
$lineat_id = e@aaaabbbbf;


$talk = new LineTalk( $access_token, $to_mid );

$ret = $talk->getProfile();
if ($ret)
    echo 'getProfile() Successed!<br>';
else
    echo 'getProfile() Failed!<br>';

$ret = $talk->addFriend();
if ($ret)
    echo 'addFriend() Successed!<br>';
else
    echo 'addFriend() Failed!<br>';



$aray_linkmsg = array( 'templateId'  => etemplateIDf,
                       epreviewf     => eurlLinkf,
                       eidf    => eidf,
                       'name'    => eproduct_namef,
                       'price'       => e100000f,
                       'subtext_param' => ecustomparamf,
                       eweblink'    => elink.htmlf
                     );

$ret = $talk->sendLinkMsg($aray_linkmsg);
if ($ret)
    echo 'sendLinkMsg() Successed!<br>';
else
    echo 'sendLinkMsg() Failed!<br>';

//$talk->openTalkroom($lineat_id);

?>

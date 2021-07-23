<?php

require 'Curl.php';

$type = $_GET['type'];
$idPost = $_GET['idPost'];
$accessToken = $_GET['accessToken'];

if ($type == 'getCmt') {
    getComment($idPost, $accessToken);
} else {
    $typeReact = $_GET['typeReact'];
    getReaction($idPost, $accessToken, $typeReact);
}

function getComment($idPost, $accessToken, $LIMIT = 8) {
    $curl = new Curl();
    $curl->_setURL("https://graph.facebook.com/$idPost/comments?access_token=${accessToken}&pretty=1&filter=stream&limit=$LIMIT&order=reverse_chronological");
    $curl->_run();
    echo $curl->_getData();
}

function getReaction($idPost, $accessToken, $typeReact) {
    $curl = new Curl();
    $curl->_setURL("https://graph.facebook.com/$idPost/reactions?limit=100&access_token=$accessToken");
    $curl->_run();
    echo $curl->_getData();
}

?>
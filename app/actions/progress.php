<?php
/**
 * Created by PhpStorm.
 * User: gerasim
 * Date: 1/24/15
 * Time: 11:19 AM
 */

$userIP = $_SERVER['REMOTE_ADDR'];

//todo
//here if user make ajax request return some json data

//get progress for user
$userProgress = $db->progress->findOne(array('_id' => $userIP));
if(!$userProgress){
    //user without progress
    //can make some tutor or Welcome user
    $db->progress->insert(array('_id' => $userIP, 'progress' => new \StdClass));
    $userProgress = $db->progress->findOne(array('_id' => $userIP));
}
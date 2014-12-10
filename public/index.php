<?php
/**
 * Created by PhpStorm.
 * User: Gerasim
 * Date: 09.12.2014
 * Time: 23:36
 */

namespace MP;

define('APP_PATH', realpath(__DIR__ . '/../app') . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', __DIR__ . DIRECTORY_SEPARATOR);

ob_start();
session_start();
error_reporting(E_ALL);

$config = require_once(APP_PATH . 'config.php');
$dbConf = $config['db'];

//http://stackoverflow.com/questions/6482224/mongodb-php-driver-how-to-create-database-and-add-user-to-it
$connection = new \MongoClient(
    //"mongodb://{$dbConf['host']}:{$dbConf['port']}/{$dbConf['name']}",
    //connect to admin by default - add admin user to db admin.system.users
    "mongodb://{$dbConf['host']}:{$dbConf['port']}",
    array(
        'username' => $dbConf['user'],
        'password' => $dbConf['pass'],
        //how add user to connection with password for db
        //what do db users, and with roles admin
    )
);
$db = $connection->{$dbConf['name']};
//I added same user for System.admin db and now it works

if(!isset($_SESSION['connection'])){

    //create db for user
    $userDbConf = array(
            'host' => 'localhost',
            'port' => 27017,
            'user' => 'user' . uniqid(),
            'pass' => 'pass' . uniqid(),
            'name' => 'mp'   . uniqid(),
    );
    $userDb = $connection->{$userDbConf['name']};
    $userDbUsers = $userDb->selectCollection("system.users");
    //create user for collection
    $userDbUsers->insert(array(
            'user' => $userDbConf['user'],
            'pwd' => md5($userDbConf['user'] . ":mongo:" . $userDbConf['pass']),
            'readOnly' => false
        )
    );

    $_SESSION['connection'] = $userDbConf;

    //generate default structure - todo in future different structure name(default, les100500 etc)
    $structure = $db->structure->find();
    foreach($structure as $collection){
        $uCollection = $userDb->selectCollection($collection['name']);
        $uCollection->batchInsert($collection['data']);
    }

    //drop old dbs if 20 min not used
    $query = array(
        'lastActivity' => array(
            '$lt' => (time() - 20*60) //20 minutes ago
        )
    );
    $dbsToRemove = $db->userDbs->find($query);
    foreach($dbsToRemove as $rdb) {
        $connection->dropDB($rdb['name']);
    }
    $db->userDbs->remove($query);
}

//connect to db from session connection
$userDbConf = $_SESSION['connection'];
$userConnection = new \MongoClient(
    "mongodb://{$userDbConf['host']}:{$userDbConf['port']}/{$userDbConf['name']}",
    array(
        'username' => $userDbConf['user'],
        'password' => $userDbConf['pass'],
    )
);

//add activity time to db
$db->userDbs->update(
    array('name' => $userDbConf['name']),
    array('$set' => array('lastActivity' => time())),
    array('upsert' => true)
);

//$dbs = $userConnection->listDBs(); //error - yes it works
//$dbs = $connection->listDBs(); //works
//print_r($dbs);

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

//todo examples
$examples = $db->examples->find();

//display all data
$render = function($path, $data){
    require(APP_PATH . $path);
};
$render('layout.php', compact('examples', 'userProgress'));

ob_end_flush();
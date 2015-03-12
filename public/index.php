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

//display all data
$render = function($path, $data = null){
    require(APP_PATH . $path);
};

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
$userDb = $userConnection->selectDB($userDbConf['name']);

//add activity time to db
$db->userDbs->update(
    array('name' => $userDbConf['name']),
    array('$set' => array('lastActivity' => time())),
    array('upsert' => true)
);

//$dbs = $userConnection->listDBs(); //error - yes it works
//$dbs = $connection->listDBs(); //works
//print_r($dbs);
//----------------------------------------------------------------------------------------------------------------------

//data
$_PUT = array();
if($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $putdata = file_get_contents('php://input');
    parse_str(urldecode($putdata), $_PUT);
}

//for angular http - http://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postData = file_get_contents('php://input');
    $_POST = json_decode($postData);
}

$_DELETE = false;
if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $_DELETE = true;
}

//Actions
if (isset($_GET['url']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'))
{
    $response = '';
    //XHR
    switch($_GET['url']){
        case 'example':
            if(isset($_GET['id']) && $_GET['id']){
                $response = $db->examples->findOne(array('_id' => new \MongoId($_GET['id'])));
            } else {
                //_id return by default always
                $response = $db->examples->find(array(), array('name' => 1));
                $response = iterator_to_array($response);
            }
            break;
        case 'description':
            break;
        case 'change-name':
            $data = $_POST;
            //todo filter data
            $name = $data->name;
            $db->progress->update(array('_id' => $_SERVER['REMOTE_ADDR']), array('$set' => array('name' => $name)));
            $response = $name;
            break;
        case 'restore-db':
            $userDb->drop();
            $structure = $db->structure->find();
            foreach($structure as $collection){
                $uCollection = $userDb->selectCollection($collection['name']);
                $uCollection->batchInsert($collection['data']);
            }
            $response = array('success' => true);
            break;
        case 'get-progress':
            require_once (APP_PATH . '/actions/progress.php');
            /**
             * @var $userProgress object from require_once
             */
            $response = $userProgress;
            break;
        case 'run-code':
            $data = $_POST;
            if(!$data){
                $response = array('error' => 'empty POST');
                break;
            }
            $id     = $data->id;
            $lang   = $data->lang;
            $code   = $data->code;
            //todo explode by new line
            if($lang === 'js'){
                $code = rtrim($code, ';');
                $response = $userDb->execute(new \MongoCode('return ' . $code . (($code[strlen($code)-1] === ')') ? '.toArray()' : '')));
                if($response['ok'] == 1){
                    $response = $response['retval'];
                } else {
                    //handle error
                }
            } else {
                //php
                $code = str_replace('$db', '$userDb', rtrim($code, ';'));
                $response = eval('return ' . $code . ';');
                $response = iterator_to_array($response);
            }

            //check response
            $response = array('data' => $response, 'correct' => false);
            $example = $db->examples->findOne(array('_id' => new \MongoId($id)));

            if($example && isset($example['answer'])){

                $answer = $userDb->execute(new \MongoCode('return ' . $example['answer']
                    . (($example['answer'][strlen($example['answer'])-1] === ')') ? '.toArray()' : '')));

                //todo check on empty result - if user delete all data
                if($answer && isset($answer['retval']) && $answer['retval'] == $response['data']){
                    $response['correct'] = true;
                    $db->progress->update(array('_id' => $_SERVER['REMOTE_ADDR']), array('$addToSet' => array('progress' => $id)));
                }
            }

            break;
        default:
            break;
    }
    header('Content-Type: application/json');
    echo json_encode($response);

    //todo check iframe SERVER_NAME == HTTP_REFERER
} else {
    //require(APP_PATH . '/actions/examples.php');
    //print_r($examples);die;
    $render('layout.php'/*, compact('user', 'userProgress')*/);
}

ob_end_flush();
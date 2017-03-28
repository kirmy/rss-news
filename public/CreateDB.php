<?php

require_once '../vendor/autoload.php';

$app = new Silex\Application;
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    // 'dbname' => 'rss_news',
    'user' => 'root',
    'password' => '123',
    'charset'   => 'utf8mb4',
    )
));

$sql = "SHOW DATABASES LIKE 'rss_news';";
$statement = $app['db']->executeQuery($sql);
if (!$user = $statement->fetch()) {
	echo "CREATE DATABASE rss_news";
    $sql = "CREATE DATABASE rss_news CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$statement = $app['db']->executeQuery($sql);
}
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db1.options' => array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'rss_news',
    'user' => 'root',
    'password' => '123',
    'charset'   => 'utf8mb4',
    )
));

// use Pimple\Container;

// $container = new Container();

// $container['cookie_name'] = 'SESSION_ID';
// $container['session_storage_class'] = 'SessionStorage';

// $container['session_storage'] = function ($c) {
	// 	return new $c['session_storage_class']($c['cookie_name']);
	//
// }
// ;
// //var_dump($container['session_storage']($container));
// ($container['session_storage'])($container);
// $container = new Container();
// var_dump($container);
// // define some services
// $container['session_storage'] = function ($c) {
	// 	return new SessionStorage('SESSION_ID');
	//
// }
// ;
// var_dump($container['session_storage']);
// $container['session'] = function ($c) {
	// 	return new Session($c['session_storage']);
	//
// }
// ;
// $session = $container['session'];
// var_dump($session);

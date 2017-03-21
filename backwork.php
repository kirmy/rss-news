<?php 
# https://rss.unian.net/site/news_ukr.rss 
require_once '/var/www/rss-demo/vendor/autoload.php'; 
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Europe/Kiev');
$db = new PDO("mysql:host=localhost;dbname=sg_news;charset=utf8", "root", "123"); 
$sql = "INSERT IGNORE INTO news (title, link, description, source, pub_date) VALUES (?, ?, ?, ?, ?)"; 
//$db->query("SET NAMES utf8;");
$stmt = $db->prepare($sql);

// create a log channel
$log = new Logger('news');
$log->pushHandler(new StreamHandler('/var/www/rss-demo/logs/news.log', Logger::DEBUG));

//load feed from sources
$sqlFeeds = "SELECT link FROM sources"; 
$feedLinks = $db->query($sqlFeeds);
$feed_urls = [];
foreach ($feedLinks as $link){
	$feed_urls[] = $link['link'];
}

// $feed_urls = [
// 	'https://rss.unian.net/site/news_ukr.rss',
// 	'http://www.pravda.com.ua/rss/',
// 	'http://www.epravda.com.ua/rss/',
// 	'http://tyzhden.ua/RSS/News/',
// 	'http://www.radiosvoboda.org/api/z_rppyeruppy'
// ];
var_dump($feed_urls);

$feed = new SimplePie(); 
$feed->set_feed_url($feed_urls); 
$feed->enable_cache(false);
$feed->enable_order_by_date(); 
$feed->init(); 
$items = $feed->get_items();
//var_dump($feed->raw_data);//->get_item_quantity()); 
// Подготовка запроса для проверки существования записи
$search = $db->prepare("SELECT * FROM news WHERE  link = ?");

foreach ($items as $item) { 
    //var_dump($item->get_title());
	$current_feed = $item->get_feed();
    
	$search->execute([$item->get_link()]);
    if (!$search->fetch(PDO::FETCH_ASSOC)) {   
		$stmt->execute([ 
			$item->get_title(), 
			$item->get_link(), 
			$item->get_description(), 
			$current_feed->get_link(), 
			$item->get_date("Y-m-d H:i:s")
		]);
		// add records to the log
		$log->info($item->get_link());
	//$log->error('Bar'); ALTER TABLE news DROP COLUMN item_id;
	}
}
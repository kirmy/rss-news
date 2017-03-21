<?php

namespace App\Controller;

use \PDO;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Cabinet
{
    public function getIndex($request, $response)
    {
        $logged = $request->getSession()->get('logged');
        if ($logged) {

            $db = new PDO("mysql:host=localhost;dbname=sg_news;charset=utf8", "root", "123");
            $sql = "SELECT * FROM sources ORDER BY id DESC LIMIT 50";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response->setContent(include '../templates/cabinet.tpl.php');
        } else {
            $response->setStatusCode('403');
            $response->setContent('Forbidden.');
        }

        return $response;
    }

    public function addFeed($request)
    {
        date_default_timezone_set('Europe/Kiev');
        $db = new PDO("mysql:host=localhost;dbname=sg_news;charset=utf8", "root", "123"); 
        $sql = "INSERT IGNORE INTO sources (name, link, feed) VALUES (?, ?, ?)"; 
        $stmt = $db->prepare($sql);

        $name = $request->request->get('name');
        $link = $request->request->get('link');
        $feed = $request->request->get('feed');
        $stmt->execute([$name, $link, $feed]);
        return new RedirectResponse('/cabinet');
    }

    public function deleteSource($request)
    {
        $db = new PDO("mysql:host=localhost;dbname=sg_news;charset=utf8", "root", "123"); 
        $sql = "DELETE FROM sources WHERE id = ?"; 
        $stmt = $db->prepare($sql);
        $id = $request->request->get('id');
        $stmt->execute([$id]);
        return new RedirectResponse('/cabinet');
    }

    public function deleteFeeds($request, $response)
    {
        echo "deleteFeed<br>";
        //var_dump($response);
        $db = new PDO("mysql:host=localhost;dbname=sg_news;charset=utf8", "root", "123"); 
        $sql = "DELETE FROM news"; 
        $count = $db->exec($sql);
        $response->setContent("Deleted $count records");
        return $response; //new RedirectResponse('/cabinet');
    }
    // public function postAddItem($request, $response)
    // {
    //     $logged = $request->getSession()->get('logged');
    //     if ($logged) {
    //         $response->setContent('CABINET');
    //     } else {
    //         $response->setStatusCode('403');
    //         $response->setContent('Forbidden.');
    //     }

    //     return $response;
    // }
}

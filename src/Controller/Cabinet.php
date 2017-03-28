<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use App\Model\SourceMapper;
use App\Model\SourceEntity;
use Symfony\Component\Validator\Constraints as Assert;

class Cabinet
{
    public static function _before(Request $request, Application $app)
    {
        $logged = $request->getSession()->get('logged');
        if (! $logged) $app->abort(403, 'Forbidden.');
    }

    public function getIndex(Request $request, Application $app)
    {
        $mapper = new SourceMapper($app['db']);
        $sources = $mapper->getSources();
        $errors = $request->getSession()->getFlashBag()->get('errors', array());
        if (count($errors)>0) {
            foreach ($errors as $error) {
                var_dump($error); 
                echo '<div class="flash-notice">'.$error.'</div>';
            }
die();
        }
       
      
        $app['view.name'] = 'cabinet';
        return $app['view']->data(['sources' => $sources])->render();
    }

    public function postAddSource(Request $request, Application $app)
    {
        $data = $request->request->all();
        //var_dump($data); die();
        
        $constraints = new Assert\Collection(array(
            'name' => new Assert\Length(array('min' => 3)),
            'source_link' => new Assert\Url(),
            'rss_feed_link' => new Assert\Url()
        ));
        //var_dump($source);     die();
        $errors = $app['validator']->validate($data, $constraints);
        if (count($errors) > 0) {
            $request->getSession()->getFlashBag()->add('errors', $errors);
            //$all_errors = $errors;
        // } 
        // $errors = $app['validator']->validate($source->getRssFeedLink(), new Assert\Url());
        // if (count($errors) > 0) {
            
        //     $all_errors->addAll($errors);
        // } 
        //var_dump($all_errors); die();
        // if (count($errors) > 0) {
//             //$request->getSession()->getFlashBag()->add('errors', $all_errors);
//             //$app['session']->getFlashBag()->add('example', 'Some example flash message');
//             //return $app->redirect('redirect-to-some-route');
//             //return (string) $all_errors;
            $es = $request->getSession()->getFlashBag()->get('errors',array());
            //var_dump(count($errors));
            foreach ($es as $error) {
                var_dump($error);    
                echo '<div class="flash-error"><br>\n\n</div>';
            }
    die();
            return $app->redirect('/cabinet');
        }
        $source = new SourceEntity($data);
        $mapper = new SourceMapper($app['db']);
        $mapper->save($source);

        return $app->redirect('/cabinet');
    }

    public function postDisableSource(Request $request, Application $app, $id)
    {
        $mapper = new SourceMapper($app['db']);
        $data = $mapper->getSourceById($id);
        $data['is_active'] = ! $data['is_active'];

        $source = new SourceEntity($data);
        $mapper->save($source);

        return $app->redirect('/cabinet');
    }
}

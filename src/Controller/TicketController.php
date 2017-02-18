<?php

namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Entity\User;
use Form\UserType;
 
/**
 * Sample controller
 *
 */
class TicketController implements ControllerProviderInterface {
    
    /**
     * Route settings
     *
     */
    public function connect(Application $app) {
        $indexController = $app['controllers_factory'];
        $indexController->get("/", array($this, 'index'))->bind('ticket_index');
        // $indexController->get("/show/{id}", array($this, 'show'))->bind('acme_show');
        $indexController->match("/", array($this, 'create'))->bind('user_create');
        $indexController->match("/dogrula", array($this, 'kullaniciDogrula'))->bind('user_list');
        $indexController->match("/giris-yap", array($this, 'girisyapAction'))->bind('user_giris');
        $indexController->match("/cikis-yap", array($this, 'sil'))->bind('sil');
        $indexController->match("/oturum", array($this, 'oturum'))->bind('oturum');
        // $indexController->match("/update/{id}", array($this, 'update'))->bind('acme_update');
        // $indexController->get("/delete/{id}", array($this, 'delete'))->bind('acme_delete');



        $app->before(function (Request $request, Application $app) {
            $uye = $app['session']->get("giris");
            if($uye){
                $this->uye = true;
                $app['twig']->addGlobal('oturum', "var");
            }else{
                $this->uye = false;
                $app['twig']->addGlobal('oturum', "yok");

                //return $app->redirect($app["url_generator"]->generate("user_giris"));
            }

            return $this->uye;
        });

        return $indexController;
    }


    // üye oturumunu burada tutucaz
    private $uyelik = [];

    // SessionController
    // Üye Giriş Yapmışmı Kontrol eder
    public function sc(Application $app){
        $uye = $app['session']->get('giris');
        $this->uyelik = $uye;
        $app['twig']->addGlobal('oturum', "var");
        return $app->json($this->uyelik);
    }

    // Çıkış Yaptırır
    public function sil(Application $app){
        $app['session']->remove('giris');

        return $app->redirect($app["url_generator"]->generate("ticket_index"));
    }

    // password encryption
    public function pw(Application $app, $password){
        // usage return $this->pw($app, 'parola');
        $rawPassword = $password;
        $salt = '%qUgq3NAYfC1MKwrW?yevbE';
        $encoder = $app['security.encoder.digest'];
        return $encoder->encodePassword($rawPassword, $salt);
        //return $app->json($this->uyelik);
    }

     // giris yap action
    public function girisyapAction(Request $request, Application $app){
        $em = $app['db.orm.em'];

        $entity = new User();

        $form = $app['form.factory']->create(new UserType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $message = $request->request->all();
            $kadi = $message["user"]["username"];
            $sifre = $message["user"]["password"];

            $em = $app['db.orm.em'];
            $entities = $em->getRepository('Entity\User')->findOneBy(array("username" => $kadi, "password" => $sifre));

            if(!$entities){

                return new Response("Sistemde bu bilgilerle kayıtlı bir kullanıcı bulunamadı");

            }

            $app['session']->set('giris', array(
                    "kullanıcı" => $kadi
                ));

            return $app['twig']->render('Ticket/dump.twig', array(
                'entities' => $entities
            ));

        }

        return $app['twig']->render('Ticket/kullanici-giris.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));



    }

    /**
     *
     * kullanıcı doğrula
     *
     */
    public function kullaniciDogrula(Application $app){

        $em = $app['db.orm.em'];
        $entities = $em->getRepository('Entity\User')->findAll();

        return $app['twig']->render('Ticket/dump.twig', array(
            'entities' => $entities
        ));
        //return $app->json($entities);
    }




    public function oturum(Application $app){

        $oturum = $app['session']->get('giris');

        return $app->json($oturum);

    }

   




    /**
     * List all entities
     *
     */
    public function index(Application $app) {
        
        $em = $app['db.orm.em'];
        $entities = $em->getRepository('Entity\User')->findAll();

        return $app['twig']->render('Ticket/anasayfa.twig', array(
            'entities' => $entities
        ));
    }
    
    // /**
    //  * Show entity
    //  *
    //  */
    // public function show(Application $app, $id) {
        
    //     $em = $app['db.orm.em'];
    //     $entity = $em->getRepository('Entity\Acme')->find($id);

    //     if (!$entity) {
    //         $app->abort(404, 'No entity found for id '.$id);
    //     }

    //     return $app['twig']->render('Acme/show.html.twig', array(
    //         'entity' => $entity
    //     ));
    // }
    
    // /**
    //  * Create entity
    //  *
    //  */
    // public function create(Application $app, Request $request) {

    //     $em = $app['db.orm.em'];
    //     $entity = new User();

    //     $form = $app['form.factory']->create(new UserType(), $entity);
    //     $form->handleRequest($request);

    //     if ($form->isValid()) {
            
    //         $em->persist($entity);
    //         $em->flush();

    //         return $app->redirect($app['url_generator']->generate('acme_show', array('id' => $entity->getId())));
    //     }

    //     return $app['twig']->render('Ticket/anasayfa.twig', array(
    //         'entity' => $entity,
    //         'form' => $form->createView()
    //     ));
    // }
    
    // /**
    //  * Update entity
    //  *
    //  */
    // public function update(Application $app, Request $request, $id) {
        
    //     $em = $app['db.orm.em'];
    //     $entity = $em->getRepository('Entity\Acme')->find($id);

    //     if (!$entity) {
    //         $app->abort(404, 'No entity found for id '.$id);
    //     }

    //     $form = $app['form.factory']->create(new AcmeType(), $entity);
    //     $form->handleRequest($request);

    //     if ($form->isValid()) {

    //         $em->flush();
    //         $app['session']->getFlashBag()->add('success', 'Entity update successfull!');
            
    //         return $app->redirect($app['url_generator']->generate('acme_show', array('id' => $entity->getId())));
    //     }

    //     return $app['twig']->render('Acme/update.html.twig', array(
    //         'entity' => $entity,
    //         'form' => $form->createView()
    //     ));
    // }
    
    // /**
    //  * Delete entity
    //  *
    //  */
    // public function delete(Application $app, $id) {
        
    //     $em = $app['db.orm.em'];
    //     $entity = $em->getRepository('Entity\Acme')->find($id);

    //     if (!$entity) {
    //         $app->abort(404, 'No entity found for id '.$id);
    //     }

    //     $em->remove($entity);
    //     $em->flush();

    //     return $app->redirect($app['url_generator']->generate('acme_index'));
    // }
}
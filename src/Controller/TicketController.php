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
use Entity\Ticket;
use Form\UserType;
use Form\TicketType;
 
/**
 * Sample controller
 *
 */
class TicketController implements ControllerProviderInterface {
    
    // üye oturumunu burada tutucaz
    private $uyelik = [];
    private $uye;

    /**
     * Route settings
     *
     */
    public function connect(Application $app) {
        $indexController = $app['controllers_factory'];
        $indexController->get("/", array($this, 'index'))->bind('anasayfa');
        $indexController->match("/kullanici-olustur", array($this, 'create'))->bind('kullanici.olustur');
        $indexController->match("/giris-yap", array($this, 'girisyapAction'))->bind('kullanici.giris');
        $indexController->match("/cikis-yap", array($this, 'sil'))->bind('sil');
        $indexController->match("/oturum", array($this, 'oturum'))->bind('oturum');

        // Ticket işlemleri
        $indexController->match("/ticket", array($this, 'ticketOlustur'))->bind('ticket.olustur');



        $app->before(function (Request $request, Application $app) {
            $uye = $app['session']->get("giris");
            if($uye){
                $this->uye = true;
                $app['twig']->addGlobal('oturum', "var");
            }else{
                $this->uye = false;
                $app['twig']->addGlobal('oturum', "yok");
            }

            return $this->uye;
        });

        return $indexController;
    }

    // Test amaçlı
    public function sc(Application $app){
        $uye = $app['session']->get('giris');
        $this->uyelik = $uye;
        $app['twig']->addGlobal('oturum', "var");
        return $app->json($this->uyelik);
    }

    // Çıkış Yaptırır
    public function sil(Application $app){
        $app['session']->remove('giris');

        return $app->redirect($app["url_generator"]->generate("anasayfa"));
    }

     // giris yap action
    public function girisyapAction(Request $request, Application $app){
        $em = $app['db.orm.em'];

        // entityi çağır
        $entity = new User();

        // form oluşturucu
        $form = $app['form.factory']->create(new UserType(), $entity);
        $form->handleRequest($request);

        //
        // form geldiyse
        //

        if ($form->isValid()) {
            
            //
            // formdan gelen veriyi al
            //
            
            $message = $request->request->all();
            $kadi = $message["user"]["username"];
            $sifre = $message["user"]["password"];

            // doctrine'i çağır
            $em = $app['db.orm.em'];
            
            // bu verilerle uyuşan bir kullanıcı var mı?
            $entities = $em->getRepository('Entity\User')->findOneBy(array("username" => $kadi, "password" => $sifre));

            // yoksa
            if(!$entities){

                // error gönder
                return new Response("Sistemde bu bilgilerle kayıtlı bir kullanıcı bulunamadı");

            }

            // varsa
            $app['session']->set('giris', array(
                    "kullanıcı" => $kadi,
                    "rol"       => $entities->getRoles(),
                ));

            return $app->redirect($app["url_generator"]->generate("anasayfa"));
        }


        //
        // form oluştur
        //
        return $app['twig']->render('Ticket/kullanici-giris.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));

    }



    //
    // test amacıyla
    //
    public function oturum(Application $app){

        $oturum = $app['session']->get('giris');

        return $app->json($oturum);

    }

   




    /**
     *
     * Anasayfa
     *
     */
    public function index(Application $app) {
        
        $em = $app['db.orm.em'];

        $giris = $app['session']->get("giris");

        if($giris){

            // kullanıcıya son oluşturduğu ticketları gösterelim
            return $app['twig']->render('Ticket/anasayfa.twig');

        }else{

            return $app['twig']->render('Ticket/anasayfa.twig');

        }
    }


    //
    // Ticket Oluşturma
    //

    public function ticketOlustur(Application $app, Request $request){

        //
        // giriş kontrolü
        //
        if(!$this->uye){
            return $app->redirect($app["url_generator"]->generate("kullanici.giris"));
        }

        $em = $app['db.orm.em'];

        // entityi çağır
        $entity = new Ticket();

        // form oluşturucu
        $form = $app['form.factory']->create(new TicketType(), $entity);
        $form->handleRequest($request);



        if ($form->isValid()) {
            
            //
            // formdan gelen veriyi al
            //
            
            $message = $request->request->all();
            $kadi = $message["user"]["username"];
            $sifre = $message["user"]["password"];

            // doctrine'i çağır
            $em = $app['db.orm.em'];
            
            // bu verilerle uyuşan bir kullanıcı var mı?
            $entities = $em->getRepository('Entity\User')->findOneBy(array("username" => $kadi, "password" => $sifre));

            // yoksa
            if(!$entities){

                // error gönder
                return new Response("Sistemde bu bilgilerle kayıtlı bir kullanıcı bulunamadı");

            }

            return $app->redirect($app["url_generator"]->generate("anasayfa"));
         }


        //
        // form oluştur
        //
        return $app['twig']->render('Ticket/ticket-olustur.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
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
    
    /**
     * Kullanıcı Oluşturur
     *
     */
    public function create(Application $app, Request $request) {

        // doctrine
        $em = $app['db.orm.em'];
        $entity = new User();

        $form = $app['form.factory']->create(new UserType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $message = $request->request->all();
            $kadi = $message["user"]["username"];


            // bu verilerle uyuşan bir kullanıcı var mı?
            $entities = $em->getRepository('Entity\User')->findOneBy(array("username" => $kadi));

            if($entities){
            
                return new Response("bu kullanıcı ismi daha önce alınmış");
            }

            $em->persist($entity);
            $em->flush();

            return $app->redirect($app['url_generator']->generate('anasayfa'));
        }

        return $app['twig']->render('Ticket/kullanici-olustur.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }
    
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
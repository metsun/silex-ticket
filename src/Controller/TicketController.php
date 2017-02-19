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
use Entity\TicketCevap;
use Entity\Kategori;
use Form\UserType;
use Form\TicketType;
use Form\TicketCevapType;
use Form\KategoriType;
 
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
        $indexController->match("/ticket/goster/{id}", array($this, 'ticketGoster'))->bind('ticket.goster');
        $indexController->match("/ticket/cevapla", array($this, 'cevapEkle'))->bind('ticket.cevap.ekle');
        $indexController->match("/ticket/kapat/{id}", array($this, 'ticketKapat'))->bind('ticket.kapat');

        // Kategori Oluştur
        $indexController->match("/kategori", array($this, 'kategoriOlustur'))->bind('kategori');



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

    /**
     *
     * Anasayfa
     *
     */
    public function index(Application $app) {
        
        $em = $app['db.orm.em'];

        $giris = $app['session']->get("giris");

        if($giris){

            // $tickets = $em->findAll('Entity\Ticket', array('user' => $id));

            // kullanıcı admin mi
            if( $giris["rol"] == 1 ){
                $tickets = $em->getRepository('Entity\Ticket')->findBy(array(), array('id' => 'DESC') );
            }else{
                // user'ı al
                $user = $em->find('Entity\User', $app["session"]->get('giris')["id"]);
                $tickets = $em->getRepository('Entity\Ticket')->findBy( array('user' => $user) );
            }

            // kullanıcıya son oluşturduğu ticketları gösterelim
            return $app['twig']->render('Ticket/anasayfa.twig', 
                array('tickets' => $tickets)
            );

        }else{

            return $app['twig']->render('Ticket/anasayfa.twig');

        }
    }



    // Test amaçlı
    public function sc(Application $app){
        $uye = $app['session']->get('giris');
        $this->uyelik = $uye;
        $app['twig']->addGlobal('oturum', "var");
        return $app->json($this->uyelik);
    }

     // giris yap action
    public function girisyapAction(Request $request, Application $app){

        // kullanıcı giriş yapmış mı
        if($this->kullaniciKontrol($app)){
            return $app->redirect($app["url_generator"]->generate("anasayfa"));
        }


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
                    "id"        => $entities->getId(),
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

    // Çıkış Yaptırır
    public function sil(Application $app){
        $app['session']->remove('giris');

        return $app->redirect($app["url_generator"]->generate("anasayfa"));
    }

    //
    // test amacıyla
    //
    public function oturum(Application $app){

        $oturum = $app['session']->get('giris');

        return $app->json($oturum);

    }

   






    //
    // Ticket Oluşturma
    //

    public function ticketOlustur(Application $app, Request $request){

        //
        // giriş kontrolü
        //
        if(!$this->uye){

            $app['session']->getFlashBag()->add('danger', 'ilk önce giriş yapmalısınız');
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

            // doctrine'i çağır
            $data = $request->request->all();
            $em = $app['db.orm.em'];

            // günü al
            $gun = strftime("%d %B %Y, %A, %H:%M", time()); 


            // dosyayı al
            $files = $request->files->get($form->getName());

            if( $files["filepath"] ){
                /* taşınacağı yer */
                $path = __DIR__.'/../../web/upload/';
                // dosya adı
                $filename = sha1(uniqid(mt_rand(), true)) . ".". $files['filepath']->guessExtension();
                // dosyayi taşı
                $files['filepath']->move($path,$filename);

                // dosyayı kaydet
                $entity->setFilepath($filename);
            }
            
            // ticket açık
            $entity->setDurum(1);
            // formdan gelmeyen verileri set edelim
            $entity->setDatetime($gun);

            // user'ı al
            $user = $em->find('Entity\User', $app["session"]->get('giris')["id"]);

            $entity->setUser($user);

            // database e bas
            $em->persist($entity);
            $em->flush();

            // ticket'ı gösteren sayfaya yönlendir
            return $app->redirect($app['url_generator']->generate('ticket.goster', array('id' => $entity->getId())));
         }


        //
        // form oluştur
        //
        return $app['twig']->render('Ticket/ticket-olustur.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));

    }


    //
    // Ticket Gösterme
    // 

    public function ticketGoster(Application $app, Request $request, $id){

        // kullanıcı giriş yapmış mı
        if(!$this->kullaniciKontrol($app)){
            $app['session']->getFlashBag()->add('danger', 'ilk önce giriş yapmalısınız');
            return $app->redirect($app["url_generator"]->generate("kullanici.giris"));
        }

        $em = $app['db.orm.em'];


        // user'ı al
        $user = $em->find('Entity\User', $app["session"]->get('giris')["id"]);

        // eğer adminse bütün ticketları göster
        if($app["session"]->get('giris')["rol"] == 1){
            $tickets = $em->getRepository('Entity\Ticket')->findOneBy( array('id' => $id) );
        }else{
            $tickets = $em->getRepository('Entity\Ticket')->findOneBy( array('id' => $id, 'user' => $user) );
        }

        // bu kişiye ait ticketları bul
        if($tickets){
            //
            // cevap formu
            //
            // entityi çağır
            $cevap = new TicketCevap();

            // form oluşturucu
            $form = $app['form.factory']->create(new TicketCevapType(), $cevap);
            // ticket'a ait cevapları al
            $cevaplar = $em->getRepository('Entity\TicketCevap')->findBy( array('ticketcevap' => $tickets) );

            if($cevaplar){

                return $app['twig']->render('Ticket/ticket-goster.twig', array(
                   'ticket' => $tickets,
                   'cevaplar' => $cevaplar
                ));
            }

            return $app['twig']->render('Ticket/ticket-goster.twig', array(
                'ticket' => $tickets,
            ));

        }

        $app['session']->getFlashBag()->add('danger', 'bu ticketı görme yetkiniz yok');

        return $app->redirect($app['url_generator']->generate('anasayfa'));
    }


    //
    // Ticket'e cevap ekle
    //

    public function cevapEkle(Application $app, Request $request){

        // kullanıcı giriş yapmış mı
        if(!$this->kullaniciKontrol($app)){
            $app['session']->getFlashBag()->add('danger', 'önce giriş yapmalısınız');
            return $app->redirect($app["url_generator"]->generate("anasayfa"));
        }


        $session = $app['session']->get('giris');
        if($request->getMethod() == 'POST'){

            $cevap = $request->request->all();

            $em = $app['db.orm.em'];

            $entity = new TicketCevap();
            
            //
            // güvenlik
            //

            // kişi bu ticket'a cevap verebilir mi
            $userKontrol = $em->getRepository('Entity\User')->findOneBy( array('id' => $session["id"]) );
            $ticketKontrol = $em->getRepository('Entity\Ticket')->findOneBy( array('user' => $userKontrol) );

            if(!$ticketKontrol && $session["rol"] != 1){
                $app['session']->getFlashBag()->add('danger', 'bu ticketa cevap veremezsiniz');
                return $app->redirect($app['url_generator']->generate('anasayfa'));
            }

            //
            // 1- User Datasını Al 
            // 2- Asıl Ticket Id'sini al
            // 3- Günü belirle
            //
            $tickets = $em->getRepository('Entity\Ticket')->findOneBy( array('id' => $cevap["id"]) );
            $userself = $em->getRepository('Entity\User')->findOneBy( array('id' => $session["id"]) );
            $gun = strftime("%d %B %Y, %A, %H:%M", time()); 
           
            // Kaydet
            // user
            $entity->setUserId($userself);
            // ticket cevap orm
            $entity->setTicketcevap($tickets);
            // gün
            $entity->setDatetime($gun);
            // cevap
            $entity->setCevap($cevap["cevap"]);

            // database e bas
            $em->persist($entity);
            $em->flush();

            return $app->redirect($app['url_generator']->generate('ticket.goster', array('id' => $cevap["id"])));

        }

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
        // kullanıcı giriş yapmış mı
        if($this->kullaniciKontrol($app)){
            $app['session']->getFlashBag()->add('warning', 'zaten üyesiniz');
            return $app->redirect($app["url_generator"]->generate("anasayfa"));
        }

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

            $app['session']->getFlashBag()->add('success', 'Üyeliğiniz oluşturuldu, şimdi giriş yapabilirsiniz');
            return $app->redirect($app['url_generator']->generate('anasayfa'));
        }

        return $app['twig']->render('Ticket/kullanici-olustur.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }


    public function ticketKapat(Application $app, $id){

        $session = $app["session"]->get("giris");

        // admin ise
        if($session["rol"] == 1 ){
            $em = $app['db.orm.em'];

            $data = $em->getRepository('Entity\Ticket')->findOneBy( array('id' => $id) ); 
            $data->setDurum(0);

            $em->persist($data);
            $em->flush();

            return $app->redirect($app['url_generator']->generate('ticket.goster', array("id" => $id)));
        }

    }

    //
    // Kategori Oluşturur
    //
    public function kategoriOlustur(Application $app, Request $request){

        $session = $app["session"]->get("giris");
        
        // admin değilse anasayfaya geri yolla
        if( $session["rol"] != 1){
            return $app->redirect($app['url_generator']->generate('anasayfa'));
        }

        $em = $app['db.orm.em'];

        // entityi çağır
        $entity = new Kategori();

        // form oluşturucu
        $form = $app['form.factory']->create(new KategoriType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();

            $app['session']->getFlashBag()->add('success', 'Kategori oluşturuldu');
            return $app->redirect($app['url_generator']->generate('kategori'));

        }

        //
        // form oluştur
        //
        return $app['twig']->render('Ticket/kategori-olustur.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));

    } 

    public function kullaniciKontrol(Application $app, $rol = 0){

        if(!$this->uye){
            return false;
        }

        return true;
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
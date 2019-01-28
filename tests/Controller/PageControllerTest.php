<?php

// tests/Controller/PageControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use Sonata\Twig\Extension\FlashMessageExtension as FlashMessageExtension;


class PageControllerTest  extends WebTestCase
{
    public function testShowPage()
    {
        $client = static::createClient();
        
        //test 1
        //$client->request('GET', '/fr/page/profil');
        //self::assertSame(200, $client->getResponse()->getStatusCode());
        
        //test 2
        $crawler = $client->request('GET', '/fr/page/profil');
        //self::assertSame(1, $crawler->filter('html:contains("Profil")')->count());
        self::assertGreaterThan(0,$crawler->filter('html:contains("Profil")')->count());
        
        //test lien
        $crawler = $client->request('GET', '/fr/page/profil');             
        self::assertGreaterThan(0, $crawler->filter('h2:contains("Profil")')->count());
        $link = $crawler
            ->filter('a:contains("Contact")') // find all links with the text "Greet"
            ->eq(1) // select the second link in the list
            ->link()
        ;
        $crawler = $client->click($link);
        self::assertGreaterThan(0, $crawler->filter('h2:contains("Contact")')->count());
        
        // test soumission formulaire CONTACT  
        $crawler = $client->request('GET', '/fr/page/contact');
        self::assertGreaterThan(0, $crawler->filter('h2:contains("Contact")')->count());
        $form = $crawler->selectButton('form[send]')->form();
        $form['form[name]'] = 'nom PHPUnit';
        $form['form[email]'] = 'amorel@trigano.fr PHPUnit';
        $form['form[subject]'] = 'emploi';
        $form['form[message]'] = 'message PHPUnit';
        $crawler = $client->submit($form);        
        // réponse : "message envoyé !"
        self::assertSame(1,$crawler->filter('html:contains("message envoyé !")')->count());
        
        // test soumission formulaire RECHERCHE  
        $crawler = $client->request('GET', '/fr/page/contact');
        //self::assertGreaterThan(0, $crawler->filter('h2:contains("Contact")')->count());
        $form = $crawler->selectButton('search-btn')->form();// nom du bouton
        $form['field-search'] = 'rapport annuel';
        $crawler = $client->submit($form);        
        // réponse : "Recherche > "rapport annuel""
        self::assertSame(1,$crawler->filter('html:contains("Recherche > ")')->count());

    }
    
    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($pageName, $url)
    {
        /*$client = self::createClient();
        $client->request('GET', $url);
        $response = $client->getResponse();
        $this->assertTrue($client->getResponse()->isSuccessful());*/
        
        
        $client = self::createClient();
        $client->catchExceptions(false);
        $client->request('GET', $url);
        $response = $client->getResponse();
        self::assertTrue(
            $response->isSuccessful(),
            sprintf(
                'La page "%s" devrait être accessible, mais le code HTTP est "%s".',
                $pageName,
                $response->getStatusCode()
            )
        );
    }

    public function provideUrls()
    {
        return [
            // FR
            ['Accueil','/'],
            ['Profil FR', '/fr/page/profil'],
            ['Contact FR', '/fr/page/contact'],
            ['Tous les documents > 2019', '/fr/documents/2019'],
            ['Tous les documents > Rapport annuel', '/fr/documents/rapport-annuel'],
            // EN
            ['Profil EN','/en/page/profile'],
            ['Contact EN', '/en/page/contact'],
            ['Documents > 2019', '/en/documents/2019'],
            ['Documents > Annual report', '/en/documents/annual-report'],
            // ...
        ];
    }
}
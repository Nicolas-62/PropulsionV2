<?php

namespace App\Controller\Frontoffice;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Media;
use App\Entity\Online;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'fo_')]
class FOController extends AbstractController
{
    // Datas passées à la vue.
    protected array $data;

    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
        // Code langue.
        $this->entityManager              =     $entityManager;
        // Datas passées à la vue.
        $this->data                       =     array();
        $this->data['styles']             = 	array(); 		// Array des feuilles de styles supplémentaires passées au layout
        $this->data['scripts']            = 	array(); 		// Array des fichiers javascript passés au layout ; chemin relatif depuis le dossier assets sans extension
        $this->list_partial               =     '';
        $this->detail_partial             =     '';
        $this->data['header_partial'] = '_components/header.html.twig';
        $this->data['footer_partial'] = '_components/footer.html.twig';
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirect('home');
    }

    public function lister($champ = "ordre", $tri = "ASC", $limit = 0, $start = 0)
    {


        // Récupération des enfants des catégories concernées.
        $this->data['tree'] = array();
        foreach($this->category_ids as $category_id){
            $this->data['tree'][$category_id] = $this->entityManager->getRepository(Category::class)->getGenealogy($category_id, $this->getParameter('locale'));
        }

        return $this->render($this->getParameter('app.fo_path'). $this->list_partial, $this->data);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, ContactNotification $notification): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

//        dump($form->getExtraData());
//        dump($form['ville']);
//        dump($contact);

        if($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien été envoyé');
        }else{
            $this->addFlash('error', 'Erreur');
        }
        // Vue renvoyée.
        return $this->render('frontoffice/contact/contact.html.twig', [
            'form'          => $form->createView()
        ]);
    }
}

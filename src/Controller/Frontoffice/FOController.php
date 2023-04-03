<?php

namespace App\Controller\Frontoffice;

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


    public EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager  = $entityManager;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirect('liste');
    }

    #[Route('/liste', name: 'liste')]
    public function liste(): Response
    {
        // On récupère la catégorie qui nous intéresse
        $cat = $this->entityManager->getRepository(Category::class)->find(1);
        $list = new ArrayCollection();
        $tree = $this->entityManager->getRepository(Category::class)->getGenealogy($list, 1, false);



//        dd($onlines);
        return $this->render('frontoffice/article/articles.html.twig', [
            'category' => $cat,
            'tree'     => $tree,
        ]);
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

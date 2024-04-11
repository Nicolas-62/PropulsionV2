<?php

namespace App\Controller\Frontoffice;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Config;
use App\Entity\Contact;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Mediaspec;
use App\Entity\Online;
use App\Entity\OpenGraph;
use App\Entity\Seo;
use App\Form\ContactType;
use App\Library\Imagique;
use App\Notification\BoNotification;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'fo_')]
class FOController extends AbstractController
{
    // Ids des categories affichées
    protected $category_ids = null;
    protected $category_id  = null;
    //Nom de la page  = nom du controller
    protected string $page = '';
    // Array des données passées au layout
    protected array $data = array();


    public function __construct(
        protected EntityManagerInterface $entityManager,
        private ContainerBagInterface $params,
    )
    {
        // Datas passées à la vue.
        $this->data['styles']             = 	array(); 		// Array des feuilles de styles supplémentaires passées au layout
        $this->data['scripts']            = 	array(); 		// Array des fichiers javascript passés au layout ; chemin relatif depuis le dossier assets sans extension
        // Récupération du nom du controller courant
        $this->page = $this->getName();
        // Nom de la page = nom du controller
        $this->data['page']              = 	$this->page;
        $this->data['page_title']        = 	$this->page;
        // Nom des vues
        $this->list_partial               =     strtolower($this->page) . '/index.html.twig'; 	// Vue de la liste
        $this->detail_partial             =     strtolower($this->page) . '/detail.html.twig'; // Vue du détail
        $this->data['header_partial']     =     '_components/header.html.twig';
        $this->data['footer_partial']     =     '_components/footer.html.twig';

        $this->data['locale']             =     $this->params->get('locale');
        // HEADER
        // Récupération de la SEO du site
        $this->data['seo']                =     $this->entityManager->getRepository(Config::class)->find(1)->getSeo();
    }

    /**
     * Retournes le nom du controller sans 'Controller'
     * @return string
     */
    public function getName()
    {
        return substr((new \ReflectionClass($this))->getShortName(), 0, -10);
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirect('home');
    }

    /**
     * Methode de construction du header
     *
     * @return void
     */
    public function buildHeader(){
        // A SURCHARGER
    }

    /**
     * Methode de construction du footer
     * @return void
     */
    public function buildFooter(){
        // A SURCHARGER
    }

    public function buildOpenGraph(Article|Category $entity = null){

        // On récupère l'url du site depuis le service request
        $siteUrl = $this->container->get('request_stack')->getCurrentRequest()->getHost();


        // A SURCHARGER
        $openGraph = new OpenGraph();

        // Site
        $openGraph->setSiteName($this->getParameter('app.site'));
        // Locale
        $openGraph->setLocale($this->getParameter('locale'));
        $openGraph->setType('website');

        // Si on a une entité
        if($entity !=null) {
            // Récupération de notre SEO selon la langue
            $seo = $entity->getSeo($this->getParameter('locale'));
            // Titre
            if ($entity->getTitle() != null || trim($entity->getTitle()) != '') {
                $openGraph->setTitle($entity->getTitle());
            }
            // Description
            if ($seo != null){
                if ($seo->getDescription() != null || trim($seo->getDescription()) != '') {
                    $openGraph->setDescription($seo->getDescription());
                }
            }
            // Type
            $openGraph->setType('article');

            // Image (url image)
            $media = $entity->getMediaForOpenGraph();
            // Si une image est définie
            if ($media != null) {
                // Ajout url de l'image
                $media_path = 'https://' . $siteUrl  . '/' . $this->getParameter('app.dyn_img_path') . $media->getMedia();
                dump($media_path);
                $openGraph->setImage($media_path);
                // Récupératin du média link pour récupérer la médiaspec
                $medialink = $this->entityManager->getRepository(MediaLink::class)->findOneBy(['media' => $media->getId()]);
                $mediaspec = $medialink->getMediaspec();
                $openGraph->setImageWidth($mediaspec->getWidth());
                // Image height
                $openGraph->setImageHeight($mediaspec->getHeight());
                $imagique = new Imagique($media_path);
                $openGraph->setImageType($imagique->getImageMimeType());
            }else {
                $openGraph->setImage('https://' . $siteUrl . $this->getParameter('app.static_img_path') . 'PLACEHOLDER_OPENGRAPH.png');

            }
        }
        // Si on a pas d'entité on récupère la SEO du site
        else{
            $defaultSeo = $this->entityManager->getRepository(Config::class)->find(1)->getSeo();
            // Titre
            $openGraph->setTitle($defaultSeo->getTitle());
            // Description
            $openGraph->setDescription($defaultSeo->getDescription());
            $media_path = 'https://' . $siteUrl . '/' . $this->getParameter('app.static_img_path') . 'PLACEHOLDER_OPENGRAPH.png';
            // Image (url image)
            $openGraph->setImage($media_path);
            // Image width
            $openGraph->setImageWidth(347);
            // Image height
            $openGraph->setImageHeight(261);
            $imagique = new Imagique($media_path);
            $openGraph->setImageType($imagique->getImageMimeType());
            // Type
            $openGraph->setType('article');
        }

        //dump($openGraph);
        return $openGraph;
    }


    /**
     * Methode d'affichage d'un article
     *
     * @param Article|null $article
     * @return Response
     */
    public function detail(?Article $article): Response
    {
        // Si l'article n'existe pas, on redirige vers la page d'erreur
//        if (!$article) {
//            return $this->redirectToRoute('fo_index');
//        }

        // On récupère la SEO de l'article ou de ses ancètres le cas échéant
        $seo    =   $this->entityManager->getRepository(Article::class)->getSeo($article, $this->getParameter('locale'));
        // Surcharge de la seo
        // Si l'article a de la Seo et qu'elle n'est pas vide, on l'a récupère sinon on récupère la Seo de la catégorie
        $link = $this->getParameter('app.fo_path'). $this->detail_partial;
        if($seo != null &&  ! $seo->isEmpty()){
            $this->data['seo']  =   $seo;
            $this->data['openGraph'] = $this->buildOpenGraph( $article , $link );
        }
        $this->data['article']  =   $article;
        // Fichiers associés à l'article
        $this->data['files']    =   array();
        // Si les fichiers sont disponibles pour l'article
        if($this->entityManager->getRepository(Article::class)->hasFiles($article)){
            // On récupère les fichiers
            $this->data['files']    =   $article->getFiles();
        }

        return $this->render($this->getParameter('app.fo_path'). $this->detail_partial, $this->data);
    }

    public function lister($champ = "ordre", $tri = "ASC", $limit = 0, $start = 0)
    {
        // Surcharge de la SEO
        // Si on a une seule categorie
        if($this->category_id != null){
            // On récupère la categorie
            $category = $this->entityManager->getRepository(Category::class)->find($this->category_id);
            // Si la catégorie existe
            if($category != null){
                // On récupère sa SEO
                $seo = $this->entityManager->getRepository(Category::class)->getSeo($category, $this->getParameter('locale'));
                // Surcharge de la seo
                if($seo != null && ! $seo->isEmpty()){
                    $this->data['seo'] = $seo;
                }
            }
        }
        $link = $this->getParameter('app.fo_path'). $this->list_partial;
        $this->data['openGraph'] = $this->buildOpenGraph($category ?? null);

        // Pas utilisé pour l'instant
        // Récupération des enfants des catégories concernées.
//        $this->data['tree'] = array();
//        // Si on a une catégorie.
//        foreach($this->category_ids as $category_id){
//            $this->data['tree'][$category_id] = $this->entityManager->getRepository(Category::class)->getGenealogy($category_id, $this->getParameter('locale'));
//        }
        return $this->render($this->getParameter('app.fo_path'). $this->list_partial, $this->data);
    }


    /*
     * file_not_found
     * @description : Affiche une page 404 File not found.
     * @access : protected
     * @return void.
     */
    protected function file_not_found(): Response
    {
        return new Response('FILE NOT FOUND', '404');
    }

}

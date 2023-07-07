<?php
namespace App\EventListener;


use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\CategoryData;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Online;
use App\Entity\Seo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SeoListener implements EventSubscriberInterface
{

    public function __construct(
        // Gestionnaire d'entité
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,

        // Code langue de l'application
        private string $locale
    )
    {
    }


    /**
     * permet de mettre en place les listeners souhaités que l'on retrouve dans ce fichier
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            AfterEntityUpdatedEvent::class => 'edit',
        ];

    }



    /**
     * écoute quand une entité est mise à jour et permet de faire des actions
     *
     * @param AfterEntityUpdatedEvent $event
     * @return void
     */
    public function edit(AfterEntityUpdatedEvent $event)
    {

        // Récupération de l'entité.
        $entity = $event->getEntityInstance();
        // Si on est en édition d'un article.
        if ($entity instanceof Article || $entity instanceof Category) {
            $seo_title       = $this->requestStack->getCurrentRequest()->get('seo_title');
            $seo_description = $this->requestStack->getCurrentRequest()->get('seo_description');
            if(
                isset($seo_title) && trim($seo_title) != '' &&
                isset($seo_description) && trim($seo_description) != ''
            ) {
                $entity_type = strtolower($entity->getClassName());
                // Récupération du code langue par défaut.
                $code_langue = $this->locale;
                // Si un language a été sélectionné.
                if(trim($entity->getLanguage()) != ''){
                    // Récupération du code langue.
                    $code_langue = $entity->getLanguage();
                }

                // Récupération de la langue.
                $language = $this->entityManager->getRepository(Language::class)->findOneBy(['code' => $code_langue]);


                // Récupération de la seo de l'entité.
                $seo = $this->entityManager->getRepository(Seo::class)->findByLanguage($entity, $language);
                // Si la seo n'existe pas on la créer
                if ($seo == null) {
                    $seo = new Seo();
                    $seo->setLanguage($language);
                    $seo->{'set' . $entity_type}($entity);
                }
                $seo->setTitle($seo_title)->setDescription($seo_description);
                $this->entityManager->getRepository(Seo::class)->save($seo, true);
            }
        }// end article
    }
}

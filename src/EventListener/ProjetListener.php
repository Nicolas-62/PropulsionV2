<?php

namespace App\EventListener;

use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\ArticleData;
use App\Entity\Category;
use App\Entity\Config;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Projet;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class ProjetListener implements EventSubscriberInterface
{
    public function __construct(
        AdminContextProvider $contextProvider,
        RequestStack $request,
        // Gestionnaire d'entité
        AuthorizationCheckerInterface $authChecker,
        // Gestionnaire d'entité
        EntityManagerInterface $entityManager
    )
    {
        $this->contextProvider = $contextProvider;
        $this->request = $request;
        // Gestionnaire d'entité
        $this->authChecker = $authChecker;
        $this->entityManager = $entityManager;
        $this->session = $request->getSession();
    }

    /**
     * permet de mettre en place les listeners souhaités que l'on retrouve dans ce fichier
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        // On défini le parent avant de définir le slug
        return [
            BeforeEntityUpdatedEvent::class  => 'checkEtape',
            AfterEntityUpdatedEvent::class   => 'defineMontantTTC',
            AfterEntityPersistedEvent::class => [['defineMontantTTC', 1]],
            BeforeEntityPersistedEvent::class => [['createFolders', 1]],
        ];
    }

    public function defineMontantTTC(AfterEntityUpdatedEvent|AfterEntityPersistedEvent $event)
    {
        // Entité
        $entity = $event->getEntityInstance();
        // Si l'entité n'a pas d'erreur
        if (!$entity->hasError()) {
            // On récupère la configuration
            $config = $this->entityManager->getRepository(Config::class)->findOneBy([]);
            // On calcule le montant TTC
            $montantTTC = $entity->getMontantHT() * (1 + $config->getTauxTVA() / 100);
            $entity->setMontantTTC($montantTTC);
            // On sauvegarde
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
    }

    public function createFolders(BeforeEntityPersistedEvent $event)
    {
        // Entité
        $entity     = $event->getEntityInstance();
        // Flag pour savoir si le dossier a été créé
        $folderCreated = true;
        // Chargement composant Filesystem
        $filesystem = new Filesystem();
        // Si le dossier de production existe
        if ($filesystem->exists(Constants::PROJET_PATH)) {
            try {
                // On créer le dossier du projet
                $filesystem->mkdir(Constants::PROJET_PATH . $event->getEntityInstance()->getRefInterne());
                // On créer les sous dossiers nécéssaires
                foreach (Constants::PROJET_SUB_FOLDERS as $folder) {
                    $filesystem->mkdir(Constants::PROJET_PATH . $event->getEntityInstance()->getRefInterne() . '/' . $folder);
                }
            } catch (\Exception $e) {
                $folderCreated = false;
            }
        }else{
            $folderCreated = false;
        }
        if( ! $folderCreated){
            // On indique que l'entité n'est pas valide.
            $entity->setError();
            $this->session->getFlashBag()->add('danger', 'Impossible de créer les dossiers du projet.');
        }
    }


        public function checkEtape(BeforeEntityUpdatedEvent $event): void
    {
        // Entité
        $entity     = $event->getEntityInstance();
        // Action en cours
        $action     = $this->contextProvider->getContext()->getCrud()->getCurrentAction();
        // Utilisateur connecté
        $user       = $this->contextProvider->getContext()->getUser();
        // Nom du champ en cours d'édition.
        $fieldName  = $this->request->getCurrentRequest()->get("fieldName");
        // Valeur
        $newValue   = $this->request->getCurrentRequest()->get("newValue");
        // Si on en édition
        if($action == "edit") {
            // Si l'entité est une catégorie ou un article.
            if ($entity instanceof Projet) {
                // Cas particulier pour les étapes
                // Si le champ commence par la chaine de caractère "etape"
                if (str_starts_with($fieldName, 'etape')) {
                    // Si on a validé l'étape
                    if ($newValue === 'true') {
                        // On renseigne l'utilisateur qui a validé l'étape
                        $entity->{'setUser' . ucfirst($fieldName)}($user->getId());
                    } else {
                        // Si l'utilisateur est administrateur ou qu'il correspond à celui qui avait validé l'étape
                        if (
                            $this->authChecker->isGranted('ROLE_ADMIN') ||
                            $user->getId() === $entity->{'getUser' . ucfirst($fieldName)}()
                        ) {
                            // On supprime l'utilisateur qui avait validé l'étape
                            $entity->{'setUser' . ucfirst($fieldName)}(null);
                        } // Sinon on remet le champ coché
                        else {
                            $entity->{'set' . ucfirst($fieldName)}(true);
                        }
                    }
                }
            }else{
                return;
            }
        }else{
            return;
        }
    }
}
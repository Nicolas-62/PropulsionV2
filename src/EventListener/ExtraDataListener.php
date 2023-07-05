<?php
namespace App\EventListener;


use App\Constants\Constants;
use App\Entity\Article;
use App\Entity\ArticleData;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\MediaLink;
use App\Entity\Online;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExtraDataListener implements EventSubscriberInterface
{

    public function __construct(
        // Gestionnaire d'entité
        private EntityManagerInterface $entityManager,
        // Code langue de l'application
        private string $locale
    )
    {
    }


    /** getSubscribedEvents permet de mettre en place les listeners souhaités que l'on retrouve dans ce fichier
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    //public function getSubscribedEvents()

    {
        return [
            AfterEntityUpdatedEvent::class => 'edit',
            //PostLoadEventArgs::class => 'getDatas'
        ];
//        return [
//            //Events::postLoad,
//            Events::postUpdate,
//        ];
    }


    /**
     * getDatas hydrate les champs spécifiques de l'objet avec leurs datas associées.
     * @param PostLoadEventArgs $eventArgs
     * @return void
     */
    public function postLoad(PostLoadEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getObjectManager();
        // Si c'est un article
        if ($eventArgs->getObject() instanceof Article) {
            $article = $eventArgs->getObject();
            $datas = $entityManager->getRepository(ArticleData::class)->getDatas($article, $this->locale);

            $dataNames = array_keys($datas);

            foreach($article->getExtraFieldNames() as $extraFieldName){
                if(in_array($extraFieldName, $dataNames)){
                    $article->{'set' . $extraFieldName}($datas[$extraFieldName]->getFieldValue());
                }
            }
        }
    }



    public function postUpdate(PostUpdateEventArgs $eventArgs)
    {
        dump('postUpdate');
        $entityManager = $eventArgs->getObjectManager();

        // Récupération de l'entité.
        $entity = $eventArgs->getObject();
        // Si c'est un article
        if ($entity instanceof Article) {

            // Récupération des datas de l'entité.
            $datas = $entityManager->getRepository(ArticleData::class)->getDatas($entity, $this->locale);

            // Formatage des données.

//            dump('edit, datas : ');
//            dump($datas);
//            dump($entity->getHeadline());
//            dump($datas['headline']->getFieldValue());

            // Récupération des noms.
            $dataNames = array_keys($datas);
            // Récupération de la langue.
            $language = $entityManager->getRepository(Language::class)->findOneBy(['code' => $this->locale]);

            // Pour chaque champ.
            foreach($entity->getExtraFields() as $field){
                // Si l'entrée existe déjà.
                if(in_array($field['name'], $dataNames)){
//                    dump('test');
                    // Si la valeur a été modifiée.
                    if($entity->{'get'.ucfirst($field['name'])}() != $datas[$field['name']]->getFieldValue()){
//                        dump('ok');
                        // Mise à jour de la valeur.
                        // CASTING en string pour formatage des booleens.
                        $datas[$field['name']]->setFieldValue( (string) $entity->{'get'.ucfirst($field['name'])}());
                        $entityManager->persist($datas[$field['name']]);
                    }
                }
                // Si l'entrée n'existe pas
                else{
                    // Création de la data
                    $articleData = new ArticleData();
                    $articleData
                        ->setObject($entity)
                        ->setLanguage($language)
                        ->setFieldKey( $field['name'] )
                        ->setFieldValue( (string) $entity->{'get'.ucfirst($field['name'])}() );
                    $entityManager->persist($articleData);
                }
            }// end foreach

            // Sauvegarde des données mise a jour.
            $entityManager->flush();
        }// end article
    }







    /** edit écoute quand une entité est créée et permet de faire des actions
     *
     * @param AfterEntityUpdatedEvent $event
     * @return void
     */
    public function edit(AfterEntityUpdatedEvent $event)
    {

        // Récupération de l'entité.
        $entity = $event->getEntityInstance();

        // Récupération du code langue par défaut.
        $code_langue = $this->locale;
        if(isset($entity->langue) && trim($entity->getLanguage()) != ''){
            // Récupération du code langue.
            $code_langue = $entity->getLanguage();
        }
        // Récupération de la langue.
        $language = $this->entityManager->getRepository(Language::class)->findOneBy(['code' => $code_langue]);

        // Si on est en édition d'un article.
        if ($entity instanceof Article) {
            // Récupération des datas de l'entité.
            $datas = $this->entityManager->getRepository(ArticleData::class)->getDatas($entity, $language->getCode());

            // Formatage des données.
            dump('edit, datas : ');
            dump($datas);
    //        dump($entity->getHeadline());
    //        dump($datas['headline']->getFieldValue());

            // Récupération des noms.
            $dataNames = array_keys($datas);



            // Pour chaque champ.
            foreach($entity->getExtraFields() as $field){
                // Si l'entrée existe déjà.
                if(in_array($field['name'], $dataNames)){
                    // DEBUG
                    // dump('test');
                    // Si la valeur a été modifiée.
                    if($entity->{'get'.ucfirst($field['name'])}() != $datas[$field['name']]->getFieldValue()){
                        // DEBUG
                        // Mise à jour de la valeur.
                        // CASTING en string pour formatage des booleens.
                        $datas[$field['name']]->setFieldValue( (string) $entity->{'get'.ucfirst($field['name'])}());
                        $this->entityManager->persist($datas[$field['name']]);
                    }
                }
                // Si l'entrée n'existe pas
                else{
                    // Création de la data
                    $articleData = new ArticleData();
                    $articleData
                        ->setObject($entity)
                        ->setLanguage($language)
                        ->setFieldKey( $field['name'] )
                        ->setFieldValue( (string) $entity->{'get'.ucfirst($field['name'])}() );
                    $this->entityManager->persist($articleData);
                }
            }// end foreach

            // Sauvegarde des données mise a jour.
            $this->entityManager->flush();
        }// end article
    }
}

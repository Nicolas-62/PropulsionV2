<?php
namespace App\EventListener;


use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
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
            // Récupération du nom du repository des datas.
            $repository = "App\\Entity\\" . ucfirst($entity->getClassName()).'Data';

            // Récupération du code langue par défaut.
            $code_langue = $this->locale;
            //dump($entity->getLanguage());
            if($entity->getLanguage() != null && trim($entity->getLanguage()) != ''){
                // Récupération du code langue.
                $code_langue = $entity->getLanguage();
            }
            // Récupération de la langue.
            $language = $this->entityManager->getRepository(Language::class)->findOneBy(['code' => $code_langue]);
            //dump($language);
            // Récupération des datas de l'entité.
            $datas = $this->entityManager->getRepository($repository)->getDatas($entity, $language->getCode());

            // Récupération des noms.
            $dataNames = array_keys($datas);

            // Pour chaque champ.
            foreach($entity->getExtraFields() as $field){
                // Si l'entrée existe déjà.
                if(in_array($field['name'], $dataNames)){
                    // Si la valeur a été modifiée.
                    if($entity->{'get'.ucfirst($field['name'])}() != $datas[$field['name']]->getFieldValue()){
                        // Mise à jour de la valeur.
                        // CASTING en string
                        //formatage des dates.
                        if(str_contains($field['name'], 'date')){

                            $datas[$field['name']]->setFieldValue($entity->{'get'.ucfirst($field['name'])}()->format($field['format']));
                        }
                        else{
                            $datas[$field['name']]->setFieldValue( (string) $entity->{'get'.ucfirst($field['name'])}());
                        }
                        $datas[$field['name']]->setUpdatedAt(new \DateTimeImmutable());
                        $this->entityManager->persist($datas[$field['name']]);
                    }

                }
                // Si l'entrée n'existe pas
                else{

                    $entityData = new $repository();
                    // Création de la data
                    $entityData
                      ->setObject($entity)
                      ->setLanguage($language)
                      ->setFieldKey( $field['name'] );
                    if(str_contains($field['name'], 'date')) {
                        $entityData->setFieldValue($entity->{'get' . ucfirst($field['name'])}()->format($field['format']));
                    }else{
                        $entityData->setFieldValue( (string) $entity->{'get' . ucfirst($field['name'])}());
                    }

                    $this->entityManager->persist($entityData);
                }
            }// end foreach

            // Sauvegarde des données mise a jour.
            $this->entityManager->flush();
        }// end article
    }
}

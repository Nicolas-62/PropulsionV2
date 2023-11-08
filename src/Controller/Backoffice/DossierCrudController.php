<?php

namespace App\Controller\Backoffice;

use App\Entity\Projet;
use App\Field\EtapeField;
use App\Field\ImageUploadField;
use App\Field\MontantHTField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DossierCrudController extends ProjetCrudController
{

    public function __construct(
        // Générateur de routes
        protected AdminUrlGenerator $adminUrlGenerator,
    )
    {
        $this->list_template_name = 'backoffice/projet/projets_etapes.html.twig';

        parent::__construct($adminUrlGenerator);
    }

    /**
     * Configure les champs à afficher dans les interfaces.
     *
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm()->setPermission('ROLE_DEV');
        yield TextField::new('client', 'Client');
        yield DateField::new('dateCreation', 'Date')->setEmptyData('');
        yield TextField::new('refInterne', 'Ref. Interne');
        yield TextField::new('refExterne', 'Ref. Client')->hideOnIndex();
        yield MontantHTField::new('montantHT', 'Montant HT')->onlyOnForms();
        // Etapes
        // Ajout des etapes
        foreach($this->getEtapeFields() as $EtapeField){
            yield $EtapeField;
        }
    }

}

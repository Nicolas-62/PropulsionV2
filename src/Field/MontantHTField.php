<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class MontantHTField  implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string $propertyName
     * @param string|null $label
     * @return mixed
     */
    public static function new(string $propertyName, ?string $label = null): MoneyField
    {
        // Configuration du champ d'upload d'un média.
        return MoneyField::new($propertyName, $label)
            ->setCurrency('EUR')
            // this template is used in 'index' and 'detail' pages
            ->setTemplatePath('backoffice/field/projet_montant_ht.html.twig')
        ;
    }

}
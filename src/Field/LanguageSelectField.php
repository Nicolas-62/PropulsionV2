<?php

  namespace App\Field;

  use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
  use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
  use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

  class LanguageSelectField  implements FieldInterface
  {
    use FieldTrait;

    /**
     * @param string $propertyName
     * @param string|null $label
     * @return mixed
     */
    public static function new(string $propertyName, ?string $label = null)
    {
      // Configuration du champ d'upload d'un média.
      return (ChoiceField::new($propertyName, 'Choisissez un language existant.')
        ->setColumns(4)
        ->setFormTypeOptions([
            'multiple' => false,
        ])->renderExpanded()
      );
    }

  }
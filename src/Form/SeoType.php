<?php

namespace App\Form;

use App\Entity\Seo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('keywords', CollectionType::class, [
                'entry_type' => TextType::class,
                'label' => 'Mots clés',
                'allow_add' => true,
                'allow_delete' => true
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configuration du formulaire
        $resolver->setDefaults([
            'data_class' => Seo::class,
        ]);
    }


}

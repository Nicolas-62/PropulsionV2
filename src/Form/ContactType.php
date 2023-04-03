<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('ville', TextType::class,
                [
                    'mapped' => false,
                    'constraints' => [
                        new Length([
                            'min' => 5,
                            'minMessage' => 'Vous devez saisir au moins 5 caractères'
                        ])
                    ]
                ]
            )
//            ->add('lastname', TextType::class)
//            ->add('phone', TextType::class)
//            ->add('email', EmailType::class)
//            ->add('message', TextareaType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configuration du formulaire
        $resolver->setDefaults([
            'data_class' => Contact::class,
            // Fichier de traduction des labels
            'translation_domain' => 'contact'
        ]);
    }

}

<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('subject', ChoiceType::class, [
                'choices' => $this->getChoices(),
                'label' => 'Sujet',
            ])
            ->add('replyTo', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
            ])
            ->add('getNewsletter', CheckboxType::class, [
                'label'    => 'Je souhaite recevoir la newsletter de La Lune',
                'required' => false,
            ]);
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

    /**
     * Récupère les choix pour le champ sujet
     * @return array
     */
    private function getChoices(): array
    {
        $choices = Contact::getSubjects();
        $output = [];
        foreach($choices as $value => $choice){
            $output[$choice] = $choice;
        }
        return $output;
    }

}

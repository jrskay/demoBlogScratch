<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class,[
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                // Si expended cest false @ multiple c'est false alors ca donne un select
                // Si expended et false et multipe et true alors select et multipke
                // Expende => true & multipe => false = radio
                // Expende => true & multipe => true = checkbox

                // expanded => false & multiple => false : select
                // expanded => false & multiple => true : select multiple
                // expanded => true & multiple => false : radio buttons
                // expanded => true & multiple => true : checkboxes

                'label' => 'Rôles'
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                // mapped => false signifie que Symfony ne doit pas verifier dans la table si le champ plainPassword existe
                'required' => false,
                // required => false permet de ne pas remplir le champ mdp
                'label' => 'Mot de passe'
            ])
            ->add('prenom')
            ->add('nom')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}


<?php

declare(strict_types=1);

namespace App\Security\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', TextType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'autocomplete' => 'email',
                ],
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Password',
                'required' => true,
                'attr' => [
                    'autocomplete' => 'current-password',
                ],
            ])
            ->add('_csrf_token', HiddenType::class, [
                'data' => (string) $options['csrf_token'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_token' => '',
        ]);

        $resolver->setAllowedTypes('csrf_token', 'string');
    }
}

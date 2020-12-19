<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => 'Prénom',
                    'empty_data' => ''
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'Nom',
                    'empty_data' => ''
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Adresse Email',
                    'empty_data' => ''
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', User::class);
    }
}

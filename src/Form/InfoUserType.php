<?php

namespace App\Form;

use App\Entity\InfoUser;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class InfoUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'First name is required.',
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'First name cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('lastName', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Last name is required.',
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'Last name cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('phoneNumber', null, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Phone number must contain only digits.',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 10,
                        'minMessage' => 'Phone number must be at least {{ limit }} characters long.',
                        'maxMessage' => 'Phone number cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoUser::class,
        ]);
    }
}

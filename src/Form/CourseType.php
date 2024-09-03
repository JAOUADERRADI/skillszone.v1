<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\Extension\Core\Type\FileType;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The title should not be blank.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'The title cannot be longer than {{ limit }} characters.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z0-9\s\-]+$/',
                        'message' => 'The title can only contain letters, numbers, spaces, and hyphens.',
                    ]),
                ],
            ])
            ->add('description', null, [
                'constraints' => [
                    new Assert\Url([
                        'message' => 'The image URL is not valid.',
                    ]),
                    new Assert\NotBlank([
                        'message' => 'The image URL should not be blank.',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Course Image (JPG, PNG file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG or PNG).',
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
            'data_class' => Course::class,
        ]);
    }
}

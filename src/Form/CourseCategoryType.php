<?php

namespace App\Form;

use App\Entity\CourseCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class CourseCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The category name field should not be blank.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'The name of category cannot be longer than {{ limit }} characters.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z\s\-]+$/',
                        'message' => 'The category name can only contain letters, spaces, and hyphens.',
                    ]),
            ],
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourseCategory::class,
        ]);
    }
}

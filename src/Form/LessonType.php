<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Lesson;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Title should not be blank.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Title cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Content should not be blank.',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Content should be at least {{ limit }} characters long.',
                    ]),
                ],
            ])
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'choice_label' => 'title',
                'placeholder' => 'Choose a course',
                'constraints' => [
                    new Assert\NotNull([
                        'message' => 'Please select a course.',
                    ]),
                ],
            ])
            ->add('video', FileType::class, [
                'label' => 'Video (MP4 file)',
                // unmapped means that this field is not associated with any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the video every time you edit the lesson
                'required' => false,
                // constraints for file validation
                'constraints' => [
                    new File([
                        'maxSize' => '1024M',  // 1GB limit
                        'mimeTypes' => [
                            'video/mp4',
                            'video/mpeg',
                            'video/quicktime',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid video file (MP4, MPEG, QuickTime)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}

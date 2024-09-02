<?php

namespace App\Form;

use App\Entity\User;

// Import necessary classes from Symfony components

use Symfony\Component\Form\AbstractType; // Base class for creating form types in Symfony
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; // Form type for creating a checkbox field
use Symfony\Component\Form\Extension\Core\Type\EmailType; // Import the EmailType class for creating an email input field in the form
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Form type for creating a password input field
use Symfony\Component\Form\Extension\Core\Type\RepeatedType; // Form type for creating a field that repeats another field, typically used for password confirmation
use Symfony\Component\Form\FormBuilderInterface; // Interface for building forms by adding fields and their configurations
use Symfony\Component\OptionsResolver\OptionsResolver; // Configures the options for a form type, allowing you to set default values and define which options are available

// Import necessary validation constraint classes from Symfony

use Symfony\Component\Validator\Constraints\Email; // Import the Email constraint to validate that the input is a properly formatted email address
use Symfony\Component\Validator\Constraints\IsTrue; // Ensures that a boolean value is true, often used for validating terms acceptance checkboxes
use Symfony\Component\Validator\Constraints\Length; // Validates the length of a string, ensuring it falls within a specified range
use Symfony\Component\Validator\Constraints\NotBlank; // Validates that a field is not empty or null, ensuring a value is provided
use Symfony\Component\Validator\Constraints\NotCompromisedPassword; // Validates that a password has not been compromised in known data breaches, enhancing security
// use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email address',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address',
                    ]),
                    // new Regex([
                    //     'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                    //     'message' => 'Please enter a valid email address',
                    // ])
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new NotCompromisedPassword([
                            'message' => 'This password has been exposed in a data breach, it cannot be used. Please use another password.',
                        ]),
                    ],
                    'label' => 'Password',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'label' => 'Repeat Password',
                ],
                'invalid_message' => 'The password fields must match.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Course;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeCourse', TextType::class, [
                'label' => 'Код:',
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 1, 255),
                ],
                'attr' => [
                    'placeholder' => 'Код курса',
                    'class'=> 'mb-3'
                ]
            ])
            ->add('nameCourse', TextType::class, [
                'label' => 'Название:',
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 1, 255),
                ],
                'attr' => [
                    'placeholder' => 'Название курса',
                    'class'=> 'mb-3'
                ]
            ])
            ->add('descriptionCourse', TextareaType::class, [
                'label' => 'Описание курса:',
                'constraints' => [
                    new Length(array('max' => 1000))
                ],
                'attr' => [
                    'placeholder' => 'Описание курса',
                    'class'=> 'mb-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}

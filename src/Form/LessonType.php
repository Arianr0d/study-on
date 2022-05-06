<?php

namespace App\Form;

use App\Entity\Lesson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;

class LessonType extends AbstractType
{
    /*private $transform;

    public function __construct(CourseToStringTransform $transform)
    {
        $this->transform = $transform;
    }*/

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameLesson', TextType::class, [
                'label' => 'Название:',
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 1, 255)
                ],
                'attr' => [
                    'placeholder' => 'Название урока',
                    'class'=> 'mb-3'
                ]
            ])
            ->add('contentLesson', TextareaType::class, [
                'label' => 'Описание урока:',
                'constraints' => [
                    new NotBlank()
                ],
                'attr' => [
                    'placeholder' => 'Описание урока',
                    'class'=> 'mb-3'
                ]
            ])
            ->add('numberLesson', NumberType::class, [
                'label' => 'Порядковый номер:',
                'constraints' => [
                    new NotBlank(),
                    new Range(array(
                        'min' => 1,
                        'max' => 10000
                    ))
                ],
                'attr' => [
                    'placeholder' => 'Порядковый номер урока',
                    'class'=> 'mb-3'
                ]
            ])
            //->add('idCourse', HiddenType::class)
        ;
        /*$builder->get('course')
            ->addModelTransformer($this->transform);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}

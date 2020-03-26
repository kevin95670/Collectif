<?php

namespace App\Form;

use App\Entity\Events;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Repository\EventsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EventsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('date', DateTimeType::class, [
                'data'  => new \DateTime(),
                'years' => range(date('Y'), date('Y')+5),
                'attr'   => ['min' => ( new \DateTime() )->format('Y-m-d H:i:s')]
            ])
            ->add('city', TextType::class)
            ->add('nb_participant', NumberType::class)
            ->add('imageFile', FileType::class, ['required' => false])
            ->add('description', TextareaType::class)
            ->add('categories', EntityType::class, [
            'class' => Categories::class,
            'choice_label' => 'name',
            'expanded' => true,
            'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Events::class,
        ));
    }
}
<?php 

namespace App\Form;

use App\Entity\Categories;
use App\Data\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SearchType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', TextType::class, [
            	'label' => false,
            	'required' => false,
            	'attr' => [ 'placeholder' => 'Mot clé']
            	])
            ->add('date', DateType::class, [
            	'required' => false,
            	'format' => 'dd/MM/yyyy',
    			'input' => 'string',
    			'input_format' => 'Y-m-d' // ajouté en 4.3
    			])
            ->add('city', TextType::class, [
            	'required' => false
            	])
            ->add('categories', EntityType::class, [
        	'required' => false,
            'class' => Categories::class,
            'choice_label' => 'name',
            'expanded' => true,
            'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SearchData::class,
            'method' => 'GET',
            'crsf_protection' => false
        ));
    }

    public function getBlockPrefix()
    {
    	return '';
    }

}
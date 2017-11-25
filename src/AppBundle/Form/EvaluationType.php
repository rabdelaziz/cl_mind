<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class EvaluationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('title', TextType::class)
	        ->add('status', EntityType::class, array(
	        		'class' => 'AppBundle:Status',
	        		'choice_label' => 'name',
	        ))
	        ->add('difficulty', EntityType::class, array(
        		'class' => 'AppBundle:Difficulty',
        		'choice_label' => 'name',
	        ))        
        	->add('topics', EntityType::class, array(
                'class' => 'AppBundle:Topic',
                'choice_label' => 'name',
                'mapped' => false,
                'expanded' => true,
                'multiple' => true,))
/*
            ->add('candidates', CollectionType::class, array(
            	'entry_type' => ContactType::class,
            	'entry_options'  => array(
            	'label' => false
            	),
            ))*/
            ->add('save', SubmitType::class, array('label' => 'Enregistrer'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Evaluation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_evaluation';
    }


}

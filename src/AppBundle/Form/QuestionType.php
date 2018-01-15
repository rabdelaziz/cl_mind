<?php

namespace AppBundle\Form;

use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use AppBundle\Form\ResponseType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class QuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('content', TextareaType::class, array(
            		'label' => 'Enoncé'
            ))
            ->add('duration', ChoiceType::class, array(
            		'label' => 'Durée (en mn)',
            		'choices' => array(
            		    1 => 1,
            		    2 => 2,
            		    3 => 3,
            		    4 => 4,
            		    5 => 5,
            		)
            ))
            ->add('level', EntityType::class, array(
                'class' => 'AppBundle:Level',
                'choice_label' => 'name',
            	'label' => 'Niveau',
            ))
            ->add('topic', EntityType::class, array(
                'class' => 'AppBundle:Topic',
                'choice_label' => 'name',
            	'label' => 'Thème',
            ))
            ->add('status', CheckboxType::class, [
                'label' => 'Statut',
                'required' => false,
            ])
            ->add('responses', CollectionType::class, array(
                'entry_type' => ResponseType::class,
                'allow_add' => true,
                'allow_delete' => true,
            	'label' => 'Réponses',
            	'by_reference' => false
            ))

            ->add('save', SubmitType::class, array('label' => 'Enregistrer'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Question'
        ));
    }
}
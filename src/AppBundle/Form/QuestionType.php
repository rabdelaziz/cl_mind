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

class QuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class)
            ->add('duration')
            ->add('level', EntityType::class, array(
                'class' => 'AppBundle:Level',
                'choice_label' => 'name',
            ))
            ->add('topic', EntityType::class, array(
                'class' => 'AppBundle:Topic',
                'choice_label' => 'name',
            ))
            ->add('responses', CollectionType::class, array(
                'entry_type' => ResponseType::class,
                'allow_add' => true,
                'allow_delete' => true
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

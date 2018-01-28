<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EvaluationFiltersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $options['entity_manager'];
        
        $builder
            ->add('authors', EntityType::class, [
                'class' => 'AppBundle:User',
                /*
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.firstName', 'ASC');
                },*/
                'label' => 'Référent',
                'choice_label' => 'firstName',
                
            ])
            ->add('difficulty', EntityType::class, [
                'class' => 'AppBundle:Difficulty',
                'label' => 'Niveau',
                'choice_label' => 'name',
                //'expanded' => true,
                //'multiple' => true,
            ])/*
            ->add('favoritecities', CollectionType::class, array(
                'entry_type'   => ChoiceType::class,
                'entry_options'  => array(
                    'choices'  => array(
                        'Nashville' => 'nashville',
                        'Paris'     => 'paris',
                        'Berlin'    => 'berlin',
                        'London'    => 'london',
                    ),
                ),
            ))*/
            ->add('status', ChoiceType::class, [
                
                /* 'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                 },*/
                'choices'  => $this->getAllStatus($em),
                'label' => 'Etat',
                'choice_label' => 'name',
                'expanded' => true,
                //'multiple' => true,
            ]);
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
        $resolver->setRequired('entity_manager');
    }
    
    public function getAllStatus($em)
    {
        $choices = array();
        $statusList = $em->getRepository('AppBundle:Status')->findAll();
        foreach ($statusList as $status) {
            $choices[$status->getId()] = $status;
        }

        return $choices;
    }
    public function getAllReferent($em)
    {
        //var_dump('test'); die;
        $choices = array();
        $candidates = $em->getRepository('AppBundle:User')->findAll();
        foreach ($candidates as $candidate) {
            $choices[$candidate->getId()] = $candidate->getFirstName();
        }
        //var_dump($choices); die;
        return $choices;
    }
}
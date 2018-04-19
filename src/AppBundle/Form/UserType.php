<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('firstName', TextType::class, [
					'label' => 'Prénom',
			])
			->add('lastName', TextType::class, [
					'label' => 'Nom',
			])
			->add('email', EmailType::class, [
					'label' => 'Email',
			])
			->add('enabled', CheckboxType::class, [
					'label' => 'Actif',
					'data' => true,
					'required' => false
			])
			->add('username', TextType::class, [
					'label' => 'Identifiant',
			])
			->add('roles', CollectionType::class, array(
					'entry_type'   => ChoiceType::class,
					'entry_options'  => array(
							'label' => false,
							'choices'  => array(
									'Administrateur' => 'ROLE_ADMIN',
									'Manager' => 'ROLE_MANAGER',
									'Réferent' => 'ROLE_REFERENT',
							),
					),
					'label' => 'Rôle',
					
			))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ));
			/*
			->add('roles', ChoiceType::class, [
					'label' => 'Rôle',
					'multiple' => false,
					'choices' => [
							'Administrateur' => 'ROLE_ADMIN',
							'Manager' => 'ROLE_MANAGER',
							'Referent' => 'ROLE_REFERENT',
					]
			]);*/
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'AppBundle\Entity\User'
		));
	}
}
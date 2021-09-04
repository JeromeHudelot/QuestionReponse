<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Type\TagType;
use App\Entity\Question;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
				'label'  => 'Intitulé',
			])
            ->add('content', TextareaType::class, [
				'label'  => 'Votre question',
			])
            ->add('tags', CollectionType::class, [
				'label'  => 'Tags',
				'entry_type' => TagType::class,
				'prototype' => true,
				'allow_add' => true,
				'by_reference' => false,
				'entry_options' => [
					'attr' => ['class' => 'tag-box'],
				],
			])
            ->add('submit', SubmitType::class, [
				'label'  => 'Sauvegarder',
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}

?>
<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
				'label'  => 'Pseudo',
			])
            ->add('email', EmailType::class, [
				'label'  => 'Email',
			])
            ->add('password', PasswordType::class, [
				'label'  => 'Mot de passe',
			])
            ->add('submit', SubmitType::class, [
				'label'  => 'Sauvegarder',
			])
        ;
    }
}

?>
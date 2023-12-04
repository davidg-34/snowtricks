<?php

namespace App\Form;

use App\Entity\Tricks;
use App\Entity\Category;
use App\Entity\Medias;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Trick extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('description', TextType::class, [
                'label' => 'Description'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('medias', EntityType::class, $options = [
                'class' => Medias::class,
                'choice_label' => 'id'
            ])
            ->getForm();
    }

    /* public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    } */
}
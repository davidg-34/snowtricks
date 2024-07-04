<?php

namespace App\Form;

use App\Entity\Medias;
use App\Entity\Tricks;
use App\Entity\Category;
use App\Form\PictureType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('description', TextType::class, [
                'label' => 'Description'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie des figures',
                'required' => false
            ])
            ->add('coverPhoto', FileType::class, [
                'data_class' => null,
                'empty_data' => '',
                'label' => 'Photo à la une',
                'mapped' => false,
                'required' => false
            ])
            /* ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'entry_options' => ['label' => 'Ajouter une image'],
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'mapped' => false,
                'required' => false,
                'by_reference' => false
            ]) */
            /* ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'entry_options' => ['label' => 'Image'],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => 'Ajouter des images',
                'mapped' => false,
                'required' => false,
            ]) */
            ->add('pictures', FileType::class, [
                'data_class' => null,
                'label' => 'Ajouter une image',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
            ])
            ->add('videos', FileType::class, [
                'data_class' => null,
                'label' => 'Ajouter une video',
                'mapped' => false,
                'required' => false,
                'multiple' => true
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
<?php

namespace App\Form;

use App\Entity\Tricks;
use App\Entity\Category;
use App\Entity\Medias;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'mapped' => true,
                'required' => false
            ])
            ->add('picture', FileType::class, [
                'data_class' => null,
                'label' => 'Ajouter une image',
                'mapped' => false,
                'required' => false
            ])
            ->add('video', FileType::class, [
                'data_class' => null,
                'label' => 'Ajouter une video',
                'mapped' => false,
                'required' => false
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
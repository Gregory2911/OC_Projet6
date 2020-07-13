<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\PictureType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')            
            ->add('category') 
            // ->add('picture', FileType::class, [
            //     'mapped' => false, // to not try to store the file in the database
            //     'required' => false, // to not re-upload the file at every edit for update
            //     'label' => 'Image d\'illustration',
            //     'constraints' => [
            //         new Image([
            //             'maxSize' => '1024k',
            //             'maxSizeMessage' => 'Le fichier image est trop lourd (1024Ko maximum)',
            //             'mimeTypes' => [
            //                 'image/jpg',
            //                 'image/jpeg',
            //                 'image/png',
            //             ],
            //             'mimeTypesMessage' => 'Merci d\'envoyer un fichier image jpeg, jpg ou png valide',
            //         ])
            //     ],
            // ])
            ->add('trickPictures', CollectionType::class,[
                'entry_type' => PictureType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ])
            // ->add('mainPicture')     
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

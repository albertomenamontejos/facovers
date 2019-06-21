<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
//use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class UploadPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('song', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Nombre de la canción',
                )
            ])
            ->add('artist', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Nombre del artista/autor',
                )
            ])
            ->add('videoFile', VichFileType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024M',
                        'mimeTypes' => [
                            'video/webm',
                            'video/ogg',
                            'video/mp4',
                            'video/x-msvideo',
                            'video/quicktime'
                        ]
                    ])
                ],
                'attr' => array(
                    'class' => 'file'
                )
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'description',
                            'placeholder' => 'Incluye una descripción',
                            'rows'=> 4
                    ],
            ])
            ->add('subir_video', SubmitType::class,
                ['label' => 'Subir video',
                    'attr' => ['class' => 'btn-upload']
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Post'
        ]);
    }
}
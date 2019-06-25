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
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Nombre del evento',
                )
            ])
            ->add('location', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Localización',
                )
            ])
            ->add('price', MoneyType::class, [
//                'divisor' => 100,
                'label' => false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Precio entrada',
                )
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'description',
                    'placeholder' => 'Incluye una descripción',
                    'rows'=> 4
                ],
            ])
            ->add('date', DateType::class, [
            ])

            ->add('crear_evento', SubmitType::class,
                ['label' => 'Crear evento',
                    'attr' => ['class' => 'btn-upload']
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Event'
        ]);
    }
}
<?php
// src/AppBundle/Form/UserType.php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PerfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('photo', VichFileType::class, [
//                'required' => true,
//                'label' => false,
//            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Incluye algo sobre tí',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Escribe aquí una biografía',
                    'style' => 'width:100%'
                )
            ])
            ->add('save', SubmitType::class, ['label' => 'Guardar']);
    }

}



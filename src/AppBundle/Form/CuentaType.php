<?php
// src/AppBundle/Form/UserType.php
namespace AppBundle\Form;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CuentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'label' => false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Nombre completo',
                    'style' => 'width:100%'
                )
            ])
            ->add('username', TextType::class,[
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Nombre usuario',
                    'style' => 'width:100%'
                )])
            ->add('email', EmailType::class,[
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Email',
                    'style' => 'width:100%'
                )
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Contraseña',
                        'style' => 'width:100%'
                    )
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Repetir contraseña',
                        'style' => 'width:100%'
                    )],
            ])
            ->add('save', SubmitType::class, ['label' => 'Guardar'])
        ;
    }

}



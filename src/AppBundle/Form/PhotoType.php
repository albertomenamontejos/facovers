<?php
// src/AppBundle/Form/UserType.php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vich\UploaderBundle\Form\Type\VichFileType;
class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photoFile', VichFileType::class,[
                'required' => true,
                'label' => false,
                'download_link'=> false,
                'allow_delete' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Subir foto'])
        ;
    }

}
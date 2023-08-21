<?php

namespace App\Form;

use App\Entity\Messenger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessengerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('FromUser',EmailType::class,[
                'label'=>'From',
                'attr' =>['class' =>'form-control']
            ])
            ->add('ToUsers',EmailType::class,[
                'label'=>'To',
                'attr' =>['class' =>'form-control']
            ])
            ->add('title',TextType::class,[
                'label'=>'title',
                'attr' =>['class' =>'form-control']
            ])
            ->add('description',TextareaType::class,[
                'label'=>'description',
                'attr' =>['class' =>'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messenger::class,
        ]);
    }
}

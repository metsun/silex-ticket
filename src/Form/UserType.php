<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
	        ->add('username', 'text', array(
                'label' => 'Kullanıcı Adı',
                ))
            ->add('password', 'password', array(
                'label' => 'Şifre',
                ))
            ->add('roles', 'choice', array(
                'choices'  => array('ROLE_USER', 'ROLE_ADMIN'),
                ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Entity\User',
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
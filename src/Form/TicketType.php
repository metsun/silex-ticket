<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Form\Extensions\Doctrine\Bridge;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
	        ->add('baslik', 'text', array(
                'label' => 'Ticket Başlığı',
                ))
/*            ->add('kategori', EntityType::class, array(
                'class' => 'Entity:Kategori',
                'choice_label' => 'isim',
            ))*/
            ->add('kategori', 'entity', 
                array('class'=>'Entity\Kategori', 'property'=>'isim', 
            ))
            ->add('onem', 'choice', array(
                 'label' => 'Önemi',
                 'choices' => array( "Düşük", "Orta", "Yüksek" ),
                ))
            ->add('icerik', 'textarea', array(
                'label' => 'Mesaj',
                ))
            ->add('filepath', 'file', array(
                'label' => 'Ek yükle'
                ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Entity\Ticket',
        ));
    }

    public function getName()
    {
        return 'ticket';
    }
}
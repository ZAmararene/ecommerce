<?php

namespace App\Form\Type;

use App\Form\DataTransformer\CentimesTranfsormer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['devide'] === false) {
            return;
        }

        $builder->addModelTransformer(new CentimesTranfsormer());
    }

    public function getParent()
    {
        // le champ de formulaire qu'on crée va s'inscrire dans la famille du NumberType
        // dès que on utilise un champ du formumaire PricesType, on s'inspire de toutes les options de NumberType
        return NumberType::class;
    }

    // définir les options du champ, pas besoin de mettre "label" par exemple car il est déjà définit dans NumberType
    public function configureOptions(OptionsResolver $resolver)
    {
        // est ce que on veut divier ou pas le contenu du champ ex: prix
        $resolver->setDefaults([
            'divide' => true
        ]);
    }
}

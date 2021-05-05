<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CentimesTranfsormer implements DataTransformerInterface
{
    // transform agit avant d'afficher la valeur dans le formulaire
    public function transform($value)
    {
        if ($value === null) {
            return;
        }

        return $value / 100;
    }

    // reverseTransform agit au moment où on a soumit une valeur dans le formaulaire
    public function reverseTransform($value)
    {
        if ($value === null) {
            return;
        }

        return $value * 100;
    }
}

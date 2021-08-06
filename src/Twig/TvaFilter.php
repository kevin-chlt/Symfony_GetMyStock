<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TvaFilter extends AbstractExtension
{

    # French rate of value-added taxes
    private const TVA_RATE_NORMAL = 1.20;
    private const TVA_RATE_REDUCED = 1.05;

    public function getFunctions () : array
    {
        return [
            new TwigFunction('addTva', [$this, 'getTvaOnPrice'])
        ];
    }

    # Check the product category and return the price with the right tax
    public function getTvaOnPrice (float $price, string $category) : float
    {
        $reducedTvaCategory = ['Alimentation', 'Enfant'];

        if(in_array($category, $reducedTvaCategory)){
            return $price * self::TVA_RATE_REDUCED;
        }
        return $price * self::TVA_RATE_NORMAL;
    }
}
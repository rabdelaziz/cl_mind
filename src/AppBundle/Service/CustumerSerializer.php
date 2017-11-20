<?php

namespace AppBundle\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CustumerSerializer extends Serializer
{
   

   public function getNormalizers()
   {

        foreach ($this->normalizers as $normalizer) {
            if ($normalizer instanceof ObjectNormalizer) {
                return $normalizer;
            }
        }

        return null;
    }

}

<?php

/**
 * Product data object / value object.
 */
class Product
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $ean;

    /**
     * @var string
     */
    public $name;

    /**
     * @param string $ean
     * @param string $name
     */
    public function __construct($ean='', $name='')
    {
        if ($ean !== '' && $name !== '')
        {
            $this->id   = $ean;
            $this->ean  = $ean;
            $this->name = $name;
        }
    }

}

/**
 * Representing empty product / null product
 */
class NullProduct extends Product { }

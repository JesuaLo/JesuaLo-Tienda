<?php

namespace App\Generico;

use App\Models\Articulo;

class Linea {
    private Articulo $articulo;
    private int $cantidad;

    public function __construct(Articulo $articulo, int $cantidad = 1) {
        $this->articulo = $articulo;
        $this->cantidad = $cantidad;
    }

    public function getArticulo(){
        return $this->articulo;
    }

    public function getCantidad(){
        return $this->cantidad;
    }

    public function incrCantidad($cantidad = 1){
        $this->cantidad += $cantidad;
    }

    public function decrCantidad($cantidad = 1){
        $this->cantidad -= $cantidad;
    }
}

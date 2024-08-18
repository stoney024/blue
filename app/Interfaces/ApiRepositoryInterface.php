<?php

namespace App\Interfaces;

interface ApiRepositoryInterface
{

    /**
     * Returns external data
     * @return string
     */
    public function fetch();

}

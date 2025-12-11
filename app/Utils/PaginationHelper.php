<?php

namespace App\Utils;

class PaginationHelper
{
    /**
     * Retorna a página válida para a lista
     *
     * @param  int  $requestedPage  Página solicitada
     * @param  int  $totalItems  Total de itens
     * @param  int  $perPage  Itens por página
     * @return int Página válida
     */
    public static function clampPage(int $requestedPage, int $totalItems, int $perPage): int
    {
        $lastPage = (int) ceil($totalItems / $perPage);

        return min($lastPage, $requestedPage);
    }
}

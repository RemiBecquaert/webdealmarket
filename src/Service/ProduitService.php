<?php

namespace App\Service;

use App\Entity\MarqueProduit;
use App\Entity\CategorieProduit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Component\Pager\PaginatorInterface;


class ProduitService{

    public function __construct(private RequestStack $requestStack, private ProduitRepository $produitRepo, private PaginatorInterface $paginator){

    }

    public function getPaginatedProduct(?CategorieProduit $categorie = null, ?MarqueProduit $marque = null){
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = 6;
        $produitQuery = $this->produitRepo->findForPagination($categorie, $marque); 
        return $this->paginator->paginate($produitQuery, $page, $limit);
    }

}
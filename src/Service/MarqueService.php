<?php

namespace App\Service;

use App\Entity\MarqueProduit;
use App\Repository\MarqueProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Component\Pager\PaginatorInterface;


class MarqueService{

    public function __construct(private RequestStack $requestStack, private MarqueProduitRepository $marqueRepo, private PaginatorInterface $paginator){

    }

    public function getPaginatedBrands(){
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $marqueQuery = $this->marqueRepo->findForPagination(); 
        return $this->paginator->paginate($marqueQuery, $page, $limit);
    }

}
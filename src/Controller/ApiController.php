<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/listeRegions", name="listeRegions")
     */
    public function listeRegion(SerializerInterface $serializer)
    {
        $mesRegions = file_get_contents('https://geo.api.gouv.fr/regions');
        // $mesRegionsTab = $serializer->decode($mesRegions, 'json');
        // $mesRegionsObj = $serializer->denormalize($mesRegionsTab, 'App\Entity\Region[]');
        $mesRegions = $serializer->deserialize($mesRegions, 'App\Entity\Region[]', 'json');
        // dump($mesRegionsObj); die();
        return $this->render('api/index.html.twig', [
            'mesRegions' => $mesRegions
        ]);
    }

    /**
     * @Route("/listeDepsParRegion", name="listeDepsParRegion")
     */
    public function listeDepsParRegion(Request $request, SerializerInterface $serializer)
    {
        // Je recupère la région sélectionnée ds le formulaire
        $codeRegion = $request->query->get('region'); 
        //Je recupère les regions
        $mesRegions = file_get_contents('https://geo.api.gouv.fr/regions');
        $mesRegions = $serializer->deserialize($mesRegions, 'App\Entity\Region[]', 'json');

        // Récupération des départements
        if($codeRegion == null || $codeRegion == 'Toutes') {
            $mesDeps = file_get_contents('https://geo.api.gouv.fr/departements');
        } else {
            $mesDeps = file_get_contents('https://geo.api.gouv.fr/regions/'.$codeRegion.'/departements');
        }
        // Décodage du format json en tableau
        $mesDeps = $serializer->decode($mesDeps, 'json');

        return $this->render('api/listDepsParRegion.html.twig', [
            'mesRegions' => $mesRegions,
            'mesDeps' => $mesDeps
        ]);
    }
}

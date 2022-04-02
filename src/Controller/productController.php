<?php
  //le C
namespace App\Controller;

use App\Entity\Product;
use App\Form\ProdType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class productController extends AbstractController
{
    #[route('/addProd', name: 'addprod', methods: ['GET','POST'])]
    public function addProd(ProductRepository $productRepository, Request $request)
    {

        $prod = new Product();
        $prodForm = $this->createForm(ProdType::class, $prod); //on cree le patron du formulaire on lui dit ca va dans newProduct
        $prodForm->handleRequest($request);//permet de pouvoir recuperer le get et le post(envoie)
        if($prodForm->isSubmitted() && $prodForm->isValid()){ //Si formulaire valide alors on renvoie
            $img = $prodForm->get('img')->getData();//on recupere une image
            $imgName = md5(uniqid()) . '.' . $img->guessExtension();//crée un id unique de l'image sous format de data

            $img->move($this->getParameter('uploadDirectory'),$imgName);//on place l'image recupere dans public/uploadDirectory grace au parametre creer dans le service.yaml
            $prod
                ->setImg($imgName)//ajoute l'id unique $imgName dans la variable $prod
                ->setActive(true); //on met le true en dur


            $productRepository->add($prod); //on ajoute dans le repository le form valider(prepa, envoie, création requete)
            //dd($prodForm, $prod);
            return $this->redirectToRoute('showProds'); //retourne la fonction showprod qui render vers une autre page
        }

        return $this->render('prod/addProd.html.twig',[ //render avec la route du fichier
            'prodForm' => $prodForm->createView() //permet de créer la vue
        ]);
    }

    #[Route('/prods', name: 'showProds', methods: ['GET','POST'])] #Donne la route le nom et la method
    public function showprod(ProductRepository $productRepository) #productRepository (m)
    {
        //$prods = $productRepository->findAll();#Il va tout chercher (c)

            $prodsActive = $productRepository->findBy(['active'=>true]);
            $prodsUnactived = $productRepository->findBy(['active'=>false]);

            return $this->render('/Prod/prods.html.twig',[
                'prodsActive'=>$prodsActive,
                'prodsUnactived' =>$prodsUnactived,
            ]);
    }
    #[Route('/updateProd/{id}', name: 'updateProd',methods: ['GET','POST'])]
    public function updateProd(ProductRepository $productRepository, $id, Request $request){
        $prod = $productRepository->findOneBy(['id' => $id]);//on utilise la methode findoneBY pour recuperer l'entité correpsondant a l'id
        //on crée le patron du form qui est en relation avec $prod
        $prodForm = $this->createForm(ProdType::class, $prod);
        //on prépare l'envoie en recupérant le get et le post
        $prodForm->handleRequest($request);
        //si le form est envoye et valide
        if ($prodForm->isSubmitted() &&$prodForm->isValid()){
            $img = $prodForm->get('img')->getData();//on recupere une image
            $imgName = md5(uniqid()) . '.' . $img->guessExtension();//crée un id unique de l'image sous format de data

            $img->move($this->getParameter('uploadDirectory'),$imgName);//on place l'image recupere dans public/uploadDirectory grace au parametre creer dans le service.yaml
            $prod->setImg($imgName);
            //on lui demande d'inserer les données
            $productRepository->add($prod);
            //retourne la fonction qui envoie sur la page 'showProduct'
            return $this->redirectToRoute('showProds');
        }
        return $this->render('Prod/updateProd.html.twig',[
            'prodForm' => $prodForm->createView()
        ]);

    }

    #[Route('/changeActiveProd/{id}', name: 'changeActivProd', methods: ['GET', 'POST'])]//Rajoute l'id à l'adresse url avec {id}(wild card)
    public function changeActiveProd(ProductRepository $productRepository, $id){
        $prod = $productRepository->findOneBy(['id' => $id]);//on utilise la methode findoneBY pour recuperer l'entité correpsondant a l'id

        if ($prod->getActive()=== true) {
            //si $prod et ture on passe en false
            $prod->setActive(false);
        }else //si false on passe en true
            ($prod->setActive()===false);

        $productRepository->add($prod);
        return $this->redirectToRoute('showProds');//Renvoie à la fonction showprod
    }

    #[Route('/deleteUnactivedProd/{id}', name: 'deleteUnactivedProd', methods: ['GET','POST'])]
    public function deleteUnactivedProduct(ProductRepository $productRepository,$id)
    {
        $prod = $productRepository->findOneBy(['id'=>$id]);
        if($prod->getActive() == false) {
            $productRepository->remove($prod);
        }
        return $this->redirectToRoute('showProds');

    }

    #[Route('/showProduct/{id}', name: 'showProduct', methods: ['GET', 'POST'])]
    public function showProduct(ProductRepository $productRepository, $id){
        $prod = $productRepository->findOneBy(['id' => $id]);//on selectionne un élément par son Id

        return $this->render('/Prod/showProduct.html.twig',[
            'prod'=> $prod
        ]);
    }
}

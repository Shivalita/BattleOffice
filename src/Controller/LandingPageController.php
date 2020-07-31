<?php

namespace App\Controller;

// use App\Entity\Client;
use App\Entity\Order;
// use App\Entity\Shipping;

use App\Entity\Product;
use App\Entity\Shipping;
use App\Form\ClientType;
use App\Form\ShippingType;
use App\Form\ProductType;
use App\Form\Entity;
use App\Form\OrderType;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// use Symfony\Component\Form\FormEvent;
// use App\Manager\OrderManager;
// use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LandingPageController extends AbstractController
{
    /**
     * @Route("/", name="landing_page")
     * @throws \Exception
     */
    
    public function index(Request $request)
    {
        $order = new Order();
        

        // Form creation based on OrderType form in order to hydrate $Order object
        $form = $this->createForm(OrderType::class, $order);

        //hydration of $order
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            
            $entityManager = $this->getDoctrine()->getManager(); 
            $entityManager->persist($order);
            $entityManager->flush();
           
             // store orderApi response in a variable as a value has to be stored in a variable.
             $response = $this->orderApi($order);


            //conversion reponse Api from Json to array
             $apiResponseContent = $response->toArray();

             //IdApi sent to DataBase
             $idApi = $apiResponseContent['order_id'];
             $order->setApiId($idApi);
             $entityManager->persist($order);
             $entityManager->flush();
            
            return $this->redirectToRoute('payment', 
            [
                'id' => $order->getId(),
              
            ]
            
            );
        };
        
        return $this->render('landing_page/index_new.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/confirmation/{id}", name="confirmation")
     */
    public function confirmation(Request $request)
    {
        $idApi = $request->get('id');
    
        $updateResponse = $this->updateApi($idApi);

        return $this->render('landing_page/confirmation.html.twig', [
        ]);
    }


    public function orderApi(Order $order){

        // Create 
        $client = HttpClient::create();
        $response = $client->request('POST', 'https://api-commerce.simplon-roanne.com/order', [


            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX',
                'Content-Type' => 'application/json',
            ],


            'json' => [
                'order' => [
                    'id' => $order->getId(),
                    'product' => $order->getProduct()->getName(),
                    'payment_method' => 'stripe',
                    'status' => 'WAITING',
                    'client' => [
                        'firstname' => $order->getClient()->getFirstName(),
                        'lastname' => $order->getClient()->getLastName(),
                        'email' => $order->getClient()->getEmail(),
                    ],
                    'addresses' => [
                        'billing' => [
                            'address_line1'=> $order->getClient()->getAddress(),
                            'address_line2'=> $order->getClient()->getAdditionalAddress(),
                            'city'=> $order->getClient()->getCity(),
                            'zipcode'=> $order->getClient()->getPostcode(),
                            'country'=> $order->getClient()->getCountry()->getName(),
                            'phone' => $order->getClient()->getPhone(),
                        ],

                        'shipping' => [
                            'address_line1'=> $order->getClient()->getShipping()->getAddress(),
                            'address_line2'=> $order->getClient()->getShipping()->getAdditionalAddress(),
                            'city'=> $order->getClient()->getShipping()->getCity(),
                            'zipcode'=> $order->getClient()->getShipping()->getPostcode(),
                            'country'=> $order->getClient()->getShipping()->getCountry()->getName(),
                            'phone' => $order->getClient()->getShipping()->getPhone(),
                        ]
                    ]
                 ]

            ]]);
           
    //    $this->updateApi($response);
       return $response;
    }


    public function updateApi($idApi){

        $client = HttpClient::create();
        $response = $client->request('POST', 'https://api-commerce.simplon-roanne.com/order/'.$idApi.'/status', [


            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX',
                'Content-Type' => 'application/json',
            ],

            'json' => [
                'status' => 'PAID',
            ]
        ]);

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]
    
    }


    //
//     public function ConvertToJson($order){

//         //converting object into JSON
//         $encoders = new JsonEncoder();
//         $normalizers = new ObjectNormalizer();
        
//         // instanciatin du serializer avec deux parametrex
//         $serializer = new Serializer([$normalizers], [$encoders]);
//         $json = $serializer->serialize($order ,'json');
//         // dd($json);

//         return $json;
      
//   }

}

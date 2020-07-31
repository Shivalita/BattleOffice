<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PaymentType;


class PaymentController extends AbstractController
{
    /**
     * @Route("/payment/{id}", name="payment")
     */
    public function newPayment(Request $request, Order $order)
    {

        $payment = new Payment();
        
        // Form creation based on OrderType form in order to hydrate $Order object
        $form = $this->createForm(PaymentType::class, $payment);

        //hydration of $order
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager(); 
            $entityManager->persist($payment);
            $entityManager->flush();

            try{

           \Stripe\Stripe::setApiKey('sk_test_51H2HR8EP44dUvF8KP1L8SUrMXQgVCWcXIixTYQgEaKqnhi2Tr2DHpYR4kMFYHoXsmGFKRAOYEszmjBZEaz46DCVl009yIoGIn5');
           
           // Token is created using Stripe Checkout or Elements!
           // Get the payment token ID submitted by the form:
           $token = $request->request->get('stripeToken');
           $charge = \Stripe\PaymentIntent::create([
             'amount' => intval($order->getProduct()->getPrice())*100,
             'currency' => 'eur',
             'description' => 'Example charge',
             'source' => $token,
           ]);
          
           }
           catch(\Exception $e){
               dd("problem", $e->getMessage());
                   }
            
            return $this->redirectToRoute('confirmation', [
                'id' => $order->getApiId(),
            ]);
        }

        return $this->render('payment/index.html.twig', [
            'form' => $form->createView(),
            'email' => $order->getClient()->getEmail(),
            'amount' => $order->getProduct()->getPrice(),
        ]);
    }
}
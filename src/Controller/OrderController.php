<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/order", methods={"GET"}, name="order_list")
     */
    public function list()
    {
        $userId = $this->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository(Order::class)->findByUserId($userId);

        $statusCode = count($orders) ? Response::HTTP_OK : Response::HTTP_NO_CONTENT;

        return $this->json([
            'data' => $orders,
            'message' => 'ok',
        ], $statusCode, [], [
            AbstractNormalizer::GROUPS => 'normal'
        ]);
    }

    /**
     * @Route("/api/order/{id}", methods={"GET"}, name="order_get")
     */
    public function detail(int $id)
    {
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->findOneBy([
            'id' => $id,
            'user' => $currentUser->getId()
        ]);

        if (empty($order)) {
            return $this->json([
                'data' => [],
                'message' => 'Order couldn\'t found',
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'data' => $order,
            'message' => 'ok',
        ], Response::HTTP_OK, [], [
            AbstractNormalizer::GROUPS => 'normal'
        ]);
    }

    /**
     * @Route("/api/order/{id}", methods={"PUT"}, name="order_update")
     */
    public function update(int $id, Request $request)
    {
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $order = $orderRepository->findOneBy([
            'id' => $id,
            'user' => $currentUser->getId()
        ]);

        if (empty($order)) {
            return $this->json([
                'data' => [],
                'message' => 'Order couldn\'t found',
            ], Response::HTTP_NOT_FOUND);
        }

        if (!empty($order->getShippingDate())) {
            return $this->json([
                'data' => [],
                'message' => 'Order has shipped',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $orderRepository->update($id, [
            'quantity' => $request->get('quantity'),
            'address' => $request->get('address')
        ]);

        return $this->json([
            'data' => [],
            'message' => 'ok',
        ]);
    }

    /**
     * @Route("/api/order", methods={"POST"}, name="order_store")
     */
    public function store(Request $request)
    {
        $productId = $request->get('product_id');
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->find($productId);

        if (empty($product)) {
            return $this->json([
                'data' => [],
                'message' => 'Product couldn\'t found',
            ], Response::HTTP_NOT_FOUND);
        }

        $bytes = random_bytes(7);
        $orderCode = bin2hex($bytes);

        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $order = $orderRepository->insert([
            'order_code' => $orderCode,
            'product' => $product,
            'quantity' => $request->get('quantity'),
            'address' => $request->get('address'),
            'user' => $this->getUser(),
        ]);

        return $this->json([
            'data' => $order,
            'message' => 'ok',
        ], Response::HTTP_CREATED, [], [
            AbstractNormalizer::GROUPS => 'normal'
        ]);
    }
}

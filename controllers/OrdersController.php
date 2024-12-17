<?php

namespace app\controllers;
use app\models\Orders;
use yii\web\Response;
use app\models\Users;
use app\models\Cart;

use Yii;

class OrdersController extends RequestController
{
    public $modelClass = 'app\models\Orders';

    //--creating a new order-------------------------------------------------------------------------------------------//
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $data = Yii::$app->request->post(); 
            $cartItems = Cart::findAll(['user_id' => $user->id_user, 'order_id' => null]);
        
            if (empty($cartItems)) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Корзина пуста'
                    ]
                ]);
            }
            
            $order = new Orders();
            $order->load($data, '');

            if (!$order->validate()) {
                return $this->validation($order); 
            }

            $order->save();

            if (!$order->save()) {
                return $this->send(500, $order->getErrors()); 
            }

            $cartItems = Cart::findAll(['user_id' => $user->id_user, 'order_id' => null]);

            foreach ($cartItems as $cartItem) {
                $cartItem->order_id = $order->id_order;
                $cartItem->save();
            }

            return $this->send(200, [
                'data' => [
                    'status' => 'ok'
                ]
            ]);   
        } else {
            return $this->send(401, [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]);
        }
    }   
    //-----------------------------------------------------------------------------------------------------------------//

    //--displaying order data-----------------------------------------------------------------------------------------------------------------//
    public function actionView()
    {
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $cartItems = Cart::find()->where(['user_id' => $user->id])->andWhere(['not', ['order_id' => null]])->all();
            
            if (empty($cartItems)) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Заказы отсутствуют'
                    ]
                ]);
            }

            $orderIds = array_column($cartItems, 'order_id');
            $orders = Orders::find()->where(['id_order' => $orderIds])->all();

            if (empty($orders)) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Orders not found'
                    ]
                ]);
            }

            $orderData = [];

            foreach ($orders as $order) {
                $orderData[] = [
                    'id' => $order->id_order,
                    'phone' => $order->phone,
                    'date' => $order->date_desired,
                    'comments'=>$order->comments,
                ];
            }

            return $this->send(200, [
                'data' => [
                    'orders' => $orderData
                ]
            ]);

        } else {
            return $this->send(401, [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]);
        }
    }
    //----------------------------------------------------------------------------------------------------------------------------------------//
}
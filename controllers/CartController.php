<?php

namespace app\controllers;
use app\models\Cart;
use app\models\Services;
use yii\web\Response;
use app\models\Users;

use Yii;

class CartController extends RequestController
{
    public $modelClass = 'app\models\Cart';

    //--creating a new cart-------------------------------------------------------------------------------------------//
    public function actionNew()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $request = Yii::$app->request->post();
            
            if (empty($request['service_id'])&& empty($request['count'])) {
                return $this->send(422, [
                    'error' => [
                        'code' => 422,
                        'message' => 'Validation error',
                        'error' => 'Не может быть пустым'
                    ]
                ]);
            }

            $serviceId = $request['service_id'];
            $count = $request['count'];
            $service = Services::findOne($serviceId);

            if (!$service) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Service not found'
                    ]
                ]);
            }
    
            $cart = Cart::find()->where(['user_id' => $user->id_user, 'service_id' => $serviceId])->one();

            if ($cart === null) {
                $cart = new Cart();
                $cart->user_id = $user->id_user;
                $cart->service_id = $serviceId;
                $cart->count = $count;
            } else {

                if ($cart->order_id !== null) {
                    $newCartItem = new Cart();
                    $newCartItem->user_id = $user->id_user;
                    $newCartItem->service_id = $serviceId;
                    $newCartItem->count = $count;

                    if ($newCartItem->save()) {
                        return $this->send(200, [
                            'data' => [
                                'status' => 'ok',
                                'id' => $newCartItem->id_cart,
                            ]
                        ]);
                    } 
                } else {
                    $cart->count += $count;
                }
            }
    
            if ($cart->save()) {
                return $this->send(200,[
                    'data' => [
                        'status' => 'ok',
                        'id' => $cart->id_cart,
                    ],
                ]);
            }  

        } else{   
            return $this->send(401, [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]); 
        }
    }  
    //---------------------------------------------------------------------------------------------------------------//

    //--displaying cart data-----------------------------------------------------------------------------------------------------------//
    public function actionItems()
    {
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $cartItems = Cart::find()->where(['user_id' => $user->id_user])->andWhere(['order_id' => null])->all();

            if (empty($cartItems)) {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Корзина пуста'
                    ]
                ]);
            }

            $cartData = [];

            foreach ($cartItems as $item) {
                $service = Services::findOne($item->service_id); 

                if ($service) { 
                    $totalPrice = $item->count * $service->price; 

                    $cartData[] = [
                        'title' => $service->title,
                        'count' => $item->count,
                        'total_price' => $totalPrice,
                    ];
                }
            }

            return [
                'data' => [
                    'cart' => $cartData,
                ],
            ];

        } else {
            return $this->send(401, [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]); 
        }
    }
    //---------------------------------------------------------------------------------------------------------------------------------//
    
    //--delete cart data-------------------------------------------------------------------------------------------------//
    public function actionDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id_cart = Yii::$app->request->get('id_cart');
        $user = Users::getToken();

        if ($user && $user->isAuthorized()) {
            $cartItem = Cart::find()->where(['id_cart' => $id_cart, 'user_id' =>$user->id_user])->one();

            if ($cartItem) {
                if ($cartItem->delete()) {
                    return $this->send(200,[
                        'data' => [
                            'status' => 'ok'
                    
                        ],
                    ]);
                } 
            } else {
                return $this->send(404, [
                    'error' => [
                        'code' => 404,
                        'message' => 'Данная услуга в корзине отсутствует'
                    ]
                ]); 
            }
        } else {
            return $this->send(401, [
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]); 
        }
    }
    //------------------------------------------------------------------------------------------------------------------//
}
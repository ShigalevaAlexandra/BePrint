<?php

namespace app\controllers;
use app\models\Services;
use app\models\Users;
use yii\web\UploadedFile;

use Yii;

class ServicesController extends RequestController
{
    public $modelClass = 'app\models\Services';

    //--creating a new service-------------------------------------------------------------------------------------------------------------//
    public function actionCreate()
    {
        $user = Users::getToken();
        $post_data=Yii::$app->request->post();

        if (!($user && $user->isAuthorized() && $user->isAdmin())) {
            return $this->send(403, ['error' => ['message' => 'Forbidden']]);
        }

        $post_data=Yii::$app->request->post();
        $service=new Services();
        $service->load($post_data, '');
        $service->photo_service=UploadedFile::getInstanceByName('photo_service');
    
        if ($service->photo_service) { 
            $hash=hash('sha256', $service->photo_service->baseName) . '.' . $service -> photo_service->extension;
            $service->photo_service->saveAs(\Yii::$app->basePath. '/api/assets/upload/' . $hash);
            $service->photo_service=$hash;
        } else {
            return $this->send(400, ['error' => ['message' => 'Файл не загружен или данная фотография уже имеется на сервере']]);
        }   

        if (!$service->validate()) {
            return $this->validation($service);
        }

        $service->save(false);
        
        return $this->send(200,[
            'data' => [
                'status' => 'ok',
                'id' => $service->id_service,

            ],
        ]);
    }
    //-------------------------------------------------------------------------------------------------------------------------------------//

    //--displaying service data------------------------------------------//
    public function actionView() {
        $services = Services::find()->all();
            
        if (empty($services)) {
            return $this->send(404, [
                'error' => [
                    'code' => 404,
                    'message' => 'Service not found'
                ]
             ]);
        }

        $serviceList = [];
        
        foreach ($services as $service) {
            $serviceList[] = [
                'id' => $service->id_service,
                'title' => $service->title,
                'typesServices' => $service->typesServices->title,
                'photo_service' => $service->photo_service, 
                'description' => $service->description,
                'price' => $service->price,
                'date_added' => $service->date_added,
            ];
        }
        
        return $this->send(200, [
            'data' => [
                'services' => $serviceList
            ]
        ]);
    }
    //---------------------------------------------------------------//

    //--searching service------------------------------------------------------------------------------------------------------------------//
    public function actionSearch()
    {
        $query = Yii::$app->request->get('typesServices');
        $services = Services::find()->joinWith('typesServices')->where(['like', 'types_services.title', $query])->all() ;
        
        if (empty($services)) {
            return $this->send(404, [
                'error' => [
                    'code' => 404,
                    'message' => 'Service not found'
                ]
            ]);
        }

        $result = [];

        foreach ($services as $service) {
            $result[] = [
                'id' => $service->id_service,
                'title' => $service->title,
                'typesServices' => $service->typesServices->title,
                'photo_service' => $service->photo_service, 
                'description' => $service->description,
                'price' => $service->price,
                'date_added' => $service->date_added,
            ];
        }

        return $this->send(200, [
            'data' => [
                'services' => $result
            ]
        ]);
    }
    //-------------------------------------------------------------------------------------------------------------------------------------//

    //--editing service data--------------------------------------------------------------//
    public function actionEdit()
    {
        $id_service = Yii::$app->request->get('id_service');
        $user = Users::getToken();
        
        if (!($user && $user->isAuthorized() && $user->isAdmin())) {
            return $this->send(403, ['error' => ['message' => 'Forbidden']]);
        }
        
        $data = Yii::$app->request->post();
        $service = Services::findOne($id_service);
        
        if (!$service) {
            return $this->send(404, [
                'error' => [
                    'code' => 404,
                    'message' => 'Service not found'
                ]
            ]);
        }

        if (isset($data['title'])) {
            $service->title = $data['title'];
        }
                
        if (isset($data['type_id'])) {
            $service->type_id = $data['type_id'];
        }
            
        if (isset($data['price'])) {
            $service->price = $data['price'];
        }
            
        if (isset($data['description'])) {
            $service->description = $data['description'];
        }

        if ($service->validate() && $service->save()) {
            return $this->send(200, [
                'data' => [
                    'status' => 'ok'
                ]
            ]);
        }

        return $this->validation($service);
    }
    //-----------------------------------------------------------------------------------//
}

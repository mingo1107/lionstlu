<?php

namespace backend\controllers;


use ball\api\ResponseCode;
use ball\file\aws\S3Manager;
use ball\file\Uploader;
use ball\filter\RequestFilter;
use ball\helper\File;
use ball\util\FileUtil;
use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class UploadController extends BackendController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ],
            [
                'class' => RequestFilter::class,
                'allow' => [
                    "index" => [
                        "method" => "post",
                        "params" => ["param_name", "category", "current"]
                    ],
                    "normal-files" => [
                        "method" => "post",
                        "params" => ["param_name", "category"]
                    ],
                    "base64" => [
                        "method" => "get",
                        "params" => ["url"]
                    ],
                    "delete" => [
                        "method" => "post",
                        "params" => ["category", "name"]
                    ]
                ]
            ]
        ]);
    }

    public function actionIndex()
    {
        $auto = empty(Yii::$app->request->post('auto')) ? false : true;
        $field = Yii::$app->request->post('param_name');
        $category = Yii::$app->request->post('category');
        $current = Yii::$app->request->post('current');


//        $bak = Yii::$app->request->post('bak');
        $uploadDir = yii::getAlias('@rootPath') . '/backend/web/upload' . $category . "/";
//        $uploadUrl = Url::base(true) . '/upload' . $category . "/";
        $uploadUrl = File::fs($category, '');

        $handler = new Uploader([
            //'accept_file_types' => '/\.(gif|jpe?g|png)$/i',
            'upload_url' => $uploadUrl,
            'upload_dir' => $uploadDir,
            'param_name' => $field,
            'image_versions' => Uploader::$singleImageVersions,
            'print_response' => false,
            'auto' => $auto
        ]);

        $response = $handler->get_response();
        if (isset($response[$field])) {
//            $s3Manager = new S3Manager();
            $path = pathinfo($response[$field][0]->name);
            $remotePath = sprintf("/upload%s/", $category);
            foreach (Uploader::$singleImageVersions as $version => $data) {
                $dir = $uploadDir;
                if ($version === '') {
                    $fileName = $response[$field][0]->name;
                } else {
                    $dir = $uploadDir . $version . "/";
                    $fileName = $path['filename'] . "_" . $version . "." . $path['extension'];
                }
                $response[$field][0]->s3Url = File::s3($category, $fileName);
//                $s3Manager->upload(StringUtil::replaceExtension($dir . $response[$field][0]->name, "png"),
//                    $remotePath . $fileName,
//                    false);
//                $s3Manager->upload($dir . $response[$field][0]->name,
//                    $remotePath . $fileName,
//                    false);
//                unlink($dir . $response[$field][0]->name);
//                if (!empty($current)) {
//                    $s3Manager->delete(File::img($category, $current, $version), false);
//                }
            }
        }
        return $response;
    }

    public function actionNormalFiles()
    {
        $auto = empty(Yii::$app->request->post('auto')) ? false : true;
        $field = Yii::$app->request->post('param_name');
        $category = Yii::$app->request->post('category');

        $uploadDir = yii::getAlias('@rootPath') . '/backend/web/upload' . $category . "/";
        $uploadUrl = File::fs($category, '');

        $handler = new Uploader([
            //'accept_file_types' => '/\.(gif|jpe?g|png)$/i',
            'upload_url' => $uploadUrl,
            'upload_dir' => $uploadDir,
            'param_name' => $field,
            'image_versions' => Uploader::$singleImageVersions,
            'print_response' => false,
            'auto' => $auto
        ]);

        $response = $handler->get_response();
        if (isset($response[$field])) {
            $s3Manager = new S3Manager();
            $path = pathinfo($response[$field][0]->name);
            $remotePath = sprintf("/upload%s/", $category);
            foreach (Uploader::$singleImageVersions as $version => $data) {
                $dir = $uploadDir;
                if ($version === '') {
                    $fileName = $response[$field][0]->name;
                } else {
                    $dir = $uploadDir . $version . "/";
                    $fileName = $path['filename'] . "_" . $version . "." . $path['extension'];
                }
                $s3Manager->upload($dir . $response[$field][0]->name,
                    $remotePath . $fileName,
                    false);
                unlink($dir . $response[$field][0]->name);
            }
        }
        return $response;
    }

    public function actionBase64()
    {
        $url = Yii::$app->request->get('url');
        return ['src' => FileUtil::base64image($url, pathinfo($url, PATHINFO_EXTENSION))];
    }

    public function actionDelete()
    {
        $category = Yii::$app->request->post('category');
        $name = Yii::$app->request->post('name');
        $s3Manager = new S3Manager();
        $path = sprintf("%s/%s", $category, $name);
        if ($s3Manager->delete($path, false)) {
            return ResponseCode::success();
        } else {
            return ResponseCode::errors(ResponseCode::ERROR_FAILED, 'An error occurred, please try again');
        }
    }
}
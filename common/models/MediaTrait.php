<?php

namespace common\models;

use ball\helper\File;
use Yii;

trait MediaTrait
{
    /**
     * @var array
     * [
     *   'dbColumn' => [
     *      'size' => size,
     *      'category' => 'category'
     *   ],
     *   'dbColumn2' => [
     *      'size' => size2,
     *      'category' => 'category2'
     *   ],
     * ]
     */
    public $mediaAttribute = [
        'media' => [
            'size' => 1,
            'category' => File::CATEGORY_UPLOAD,
        ]
    ];


    /**
     * @param $data
     * @param null $formName
     * @return bool
     * @throws \Exception
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            foreach ($this->mediaAttribute as $dbColumn => $formInfo) {
                $this->$dbColumn = $this->generate($dbColumn, $formInfo);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $attribute
     * @param string $property
     * @param int $index
     * @return string
     * @throws \Exception
     */
    public function getMediaInputName(string $attribute, string $property = 'src', int $index = 0)
    {
        if (isset($this->mediaAttribute[$attribute])) {
            return $attribute . "_$property" . "_$index";
        } else {
            throw new \Exception("Attribute '$attribute' is not set to media");
        }
    }

    /**
     * @param string $attribute
     * @return Media[]|Media
     */
    public function serializeMedia(string $attribute)
    {
        $data = json_decode($this->$attribute);
        if ($this->mediaAttribute[$attribute]['size'] == 1) {
            if (!empty($data) && !empty($data[0])) {
                return $data[0];
            } else {
                return new Media();
            }
        } else {
            $mediaList = [];
            for ($i = 0; $i < $this->mediaAttribute[$attribute]['size']; ++$i) {
                if (!empty($data[$i])) {
                    array_push($mediaList, $data[$i]);
                } else {
                    array_push($mediaList, new Media());
                }
            }
            return $mediaList;
        }
    }

    /**
     * @param $model
     * @param string $attribute
     * @param int $size
     * @return Media[]|Media
     */
    public static function serialize($model, string $attribute, int $size = 1)
    {
        $data = json_decode($model->$attribute);

        if ($size == 1) {
            if (!empty($data) && !empty($data[0])) {
                return $data[0];
            } else {
                return new Media();
            }
        } else {
            $mediaList = [];
            for ($i = 0; $i < $size; ++$i) {
                if (!empty($data[$i])) {
                    array_push($mediaList, $data[$i]);
                } else {
                    array_push($mediaList, new Media());
                }
            }
            return $mediaList;
        }
    }

    /**
     * @param $formName
     * @param $formInfo
     * @return string
     * @throws \Exception
     */
    private function generate($formName, $formInfo)
    {
        $img = [];
        $size = $formInfo['size'];
        $category = $formInfo['category'];

        for ($i = 0; $i < $size; ++$i) {
            if (!empty(Yii::$app->request->post($this->getMediaInputName($formName, "src", $i)))) {
                $media = new Media();
                $media->src = Yii::$app->request->post($this->getMediaInputName($formName, "src", $i));
                $media->link = Yii::$app->request->post($this->getMediaInputName($formName, "link", $i));
                $media->alt = Yii::$app->request->post($this->getMediaInputName($formName, "alt", $i));
                $path = File::img($category, $media->src);
                $imageSize = @getimagesize($path);
                if (is_array($imageSize)) {
                    $media->width = $imageSize[0];
                    $media->height = $imageSize[1];
                } else {
                    $media->width = null;
                    $media->height = null;
                }
                array_push($img, $media);
            }
        }

        return json_encode($img);
    }
}
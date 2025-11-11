<?php

namespace backend\widget;


use ball\helper\File;
use Exception;
use yii\base\Widget;

class Upload extends Widget
{
    public $category;
    public $auto = true;
    public $bak = false;
    private $accept;
    public $rwd = false;
    public $type = File::TYPE_FILE;

    public $id;
    public $wId;
    public $mId;

    public $name;
    public $wName;
    public $mName;

    private $fileId;
    private $wFileId;
    private $mFileId;

    public $current;
    public $wCurrent;
    public $mCurrent;

    private $img;
    private $wImg;
    private $mImg;

    private $path;
    private $wPath;
    private $mPath;

    public $dataUrl = '/upload/index';
    public $crop = false;
    public $cropRatio = 1;
    public $autoSubmit = false;
    public $autoRefresh = false;
    public $allowMultiple = false;
    public $showOriginFileOption = false;


    public function run()
    {
        if (!empty($this->current)) {
            try {
                $this->path = File::fs($this->category, $this->current);
                $this->img = File::s3($this->category, $this->current);
            } catch (Exception $e) {
                $this->path = "";
                $this->img = "";
            }
            if ($this->wCurrent != "") {
                try {
                    $this->wPath = File::fs($this->category, $this->wCurrent);
                    $this->wImg = File::s3($this->category, $this->wCurrent);
                } catch (Exception $e) {
                    $this->wPath = "";
                    $this->wImg = "";
                }
            }
            if ($this->mCurrent != "") {
                try {
                    $this->mPath = File::fs($this->category, $this->mCurrent);
                    $this->mImg = File::s3($this->category, $this->mCurrent);
                } catch (Exception $e) {
                    $this->mPath = "";
                    $this->mImg = "";
                }
            }

            $this->type = File::getType($this->current);
        } else {
            $this->current = "";
            $this->wCurrent = "";
            $this->mCurrent = "";
        }

        if ($this->wId == '') {
            $this->wId = 'w_' . $this->id;
        }

        if ($this->wName == '') {
            $this->wName = 'w_' . $this->name;
        }

        if ($this->mId == '') {
            $this->mId = 'm_' . $this->id;
        }

        if ($this->mName == '') {
            $this->mName = 'm_' . $this->name;
        }

        $this->fileId = $this->id . '-file';
        $this->wFileId = $this->wId . '-file';
        $this->mFileId = $this->mId . '-file';


        return $this->render('upload', [
            'id' => $this->id,
            'wId' => $this->wId,
            'mId' => $this->mId,
            'fileId' => $this->fileId,
            'wFileId' => $this->wFileId,
            'mFileId' => $this->mFileId,
            'name' => $this->name,
            'wName' => $this->wName,
            'mName' => $this->mName,
            'type' => $this->type,
            'auto' => $this->auto,
            'category' => $this->category,
            'accept' => $this->accept,
            'rwd' => $this->rwd,
            'current' => $this->current,
            'wCurrent' => $this->wCurrent,
            'mCurrent' => $this->mCurrent,
            'path' => $this->path,
            'wPath' => $this->wPath,
            'mPath' => $this->mPath,
            'img' => $this->img,
            'wImg' => $this->wImg,
            'mImg' => $this->mImg,
            'crop' => $this->crop,
            'dataUrl' => $this->dataUrl,
            'autoSubmit' => $this->autoSubmit,
            'autoRefresh' => $this->autoRefresh,
            'allowMultiple' => $this->allowMultiple,
            'showOriginFileOption' => $this->showOriginFileOption,
            'cropRatio' => $this->cropRatio
        ]);
    }
}
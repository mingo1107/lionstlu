<?php

namespace frontend\controllers;

use frontend\filters\MemberAccessFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * 受保護內容控制器
 * 
 * 處理需要會員權限的頁面：
 * - 學習中心
 * - 工具下載
 * - 直播
 * - 影片回放
 */
class ProtectedController extends FrontendController
{
    /**
     * 行為配置
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            // 會員權限過濾器
            'memberAccess' => [
                'class' => MemberAccessFilter::class,
            ],
        ]);
    }

    /**
     * 學習中心
     */
    public function actionLearningCenter()
    {
        return $this->render('learning-center', [
            'member' => \Yii::$app->user->identity,
        ]);
    }

    /**
     * 工具下載
     */
    public function actionDownload()
    {
        return $this->render('download', [
            'member' => \Yii::$app->user->identity,
        ]);
    }

    /**
     * 直播
     */
    public function actionLive()
    {
        return $this->render('live', [
            'member' => \Yii::$app->user->identity,
        ]);
    }

    /**
     * 影片回放
     */
    public function actionVideo()
    {
        return $this->render('video', [
            'member' => \Yii::$app->user->identity,
        ]);
    }
}


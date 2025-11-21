<?php

namespace frontend\filters;

use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

/**
 * 會員權限過濾器
 * 
 * 檢查會員是否有權限訪問受保護的內容
 * 條件：
 * 1. 已登入
 * 2. 已開通（validate = 1）
 * 3. 在會員期限內（period_start ~ period_end）
 * 
 * 使用方式：
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'memberAccess' => [
 *             'class' => MemberAccessFilter::class,
 *             'only' => ['learning-center', 'download', 'live', 'video'],
 *         ],
 *     ];
 * }
 * ```
 */
class MemberAccessFilter extends ActionFilter
{
    /**
     * 訪問被拒絕時的錯誤訊息
     * @var string
     */
    public $denyMessage = '您沒有權限訪問此內容';

    /**
     * 訪問被拒絕時的重定向 URL
     * 如果為 null，則拋出 403 錯誤
     * @var string|array|null
     */
    public $denyCallback;

    /**
     * 是否顯示詳細的拒絕原因
     * @var bool
     */
    public $showDetailedReason = true;

    /**
     * 在執行 action 之前檢查權限
     */
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            // 未登入，重定向到登入頁面
            Yii::$app->user->loginRequired();
            return false;
        }

        /** @var \common\models\MemberModel $member */
        $member = Yii::$app->user->identity;

        // 檢查會員是否已開通
        if (!$member->isValidated()) {
            return $this->denyAccess('會員尚未開通，請聯絡管理員開通會員權限');
        }

        // 檢查會員是否在有效期限內
        if (!$member->isInValidPeriod()) {
            $reason = $this->showDetailedReason ? $member->getAccessStatus() : '會員權限已過期或尚未生效';
            return $this->denyAccess($reason);
        }

        return true;
    }

    /**
     * 拒絕訪問
     * @param string $message
     * @return bool
     * @throws ForbiddenHttpException
     */
    protected function denyAccess($message)
    {
        if ($this->denyCallback !== null) {
            // 使用自定義回調處理
            call_user_func($this->denyCallback, $message);
            return false;
        }

        // 拋出 403 錯誤
        throw new ForbiddenHttpException($message);
    }
}


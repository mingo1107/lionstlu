<?php

namespace backend\controllers;


use ball\helper\HtmlHelper;
use ball\helper\Pagination;
use ball\helper\SQLHelper;
use common\models\AreaModel;
use common\models\MemberModel;
use Yii;
use yii\helpers\Html;

class MemberController extends BackendController
{
    protected $actionLabel = '會員';

    public function actionIndex()
    {
        $start = Pagination::getOffset();
        $search = SQLHelper::buildSearchQuery(['status', 'keyword', 'area_id', 'is_self_register']);
        $list = MemberModel::query($search, Pagination::PAGE_SIZE, $start);
        $count = MemberModel::count($search);
        $areaList = AreaModel::findAllForSelect();
        return $this->render('index', [
            'list' => $list,
            'start' => $start,
            'count' => $count,
            'areaList' => $areaList,
            'search' => $search
        ]);
    }

    public function actionCreate()
    {
        $model = new MemberModel(['scenario' => MemberModel::SCENARIO_CREATE]);
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('建立成功');
            }
            return $this->redirect(['option?id=' . $model->id]);
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    public function actionUpdate()
    {
        $id = intval(Yii::$app->request->get('id'));
        $model = MemberModel::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->redirect(['index']);
        }
        $model->scenario = MemberModel::SCENARIO_UPDATE;
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            if (!empty($model->errors)) {
                HtmlHelper::setError(Html::errorSummary($model));
            } else {
                HtmlHelper::setMessage('更新成功');
            }
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }

    public function actionDelete()
    {
        $id = intval(Yii::$app->request->get('id'));
        MemberModel::deleteAll(['id' => $id]);
        HtmlHelper::setMessage('刪除成功');
        return $this->redirect(['index' . $this->queryString]);
    }

    /**
     * 匯出會員資料
     */
    public function actionDownloadTemplate()
    {
        $objPHPExcel = new \PHPExcel();
        $sheet = $objPHPExcel->getActiveSheet();

        // 設定標題行（新增 分會名稱 於生日之後）
        $headers = [
            'ID(四位數0001)',
            '區',
            '帳號(Email)',
            '密碼',
            '名稱(姓名)',
            '手機',
            '生日',
            '分會名稱',
            '所在城市',
            '所在區域',
            '所在地址',
            '其他城市',
            '會員期限起',
            '會員期限訖'
        ];

        // 設定標題行
        $colIndex = 0;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, $header);
            $sheet->getColumnDimensionByColumn($colIndex)->setWidth(15);
            $colIndex++;
        }

        // 設定標題行樣式
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ]
        ];
        // 14 欄，樣式範圍為 A1:N1
        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

        // 查詢所有會員資料（包含區域名稱）
        $sql = "SELECT m.*, a.area_name FROM member m LEFT JOIN area a ON m.area_id = a.id ORDER BY m.id DESC";
        $members = MemberModel::getDb()->createCommand($sql)->queryAll(\PDO::FETCH_OBJ);

        // 填入會員資料
        $rowIndex = 2; // 從第2行開始（第1行是標題）
        foreach ($members as $member) {
            // 格式化日期：將 YYYY-MM-DD 轉換為 YYYY/MM/DD
            $formatDate = function ($date) {
                if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
                    return '';
                }
                // 如果是日期時間格式，只取日期部分
                if (strpos($date, ' ') !== false) {
                    $date = substr($date, 0, 10);
                }
                return str_replace('-', '/', $date);
            };

            $rowData = [
                isset($member->id) ? str_pad($member->id, 4, '0', STR_PAD_LEFT) : '',  // ID(四位數)
                $member->area_name ?? '',                       // 區
                $member->email ?? '',                           // 帳號(Email)
                '',                                             // 密碼（留空，因為是加密的）
                $member->name ?? '',                            // 名稱(姓名)
                $member->mobile ?? '',                          // 手機
                $formatDate($member->birthday),                 // 生日
                $member->club_name ?? '',                       // 分會名稱
                $member->city ?? '',                            // 所在城市
                $member->district ?? '',                        // 所在區域
                $member->address ?? '',                         // 所在地址
                $member->other_city ?? '',                      // 其他城市
                $formatDate($member->period_start),             // 會員期限起
                $formatDate($member->period_end)                // 會員期限訖
            ];

            $colIndex = 0;
            foreach ($rowData as $value) {
                $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $value);
                $colIndex++;
            }
            $rowIndex++;
        }

        // 輸出檔案
        $filename = '會員資料匯出_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }

    /**
     * 匯出空白 Excel 範本
     */
    public function actionDownloadEmptyTemplate()
    {
        $objPHPExcel = new \PHPExcel();
        $sheet = $objPHPExcel->getActiveSheet();

        // 設定標題行（新增 分會名稱 於生日之後）
        $headers = [
            'ID(四位數0001)',
            '區',
            '帳號(Email)',
            '密碼',
            '名稱(姓名)',
            '手機',
            '生日',
            '分會名稱',
            '所在城市',
            '所在區域',
            '所在地址',
            '其他城市',
            '會員期限起',
            '會員期限訖'
        ];

        // 設定標題行
        $colIndex = 0;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, $header);
            $sheet->getColumnDimensionByColumn($colIndex)->setWidth(15);
            $colIndex++;
        }

        // 設定標題行樣式
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ]
        ];
        // 14 欄，樣式範圍為 A1:N1
        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

        // 輸出檔案
        $filename = '會員匯入空白範本_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }

    /**
     * 匯入會員
     */
    public function actionImport()
    {
        $insertCount = 0;  // 新增筆數
        $updateCount = 0;  // 更新筆數
        $failCount = 0;    // 失敗筆數
        $failedRecords = [];
        $totalRows = 0;
        $skippedRows = 0;

        if (Yii::$app->request->isPost) {
            $file = \yii\web\UploadedFile::getInstanceByName('import_file');

            if ($file && $file->tempName) {
                try {
                    // 使用最簡單直接的方式讀取 Excel
                    // 直接讀取格式化的字串值，完全避免 PHPExcel 的類型判斷問題
                    $rows = $this->readExcelFile($file->tempName);
                    $totalRows = count($rows) - 1; // 扣除標題行

                    // 跳過第一行（標題行），從第二行開始處理
                    for ($i = 1; $i < count($rows); $i++) {
                        $row = $rows[$i];

                        // 跳過完全空白的行（檢查前5個欄位）
                        $isEmpty = true;
                        for ($j = 0; $j < 5 && $j < count($row); $j++) {
                            if (!empty(trim($row[$j]))) {
                                $isEmpty = false;
                                break;
                            }
                        }
                        if ($isEmpty) {
                            $skippedRows++;
                            continue;
                        }

                        $result = $this->importMemberRow($row);

                        if ($result['success']) {
                            if ($result['action'] === 'insert') {
                                $insertCount++;
                            } else {
                                $updateCount++;
                            }
                        } else {
                            $failCount++;
                            $failedRecords[] = [
                                'row' => $i + 1,
                                'data' => $row,
                                'error' => $result['error']
                            ];
                        }
                    }

                    $message = "匯入完成！";
                    if ($insertCount > 0) {
                        $message .= " 新增 {$insertCount} 筆";
                    }
                    if ($updateCount > 0) {
                        $message .= " 更新 {$updateCount} 筆";
                    }
                    if ($failCount > 0) {
                        $message .= " 失敗 {$failCount} 筆";
                    }
                    if ($skippedRows > 0) {
                        $message .= " 略過 {$skippedRows} 筆空白行";
                    }
                    HtmlHelper::setMessage($message);
                } catch (\Exception $e) {
                    // 記錄詳細錯誤到日誌
                    Yii::error(
                        '匯入會員檔案失敗：' . $e->getMessage() .
                            ' | 檔案：' . $e->getFile() .
                            ' | 行號：' . $e->getLine() .
                            ' | 堆疊：' . $e->getTraceAsString(),
                        'member-import'
                    );

                    // 顯示詳細錯誤訊息（開發環境）
                    $errorMsg = '檔案解析失敗：' . $e->getMessage();
                    if (YII_DEBUG) {
                        $errorMsg .= '<br>檔案：' . $e->getFile() .
                            '<br>行號：' . $e->getLine() .
                            '<br>堆疊：<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                    }
                    HtmlHelper::setError($errorMsg);
                }
            } else {
                HtmlHelper::setError('請選擇要匯入的 Excel 檔案');
            }
        }

        return $this->render('import', [
            'insertCount' => $insertCount,
            'updateCount' => $updateCount,
            'failCount' => $failCount,
            'failedRecords' => $failedRecords
        ]);
    }

    /**
     * 讀取 Excel 檔案內容
     * 使用最安全的方式：直接讀取格式化字串值，避免 PHPExcel 的類型判斷問題
     *
     * @param string $filePath Excel 檔案路徑
     * @return array 二維陣列，每一行是一個陣列
     * @throws \Exception
     */
    private function readExcelFile($filePath)
    {
        // 🔒 使用最安全的方式讀取 Excel：getFormattedValue()
        // 完全避免 PHPExcel 在 PHP 7.2 的 offset 錯誤
        \PHPExcel_Cell::setValueBinder(new \backend\helpers\SafeValueBinder());

        try {
            // 讀取 Excel
            $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
            $sheet = $objPHPExcel->getActiveSheet();

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // 將欄位字母轉成數字（A=1, B=2...）
            $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

            // 固定模板欄位數 (14 欄)，如果 Excel 比這多就多讀
            $maxCols = max($highestColumnIndex, 14);

            $rows = [];

            for ($row = 1; $row <= $highestRow; $row++) {
                $rowData = [];

                for ($col = 0; $col < $maxCols; $col++) {
                    try {
                        $cellAddress = \PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                        $cell = $sheet->getCell($cellAddress);

                        // 使用最安全的可讀格式：一定是字串
                        $value = $cell->getFormattedValue();

                        $rowData[] = ($value === null) ? '' : (string)$value;
                    } catch (\Exception $e) {
                        // 避免 Excel 的奇怪 cell 導致中斷
                        $rowData[] = '';
                    }
                }

                $rows[] = $rowData;
            }

            return $rows;
        } finally {
            // 恢復預設 ValueBinder（避免影響其他功能）
            \PHPExcel_Cell::setValueBinder(new \PHPExcel_Cell_DefaultValueBinder());
        }
    }


    /**
     * 匯入單筆會員資料
     * @param array $row Excel 行數據
     * @return array
     */
    private function importMemberRow($row)
    {
        try {
            // 記錄處理的行資料（用於除錯）
            if (YII_DEBUG) {
                Yii::trace('處理會員資料行：' . json_encode($row, JSON_UNESCAPED_UNICODE), 'member-import');
            }
            // 解析欄位 (索引從 0 開始)
            // 將所有值轉換為字串，避免整數類型問題
            // 第一個欄位 (索引 0) 是會員編號，已改用資料庫 ID，匯入時直接略過
            $areaName = isset($row[1]) ? trim((string)$row[1]) : '';      // 區
            $email = isset($row[2]) ? trim((string)$row[2]) : '';         // 帳號(Email)
            $password = isset($row[3]) ? trim((string)$row[3]) : '';      // 密碼
            $name = isset($row[4]) ? trim((string)$row[4]) : '';          // 名稱(姓名)
            $mobile = isset($row[5]) ? trim((string)$row[5]) : '';        // 手機
            $birthday = isset($row[6]) ? $this->parseExcelDate($row[6]) : null;  // 生日
            $clubName = isset($row[7]) ? trim((string)$row[7]) : '';      // 分會名稱
            $city = isset($row[8]) ? $this->normalizeCityName(trim((string)$row[8])) : '';  // 所在城市
            $district = isset($row[9]) ? $this->normalizeDistrictName(trim((string)$row[9])) : '';  // 所在區域
            $address = isset($row[10]) ? trim((string)$row[10]) : '';     // 所在地址
            $otherCity = isset($row[11]) ? trim((string)$row[11]) : '';   // 其他城市
            $periodStart = isset($row[12]) ? $this->parseExcelDate($row[12]) : null;  // 會員期限起
            $periodEnd = isset($row[13]) ? $this->parseExcelDate($row[13]) : null;    // 會員期限訖

            // 驗證必填欄位
            if (empty($email)) {
                return ['success' => false, 'action' => '', 'error' => 'Email 不能為空'];
            }
            if (empty($name)) {
                return ['success' => false, 'action' => '', 'error' => '姓名不能為空'];
            }

            // 查找區域 ID
            $areaId = 0;
            if (!empty($areaName)) {
                $area = AreaModel::findOne(['area_name' => $areaName]);
                if ($area) {
                    $areaId = $area->id;
                } else {
                    // 區域名稱不存在於資料庫中
                    return ['success' => false, 'action' => '', 'error' => "區域「{$areaName}」不存在，請先在系統中建立該區域"];
                }
            }

            // 檢查 Email 是否已存在
            $member = MemberModel::findOne(['username' => $email]);
            $isNewRecord = empty($member);

            if ($member) {
                // 更新現有會員
                $member->scenario = MemberModel::SCENARIO_IMPORT;

                // 密碼處理：更新會員時，只有當 Excel 中有提供密碼時才更新
                // 如果密碼為空則不更新，避免覆蓋原本的密碼
                if (!empty($password) && trim($password) !== '') {
                    $member->setPassword($password);
                }
                // 如果密碼為空，不執行任何操作，保留原本的密碼
            } else {
                // 新增會員
                $member = new MemberModel(['scenario' => MemberModel::SCENARIO_IMPORT]);
                $member->email = $email;

                // 密碼處理：新增會員時必須設定密碼
                if (!empty($password) && trim($password) !== '') {
                    // 如果 Excel 中有提供密碼，使用提供的密碼
                    $member->setPassword($password);
                } else {
                    // 如果密碼為空，使用 email 前綴作為預設密碼
                    $defaultPassword = substr($email, 0, strpos($email, '@'));
                    $member->setPassword($defaultPassword);

                    // 記錄使用預設密碼的會員（方便後續通知）
                    Yii::info("會員 {$email} 使用預設密碼：{$defaultPassword}", 'member-import');
                }
            }
            // 設定會員其他資料
            $member->name = $name;
            if (!empty($mobile)) {
                $member->mobile = $mobile;
            }
            if (!empty($birthday)) {
                $member->birthday = $birthday;
            }
            if (!empty($clubName)) {
                $member->club_name = $clubName;
            }
            if (!empty($city)) {
                $member->city = $city;
            }
            if (!empty($district)) {
                $member->district = $district;
            }
            if (!empty($address)) {
                $member->address = $address;
            }
            if (!empty($otherCity)) {
                $member->other_city = $otherCity;
            }
            if (!empty($periodStart)) {
                $member->period_start = $periodStart;
            }
            if (!empty($periodEnd)) {
                $member->period_end = $periodEnd;
            }
            if ($areaId > 0) {
                $member->area_id = $areaId;
            }

            // 儲存會員
            if ($member->save()) {
                $action = $isNewRecord ? 'insert' : 'update';
                return ['success' => true, 'action' => $action];
            } else {
                $errors = [];
                foreach ($member->errors as $field => $fieldErrors) {
                    $errors[] = implode(', ', $fieldErrors);
                }
                return ['success' => false, 'action' => '', 'error' => implode('; ', $errors)];
            }
        } catch (\Exception $e) {
            // 記錄詳細錯誤
            Yii::error(
                '匯入單筆會員資料失敗：' . $e->getMessage() .
                    ' | 檔案：' . $e->getFile() .
                    ' | 行號：' . $e->getLine() .
                    ' | 資料：' . json_encode($row, JSON_UNESCAPED_UNICODE),
                'member-import'
            );

            $errorMsg = $e->getMessage();
            if (YII_DEBUG) {
                $errorMsg .= ' (檔案：' . basename($e->getFile()) . '，行號：' . $e->getLine() . ')';
            }
            return ['success' => false, 'action' => '', 'error' => $errorMsg];
        }
    }

    /**
     * 正規化城市名稱（臺→台）
     */
    private function normalizeCityName($city)
    {
        return str_replace('臺', '台', $city);
    }

    /**
     * 正規化區域名稱（自動補"區"字）
     */
    private function normalizeDistrictName($district)
    {
        if (empty($district)) {
            return $district;
        }
        // 如果最後一個字不是"區"，則補上
        if (mb_substr($district, -1) !== '區') {
            $district .= '區';
        }
        return $district;
    }

    /**
     * 解析 Excel 日期格式
     */
    private function parseExcelDate($value)
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return null;
        }

        // 將值轉換為字串進行檢查
        $valueStr = (string)$value;

        // 如果已經是字串格式的日期
        if (preg_match('/^\d{4}[-\/]\d{1,2}[-\/]\d{1,2}$/', $valueStr)) {
            return date('Y-m-d', strtotime($valueStr));
        }

        // 如果是 Excel 的數字日期格式
        if (is_numeric($value)) {
            try {
                $timestamp = \PHPExcel_Shared_Date::ExcelToPHP((float)$value);
                return date('Y-m-d', $timestamp);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}

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

    /**
     * 匯出會員資料
     */
    public function actionDownloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 設定標題行
        $headers = [
            'ID(四位數0001)',
            '區',
            '帳號(Email)',
            '密碼',
            '名稱(姓名)',
            '手機',
            '生日',
            '所在城市',
            '所在區域',
            '所在地址',
            '其他城市',
            '會員期限起',
            '會員期限訖'
        ];

        // 設定標題行樣式
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];

        foreach ($headers as $col => $header) {
            $cell = $sheet->getCellByColumnAndRow($col + 1, 1);
            $cell->setValue($header);
            $sheet->getColumnDimensionByColumn($col + 1)->setWidth(15);
        }
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

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
                $member->member_code ?? '',                    // ID(四位數0001)
                $member->area_name ?? '',                       // 區
                $member->email ?? '',                           // 帳號(Email)
                '',                                             // 密碼（留空，因為是加密的）
                $member->name ?? '',                            // 名稱(姓名)
                $member->mobile ?? '',                          // 手機
                $formatDate($member->birthday),                 // 生日
                $member->city ?? '',                            // 所在城市
                $member->district ?? '',                        // 所在區域
                $member->address ?? '',                         // 所在地址
                $member->other_city ?? '',                      // 其他城市
                $formatDate($member->period_start),             // 會員期限起
                $formatDate($member->period_end)                // 會員期限訖
            ];

            foreach ($rowData as $col => $value) {
                $sheet->getCellByColumnAndRow($col + 1, $rowIndex)->setValue($value);
            }
            $rowIndex++;
        }

        // 輸出檔案
        $filename = '會員資料匯出_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
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
        $uploadFile = null;
        $totalRows = 0;
        $skippedRows = 0;

        if (Yii::$app->request->isPost) {
            $file = \yii\web\UploadedFile::getInstanceByName('import_file');

            if ($file && $file->tempName) {
                try {
                    // 載入 Excel 文件
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->tempName);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                    $totalRows = count($rows) - 1; // 扣除標題行

                    // 跳過第一行（標題行）
                    for ($i = 1; $i < count($rows); $i++) {
                        $row = $rows[$i];

                        // 跳過完全空白的行（檢查前5個欄位）
                        $isEmpty = true;
                        for ($j = 0; $j < 5; $j++) {
                            if (isset($row[$j]) && trim($row[$j]) !== '') {
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
                    HtmlHelper::setMessage($message);
                } catch (\Exception $e) {
                    HtmlHelper::setError('檔案解析失敗：' . $e->getMessage());
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
     * 匯入單筆會員資料
     * @param array $row Excel 行數據
     * @return array
     */
    private function importMemberRow($row)
    {
        try {
            // 解析欄位 (索引從 0 開始)
            $memberCode = isset($row[0]) && trim($row[0]) !== '' ? trim($row[0]) : '';
            // 如果 member_code 是純數字，自動補零到四位數（Excel 可能會過濾掉前面的 0）
            if (!empty($memberCode) && is_numeric($memberCode)) {
                $code = intval($memberCode);
                $memberCode = str_pad($code, 4, '0', STR_PAD_LEFT);
            }
            $areaName = isset($row[1]) && trim($row[1]) !== '' ? trim($row[1]) : '';
            $email = isset($row[2]) && trim($row[2]) !== '' ? trim($row[2]) : '';
            $password = isset($row[3]) && trim($row[3]) !== '' ? trim($row[3]) : '';
            $name = isset($row[4]) && trim($row[4]) !== '' ? trim($row[4]) : '';
            $mobile = isset($row[5]) && trim($row[5]) !== '' ? trim($row[5]) : '';
            $birthday = isset($row[6]) ? $this->parseExcelDate($row[6]) : null;
            $city = isset($row[7]) && trim($row[7]) !== '' ? $this->normalizeCityName(trim($row[7])) : '';
            $district = isset($row[8]) && trim($row[8]) !== '' ? $this->normalizeDistrictName(trim($row[8])) : '';
            $address = isset($row[9]) && trim($row[9]) !== '' ? trim($row[9]) : '';
            $otherCity = isset($row[10]) && trim($row[10]) !== '' ? trim($row[10]) : '';
            $periodStart = isset($row[11]) ? $this->parseExcelDate($row[11]) : null;
            $periodEnd = isset($row[12]) ? $this->parseExcelDate($row[12]) : null;

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
            
            // 檢查 member_code 是否已存在（如果提供了 member_code）
            if (!empty($memberCode)) {
                $existingMemberByCode = MemberModel::findOne(['member_code' => $memberCode]);
                // 如果 member_code 已存在，且不是同一個會員，則報錯
                if ($existingMemberByCode && ($isNewRecord || $existingMemberByCode->id != $member->id)) {
                    return ['success' => false, 'action' => '', 'error' => "會員編號「{$memberCode}」已存在，請使用其他編號"];
                }
            }

            if ($member) {
                // 更新現有會員
                $member->scenario = MemberModel::SCENARIO_IMPORT;

                // 設定會員編號：直接使用 Excel 檔案內容（不自動產生）
                if (!empty($memberCode)) {
                    $member->member_code = $memberCode;
                }

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

                // 設定會員編號：直接使用 Excel 檔案內容（不自動產生）
                if (!empty($memberCode)) {
                    $member->member_code = $memberCode;
                }

                // 密碼處理：新增會員時，如果 Excel 中有提供密碼則設定
                // 如果密碼為空則不設定（可能導致無法登入，但允許後續手動設定）
                if (!empty($password) && trim($password) !== '') {
                    $member->setPassword($password);
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
            return ['success' => false, 'action' => '', 'error' => $e->getMessage()];
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
        if (empty($value)) {
            return null;
        }

        // 如果已經是字串格式的日期
        if (is_string($value) && preg_match('/^\d{4}[-\/]\d{1,2}[-\/]\d{1,2}$/', $value)) {
            return date('Y-m-d', strtotime($value));
        }

        // 如果是 Excel 的數字日期格式
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}

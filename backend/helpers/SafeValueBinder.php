<?php

namespace backend\helpers;

/**
 * 安全的 PHPExcel ValueBinder
 * 修復 DefaultValueBinder 在 PHP 7.2+ 處理整數時的問題
 * 問題：DefaultValueBinder 第 82 行使用 $pValue{0} 訪問字串，但如果是整數會出錯
 */
class SafeValueBinder extends \PHPExcel_Cell_DefaultValueBinder
{
    /**
     * 綁定值到儲存格
     * 必須覆寫此方法，因為父類別使用 self:: 而不是 static::
     * 
     * @param \PHPExcel_Cell $cell 儲存格物件
     * @param mixed $value 要設定的值
     * @return bool
     */
    public function bindValue(\PHPExcel_Cell $cell, $value = null)
    {
        // 首先轉換型別（如果需要的話）
        if (is_null($value)) {
            $value = '';
        }

        // 如果是字串且包含換行符，使用 RichText（保持原有邏輯）
        if (is_string($value) && strlen($value) > 1 && strpos($value, "\n") !== false) {
            $value = new \PHPExcel_RichText($value);
        }

        // 如果不是 RichText，轉換為字串（保持原有邏輯）
        if (!($value instanceof \PHPExcel_RichText)) {
            $value = (string)$value;
        }

        // 關鍵修復：使用 static:: 而不是 self::
        // 這樣才能呼叫子類別（SafeValueBinder）的 dataTypeForValue()
        $cell->setValueExplicit($value, static::dataTypeForValue($value));

        return true;
    }

    /**
     * 判斷值的資料類型
     * 重寫此方法以修復整數類型判斷問題
     * 
     * @param mixed $pValue 要判斷的值
     * @return string 資料類型
     */
    public static function dataTypeForValue($pValue = null)
    {
        // 修復：先檢查類型，避免在非字串上使用字串操作
        if ($pValue === null) {
            return \PHPExcel_Cell_DataType::TYPE_NULL;
        }
        if ($pValue === '') {
            return \PHPExcel_Cell_DataType::TYPE_STRING;
        }
        
        // 重要：先檢查數字類型，避免後續的字串訪問錯誤
        if (is_bool($pValue)) {
            return \PHPExcel_Cell_DataType::TYPE_BOOL;
        }
        if (is_float($pValue) || is_int($pValue)) {
            return \PHPExcel_Cell_DataType::TYPE_NUMERIC;
        }
        
        // 檢查物件類型
        if ($pValue instanceof \PHPExcel_RichText) {
            return \PHPExcel_Cell_DataType::TYPE_INLINE;
        }
        
        // 只有確認是字串後，才進行字串操作
        if (is_string($pValue)) {
            // 檢查是否為公式（修復：使用 $pValue[0] 而不是 $pValue{0}）
            if (strlen($pValue) > 1 && $pValue[0] === '=') {
                return \PHPExcel_Cell_DataType::TYPE_FORMULA;
            }
            
            // 檢查是否為數字字串
            if (preg_match('/^[\+\-]?([0-9]+\\.?[0-9]*|[0-9]*\\.?[0-9]+)([Ee][\-\+]?[0-2]?\d{1,3})?$/', $pValue)) {
                $tValue = ltrim($pValue, '+-');
                if (strlen($tValue) > 1 && $tValue[0] === '0' && isset($tValue[1]) && $tValue[1] !== '.') {
                    return \PHPExcel_Cell_DataType::TYPE_STRING;
                }
                if ((strpos($pValue, '.') === false) && ($pValue > PHP_INT_MAX)) {
                    return \PHPExcel_Cell_DataType::TYPE_STRING;
                }
                return \PHPExcel_Cell_DataType::TYPE_NUMERIC;
            }
            
            return \PHPExcel_Cell_DataType::TYPE_STRING;
        }
        
        // 預設返回字串類型
        return \PHPExcel_Cell_DataType::TYPE_STRING;
    }
}


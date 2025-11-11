<?php

namespace ball\helper;

use Yii;

class HtmlHelper
{
    const MESSAGE = 'html_message';
    const ERROR = 'html_error';

    /**
     * 以矩陣的型態產生select tag的option value，開始數字比結束數字小時，會從開始數字遞減，
     * 開始數字比較結束數字大會從開始數字遞增
     * @param int $begin 開始數字
     * @param int $end 結束數字
     * @param array $text 自定顯示文字
     * @return array option values
     */
    public static function numbers($begin, $end, $text = null)
    {
        $isarray = is_array($text);
        $array = array();
        if ($isarray) {
            if ($begin > $end) {
                for ($i = $begin; $i >= $end; --$i) {
                    $array[$i] = $text[$i];
                }
            } else {
                for ($i = $begin; $i <= $end; ++$i) {
                    $array[$i] = $text[$i];
                }
            }
        } else {
            if ($begin > $end) {
                for ($i = $begin; $i >= $end; --$i) {
                    $array[$i] = $i;
                }
            } else {
                for ($i = $begin; $i <= $end; ++$i) {
                    $array[$i] = $i;
                }
            }
        }
        return $array;
    }

    public static function taiwanCities()
    {
        return array('基隆市' => '基隆市', '台北市' => '台北市', '新北市' => '新北市', '宜蘭縣' => '宜蘭縣', '新竹市' => '新竹市',
            '新竹縣' => '新竹縣', '桃園縣' => '桃園縣', '苗栗縣' => '苗栗縣', '台中市' => '台中市', '彰化縣' => '彰化縣', '南投縣' => '南投縣',
            '嘉義市' => '嘉義市', '嘉義縣' => '嘉義縣', '雲林縣' => '雲林縣', '台南市' => '台南市', '高雄市' => '高雄市', '屏東縣' => '屏東縣',
            '台東縣' => '台東縣', '花蓮縣' => '花蓮縣', '金門縣' => '金門縣', '連江縣' => '連江縣', '澎湖縣' => '澎湖縣', '其他地區' => '其他地區');
    }

    /**
     * html select tag專用
     * 一天24小時以半小時為單位的下拉式選單
     * @return array 格式為HH:mm的集合
     */
    public static function halfHourOfDay()
    {
        $timespanArray = array('00', '30');
        $time = array();
        for ($i = 0; $i < 24; ++$i) {
            if ($i < 10) {
                $value = '0' . $i;
            } else {
                $value = $i;
            }

            for ($j = 0; $j < sizeof($timespanArray); ++$j) {
                $time[$value . ':' . $timespanArray[$j]] = $value . ':' . $timespanArray[$j] . ':00';
            }
        }
        return $time;
    }

    /**
     * 1男0女
     * @param string $gender
     * @return string
     */
    public static function genderName($gender)
    {
        return $gender == '0' ? '女' : '男';
    }


    public static function checked($value1, $value2)
    {
        if ((string)$value1 === (string)$value2) {
            return 'checked="checked"';
        } else {
            return '';
        }
    }

    public static function selected($value1, $value2)
    {
        if ((string)$value1 === (string)$value2) {
            return 'selected="selected"';
        } else {
            return '';
        }
    }

    public static function substring($text, $maxlength)
    {
        $len = mb_strlen($text, 'UTF-8');
        $text = mb_substr($text, 0, $maxlength, 'UTF-8');
        if ($len > $maxlength) {
            $text .= '...';
        }
        return $text;
    }

    public static function setMessage($message)
    {
        Yii::$app->session->setFlash(self::MESSAGE, $message);
    }

    public static function setError($message)
    {
        Yii::$app->session->setFlash(self::ERROR, $message);
    }


    public static function displayFlash()
    {
        if (Yii::$app->session->hasFlash(self::MESSAGE)) {
            $message = Yii::$app->session->getFlash(self::MESSAGE);
            return <<<HTML
        <div class="alert alert-success m-2">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            $message
        </div>
HTML;
        }

        if (Yii::$app->session->hasFlash(self::ERROR)) {
            $message = Yii::$app->session->getFlash(self::ERROR);
            return <<<HTML
        <div class="alert alert-danger m-2">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            $message
        </div>
HTML;
        } else {
            return '';
        }
    }

}

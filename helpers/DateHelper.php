<?php
namespace frontend\modules\simplechat\helpers;
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 25.10.15
 * Time: 11:31
 */
class DateHelper
{
    public static function formatMessageDate($value)
    {
        $today = date_create()->setTime(0,0,0);
        $date = date_create($value)->setTime(0,0,0);
        if($today == $date) {
            $label = 'Today';
        }else if($today->getTimestamp() - $date->getTimestamp() == 24 * 60 * 60) {
            $label = 'Yesterday';
        }else if($today->format('W') == $date->format('W') && $today->format('Y') == $date->format('Y')) {
            $label = \Yii::$app->formatter->asDate($value,'php:l');
        }else if($today->format('Y') == $date->format('Y')) {
            $label = \Yii::$app->formatter->asDate($value,'php:d F');
        }else {
            $label = \Yii::$app->formatter->asDate($value,'medium');
        }
        $formatted =  \Yii::$app->formatter->asTime($value,'short');

        return [$label, $formatted];
    }

    public static function formatConversationDate($value)
    {
        $today = date_create()->setTime(0,0,0);
        $date = date_create($value)->setTime(0,0,0);
        if($today == $date) {
            $formatted = \Yii::$app->formatter->asTime($value,'short');
        }else if($today->getTimestamp() - $date->getTimestamp() == 24 * 60 * 60) {
            $formatted = 'Yesterday';
        }else if($today->format('W') == $date->format('W') && $today->format('Y') == $date->format('Y')) {
            $formatted = \Yii::$app->formatter->asDate($value,'php:l');
        }else if($today->format('Y') == $date->format('Y')) {
            $formatted = \Yii::$app->formatter->asDate($value,'php:d F');
        }else {
            $formatted = \Yii::$app->formatter->asDate($value,'medium');
        }
        return $formatted;
    }
}
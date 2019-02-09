<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the Base model class 
 *
 * 
 * @property datetime $created
 * @property datetime $updated
 * @property datetime $deleted
 * @property boolean $is_deleted
 */

class Base extends ActiveRecord
{
    protected $shortTextLength = 255;
    
    /**
     * Get the list of data models 
     * @param array $params
     * @return array the list of data models or int count of models
     */
    public function getList($params = null)
    {
        $query = $this->getListQuery($params);

        if (empty($query)) {
            return [];
        }

        if (empty($params['count'])) {
            $activeDataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
            ]);

            return $activeDataProvider->getModels();
        }

        return $query->asArray()->count();
    }

    /**
     * Get list query 
     * @param array $params
     * @return ActiveQuery
     */
    public function getListQuery($params = [])
    {
        if (empty($params)) {
            $params = [];
        }

        $query = self::find()->where(['is_deleted' => '0']);

        if (empty($params['count']) && !empty($params['order_by'])) {
            if (!empty($params['order_mode']) && $params['order_mode'] == 'desc') {
                $query->orderBy([$params['order_by'] => SORT_DESC]);
            } else {
                $query->orderBy([$params['order_by'] => SORT_ASC]);
            }
        }

        if (empty($params['count']) && empty($params['no_limit']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (!empty($params['limit'])) {
            $limit = $params['limit'];
        }

        if (empty($params['count']) && empty($params['no_limit'])) {
            $query->limit($limit);
        }

        return $query;
    }
    
    /**
     * Mark object as deleted
     * 
     * @return bool markDeleted success
     */
    public function markDeleted()
    {
        $this->is_deleted = 1;
        return $this->save();
    }

    /**
     * Recover object
     * 
     * @return bool recover success
     */
    public function recover()
    {
        $this->is_deleted = 0;
        return $this->save();
    }
    
    public function getShortText($text)
    {
        if (empty($text) || mb_strlen($text) < $this->shortTextLength) {
            return $text;
        }

        return mb_substr($text, 0, $this->shortTextLength) . '...';
    }
}

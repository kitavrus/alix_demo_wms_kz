<?php

namespace common\modules\audit\models;

use common\modules\store\models\StoreReviews;
use common\modules\audit\interfaces\AuditInterface;
class StoreReviewsAudit extends Audit implements AuditInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store_reviews_audit';
    }

    /**
     * @inheritdoc
     */
    public function getAuditObjectClass()
    {
        return StoreReviews::className();
    }
}

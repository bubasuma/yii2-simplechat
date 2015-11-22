<?php


use frontend\modules\simplechat\migrations\Migration;

class m151121_105654_user_data extends Migration
{
    public function up()
    {
        $data = require(__DIR__.'/data/users.php');
        try{
            foreach($data as $item){
                $user = new \frontend\modules\simplechat\db\demo\User();
                $user->setAttributes($item);
                if($user->save()){
                    $profile = new \frontend\modules\simplechat\db\demo\UserProfile();
                    $profile->setAttributes($item);
                    $profile->id = $user->id;
                    if(!$profile->save()){
                        $user->delete();
                    }
                }
            }
            return true;

        }catch (\yii\base\Exception $e){
            return false;
        }

    }

    public function down()
    {
        $this->delete(self::TABLE_USER);
    }
}

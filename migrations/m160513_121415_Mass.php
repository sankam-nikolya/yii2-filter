<?php

use yii\db\Schema;
use yii\db\Migration;

class m160513_121415_Mass extends Migration
{

    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        else {
            $tableOptions = null;
        }
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $this->createTable('{{%filter}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'description' => Schema::TYPE_TEXT . "",
                'relation_field_name' => Schema::TYPE_STRING . "(55)",
                'model_name' => Schema::TYPE_STRING . "(55) NOT NULL",
                'type' => Schema::TYPE_STRING . "(55) NOT NULL",
                'relation_field_value' => Schema::TYPE_TEXT . " COMMENT 'PHP serialize'",
                ], $tableOptions);

            $this->createTable('{{%filter_relation_value}}', [
                'id' => Schema::TYPE_PK . "",
                'filter_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'value' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                ], $tableOptions);

            $this->createTable('{{%filter_value}}', [
                'id' => Schema::TYPE_PK . "",
                'filter_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'variant_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'item_id' => Schema::TYPE_INTEGER . "(11)",
                ], $tableOptions);

            $this->createIndex('variant_id', '{{%filter_value}}', 'variant_id,item_id', 1);
            
            $this->addForeignKey(
                'fk_variant', '{{%filter_value}}', 'variant_id', '{{%filter_variant}}', 'id', 'CASCADE', 'CASCADE'
            );
            
            $this->addForeignKey(
                'fk_filter', '{{%filter_value}}', 'filter_id', '{{%filter}}', 'id', 'CASCADE', 'CASCADE'
            );
            
            $this->createTable('{{%filter_variant}}', [
                'id' => Schema::TYPE_PK . "",
                'filter_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'value' => Schema::TYPE_STRING . "(255)",
                'numeric_value' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                ], $tableOptions);

            $this->createIndex('filter_id', '{{%filter_variant}}', 'filter_id', 0);
            $transaction->commit();
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' and rollBack this';
            $transaction->rollBack();
        }
    }

    public function safeDown()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $this->dropTable('{{%filter}}');
            $this->dropTable('{{%filter_relation_value}}');
            $this->dropTable('{{%filter_value}}');
            $this->dropTable('{{%filter_variant}}');
            $transaction->commit();
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' and rollBack this';
            $transaction->rollBack();
        }
    }

}

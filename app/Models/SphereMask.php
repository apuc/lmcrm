<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SphereMask extends Model
{
    protected $table = NULL;

    public $tableDB = NULL;
    public $timestamps = false;

    public function __construct($id = NULL, array $attributes = array())
    {
        $this->table = 'sphere_bitmask_'.(int)$id;
        if (!DB::getSchemaBuilder()->hasTable($this->table)) {
            DB::statement('CREATE TABLE IF NOT EXISTS `' . $this->table . '`(`id` INT NOT NULL AUTO_INCREMENT, `agent_id` BIGINT NOT NULL, `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`))', []);
            DB::statement('ALTER TABLE `'.$this->table.'` ADD UNIQUE (`agent_id`)');
        }
        $this->tableDB = DB::table($this->table);

        parent::__construct($attributes);

        return $this->table;
    }

    public function getTableName(){
        return $this->table;
    }

    public function findAgentMask($agent_id){
        return $this->tableDB->where('agent_id','=',$agent_id);
    }

    public function findAgentShortMask($agent_id){
        $short_mask=array();
        $mask = get_object_vars($this->tableDB->where('agent_id','=',$agent_id)->first());
        foreach($mask as $field=>$val){
            if(stripos($field,'fb_')!==false){
                $short_mask[preg_replace('/^fb_[\d]+_/','',$field)]=$val;
            }
        }
        return $short_mask;
    }


    public function attributes() {
        return DB::getSchemaBuilder()->getColumnListing($this->table);
    }

    public function attributesAssoc() {
        $attributes = DB::getSchemaBuilder()->getColumnListing($this->table);
        $indexes= array();
        foreach($attributes as $field){
            if(stripos($field,'fb_')!==false){
                $indexes[$field]=preg_replace('/^fb_[\d]+_/','',$field);
            }
        }
        return $indexes;
    }

    public function addAttr($group_index,$opt_index){
        if(is_array($opt_index)) {
            foreach($opt_index as $aVal) $this->addAttr($group_index,$aVal);
        } else {
            $index = implode('_', ['fb', $group_index, $opt_index]);
            if (!in_array($index, $this->attributes())) {
                DB::statement('ALTER TABLE `' . $this->table . '` ADD COLUMN `' . $index . '` TINYINT(1) NULL', []);
            }
        }
        return $this->tableDB;
    }

    public function removeAttr($group_index,$opt_index){
        if(is_array($group_index) && $opt_index==null) {
            foreach($group_index as $item) {
                $delAttr = preg_grep("/^fb_" . $item . "_.*/", $this->attributes());
                foreach($delAttr as $item) {
                    DB::statement('ALTER TABLE `' . $this->table . '` DROP COLUMN `' . $item . '', []);
                }
            }
        } else {
            if (is_array($opt_index)) {
                foreach ($opt_index as $aVal) $this->removeAttr($group_index, $aVal);
            } else {
                $index = implode('_', ['fb', $group_index, $opt_index]);
                if (in_array($index, $this->attributes())) {
                    DB::statement('ALTER TABLE `' . $this->table . '` DROP COLUMN `' . $index . '', []);
                }
            }
        }
        return $this->tableDB;
    }

    public function setAttr($agent_id,$opt_index){
        if (is_array($opt_index)) {
            $values = array();
            $mask = $this->tableDB->where('agent_id','=',$agent_id)->first();
            if($mask) {
                $values['id']=$mask->id;
            } else {
                $values['id'] = $this->tableDB->insertGetId(['agent_id'=>$agent_id]);
            }
            $attributes = $this->attributesAssoc();
            foreach($attributes as $field=>$index) {
                $values[$field]=(in_array($index,$opt_index))?1:0;
            }
            $test=$values;
            $this->tableDB->update($values);
        }
        return $this->tableDB;
    }

    public function setDefault($index=0, $hash=false, $force=false){
        if($index==0 || !is_array($hash)) { return false; }
        foreach($hash as $id=>$val) {
            $fname = implode('_', ['fb', $index, $id]);
            $this->tableDB->where($fname,NULL)->update([$fname=>1]);
        }
        return $this->tableDB;
    }

    public function _delete() {
        //return $this->tableDB->drop();
        return DB::delete('DROP TABLE `'.$this->table.'`');
    }

    public function getAppends() {

        return $this->hasOne();
    }

    public function copyAttr($group_index,$new_opt_index,$parent_opt_index){
        DB::statement('UPDATE `'.$this->table.'` SET `'.implode('_', ['fb', $group_index, $new_opt_index]).'`=`'.implode('_', ['fb', $group_index, $parent_opt_index]).'` WHERE 1');
        return $this->tableDB;
    }
}
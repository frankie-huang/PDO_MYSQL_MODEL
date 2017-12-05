<?php
require_once "config.php";
require_once "PDO_MySQL.class.php";

/* 以下仅为使用示例 */

// 配置文件初始化
$link = M("fills");

// 数组参数初始化
$dbConfig = array(
    'hostname' => '127.0.0.1',
    'username'=>'root',
    'password'=>'root',
    'database'=>'db_name',
);
$link = M('fills', 1, $dbConfig);

# 如果需要此配置（上面的$dbConfig数组）切换数据表，既可以使用$link->table('other_table')，也可以如下使用
$other_table = M('other_table', 1);


## 1. select

//SELECT * FROM `fills` WHERE ( ( `degree` = '-1' AND `teacher` = 'who' ) OR `id` > '5' OR `status` <> '0' ) AND `subject` = '2' AND `chapter` >= '7'
$subsubmap['degree']=-1;
$subsubmap['teacher']='who';
$submap['_complex']=$subsubmap;
$submap['id']=array('gt',5);
$submap['status']=array('neq',0);
$submap['_logic']='or';
$map['_complex']=$submap;
$map['subject']=2;
$map['chapter']=array('egt',7);
dump($link->where($map)->select());

//SELECT fills.id,users.id AS `id_users`,concat(name,'-',id) AS `truename`,LEFT(title,7) AS `sub_title` FROM fills,chat.users
$field=array(
    'fills.id',
    'users.id'=>'id_users',
    "concat(name,'-',id)"=>'truename',
    'LEFT(title,7)'=>'sub_title'
);
dump($link->table('fills,chat.users')->field($field)->select()); //返回二维数组或null或false


## 2. find

//SELECT * FROM `fills` WHERE `id` = '4' LIMIT 1
dump($link->find(4)); //返回一维数组或null或false


## 3. add

//INSERT INTO users (`user_id`,`nick`,`school`) VALUES ('abcd1234','frankie','scut')
$data['user_id']='abcd1234';
$data['nick']='frankie';
$data['school']='scut';
dump($link->table('users')->add($data)); //返回lastID或false


## 4. save

//UPDATE users SET `nick` = 'frankie123',`school` = 'scut' WHERE `user_id` = 'abcd1234'
$data['user_id']='abcd1234';
$data['nick']='frankie123';
$data['school']='scut';
dump($link->table('users')->save($data)); //返回更新影响数或false


## 5. setField

//UPDATE users SET `nick` = 'test' WHERE `user_id` = 'abcd1234'
$save_where['user_id']='abcd1234';
dump($link->table('users')->where($save_where)->setField('nick','test')); //返回更新影响数或false

## 6. delete

//DELETE FROM `fills` WHERE `id` > '54' AND `subject` = '1'
$delete_where['id']=array('gt',54);
$delete_where['subject']=1;
dump($link->where($delete_where)->delete()); //返回删除成功的行数或false

//DELETE t2 FROM users as t1 INNER JOIN chat.users as t2 on t1.user_id=t2.user_id
dump($link->table('users as t1')->join('chat.users as t2 on t1.user_id=t2.user_id')->delete('t2'));


## 7.  打印当前模型执行的最后一条SQL语句
dump($link->getLastSql());
dump($link->_sql());


## 8. 事务驱动
$link->startTrans(); # 开启事务
$link->inTrans(); # 判断事务处于事务中，是则返回true
$link->where('u_id=1')->delete();
if ( $something == true ) {
    $link->commit();
} else {
    $link->rollback();
}


?>
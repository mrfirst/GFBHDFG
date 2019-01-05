<?php
header("charset=utf-8");
ini_set("display_errors", 0);

error_reporting(E_ALL ^ E_NOTICE);

error_reporting(E_ALL ^ E_WARNING);
$mysql_server_name="cd-cdb-dkzg4pd4.sql.tencentcdb.com:63389"; //数据库服务器名称
$mysql_username="phlxuser"; // 连接数据库用户名
$mysql_password="12345678"; // 连接数据库密码
$mysql_database="phlxdb"; // 数据库的名字

// 连接到数据库
$conn=mysql_connect($mysql_server_name, $mysql_username,
    $mysql_password);
if(!$conn) {
    echo "数据库连接失败！".mysql_error;
}
mysql_query("set character set 'utf8'");//读库
mysql_query("set names 'utf8'");//写库
mysql_select_db($mysql_database, $conn);
$username = isset($_POST['username']) ? $_POST['username'] : '';
$iphone = isset($_POST['iphone']) ? $_POST['iphone'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$branch = isset($_POST['branch']) ? $_POST['branch'] : '';
$luckynumbers = isset($_POST['luckynumbers']) ? $_POST['luckynumbers'] : '';


register($username,$iphone,$email,$branch,$luckynumbers);
close_conn();

//用户签到
function register($username,$iphone,$email,$branch,$luckynumbers) {
    global $conn;

    if($conn) {
        //数据库查询
        $result = mysql_query("select username from qiandao");
        $exist = false;
        while($row = mysql_fetch_array($result)) {
            if($username == $row['username']||$luckynumbers==$row['luckynumbers']) {
                //签到失败，用户名或者幸运号已存在;
                $exist = true;
                $register_result = array("register_result"=>false,"error_code"=>0);
                $json = json_encode($register_result);
                echo $json;
            }
        }

        //插入数据库
        if(!$exist) {
			$id = mysql_num_rows($result)+1;
			$success=mysql_query("insert into qiandao values('$id','$username','$iphone','$email','$branch','$luckynumbers')");
            if($success) {
                //签到成功
                $register_result = array("register_result"=>$success);
                $json = json_encode($register_result);
                echo $json;
            } else {
                //签到失败，数据库插入错误
                $register_result = array("register_result"=>$success,"error_code"=>1);
                $json = json_encode($register_result);
                echo $json;
            }
        }
    }
}

//关闭连接
function close_conn() {
    global $conn;
    mysql_close($conn);
}
?>  
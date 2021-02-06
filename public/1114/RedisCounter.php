<?

$redis = new Redis();

$redis->connect("redis",6379);

$key = "acsess_counter_1114";

$value = $redis->get($key);

$count;

if ($value === false){
        $count = 0;  
} else{
	$count = intval($value);
}       

$count++;

printf("このアクセスは全体で%d回目のアクセスです！",$count);

$redis->set($key,strval($count));

?>

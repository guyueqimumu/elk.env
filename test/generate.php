<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2020/8/11
 * Time: 17:20
 */
$str = "我的小时候吵闹任性的时候我的外婆总会唱歌哄我夏天的午后老老的歌安慰我那首歌好像这样唱的天黑黑欲落雨天黑黑黑黑离开小时候有了自己的生活新鲜的歌新鲜的念头任性和冲动无法控制的时候我忘记还有这样的歌天黑黑欲落雨天黑黑黑黑我爱上让我奋不顾身的一个人我以为这就是我所追求的世界然而横冲直撞被误解被骗是否成人的世界背后总有残缺我走在每天必须面对的分岔路我怀念过去单纯美好的小幸福爱总是让人哭让人觉得不满足天空很大却看不清楚好孤独我爱上让我奋不顾身的一个人我以为这就是我所追求的世界然而横冲直撞被误解被骗是否成人的世界背后总有残缺我走在每天必须面对的分岔路我怀念过去单纯美好的小幸福爱总是让人哭让人觉得不满足天空很大却看不清楚好孤独天黑的时候我又想起那首歌突然期待下起安静的雨原来外婆的道理早就唱给我听下起雨也要勇敢前进我相信一切都会平息我现在好想回家去天黑黑欲落雨天黑黑黑黑";
$length = mb_strlen($str, 'utf-8');
$offset = 4;
$randNum = rand(0, $length);
$start = ($randNum > ($length - $offset)) ? ($length - $offset) : $randNum;
$gender = [
    "male",
    "female",
];
$from = [
    "四川",
    "兰州",
    "北京",
    "广州",
    "湖南",
    "山东",
    "广西",
    "河北",
    "河北",
];
$hobby=[
    "吃饭",
    "玩游戏",
    "听音乐",
    "运动",
    "看书",
    "睡觉",
];
$data = [
    'name' => mb_substr($str, $start, $offset),
    'gender' => $gender[rand(0, 1)],
    'from' => $from[rand(0, (count($from)-1))],
    'hobby' => $hobby[rand(0, (count($hobby)-1))],
    'age' => rand(0, 100),
    'createdAt' => date("Y-m-d H:i:s"),
    'updatedAt' => date("Y-m-d H:i:s"),
];
echo json_encode($data,JSON_UNESCAPED_UNICODE).PHP_EOL;


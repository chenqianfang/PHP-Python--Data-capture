<?php
set_time_limit(0); //执行时间无限
ini_set('memory_limit', '-1');    //内存无限
ini_set("display_errors","On");  
error_reporting(E_ALL); 
ob_end_flush();  
ob_implicit_flush(1);  
$book_url = 'https://www.zwdu.com';
$url = 'https://www.zwdu.com/book/8167/';
$data = file_get_contents($url);
//页面源是gbk的编码格式，需要进行转换
$data = iconv("gb2312", "utf-8//IGNORE",$data);
//echo $data;

$p = '/<div id="info">\s*<h1>(.*?)<\/h1>/';//书名
$m = '/<p>作&nbsp;&nbsp;&nbsp;&nbsp;者：(.*?)<\/p>/';//作者
preg_match($p, $data, $title);
print_r($title[1]);echo '<br>';
$articleTitle = $title[1];//书名
if(!is_dir('book/')){
	//判断是已存在该小说的文件夹，如果不存在，就新建一个
    mkdir('book/',0777,true);
}
preg_match($m, $data, $author);
print_r($author[1]);echo '<br>';
$articleAuthor = $author[1];//作者

preg_match_all('/<dd><a href="(.*?)"/', $data, $chapter);

//print_r($chapter[1]);echo '<br>';die;
$chapter_url = $chapter[1];//章节列表url数组
$len = count($chapter_url);
$chapterTitle = [];
$chapterText = [];
$i = 590;
do {
	$chapterData = file_get_contents($book_url.$chapter_url[$i]);
	$chapterData = iconv("gb2312", "utf-8//IGNORE",$chapterData);
	preg_match('/<div class="bookname">\s*<h1>(.*?)<\/h1>/', $chapterData, $chapterH);
	//print_r($chapterH[1]);
	$chapterTitle[$i] = $chapterH[1];//章节标题
	preg_match('/<div id="content">(.*?)<\/div>/', $chapterData, $chapterT);
	$chapterT[1] = preg_replace('/八.*Ｍ/', '', $chapterT[1]);
	//print_r($chapterT[1]);
	$chapterText[$i] = $chapterT[1];//章节内容
	echo $chapterH[1].'<br>';
	$text = strip_tags($chapterT[1]);//去除html代码
	$text = $chapterH[1]."\r\n".$text."\r\n";//插入章节标题
	$text = str_replace('&nbsp;', '', $text);
	$fileName = 'book/'.$articleTitle.".txt";
	//转换文件名编码格式，防止windows下乱码，linux系统下不用转码
	$fileName = iconv('UTF-8', 'GBK', $fileName);
	//a+方式，向文本末尾写入新的章节
	$chapterFile = fopen($fileName, "a+") or die("Unable to open file!");
	fwrite($chapterFile, $text);
	fclose($chapterFile);
	sleep(1); //休眠一秒
	$i = $i + 1;
} while ($i < $len );
?>
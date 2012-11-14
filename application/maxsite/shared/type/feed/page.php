<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


header('Content-type: text/html; charset=utf-8');
header('Content-Type: application/rss+xml');

$cache_key = mso_md5('feed_' . mso_current_url());
$k = mso_get_cache($cache_key);
if ($k) return print($k); // да есть в кэше
ob_start();

require_once( getinfo('common_dir') . 'page.php' ); // основные функции страниц 
require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

$this->load->helper('xml');

$encoding = 'utf-8';

$time_zone = getinfo('time_zone'); // 2.00 -> 200
$time_zone_server = date('O') / 100; // +0100 -> 1.00
$time_zone = $time_zone + $time_zone_server; // 3
$time_zone = number_format($time_zone, 2, '.', ''); // 3.00

if ($time_zone < 10 and $time_zone > 0) $time_zone = '+0' . $time_zone;
elseif ($time_zone > -10 and $time_zone < 0) { $time_zone = '0' . $time_zone; $time_zone = str_replace('0-', '-0', $time_zone); }
else $time_zone = '+00.00';
$time_zone = str_replace('.', '', $time_zone);


$description = mso_head_meta('description');
$feed_url = getinfo('siteurl');
$language = 'en-ru';
$generator = 'MaxSite CMS (http://max-3000.com/)';

$par = array( 'limit'=>1, 'cut' => tf('Читать полностью') . ' »', 'type'=>false ); 
$pages = mso_get_pages($par, $pagination); 

if ($pages) 
{

	$pubdate = date('D, d M Y H:i:s '. $time_zone, strtotime(mso_date_convert('Y-m-d H:i:s', $pages[0]['page_date_publish'])));

	echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';
	
	# получаем комментарии к странице
	$page = $pages[0];
	extract($page);
	
	// $feed_name = mso_meta_title($page_title) . ' ('. tf('Комментарии к странице'). ')';
	$feed_name = mso_head_meta('title', $pages, '%page_title%', '', true ) . ' ('. tf('комментарии к странице'). ')' ;
	
	$comments = mso_get_comments($page_id);
?>

<rss version="2.0">
	<channel>
		<title><?= $feed_name ?></title>
		<link><?= $feed_url ?></link>
		<description><?= $description ?></description>
		<pubDate><?= $pubdate ?></pubDate>
		<language><?= $language ?></language>
		<generator><?= $generator ?></generator>
		<copyright>Copyright <?= gmdate("Y", time()) ?>, <?= getinfo('siteurl') ?></copyright>
		<?php foreach ($comments as $comment) : extract($comment); ?>
		<item>
			<title><?= xml_convert(strip_tags($users_nik . $comments_author_name . $comusers_nik)) ?> к "<?= xml_convert(strip_tags($page_title)) ?>"</title>
			<link><?= getinfo('siteurl') . 'page/' . mso_slug($page_slug) ?>#comment-<?= $comments_id ?></link>
			<guid><?= getinfo('siteurl') . 'page/' . mso_slug($page_slug) ?>#comment-<?= $comments_id ?></guid>
			<pubDate><?= date('D, d M Y H:i:s '. $time_zone, strtotime(mso_date_convert('Y-m-d H:i:s', $comments_date ))) ?></pubDate>
			<author>none@none.com (<?= xml_convert(strip_tags($users_nik . $comments_author_name . $comusers_nik)) ?>)</author>
			<description><![CDATA[<?= $comments_content  ?>]]></description>
		</item>
		<?php endforeach; ?>
	</channel>
</rss>
<?php 

} // if ($pages) 

mso_add_cache($cache_key, ob_get_flush()); // сразу и в кэш добавим - время 10 минут 60 сек * 10 минут *
?>
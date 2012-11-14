<?php
require_once ('admin.php');
$title = __('Export to MaxSite CMS');
$parent_file = 'edit.php';

if ( isset( $_GET['download'] ) ) export_wp();

require_once ('admin-header.php');
?>

<div class="wrap">
<h2>Экспорт в MaxSite CMS</h2>
<div class="narrow">

<p>Выберите автора для экспорта.</p>
<p>Я рекомендую экспортировать записи частями. Дело в том, что когда вы будете делать конвертирование, то скорее всего столкнетесь с ситуацией, когда ваш сервер ограничит размер памяти и файла. Чтобы этого не произошло лучше сразу разбить файл экспорта на несколько частей, а потом их последовательно конвертировать в <a href="http://max-3000.com/">MaxSite CMS</a></p>
<p>Следите за тем, чтобы размер выходного файла не превышал 300-500Кб. Количество можно подобрать экспериментально. У меня получилось примерно по 30 записей. После того, как вы экспортируете первый файл (с 1), то потом укажите начиная с 30, потом 60, потом 90 и т.д. Таким образом у вас получится несколько xml-файлов.</p>


<form action="" method="get">

<table>
<tr>
<th>Автор:</th>
<td>
<select name="author">
<option value="all" selected="selected"><?php _e('All'); ?></option>
<?php
$authors = $wpdb->get_col( "SELECT post_author FROM $wpdb->posts GROUP BY post_author" );
foreach ( $authors as $id ) {
	$o = get_userdata( $id );
	echo "<option value='$o->ID'>$o->display_name</option>";
}
?>
</select>
</td>
</tr>
</table>

<?php
$all_count = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_status != 'inherit'");
?>

<p>Начиная с записи <input type="text" name="limit_start" value="1"> (всего <?= count($all_count) ?> записей)
<br>Количество по <input type="text" name="limit_count" value="30"></p>


<p class="submit"><input type="submit" name="submit" value="<?php _e('Download Export File'); ?> &raquo;">
<input type="hidden" name="download" value="true">
</p>
</form>
</div>
</div>

<?php

function export_wp() {
global $wpdb, $post_ids, $post;

do_action('export_wp');

$filename = 'wordpress.' . date('Y-m-d') . '.xml';

// ограничения по кол-ву записей
$limit = '';
if ( isset($_GET['limit_start']) and $_GET['limit_start'] and isset($_GET['limit_count']) and $_GET['limit_count']) 
{
	$limit_start = (int) $_GET['limit_start'];
	$limit_count = (int) $_GET['limit_count'];
	
	if ($limit_start and $limit_count) 
	{
		$limit = ' LIMIT ' . $limit_start . ', ' . $limit_count;
		$filename = 'wp-' . $limit_start . '-'. ($limit_count+$limit_start) . '.xml';
	}
}


header('Content-Description: File Transfer');
header("Content-Disposition: attachment; filename=$filename");
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);

$where = '';
if ( isset( $_GET['author'] ) && $_GET['author'] != 'all' ) {
	$author_id = (int) $_GET['author'];
	$where = " WHERE post_author = '$author_id' and post_status != 'inherit'";
}
else $where = " WHERE post_status != 'inherit'";


// grab a snapshot of post IDs, just in case it changes during the export


$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts $where ORDER BY post_date_gmt ASC{$limit}");

$categories = (array) get_categories('get=all');
$tags = (array) get_tags('get=all');

function wxr_missing_parents($categories) {
	if ( !is_array($categories) || empty($categories) )	return array();

	foreach ( $categories as $category )
		$parents[$category->term_id] = $category->parent;

	$parents = array_unique(array_diff($parents, array_keys($parents)));

	if ( $zero = array_search('0', $parents) )
		unset($parents[$zero]);

	return $parents;
}

while ( $parents = wxr_missing_parents($categories) ) {
	$found_parents = get_categories("include=" . join(', ', $parents));
	if ( is_array($found_parents) && count($found_parents) )
		$categories = array_merge($categories, $found_parents);
	else
		break;
}

// Put them in order to be inserted with no child going before its parent
$pass = 0;
$passes = 1000 + count($categories);
while ( ( $cat = array_shift($categories) ) && ++$pass < $passes ) {
	if ( $cat->parent == 0 || isset($cats[$cat->parent]) ) {
		$cats[$cat->term_id] = $cat;
	} else {
		$categories[] = $cat;
	}
}
unset($categories);

function wxr_cdata($str) {
	if ( seems_utf8($str) == false ) $str = utf8_encode($str);

	// $str = ent2ncr(wp_specialchars($str));

	$str = "<![CDATA[$str" . ( ( substr($str, -1) == ']' ) ? ' ' : '') . "]]>";

	return $str;
}

function wxr_cat_name($c) {
	if ( empty($c->name) ) return;

	echo '<wp:cat_name>' . wxr_cdata($c->name) . '</wp:cat_name>';
}

function wxr_category_description($c) {
	if ( empty($c->description) ) return;

	echo '<wp:category_description>' . wxr_cdata($c->description) . '</wp:category_description>';
}

function wxr_tag_name($t) {
	if ( empty($t->name) ) return;

	echo '<wp:tag_name>' . wxr_cdata($t->name) . '</wp:tag_name>';
}

function wxr_tag_description($t) {
	if ( empty($t->description) ) return;

	echo '<wp:tag_description>' . wxr_cdata($t->description) . '</wp:tag_description>';
}

function wxr_post_taxonomy() {
	$categories = get_the_category();
	$tags = get_the_tags();
	$cat_names = array();
	$tag_names = array();
	$the_list = array();
	$filter = 'rss';

	if ( !empty($categories) ) foreach ( (array) $categories as $category ) {
		$cat_name = sanitize_term_field('name', $category->name, $category->term_id, 'category', $filter);
		// $the_list .= "\n\t\t<category><![CDATA[$cat_name]]></category>\n";
		$the_list['category'][$category->term_id] = $cat_name;
	}

 	if ( !empty($tags) ) foreach ( (array) $tags as $tag ) {
		$tag_name = sanitize_term_field('name', $tag->name, $tag->term_id, 'post_tag', $filter);
		// $the_list .= "\n\t\t<category domain=\"tag\"><![CDATA[$tag_name]]></category>\n";
		$the_list['tag'][$tag->term_id] = $tag->name;
	}

	echo '<category><![CDATA[' . serialize($the_list) . ']]></category>';
}

echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?' . ">\n";

?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.0/"
>

<channel>
	<title><?php bloginfo_rss('name'); ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></pubDate>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php if ( $cats ) : ?><categorys><![CDATA[<?php echo serialize($cats); ?>]]></categorys><?php endif; ?>
	<?php if ( $tags ) : ?><tags><![CDATA[<?php echo serialize($tags); ?>]]></tags><?php endif; ?>
	<?php do_action('rss2_head'); ?>
	<?php if ($post_ids) {
		global $wp_query;
		$wp_query->in_the_loop = true;  // Fake being in the loop.
		// fetch 20 posts at a time rather than loading the entire table into memory
		while ( $next_posts = array_splice($post_ids, 0, 20) ) {
			$where = "WHERE ID IN (".join(',', $next_posts).") and post_status != 'inherit'";
			$posts = $wpdb->get_results("SELECT * FROM $wpdb->posts $where ORDER BY post_date_gmt ASC");
				foreach ($posts as $post) {
			setup_postdata($post); ?>
			
<item>
	<title><?php the_title_rss() ?></title>
	<link><?php the_permalink_rss() ?></link>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
	<dc:creator><?php the_author() ?></dc:creator>
	<?php wxr_post_taxonomy() ?>
	<content><![CDATA[<?php echo $post->post_content ?>]]></content>
	<wp:post_id><?php echo $post->ID; ?></wp:post_id>
	<wp:post_date><?php echo $post->post_date; ?></wp:post_date>
	<wp:post_date_gmt><?php echo $post->post_date_gmt; ?></wp:post_date_gmt>
	<wp:comment_status><?php echo $post->comment_status; ?></wp:comment_status>
	<wp:post_name><?php echo $post->post_name; ?></wp:post_name>
	<wp:status><?php echo $post->post_status; ?></wp:status>
	<wp:post_type><?php echo $post->post_type; ?></wp:post_type>

	<?php
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post->ID");
	if ( $comments ) { ?>
	<comments><![CDATA[<?php echo serialize($comments); ?>]]></comments>
	<?php } ?>

</item>

<?php } } } ?>
</channel>
</rss>
<?php
	die();
}

include ('admin-footer.php');
?>
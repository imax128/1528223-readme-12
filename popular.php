<?php

$is_auth = rand(0, 1);

$user_name = 'Максим'; // укажите здесь ваше имя

// подключаем файл с функциями
include ('helpers.php');

include ('mysqli_connect.php');

if (!$con) {
   print('Ошибка подключения: ' . mysqli_connect_error());
} else {
	$sql = 'SELECT id, name, class FROM content_type';
	$sql_content_types = mysqli_query($con, $sql);
	$content_types = mysqli_fetch_all($sql_content_types, MYSQLI_ASSOC);
	
	$get_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_NUMBER_INT) ?? '0';
	
	$sql = '
		SELECT
			posts.num_views as views,
			posts.dt_add as datetime,
			posts.header as header,

			content_type.class as type,
			
			users.username as username,
			users.avatar as avatar,
			
			CONCAT (posts.text, posts.image_url, posts.video_url) as content,
			
			posts.id as id,
			posts.author as author,
			posts.site_url as url
			
		FROM posts
			LEFT JOIN users ON posts.user_id = users.id
			LEFT JOIN content_type ON posts.content_type_id = content_type.id
	';
	if ($get_type !== '0') {
		$sql .= ' WHERE content_type.id = ' . $get_type;
	}
	$sql .= ' ORDER BY num_views DESC';
	$sql_popular_posts = mysqli_query($con, $sql);
	$popular_posts = mysqli_fetch_all($sql_popular_posts, MYSQLI_ASSOC);
}

// защита от XSS-атак
foreach ($popular_posts as $array_key => $array_value) {
	foreach ($array_value as $key => $value) {
		$popular_posts[$array_key][$key] = htmlspecialchars($value);
	}
}

// добавляем случайные даты в двумерный массив списка постов с помощью функции generate_random_date
$index = 0;
foreach ($popular_posts as $array_key => $array_value) {
	$popular_posts[$array_key]['datetime'] = generate_random_date($index);
	$index += 1;
}

$page_content = include_template('main.php', ['content_types' => $content_types, 'popular_posts' => $popular_posts, 'get_type' => $get_type]);

// окончательный HTML-код
$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'readme: популярное', 'is_auth' => $is_auth, 'user_name' => $user_name]);

print($layout_content);

?>
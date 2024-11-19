<?php
// Устанавливаем количество символов, которые будут отображаться на одной странице
define('PAGE_SIZE', 3000);

// Массив с доступными книгами и их файлами
// Внутри текст "рыба"
$books = [
    'book1' => 'book1.html',
    'book2' => 'book2.html',
    'book3' => 'book3.html',
];

// Проверяем выбрал ли пользователь книгу
if (isset($_GET['book'])) {
    $selectedBook = $_GET['book'];
} else {
    $selectedBook = null;
}

// Загружаем текст выбранной книги, если она существует в массиве $books
if ($selectedBook && isset($books[$selectedBook])) {
    $text = file_get_contents($books[$selectedBook]);
} else {
    $text = null;
}

// Определяем текущую страницу 
if (isset($_GET['page'])) { // Берем из URL если есть
    $page = intval($_GET['page']); 
} elseif ($selectedBook && isset($_COOKIE["page_$selectedBook"])) { // Или из куки
    $page = intval($_COOKIE["page_$selectedBook"]);
} else { // Иначе устанавливаем текущей страницей первую
    $page = 1;
}

// Сохраняем текущую страницу в куки для выбранной книги
if ($selectedBook) {
    setcookie("page_$selectedBook", $page);
}

// Функция для возврата содержимого страницы
function getPageContent($page, $text) {
    $pages = str_split($text, PAGE_SIZE); // Разделяем текст на страницы заданным размером
    // Возвращаем запрашиваемую страницу
    if (isset($pages[$page - 1])) {
        return $pages[$page - 1];
    } else {
        return "Страница не найдена"; // Выдаем ощибку если страница не найдена
    }
}

// Функция для выделения текста при поиске по странице
function highlightText($content, $query) {
    if ($query) {
        // Подсвечиваем совпавший текст
        return preg_replace("/(" . preg_quote($query, '/') . ")/iu", "<span class='highlight'>$1</span>", $content);
    }
    return $content;
}

// Получаем текст для поиска из URL
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
} else {
    $searchQuery = '';
}

// Получаем содержимое текущей страницы
if ($text) {
    $currentPageContent = getPageContent($page, $text);
    $highlightedContent = highlightText($currentPageContent, $searchQuery);
} else {
    $currentPageContent = '';
    $highlightedContent = '';
}

// Подсчитываем общее количество страниц
if ($text) {
    $totalPages = ceil(strlen($text) / PAGE_SIZE);
} else {
    $totalPages = 0;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Читалка</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" 
          integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        .main-reader-color { background-color: #78a5de; }
        .highlight { background-color: yellow; font-weight: bold; }
    </style>
</head>

<body>
<div class="container mt-4">
    <h2 class="text-center">Приятного чтения!</h2>

    <!-- Форма для выбора книги -->
    <form method="get" class="mb-3">
        <label for="book">Выберите книгу:</label>
        <select name="book" id="book" onchange="this.form.submit()">
            <option value=""></option>
            <?php foreach ($books as $key => $file): ?>
                <option value="<?php echo $key; ?>" <?php if ($key === $selectedBook) echo 'selected'; ?>>
                    <?php echo ucfirst($key); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($selectedBook): ?>
            <input type="hidden" name="page" value="<?php echo $page; ?>">
        <?php endif; ?>
    </form>

    <?php if ($selectedBook): ?>
        <div class="main-reader-color p-3 rounded">
            <h3 class="text-center">Страница: <?php echo $page; ?> из <?php echo $totalPages; ?></h3>
            <div class="bg-white p-3 rounded">
                <?php echo $highlightedContent; ?>
            </div>

            <!-- Навигация по страницам -->
            <div class="d-flex justify-content-between mt-3">
                <a href="?book=<?php echo $selectedBook; ?>&page=<?php echo max(1, $page - 1); ?>" class="btn btn-secondary">Предыдущая</a>
                <a href="?book=<?php echo $selectedBook; ?>&page=<?php echo min($totalPages, $page + 1); ?>" class="btn btn-secondary">Следующая</a>
            </div>
        </div>

        <!-- Поиск по книге -->
        <div class="main-reader-color p-3 rounded mt-4">
            <h4>Поиск в тексте</h4>
            <form action="" method="get" class="d-flex">
                <input type="hidden" name="page" value="<?php echo $page; ?>">
                <input type="hidden" name="book" value="<?php echo $selectedBook; ?>">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" class="form-control me-2">
                <button class="btn btn-primary" type="submit">Искать</button>
            </form>
        </div>
    <?php else: ?>
        <p class="text-center mt-4">Выберите книгу для чтения</p>
    <?php endif; ?>
</div>
</body>
</html>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>quiz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php

        // массив с вопросами и ответами
        $questions = [
            [
                'question' => 'Какой язык использовался для разработки этого сайта?',
                'correct' => 'PHP',
                'incorrect' => ['Python', 'C++', 'Java', 'Ruby']
            ],
            [
                'question' => 'Каким тегом объявляется веб-страница?',
                'correct' => 'html',
                'incorrect' => ['head', 'body', 'title', 'div']
            ],
            [
                'question' => 'Какая CSS команда используется для изменения цвета текста?',
                'correct' => 'color',
                'incorrect' => ['font-color', 'text-color', 'background-color', 'text-style']
            ]
        ];

        // Проверяем отправку данных на сервер
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $score = 0;
            // Проверяем ответы пользователя
            foreach ($questions as $index => $q) {
                $user_answer = $_POST["question_$index"] ?? null;
                if ($user_answer === $q['correct']) {
                    $score++;
                }
            }

            // Вывод результата
            echo "<div class='result'>Вы ответили правильно на $score из " . count($questions) . " вопросов.</div>";
            // Кнопка для повторного прохождения теста
            echo "<form method='get'>";
            echo "<button type='submit' class='submit-btn'>Пройти тест заново</button>";
            echo "</form>";
        } else {
            // Если форма не отправлена, выводим тест
            echo '<form method="POST">';
            foreach ($questions as $index => $q) {
                // Создаем массив для ответов
                $answers = [$q['correct']]; // Добавляем правильный ответ
                $incorrect_answers = $q['incorrect']; // Создаем копию неправильных ответов 
                shuffle($incorrect_answers); // Перемешиваем неправильные ответы
                $answers = array_merge($answers, array_slice($incorrect_answers, 0, 3)); // Добавляем первые три
                shuffle($answers); // Перемешиваем получившийся массив

                echo "<div class='question'>";
                echo "<h3>" . ($index + 1) . ". " . $q['question'] . "</h3>";
                echo "<div class='answers'>";
                foreach ($answers as $answer) {
                    echo "<label><input type='radio' name='question_$index' value='$answer'> $answer</label>";
                }
                echo "</div></div>";
            }
            echo '<button type="submit" class="submit-btn">Отправить</button>';
            echo '</form>';
        }
        ?>
    </div>
</body>
</html>

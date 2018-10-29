<?php
    $commandLine = $argv;
    $csv_file = './books.csv';
	
    if(count($commandLine) == 1)
	    exit('Ошибка! Укажите название книги'."\n");	
	if ($commandLine[1] && count($commandLine) == 2 && !file_exists($csv_file))
		exit('Ошибка. Файл $csv_file не найден'."\n");	
    if(!file_exists($csv_file)) 
	    $myfile = fopen($csv_file, "w");       	
    if(!is_writable($csv_file)) 
	    exit('Ошибка. Файл $csv_file защищен от записи'."\n");		

    $arrayBooks = array_slice($commandLine, 1, count($commandLine) - 1);
    $titleBooks = implode(' ',$arrayBooks);

    $apiUrl = 'https://www.googleapis.com/books/v1/volumes?q=' . urlencode($titleBooks);

    $books = json_decode(file_get_contents($apiUrl));

    check_correct_json();

    foreach($books->items as $book) {
        $authors = [];
        if(isset($book->volumeInfo->authors)) {
            foreach($book->volumeInfo->authors as $author)
                $authors[] = $author;
        }
		
    $row = $book->id . ',' . $book->volumeInfo->title . ',' . implode(' ',$authors);
        file_put_contents('./books.csv', $row."\n", FILE_APPEND); 
    }

    function check_correct_json() {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                echo 'JSON: Ошибок нет'."\n";
                    break;
            case JSON_ERROR_DEPTH:
                exit('JSON: Достигнута максимальная глубина стека'."\n");
                    break;
            case JSON_ERROR_STATE_MISMATCH:
                exit('JSON: Некорректные разряды или несоответствие режимов'."\n");
                    break;
            case JSON_ERROR_CTRL_CHAR:
                exit('JSON: Некорректный управляющий символ'."\n");
                    break;
            case JSON_ERROR_SYNTAX:
                exit('JSON: Синтаксическая ошибка, некорректный JSON'."\n");
                    break;
            case JSON_ERROR_UTF8:
                exit('JSON: Некорректные символы UTF-8, возможно неверно закодирован'."\n");
                    break;
        default:
            exit('JSON: Неизвестная ошибка'."\n");
    break;
  }
}

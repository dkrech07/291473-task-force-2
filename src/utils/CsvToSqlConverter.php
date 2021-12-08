<?php

declare(strict_types=1);

namespace TaskForce\utils;

use TaskForce\exceptions\CsvToSqlException;
use TaskForce\exceptions\FileSourceException;

use SplFileObject;
use RuntimeException;

class CsvToSqlConverter
{
    public function __construct($sql_directory)
    {
        // Проверяю доступна ли папка для сохранения sql-файлов;
        if (!file_exists($sql_directory)) {
            throw new CsvToSqlException("Папка " . $sql_directory . " не найдена.");
        }
        // Задаю директорию, если переданная в класс папка присутствует в проекте;
        $this->sql_directory = $sql_directory;
    }

    // Получаю колонки таблицы БД;
    private function get_tables_columns(SplFileObject $csv_file): string
    {
        // SplFileObject::rewind — Перемотка файлового указателя в начало файла
        $csv_file->rewind();

        // SplFileObject::fgetcsv — Получить строку из файла и её разбор как поля CSV
        $data = $csv_file->fgetcsv();

        // Получаю первый элемент массива;
        $columns = array_shift($data);

        foreach ($data as $value) {
            $columns .= ", $value";
        }

        return $columns;
    }


    private function array_to_string(array $array): string
    {
        print_r($array);
        return implode('", "', $array);
    }

    // Пишет данные в sql-файл;
    private function write_sql_line(SplFileObject $sql_file, string $sql_line): void
    {
        // SplFileObject::fwrite — Запись в файл
        $sql_file->fwrite("$sql_line\r\n");
    }

    private function get_next_line(SplFileObject $csv_file): iterable
    {
        // SplFileObject::eof — Проверяет, достигнут ли конец файла
        while (!$csv_file->eof()) {
            // SplFileObject::fgetcsv — Получить строку из файла и её разбор как поля CSV
            yield $csv_file->fgetcsv();
        }
    }

    public function convert_csv_file($csv_file_name): void
    {
        $csv_file = new SplFileObject($csv_file_name);

        try {
            $sql_file_name = pathinfo($csv_file_name, PATHINFO_FILENAME);
            $sql_file_names = explode('.', $sql_file_name)[0] ?? null;
            $sql_file_path = $this->sql_directory . $sql_file_name . '.sql';
            $sql_file = new SplFileObject($sql_file_path, 'w');

            // print('name: ' . $sql_file_name);
            // print('<br>');
            // print('names: ' . $sql_file_names);
            // print('<br>');
            // print('path: ' . $sql_file_path);
            // print('<br>');
            // print($this->sql_directory);
        } catch (RuntimeException $error) {
            throw new FileSourceException('Ошибка создания sql-файла: ' . $error);
        }

        // Удаляет символы переноса в конце строки
        $csv_file->setFlags(SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        // Получаю все колонки таблицы
        $tables_columns = $this->get_tables_columns($csv_file);
        // print('<br>');
        // print($tables_columns);
        // Получаю первые значения каждой таблицы;

        print_r($csv_file->current());
        print('<br>');
        print_r($csv_file->fgetcsv());
        print('<br>');
        $first_values = $this->array_to_string(
            $csv_file->fgetcsv()
        );

        // print($first_values);
        // print('<br>');

        // Пишу строку в sql;
        $sql_query = "INSERT INTO $sql_file_names (" . $tables_columns . ")\r\n" . 'VALUES ("' . $first_values . '"),';

        $this->write_sql_line($sql_file, $sql_query);

        // Нужно заменить последнюю в файлах ',' на ';'
    }
}

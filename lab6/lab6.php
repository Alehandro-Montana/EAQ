<?php

// Інтерфейс для завантаження файлів
interface Downloader
{
    public function download(string $url): string;
}

// Клас для простого завантажувача
class SimpleDownloader implements Downloader
{
    public function download(string $url): string
    {
        
        return "File content from $url"; // Повертає вміст файлу
    }
}

// Клас-замісник для кешування
class CachedDownloader implements Downloader
{
    private $downloader;
    private $cache = [];

    public function __construct(Downloader $downloader) //Параметр тут екзмепляр  з SimpleDownloader
    {
        $this->downloader = $downloader;
    }

    public function download(string $url): string
    {
        if (isset($this->cache[$url])) {
            return $this->cache[$url]; // Повертає кешований вміст
        }

        $content = $this->downloader->download($url);
        $this->cache[$url] = $content; // Кешує вміст
        return $content;
    }
}

$simpleDownloader = new SimpleDownloader();


$cachedDownloader = new CachedDownloader($simpleDownloader);


$url = "http://myexample.com/file1.txt";

// Перший запит завантажено
echo $cachedDownloader->download($url) . "\n";

// Другий запит взято з кешу
echo $cachedDownloader->download($url) . "\n";

// Завантаження іншого файлу
$anotherUrl = "http://myexample.com/file2.txt";
echo $cachedDownloader->download($anotherUrl) . "\n";

// Завантаження файлу ще раз - кешування спрацює
echo $cachedDownloader->download($anotherUrl) . "\n";

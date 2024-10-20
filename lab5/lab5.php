<?php


abstract class Page
{
    protected $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    abstract public function render(): string;
}

// Клас для простої сторінки
class SimplePage extends Page
{
    private $title;
    private $content;

    public function __construct(Renderer $renderer, string $title, string $content) //приймає рендерер, заголовок та контент
    {
        parent::__construct($renderer);
        $this->title = $title;
        $this->content = $content;
    }

    public function render(): string
    {
        return $this->renderer->renderSimplePage($this->title, $this->content);
    }
}

// Клас для сторінки товару
class ProductPage extends Page
{
    private $product;

    public function __construct(Renderer $renderer, Product $product) //приймає рендерер та об'єкт продукту
    {
        parent::__construct($renderer);
        $this->product = $product;
    }

    public function render(): string
    {
        return $this->renderer->renderProductPage($this->product);
    }
}

// Клас продукту
class Product
{
    private $id;
    private $name;
    private $description;
    private $image;

    public function __construct(int $id, string $name, string $description, string $image) //приймає id, назву, опис та зображення продукту
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getImage(): string { return $this->image; }
}

// Інтерфейс для рендерерів
interface Renderer
{
    public function renderSimplePage(string $title, string $content): string;
    public function renderProductPage(Product $product): string;
}

// Клас для HTML-рендерера
class HTMLRenderer implements Renderer
{
    public function renderSimplePage(string $title, string $content): string
    {
        return "<h1>$title</h1><p>$content</p>";
    }

    public function renderProductPage(Product $product): string
    {
        return "<h1>{$product->getName()}</h1><p>{$product->getDescription()}</p><img src='{$product->getImage()}' /><p>ID: {$product->getId()}</p>";
    }
}

// Клас для JSON-рендерера
class JsonRenderer implements Renderer
{
    public function renderSimplePage(string $title, string $content): string
    {
        return json_encode(['title' => $title, 'content' => $content]);
    }

    public function renderProductPage(Product $product): string
    {
        return json_encode([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'image' => $product->getImage(),
        ]);
    }
}

// Клас для XML-рендерера
class XmlRenderer implements Renderer
{
    public function renderSimplePage(string $title, string $content): string
    {
        return "<page><title>$title</title><content>$content</content></page>";
    }

    public function renderProductPage(Product $product): string
    {
        return "<product><id>{$product->getId()}</id><name>{$product->getName()}</name><description>{$product->getDescription()}</description><image>{$product->getImage()}</image></product>";
    }
}
// Створення продукту
$product = new Product(1, "Холодильник Bosch ", "279 л об'єм морозильної камери...", "image_url.jpg");

// Рендеринг простої сторінки
$simplePageHtml = new SimplePage(new HTMLRenderer(), "Simple Page  HTML", "Lorem ipsum dolor sit amet.");
echo $simplePageHtml->render() . "\n";

$simplePageJson = new SimplePage(new JsonRenderer(), "Simple Page Json", "Lorem ipsum dolor sit amet.");
echo $simplePageJson->render() . "\n";

$simplePageXml = new SimplePage(new XmlRenderer(), "Simple Page Xml", "Lorem ipsum dolor sit amet.");
echo $simplePageXml->render() . "\n";

// Рендеринг сторінки товару
$productPageHtml = new ProductPage(new HTMLRenderer(), $product);
echo $productPageHtml->render() . "\n";

$productPageJson = new ProductPage(new JsonRenderer(), $product);
echo $productPageJson->render() . "\n";

$productPageXml = new ProductPage(new XmlRenderer(), $product);
echo $productPageXml->render() . "\n";

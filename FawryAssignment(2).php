<?php

abstract class Book
{
    // protected string  $isbn;
    // protected string  $title;
    // protected int     $year;
    // protected float   $price;
    // protected string $author;

    public function __construct(
        protected string $isbn,
        protected string $title,
        protected int    $year,
        protected float  $price,
        protected string $author
    ) {/*  will fill this property automatic */
    }

    abstract public function isAvailable(): bool;
    abstract public function purchase(
        int $quantity,
        string $email,
        string $address,
    ): float;
    abstract public function getType(): string;


    public function getISBN(): string
    {
        return $this->isbn;
    }

    public function getYear(): int
    {
        return $this->year;
    }
    public function __toString(): string {
    return "{$this->title} by {$this->author} ({$this->year}) - {$this->isbn}";
}

}


class PaperBook extends Book
{
    private int $stock;
    public function __construct($isbn, $title, $year, $price, $author, $stock)
    {
        parent::__construct($isbn, $title, $year, $price, $author);
        $this->stock = $stock;
    }
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }


    public function purchase(int $quantity, string $email, string $address): float
    {
        if ($quantity > $this->stock) {
            throw new Exception("Not enough stock for PaperBook");
        }
        $this->stock -= $quantity;
        echo "Quantum book store: Shipping to $address\n";
        return $this->price * $quantity;
    }

    public function getType(): string
    {
        return 'PaperBook';
    }
}

class Ebook extends Book
{
    private string $fileType;

    public function __construct($isbn, $title, $year, $price, $author, $fileType)
    {
        parent::__construct($isbn, $title, $year, $price, $author);
        $this->fileType = $fileType;
    }

    public function isAvailable(): bool
    {
        return true;
    }
    public function purchase(int $quantity, string $email, string $address): float
    {

        echo "Quantum book store: Sending EBook to $email\n";
        return $this->price * $quantity;
    }

    public function getType(): string
    {
        return 'EBook';
    }
}

class DemoBook extends Book
{
    public function isAvailable(): bool
    {
        return false;
    }

    public function purchase(int $quantity, string $email, string $address): float
    {
        throw new Exception("Demo books are not for sale");
    }

    public function getType(): string
    {
        return 'DemoBook';
    }
}


class BookStore
{
    private array $inventory = [];

    public function addBook(Book $book): void
    {
        $this->inventory[$book->getISBN()] = $book;
        echo "Quantum book store: Book added - {$book}\n";
        // echo "Quantum book store: Book added - {$book->getISBN()}\n";
    }

    public function removeOutdatedBooks(int $year)
    {
        $removed = [];
        foreach ($this->inventory as $isbn => $book) {
            if ((date('Y') - $book->getYear()) > $year) {
                unset($this->inventory[$isbn]);
                $removed[] = $book;
                echo "Quantum book store: Removed book $isbn\n";
            }
        }
        return $removed;
    }


    public function buyBook(string $isbn, int $quantity, string $email, string $address): float
    {
        if (!isset($this->inventory[$isbn])) {
            throw new Exception("Book not found");
        }

        $book = $this->inventory[$isbn];

        if (!$book->isAvailable()) {
            throw new Exception("Book is not available for purchase");
        }

        return $book->purchase($quantity, $email, $address);
    }

}



class QuantumBookstoreFullTest {
    public static function run() {
        $store = new BookStore();

        $store->addBook(new DemoBook("D001", "AI Revolution", 2015, 0, "Nour"));
        $store->addBook(new PaperBook("P001", "OOP Mastery", 2020, 150, "Mustafa", 10));
        $store->addBook(new EBook("E001", "Laravel Secrets", 2021, 100, "Ahmed", "PDF"));

        $store->removeOutdatedBooks(4);

        try {
            $store->buyBook("P001", 2, "test@example.com", "Cairo");
            $store->buyBook("E001", 1, "ebook@example.com", ""); // no address needed
            $store->buyBook("D001", 1, "demo@example.com", "");  // should fail
        } catch (Exception $e) {
            echo "Quantum book store: Error - {$e->getMessage()}\n";
        }
    }

    
}
QuantumBookstoreFullTest::run();
// Note: I am currently a third-year student at university.

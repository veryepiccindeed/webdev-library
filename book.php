<?php
session_start();
// Base class: Book


// Base class for books
class Book {
    protected $title;
    protected $author;
    protected $publicationYear;

    public function __construct($title, $author, $publicationYear) {
        $this->title = $title;
        $this->author = $author;
        $this->publicationYear = $publicationYear;
    }

    public function getDetails() {
        return "Title: " . $this->title . ", Author: " . $this->author . ", Year: " . $this->publicationYear;
    }

    public function toArray() {
        return [
            'type' => 'Book',
            'title' => $this->title,
            'author' => $this->author,
            'publicationYear' => $this->publicationYear
        ];
    }

    public static function fromArray($data) {
        return new Book($data['title'], $data['author'], $data['publicationYear']);
    }
}

// EBook class that extends Book
class EBook extends Book {
    private $fileSize;

    public function __construct($title, $author, $publicationYear, $fileSize) {
        parent::__construct($title, $author, $publicationYear);
        $this->fileSize = $fileSize;
    }

    public function getDetails() {
        return parent::getDetails() . ", File Size: " . $this->fileSize . "MB";
    }

    public function toArray() {
        $data = parent::toArray();
        $data['type'] = 'EBook';
        $data['fileSize'] = $this->fileSize;
        return $data;
    }

    public static function fromArray($data) {
        return new EBook($data['title'], $data['author'], $data['publicationYear'], $data['fileSize']);
    }
}

// PrintedBook class that extends Book
class PrintedBook extends Book {
    private $numberOfPages;

    public function __construct($title, $author, $publicationYear, $numberOfPages) {
        parent::__construct($title, $author, $publicationYear);
        $this->numberOfPages = $numberOfPages;
    }

    public function getDetails() {
        return parent::getDetails() . ", Pages: " . $this->numberOfPages;
    }

    public function toArray() {
        $data = parent::toArray();
        $data['type'] = 'PrintedBook';
        $data['numberOfPages'] = $this->numberOfPages;
        return $data;
    }

    public static function fromArray($data) {
        return new PrintedBook($data['title'], $data['author'], $data['publicationYear'], $data['numberOfPages']);
    }
}

// Initialize books in session if not already set
if (!isset($_SESSION['books'])) {
    $_SESSION['books'] = [];
}

// Adding a book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addBook'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publicationYear = intval($_POST['publicationYear']);

    // Create book based on type
    if ($_POST['bookType'] === 'EBook') {
        $fileSize = intval($_POST['fileSize']);
        $newBook = new EBook($title, $author, $publicationYear, $fileSize);
    } else {
        $numberOfPages = intval($_POST['numberOfPages']);
        $newBook = new PrintedBook($title, $author, $publicationYear, $numberOfPages);
    }

    // Add the new book to session as an array
    $_SESSION['books'][] = $newBook->toArray();
    echo "<p>Book added successfully!</p>";
}

// Querying a book by index
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['queryBook'])) {
    $queryIndex = intval($_POST['queryIndex']) - 1; // Convert 1-based index to 0-based

    if (isset($_SESSION['books'][$queryIndex])) {
        $bookData = $_SESSION['books'][$queryIndex];
        $book = null;
        switch ($bookData['type']) {
            case 'EBook':
                $book = EBook::fromArray($bookData);
                break;
            case 'PrintedBook':
                $book = PrintedBook::fromArray($bookData);
                break;
            default:
                $book = Book::fromArray($bookData);
        }
        echo "<p>Book Details: " . $book->getDetails() . "</p>";
    } else {
        echo "<p>Book not found at this index.</p>";
    }
}

// Display all added books
// Display all added books
if (!empty($_SESSION['books'])) {
    echo "<h2>List of Books:</h2>";
    echo "<ul>";
    foreach ($_SESSION['books'] as $index => $bookData) {
        $book = null;
        switch ($bookData['type']) {
            case 'EBook':
                $book = EBook::fromArray($bookData);
                echo "<li>EBook \"" . $book->getDetails() . "\"</li>"; // Menambahkan penanda EBook
                break;
            case 'PrintedBook':
                $book = PrintedBook::fromArray($bookData);
                echo "<li>PrintedBook \"" . $book->getDetails() . "\"</li>"; // Menambahkan penanda PrintedBook
                break;
            default:
                $book = Book::fromArray($bookData);
                echo "<li>Book \"" . $book->getDetails() . "\"</li>"; // Penanda untuk buku umum
        }
    }
    echo "</ul>";
} else {
    echo "<p>No books added yet.</p>";
}

?>

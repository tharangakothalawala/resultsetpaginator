# ResultSet Paginator
This is a simple set of classes that provides you pagination data for a given query. This executes a query using a given database connection (PDO, mysqli).

[![Build Status](https://travis-ci.org/tharangakothalawala/resultsetpaginator.svg?branch=master)](https://travis-ci.org/tharangakothalawala/resultsetpaginator)

## Usage Examples


### Example usage with a PDO connection (ex: Laravel)
```php
use TSK\ResultSetPaginator\PaginationFactory;

$page = empty($_GET['page']) ? 2 : $_GET['page'];
$limit = empty($_GET['limit']) ? 10 : $_GET['limit'];

$paginationFactory = new PaginationFactory(DB::connection()->getPdo(), $page, $limit);
$paginator = $paginationFactory->getPaginator();

/** @var \PDOStatement $stmt */
$stmt = $paginator->query($sql);
$records = $stmt->fetchAll();

$pagination = '';
foreach($paginator->getPagination() as $page) {
    if ($page->isCurrentPage()) {
        $pagination .= " {$page->getDisplayValue()} ";
        continue;
    }

    $pagination .= " <a href='?page={$page->getPageNumber()}&limit={$limit}'>{$page->getDisplayValue()}</a> ";
}

echo $pagination;

```

##### The above example will produce the below output:
<< < 1 ~~2~~ 3 4 5 > >>

### Example usage with a mysqli connection
```php
use TSK\ResultSetPaginator\PaginationFactory;

$page = empty($_GET['page']) ? 2 : $_GET['page'];
$limit = empty($_GET['limit']) ? 10 : $_GET['limit'];

$dbConn = new mysqli('localhost', 'tharanga', 'qwerty', 'test');

$paginationFactory = new PaginationFactory($dbConn, $page, $limit);
$paginator = $paginationFactory->getPaginator();

/** @var \mysqli_result $resultset */
$resultset = $paginator->query($sql);
//while($row = $resultset->fetch_assoc()) {
//    $records[] = $row;
//}

$pagination = '';
foreach($paginator->getPagination() as $page) {
    if ($page->isCurrentPage()) {
        $pagination .= " {$page->getDisplayValue()} ";
        continue;
    }

    $pagination .= " <a href='?page={$page->getPageNumber()}&limit={$limit}'>{$page->getDisplayValue()}</a> ";
}

echo $pagination;

```
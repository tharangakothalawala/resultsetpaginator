# ResultSet Paginator
This is a simple set of classes that provides you pagination data for a given query. This executes a query using a given database connection (PDO, mysqli). Also supports plain pagination.

[![Build Status](https://travis-ci.org/tharangakothalawala/resultsetpaginator.svg?branch=master)](https://travis-ci.org/tharangakothalawala/resultsetpaginator)
[![Total Downloads](https://poser.pugx.org/tharangakothalawala/resultsetpaginator/d/total.svg)](https://packagist.org/packages/tharangakothalawala/resultsetpaginator)
[![Latest Stable Version](https://poser.pugx.org/tharangakothalawala/resultsetpaginator/v/stable.svg)](https://packagist.org/packages/tharangakothalawala/resultsetpaginator)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/tharangakothalawala/resultsetpaginator)

## Usage Examples

### Pagination with known result total count

```php
use TSK\ResultSetPaginator\Paginator\PaginationProvider;

$page = empty($_GET['page']) ? 3 : $_GET['page'];
$limit = empty($_GET['limit']) ? 10 : $_GET['limit'];
$totalResultCount = $laravelModel->count(); // "SELECT COUNT(*) FROM table" for example

$paginationProvider = new PaginationProvider($page, $limit, $totalResultCount);
$paginationProvider->setVisiblePaginationRange(1);

$modelItems => $laravelModel
    ->offset($paginationProvider->offset())
    ->limit($limit)
    ->get();

$pagination = '';
foreach($paginationProvider->pages() as $page) {
    if ($page->isCurrentPage()) {
        $pagination .= " {$page->getDisplayValue()} ";
        continue;
    }

    $pagination .= " <a href='?page={$page->getPageNumber()}&limit={$limit}'>{$page->getDisplayValue()}</a> ";
}

echo $pagination;
```

##### The above example will produce the below output:
<< < 2 ~~3~~ 4 > >>

### Example usage with a PDO connection (ex: Laravel)

```php
use TSK\ResultSetPaginator\QueryExecerFactory;

$page = empty($_GET['page']) ? 2 : $_GET['page'];
$limit = empty($_GET['limit']) ? 10 : $_GET['limit'];

$queryExecerFactory = new QueryExecerFactory(DB::connection()->getPdo(), $page, $limit);
$queryExecer = $queryExecerFactory->getQueryExecer();

/** @var \PDOStatement $stmt */
$stmt = $queryExecer->query($sql);
$records = $stmt->fetchAll();

$pagination = '';
foreach($queryExecer->paginationProvider()->pages() as $page) {
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
use TSK\ResultSetPaginator\QueryExecerFactory;

$page = empty($_GET['page']) ? 2 : $_GET['page'];
$limit = empty($_GET['limit']) ? 10 : $_GET['limit'];

$dbConn = new mysqli('localhost', 'tharanga', 'qwerty', 'test');

$queryExecerFactory = new QueryExecerFactory($dbConn, $page, $limit);
$queryExecer = $queryExecerFactory->getQueryExecer();

/** @var \mysqli_result $resultset */
$resultset = $queryExecer->query($sql);
//while($row = $resultset->fetch_assoc()) {
//    $records[] = $row;
//}

$pagination = '';
foreach($queryExecer->paginationProvider()->pages() as $page) {
    if ($page->isCurrentPage()) {
        $pagination .= " {$page->getDisplayValue()} ";
        continue;
    }

    $pagination .= " <a href='?page={$page->getPageNumber()}&limit={$limit}'>{$page->getDisplayValue()}</a> ";
}

echo $pagination;
```

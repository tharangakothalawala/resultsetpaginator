# ResultSet Paginator
This is a simple set of classes that provides you pagination data for a given query. This executes a query using a given database connection (PDO, mysqli).

## Usage Examples


### Example usage in a Laravel codebase
```php
use TSK\ResultSetPaginator\PaginationFactory;

$page = 2;
$limit = 10;
$paginationFactory = new PaginationFactory(DB::connection()->getPdo(), $page, $limit);
$paginator = $paginationFactory->getPaginator();

/** @var \PDOStatement $stmt */
$stmt = $paginator->query($sql);
$records = $stmt->fetchAll();

$pagination = '';
foreach($paginator->getPagination() as $pageLink) {
	if ($pageLink['isCurrentPage']) {
		$pagination .= " {$pageLink['displayValue']} ";
		continue;
	}

	$pagination .= " <a href='#{$pageLink['pageNumber']}'>{$pageLink['displayValue']}</a> ";
}

```

##### The above example will produce the below output:
<< < 1 ~~2~~ 3 4 5 > >>

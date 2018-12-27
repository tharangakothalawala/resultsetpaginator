### 2.0.0

- A lot of class renames and rearranging. run the following commands to replace in your files accordingly.

```bash
sed "s/PaginationFactory/getQueryExecer/g" file.php --in-place
sed "s/getPaginator/QueryExecerFactory/g" file.php --in-place
sed "s/getPagination/paginationProvider()->pages/g" file.php --in-place
sed "s/getFoundRows/paginationProvider()->totalCount/g" file.php --in-place
```

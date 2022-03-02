## Laravel Query Filter

<details><summary>Installation</summary>
<p>

#### Install the package via composer:

```shell script
composer marksihor/laravel-query-filter
```

#### Publish the config files (needed if You're willing to change configs):

```shell script
php artisan vendor:publish --provider="LaravelQueryFilter\\LaravelQueryFilterServiceProvider" --tag=config
```

</p>
</details>

<details><summary>Usage</summary>
<p>

#### 1. Add "FiltersQueries" trait to Your Controller.php:

```php
namespace App\Http\Controllers;

...
use LaravelQueryFilter\FiltersQueries;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, FiltersQueries;
}

```

#### 2. Use "$this->filter()" method in Your controllers like in example below:

```php
namespace App\Http\Controllers;

...

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $collection = $this->filter(Post::query())->paginate(20);

        return response()->json([
            'data' => $collection
        ]);
    }
}
```

</p>
</details>

<details><summary>Configurations</summary>
<p>

## Filter Settings (configs/laravel_query_filter.php):

#### Model Configuration:

There are two ways to configure Models:

- pass an array of parameters (in this case they will be processed every request)
- pass an anonymous function (in this case extra logic can be provided)

Model settings options (if not provided - the check will not be performed):

- columns - the columns that will be displayed when retrieving records
- relations - the relations that well be allowed to retrieve and filter (empty array - forbids all relations)

```php
[
    ...
    'model_settings' => [
        \App\Models\Post::class => function () {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return [
                'columns' => ['id', 'title', 'text', 'user_id'],
                'relations' => ['comments', 'user']
            ];
        }
           return [];
        },
        \App\Models\User::class => [
            'columns' => ['id', 'name', 'email'],
            'relations' => []
        ]
    ]
]
```

#### Filters Configuration:

The filters of application are listed in the filters array. To disable specific filter, simply delete the corespondent
class from the list. It is easy to add Your own filter:

- Create new filter class in Your application;
- Implement the \LaravelQueryFilter\Filters\FilerInterface interface, and write the logic for the filter;
- Add the created filter to filters list;

#### Publish the config files (needed if You're willing to change configs):

```shell script
php artisan vendor:publish --provider="LaravelQueryFilter\\LaravelQueryFilterServiceProvider" --tag=config
```

</p>
</details>

<details><summary>Query Examples</summary>
<p>

## <span style="color: blue"> Filter by column (\LaravelQueryFilter\Filters\ColumnValuesFilter::class) </span>

Exact match:

> <span style="color: green">example.com/api/posts?name=Post1 </span>

String that contains the substring (surround the serchable string with % character):

> example.com/api/posts?text=%hello%

Starts with the substring (put % character to the end of the serchable string):

> example.com/api/posts?text=Error%

Ends with the substring (put % character to the start of the serchable string):

> example.com/api/posts?text=%provident.

Json column filter (same syntax to find contains, starts with, ends with):

> example.com/api/posts?data->name=John <br>
> example.com/api/posts?data__name=John

## Filter by reserved words (\LaravelQueryFilter\Filters\ColumnValuesFilter::class)

Records where value is null:

> example.com/api/posts?status=null

Records where value is not null:

> example.com/api/posts?status=notNull

Records where date is today:

> example.com/api/posts?created_at=today

Records where date is tomorrow:

> example.com/api/posts?created_at=tomorrow

Records where date is yesterday:

> example.com/api/posts?created_at=yesterday

Records where date is day beforeyesterday:

> example.com/api/posts?created_at=day_before_yesterday

Records where date is more than or equal current:

> example.com/api/posts?created_at=future

Records where date is less than or equal current:

> example.com/api/posts?created_at=past

Records where value is more than or equal to:

> example.com/api/posts?likes[from]=100

Records where value is less than or equal to:

> example.com/api/posts?likes[to]=200

Records where value is in the list:

> example.com/api/posts?status[in]=active,disabled

Records where value is not in the list:

> example.com/api/posts?status[not_in]=active,disabled

## Ordering (\LaravelQueryFilter\Filters\OrderFilter::class)

Order by asc:

> example.com/api/posts?orderBy=title&order=asc

Order by desc:

> example.com/api/posts?orderBy=title&order=desc

Order asc/desc (old way):
> example.com/api/posts?id[orderBy]=asc <br>
> example.com/api/posts?id[orderBy]=desc

## Selecting columns (\LaravelQueryFilter\Filters\SelectColumnsFilter::class)

Select columns by provided comma separated values:

> example.com/api/posts?select=id,title

## Retrieving related records (\LaravelQueryFilter\Filters\WithCountRelationsFilter::class)

### Basic

Direct relations by providing comma separated relation names:

> example.com/api/posts?with=comments,user

Nested relations by providing dot separated relationships structure:

> example.com/api/posts?with=comments.user

### Advanced

Direct relations with extra filters (select, order, filter by column):

> example.com/api/posts?with[comments][select]=id,text,post_id&with[comments][orderBy]=id&with[comments][order]=desc&with[comments][text]=%non%

Nested relations with extra filters (select, with):

> example.com/api/posts?with[user][with]=comments&with[user][select]=id&with[user][with][comments][select]=id,post_id,user_id&select=id,user_id

## With count relationships (\LaravelQueryFilter\Filters\WithCountRelationsFilter::class)

### Basic

Count direct relations by providing comma separated relation names:

> example.com/api/posts?withCount=comments,user

### Advanced

Count direct relations by providing relation and additional filters:

> example.com/api/posts?withCount[comments][user_id]=8

## Retrieving records that has relations (\LaravelQueryFilter\Filters\HasRelationsFilter::class)

### Basic

By providing comma separated relation names:

> example.com/api/posts?has=comments
> example.com/api/posts?has=comments.user

### Advanced

By providing relation names with additional filters:

> example.com/api/posts?has[comments][id]=20

## Retrieving records that does not have relations (\LaravelQueryFilter\Filters\HasNotRelationsFilter::class)

### Basic

By providing comma separated relation names:

> example.com/api/posts?hasNot=comments

### Advanced

By providing relation names with additional filters:

> example.com/api/posts?hasNot[comments][id]=13

</p>
</details>





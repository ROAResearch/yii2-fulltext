Yii2 FullText Support
=====================

Library with migrations and queries to utilize the FullText search funcionality.

Installation
-----------

You can use composer to install the library `roaresearch/yii2-fulltext` by running the
command;

`composer require roaresearch/yii2-fulltext`

or edit the `composer.json` file

```json
require: {
    "roaresearch/yii2-fulltext": "*",
}
```

Usage
-----

### Create Migrations

The migration to create a full text index is meant to be executed after loading
the DB data using Fixtures or other tools.

#### `roaresearch\yii2\fullText\AddFullTextMigration`

Create a new migration for each table to be modified. Remember that FullText
significantly slows the storing of information so its meant to be used sparcely.

```php
use roaresearch\yii2\fullText\AddFullTextMigration;

class m170101_000001_add_fulltext_article extends AddFullTextMigration
{
    public function getTableName()
    {
        return 'article';
    }

    public function fullTextIndexes()
    {
        return [
            'ft-index1' => 'column1',
            'ft-index2' => ['column1', 'column2'],
        ];
    }
}
```

### Create Query

To ease the creation of the SQL expression required for the full text search the
static method `MatchAgainstExpression::matchAgainst()` can be used.

```php
use roaresearch\yii2\fullText\MatchAgainstExpression as MA;

$query = Article::find()
    ->andWhere(
        MA::matchAgainst(['title', 'body'], $text)
    );
```

> Tip: put the full text filter at the very end to optimize the query.

MySQL will naturally order the results based on the full text filter IN MOST
CASES when the ORDER BY part is omited. Still there are exceptions and for that

```php
use roaresearch\yii2\fullText\MatchAgainstExpression as MA;

$ma = MA::matchAgainst(['title', 'body'], $text);
$query = Article::find()
    ->addSelect(['relevance' => $ma])
    ->andWhere($ma)
    ->orderBy(['relevance' => SORT_DESC]);
```

> Tip dont forget to add all the needed columns on the select.
> Tip although it looks like it runs the full text search twice it doesnt since
  internally query parameters are used.

### Search Mode

MySQL supports 3 modes when performing an SQL statement and they are supported
using `ModeEnum`, to make in human understandable names.

They can be used like this

```php
use roaresearch\yii2\fullText\{MatchAgainstExpression as MA, ModeEnum};

MA::matchAgainst(['title', 'body'], $text, ModeEnum::Nat);
MA::matchAgainst(['title', 'body'], $text, ModeEnum::Sym);
MA::matchAgainst(['title', 'body'], $text, ModeEnum::Dbl);

```

#### ModeEnum::Nat

Its the default or natural mode which is the closest to the human language.

Doesnt support symbol operators, cant catch typos or related terms.

Example: 'mysql' any result that includes the word 'mysql'.

#### ModeENum::Sym

The mysql documentation call this mode `boolean` but there is nothing boolean
about it so I will call it symbolic since it allows symbol operators.

Doesnt catch typos or related terms

Example: 'mysql -oracle' result must include 'mysql' but not contain 'oracle'

#### ModeEnum::Dbl

Internally is called query expansion but doesnt expand anything so I call it
double since it runs the search twice to find related terms and fix typos.

Doesnt support symbolic operators.

Example: 'database' would return anything related to databases like 'mysql'
and 'oracle' even if they dont contain the word 'database'.

#### Testing Environment

This library use [Composer Utils](https://github.com/ROAResearch/composer-utils)
to quickly deploy the needed database and testing Environment.

```bash
git clone https://github.com/ROAResearch/yii2-fulltext.git
cd yii2-rmdb/
composer deploy
```

This will ask db credentials, validate them and create the needed database and
structure.

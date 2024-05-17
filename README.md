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
    "roareasearch/yii2-fulltext": "*",
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

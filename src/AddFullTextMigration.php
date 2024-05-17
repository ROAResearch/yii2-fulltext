<?php

namespace roaresearch\yii2\fullText;

/**
 * Class that adds a full text index to a table. Its meant to be executed AFTER
 * loading data on the tables like for example using fixtures.
 *
 * Usage:
 * ```php
 * class M240101000001AddFullTextArticle extends AddFullTextMigration
 * {
 *     public function tableName(): string
 *     {
 *         return 'article';
 *     }
 *
 *     public function fullTextIndexes(): string
 *     {
 *         return [
 *             'ft' => ['title', 'author', 'text', 'tags'],
 *         ];
 *     }
 * }
 * ```
 *
 * Execute the migration as normal with the `migration/up` command.
 *
 * @author Angel (Faryshta) Guevara <aguevara@invernaderolabs.com>
 */
abstract class AddFullTextMigration extends \yii\db\Migration
{
    /**
     * @var string template for the message announcing the full text is being
     *   created
     */
    protected const BEGIN_MSG = 'add full text {name} on {table} ({columns})';

    /**
     * @var string SQL to be executed, the params `table`, `index` and `columns`
     *   will be replaced by the configured values.
     */
    protected const ADD_FULLTEXT_SQL = <<<SQL
       ALTER TABLE {table}
       ADD FULLTEXT INDEX {index} ({columns})
       SQL;

    /**
     * @return string name of the table where the index will be added
     */
    abstract public function tableName(): string;

    /**
     * @return array pairs of index_name => columns that conform a full text
     *   index. The columns can be a single string or an array.
     *
     *
     * ```php
     * return [
     *     'ft_content' => ['title', 'body'],
     *     'ft_tags' => 'tags',
     * ];
     * ```
     */
    abstract public function fullTextIndexes(): array;

    /**
     * @inheritdoc
     */
    public function up()
    {
        $table = $this->tableName();
        foreach ($this->fullTextIndexes() as $index => $columns) {
            $this->createFullTextIndex($index, $table, (array) $columns);
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $table = $this->tableName();
        foreach (array_keys($this->fullTextIndexes()) as $index) {
            $this->dropIndex($index, $table);
        }
    }

    /**
     * Creates a FullText index on a table.
     *
     * @param string $index name of the index
     * @param string $table name of the table
     * @param string[] $columns the set of column names
     */
    public function createFullTextIndex(
        string $index,
        string $table,
        array $columns
    ): void {
        $cols = implode(', ', $columns);
        $time = $this->beginCommand(strtr(static::BEGIN_MSG, [
            '{name}' => $name,
            '{table}' => $table,
            '{columns}' => $cols,
        ]));
        $this->db->createCommand(strtr(static::ADD_FULLTEXT_SQL, [
            '{name}' => $name,
            '{table}' => $table,
            '{columns}' => $cols,
        ]))->execute();
        $this->endCommand($time);
    }
}

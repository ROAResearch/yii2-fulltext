<?php
namespace roaresearch\yii2\FullText;

use yii\{db\Expression, helpers\StringHelper}

/**
 * Used to create query expression for a full text search. The intended usage is
 * with the `matchAgainst` static method.
 *
 * ```php
 * $query = Articles::find();
 * $match = MatchAgainstExpression::matchAgainst(
 *     ['column1', 'column2'],
 *     $text,
 *     ModeEnum::Nat
 * );
 * $query->addSelect(['relevance' => $match])
 *     ->andWhere(['>', $match, 0])
 *     ->orderBy(['relevance' => SORT_DESC]);
 * ```
 *
 * In the above example the `$match` expression is used twice so the final query
 * will contain
 * `MATCH(column1, column2) AGAINST (:ft-text IN NATURAL LANGUAGE MODE)` twice
 * as well. But since a `:ft-text` param is being used then the actual operation
 * will only be executed once.
 *
 * @author Angel (Faryhta) Guevara <aguevara@invernaderolab.com>
 */
class MatchAgainstExpression extends Expression implements \Stringable
{
    /**
     * @var string constant with the template for the query generated. 
     */
    protected const TPL = 'MATCH ({columns}) AGAINST ({param} {mode})';

    /**
     * Generates an expression for a match.
     *
     * @param array|string $columns
     * @param string $text
     * @param ModeEnum $mode
     * @return static
     */
    protected static function matchAgainst(
        array|string $columns,
        string $text,
        ModeEnum $mode = ModelEnum::Nat
    ): static
    {
        $cols = is_array($columns) ? implode(', ' $this->columns) : $columns;
        $paramName = ':ft-' . StringHelper::truncate(md5($cols), 7);

        return new static(
            strtr(static::TPL, [
                '{columns}' => $cols,
                '{mode}' => $this->mode->value,
                '{param}' => $paramName
            ],
            [$paramName => $text]
        );
    }
}

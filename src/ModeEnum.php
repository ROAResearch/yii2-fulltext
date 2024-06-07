<?php

namespace roaresearch\yii2\FullText;

enum ModeEnum: string
{
    case Nat = 'IN NATURAL LANGUAGE MODE';
    case Sym = 'IN BOOLEAN MODE';
    case Dbl = 'WITH QUERY EXPANSION';
}

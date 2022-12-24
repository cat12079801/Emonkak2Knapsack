<?php declare(strict_types=1);
require_once 'vendor/autoload.php';
require_once 'lib/ReplacementAgent.php';
require_once 'lib/SpyNodeTraverser.php';
require_once 'lib/TargetFiles.php';

use PhpParser\{
    Lexer,
    Node,
    NodeDumper,
    NodeVisitor,
    NodeVisitorAbstract,
    Node\Expr\Assign,
    Node\Expr\Closure,
    Node\Expr\MethodCall,
    Node\Expr\StaticCall,
    Node\Expr\Variable,
    Node\Identifier,
    Node\Name,
    Node\Scalar\Encapsed,
    Node\Scalar\LNumber,
    Node\Scalar\String_,
    Node\Stmt\Function_,
    Node\Stmt\UseUse,
    Parser,
    ParserFactory,
    PrettyPrinter,
    ReplacementAgent,
    SpyNodeTraverser
};

$isDryRun = in_array('--dry-run', $argv, true);

$lexer = new Lexer\Emulative([
    'usedAttributes' => [
    'comments',
    'startLine', 'endLine',
    'startTokenPos', 'endTokenPos',
    ],
]);
$parser = new Parser\Php7($lexer);

$traverser = new SpyNodeTraverser();
$traverser->addVisitor(new class extends NodeVisitorAbstract {
    // String Selector Pattern
    const SSP = '/^\[.+\]$/';

    public function leaveNode(Node $node) {
        if ($node instanceOf UseUse) {
            $useOriginal = implode('\\', $node->name->parts);
            if ($useOriginal === 'Emonkak\Collection\Collection') {
                return new ReplacementAgent(
                    $node->getStartLine(),
                    '/Emonkak\\\\Collection\\\\Collection/',
                    'DusanKasan\Knapsack\Collection'
                );
            }
        }

        /**
         * Collectionを変数に格納している場合洗い出し
         *
        if ($node instanceof Assign && self::isCollectionClassMethodChainRecursive($node->expr)) {
            if (in_array($node->expr->name->name, [
                'toList', 'toArray', 'any', 'maxWith', 'intercalate', 'sum',
                'toListRec', 'find', 'all', 'findWhere'
            ])) {
                return null;
            }
            if ($node->expr->name->name === 'first' && $node->expr->args === []) {
                return null;
            }
            echo "  L{$node->getStartLine()} {$node->expr->name->name}\n";
        }
         */

        // migrate_user_authentication/migrate.php L220でCollectionを変数に格納している対応
        if ($node->name->name === 'initial' && $node->var->name === 'sortedEndpoints') {
            return new ReplacementAgent(
                $node->name->getStartLine(),
                '/initial\(\)/',
                'dropLast()->values()'
            );
        }
        if ($node->name->name === 'rest' && $node->var->name === 'sortedEndpoints') {
            return new ReplacementAgent(
                $node->name->getStartLine(),
                '/rest\(\)/',
                'except([0])->values()'
            );
        }

        // Collection:: 以下のメソッドチェーン以外はnullを返す
        if (!self::isCollectionClassMethodChainRecursive($node)) {
            // ただし 'convertTargetableVariable' という変数から伸びるメソッドチェーンは対象とする
            if (self::getBaseVarName($node) !== 'convertTargetableVariable') {
                return null;
            }
        }

        if ($node instanceOf MethodCall) {
            if ($node->name->name === 'map') {
                if ($node->args[0]->value instanceof String_) {
                    $value = self::bothSidesTrim($node->args[0]->value->value);
                    return new ReplacementAgent(
                        $node->args[0]->getStartLine(),
                        "/'\[" . $value . "\]'/",
                        'static function($x) { return $x[\''
                            . $value
                            . '\']; }'
                    );
                } else if (count($node->args) === 2) {
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/map\(((?:(?:static )?function ?\([\w\$, ]+\) ?{.+}(?:, +)?){2,})\)/',
                        'transform((new \LogicLinks\Transformer())->mapValueKey(\1))'
                    );
                }
            }

            if ($node->name->name === 'groupBy') {
                if ($node->args[0]->value instanceof String_) {
                    $value = self::bothSidesTrim($node->args[0]->value->value);
                    return new ReplacementAgent(
                        $node->args[0]->getStartLine(),
                        "/groupBy\('\[" . $value . "\]'\)/",
                        [
                            "groupByKey('" . $value . "')",
                            "->map('DusanKasan\\Knapsack\\toArray')"
                        ]
                    );
                } else if ($node->args[0]->value instanceof Closure) {
                    return new ReplacementAgent(
                        $node->args[0]->value->getEndLine(),
                        '/(?<=})\)/',
                        [
                            ")",
                            "->map('DusanKasan\\Knapsack\\toArray')"
                        ]
                    );
                }
            }

            if ($node->name->name === 'sum') {
                if ($node->args[0]->value instanceof String_) {
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/sum\([\'"]\[(\w+)\][\'"]\)/',
                        [
                            "map(function(\$x) { return \$x['\\1']; })",
                            "->sum()"
                        ]
                    );
                } else if ($node->args[0]->value instanceof Closure) {
                    return [
                        new ReplacementAgent(
                            $node->name->getStartLine(),
                            '/sum/',
                            'map'
                        ),
                        new ReplacementAgent(
                            $node->args[0]->value->getEndLine(),
                            '/(?<=})\)/',
                            [')', '->sum()']
                        ),
                    ];
                }
            }

            if ($node->name->name === 'toList') {
                if (self::haveParentMethod($node, 'values') ||
                    self::haveParentMethod($node, 'concat')) {
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/toList/',
                        'toArray'
                    );
                }
                return new ReplacementAgent(
                    $node->name->getStartLine(),
                    '/toList\(\)/',
                    ['values()', '->toArray()']
                );
            }

            if ($node->name->name === 'toListRec') {
                return new ReplacementAgent(
                    $node->name->getStartLine(),
                    '/toListRec\(\)/',
                    [
                        'transform((new \LogicLinks\Transformer())->toListRec())',
                        '->toArray()'
                    ]
                );
            }

            if ($node->name->name === 'without') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/without\(/',
                        'diff(['
                    ),
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/\)/',
                        '])'
                    ),
                ];
            }

            if (in_array($node->name->name, ['any', 'some'])) {
                if ($node->args === []) {
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/' . $node->name->name . '\(\)/',
                        'some(static function($x) { return $x == true; })'
                    );
                } else if ($node->args[0]->value instanceof Closure) {
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/' . $node->name->name . '/',
                        'some'
                    );
                } else if ($node->args[0]->value instanceof String_) {
                    $value = self::bothSidesTrim($node->args[0]->value->value);
                    if (preg_match(self::SSP, $value)) {
                    } else {
                        return new ReplacementAgent(
                            $node->name->getStartLine(),
                            "/" . $node->name->name. "\('\[" . $value . "\]'/",
                            'some(static function($x) { return $x[\''
                                . $value
                                . '\'] == true; }'
                        );
                    }
                }
            }

            if (in_array($node->name->name, ['all', 'every'])) {
                if ($node->args === []) {
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/' . $node->name->name . '\(\)/',
                        'every(static function($x) { return $x == true; })'
                    );
                } else if ($node->args[0]->value instanceof Closure) {
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/' . $node->name->name . '/',
                        'every'
                    );
                } else if ($node->args[0]->value instanceof String_) {
                    $value = self::bothSidesTrim($node->args[0]->value->value);
                    if (preg_match(self::SSP, $value)) {
                    } else {
                        return new ReplacementAgent(
                            $node->name->getStartLine(),
                            "/" . $node->name->name . "\('\[" . $value . "\]'/",
                            'every(static function($x) { return $x[\''
                                . $value
                                . '\'] == true; }'
                        );
                    }
                }
            }

            if ($node->name->name === 'minWith') {
                if ($node->args[0]->value instanceof Closure) {
                    return [
                        new ReplacementAgent(
                            $node->name->getStartLine(),
                            '/minWith/',
                            'sort'
                        ),
                        new ReplacementAgent(
                            $node->args[0]->value->getEndLine(),
                            '/(?<=})\)/',
                            [')', '->first()']
                        ),
                    ];
                } else {
                    throw new \UnexpectedValueException();
                }
            }

            if ($node->name->name === 'maxWith') {
                if ($node->args[0]->value instanceof Closure) {
                    return [
                        new ReplacementAgent(
                            $node->name->getStartLine(),
                            '/maxWith/',
                            'sort'
                        ),
                        new ReplacementAgent(
                            $node->args[0]->value->getEndLine(),
                            '/(?<=})\)/',
                            [')', '->last()']
                        ),
                    ];
                } else {
                    throw new \UnexpectedValueException();
                }
            }

            if ($node->name->name === 'sortBy') {
                if ($node->args[0]->value instanceof Closure) {
                    return [
                        new ReplacementAgent(
                            $node->name->getStartLine(),
                            '/sortBy\(/',
                            'transform((new \LogicLinks\Transformer())->sortBy('
                        ),
                        new ReplacementAgent(
                            $node->args[0]->value->getEndLine(),
                            '/(?<=})\)/',
                            '))'
                        ),
                    ];
                } else if ($node->args[0]->value instanceof String_) {
                    if (preg_match(self::SSP, $node->args[0]->value->value) !== 1) {
                        throw new \UnexpectedValueException();
                    }
                    $value = self::bothSidesTrim($node->args[0]->value->value);
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        "/sortBy\('\[" . $value . "\]'\)/",
                        "sort(static function(\$x, \$y) { return \$x['$value'] <=> \$y['$value']; })"
                    );
                }
            }

            if ($node->name->name === 'each') {
                return new ReplacementAgent(
                    $node->args[0]->getEndLine(),
                    '/\);/',
                    [')', '->toArray();']
                );
            }

            if ($node->name->name === 'uniq') {
                if ($node->args === []) {
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/uniq\(\)/',
                        'transform((new \LogicLinks\Transformer())->unique())'
                    );
                } else if ($node->args[0]->value instanceof Closure) {
                    return [
                        new ReplacementAgent(
                            $node->name->getStartLine(),
                            '/uniq\(/',
                            'transform((new \LogicLinks\Transformer())->unique('
                        ),
                        new ReplacementAgent(
                            $node->args[0]->value->getEndLine(),
                            '/(?<=})\)/',
                            '))'
                        ),
                    ];
                }
            }

            if (in_array($node->name->name, ['flatMap', 'concatMap'])) {
                return new ReplacementAgent(
                    $node->name->getStartLine(),
                    '/' . $node->name->name . '/',
                    'mapcat'
                );
            }

            if ($node->name->name === 'findWhere') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/findWhere\(\[[\'"]\[/',
                        'transform((new \LogicLinks\Transformer())->findWhere(\''
                    ),
                    new ReplacementAgent(
                        $node->args[0]->getStartLine(),
                        '/\][\'"]\s+=>\s+/',
                        '\', '
                    ),
                    new ReplacementAgent(
                        $node->args[0]->getEndLine(),
                        '/\]\)/',
                        ['))->first()']
                    ),
                ];
            }

            if ($node->name->name === 'difference') {
                return new ReplacementAgent(
                    $node->name->getStartLine(),
                    '/difference/',
                    'diff'
                );
            }

            if ($node->name->name === 'intercalate') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/intercalate/',
                        'interpose'
                    ),
                    new ReplacementAgent(
                        $node->getEndLine(),
                        '/((?:intercalate|interpose)\([\'"][^\(\)]*[\'"]\))/',
                        '\1->toString()'
                    ),
                ];
            }

            if ($node->name->name === 'invoke') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/invoke/',
                        'transform((new \LogicLinks\Transformer())->invoke'
                    ),
                    new ReplacementAgent(
                        $node->getEndLine(),
                        '/(?<=[\'"])\)/',
                        '))'
                    ),
                ];
            }

            if ($node->name->name === 'object') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/object/',
                        'transform((new \LogicLinks\Transformer())->object'
                    ),
                    new ReplacementAgent(
                        $node->getEndLine(),
                        '/\)$/',
                        '))'
                    ),
                ];
            }

            if ($node->name->name === 'omit') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/omit\((?=[\'"])/',
                        'except(['
                    ),
                    new ReplacementAgent(
                        $node->getEndLine(),
                        '/(?<=[\'"])\)/',
                        '])'
                    ),
                ];
            }

            if ($node->name->name === 'pick') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/pick\(/',
                        'only(['
                    ),
                    new ReplacementAgent(
                        $node->getEndLine(),
                        '/(?<=[\'"])\)/',
                        '])'
                    ),
                ];
            }

            if ($node->name->name === 'pluck') {
                if ($node->args[0]->value instanceof String_) {
                    return [
                        new ReplacementAgent(
                            $node->name->getStartLine(),
                            '/pluck\([\'"]\[/',
                            'map(static function($x) { return $x[\''
                        ),
                        new ReplacementAgent(
                            $node->name->getEndLine(),
                            '/\][\'"]\)/',
                            '\']; })'
                        ),
                    ];
                } else if ($node->args[0]->value instanceof Encapsed) {
                    $var = $node->args[0]->value->parts[1]->name;
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        "/pluck\(['\"]\[\{?\\$$var\}?\]['\"]\)/",
                        "map(static function(\$x) use (\$$var) { return \$x[\$$var]; })"
                    );
                }
            }

            if ($node->name->name === 'sort') {
                if ($node->args === []) {
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/sort\(\)/',
                        'sort(function($x, $y) { return $x <=> $y; })'
                    );
                } else if ($node->args[0]->value instanceof Closure) {
                    // そのままでok
                }
            }

            if ($node->name->name === 'where') {
                if ($node->args[0]->value->items[0]->value instanceof MethodCall) {
                    $key = self::bothSidesTrim($node->args[0]->value->items[0]->key->value);
                    [$var, $methodChainStr] = self::makeMethodChain($node->args[0]->value->items[0]->value);
                    $quotedChainStr = preg_quote($methodChainStr);
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        "/where\(\[['\"]\[$key\]['\"] => $quotedChainStr\]\)/",
                        "filter(static function(\$v) use (\$$var) { return \$v['$key'] === $methodChainStr; })"
                    );
                } else {
                    $key = self::bothSidesTrim($node->args[0]->value->items[0]->key->value);
                    $value = $node->args[0]->value->items[0]->value->value;
                    return new ReplacementAgent(
                        $node->name->getStartLine(),
                        "/where\(\[['\"]\[$key\]['\"] => $value\]\)/",
                        "filter(static function(\$v) { return \$v['$key'] === $value; })"
                    );
                }
            }

            if ($node->name->name === 'join') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/join\(/',
                        'transform((new \LogicLinks\Transformer())->join('
                    ),
                    new ReplacementAgent(
                        $node->getEndLine(),
                        '/\)$/',
                        '))'
                    ),
                ];
            }

            if ($node->name->name === 'groupJoin') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/groupJoin\(/',
                        'transform((new \LogicLinks\Transformer())->leftOuterJoin('
                    ),
                    new ReplacementAgent(
                        $node->getEndLine(),
                        '/\)$/',
                        '))'
                    ),
                ];
            }

            if ($node->name->name === 'memoize') {
                return [
                    new ReplacementAgent(
                        $node->name->getStartLine(),
                        '/ *->memoize\(\);/',
                        ';'
                    ),
                    new ReplacementAgent(
                        $node->name->getStartLine() - 1,
                        "/\n$/",
                        ''
                    ),
                ];
            }

            if ($node->name->name === 'zipWith') {
                return new ReplacementAgent(
                    $node->name->getStartLine(),
                    '/zipWith\(((?:\.\.\.)?\$\w+|\[(?:\$?[0-9\w]+(?:, +)?)+\])\)/',
                    'transform((new \LogicLinks\Transformer())->zipWith(\1))'
                );
            }
        }

        if ($node instanceOf StaticCall) {
            if ($node->name->name === 'concat') {
                if ($node->getStartLine() !== $node->getEndLine()) {
                    // 改行されてる場合
                    return [
                        new ReplacementAgent(
                            $node->getStartLine(),
                            '/concat/',
                            'from'
                        ),
                        new ReplacementAgent(
                            $node->getEndLine(),
                            '/(^ +|\]|concat\(\$\w+)\)/',
                            ['\1)', '->flatten(1)', '->values()']
                        ),
                    ];
                } else {
                    // 一行で書かれている場合。「)」がconcat()の外にある場合を考慮
                    // concat()の中で「)」が使われることは考慮しない。配列か変数のみを許容する(つもり
                    return [
                        new ReplacementAgent(
                            $node->getStartLine(),
                            '/(concat\((\$\w+|[\[\]0-9, ]+)\))/',
                            ['\1', '->flatten(1)', '->values()']
                        ),
                        new ReplacementAgent(
                            $node->getStartLine(),
                            '/concat/',
                            'from'
                        ),
                    ];
                }
            }

            if ($node->name->name === 'zip') {
                return new ReplacementAgent(
                    $node->name->getStartLine(),
                    '/zip\(\[(.+)\]\)/',
                    "from([\\1])->map(function(\$x){ return Collection::from(\$x); })->transpose()->map('\\DusanKasan\\Knapsack\\toArray')"
                );
            }

            if ($node->name->name === 'range') {
                if (!$node->args[0]->value instanceof LNumber) {
                    throw new \UnexpectedValueException('その型は未対応');
                }
                if (count($node->args) !== 1) {
                    throw new \UnexpectedValueException('引数1つの場合しか対応してない');
                }

                $end = $node->args[0]->value->value - 1;
                return new ReplacementAgent(
                    $node->name->getStartLine(),
                    '/(?<=range\()(\d+)(?=\))/',
                    "0, $end"
                );
            }
        }

        return null;
    }

    private static function bothSidesTrim(string $s): string {
        return substr($s, 1, strlen($s) - 2);
    }

    private static function isCollectionClassMethodChainRecursive($node): bool {
        if ($node->var instanceof MethodCall) {
            return self::isCollectionClassMethodChainRecursive($node->var);
        } else if ($node->var instanceof StaticCall) {
            return $node->var->class->parts[0] === 'Collection';
        } else if ($node instanceof StaticCall) {
            return $node->class->parts[0] === 'Collection';
        }

        // $collection = Collection::from([1, 2, 3]);
        // $collection->sort()->toList(); ← falseを返す
        // みたいなコードに遭遇した場合は見つけられない
        // sort() の木を読んでも $collection が Collection クラスの
        // インスタンスであることはわからない。
        return false;
    }

    private static function getBaseVarName($node): ?string {
        if ($node instanceof MethodCall) {
            return self::getBaseVarName($node->var);
        } else if ($node instanceof Variable) {
            return $node->name;
        } else {
            return null;
        }
    }

    private static function haveParentMethod(Node $node, string $name): bool {
        if ($node->name->name === $name) {
            return true;
        } else if ($node->var !== null) {
            return self::haveParentMethod($node->var, $name);
        } else {
            return false;
        }
    }

    /**
     * @param Node $node
     * @return array [string variableName, string makedMethodChain]
     */
    private static function makeMethodChain(Node $node): array {
        // メソッドチェーン中の引数等、汎用的な考慮はしていない
        if ($node instanceof MethodCall) {
            [$var, $methodChainStr] = self::makeMethodChain($node->var);
            return [$var, "$methodChainStr->{$node->name}()"];
        } else if ($node instanceof Variable) {
            return [$node->name, "\${$node->name}"];
        } else {
            throw new \UnexpectedValueException('型がおかしい');
        }
    }
});

$printer = new PrettyPrinter\Standard();

foreach ($targetFiles as $filepath) {
    // echo "$filepath...";

    if (!file_exists($filepath)) {
        // echo "skip\n";
        continue;
    }
    $code = file_get_contents($filepath);

    $stmts = $parser->parse($code);

    $traverser->traverse($stmts);
    $replaceInfo = $traverser->getReplaceInfo();
    $traverser->clearReplaceInfo();

    $file = file($filepath);
    if ($isDryRun) {
        echo $filepath."\n";
    }
    foreach($replaceInfo as $info) {
        $info->setExtraInfo($file, $filepath);
        if ($isDryRun) {
            $info->replaceDryRun();
        } else {
            $file[$info->targetLineIdx] = $info->replace();
        }
    }
    file_put_contents($filepath, $file);

    $dumper = new NodeDumper([
        'dumpComments' => true,
        'dumpPositions' => true,
    ]);
    // file_put_contents("$filepath.oldast", $dumper->dump($stmts));
}

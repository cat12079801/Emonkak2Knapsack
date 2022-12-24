<?php

namespace LogicLinks;

use Closure;
use DusanKasan\Knapsack\Collection;

/**
 * Emonkak\Collection\Collection
 * から
 * Dusankasan\Knapsack\Collection
 * に乗り換える際に不足しているメソッドをtransformで使えるように実装
 */
class Transformer {
    /**
     * Emonkakだと '[hoge]' のようにセレクタを文字列で渡せたがここでは対応しない
     * @param null|callable $src
     * @return callable
     */
    private static function resolveSelector($src): callable {
        if ($src === null) {
            // 与えられた要素をそのまま返す
            return static function($x) { return $x; };
        }
        if (is_callable($src)) {
            return $src;
        }

        $type = gettype($src);
        throw new \InvalidArgumentException("Invalid selector, got '$type'.");
    }

    private static function getInnerJoinedKeys(array $a, array $b): array {
        $result = [];
        foreach($a as $a_k => $a_v) {
            $b_keys = array_keys($b, $a_v);
            foreach($b_keys as $b_k) {
                $result[] = [$a_k, $b_k];
            }
        }
        return $result;
    }

    private static function getLeftOuterJoinedKeys(array $a, array $b): array {
        $result = [];
        foreach($a as $a_k => $a_v) {
            $b_keys = array_keys($b, $a_v);
            if ($b_keys === []) $b_keys = [null];
            foreach($b_keys as $b_k) {
                $result[] = [$a_k, $b_k];
            }
        }
        return $result;
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#sortby-callable-valueselector--collection
     * EmonkakのsortBy()に相当する操作をするクロージャを返す
     * @param Closure $callback
     * @return Closure
     */
    public static function sortBy(Closure $callback): Closure {
        return static function(Collection $c) use ($callback): Collection {
            return $c
                ->map(static function($x) use ($callback) {
                    return ['k' => $callback($x), 'v' => $x];
                })
                ->sort(static function($x, $y) { return $x['k'] <=> $y['k']; })
                ->map(static function($x) { return $x['v']; });
        };
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#uniq-callable-valueselector--collection
     * Emonkakのuniq()に相当する操作をするクロージャを返す
     * 引数に関数を与えた場合その結果の値を基に比較を行う
     * その際、異なる値に対して関数が同じ結果を返した場合最初にその結果を返した値を返却する
     * また、引数の有無に関わらず順番は保証される
     * @param Closure|null $callback
     * @return Closure
     */
    public static function unique($callback = null): Closure {
        $callback = $callback ?? static function($x) { return $x; };

        return static function(Collection $c) use ($callback): Collection {
            $dic = [];
            foreach($c->values()->toArray() as $a) {
                $k = $callback($a);
                if (empty($dic[$k])) {
                    $dic[$k] = $a;
                }
            }
            return Collection::from($dic);
        };
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#findwherearray-properties-mixed
     * EmonkakのfindWhere()に相当する操作をするクロージャを返す
     * ただし、transformにわたすクロージャはCollectionを返す必要がある
     * 完全に同値の結果を受け取りたい場合は返却されたCollectionに ->first() する
     * @param string $key
     * @param mixed $value
     * @return Closure
     */
    public static function findWhere(string $key, $value): Closure {
        return static function(Collection $c) use ($key, $value): Collection {
            $result = $c
                ->find(static function($v) use ($key, $value) {
                    return $v[$key] === $value;
                }, null);
            return Collection::from([$result]);
        };
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#invokestring-method--mixed-argments-collection
     * Emonkakのinvoke()に相当する操作をするクロージャを返す
     * @param string $method
     * @param mixed ...$args
     * @return Closure
     */
    public static function invoke(string $method, ...$args): Closure {
        return static function(Collection $c) use ($method, $args): Collection {
            return $c
                ->map(static function($x) use ($method, $args) {
                    return call_user_func_array([$x, $method], $args);
                });
        };
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#object-array-values--collection
     * Emonkakのobject()に相当する操作をするクロージャを返す
     * @param array|null $values
     * @return Closure
     */
    public static function object(array $values = null): Closure {
        return static function(Collection $c) use ($values): Collection {
            if ($values === null) {
                $f = static function($x) {
                    return [$x[0] => $x[1]];
                };
            } else {
                $f = static function($x, $key) use ($values) {
                    return [$x => $values[$key]];
                };
            }

            return $c->map($f)->flatten();
        };
    }

    /**
     * @param Collection $c
     * @return array
     */
    private static function recursive(Collection $c): array {
        $result = [];
        foreach($c->values()->toArray() as $item) {
            if ($item instanceof Collection) {
                $item = self::recursive($item);
            }
            if (is_array($item)) {
                $item = self::recursive(Collection::from($item));
            }
            $result[] = $item;
        }
        return $result;
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#tolistrec-mixed
     * EmonkakのtoListRec()に相当する操作をするクロージャを返す
     * @return Closure
     */
    public static function toListRec(): Closure {
        return static function(Collection $c): Collection {
            $result = self::recursive($c);
            return Collection::from($result);
        };
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#joinmixed-inner-callable-outerkeyselector-callable-innerkeyselector-callable-resultvalueselector-collection
     * Emonkakのjoin()に相当する操作をするクロージャを返す
     * ただし各種セレクタに文字列は受け付けない
     * @param Collection|array $inner
     * @param null|callable $outerKeySelector
     * @param null|callable $innerKeySelector
     * @param callable $resultValueSelector
     * @return Closure
     */
    public static function join($inner, $outerKeySelector, $innerKeySelector, callable $resultValueSelector): Closure {
        if ($inner instanceof Collection) {
            // $inner is already Collection
        } else if (is_array($inner)) {
            $inner = Collection::from($inner);
        } else {
            $type = gettype($inner);
            throw new \InvalidArgumentException("Invalid inner type, got '$type'.");
        }

        $outerKeySelector = self::resolveSelector($outerKeySelector);
        $innerKeySelector = self::resolveSelector($innerKeySelector);

        return static function(Collection $outer) use ($inner, $outerKeySelector, $innerKeySelector, $resultValueSelector): Collection {
            $outerKeys = $outer->map($outerKeySelector);
            $innerKeys = $inner->map($innerKeySelector);
            $keysArray = self::getInnerJoinedKeys($outerKeys->toArray(), $innerKeys->toArray());

            $result = [];
            $outer = $outer->toArray();
            $inner = $inner->toArray();
            foreach($keysArray as list($outerKey, $innerKey)) {
                $result[] = $resultValueSelector(
                    $outer[$outerKey],
                    $inner[$innerKey]
                );
            }
            return Collection::from($result);
        };
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#joinmixed-inner-callable-outerkeyselector-callable-innerkeyselector-callable-resultvalueselector-collection
     * EmonkakのgroupJoin()の一部の操作を可能とするクロージャを返す
     *
     * EmonkakのgroupJoinの振る舞いが不可思議であったためその振る舞いを再現はしていない
     * 具体的にはグループ化の挙動が不明であったこと、対象リポジトリの既存実装でjoinの機能を利用していなかったため
     * 単なるleft outer joinとして実装し直している
     * また各種セレクタに文字列は受け付けない
     * @param Collection|array $inner
     * @param null|callable $outerKeySelector
     * @param null|callable $innerKeySelector
     * @param callable $resultValueSelector
     * @return Closure
     */
    public static function leftOuterJoin($inner, $outerKeySelector, $innerKeySelector, callable $resultValueSelector): Closure {
        if ($inner instanceof Collection) {
            // $inner is already Collection
        } else if (is_array($inner)) {
            $inner = Collection::from($inner);
        } else {
            $type = gettype($inner);
            throw new \InvalidArgumentException("Invalid inner type, got '$type'.");
        }

        $outerKeySelector = self::resolveSelector($outerKeySelector);
        $innerKeySelector = self::resolveSelector($innerKeySelector);

        return static function(Collection $outer) use ($inner, $outerKeySelector, $innerKeySelector, $resultValueSelector): Collection {
            $outerKeys = $outer->map($outerKeySelector);
            $innerKeys = $inner->map($innerKeySelector);
            $keysArray = self::getLeftOuterJoinedKeys($outerKeys->toArray(), $innerKeys->toArray());

            $result = [];
            $outer = $outer->toArray();
            $inner = $inner->toArray();
            foreach($keysArray as list($outerKey, $innerKey)) {
                $result[] = $resultValueSelector(
                    $outer[$outerKey],
                    $inner[$innerKey]
                );
            }
            return Collection::from($result);
        };
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#mapcallable-valueselector-callable-keyselector-collection
     * Emonkakのmap()の第二引数が存在する場合の挙動に対応する
     * valueSelector, keySelectorは引数として「$value, $key, 元のコレクションの配列」を受け取る
     *
     * @param null|callable $valueSelector
     * @param null|callable $keySelector
     * @return Closure
     */
    public static function mapValueKey(callable $valueSelector, callable $keySelector): Closure {
        return static function(Collection $c) use ($valueSelector, $keySelector): Collection {
            $c = $c->toArray();
            $result = [];
            foreach($c as $k => $v) {
                $result[$keySelector($v, $k, $c)] = $valueSelector($v, $k, $c);
            }
            return Collection::from($result);
        };
    }

    /**
     * https://github.com/emonkak/php-collection/wiki#zipwithmixed-xss-mixed-collection
     * EmonkakのzipWith()相当の操作を行うクロージャを返す
     * 元のコレクション、引数として渡された配列ともにkeyは破棄して操作を行います
     *
     * @param mixed ...$arrays
     * @return Closure
     */
    public static function zipWith(...$arrays): Closure {
        return static function(Collection $c) use ($arrays): Collection {
            $collection = Collection::from(
                array_merge([$c->values()->toArray()], $arrays)
            )->map(static function ($v) { return array_values($v); });

            $minIdx = $collection
                ->reduce(static function($minIdx, $x) {
                    if (count($x) < $minIdx) {
                        $minIdx = count($x);
                    }
                    return $minIdx;
                }, $c->size());

            return $collection
                ->map(static function($x) use ($minIdx) {
                    return Collection::from($x)
                        ->filter(static function($v, $k) use ($minIdx) {
                            return $k < $minIdx;
                        });
                })
                ->transpose()
                ->map('\\DusanKasan\\Knapsack\\toArray');
        };
    }
}

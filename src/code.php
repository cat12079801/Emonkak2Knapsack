<?php
require 'vendor/autoload.php';
require_once 'src/Transformer.php';

use Emonkak\Collection\Collection;

function hoge() {
    echo 'neko';
    echo 100;
}

class CollectionClass {
    public static function sample() {
        return Collection::from([['foo' => 1], ['foo' => 2]])
            ->map('[foo]')
            ->toList();
        // => [1, 2]
    }

    public static function from($a) {
        return Collection::from($a)
            ->toList();
    }

    public static function fromTraversable($a) {
        return Collection::from(new \ArrayIterator($a))
            ->toList();
    }

    // $a: 配列の配列
    public static function concat($a) {
        return Collection::concat($a)
            ->toList();
    }

    public static function concatFixedArray() {
        return Collection::concat([[1, 2, 3], [4, 5, 6]])
            ->toList();
    }

    public static function concatFixedArrayWithBreak() {
        return Collection::concat([
                [1, 2, 3],
                [4, 5, 6],
            ])
            ->toList();
    }

    public static function concatFixedArrayWithBreakSingleBracket() {
        return Collection::concat(
                [
                    [1, 2, 3],
                    [4, 5, 6],
                ]
            )
            ->toList();
    }

    public static function concatFixedArrayWithLazyBreak() {
        return Collection::concat([
                [1, 2, 3],
                [4, 5, 6],
            ])->toList();
    }

    public static function concatWithNest($a) {
        $dummyFunction = function($_, $x) {
            return $x;
        };
        $muimi = '';

        $hoge = $dummyFunction($muimi, Collection::concat($a)->toList());
        return $hoge;
    }

    public static function map($a, $f) {
        return Collection::from($a)
            ->map($f)
            ->toList();
    }

    public static function mapStringSelector($a) {
        return Collection::from($a)
            ->map('[type]')
            ->toList();
    }

    public static function mapValueKey($a) {
        return Collection::from($a)
            ->map(static function($v, $k, $src) { return $k; }, static function($x) { return $x->get(); })
            ->toArray();
    }

    public static function join($a, $b, $f1, $f2, $f3) {
        return Collection::from($a)
            ->join($b, $f1, $f2, $f3)
            ->toList();
    }

    public static function without1($a, $arg1) {
        return Collection::from($a)
            ->without($arg1)
            ->toList();
    }

    public static function without3($a, $arg1, $arg2, $arg3) {
        return Collection::from($a)
            ->without($arg1, $arg2, $arg3)
            ->toList();
    }

    // any() は some() のエイリアス
    public static function any($a) {
        return Collection::from($a)
            ->any();
    }

    public static function anyWithFunction($a) {
        return Collection::from($a)
            ->any(function($x) {
                return $x == true;
            });
    }

    public static function anyWithStaticFunction($a) {
        return Collection::from($a)
            ->any(static function($x) {
                return $x == true;
            });
    }

    public static function anyWithStringSelector($a) {
        return Collection::from($a)
            ->any('[any]');
    }

    public static function filter($a, $f) {
        return Collection::from($a)
            ->filter($f)
            ->toList();
    }

    public static function groupBy($a) {
        return Collection::from($a)
            ->groupBy(function($x) { return strlen($x); })
            ->toArray();
    }

    public static function groupByStringSelector($a) {
        return Collection::from($a)
            ->groupBy('[type]')
            ->toArray();
    }

    public static function indexBy($a, $f) {
        return Collection::from($a)
            ->indexBy($f)
            ->toArray();
    }

    public static function toArray($a) {
        return Collection::from($a)
            ->toArray();
    }

    public static function minWith($a, $converter) {
        $result = Collection::from($a)
            ->minWith(static function ($x, $y) {
                return $x <=> $y;
            });
        return $converter($result);
    }

    public static function minWithNoTailSemicolon($a) {
        return Collection::from($a)
            ->minWith(static function($x, $y) {
                return $x <=> $y;
            })
            ;
    }

    public static function groupJoin($a, $b, $f1, $f2, $f3) {
        return Collection::from($a)
            ->groupJoin($b, $f1, $f2, $f3)
            ->toList();
    }

    public static function sortBy($a) {
        return Collection::from($a)
            ->sortBy(function($n) { return sin($n); })
            ->toList();
    }

    public static function sortByWithArgType($a) {
        return Collection::from($a)
            ->sortBy(function(int $n) {
                return sin($n);
            })
            ->toList();
    }

    public static function sortByStringSelector($a) {
        return Collection::from($a)
            ->sortBy('[price]')
            ->toList();
    }

    public static function sortByWithLazyBreak($a) {
        return Collection::from($a)->sortBy(function(int $n) {
                return sin($n);
            })->toList();
    }

    public static function reverse($a) {
        return Collection::from($a)
            ->reverse()
            ->toList();
    }

    public static function toListRec($a) {
        return Collection::from($a)
            ->toListRec();
    }

    public static function uniq($a) {
        return Collection::from($a)
            ->uniq()
            ->toList();
    }

    public static function uniqWithFunction($a) {
        return Collection::from($a)
            ->uniq(function($x) {
                return floor($x);
            })
            ->toList();
    }

    public static function size($a) {
        return Collection::from($a)
            ->size();
    }

    public static function all($a) {
        return Collection::from($a)
            ->all();
    }

    public static function allWithFunction($a) {
        return Collection::from($a)
            ->all(function($x) {
                return $x == true;
            });
    }

    public static function allWithStaticFunction($a) {
        return Collection::from($a)
            ->all(static function($x) {
                return $x == true;
            });
    }

    public static function allWithStringSelector($a) {
        return Collection::from($a)
            ->all('[all]');
    }

    public static function flatMap($a) {
        return Collection::from($a)
            ->flatMap(function($x) { return range(1, $x); })
            ->toList();
    }

    public static function findWhere($a) {
        return Collection::from($a)
            ->findWhere(['[age]' => 17]);
    }

    public static function concatMap($a) {
        return Collection::from($a)
            ->concatMap(function($x) { return range(1, $x); })
            ->toList();
    }

    public static function difference($a, $b) {
        return Collection::from($a)
            ->difference($b)
            ->toList();
    }

    public static function each($a) {
        $array = [];
        Collection::from($a)
            ->each(function($x) use (&$array) {
                $array[] = $x * 10;
            });
        return $array;
    }

    public static function find($a, $f) {
        return Collection::from($a)
            ->find($f);
    }

    public static function first($a) {
        return Collection::from($a)
            ->first();
    }

    public static function flatten($a) {
        return Collection::from($a)
            ->flatten()
            ->toList();
    }

    public static function intercalate($a, $separator) {
        return Collection::from($a)
            ->intercalate("$separator");
    }

    public static function intercalateRot13($a, $separator) {
        return str_rot13(Collection::from($a)
            ->intercalate("$separator") . 'hogepiyo');
    }

    public static function invokeWith1Arg($a) {
        return Collection::from($a)
            ->invoke('format', 'Y-m-d H:i:s')
            ->toList();
    }

    public static function invokeWith2Args($a) {
        return Collection::from($a)
            ->invoke('getTimestamp')
            ->toList();
    }

    public static function isEmpty($a) {
        return Collection::from($a)
            ->isEmpty();
    }

    public static function keys($a) {
        return Collection::from($a)
            ->keys()
            ->toList();
    }

    public static function maxWith($a, $converter) {
        $result = Collection::from($a)
            ->maxWith(static function ($x, $y) {
                return $x <=> $y;
            });
        return $converter($result);
    }

    public static function maxWithNoTailSemicolon($a) {
        return Collection::from($a)
            ->maxWith(static function($x, $y) {
                return $x <=> $y;
            })
            ;
    }

    public static function memoize($a) {
        $convertTargetableVariable = Collection::from($a)
            ->memoize();

        return $convertTargetableVariable->toList();
    }

    public static function object($a) {
        return Collection::from($a)
            ->object()
            ->toArray();
    }

    public static function objectWithArg($a, $b) {
        return Collection::from($a)
            ->object($b)
            ->toArray();
    }

    public static function omit($a, $b) {
        return Collection::from($a)
            ->omit("$b")
            ->toArray();
    }

    public static function pick($a, $b) {
        return Collection::from($a)
            ->pick("$b")
            ->toArray();
    }

    public static function pluck($a, $b) {
        return Collection::from($a)
            ->pluck("[{$b}]")
            ->toArray();
    }

    public static function pluckWithStringSelector($a) {
        return Collection::from($a)
            ->pluck("[name]")
            ->toArray();
    }

    // any() は some() のエイリアス
    public static function some($a) {
        return Collection::from($a)
            ->some();
    }

    public static function sortNoArg($a) {
        return Collection::from($a)
            ->sort()
            ->toList();
    }

    public static function sortFixedFunction($a) {
        return Collection::from($a)
            ->sort(function($x, $y) { return $x <=> $y; })
            ->toList();
    }

    public static function sum($a) {
        return Collection::from($a)
            ->sum();
    }

    public static function sumStringSelector($a) {
        return Collection::from($a)
            ->sum('[value]');
    }

    public static function sumWithFunction($a) {
        return Collection::from($a)
            ->sum(function($v, $k) {
                return $v * 100 - $k;
            }) / (10 * 10);
    }

    public static function values($a) {
        return Collection::from($a)
            ->values()
            ->toList();
    }

    public static function where($a) {
        return Collection::from($a)
            ->where(['[age]' => 24])
            ->toList();
    }

    public static function whereWithObj($a) {
        $obj = new Class{
            private $age = 24;
            public function getAge() {
                return $this->age;
            }
        };
        return Collection::from($a)
            ->where(['[age]' => $obj->getAge()])
            ->toList();
    }

    public static function whereWithObjNested($a) {
        $inner2 = new Class{
            private $age = 24;

            function getAge() {
                return $this->age;
            }
        };
        $innerObj = new Class($inner2){
            private $inner2;
            public function __construct($inner2) {
                $this->inner2 = $inner2;
            }
            public function getInner2() {
                return $this->inner2;
            }
        };
        $obj = new Class($innerObj){
            private $innerObj;
            public function __construct($innerObj) {
                $this->innerObj = $innerObj;
            }
            public function getInnerObj() {
                return $this->innerObj;
            }
        };

        return Collection::from($a)
            ->where(['[age]' => $obj->getInnerObj()->getInner2()->getAge()])
            ->toList();
    }

    public static function range10() {
        return Collection::range(10)
            ->toList();
    }

    public static function range4() {
        return Collection::range(4)
            ->toList();
    }

    public static function range24() {
        return Collection::range(24)
            ->toList();
    }

    public static function zip($a1, $a2, $a3) {
        return Collection::zip([$a1, $a2, $a3])
            ->toList();
    }

    public static function initial($a) {
        $sortedEndpoints = Collection::from($a);
        $convertTargetableVariable = $sortedEndpoints->initial();
        return $convertTargetableVariable->toList();
    }

    public static function rest($a) {
        $sortedEndpoints = Collection::from($a);
        $convertTargetableVariable = $sortedEndpoints->rest();
        return $convertTargetableVariable->toList();
    }

    public static function zipWith($a, $b) {
        return Collection::from($a)
            ->zipWith(...$b)
            ->toList();
    }

    public static function zipWithSingleArray($a) {
        return Collection::from($a)
            ->zipWith([100, 200, 300, 400, 500])
            ->toList();
    }
}

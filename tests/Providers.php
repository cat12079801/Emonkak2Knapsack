<?php

class Providers {
    const CATS_ARRAY = [
        ['name' => 'ミケ', 'type' => '三毛猫'],
        ['name' => 'クロ', 'type' => '黒猫'],
        ['name' => 'シロ', 'type' => '白猫'],
        ['name' => 'ぶち', 'type' => '三毛猫'],
        ['name' => 'マシュマロ', 'type' => '白猫'],
        ['name' => 'ヤマト運輸', 'type' => '黒猫'],
        ['name' => '美人さん', 'type' => 'アメリカンショートヘア'],
    ];

    public function fromProvider() {
        return [
            '通常' => [[1, 2, 3], [1, 2, 3]],
            '混合' => [['hoge', false, null], ['hoge', false, null]],
            '空配列' => [[], []],
            '深い配列' => [
                [100000, '0980198051', [124, 124], [[[['x']]]]],
                [100000, '0980198051', [124, 124], [[[['x']]]]],
            ],
        ];
    }

    public function concatProvider() {
        return [
            '通常' => [
                [1, 2, 3, 4, 5],
                [[1, 2, 3], [4, 5]]
            ],
            '混合' => [
                [1, true, false, null, 'hoge'],
                [[1, true], [false, null, 'hoge']]
            ],
            '空配列' => [
                [],
                [[], []]
            ],
            '空配列2' => [
                [1],
                [[1], []]
            ],
            '空配列3' => [
                [1],
                [[], [1]]
            ],
            '多次元配列' => [
                [[[[[true]]]], [[[[[false]]]]]],
                [[[[[[true]]]], [[[[[false]]]]]]]
            ],
        ];
    }

    public function concatFixedArrayProvider() {
        return [
            'Collection::concat([[1, 2, 3], [4, 5, 6]])' => [
                [1, 2, 3, 4, 5, 6],
            ],
        ];
    }

    public function mapProvider() {
        return [
            '10足す' => [
                [10, 11, 13, 20],
                [0, 1, 3, 10],
                function($x) { return $x + 10; },
            ],
            '100掛ける' => [
                [100, 1.0, 0, 100],
                [1, 0.01, 0, true],
                function($x) { return $x * 100; },
            ],
            'bool型にする' => [
                [true, false, true, false],
                [true, 0, 'にゃーん(ΦωΦ)', null],
                function($x) { return (bool)$x; },
            ],
        ];
    }

    public function mapStringSelectorProvider() {
        // selector: '[type]'
        return [
            '(ΦωΦ)' => [
                [
                    '三毛猫',
                    '黒猫',
                    '白猫',
                    '三毛猫',
                    '白猫',
                    '黒猫',
                    'アメリカンショートヘア',
                ],
                self::CATS_ARRAY,
            ],
        ];
    }

    public function mapValueKeyProvider() {
        $getAnonymousClass = function($x) {
            return new Class($x) {
                private $x;
                public function __construct($x) {
                    $this->x = $x;
                }
                public function get() {
                    return $this->x;
                }
            };
        };

        return [
            'オブジェクトの値とindexを入れ替え' => [
                [
                    '10' => 0,
                    '20' => 1,
                    '14' => 2,
                    '19' => 3,
                    '33' => 4,
                    '12' => 5,
                    '99' => 6,
                ],
                [
                    $getAnonymousClass('10'),
                    $getAnonymousClass('20'),
                    $getAnonymousClass('14'),
                    $getAnonymousClass('19'),
                    $getAnonymousClass('33'),
                    $getAnonymousClass('12'),
                    $getAnonymousClass('99'),
                ],
            ],
        ];
    }

    public function joinProvider() {
        return [
            '公式のサンプル' => [
                [
                    ['id' => 1, 'name' => 'User1', 'addr' => ['id' => 1, 'addr' => 'Addr1']],
                    ['id' => 2, 'name' => 'User2', 'addr' => ['id' => 2, 'addr' => 'Addr2']],
                    ['id' => 2, 'name' => 'User2', 'addr' => ['id' => 2, 'addr' => 'Addr3']],
                    ['id' => 5, 'name' => 'User5', 'addr' => ['id' => 5, 'addr' => 'Addr4']],
                ],
                [
                    ['id' => 1, 'name' => 'User1'],
                    ['id' => 2, 'name' => 'User2'],
                    ['id' => 3, 'name' => 'User3'],
                    ['id' => 4, 'name' => 'User4'],
                    ['id' => 5, 'name' => 'User5'],
                ],
                [
                    ['id' => 1, 'addr' => 'Addr1'],
                    ['id' => 2, 'addr' => 'Addr2'],
                    ['id' => 2, 'addr' => 'Addr3'],
                    ['id' => 5, 'addr' => 'Addr4']
                ],
                function($user) { return $user['id']; },
                function($addr) { return $addr['id']; },
                function($user, $addr) {
                    $user['addr'] = $addr;
                    return $user;
                },
            ],
            '実際の実装で使っている' => [
                [
                    ['a' => 1, 'b' => 100],
                    ['a' => 2, 'b' => 200],
                    ['a' => 5, 'b' => 500],
                    ['a' => 7, 'b' => 700],
                    ['a' => 9, 'b' => 900],
                ],
                [
                    ['a' => 1, 'b' => 100],
                    ['a' => 2, 'b' => 200],
                    ['a' => 3, 'b' => 300],
                    ['a' => 4, 'b' => 400],
                    ['a' => 5, 'b' => 500],
                    ['a' => 6, 'b' => 600],
                    ['a' => 7, 'b' => 700],
                    ['a' => 8, 'b' => 800],
                    ['a' => 9, 'b' => 900],
                ],
                [
                    1,
                    2,
                    5,
                    7,
                    9,
                ],
                function($x) { return $x['a']; },
                null,
                function($outer) { return $outer; },
            ],
        ];
    }

    public function withoutProvider1() {
        return [
            '通常' => [
                [2, 3, 4, 5, 6, 7],
                [1, 2, 3, 4, 5, 6, 7],
                1,
            ],
            '空' => [
                ['(ΦωΦ)'],
                [null, null, null, '(ΦωΦ)'],
                null,
            ],
        ];
    }

    public function withoutProvider3() {
        return [
            '通常' => [
                [2, 5, 6, 7],
                [1, 2, 3, 4, 5, 6, 7],
                1,
                3,
                4,
            ],
        ];
    }

    public function anyProvider() {
        return [
            'true単体' => [
                true,
                [1],
            ],
            'false単体' => [
                false,
                [0],
            ],
            'trueたくさん' => [
                true,
                [0, null, 'x', new Providers()],
            ],
            'falseたくさん' => [
                false,
                [0, null, false, '', []],
            ],
        ];
    }

    public function anyStringSelectorProvider() {
        // '[any]'
        return [
            'return true' => [
                true,
                [
                    ['any' => 0],
                    ['any' => []],
                    ['any' => false],
                    ['any' => null],
                    ['any' => true],
                ],
            ],
            'return false' => [
                false,
                [
                    ['any' => 0],
                    ['any' => []],
                    ['any' => false],
                    ['any' => null],
                    ['any' => false],
                ],
            ],
        ];
    }

    public function filterProvider() {
        return [
            '0判定' => [
                [0],
                [0, 1, 2, 3, 4, 5],
                function($x) { return $x === 0; },
            ],
            '偶数判定' => [
                [2, 4, 6, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                function($x) { return $x % 2 === 0; },
            ],
            '文字列判定' => [
                ['neko', 'is', 'kawaii', '(ΦωΦ)'],
                [102, 'neko', 'is', true, null, 'kawaii', true, '(ΦωΦ)'],
                function($x) { return gettype($x) === 'string'; },
            ],
        ];
    }

    public function groupByProvider() {
        return [
            [
                '数字' => [
                    3 => [2.1, 1.3, 2.4, 3.7, 100],
                    1 => [3.0]
                ],
                [2.1, 1.3, 2.4, 3.7, 100, 3.0],
            ],
            [
                '果物' => [
                    5 => ['apple', 'grape'],
                    6 => ['orange', 'banana'],
                    4 => ['pear'],
                ],
                ['apple', 'orange', 'grape', 'pear', 'banana'],
            ],
            [
                '型分類' => [
                    1  => [true],
                    0  => [null, false],
                    3  => [100],
                    4  => ['neko'],
                    15 => ['groupByProvider'],
                ],
                [true, null, 100, 'neko', false, groupByProvider],
            ],
        ];
    }

    public function groupByStringSelectorProvider() {
        // selector: '[type]'
        return [
            '(ΦωΦ)' => [
                [
                    '三毛猫' => [
                        ['name' => 'ミケ', 'type' => '三毛猫'],
                        ['name' => 'ぶち', 'type' => '三毛猫'],
                    ],
                    '黒猫' => [
                        ['name' => 'クロ', 'type' => '黒猫'],
                        ['name' => 'ヤマト運輸', 'type' => '黒猫'],
                    ],
                    '白猫' => [
                        ['name' => 'シロ', 'type' => '白猫'],
                        ['name' => 'マシュマロ', 'type' => '白猫'],
                    ],
                    'アメリカンショートヘア' => [
                        ['name' => '美人さん', 'type' => 'アメリカンショートヘア'],
                    ],
                ],
                self::CATS_ARRAY,
            ],
        ];
    }

    public function indexByProvider() {
        return [
            '公式サンプル' => [
                [
                    40 => ['name' => 'moe', 'age' => 40],
                    50 => ['name' => 'larry', 'age' => 50],
                    60 => ['name' => 'curly', 'age' => 60],
                ],
                [
                    ['name' => 'moe', 'age' => 40],
                    ['name' => 'larry', 'age' => 50],
                    ['name' => 'curly', 'age' => 60],
                ],
                function($x) { return $x['age']; },
            ],
            '商品リスト' => [
                [
                    100 => ['id' => 1, 'price' => 100, 'type' => 'A'],
                    200 => ['id' => 2, 'price' => 200, 'type' => 'A'],
                    120 => ['id' => 3, 'price' => 120, 'type' => 'B'],
                    320 => ['id' => 4, 'price' => 320, 'type' => 'B'],
                    110 => ['id' => 5, 'price' => 110, 'type' => 'C'],
                    140 => ['id' => 6, 'price' => 140, 'type' => 'A'],
                    330 => ['id' => 7, 'price' => 330, 'type' => 'A'],
                    170 => ['id' => 8, 'price' => 170, 'type' => 'B'],
                    180 => ['id' => 9, 'price' => 180, 'type' => 'A'],
                ],
                [
                    ['id' => 1, 'price' => 100, 'type' => 'A'],
                    ['id' => 2, 'price' => 200, 'type' => 'A'],
                    ['id' => 3, 'price' => 120, 'type' => 'B'],
                    ['id' => 4, 'price' => 320, 'type' => 'B'],
                    ['id' => 5, 'price' => 110, 'type' => 'C'],
                    ['id' => 6, 'price' => 140, 'type' => 'A'],
                    ['id' => 7, 'price' => 330, 'type' => 'A'],
                    ['id' => 8, 'price' => 170, 'type' => 'B'],
                    ['id' => 9, 'price' => 180, 'type' => 'A'],
                ],
                function($x) { return $x['price']; },
            ],
        ];
    }

    public function toArrayProvider() {
        return [
            '公式サンプル' => [
                [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                ],
                [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                ],
            ],
            '空配列' => [
                [],
                [],
            ],
        ];
    }

    public function minWithProvider() {
        return [
            '公式サンプル' => [
                1,
                [3, 2, 1],
                function($x) { return $x; },
            ],
            '実装参考' => [
                '2019-11-11 11:11:11',
                [
                    new DateTimeImmutable('2019-11-11 11:11:11'),
                    new DateTimeImmutable('2019-11-12 11:11:11'),
                    new DateTimeImmutable('2019-11-13 11:11:11'),
                    new DateTimeImmutable('2019-11-14 11:11:11'),
                    new DateTimeImmutable('2019-11-15 11:11:11'),
                ],
                function($x) { return $x->format('Y-m-d H:i:s'); },
            ],
        ];
    }

    public function minWithNoTailSemicolonProvider() {
        return [
            '公式サンプル' => [
                1,
                [3, 1, 6, 87, 1, 61, 1, 6],
            ],
        ];
    }

    public function groupJoinProvider() {
        return [
            /* groupJoin は leftOuterJoinとして再実装するのでこのテストケースは削除
            '公式サンプル(修正版)' => [
                [
                    ['user_id' => 1, 'name' => 'User1', 'addresses' => [['user_id' => 1, 'address' => 'Address1']]],
                    ['user_id' => 2, 'name' => 'User2', 'addresses' => [['user_id' => 2, 'address' => 'Address2'], ['user_id' => 2, 'address' => 'Address3']]],
                    ['user_id' => 3, 'name' => 'User3', 'addresses' => []],
                    ['user_id' => 4, 'name' => 'User4', 'addresses' => []],
                    ['user_id' => 5, 'name' => 'User5', 'addresses' => [['user_id' => 5, 'address' => 'Address4']]]
                ],
                [
                    ['user_id' => 1, 'name' => 'User1'],
                    ['user_id' => 2, 'name' => 'User2'],
                    ['user_id' => 3, 'name' => 'User3'],
                    ['user_id' => 4, 'name' => 'User4'],
                    ['user_id' => 5, 'name' => 'User5'],
                ],
                [
                    ['user_id' => 1, 'address' => 'Address1'],
                    ['user_id' => 2, 'address' => 'Address2'],
                    ['user_id' => 2, 'address' => 'Address3'],
                    ['user_id' => 5, 'address' => 'Address4'],
                ],
                function($user) { return $user['user_id']; },
                function($address) { return $address['user_id']; },
                function($user, $addresses) {
                    $user['addresses'] = $addresses;
                    return $user;
                },
            ],
             */
            '実装参考' => [
                [
                    ['id' => 1, 'name' => 'neko'],
                    ['id' => 2, 'name' => 'hito'],
                    null,
                    ['id' => 4, 'name' => 'yagi'],
                    null,
                    ['id' => 6, 'name' => 'tori'],
                ],
                [
                    ['id' => 1, 'name' => 'neko'],
                    ['id' => 2, 'name' => 'hito'],
                    ['id' => 3, 'name' => 'sake'],
                    ['id' => 4, 'name' => 'yagi'],
                    ['id' => 5, 'name' => 'roba'],
                    ['id' => 6, 'name' => 'tori'],
                ],
                [
                    ['id' => 1, 'feature' => 'かわいい'],
                    ['id' => 2, 'feature' => '道具'],
                    ['id' => 4, 'feature' => '毛糸'],
                    ['id' => 6, 'feature' => '飛べる'],
                ],
                function($x) { return $x['id']; },
                function($x) { return $x['id']; },
                function($outer, $inner) {
                    if (empty($inner)) return null;
                    return $outer;
                },
            ],
        ];
    }

    public function sortByProvider() {
        // function($n) { return sin($n); }
        return [
            '公式サンプル' => [
                [5, 4, 6, 3, 1, 2],
                [1, 2, 3, 4, 5, 6],
            ],
            'サンプル2' => [
                [100, 0, 13, 51, 40, 20],
                [100, 20, 40, 13, 51, 0],
            ],
        ];
    }

    public function sortByStringSelectorProvider() {
        // selector: '[price]'
        return [
            '物品' => [
                [
                    ['name' => 'C', 'price' => 80],
                    ['name' => 'A', 'price' => 170],
                    ['name' => 'B', 'price' => 220],
                ],
                [
                    ['name' => 'A', 'price' => 170],
                    ['name' => 'B', 'price' => 220],
                    ['name' => 'C', 'price' => 80],
                ],
            ],
        ];
    }

    public function reverseProvider() {
        return [
            '公式サンプル' => [
                [3, 2, 1],
                [1, 2, 3],
            ],
        ];
    }

    public function toListRecProvider() {
        return [
            '公式サンプル' => [
                [
                    [1, 2],
                    [3],
                ],
                [
                    ['foo' => 1, 'bar' => 2],
                    ['baz' => 3],
                ],
            ],
            '三重' => [
                [
                    0,
                    [1],
                    [[2]],
                    [[[3]]],
                ],
                [
                    '0' => 0,
                    ['1' => 1],
                    ['2' => ['2' => 2]],
                    ['3' => ['3' => ['3' => 3]]],
                ],
            ],
        ];
    }

    public function uniqProvider() {
        return [
            '公式サンプル' => [
                [1, 2, 3, 4],
                [1, 2, 1, 3, 1, 4],
            ],
        ];
    }

    public function uniqWithFunctionProvider() {
        // function($x) { return floor($x); }
        return [
            '小数点切り捨て' => [
                [1.3, 5.2, 3.0, 2.0, 4.2],
                [1.3, 5.2, 3.0, 1.0, 2.0, 4.2, 1.2, 5.3, 3.1, 5.5],
            ],
        ];
    }

    public function sizeProvider() {
        return [
            '公式サンプル' => [
                3,
                ['one' => 1, 'two' => 2, 'three' => 3],
            ],
        ];
    }

    public function allProvider() {
        return [
            'true単体' => [
                true,
                [1],
            ],
            'false単体' => [
                false,
                [0],
            ],
            'trueたくさん' => [
                false,
                [0, null, 'x', new Providers()],
            ],
            'falseたくさん' => [
                false,
                [0, null, false, '', []],
            ],
        ];
    }

    public function allStringSelectorProvider() {
        // '[all]'
        return [
            'return true' => [
                true,
                [
                    ['all' => 1],
                    ['all' => [1]],
                    ['all' => true],
                    ['all' => !null],
                    ['all' => [[[3]]]],
                ],
            ],
            'return false' => [
                false,
                [
                    ['all' => 1],
                    ['all' => [1]],
                    ['all' => true],
                    ['all' => null],
                    ['all' => [[[3]]]],
                ],
            ],
        ];
    }

    public function findWhereProvider() {
        // ['[age]' => 17],
        return [
            '公式サンプル' => [
                ['name' => 'Yukari Tamura', 'age' => 17],
                [
                    ['name' => 'Yukari Tamura', 'age' => 17],
                    ['name' => 'Yui Horie', 'age' => 17],
                ],
            ],
        ];
    }

    public function concatMapProvider() {
        return[
            '公式サンプル' => [
                [1, 1, 2, 1, 2, 3],
                [1, 2, 3],
            ],
        ];
    }

    public function differenceProvider() {
        return[
            '公式サンプル' => [
                [1, 3, 4],
                [1, 2, 3, 4, 5],
                [5, 2, 10],
            ],
        ];
    }

    public function eachProvider() {
        return[
            'sample' => [
                [10, 20, 30],
                [1, 2, 3],
            ],
        ];
    }

    public function findProvider() {
        return[
            '公式サンプル' => [
                2,
                [1, 2, 3, 4, 5, 6],
                function($x) { return $x % 2 === 0; },
            ],
            'nullを返す' => [
                null,
                [1, 2, 3, 4, 5, 6],
                function($x) { return $x === 0; },
            ],
            '空配列' => [
                null,
                [],
                function($x) { return $x === 0; },
            ],
        ];
    }

    public function firstProvider() {
        // first() は引数なしでしか使っていない
        return[
            '公式サンプル1' => [
                1,
                [1, 2, 3],
            ],
            'サンプル2' => [
                null,
                [null, true, false],
            ],
            '公式サンプル3' => [
                100,
                [100, 200, 300],
            ],
        ];
    }

    public function flattenProvider() {
        // depthは考慮せずすべて平坦化する
        return[
            '公式サンプル1' => [
                [1, 2, 3, 4],
                [1, [2], [3, [[4]]]],
            ],
            'depth=2' => [
                [100, 200, 300],
                [[100], [200], [300]],
            ],
            'depth=3' => [
                [3, 3, 3],
                [[[3]], [[3]], [[3]]],
            ],
        ];
    }

    public function intercalateProvider() {
        return[
            '公式サンプル' => [
                'foo,bar,baz',
                ['foo', 'bar', 'baz'],
                ',',
            ],
            '改行文字' => [
                "foo\nbar\nbaz",
                ['foo', 'bar', 'baz'],
                "\n",
            ],
            '改行文字を含む' => [
                "foo\n  when bar\n  when baz",
                ['foo', 'bar', 'baz'],
                "\n  when ",
            ],
            '空文字セパレータ' => [
                'foobarbaz',
                ['foo', 'bar', 'baz'],
                '',
            ],
            '複数文字セパレータ' => [
                'foonekobarnekobaz',
                ['foo', 'bar', 'baz'],
                'neko',
            ],
            'string以外がcollectionにいる' => [
                '0,100,1,,',
                [0, 100, true, false, null],
                ',',
            ],
        ];
    }

    public function intercalateRot13Provider() {
        return[
            '公式サンプル' => [
                'sbboneonmubtrcvlb',
                ['foo', 'bar', 'baz'],
                '',
            ],
        ];
    }

    public function invokeWith1ArgProvider() {
        return[
            '公式サンプル' => [
                ['2000-01-01 00:00:00', '2013-01-01 00:00:00'],
                [new DateTime('2000-01-01'), new DateTime('2013-01-01')],
            ],
        ];
    }

    public function invokeWith2ArgsProvider() {
        return[
            'invokeの引数が1つ' => [
                [946684800, 1356998400],
                [new DateTime('2000-01-01'), new DateTime('2013-01-01')],
            ],
        ];
    }

    public function isEmptyProvider() {
        return[
            '公式サンプル1' => [
                false,
                [1, 2, 3],
            ],
            '公式サンプル2' => [
                true,
                new EmptyIterator(),
            ],
            '空配列' => [
                true,
                [],
            ],
        ];
    }

    public function keysProvider() {
        return[
            '公式サンプル' => [
                ['one', 'two', 'three'],
                ['one' => 1, 'two' => 2, 'three' => 2],
            ],
        ];
    }

    public function maxWithProvider() {
        return [
            '公式サンプル' => [
                3,
                [3, 2, 1],
                function($x) { return $x; },
            ],
            '実装参考' => [
                '2019-11-15 11:11:11',
                [
                    new DateTimeImmutable('2019-11-11 11:11:11'),
                    new DateTimeImmutable('2019-11-12 11:11:11'),
                    new DateTimeImmutable('2019-11-13 11:11:11'),
                    new DateTimeImmutable('2019-11-14 11:11:11'),
                    new DateTimeImmutable('2019-11-15 11:11:11'),
                ],
                function($x) { return $x->format('Y-m-d H:i:s'); },
            ],
        ];
    }

    public function maxWithNoTailSemicolonProvider() {
        return [
            '公式サンプル' => [
                87,
                [3, 1, 6, 87, 1, 61, 1, 6],
            ],
        ];
    }

    public function memoizeProvider() {
        return[
            '実質テスト不可能' => [
                [1, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
            ],
        ];
    }

    public function objectProvider() {
        return[
            '公式サンプル(引数なし)' => [
                ['moe' => 30, 'larry' => 40, 'curly' => 50],
                [['moe', 30], ['larry', 40], ['curly', 50]],
            ],
        ];
    }

    public function objectWithArgProvider() {
        return[
            '公式サンプル(引数あり)' => [
                ['moe' => 30, 'larry' => 40, 'curly' => 50],
                ['moe', 'larry', 'curly'],
                [30, 40, 50],
            ],
        ];
    }

    public function omitProvider() {
        return[
            '公式サンプル' => [
                ['name' => 'moe', 'age' => 50],
                ['name' => 'moe', 'age' => 50, 'userid' => 'moe1'],
                'userid',
            ],
        ];
    }

    public function pickProvider() {
        // pick(string[]|string ...$keys) だが既存実装では文字列1つ渡す例しかない
        return[
            '公式サンプル' => [
                ['name' => 'moe'],
                ['name' => 'moe', 'age' => 50, 'userid' => 'moe1'],
                'name',
            ],
        ];
    }

    public function pluckProvider() {
        // '[name]'
        return[
            '公式サンプル' => [
                ['moe', 'larry', 'curly'],
                [
                    ['name' => 'moe', 'age' => 40],
                    ['name' => 'larry', 'age' => 50],
                    ['name' => 'curly', 'age' => 60]
                ],
                'name',
            ],
        ];
    }

    public function sortNoArgProvider() {
        return [
            'サンプル1' => [
                [1, 2, 3],
                [2, 3, 1],
            ],
        ];
    }

    public function sortFixedFunctionProvider() {
        // function($x, $y) { return $x <=> $y; }
        return [
            'サンプル1' => [
                [1, 2, 3],
                [2, 3, 1],
            ],
        ];
    }

    public function sumProvider() {
        return[
            '公式サンプル' => [
                15,
                [0, 1, 2, 3, 4, 5],
            ],
        ];
    }

    public function sumStringSelectorProvider() {
        // '[value]'
        return[
            '文字列セレクタ' => [
                15,
                [
                    ['value' => 0],
                    ['value' => 1],
                    ['value' => 2],
                    ['value' => 3],
                    ['value' => 4],
                    ['value' => 5]
                ],
            ],
        ];
    }

    public function sumWithFunctionProvider() {
        // ->sum(function($v, $k) {
        //     return $v * 100 - $k;
        // }) / (10 * 10);
        return[
            'クロージャでごにゃごにゃする' => [
                258.15,
                [
                    10 => 100,
                    14 => 4,
                    2 => 5,
                    52 => 51,
                    7 => 99,
                ],
            ],
        ];
    }

    public function valuesProvider() {
        return[
            '公式サンプル' => [
                [1, 2, 3],
                ['one' => 1, 'two' => 2, 'three' => 3],
            ],
        ];
    }

    public function whereProvider() {
        // ['[age]' => 24]
        return[
            '公式サンプル' => [
                [
                    ["name" => "Yuka Iguchi",   "age" => 24],
                    ["name" => "Kana Hanazawa", "age" => 24]
                ],
                [
                    ['name' => 'Yui Ogura',     'age' => 17],
                    ['name' => 'Rina Hidaka',   'age' => 19],
                    ['name' => 'Yuka Iguchi',   'age' => 24],
                    ['name' => 'Yoko Hikasa',   'age' => 27],
                    ['name' => 'Kana Hanazawa', 'age' => 24]
                ],
            ],
        ];
    }

    public function range10Provider() {
        return[
            '公式サンプル1' => [
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
        ];
    }

    public function range4Provider() {
        return[
            '公式サンプル1' => [
                [0, 1, 2, 3],
            ],
        ];
    }

    public function range24Provider() {
        return[
            '公式サンプル1' => [
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
            ],
        ];
    }

    public function zipProvider() {
        return[
            '公式サンプル' => [
                [
                    ['moe', 30, true],
                    ['larry', 40, false],
                    ['curly', 50, false],
                ],
                [
                    ['moe', 'larry', 'curly'],
                    [30, 40, 50],
                    [true, false, false],
                ],
            ],
        ];
    }

    public function initialProvider() {
        return[
            '公式サンプル' => [
                [5, 4, 3, 2],
                [5, 4, 3, 2, 1],
            ],
        ];
    }

    public function restProvider() {
        return[
            '公式サンプル' => [
                [4, 3, 2, 1],
                [5, 4, 3, 2, 1],
            ],
        ];
    }

    public function zipWithProvider() {
        return[
            '公式サンプル' => [
                [
                    ["moe", 30, true],
                    ["larry", 40, false],
                    ["curly", 50, false],
                ],
                ['moe', 'larry', 'curly'],
                [
                    [30, 40, 50],
                    [true, false, false],
                ]
            ],
            '数が足りない' => [
                [
                    ['A', 1, true],
                    ['B', 2, false],
                    ['C', 3, null],
                ],
                ['A', 'B', 'C', 'D', 'E'],
                [
                    [1, 2, 3, 4, 5],
                    [true, false, null],
                ]
            ],
            'むしろ多い' => [
                [
                    ['A', 1, true],
                    ['B', 2, false],
                    ['C', 3, null],
                    ['D', 4, ''],
                    ['E', 5, 999],
                ],
                ['A', 'B', 'C', 'D', 'E'],
                [
                    [1, 2, 3, 4, 5],
                    [true, false, null, '', 999, 777, -2],
                ]
            ],
            'いっそ引数が空配列' => [
                [
                    ['A'],
                    ['B'],
                    ['C'],
                    ['D'],
                    ['E'],
                ],
                ['A', 'B', 'C', 'D', 'E'],
                []
            ],
            '空配列の配列のとき' => [
                [],
                ['A', 'B', 'C', 'D', 'E'],
                [
                    [], [], [], [],
                ]
            ],
            '一部空配列' => [
                [],
                ['A', 'B', 'C', 'D', 'E'],
                [
                    [],
                    [],
                    [3],
                    [],
                    [6],
                ]
            ],
        ];
    }
    public function zipWithSingleArrayProvider() {
        // [100, 200, 300, 400, 500]
        return[
            '公式サンプル' => [
                [
                    ['カレー', 100],
                    ['ラーメン', 200],
                    ['ハンバーグ', 300],
                    ['チャーハン', 400],
                    ['オムライス', 500],
                ],
                ['カレー', 'ラーメン', 'ハンバーグ', 'チャーハン', 'オムライス'],
            ],
        ];
    }
}

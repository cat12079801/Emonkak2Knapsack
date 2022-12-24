<?php

require_once ('vendor/autoload.php');
require_once ('Providers.php');
require_once (dirname(__FILE__) .'/../src/code.php');

use PHPUnit\Framework\TestCase;

class CollectionClassTest extends TestCase {
    public function testSample()
    {
        $this->assertSame(
            [1, 2],
            CollectionClass::sample()
        );
    }

    /**
     * @dataProvider Providers::fromProvider
     */
    public function testFrom($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::from($a)
        );
    }

    /**
     * @dataProvider Providers::fromProvider
     */
    public function testFromTraversable($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::fromTraversable($a)
        );
    }

    /**
     * @dataProvider Providers::concatProvider
     */
    public function testConcat($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::concat($a)
        );
    }

    /**
     * @dataProvider Providers::concatFixedArrayProvider
     */
    public function testconcatFixedArray($expected) {
        $this->assertSame(
            $expected,
            CollectionClass::concatFixedArray()
        );
    }

    /**
     * @dataProvider Providers::concatFixedArrayProvider
     */
    public function testconcatFixedArrayWithBreak($expected) {
        $this->assertSame(
            $expected,
            CollectionClass::concatFixedArrayWithBreak()
        );
    }

    /**
     * @dataProvider Providers::concatFixedArrayProvider
     */
    public function testconcatFixedArrayWithBreakSingleBracket($expected) {
        $this->assertSame(
            $expected,
            CollectionClass::concatFixedArrayWithBreakSingleBracket()
        );
    }

    /**
     * @dataProvider Providers::concatFixedArrayProvider
     */
    public function testconcatFixedArrayWithLazyBreak($expected) {
        $this->assertSame(
            $expected,
            CollectionClass::concatFixedArrayWithLazyBreak()
        );
    }

    /**
     * @dataProvider Providers::concatProvider
     */
    public function testConcatWithNest($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::concatWithNest($a)
        );
    }

    /**
     * @dataProvider Providers::mapProvider
     */
    public function testMap($expected, $a, $f) {
        $this->assertSame(
            $expected,
            CollectionClass::map($a, $f)
        );
    }

    /**
     * @dataProvider Providers::mapStringSelectorProvider
     */
    public function testMapStringSelector($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::mapStringSelector($a)
        );
    }

    /**
     * @dataProvider Providers::mapValueKeyProvider
     */
    public function testMapValueKey($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::mapValueKey($a)
        );
    }

    /**
     * @dataProvider Providers::joinProvider
     */
    public function testJoin($expected, $a, $b, $f1, $f2, $f3) {
        $this->assertSame(
            $expected,
            CollectionClass::join($a, $b, $f1, $f2, $f3)
        );
    }

    /**
     * @dataProvider Providers::withoutProvider1
     */
    public function testWithout1($expected, $a, $arg1) {
        $this->assertSame(
            $expected,
            CollectionClass::without1($a, $arg1)
        );
    }

    /**
     * @dataProvider Providers::withoutProvider3
     */
    public function testWithout3($expected, $a, $arg1, $arg2, $arg3) {
        $this->assertSame(
            $expected,
            CollectionClass::without3($a, $arg1, $arg2, $arg3)
        );
    }

    /**
     * @dataProvider Providers::anyProvider
     */
    public function testAny($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::any($a)
        );
    }

    /**
     * @dataProvider Providers::anyProvider
     */
    public function testAnyWithFunction($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::anyWithFunction($a)
        );
    }

    /**
     * @dataProvider Providers::anyProvider
     */
    public function testAnyWithStaticFunction($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::anyWithStaticFunction($a)
        );
    }

    /**
     * @dataProvider Providers::anyStringSelectorProvider
     */
    public function testAnyWithStringSelector($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::anyWithStringSelector($a)
        );
    }

    /**
     * @dataProvider Providers::filterProvider
     */
    public function testFilter($expected, $a, $f) {
        $this->assertSame(
            $expected,
            CollectionClass::filter($a, $f)
        );
    }

    /**
     * @dataProvider Providers::groupByProvider
     */
    public function testGroupBy($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::groupBy($a)
        );
    }

    /**
     * @dataProvider Providers::groupByStringSelectorProvider
     */
    public function testGroupByStringSelector($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::groupByStringSelector($a)
        );
    }

    /**
     * @dataProvider Providers::indexByProvider
     */
    public function testIndexBy($expected, $a, $idx) {
        $this->assertSame(
            $expected,
            CollectionClass::indexBy($a, $idx)
        );
    }

    /**
     * @dataProvider Providers::toArrayProvider
     */
    public function testToArray($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::toArray($a)
        );
    }

    /**
     * @dataProvider Providers::minWithProvider
     */
    public function testMinWith($expected, $a, $converter) {
        $this->assertSame(
            $expected,
            CollectionClass::minWith($a, $converter)
        );
    }

    /**
     * @dataProvider Providers::minWithNoTailSemicolonProvider
     */
    public function testMinWithNoTailSemicolon($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::minWithNoTailSemicolon($a)
        );
    }

    /**
     * @dataProvider Providers::groupJoinProvider
     */
    public function testGroupJoin($expected, $a, $b, $f1, $f2, $f3) {
        $this->assertSame(
            $expected,
            CollectionClass::groupJoin($a, $b, $f1, $f2, $f3)
        );
    }

    /**
     * @dataProvider Providers::sortByProvider
     */
    public function testSortBy($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::sortBy($a)
        );
    }

    /**
     * @dataProvider Providers::sortByProvider
     */
    public function testSortByWithArgType($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::sortByWithArgType($a)
        );
    }

    /**
     * @dataProvider Providers::sortByStringSelectorProvider
     */
    public function testSortByStringSelector($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::sortByStringSelector($a)
        );
    }

    /**
     * @dataProvider Providers::sortByProvider
     */
    public function testSortByWithLazyBreak($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::sortByWithLazyBreak($a)
        );
    }

    /**
     * @dataProvider Providers::reverseProvider
     */
    public function testReverse($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::reverse($a)
        );
    }

    /**
     * @dataProvider Providers::toListRecProvider
     */
    public function testToListRec($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::toListRec($a)
        );
    }

    /**
     * @dataProvider Providers::uniqProvider
     */
    public function testUniq($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::uniq($a)
        );
    }

    /**
     * @dataProvider Providers::uniqWithFunctionProvider
     */
    public function testUniqWithFunction($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::uniqWithFunction($a)
        );
    }

    /**
     * @dataProvider Providers::sizeProvider
     */
    public function testSize($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::size($a)
        );
    }

    /**
     * @dataProvider Providers::allProvider
     */
    public function testAll($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::all($a)
        );
    }

    /**
     * @dataProvider Providers::allProvider
     */
    public function testAllWithFunction($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::allWithFunction($a)
        );
    }

    /**
     * @dataProvider Providers::allProvider
     */
    public function testAllWithStaticFunction($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::allWithStaticFunction($a)
        );
    }

    /**
     * @dataProvider Providers::allStringSelectorProvider
     */
    public function testAllWithStringSelector($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::allWithStringSelector($a)
        );
    }

    /**
     * @dataProvider Providers::concatMapProvider
     */
    public function testFlatMap($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::flatMap($a)
        );
    }

    /**
     * @dataProvider Providers::findWhereProvider
     */
    public function testFindWhere($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::findWhere($a)
        );
    }

    /**
     * @dataProvider Providers::concatMapProvider
     */
    public function testConcatMap($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::concatMap($a)
        );
    }

    /**
     * @dataProvider Providers::differenceProvider
     */
    public function testDifference($expected, $a, $b) {
        $this->assertSame(
            $expected,
            CollectionClass::difference($a, $b)
        );
    }

    /**
     * @dataProvider Providers::eachProvider
     */
    public function testEach($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::each($a)
        );
    }

    /**
     * @dataProvider Providers::findProvider
     */
    public function testFind($expected, $a, $f) {
        $this->assertSame(
            $expected,
            CollectionClass::find($a, $f)
        );
    }

    /**
     * @dataProvider Providers::firstProvider
     */
    public function testFirst($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::first($a)
        );
    }

    /**
     * @dataProvider Providers::flattenProvider
     */
    public function testFlatten($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::flatten($a)
        );
    }

    /**
     * @dataProvider Providers::intercalateProvider
     */
    public function testIntercalate($expected, $a, $separator) {
        $this->assertSame(
            $expected,
            CollectionClass::intercalate($a, $separator)
        );
    }

    /**
     * @dataProvider Providers::intercalateRot13Provider
     */
    public function testIntercalateRot13($expected, $a, $separator) {
        $this->assertSame(
            $expected,
            CollectionClass::intercalateRot13($a, $separator)
        );
    }

    /**
     * @dataProvider Providers::invokeWith1ArgProvider
     */
    public function testInvokeWith1Arg($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::invokeWith1Arg($a)
        );
    }

    /**
     * @dataProvider Providers::invokeWith2ArgsProvider
     */
    public function testInvokeWith2Args($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::invokeWith2Args($a)
        );
    }

    /**
     * @dataProvider Providers::isEmptyProvider
     */
    public function testIsEmpty($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::isEmpty($a)
        );
    }

    /**
     * @dataProvider Providers::keysProvider
     */
    public function testKeys($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::keys($a)
        );
    }

    /**
     * @dataProvider Providers::maxWithProvider
     */
    public function testMaxWith($expected, $a, $converter) {
        $this->assertSame(
            $expected,
            CollectionClass::maxWith($a, $converter)
        );
    }

    /**
     * @dataProvider Providers::maxWithNoTailSemicolonProvider
     */
    public function testMaxWithNoTailSemicolon($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::maxWithNoTailSemicolon($a)
        );
    }

    /**
     * @dataProvider Providers::memoizeProvider
     */
    public function testMemoize($expected, $a) {
        // dusankasanは常にlazy collectionを返すのでmemoize不要
        $this->assertSame(
            $expected,
            CollectionClass::memoize($a)
        );
    }

    /**
     * @dataProvider Providers::objectProvider
     */
    public function testObject($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::object($a)
        );
    }

    /**
     * @dataProvider Providers::objectWithArgProvider
     */
    public function testObjectWithArg($expected, $a, $b) {
        $this->assertSame(
            $expected,
            CollectionClass::objectWithArg($a, $b)
        );
    }

    /**
     * @dataProvider Providers::omitProvider
     */
    public function testOmit($expected, $a, $b) {
        $this->assertSame(
            $expected,
            CollectionClass::omit($a, $b)
        );
    }

    /**
     * @dataProvider Providers::pickProvider
     */
    public function testPick($expected, $a, $b) {
        $this->assertSame(
            $expected,
            CollectionClass::pick($a, $b)
        );
    }

    /**
     * @dataProvider Providers::pluckProvider
     */
    public function testPluck($expected, $a, $b) {
        $this->assertSame(
            $expected,
            CollectionClass::pluck($a, $b)
        );
    }

    /**
     * @dataProvider Providers::pluckProvider
     */
    public function testPluckWithStringSelector($expected, $a, $b) {
        $this->assertSame(
            $expected,
            CollectionClass::pluckWithStringSelector($a)
        );
    }

    /**
     * @dataProvider Providers::anyProvider
     */
    public function testSome($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::some($a)
        );
    }

    /**
     * @dataProvider Providers::sortNoArgProvider
     */
    public function testSortNoArg($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::sortNoArg($a)
        );
    }

    /**
     * @dataProvider Providers::sortFixedFunctionProvider
     */
    public function testSortFixedFunction($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::sortFixedFunction($a)
        );
    }

    /**
     * @dataProvider Providers::sumProvider
     */
    public function testSum($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::sum($a)
        );
    }

    /**
     * @dataProvider Providers::sumStringSelectorProvider
     */
    public function testSumStringSelector($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::sumStringSelector($a)
        );
    }

    /**
     * @dataProvider Providers::sumWithFunctionProvider
     */
    public function testSumWithFunction($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::sumWithFunction($a)
        );
    }

    /**
     * @dataProvider Providers::valuesProvider
     */
    public function testValues($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::values($a)
        );
    }

    /**
     * @dataProvider Providers::whereProvider
     */
    public function testWhere($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::where($a)
        );
    }

    /**
     * @dataProvider Providers::whereProvider
     */
    public function testWhereWithObj($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::whereWithObj($a)
        );
    }

    /**
     * @dataProvider Providers::whereProvider
     */
    public function testWhereWithObjNested($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::whereWithObjNested($a)
        );
    }

    /**
     * @dataProvider Providers::range10Provider
     */
    public function testRange10($expected) {
        $this->assertSame(
            $expected,
            CollectionClass::range10()
        );
    }

    /**
     * @dataProvider Providers::range4Provider
     */
    public function testRange4($expected) {
        $this->assertSame(
            $expected,
            CollectionClass::range4()
        );
    }

    /**
     * @dataProvider Providers::range24Provider
     */
    public function testRange24($expected) {
        $this->assertSame(
            $expected,
            CollectionClass::range24()
        );
    }

    /**
     * @dataProvider Providers::zipProvider
     */
    public function testZip($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::zip(...$a)
        );
    }

    /**
     * @dataProvider Providers::initialProvider
     */
    public function testInitial($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::initial($a)
        );
    }

    /**
     * @dataProvider Providers::restProvider
     */
    public function testRest($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::rest($a)
        );
    }

    /**
     * @dataProvider Providers::zipWithProvider
     */
    public function testZipWith($expected, $a, $b) {
        $this->assertSame(
            $expected,
            CollectionClass::zipWith($a, $b)
        );
    }

    /**
     * @dataProvider Providers::zipWithSingleArrayProvider
     */
    public function testZipWithSingleArray($expected, $a) {
        $this->assertSame(
            $expected,
            CollectionClass::zipWithSingleArray($a)
        );
    }
}

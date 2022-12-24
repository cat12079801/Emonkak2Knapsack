<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\{
    Node
};

class ReplacementAgent {
    public $pattern;
    public $destination;
    public $targetLineIdx;
    public $file;
    public $targetLine;
    public $isSetExtraInfo = false;

    const SEARCH = 1;
    const REPLACE = 2;
    const C_RESET = "\e[0m";
    const C_SEARCH = "\e[38;5;214m";
    const C_REPLACE = "\e[38;5;078m";


    /**
     * @param Node $node
     * @param string $pattern 置換対象文字列のパターン
     * @param string|string[] $destination 置換先文字列。文字列の配列の場合いい感じ(ベストエフォート)に改行を行う
     */
    public function __construct(int $targetLine, string $pattern, $destination) {
        $this->pattern = $pattern;
        $this->destination = $destination;
        $this->targetLineIdx = $targetLine - 1;
    }

    public function setExtraInfo(array $file, string $filepath) {
        $this->file = $file;
        // 対象が複数行にまたがったときは改めて考える
        $this->targetLine = $file[$this->targetLineIdx];
        $this->filepath = $filepath;
        $this->indentWidth = 4;
        $this->isSetExtraInfo = true;
    }

    private function checkReplaceable() {
        if (!$this->isSetExtraInfo) {
            throw new \UnexpectedValueException('先にsetExtraInfo()する');
        }

        $matchNum = preg_match_all(
            $this->pattern,
            $this->targetLine
        );
        if ($matchNum === false) {
            var_dump($this->pattern);
            throw new \UnexpectedValueException("想定外のpattern targeLine: $this->targetLine, pattern: $this->pattern, replace: $this->destination");
        } else if ($matchNum === 0) {
            throw new \UnexpectedValueException("マッチせず targeLine: $this->targetLine, pattern: $this->pattern, replace: $this->destination");
        } else if ($matchNum > 1) {
            throw new \UnexpectedValueException("複数マッチ発生 targeLine: $this->targetLine, pattern: $this->pattern, replace: $this->destination");
        }
    }

    public function replace(): string {
        $this->checkReplaceable();
        return preg_replace(
            $this->pattern,
            $this->getReplaceDestination(),
            $this->targetLine,
            1
        );
    }

    public function replaceDryRun() {
        $this->checkReplaceable();
        echo "L:".($this->targetLineIdx+1)."\n";
        $this->printDiff(self::SEARCH);
        $this->printDiff(self::REPLACE);
        echo "\n";
    }

    private function printDiff(int $type) {
        $lines = explode("\n", chop($this->decorateText($type)));
        $symbol = (function(int $type): string {
            if ($type === self::SEARCH) return '- ';
            if ($type === self::REPLACE) return '+ ';
        })($type);

        $C = '';
        foreach($lines as $line) {
            echo self::C_RESET. $symbol . $C . $line . "\n";

            if ($C === '') {
                $C = (function(int $type): string {
                    if ($type === self::SEARCH) return self::C_SEARCH;
                    if ($type === self::REPLACE) return self::C_REPLACE;
                })($type);
            }
        }
    }

    public function decorateText(int $type) {
        if ($type === self::SEARCH) {
            return preg_replace(
                $this->pattern,
                self::C_SEARCH.$this->getPatternNotEscaped().self::C_RESET,
                $this->targetLine,
                1
            );
        } else if ($type === self::REPLACE) {
            return preg_replace(
                '/' . preg_quote($this->getReplaceDestination()) . '/',
                self::C_REPLACE.$this->getReplaceDestination().self::C_RESET,
                $this->replace(),
                1
            );
        } else {
            new \UnexpectedValueException('不正な引数');
        }
    }

    private function getReplaceDestination(): string {
        if (is_string($this->destination)) {
            return $this->destination;
        }

        $indent = preg_replace('/\S.*\n?$/', '', $this->targetLine);

        if (preg_match('/^ +->/', $this->targetLine) === 0) {
            $indent = $indent . str_repeat(' ', $this->indentWidth);
        }

        return implode("\n$indent", $this->destination);
    }

    private function getPatternNotEscaped(): string {
        $result = substr($this->pattern, 1, strlen($this->pattern) - 2);

        // 正規表現の後読み/先読みを除去する
        // (hoge|piyo) のような正規表現を破壊するので注意
        $result = preg_replace('/(?<!\\\\)\(.+(?<!\\\\)\)/', '', $result);

        foreach(explode(' ', '\ . + * ? [ ^ ] $ ( ) { } = ! < > | : -') as $c) {
            $result = str_replace("\\$c", $c, $result);
        }
        return $result;
    }
}

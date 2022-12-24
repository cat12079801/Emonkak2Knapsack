<?php declare(strict_types=1);
require_once 'vendor/autoload.php';
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

$lexer = new Lexer\Emulative([
    'usedAttributes' => [
    'comments',
    'startLine', 'endLine',
    'startTokenPos', 'endTokenPos',
    ],
]);
$parser = new Parser\Php7($lexer);

$result = [];
$currentFilePath = '';

$traverser = new SpyNodeTraverser();
$traverser->addVisitor(new class extends NodeVisitorAbstract {
    public function leaveNode(Node $node) {
        if (!self::isCollectionClassMethodChainRecursive($node)) {
            return null;
        }

        if (!$node instanceof MethodCall && !$node instanceof StaticCall) {
            return null;
        }

        global $result, $currentFilePath;

        $methodName = $node->name->name;
        if (empty($result[$methodName])) {
            $result[$methodName] = [];
        }
        $argNum = (string)count($node->args) . 'args';
        if (empty($result[$methodName][$argNum])) {
            $result[$methodName][$argNum] = [];
        }
        $types = [];
        foreach($node->args as $arg) {
            $types[] = $arg->value->getType();
        }
        $typeStr = implode(', ', $types);
        if (empty($result[$methodName][$argNum][$typeStr])) {
            $result[$methodName][$argNum][$typeStr] = [];
        }
        $result[$methodName][$argNum][$typeStr][] = "$currentFilePath L{$node->name->getStartLine()}";
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
});

foreach ($targetFiles as $filepath) {
    if (!file_exists($filepath)) {
        continue;
    }
    $currentFilePath = $filepath;
    $code = file_get_contents($filepath);
    $stmts = $parser->parse($code);
    $traverser->traverse($stmts);
}

echo(json_encode($result) . "\n");

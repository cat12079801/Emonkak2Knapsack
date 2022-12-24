<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\{
    NodeTraverser
};

class SpyNodeTraverser extends NodeTraverser {
    protected $replaceInfo = [];

    public function getReplaceInfo(): array {
        return $this->replaceInfo;
    }

    public function clearReplaceInfo() {
        $this->replaceInfo = [];
    }

    /**
     * original doc: Recursively traverse a node.
     * パースした情報を replaceInfo に格納するため *だけ* に機能を拡張/削除している
     * enterNode, leaveNode の返り値はnull|ReplacementAgent
     *
     * @param Node $node Node to traverse.
     *
     * @return Node Result of traversal (may be original node or new one)
     */
    protected function traverseNode(Node $node) : Node {
        foreach ($node->getSubNodeNames() as $name) {
            $subNode =& $node->$name;

            if (\is_array($subNode)) {
                $subNode = $this->traverseArray($subNode);
                if ($this->stopTraversal) {
                    break;
                }
            } elseif ($subNode instanceof Node) {
                $traverseChildren = true;

                foreach ($this->visitors as $visitorIndex => $visitor) {
                    $return = $visitor->enterNode($subNode);
                    if ($return && $return instanceof ReplacementAgent) {
                        $this->replaceInfo[] = $return;
                    } else if (is_array($return)) {
                        foreach ($retrun as $info) {
                            if (!$info instanceof ReplacementAgent) {
                                throw new \UnexpectedValueException('不正な型です');
                            }
                            $this->replaceInfo[] = $info;
                        }
                    } else if ($return !== null) {
                        throw new \UnexpectedValueException('不正な型です');
                    }
                }

                if ($traverseChildren) {
                    $subNode = $this->traverseNode($subNode);
                    if ($this->stopTraversal) {
                        break;
                    }
                }

                foreach ($this->visitors as $visitorIndex => $visitor) {
                    $return = $visitor->leaveNode($subNode);
                    if ($return && $return instanceof ReplacementAgent) {
                        $this->replaceInfo[] = $return;
                    } else if (is_array($return)) {
                        foreach ($return as $info) {
                            if (!$info instanceof ReplacementAgent) {
                                throw new \UnexpectedValueException('不正な型です');
                            }
                            $this->replaceInfo[] = $info;
                        }
                    } else if ($return !== null) {
                        throw new \UnexpectedValueException('不正な型です');
                    }
                }
            }
        }

        return $node;
    }

    /**
     * original doc: Recursively traverse array (usually of nodes).
     * パースした情報を replaceInfo に格納するため *だけ* に機能を拡張/削除している
     * enterNode, leaveNode の返り値はnull|ReplacementAgent
     *
     * @param array $nodes Array to traverse
     *
     * @return array Result of traversal (may be original array or changed one)
     */
    protected function traverseArray(array $nodes) : array {
        foreach ($nodes as $i => &$node) {
            if ($node instanceof Node) {
                $traverseChildren = true;

                foreach ($this->visitors as $visitorIndex => $visitor) {
                    $return = $visitor->enterNode($node);
                    if ($return && $return instanceof ReplacementAgent) {
                        $this->replaceInfo[] = $return;
                    } else if (is_array($return)) {
                        foreach ($retrun as $info) {
                            if (!$info instanceof ReplacementAgent) {
                                throw new \UnexpectedValueException('不正な型です');
                            }
                            $this->replaceInfo[] = $info;
                        }
                    } else if ($return !== null) {
                        throw new \UnexpectedValueException('不正な型です');
                    }
                }

                if ($traverseChildren) {
                    $node = $this->traverseNode($node);
                    if ($this->stopTraversal) {
                        break;
                    }
                }

                foreach ($this->visitors as $visitorIndex => $visitor) {
                    $return = $visitor->leaveNode($node);
                    if ($return && $return instanceof ReplacementAgent) {
                        $this->replaceInfo[] = $return;
                    } else if (is_array($return)) {
                        foreach ($return as $info) {
                            if (!$info instanceof ReplacementAgent) {
                                throw new \UnexpectedValueException('不正な型です');
                            }
                            $this->replaceInfo[] = $info;
                        }
                    } else if ($return !== null) {
                        throw new \UnexpectedValueException('不正な型です');
                    }
                }
            } elseif (\is_array($node)) {
                throw new \LogicException('Invalid node structure: Contains nested arrays');
            }
        }

        return $nodes;
    }

}

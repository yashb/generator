<?php

require __DIR__."/../vendor/autoload.php";

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeTraverser;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class MyParserNodeVisitor extends \PhpParser\NodeVisitorAbstract
{
    function __construct() {

    }

    public function enterNode(Node $node)
    {
        /*if ($node instanceof Node\Name)
        {
             //echo "Node nameD:". $node."\n\n";
            return new Node\Name($node->toString('_'));
        }*/
        
        if ( $node instanceof Stmt\Interface_
                  || $node instanceof Stmt\Function_)
        {
            echo "\nNode name:". $node->name."\n\n";
            
            print_r($node);

            //$node->name = $node->namespacedName->toString('_');
        }
        
         elseif ($node instanceof Stmt\Class_)
        {
            
            //List all implements 
            
            //List all classes
            
            //This class can use those functions as own
            //Parse and get those functions also
             
            
            echo "\nClass name:". $node->name."\n\n";
            
        }
        
        elseif ($node instanceof Stmt\ClassMethod)
        {
             echo "\nClass method:". $node->name."\n\n";
        }
        elseif ($node instanceof Stmt\Const_)
        {
            /*foreach ($node->consts as $const) {
                $const->name = $const->namespacedName->toString('_');
            }*/
        }
        elseif ($node instanceof Stmt\Namespace_)
        {
            echo "\nNode Namespace_ name:". $node->name."\n\n";
            // returning an array merges is into the parent array
            //return $node->stmts;
        }
        elseif ($node instanceof Stmt\Use_)
        {
            //print_r($node);
            echo "\nNode Use_ Namespace_ name:";//. $node->uses[0]->name."\n\n";
            
            foreach($node->uses as $use)
            {
                
                //Name is using _ as seperator
                
                echo $use->name. " | " .$use->alias. "\n\n";
            }
            
            // returning false removed the node altogether
            //return false;
        }
    }
}



$lexer = new PhpParser\Lexer(array(
    'usedAttributes' => array(
        'comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos'
    )
));

$parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP5, $lexer);

$visitor = new MyParserNodeVisitor();

$traverser = new PhpParser\NodeTraverser();
$traverser->addVisitor($visitor);

try {
    $stmts = $parser->parse(file_get_contents('sample.php'));

    //print_r($stmts);

    //$visitor->setTokens($lexer->getTokens());
    $stmts = $traverser->traverse($stmts);

}
catch (PhpParser\Error $e)
{
    echo 'Parse Error: ', $e->getMessage();
}



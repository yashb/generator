<?php

require __DIR__."/../vendor/autoload.php";

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeTraverser;
use PhpParser\Node;
use PhpParser\Node\Stmt;


define("CACHE_DIR", ".kiwicache" . DIRECTORY_SEPARATOR );
define("IS_DEBUG", FALSE );


class Indexer
{
    private $path;
    
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    public function scanFile($filename)
    {

        if(empty($filename))
            return;
        
        new MyPhpParser($filename);
    }
    
    public function scan()
    {
        
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );
        
        $paths = array($this->path);
        
        foreach ($iter as $file) {
            
            if(IS_DEBUG)
                echo "File: ". $file->getPathname() . PHP_EOL;
            
            if($file->getExtension() == "php")
            {
                $this->scanFile( $file->getPathname() );
            }
            
            /*if ($dir->isDir()) {
                $paths[] = $path;
            }*/
        }
    }
}


class DirHelper
{
    public static function createCache()
    {
        if(!is_dir(CACHE_DIR))
        mkdir( CACHE_DIR , 0777, true);
    }
    
    public static function createDir($dir)
    {
        if(!is_dir($dir))
            mkdir( $dir , 0777, true);
    }
    
    
    
}

class MyParserNodeVisitor extends \PhpParser\NodeVisitorAbstract
{
    
    private $namespace_;
    private $use_ = array();
    private $result;
    private $filename;
    
    private $className;
    private $classMethods;
    
    private $functions = array();
    
    private $debug;
    
    function __construct( $filename)
    {
        $this->filename = $filename;
        
        
    }
    
    public function addItem($name, $type)
    {
        
        $this->className = $name;
        
        $this->result[$this->className]['type'] = $type;
        $this->result[$this->className]['methods'] = array();
        $this->result[$this->className]['use'] = array();
        
    }
    
    public function enterNode(Node $node)
    {
        /*if ($node instanceof Node\Name)
        {
             //echo "Node nameD:". $node."\n\n";
            return new Node\Name($node->toString('_'));
        }
        
        || $node instanceof Stmt\Function_
        
        */
        if ( $node instanceof Stmt\Function_ )
        {
            array_push( $this->functions, $node->name );
        }
        else if ( $node instanceof Stmt\Interface_ )
        {
            if(IS_DEBUG)
                echo "\nNode name:". $node->name."\n\n";
            
            //print_r($node);
            
            $this->addItem($node->name, 'interface');
            
            //$node->name = $node->namespacedName->toString('_');
        }
        
        elseif ($node instanceof Stmt\Trait_)
        {
            
            //List all implements
            
            //List all classes
            
            //This class can use those functions as own
            //Parse and get those functions also
            
            if(IS_DEBUG)
                echo "\Trait name:". $node->name."\n\n";
            
        }
        elseif ($node instanceof Stmt\TraitUse)
        {
            /*            echo "\Trait name:". $node->traits."\n\n";*/
            
            foreach ($node->traits as $trait) {
                //print_r($trait);
            }
            
        }
        elseif ($node instanceof Stmt\Class_)
        {
            
            //List all implements
            
            //List all classes
            
            //This class can use those functions as own
            //Parse and get those functions also
            
            
            if(IS_DEBUG)
                echo "\nClass name:". $node->name."\n\n";
            
            
            $this->addItem($node->name, 'class');
            /*            $this->result['class'] = $node->name;*/
            
            
        }
        
        elseif ($node instanceof Stmt\ClassMethod)
        {
            if(IS_DEBUG)
                echo "\nClass method:". $node->name."\n\n";
            
            //$this->result[$this->className]['methods'][] = $node->name;
            array_push( $this->result[$this->className]['methods'], $node->name );
            //$this->classMethods[] = $node->name;
            
        }
        elseif ($node instanceof Stmt\Const_)
        {
            /*foreach ($node->consts as $const) {
                $const->name = $const->namespacedName->toString('_');
            }*/
        }
        elseif ($node instanceof Stmt\Namespace_)
        {
            $this->namespace_ = $node->name;
            
            $this->namespace_ = str_replace("\\" ,"/" , $this->namespace_ );

            if(IS_DEBUG)
                echo "\nNode Namespace_ name:". $node->name."\n\n";
            
            
            
            
            // returning an array merges is into the parent array
            //return $node->stmts;
        }
        elseif ($node instanceof Stmt\Use_)
        {
            //print_r($node);
            if(IS_DEBUG)
                echo "\nNode Use_ Namespace_ name:";//. $node->uses[0]->name."\n\n";
            
            foreach($node->uses as $use)
            {
                
                //Name is using _ as seperator
                
                if(IS_DEBUG)
                {
                    echo $use->name. " | " .$use->alias. "\n\n";
                    echo "\nClass method:". $use->name."\n\n";
                }
                    
                    
            
                //$this->result[$this->className]['methods'][] = $node->name;
                array_push( $this->use_, array( "name" => $use->name->parts, "alias" => $use->alias) );
            }
            
            // returning false removed the node altogether
            //return false;
        }
    }
    
    public function afterTraverse(array $nodes)
    {
        //create class and methods
        if(IS_DEBUG)
            print_r($this->result);
    
        //$this->result[$className]= $classMethods;
        
        //Write a file with some node value
       $this->writeJson();
    }
    
    public function writeJson()
    {
        
        if(empty($this->namespace_))
        {
            $filename = CACHE_DIR  .basename($this->filename,".php") .'.json';
        }
        else
        {
            $dir = CACHE_DIR . $this->namespace_ . DIRECTORY_SEPARATOR;
            
            DirHelper::createDir($dir);
            
            $filename = $dir .  basename($this->filename,".php") .'.json';
        }
        
        
        $this->result['namespace'] = $this->namespace_;
        $this->result['use'] = $this->use_;
        $this->result['functions'] = $this->functions;
        
        
        if(IS_DEBUG)
        {
            //print_r($this->result);
            echo "Writing json file -> " .$filename . PHP_EOL ;
        }
        
        $content = json_encode($this->result);
        
        if(IS_DEBUG)
            echo "content: " . $content.PHP_EOL;
        
        $fp = fopen($filename, 'w');
        
        fwrite($fp, $content );
        
        fclose($fp);
    }
}




class MyPhpParser
{
    // private $lexer;
    private $parser;
    private $traverser;
    private $filename;
    
    public function __construct($filename)
    {
        $this->filename = $filename;
        
        //Create cache dir
        DirHelper::createCache();
        
        //Init 
        $this->initInstances();
        
        //parse file
        $this->myParser();
        
    }
    
    private function initInstances()
    {
        $lexer = new PhpParser\Lexer ( array(
        'usedAttributes' => array(
        'comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos'
        )
        ) ) ;
        
        $this->parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP5, $lexer);
        
        
        $visitor = new MyParserNodeVisitor($this->filename);
        
        $this->traverser = new PhpParser\NodeTraverser();
        $this->traverser->addVisitor($visitor);
    }
    
    private function myParser()
    {
        try {
            $stmts = $this->parser->parse(file_get_contents($this->filename));
            
            //print_r($stmts);
            
            //$visitor->setTokens($lexer->getTokens());
            $stmts = $this->traverser->traverse($stmts);
            
        }
        catch (PhpParser\Error $e)
        {
            echo 'Parse Error: ', $e->getMessage();
        }
        
        
    }
    
}






$ind = new Indexer(getcwd(). DIRECTORY_SEPARATOR . "test");


$filename = $argv[1];

if(empty($filename))
    $ind->scan();
else 
    $ind->scanFile($filename);

//getcwd()

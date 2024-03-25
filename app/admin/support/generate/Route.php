<?php
namespace app\admin\support\generate;
use PhpParser\{Node};
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

class Route extends FileGenerator
{

    protected string $controller;

    protected string $controllerNamespace;

    public function generate()
    {
        $stmts = $this->getRoute();

        $isHasSameNamespace = false;
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\Use_) {
                if ($stmt->uses[0]->name->name == $this->controllerNamespace) {
                    $isHasSameNamespace = true;
                }
            }
        }

        if ($isHasSameNamespace) {
            return true;
        }

        array_unshift($stmts, new Node\Stmt\Use_([new Node\UseItem(new Node\Name($this->controllerNamespace))]));

        $stmts[] = new Node\Stmt\Expression(
            new Node\Expr\MethodCall(
                new Node\Expr\StaticCall(
                    new Node\Name('Route'),
                    new Node\Identifier('resource'),
                    [
                        new Node\Arg(new Node\Scalar\String_(lcfirst($this->controller))),
                        new Node\Arg(new Node\Expr\ClassConstFetch(
                            new Node\Name($this->controller),
                            new Node\Identifier('Class')
                        ))
                    ]
                ),

                new Node\Identifier('except'),
                [
                    new Node\Arg(
                        new Node\Expr\Array_(
                            [
                                new Node\ArrayItem(
                                    new Node\Scalar\String_('create')
                                ),
                                new Node\ArrayItem(
                                    new Node\Scalar\String_('edit')
                                )
                            ]
                        )
                    )
                ]
            )
        );

        $prettyPrinter = new PrettyPrinter\Standard;

        file_put_contents(app_path('admin' . DIRECTORY_SEPARATOR . 'route')  . 'auth.php', $prettyPrinter->prettyPrintFile($stmts));

        return true;
    }

    protected function getRoute()
    {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();

        return $parser->parse(file_get_contents(
            app_path('admin' . DIRECTORY_SEPARATOR . 'route')  . 'auth.php'
        ));
    }

    public function setController($controller): static
    {
        $this->controllerNamespace = $controller;

        $controller = explode('\\', $controller);
        $this->controller = end($controller);

        return $this;
    }
}

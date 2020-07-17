<?php

namespace Adapttive\GrumPHP;

use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use GrumPHP\Task\TaskInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Swagger extends AbstractExternalTask implements TaskInterface
{
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'swagger_schema_url' => null,
            'request' => 'GET',
        ]);

        $resolver->addAllowedTypes('swagger_schema_url', ['null', 'string']);

        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();
        $arguments = $this->processBuilder->createArgumentsForCommand('curl');
        $arguments->addOptionalArgument('--request %s', $config['request']);
        $arguments->addOptionalArgument(
            '-s -o /dev/null -w "%{http_code}" --insecure --url "%s"',
            $config['swagger_schema_url']
        );
        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful() || $process->getOutput() != 200) {
            return TaskResult::createFailed($this, $context, $this->formatter->format($process));
        }

        return TaskResult::createPassed($this, $context);
    }
}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Yura Rodchyn (rodchyn) rodchyn@gmail.com
 * Date: 7/15/13
 * Time: 7:41 PM
 * To change this template use File | Settings | File Templates.
 */

namespace ElephantLang\Commands;

use ElephantLang\Parser\Builder;
use ElephantLang\Lexer;
use ElephantLang\Parser\Parser;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('test:parser')
            ->setDescription('Test generated parser on test file')

            ->addOption(
                'file',
                null,
                InputOption::VALUE_NONE,
                'If set, parser will use custom file to parse instead of test.elph'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello ' . $name;
        } else {
            $text = 'Hello';
        }

        */

        $file = 'test.elph';

        if ($input->getOption('file')) {
            $file = $input->getOption('file');
        }

        $example = file_get_contents(dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/test.elph');

        $output->writeln( "<fg=green>Input value: " . $example . "</fg=green>" );

        try {
            $lexer = new Lexer();
            $parser = new Parser($lexer);
            //Parser::Trace(fopen('php://output', 'w'), 'Trace: ');
            $lexer->tokenizeAll($example);

            foreach($lexer->tokens as $token) {
                $output->writeln("Parsing {$token->symbol} Token {$token->value}");
                $parser->parse($token);
            }

            $parser->parse(null);

        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            $lexer->debugPrintReport();
        }

        $output->writeln('');
        $output->writeln( "<fg=green>Return value: " . $parser->retvalue . "</fg=green>" );
    }
}

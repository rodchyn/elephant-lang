<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Yura Rodchyn (rodchyn) rodchyn@gmail.com
 * Date: 7/15/13
 * Time: 7:41 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Rodchyn\ElephantLang\Commands;

use Rodchyn\ElephantLang\Parser\Builder;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Build elephant-lang parser from yacc definitions')
        /*
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )*/;
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

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }
        */
        $builder = new Builder();
        $builder->build($output);
    }
}

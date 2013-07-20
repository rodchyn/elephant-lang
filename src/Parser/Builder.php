<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Yura Rodchyn (rodchyn) rodchyn@gmail.com
 * Date: 7/15/13
 * Time: 7:56 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Rodchyn\ElephantLang\Parser;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

define('ROOT', realpath(dirname(dirname(dirname(dirname(__FILE__))))) . '/');

require_once ROOT . 'lib/ParserGenerator/ParserGenerator.php';

class Builder
{

    /**
     * @var string
     */
    private $source = 'parser.y';

    /**
     * @var string
     */
    private $target = 'src/Parser/Parser.php';

    /**
     * Set grammar source file to build parser from it
     *
     * @param $source string
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    public function build(OutputInterface &$output)
    {
        $style = new OutputFormatterStyle('red', null, array('bold', 'blink'));
        $output->getFormatter()->setStyle('fire', $style);

        // Disable output.
        ini_set('implicit_flush', false);

        // Lemon takes arguments on the command line.
        $_SERVER['argv'] = $argv = array('-s', ROOT . $this->source);

        $output->writeln("<fg=green>Attempting to build \"{$this->target}\" from \"{$this->source}\"</fg=green>");
        $output->writeln("This could take a few minutes...");

        // The -q flag doesn't seem to work but we can catch the output in a
        // buffer (only want to display the errors).
        ob_start();

        $lemon = new \PHP_ParserGenerator;
        $lemon->main();

        $reply = explode("\n", ob_get_contents());

        ob_end_clean();

        $errors = array();
        $conflicts = 0;

        foreach ($reply as $i => $line) {
            // Errors are prefixed with the grammar file path.
            if (strpos($line, $argv[1]) === 0) {
                $errors[] = str_replace($argv[1], basename($argv[1]), $line);
            }

            if ($i === count($reply) - 2) {
                if (preg_match('/^(\d+).+/', $line, $m)) {
                    $conflicts = intval($m[1]);
                }
            }
        }

        if ($errors) {
            $output->writeln("<fire>" . implode("\n", $errors) . "</fire>");
        }

        // Build was a success!
        if (!file_exists(ROOT . $this->target) || @unlink(ROOT . $this->target)) {
            $content = file_get_contents(ROOT . 'parser.php');

            // Add namespace declaration.
            $content = strtr($content, array(
                '<?php' =>
                "<?php\n\n"
                    . "namespace Rodchyn\ElephantLang\Parser;\n\n"
                    . "use \ArrayAccess as ArrayAccess;\n\n"
                    . "use Rodchyn\ElephantLang\Init as Init;\n\n"
                    . "Init::init();\n\n"
                    . ""
            ));

            // Write.
            file_put_contents(ROOT . $this->target, $content);

            $output->writeln("Parser builded!");

            // Clean up.
            unlink(ROOT . 'parser.php');

            if ($conflicts) {
                $output->writeln("but <fire>{$conflicts} parsing conflicts occurred (see {$this->source}.out).</fire>");
            } else {
                unlink(ROOT . 'parser.out');
            }

            return 0;

        } else {
            // Bad permissions.
            $output->writeln("<fire>Failed!</fire>");
            $output->writeln("<fire>Couldn't remove {$this->target}. Check your permissions.</fire>");

            return 1;
        }
    }


}

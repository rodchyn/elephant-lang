<?php

define('ROOT', realpath(dirname(__FILE__)).'/');

// Disable output.
ini_set('implicit_flush', false);

function init()
{
  $arg = array_slice($_SERVER['argv'], 1);
  $arg = $arg ? $arg[0] : '';

  if (in_array($arg, array('?', 'help')))
  {
    exit("make.php\n");
  }

  make();
}

function make()
{
  require 'vendor/ParserGenerator/ParserGenerator.php';

  $source = 'parser.y';
  $target = 'src/ElephantLang/Parser.php';

  // Lemon takes arguments on the command line.
  $_SERVER['argv'] = $argv = array('-s', ROOT.$source);

  echo "Attempting to build \"{$target}\" from \"{$source}\".\n";
  echo "This could take a few minutes...\n";

  // The -q flag doesn't seem to work but we can catch the output in a
  // buffer (only want to display the errors).
  ob_start();

  $lemon = new PHP_ParserGenerator;
  $lemon->main();

  $reply = explode("\n", ob_get_contents());

  ob_end_clean();

  $errors = array();
  $conflicts = 0;

  foreach ($reply as $i => $line)
  {
    // Errors are prefixed with the grammar file path.
    if (strpos($line, $argv[1]) === 0)
    {
      $errors[] = str_replace($argv[1], basename($argv[1]), $line);
    }

    if ($i === count($reply) - 2)
    {
      if (preg_match('/^(\d+).+/', $line, $m))
      {
        $conflicts = intval($m[1]);
      }
    }
  }

  if ($errors)
  {
    exit(implode("\n", $errors));
  }

  // Build was a success!
  if ( ! file_exists(ROOT.$target) || @unlink(ROOT.$target))
  {
    $content = file_get_contents(ROOT.'parser.php');

    // Add namespace declaration.
    $content = strtr($content, array(
      '<?php' =>
          "<?php\n\n"
        . "namespace ElephantLang;\n\n"
        . "use \ArrayAccess as ArrayAccess;\n"
        . "\n"
    ));

    // Write.
    file_put_contents(ROOT.$target, $content);

    echo "Success!\n";

    // Clean up.
    unlink(ROOT.'parser.php');

    if ($conflicts)
    {
      echo "{$conflicts} parsing conflicts occurred (see {$source}.out).\n";
    }
    else
    {
      unlink(ROOT.'parser.out');
    }

    exit(0);
  }
  else
  {
    // Bad permissions.
    echo "Failed!\n";
    echo "Couldn't remove {$target}. Check your permissions.\n";

    exit(1);
  }
}

init();

?>

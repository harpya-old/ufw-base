<?php
namespace xbrain\ufw\utils;
/* ==============================================================================================================

  Process -
  Holds information about a given process.

  ============================================================================================================== */

class Process  
//extends Object 
{

    // Process-related data
    public $user;
    public $processID;
    public $parentProcessID;
    public $startTime;
    public $cpuTime;
    public $tty;
    
    // Command-line related properties
    public $command = '';  // Command name, without its path
    public $commandLine;    // Full command line
    public $title;    // Caption on Windows, process name on Unix
    public $argv;     // An argv array, with argv[0] being the command path

    public function __construct($command, $process_name = false) {
        $this->argv = $this->toArgv($command, false);

        if (count($this->argv))
            $this->command = pathinfo($this->argv [0], PATHINFO_FILENAME);

        $this->commandLine = $command;
        $this->title = ( $process_name ) ? $process_name : $this->command;
    }

    /* --------------------------------------------------------------------------------------------------------------

      NAME
      ToArgv - Converts a command-line string to an argv array.

      PROTOTYPE
      $argv	=  Convert::ToArgv ( $str, $argv0 = false ) ;

      DESCRIPTION
      Converts the specified string, which represents a command line, to an argv array.
      Quotes can be used to protect individual arguments from being split and are removed from the argument.

      PARAMETERS
      $str (string) -
      Command-line string to be parsed.

      $argv0 (string) -
      Normally, the first element of a $argv array is the program name. $argv0 allows to specify a
      program name if the supplied command-line contains only arguments.

      RETURN VALUE
      Returns an array containing the arguments.

     * ------------------------------------------------------------------------------------------------------------- */

    protected function toArgv($str, $argv0 = false) {
        $argv = [];

        if ($argv0)
            $argv [] = $argv0;

        $length = strlen($str);
        $quoted = false;
        $param = '';

        // Loop through input string characters
        for ($i = 0; $i < $length; $i ++) {
            $ch = $str [$i];

            switch ($ch) {
                // Backslash : escape sequence - only interpret a few special characters
                case '\\' :
                    if ($i + 1 < $length) {
                        $ch2 = $str [++$i];

                        switch ($ch2) {
                            case 'n' : $param .= "\n";
                                break;
                            case 't' : $param .= "\t";
                                break;
                            case 'r' : $param .= "\r";
                                break;
                            case 'v' : $param .= "\v";
                                break;
                            default : $param .= $ch2;
                        }
                    } else
                        $param .= '\\';

                    break;

                // Space - this terminates the current parameter, if we are not in a quoted string
                case ' ' :
                case "\t" :
                case "\n" :
                case "\r" :
                    if ($quoted)
                        $param .= $ch;
                    else if ($param) {
                        $argv [] = $param;
                        $param = '';
                    }

                    break;

                // A quote - Either the start or the end of a quoted value
                case '"' :
                case "'" :
                    if ($quoted) {  // We started a quoted string
                        if ($quoted == $ch) // This quoted string started with the same character as the current one
                            $quoted = false;
                        else    // This quoted string started with a different character
                            $param .= $ch;
                    } else    // We are not in a quoted string, so say that one quoted string has started
                        $quoted = $ch;

                    break;

                // Other : just append the current character to the current parameter
                default :
                    $param .= $ch;
            }
        }

        // Check for last parameter
        if ($param)
            $argv [] = $param;

        // All done, return
        return ( $argv );
    }

}

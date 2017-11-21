#!/bin/bash

# Define global variables
PROJECT_ROOT="$(git rev-parse --show-toplevel)"
FILES_NAME_TO_CHECK="$(git diff --cached --name-only --diff-filter=ACM  | egrep '.*\.php')"
ASK_PROMPT=0
BOLD=$(tput bold)
UNDERLINE=$(tput smul)
RED=$(tput setaf 1)
GREEN=$(tput setaf 2)
YELLOW=$(tput bold;tput setaf 3)
MAGENTA=$(tput bold;tput setaf 5)
ERROR=$(tput bold;tput setb 4)
WARN=$(tput bold;tput setb 6)
NORMAL=$(tput sgr0)

INDEX_ERROR=0
INDEX_WARNING=1
INDEX_VALID_TEST=2



# Check PHP Syntax Errors Before Committing
function check_lint()
{
    echo -e "\n$MAGENTA  Check for PHP Syntax errors $NORMAL"
    echo "$FILES_NAME_TO_CHECK" | while read FILE; do
        if [[ "$FILE" =~ ^.+\.(php|phtml)$ ]]; then
            # Courtesy of swytsh from the comments below.
            if [[ -f $FILE ]]; then
                tmp=$(php -l "$FILE" 2>&1)
                if [ $? -ne 0 ]; then
                    print_check_fail "$FILE"
                else
                    print_check_good "$FILE"
                fi
            fi
        fi
    done

    check_errors "Aborting commit due to files with syntax errors."
}

# Look for debug codes in module files
function check_dump()
{
    echo -e "\n$MAGENTA  Check for debug code in module files $NORMAL"

    # Get only the module files
    module_files="$(echo -e "$FILES_NAME_TO_CHECK" | egrep "src")"

    if [[ ! -n $module_files ]];then
        echo "    No module files to check"
        return
    fi

    echo "$module_files" | while read FILE; do
        result=$(grep "dump(" "$FILE" | wc -l)
        if [ $result -ne 0 ]; then
            print_check_warn "$FILE" " + 4"
        else
            print_check_good "$FILE"
        fi
    done

    check_errors "Some files contain debug code. Please review"
}

# Run Code Sniffer to detect code style issues
function check_phpcs()
{
    echo -e "\n$MAGENTA  Run PHP Code Sniffer $NORMAL"
    phpcs_path="$PROJECT_ROOT/vendor/bin"

    for FILE in $FILES_NAME_TO_CHECK; do
        result="$($phpcs_path/phpcs --standard=PSR2 $FILE 2>&1)"
        if [ $? -ne 0 ]; then
            print_check_warn "$FILE" " + 4"
            result="$(echo -e "$result" | egrep -v "^FILE" | egrep -v "^Time: " | sed 's/^/       &/g')"
            echo "$result"
        else
            print_check_good "$FILE"
        fi
    done

    check_errors "Some files does not match the coding standards. Please review"
}

# Run Mess Detector
function check_phpmd()
{
    echo -e "\n$MAGENTA  Run PHP Mess Detector $NORMAL"
    phpmd_path="$PROJECT_ROOT/vendor/bin"
    phpmd_ruleset="$PROJECT_ROOT/phpmd.xml"
    echo "$FILES_NAME_TO_CHECK" | while read FILE; do
        result="$($phpmd_path/phpmd "$FILE" text $phpmd_ruleset 2>&1)"
        if [ $? -ne 0 ]; then
            print_check_warn "$FILE" " + 4"
            file_absolue_path="$(echo -e "$PROJECT_ROOT/$FILE:")"
            result="$(echo -e "$result" | sed 's,'"$file_absolue_path"',Line ,g' | sed 's/^/       &/g' | sed 's/	/ - /g')"
            echo "$result"
            echo ""
        else
            print_check_good "$FILE"
        fi
    done

    check_errors "The Mess Detector found some issues. Please review"
}

# Run PHPUnit tests
function run_unit_tests()
{
    echo -e "\n$MAGENTA  Run PHPUnit tests for changed module files $NORMAL"

    # Get the changed test files
    test_files="$(echo -e "$FILES_NAME_TO_CHECK" | egrep "tests/" | egrep ".*Test\.php")"
    # Get the changed module files and generate the UnitTest pair of them
    module_files="$(echo -e "$FILES_NAME_TO_CHECK" | egrep "src/" | sed 's/src\/WebHemi\//tests\/WebHemiTest\//' | sed 's/.php/Test.php/') "
    # Sort and remove duplicates
    watched_files="$(echo -e "$test_files\n$module_files" | sort -u)"

    # Go through the file list and check if the UnitTest exists. If so then run the test
    echo "$watched_files" | while read FILE; do
        if [[ -f "$FILE" ]]; then
            # Set test for message
            touch /tmp/test.git.commit
            output="$($PROJECT_ROOT/vendor/bin/phpunit -c "$PROJECT_ROOT/phpunit-fast.xml" "$FILE" 2>&1)"
            if [ $? -ne 0 ]; then
                print_check_fail "$FILE"

                result="$(echo "$output" | egrep "(^Tests: |^Failed |_Exception: |^[0-9])")"
                if [ -n "$result" ];then
                    result="$(echo -e "$result" | sed 's/^/       &/g' | sed 's/^       [A-Z]/   &/g')"
                    echo "$result"
                fi
            else
                msg="$FILE"

                result="$(echo "$output" | egrep "^OK")"
                if [ -n "$result" ];then
                    msg="$msg - $GREEN$result$NORMAL"
                fi
                print_check_good "$msg" "+ 11"
            fi
        fi
    done

    if [ ! -f /tmp/test.git.commit ]; then
        echo "    No valid test files"
    fi

    check_errors "Aborting commit due to some PHPUnit test failed."
}

# Print stylish [OK]
#
# @param string $1 The message on the left side
function print_check_good()
{
    correcture="$2"

    # Count the gap
    col=`expr $(tput cols) - ${#1} $correcture`

    echo -en "    $1"
    printf "%*s%s\n" $col "  [$GREEN OK $NORMAL]"
}

# Print stylish [WARN]
#
# @param string $1 The message on the left side
function print_check_warn()
{
    correcture="$2"

    # Set commit warning
    touch /tmp/warn.git.commit
    # Set prompt for abort
    touch /tmp/prompt.git.commit
    # Count the gap
    col=`expr $(tput cols) - ${#1} $correcture`

    echo -en "    $YELLOW$1$NORMAL"
    printf "%*s%s\n" $col "  [$YELLOW WARN $NORMAL]"
}

# Print stylish [FAIL]
#
# @param string $1 The message on the left side
function print_check_fail()
{
    correcture="$2"

    # Set commit blocker
    touch /tmp/block.git.commit

    # Count the gap
    col=`expr $(tput cols) - ${#1} - 3 $correcture`

    echo -en "    $ERROR!$NORMAL$YELLOW $1 $NORMAL"
    printf "%*s%s\n" $col "  [$RED FAIL $NORMAL]"
}

# Checks if there were errors and exit
#
# @param string $1 The message to print when abort the commit
function check_errors()
{
    msg="$1"

    if [ -f /tmp/block.git.commit ]; then
        echo -en "\n$ERROR ERROR: $msg " >&2
        echo -e "$NORMAL"
        exit 1
    fi

    if [ -f /tmp/warn.git.commit ]; then
        tmp=$(rm -f /tmp/warn.git.commit 2>&1)
        echo -en "\n$WARN WARNING: $msg " >&2
        echo -e "$NORMAL"
    fi
}

# Prepare environment
function init()
{
    clear
    echo ""
    echo -e "   ██╗    ██╗███████╗██████╗ ██╗  ██╗███████╗███╗   ███╗██╗"
    echo -e "   ██║    ██║██╔════╝██╔══██╗██║  ██║██╔════╝████╗ ████║██║"
    echo -e "   ██║ █╗ ██║█████╗  ██████╔╝███████║█████╗  ██╔████╔██║██║"
    echo -e "   ██║███╗██║██╔══╝  ██╔══██╗██╔══██║██╔══╝  ██║╚██╔╝██║██║"
    echo -e "   ╚███╔███╔╝███████╗██████╔╝██║  ██║███████╗██║ ╚═╝ ██║██║"
    echo -e "    ╚══╝╚══╝ ╚══════╝╚═════╝ ╚═╝  ╚═╝╚══════╝╚═╝     ╚═╝╚═╝"
    echo ""
    echo ""
    echo -e "$YELLOW \bStarting pre-commit hooks. $NORMAL"
    echo ""
    echo -e "$MAGENTA  Prepare environment $NORMAL"

    # Just a message for what we already done
    print_check_good "Collect changed PHP files..."

    # Information about the commit
    file_num="$(echo -e $FILES_NAME_TO_CHECK | wc -w)"
    echo -e "        There are $YELLOW$file_num$NORMAL files to commit..."

    # Do cleanup
    if [ -f /tmp/block.git.commit ]; then
        msg="Delete lock file..."
        tmp=$(rm -f /tmp/block.git.commit 2>&1)
        if [[ $? -ne 0 ]];then
            print_check_fail "$msg"
        else
            print_check_good "$msg"
        fi

        check_errors "Aborting commit. Cannot remove file /tmp/block.git.commit Permission denied."
    fi

    if [ -f /tmp/warn.git.commit ]; then
        msg="Delete warn file..."
        tmp=$(rm -f /tmp/warn.git.commit 2>&1)
        print_check_good "$msg"
    fi

    if [ -f /tmp/warn.git.commit ]; then
        msg="Delete prompt file..."
        tmp=$(rm -f /tmp/prompt.git.commit 2>&1)
        print_check_good "$msg"
    fi

    if [ -f /tmp/test.git.commit ]; then
        msg="Delete test file..."
        tmp=$(rm -f /tmp/test.git.commit 2>&1)
        print_check_good "$msg"
    fi
}

# Main task
function main()
{
    init
    # Quit if the commit is not executed from shell (but from some nested shells within an IDE)
    if [[ $SHLVL -lt "2" ]]; then
        exit;
    fi

    # Quit if there are no PHP files to commit
    if [[ ! -n $FILES_NAME_TO_CHECK ]];then
        exit;
    fi

    # Run tasks
    check_lint
    check_dump
    check_phpcs
    check_phpmd
    run_unit_tests
    echo -e "$NORMAL"

    if [ -f /tmp/prompt.git.commit ]; then
        exec < /dev/tty

        while true; do
          read -p "You have warnings. Are you sure you want to proceed with commit? [Y|n] " yn
          if [ "$yn" = "" ]; then
            yn='Y'
          fi
          case $yn in
              [Yy] ) exit;;
              [Nn] ) echo -en "\n$ERROR Commit aborted " >&2;echo -e "$NORMAL";exit 1;;
              * ) exit;;
          esac
        done
    fi

}

main

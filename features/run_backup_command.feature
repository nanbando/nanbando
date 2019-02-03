@console
Feature: Run backup command
    In order to backup data the backup command should run
    As a command user
    I want to run a backup when I call the application with the "backup" command

    Background:
        Given the resources directory contains following files
          | name                          |
          | uploads/84-0-frankenstein.txt |
          | uploads/pg345-dracula.txt     |
        And there exists following "backup.php" file
          """
            attach('uploads', \Nanbando\Script\DirectoryScript::create(get('%cwd%/uploads')));

            storage('test', \Nanbando\Storage\DirectoryStorageAdapter::create(get('%cwd%/var/storage/test')));
          """
        And I stop the time at "2018-04-05 20:20"

    Scenario: The backup file should contain all the files and some additional parameter
        When I run "bin/nanbando backup"
        Then I should see "Backup started", "Backup finished"
        And The file "var/backups/20180405-202000.json" should exists
        And The backup-archive "var/backups/20180405-202000.tar.gz" should exists
        And should contain following files
          | name                          | hash                                                     | size   |
          | uploads/84-0-frankenstein.txt | 110cf6e796f0f1b7926036369d25499c047798b6c7ba871b24f57119 | 442932 |
          | uploads/pg345-dracula.txt     | c494c52277bcada86aa142fc6d53e149a7e26d2f8aa9d7f2c72835b7 | 867184 |
        And should contain following file-metadata
            | name                          | hash                                                     | size   |
            | uploads/84-0-frankenstein.txt | 110cf6e796f0f1b7926036369d25499c047798b6c7ba871b24f57119 | 442932 |
            | uploads/pg345-dracula.txt     | c494c52277bcada86aa142fc6d53e149a7e26d2f8aa9d7f2c72835b7 | 867184 |
        And should contain following parameters
          | name     | type     | value            |
          | label    | string   |                  |
          | message  | string   |                  |
          | mode     | string   | full             |
          | started  | datetime | 2018-04-05T20:20 |
          | finished | datetime | 2018-04-05T20:20 |
        # no push should happen
        And The file "var/storage/test/20180405-202000.tar.gz" should not exists
        And The file "var/storage/test/20180405-202000.json" should not exists

    Scenario: The backup filename should contain the label
        When I run "bin/nanbando backup testlabel"
        Then The file "var/backups/20180405-202000_testlabel.tar.gz" should exists
        And The file "var/backups/20180405-202000_testlabel.json" should exists
        And The backup-archive "var/backups/20180405-202000_testlabel.tar.gz" should contain following parameters
          | name    | type   | value     |
          | label   | string | testlabel |
          | message | string |           |

    Scenario: The backup file should contain the message as parameter
        When I run "bin/nanbando backup -m mymessage"
        Then The file "var/backups/20180405-202000.tar.gz" should exists
        And The file "var/backups/20180405-202000.json" should exists
        And The backup-archive "var/backups/20180405-202000.tar.gz" should contain following parameters
          | name    | type   | value     |
          | label   | string |           |
          | message | string | mymessage |

    Scenario: The backup file should be pushed
        When I run "bin/nanbando backup --push"
        Then The file "var/storage/test/20180405-202000.tar.gz" should exists
        And The file "var/storage/test/20180405-202000.json" should exists

    Scenario: The backup file should be pushed with shortcut
        When I run "bin/nanbando backup -p"
        Then The file "var/storage/test/20180405-202000.tar.gz" should exists
        And The file "var/storage/test/20180405-202000.json" should exists

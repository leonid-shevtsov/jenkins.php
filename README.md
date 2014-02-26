# Jenkins 0.3 - a PHP-on-Apache log analyzer

*This tool has nothing to do with the Jenkins CI server. If anything, I had the name earlier. :)*

## Usage

    jenkins.php <errorlog> <errorlog> ...

## Recommended usage - add to logrotate

In `/etc/logrotate.d/apache2`

    /var/log/apache2/*.error.log {
      # other logrotate options here...

      delaycompress

      postrotate
        jenkins /var/log/apache2/*.error.log.1 | mail -s "Jenkins report" YOURMAIL@GMAIL.com
      endscript
    }

## Not recommended - crontab line to run Jenkins daily

It's your responsibility to rotate the logs

    05 00 * * * ~/scripts/jenkins.php /var/log/apache2/*.error.log | mail -s "Jenkins report" YOURMAIL@GMAIL.com

## Installation

The whole script is a self-contained PHP file, so download `jenkins.php` from this repository and put it in a
convenient place on your system.

## Adapter types of notifications

for add new types of notification, you might  implement the interface INotification and instance this class in jenkins.php. 
class MailAdapter implements JenkinsLogAnalyzer\INotifyAdapter {
    public function notify($report) {
        //send e-mail authenticated
    }
}

//jenkins.php
$cli = new \JenkinsLogAnalyzer\CLI($_SERVER['argc'], $_SERVER['argv'], new MailAdapter);

## Testing

PHPUnit tests coverage is provided. To run tests:

    phpunit test

## Changelog

### 2012-04-06 - 0.3

* Stripped out log rotation and mailing in favor of KISS.
* Code cleanup
* Github release.

### 2008-08-25 - 0.2

* Initial release


* * *

Copyright (C) 2012 Leonid Shevtsov 

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

#UVAGitUploader
##Introduction
UVAGitUploader is a PHP script used for committing the UVA problems solved by a user into a GitHub repository.

##Installation
- Clone this repository in your computer: `git clone https://github.com/MaSteve/UVAGitUploader.git`
- Run the composer installer: `composer install`
- Modify the `src/uploader.php` file including your UVA problems repository path and your problems folder path.
- If you want to receive email notifications each time the script can't commit any problem, add your email and set `NOTIFICATIONS` like `true`. (**Warnign**: email notifications need an email server configuration).
- Add a cron job for automatic committing.
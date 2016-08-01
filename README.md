#UVAGitUploader

##Introduction
UVAGitUploader is a PHP script used to commit UVa problems to a GitHub repository.

##Installation and configuration
- Install and configure `git`, `php` and `composer`.
- Clone this repository in your computer: `git clone https://github.com/MaSteve/UVAGitUploader.git`.
- Run `composer install`.
- Modify `.env.example` to include your UVa problems local repository path and your problems folder path. Modify any other configuration option to suit your needs. Once done, rename `.env.example` to `.env`.
    - If you want to receive an email if there are no more problems in the problem folder to commit, set `NOTIFICATIONS` to `true` and add your email address. (**Warning**: sending emails requires an email server configuration).
- Make sure you configure `git` so that it does not prompt you for your credentials when pushing changes to `origin/master`. There are [many ways](http://stackoverflow.com/questions/5343068/is-there-a-way-to-skip-password-typing-when-using-https-on-github) you can do this; to store your unencrypted credentials under the project root directory, use:
```
git config credential.helper store
```
- Run the script. The first time you run it, it will prompt you for your GitHub username and password.
```
php src/uploader.php
```
- *Tip*: Add a cron job to commit automatically. For example, to upload [daily at 10:00 A.M.](http://crontab.guru/#00_10_*_*_*), run `crontab -e` and add this to the end of the file:
```
/path/to/UVAGitUploader/src/uploader.php 00 10 * * *
```

# Contributing

Contributions are **welcome** and will be fully **credited**. We accept contributions via Pull Requests on [Github](https://github.com/shakurov/laravel-coinbase/pulls).

## Pull Requests

- **[PSR-2 Coding Standard.](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** The easiest way to apply the conventions is to install [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).
- **Add tests!** Your patch won't be accepted if it doesn't have tests.
- **Document any change in behaviour.** Make sure the `README.md` and any other relevant documentation are kept up-to-date.
- **Consider our release cycle.** We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.
- **Create feature branches.** Don't ask us to pull from your master branch.
- **One pull request per feature.** If you want to do more than one thing, send multiple pull requests.
- **Send coherent history.** Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](https://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Running Tests

First, you need to configure the environment variables. Copy the default phpunit config (`phpunit.xml.dist`) into "gitignored" `phpunit.xml` to avoid committing sensitive data:

```bash
$ cp phpunit.xml.dist phpunit.xml
```

Edit `phpunit.xml` file, fill the `COINBASE_API_KEY` and `COINBASE_WEBHOOK_SECRET` env variables according to the data from your Coinbase Commerce account.

Now run phpunit:
```bash
$ composer run-script test
```


*Happy coding!*

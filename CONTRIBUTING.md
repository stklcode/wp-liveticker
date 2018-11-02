# How to contribute

As for all great Open Source projects, contributions in form of bug reports and code are welcome and important to keep the project alive.

In general, this project follows the [GitHub Flow](https://guides.github.com/introduction/flow/). 
Fork the project, commit your changes to your branch, open a pull request and it will probably be merged.
However, to ensure maintainability and quality of the code, there are some guidelines you might be more or less familiar with. 
For that purpose, this document describes the important points.

## Opening an Issue

If you experience any issues with the plugin or the code, don't hesitate to file an issue.

### Bug Reports

Think you found a bug?
Please clearly state what happens and describe your environment to help tracking down the issue.
 
* Which version of the plugin are you running?
* Which version of WordPress?
* Which version(s) of related plugin(s)?
* Which version of PHP and - if of interest - which webserver are you running?

### Feature Requests

Missing a feature or like to have certain functionality enhanced?
No problem, please open an issue and describe what and why you think this change is required.

## Pull Requests

If you want to contribute your code to solve an issue or implement a desired feature yourself, you might open a pull request.
If the changes introduce new functionality or affect major parts of existing code, please consider opening an issue for discussion first.

For adding new functionality a new test case the corresponding PHPUnit test would be nice (no hard criterion though).

### Branches

The `master` branch represents the current state of development.
Please ensure your initial code is up to date with it at the time you start development.
The `master` should also be target for most pull requests.

In addition, this project features a `develop` branch, which holds bleeding edge developments, not necessarily considered stable or even compatible.
Do not expect this code to run smoothly, but you might have a look into the history to see if some work on an issue has already been started there.

For fixes and features, there might be additional branches, likely prefixed by `ft-` (feature) or `hf-` (hotfix) followed by an issue number (if applicable) and/or a title.
Feel free to adapt these naming scheme to your forks.

### Merge Requirements

To be merged into the master branch, your code has to pass the automated continuous integration tests, to ensure compatibility.
In Addition your code has to be approved by a project member.

#### What if my code fails the tests?

Don't worry, you can submit your PR anyway. 
The reviewing process might help you to solve remaining issues.

### Commit messages

Please use speaking titles and messages for your commits, to ensure a transparent history.
If your patch fixes an issue, reference the ID in the first line.
If you feel like you have to _briefly_ explain your changes, do it (for long explanations and discussion, consider opening an issue or describe in the PR).

**Example commit:**
```text
Fix nasty bug from #1337

This example commit fixes the issue that some people write non-speaking commit messages like 'done magic'.
A short description is helpful sometimes.
```

You might sign your work, although that's no must.

### When will it be merged?

Short answer: When it makes sense.

Bugfixes should be merged in time - assuming they pass the above criteria.
New features might be assigned to a certain milestone and as a result of this be scheduled according to the planned release cycle.

## Compatibility

To ensure usability for a wide range of users, please take note on the software requirements stated in the `README`.
This includes especially the minimum PHP version and also the minimum version of WordPress.

If you are unsure if your code matches these versions, the test will probably tell you.

In case you think, your change is more important than maintaining backwards compatibility, please start a discussion to see, 
if we might increase the minimum version or find a workaround for legacy systems. 

## Build Environment

All you need to start off - besides your favorite IDE of course - is [Composer](https://getcomposer.org). 
Running `composer install` will fetch all dependencies and build tools required. 
You might have noticed that this project contains a [Robo](http://robo.li) build script.
Running the _build_-task (`./vendor/bin/robo build`) executes the full chain from cleanup over test to bundling the final product in the `dist` directory.
A complete list of the available tasks and options can be shown by running `robo list`.

## Code Style

This project, as part of the WordPress ecosystem adheres to the [WordPress Coding Standards](https://codex.wordpress.org/WordPress_Coding_Standards).
Please make sure that you are at least roughly familiar with those guidelines.

The code style is automatically checked for commits (including pull requests) using [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).
You can check your code against the configured ruleset yourself by running
 `./vendor/bin/phpcs --standard=phpcs.xml your-edited-file.php` (assuming `composer install` has been executed) or the Robo task `robo test:cs` for a complete scan.

Please see these standards as guidelines. 
If code style this is the only test that fails and your code's semantics are fine, don't hesitate to submit your pull request anyway.
We probably find a solution for that.

## Continuous Integration

Automated tests are run using [Travis CI](https://travis-ci.org/stklcode/wp-liveticker) for every commit including pull requests.
They ensure compatibility with the supported PHP versions and the WP Coding Standards.

There is also a semi-automated code quality analysis pushing results to [SonarCloud](https://sonarcloud.io/dashboard?id=de.stklcode.web.wordpress.plugins%3Awp-liveticker).
Keep in mind that the ruleset is not yet perfect, so not every minor issue has to be fixed immediately.

## Still Open Questions?

If anything is still left unanswered and you're unsure if you got it right, don't hesitate to contact a team member.
in any case you might submit your request/issue anyway, we won't refuse good code only for formal reasons.
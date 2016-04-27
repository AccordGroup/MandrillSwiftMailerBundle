# Release Policy

* Releases must only be created from the master branch
* Releases may only be created when the latest Travis CI build is passing

## Semantic Versioning

As of 2016-04-27, this project follows Semantic Versioning, which uses the MAJOR.MINOR.PATCH pattern.

* MAJOR - Changes which introduce backwards-compatibility breaking changes, or remove deprecated features.
* MINOR - Changes which introduce new features while maintaining backwards-compatibility.
* PATCH - Bug fixes, or changes to improve performance or usability while maintaining backwards-compatibility.

## Release Schedule

The creation of a release should be considered when a commit is pushed to master.

* MAJOR - Release dates for major changes are decided by the AccordGroup/MandrillSwiftMailer team members
* MINOR - Released on the following Monday
* PATCH
    * Critical bug fixes: Released ASAP.
    * Edge cases: Released on the following Monday. If more than one person comments that they're experiencing the issue in a production environment, the release should be treated as a critical bug.
    * Improvement/refactoring: Released on the following Monday 
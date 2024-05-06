# Pull Request
<!-- 
PR title needs to be prefixed with a conventional commit type
(chore,ci,deprecate,docs,feat,fix,refactor,revert)

It should also be brief and descriptive for a good changelog entry

examples: "feat: add new implementation" or "fix: remove unused imports"
-->

## Proposed Changes
<!-- Describe what the changes are and link to a Discussion or Issue if one exists -->

## Readiness Checklist

### Author/Contributor

- [ ] You have read [CONTRIBUTING](https://github.com/ericsizemore/simple_counter/blob/master/CONTRIBUTING.md)
- [ ] If documentation is needed for this change, has that been included in this pull request
- [ ] run `composer run-script test` and ensure you have test coverage for the lines you are introducing
- [ ] run `composer run-script phpstan` and fix any issues that you have introduced
- [ ] run `composer run-script psalm` and fix any issues that you have introduced
- [ ] run `composer run-script cs:check` and fix any issues that you have introduced 

### Reviewer

- [ ] Label as either `fix`, `documentation`, or `enhancement`
- [ ] Additionally label as `verified` or `unverified`
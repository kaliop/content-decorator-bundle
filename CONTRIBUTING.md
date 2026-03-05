# Contributing

Thanks for contributing to Kaliop Content Decorator Bundle.

## Scope and Compatibility

- Current major line: `1.x`
- Supported platform for this line: Ibexa 4.6, Symfony 5.4 LTS, PHP 8.1+
- Keep backward compatibility within a major line

## Branching Model

- This project follows SemVer.
- `main` is the next feature release branch
- Each previous major line has a dedicated maintenance branch (for example `1.x`)
- Bugfixes for a previous major line should target its maintenance branch

## Pull Requests

1. Create a branch from the correct base branch.
2. Keep PRs focused and small when possible.
3. Update `README.md` and include release notes in GitHub Releases for user-visible changes.
4. Ensure CI is green before requesting review.

## Coding Standards

Run these commands before opening a PR:

```bash
composer validate --strict
composer check-cs
composer phpstan
composer deptrac
```

## Commit and Release Notes

- Use clear commit messages describing intent.
- For releases, follow SemVer and document changes in GitHub Releases.

## Security

Do not open public issues for security vulnerabilities.
Please follow the private reporting process in `SECURITY.md`.

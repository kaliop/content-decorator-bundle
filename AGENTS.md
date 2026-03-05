# AGENTS

This file provides guidance for LLM coding agents working on this repository.

## Project Purpose

Kaliop Content Decorator Bundle provides a typed model + repository layer for
Ibexa content objects.

## Architecture

- `src/bundle`: Symfony bundle integration (DI extension, configuration, wiring)
- `src/contracts`: public contracts and abstractions (stable API surface)
- `src/lib`: implementation details (mappers, factories, repositories, events)

When changing behavior, preserve this layered split.

## Supported Stack (1.x line)

- Ibexa 4.6
- Symfony 5.4 LTS
- PHP 8.1+

Do not widen platform constraints for this major line without explicit
maintainer approval.

## Branching and Versioning

- `main` is the next feature release branch
- previous major lines live on dedicated maintenance branches (for example `1.x`)
- SemVer is required
- no backward-incompatible changes in a minor/patch release

## Coding Rules

- Follow existing code style and strict types
- Keep contracts stable (`src/contracts`)
- Prefer adding new behavior in `src/lib` and wiring in `src/bundle`
- Avoid introducing framework or Ibexa version coupling outside supported line
- Keep dependencies minimal

## Required Checks

Before finalizing changes, run:

```bash
composer validate --strict
composer check-cs
composer phpstan
composer deptrac
```

## Documentation Rules

If behavior changes:

- update `README.md`
- update release notes in GitHub Releases
- add upgrade notes for breaking changes

## Release Workflow Notes

- Releases are tag-driven (`vX.Y.Z`) via GitHub Actions
- Packagist is notified from release workflow using `PACKAGIST_TOKEN`
